<?php

namespace App\Http\Controllers;

use App\Models\Pesan;
use Illuminate\Http\Request;
use Carbon\Carbon;

class KdsController extends Controller
{
    public function index()
    {
        return view('kds.index');
    }

    public function getActiveOrders()
    {
        // Fetch active orders sorted by oldest first (FIFO)
        $orders = Pesan::with(['menus', 'meja'])
            ->whereIn('status', ['belum diantar', 'sedang diproses'])
            ->whereDate('created_at', Carbon::today())
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function ($pesan) {
                return [
                    'id'           => $pesan->id,
                    'created_at'   => $pesan->created_at->format('H:i'),
                    'time_minutes' => $pesan->created_at->diffInMinutes(Carbon::now()),
                    'meja'         => $pesan->meja ? 'Meja ' . $pesan->meja->nomor_meja : 'Takeaway',
                    'status'       => $pesan->status,
                    'is_new'       => str_starts_with($pesan->status, 'belum'),
                    'menus'        => $pesan->menus->map(function ($menu) {
                        return [
                            'name'     => $menu->nama,
                            'quantity' => $menu->pivot->quantity,
                            'notes'    => $menu->pivot->notes ?? null,
                        ];
                    }),
                ];
            });

        return response()->json($orders);
    }

    public function updateStatus(Request $request, $id)
    {
        $pesan = Pesan::findOrFail($id);

        $request->validate([
            'status' => 'required|string'
        ]);

        $pesan->status = $request->status;
        $pesan->save();

        return response()->json([
            'success'    => true,
            'message'    => 'Pesanan #' . $id . ' diperbarui.',
            'new_status' => $pesan->status
        ]);
    }
}
