<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Meja;
use Illuminate\Http\Request;

class PosController extends Controller
{
    public function index(Request $request)
    {
        $activeShift = \App\Models\Shift::where('user_id', auth()->id())->where('status', 'open')->first();

        // Get all menus formatted for Alpine.js
        $menus = Menu::all()->map(function ($item) {
            return [
                'id' => $item->id,
                'name' => $item->nama,
                'description' => $item->deskripsi,
                'price' => $item->harga,
                'discount' => $item->diskon ?? 0,
                'image' => asset('storage/' . $item->gambar),
                'stok' => $item->stok,
                'category' => $item->kategori ?? 'Lainnya',
            ];
        });

        // Get categories
        $kategori = Menu::select('kategori')->distinct()->pluck('kategori');

        // Get table list
        $mejas = Meja::orderBy('nomor_meja', 'asc')->get();

        // Get Unified Active Orders for the sliding panel (Limited to 60 for massive performance boost)
        $activeOrders = \App\Models\Pesan::with(['menus', 'meja'])
            ->whereDate('created_at', today())
            ->orderBy('created_at', 'desc')
            ->limit(60)
            ->get();

        return view('pos.index', compact('menus', 'kategori', 'mejas', 'activeShift', 'activeOrders'));
    }
}
