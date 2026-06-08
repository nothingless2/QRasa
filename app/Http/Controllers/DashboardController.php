<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        if (in_array($user->role, ['chef', 'waiter', 'cashier'])) {
            return redirect()->route('pesan.index');
        }

        $baseQuery = \App\Models\Pesan::query();

        // KPI Cards Data
        $totalMenus = Menu::count();

        $period = $request->input('period', 'today');

        $revenueQuery = (clone $baseQuery);
        $ordersQuery = (clone $baseQuery);

        switch ($period) {
            case 'this_week':
                $revenueQuery->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
                $ordersQuery->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
                break;
            case 'this_month':
                $revenueQuery->whereMonth('created_at', Carbon::now()->month);
                $ordersQuery->whereMonth('created_at', Carbon::now()->month);
                break;
            case 'today':
            default:
                $revenueQuery->whereDate('created_at', Carbon::today());
                $ordersQuery->whereDate('created_at', Carbon::today());
                break;
        }

        $revenueMetrics = $revenueQuery->selectRaw('SUM(total) as grand_total, SUM(subtotal) as total_subtotal, SUM(service_charge) as total_service, SUM(pajak_pb1) as total_pb1', [])->first();
        $revenue = $revenueMetrics->grand_total ?? 0;
        $totalSubtotal = $revenueMetrics->total_subtotal ?? 0;
        $totalService = $revenueMetrics->total_service ?? 0;
        $totalPb1 = $revenueMetrics->total_pb1 ?? 0;

        $orders = $ordersQuery->count();

        $averageOrderValue = (clone $baseQuery)->whereMonth('created_at', Carbon::now()->month)->avg('total');

        // Top Selling Menus
        $topSellingMenus = Menu::select('menus.nama', DB::raw('SUM(menu_pesan.quantity) as total_quantity'))
            ->join('menu_pesan', 'menus.id', '=', 'menu_pesan.menu_id')
            ->join('pesans', 'menu_pesan.pesan_id', '=', 'pesans.id')
            ->where('pesans.created_at', '>=', Carbon::now()->subDays(30))
            ->groupBy('menus.id', 'menus.nama')
            ->orderBy('total_quantity', 'desc')
            ->limit(5)
            ->get();

        // Low Stock Warning
        $lowStockMenus = Menu::where('stok', '<', 10)->orderBy('stok', 'asc')->get();
        $adminLowStockMenus = Menu::where('stok', '<', 10)->orderBy('stok', 'asc')->get();

        // Recent Orders
        $pesanans = \App\Models\Pesan::latest()->paginate(5);

        // Chart Data with Date Filter
        $chartPeriod = $request->input('chart_period', 'this_month');
        $labels = [];
        $data = [];

        switch ($chartPeriod) {
            case 'this_year':
                $salesData = \App\Models\Pesan::query()
                    ->select(
                        DB::raw('MONTH(created_at) as month'),
                        DB::raw('SUM(total) as total_sales')
                    )
                    ->whereYear('created_at', Carbon::now()->year)
                    ->groupBy('month')
                    ->orderBy('month', 'ASC')
                    ->pluck('total_sales', 'month')->all();

                for ($i = 1; $i <= 12; $i++) {
                    $labels[] = Carbon::create()->month($i)->format('M');
                    $data[] = $salesData[$i] ?? 0;
                }
                break;

            case 'last_30_days':
                $startDate = Carbon::now()->subDays(29)->startOfDay();
                $endDate = Carbon::now()->endOfDay();
                $salesData = \App\Models\Pesan::query()
                    ->select(
                        DB::raw('DATE(created_at) as date'),
                        DB::raw('SUM(total) as total_sales')
                    )
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->groupBy('date')
                    ->orderBy('date', 'ASC')
                    ->pluck('total_sales', 'date')->all();

                for ($date = $startDate; $date <= $endDate; $date->addDay()) {
                    $labels[] = $date->format('M d');
                    $data[] = $salesData[$date->format('Y-m-d')] ?? 0;
                }
                break;

            case 'last_7_days':
                $startDate = Carbon::now()->subDays(6)->startOfDay();
                $endDate = Carbon::now()->endOfDay();
                $salesData = \App\Models\Pesan::query()
                    ->select(
                        DB::raw('DATE(created_at) as date'),
                        DB::raw('SUM(total) as total_sales')
                    )
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->groupBy('date')
                    ->orderBy('date', 'ASC')
                    ->pluck('total_sales', 'date')->all();

                for ($date = $startDate; $date <= $endDate; $date->addDay()) {
                    $labels[] = $date->format('M d');
                    $data[] = $salesData[$date->format('Y-m-d')] ?? 0;
                }
                break;

            case 'this_month':
            default:
                $startDate = Carbon::now()->startOfMonth();
                $endDate = Carbon::now()->endOfMonth();
                $salesData = \App\Models\Pesan::query()
                    ->select(
                        DB::raw('DATE(created_at) as date'),
                        DB::raw('SUM(total) as total_sales')
                    )
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->groupBy('date')
                    ->orderBy('date', 'ASC')
                    ->pluck('total_sales', 'date')->all();

                for ($date = $startDate; $date <= $endDate; $date->addDay()) {
                    $labels[] = $date->format('d');
                    $data[] = $salesData[$date->format('Y-m-d')] ?? 0;
                }
                break;
        }

        $chartData = [
            'labels' => $labels,
            'data' => $data,
        ];

        return view('dashboard', compact(
            'pesanans',
            'chartData',
            'period',
            'revenue',
            'totalSubtotal',
            'totalService',
            'totalPb1',
            'orders',
            'averageOrderValue',
            'topSellingMenus',
            'lowStockMenus',
            'adminLowStockMenus',
            'totalMenus',
            'chartPeriod'
        ));
    }

    public function exportPdf(Request $request)
    {
        $user = Auth::user();
        if ($user->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }

        $period = $request->input('period', 'this_month');
        $baseQuery = \App\Models\Pesan::with(['menus', 'meja']);

        switch ($period) {
            case 'this_week':
                $baseQuery->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
                $periodLabel = 'Minggu Ini (' . Carbon::now()->startOfWeek()->format('d M Y') . ' - ' . Carbon::now()->endOfWeek()->format('d M Y') . ')';
                break;
            case 'today':
                $baseQuery->whereDate('created_at', Carbon::today());
                $periodLabel = 'Hari Ini (' . Carbon::today()->format('d M Y') . ')';
                break;
            case 'this_month':
            default:
                $baseQuery->whereMonth('created_at', Carbon::now()->month);
                $periodLabel = 'Bulan Ini (' . Carbon::now()->format('F Y') . ')';
                break;
        }

        $pesans = $baseQuery->latest()->get();
        $totalRevenue = $pesans->sum('total');
        $totalSubtotal = $pesans->sum('subtotal');
        $totalService = $pesans->sum('service_charge');
        $totalPb1 = $pesans->sum('pajak_pb1');
        $totalOrders = $pesans->count();

        $pdf = Pdf::loadView('pdf.penjualan', [
            'pesans' => $pesans,
            'totalRevenue' => $totalRevenue,
            'totalSubtotal' => $totalSubtotal,
            'totalService' => $totalService,
            'totalPb1' => $totalPb1,
            'totalOrders' => $totalOrders,
            'periodLabel' => $periodLabel,
            'printDate' => Carbon::now()->timezone('Asia/Jakarta')->format('d F Y H:i:s') . ' WIB'
        ]);

        return $pdf->download('Laporan_Penjualan_QRasa_' . Carbon::now()->format('Ymd') . '.pdf');
    }

    /**
     * Export sales report as CSV for Excel.
     */
    public function exportCsv(Request $request)
    {
        $user = Auth::user();
        if ($user->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }

        $period = $request->input('period', 'this_month');
        $baseQuery = \App\Models\Pesan::with(['menus', 'meja']);

        switch ($period) {
            case 'this_week':
                $baseQuery->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
                $periodLabel = 'Minggu Ini';
                break;
            case 'today':
                $baseQuery->whereDate('created_at', Carbon::today());
                $periodLabel = 'Hari Ini (' . Carbon::today()->format('d M Y') . ')';
                break;
            case 'this_month':
            default:
                $baseQuery->whereMonth('created_at', Carbon::now()->month);
                $periodLabel = 'Bulan Ini (' . Carbon::now()->format('F Y') . ')';
                break;
        }

        $pesans = $baseQuery->latest()->get();

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="Laporan_Penjualan_QRasa_' . Carbon::now()->format('Ymd') . '.csv"',
        ];

        $callback = function () use ($pesans, $periodLabel) {
            $file = fopen('php://output', 'w');
            // BOM for Excel UTF-8
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Report header
            fputcsv($file, ['Laporan Rekapitulasi Penjualan - ' . $periodLabel]);
            fputcsv($file, ['Dicetak pada: ' . Carbon::now()->timezone('Asia/Jakarta')->format('d F Y H:i:s') . ' WIB']);
            fputcsv($file, []); // blank row

            fputcsv($file, [
                'No', 'Tanggal', 'ID Pesanan', 'Meja', 'Metode Bayar',
                'Subtotal', 'Service Charge', 'PB1', 'Grand Total'
            ]);

            $totalSubtotal = 0;
            $totalService = 0;
            $totalPb1 = 0;
            $totalRevenue = 0;

            foreach ($pesans as $i => $pesan) {
                $totalSubtotal += $pesan->subtotal;
                $totalService += $pesan->service_charge;
                $totalPb1 += $pesan->pajak_pb1;
                $totalRevenue += $pesan->total;

                fputcsv($file, [
                    $i + 1,
                    $pesan->created_at->timezone('Asia/Jakarta')->format('d/m/Y H:i'),
                    '#' . $pesan->id,
                    $pesan->meja ? 'Meja ' . $pesan->meja->nomor_meja : 'Bawa Pulang',
                    strtoupper($pesan->payment_method ?? 'N/A'),
                    $pesan->subtotal,
                    $pesan->service_charge,
                    $pesan->pajak_pb1,
                    $pesan->total,
                ]);
            }

            fputcsv($file, []); // blank row
            fputcsv($file, ['', '', '', '', 'TOTAL:', $totalSubtotal, $totalService, $totalPb1, $totalRevenue]);
            fputcsv($file, ['', '', '', '', 'Jumlah Transaksi:', $pesans->count() . ' bon']);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
