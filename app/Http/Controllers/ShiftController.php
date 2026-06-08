<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Shift;
use App\Models\Pesan;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class ShiftController extends Controller
{
    public function index(Request $request)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403);
        }

        $query = Shift::with('user');

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function(\Illuminate\Database\Eloquent\Builder $q) use ($search) {
                $q->where('status', 'like', "%{$search}%")
                  ->orWhereHas('user', function(\Illuminate\Database\Eloquent\Builder $userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $shifts = $query->orderBy('created_at', 'desc')->paginate(10);
        return view('admin.shifts.index', compact('shifts'));
    }

    public function startShift(Request $request)
    {
        $request->validate([
            'starting_cash' => 'required|numeric|min:0',
        ]);

        $activeShift = Shift::where('user_id', Auth::id())->where('status', 'open')->first();
        if ($activeShift) {
            return response()->json(['success' => false, 'message' => 'Anda masih memiliki shift yang aktif.'], 400);
        }

        $shift = Shift::create([
            'user_id' => Auth::id(),
            'shift_start' => Carbon::now(),
            'starting_cash' => $request->starting_cash,
            'status' => 'open',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Shift berhasil dibuka.',
            'shift' => $shift
        ]);
    }

    public function endShift(Request $request)
    {
        $request->validate([
            'ending_cash_actual' => 'required|numeric|min:0',
        ]);

        $shift = Shift::where('user_id', Auth::id())->where('status', 'open')->first();
        if (!$shift) {
            return response()->json(['success' => false, 'message' => 'Tidak ada shift aktif.'], 400);
        }

        $tunaiSales = Pesan::where('payment_method', 'tunai')
            ->where('status_pembayaran', 'sudah dibayar')
            ->where('updated_at', '>=', $shift->shift_start)
            ->sum('total');

        // Calculate total expenses for this shift
        $totalExpenses = $shift->expenses()->sum('amount');

        $expected = $shift->starting_cash + $tunaiSales - $totalExpenses;
        $actual = $request->ending_cash_actual;
        $variance = $actual - $expected;

        $shift->update([
            'shift_end'            => Carbon::now(),
            'ending_cash_expected' => $expected,
            'ending_cash_actual'   => $actual,
            'total_expenses'       => $totalExpenses,
            'variance'             => $variance,
            'status'               => 'closed',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Shift berhasil ditutup.',
            'summary' => [
                'expected'       => $expected,
                'actual'         => $actual,
                'variance'       => $variance,
                'total_expenses' => $totalExpenses,
            ]
        ]);
    }

    /**
     * Export shift history as CSV.
     */
    public function exportCsv(Request $request)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403);
        }

        $shifts = Shift::with(['user', 'expenses'])->orderBy('created_at', 'desc')->get();

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="Rekap_Shift_Kasir_' . date('Ymd') . '.csv"',
        ];

        $callback = function () use ($shifts) {
            $file = fopen('php://output', 'w');
            // BOM for Excel UTF-8
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($file, [
                'No', 'Kasir', 'Mulai Shift', 'Selesai Shift',
                'Modal Awal', 'Penjualan Tunai', 'Pengeluaran',
                'Target Sistem', 'Aktual Fisik', 'Selisih', 'Status'
            ]);

            foreach ($shifts as $i => $shift) {
                $salesDuringShift = $shift->ending_cash_expected
                    ? ($shift->ending_cash_expected - $shift->starting_cash + $shift->total_expenses)
                    : 0;

                fputcsv($file, [
                    $i + 1,
                    $this->sanitizeCsv($shift->user->name ?? 'N/A'),
                    $shift->shift_start ? $shift->shift_start->timezone('Asia/Jakarta')->format('d/m/Y H:i') : '-',
                    $shift->shift_end ? $shift->shift_end->timezone('Asia/Jakarta')->format('d/m/Y H:i') : '-',
                    $shift->starting_cash,
                    $salesDuringShift,
                    $shift->total_expenses,
                    $shift->ending_cash_expected ?? '-',
                    $shift->ending_cash_actual ?? '-',
                    $shift->variance ?? '-',
                    $shift->status === 'open' ? 'AKTIF' : 'SELESAI',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Sanitize CSV values to prevent formula injection.
     */
    private function sanitizeCsv(string $value): string
    {
        if (in_array(substr($value, 0, 1), ['=', '+', '-', '@'], true)) {
            return "'" . $value;
        }
        return $value;
    }
}

