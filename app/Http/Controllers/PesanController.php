<?php
namespace App\Http\Controllers;

use App\Models\Pesan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Throwable;

class PesanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        // Tampilkan semua pesanan tanpa filter berdasarkan user
        $query = Pesan::with(['menus', 'meja']);

        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        if ($request->has('payment_method') && $request->payment_method != '') {
            $query->where('payment_method', $request->payment_method);
        }

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function(\Illuminate\Database\Eloquent\Builder $q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                  ->orWhereHas('meja', function(\Illuminate\Database\Eloquent\Builder $mejaQuery) use ($search) {
                      $mejaQuery->where('nomor_meja', 'like', "%{$search}%");
                  });
            });
        }

        $pesans = $query->latest()->paginate(10);

        // Get unique statuses and payment methods for filter options
        $statuses = Pesan::distinct()->pluck('status')->toArray();
        $statuses = array_diff($statuses, ['sedang diproses', 'pending']);

        $paymentMethods = Pesan::distinct()->pluck('payment_method')->toArray();
        if (!in_array('QRIS', $paymentMethods)) {
            $paymentMethods[] = 'QRIS';
        }
        // Sort payment methods alphabetically for better UX
        sort($paymentMethods);

        // Batasi akses hanya untuk role admin, chef, waiter, dan cashier
        if (!in_array($user->role, ['admin', 'chef', 'waiter', 'cashier'])) {
            abort(403, 'Unauthorized action.');
        } else {
            $mejas = \App\Models\Meja::all();
            return view('pesan.index', compact('pesans', 'statuses', 'paymentMethods', 'mejas'));
        }

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        try {
            return \Illuminate\Support\Facades\DB::transaction(function () use ($request) {
                $request->validate([
                'cartItems'            => 'required|array',
                'cartItems.*.id'       => 'required|exists:menus,id',
                'cartItems.*.quantity' => 'required|integer|min:1',
                'cartItems.*.notes'    => 'nullable|string|max:255',
                'total'                => 'required|numeric|min:0',
                'payment_method'       => 'required|string',
                'meja_id'              => 'nullable|exists:mejas,id',
            ]);

            $createdPesanIds = [];

            // Calculate total and prepare menu quantities for this single order
            $total          = 0;
            $menuQuantities = [];
            $menuIds        = collect($request->cartItems)->pluck('id')->unique()->all();
            $allMenus       = \App\Models\Menu::whereIn('id', $menuIds)->get()->keyBy('id');

            foreach ($request->cartItems as $item) {
                $menu = $allMenus->get($item['id']);
                if (! $menu) {
                    continue;
                }

                // Validasi stok
                if ($menu->stok <= 0) {
                    return response()->json(['success' => false, 'message' => "Stok untuk menu '{$menu->nama}' sudah habis."], 400);
                }
                if ($item['quantity'] > $menu->stok) {
                    return response()->json(['success' => false, 'message' => "Stok untuk menu '{$menu->nama}' tidak mencukupi."], 400);
                }

                // Terapkan diskon
                $hargaSetelahDiskon = $menu->harga - ($menu->harga * (($menu->diskon ?? 0) / 100));
                $total += ($hargaSetelahDiskon * $item['quantity']);
                $menuQuantities[$item['id']] = [
                    'quantity' => $item['quantity'],
                    'notes'    => $item['notes'] ?? null,
                ];

                // Kurangi stok menu
                $menu->stok -= $item['quantity'];
                $menu->save();
            }

            // Dynamic Settings Fetch for Taxes
            $setting      = \App\Models\Setting::first();
            $tax_rate     = $setting ? ($setting->tax_percent / 100) : 0.11;
            $service_rate = $setting ? ($setting->service_percent / 100) : 0.05;

            // Tax Calculations
            $subtotal       = $total; // the aggregated sum of core items
            $service_charge = (int) round($subtotal * $service_rate);
            $dpp            = $subtotal + $service_charge;
            $pajak_pb1      = (int) round($dpp * $tax_rate);
            $grandTotal     = $dpp + $pajak_pb1; // The final checkout amount

            // Create the Pesan entry
            $pesan                    = new Pesan();
            $pesan->meja_id           = $request->meja_id;
            $pesan->subtotal          = $subtotal;
            $pesan->service_charge    = $service_charge;
            $pesan->pajak_pb1         = $pajak_pb1;
            $pesan->total             = $grandTotal;
            $pesan->status            = 'belum diantar';
            $pesan->status_pembayaran = 'belum dibayar';
            $pesan->payment_method    = $request->payment_method;
            $pesan->save();

            $pesan->menus()->attach($menuQuantities);
            $createdPesanIds[] = $pesan->id;

            if ($request->ajax()) {
                return response()->json([
                    'success'           => true,
                    'message'           => 'Pesanan berhasil dibuat!',
                    'redirect'          => route('pesan.summary', ['pesan' => $pesan->id]),
                    'created_pesan_ids' => $createdPesanIds,
                ]);
            }

                // For non-AJAX requests, redirect to the index page to see all orders
                return redirect()->route('pesan.index')->with('success', 'Pesanan berhasil dibuat!');
            });
        } catch (Throwable $e) {
            // Log the error for debugging
            \Log::error('Checkout Error: ' . $e->getMessage(), [
                'file'         => $e->getFile(),
                'line'         => $e->getLine(),
                'trace'        => $e->getTraceAsString(),
                'request_data' => $request->all(),
            ]);

            // Return a JSON error response
            if ($request->ajax()) {
                return response()->json([
                    'success'       => false,
                    'message'       => 'Terjadi kesalahan saat memproses pesanan: ' . $e->getMessage(),
                    'error_details' => [
                        'file' => $e->getFile(),
                        'line' => $e->getLine(),
                    ],
                ], 500);
            }

            // For non-AJAX requests, redirect back with error
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memproses pesanan. Silakan coba lagi.');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function show(Pesan $pesan)
    {
        $pesan->load(['menus', 'meja']);
        return view('pesan.summary', compact('pesan'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Pesan $pesan)
    {
        //
    }

    public function updateStatus(Pesan $pesan)
    {
        $pesan->update(['status' => 'sudah diantar']);
        return redirect()->route('pesan.index')->with('success', 'Status pesanan berhasil diperbarui!');
    }

    // Tambahan baru
    public function updateStatusPembayaran(Request $request, Pesan $pesan)
    {
        $updateData = ['status_pembayaran' => 'sudah dibayar'];
        
        if ($request->has('payment_method') && in_array(strtolower($request->payment_method), ['tunai', 'qris', 'transfer'])) {
            $updateData['payment_method'] = strtolower($request->payment_method);
        }

        $pesan->update($updateData);

        // Flash session parameter to trigger auto-print popup on the frontend
        $request->session()->flash('print_struk_id', $pesan->id);

        return redirect()->route('pesan.index')->with('success', 'Status pembayaran berhasil diperbarui!');
    }

    public function strukKasir(Pesan $pesan)
    {
        // Eager load relationships to prevent N+1 Performance Issues on receipt loops
        $pesan->load(['menus', 'meja']);

        return view('pesan.struk', compact('pesan'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Pesan $pesan)
    {
        //
    }

    public function moveTable(Request $request, Pesan $pesan)
    {
        $request->validate([
            'meja_id' => 'required|exists:mejas,id',
        ]);

        if ($pesan->status_pembayaran === 'sudah dibayar') {
            return response()->json(['success' => false, 'message' => 'Pesanan yang sudah dibayar tidak dapat dipindah meja.'], 400);
        }

        $pesan->meja_id = $request->meja_id;
        $pesan->save();

        return response()->json([
            'success' => true,
            'message' => 'Meja berhasil dipindah.'
        ]);
    }

    public function splitBill(Request $request, Pesan $pesan)
    {
        $request->validate([
            'items' => 'required|array',
        ]);

        if ($pesan->status_pembayaran === 'sudah dibayar') {
            return response()->json(['success' => false, 'message' => 'Pesanan sudah dibayar, tidak dapat di-split.'], 400);
        }

        // Clone order shell
        $newPesan = $pesan->replicate();
        $newPesan->subtotal = 0;
        $newPesan->service_charge = 0;
        $newPesan->pajak_pb1 = 0;
        $newPesan->total = 0;
        $newPesan->save();

        // Fallback calculation: subtotal depends on historical or pure recalculation. 
        // We will subtract the moved items' net amounts.
        $originalSubtotal = (float) $pesan->subtotal;
        $newSubtotal = 0;

        foreach ($request->items as $menuId => $qtyToSplit) {
            $qtyToSplit = (int) $qtyToSplit;
            if ($qtyToSplit <= 0) continue;

            $existingPivot = $pesan->menus()->where('menu_id', $menuId)->first();
            if ($existingPivot) {
                $currentQty = (int) $existingPivot->pivot->quantity;
                $price = (float) ($existingPivot->harga - ($existingPivot->harga * (($existingPivot->diskon ?? 0) / 100)));

                if ($qtyToSplit >= $currentQty) {
                    // Pindah total item ke pesanan baru
                    $pesan->menus()->detach($menuId);
                    $newPesan->menus()->attach($menuId, [
                        'quantity' => $currentQty
                    ]);
                    $originalSubtotal -= ($currentQty * $price);
                    $newSubtotal += ($currentQty * $price);
                } else {
                    // Pindah parsial
                    $pesan->menus()->updateExistingPivot($menuId, [
                        'quantity' => $currentQty - $qtyToSplit
                    ]);
                    $newPesan->menus()->attach($menuId, [
                        'quantity' => $qtyToSplit
                    ]);
                    $originalSubtotal -= ($qtyToSplit * $price);
                    $newSubtotal += ($qtyToSplit * $price);
                }
            }
        }

        // Dynamic Settings Fetch for Taxes
        $setting      = \App\Models\Setting::first();
        $tax_rate     = $setting ? ($setting->tax_percent / 100) : 0.11;
        $service_rate = $setting ? ($setting->service_percent / 100) : 0.05;

        // Recalculate Taxes for the original ticket
        $pesan->subtotal = max(0, $originalSubtotal);
        $pesan->service_charge = (int) round($pesan->subtotal * $service_rate);
        $dpp1 = $pesan->subtotal + $pesan->service_charge;
        $pesan->pajak_pb1 = (int) round($dpp1 * $tax_rate);
        $pesan->total = $dpp1 + $pesan->pajak_pb1;
        $pesan->save();

        // Recalculate Taxes for the newly split ticket
        $newPesan->subtotal = $newSubtotal;
        $newPesan->service_charge = (int) round($newSubtotal * $service_rate);
        $dpp2 = $newPesan->subtotal + $newPesan->service_charge;
        $newPesan->pajak_pb1 = (int) round($dpp2 * $tax_rate);
        $newPesan->total = $dpp2 + $newPesan->pajak_pb1;
        $newPesan->save();

        // Hapus pesanan asli bila sudah tidak bersisa daftar menunya
        if ($pesan->menus()->count() == 0) {
            $pesan->delete();
        }

        return response()->json([
            'success' => true,
            'message' => 'Tagihan berhasil dipisah jadi dua struk.'
        ]);
    }
}
