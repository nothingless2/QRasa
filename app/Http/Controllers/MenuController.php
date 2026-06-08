<?php
namespace App\Http\Controllers;

use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class MenuController extends Controller
{
    public function __construct()
    {
        // $this->authorizeResource(Menu::class, 'menu'); // Keep this commented
    }

    // Admin view for managing menus
    public function index(Request $request)
    {
        $this->authorize('viewAny', Menu::class);

        $user = Auth::user();
        $query = Menu::query()->orderBy('created_at', 'desc');

        if ($user->role !== 'admin') {
            $query->where('user_id', $user->id);
        }

        // Filter by category if present
        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        $menu = $query->paginate(10);
        $kategori = Menu::select('kategori')->distinct()->pluck('kategori');

        return view('menu.index', compact('menu', 'kategori'));
    }

    // Public view for customers
    public function show(Request $request)
    {
        $query = Menu::query();
        $kategori = Menu::select('kategori')->distinct()->pluck('kategori');
        $historyPesanan = collect();

        // Filter by category if present
        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        $selectedKategori = $request->kategori;
        $menu = $query->get();

        // Pindahkan transformasi data ke controller
        $formattedMenu = $menu->map(function ($item) {
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

        if ($request->has('meja_id')) {
            $meja = \App\Models\meja::find($request->meja_id);
            if ($meja) {
                // Hanya ambil pesanan HARI INI yang BELUM DIBAYAR untuk meja ini
                $historyPesanan = $meja->pesans()
                    ->whereDate('created_at', \Carbon\Carbon::today())
                    ->where('status_pembayaran', '!=', 'sudah dibayar')
                    ->with('menus')
                    ->latest()
                    ->get();
            }
        }

        if ($request->ajax()) {
            return response()->json([
                'menu' => $formattedMenu, // Kembalikan data yang sudah diformat
                'kategori' => $kategori,
                'selectedKategori' => $selectedKategori,
                'historyPesanan' => $historyPesanan,
            ]);
        }

        return view('menu', compact('formattedMenu', 'kategori', 'selectedKategori', 'historyPesanan'));
    }

    public function create()
    {
        $this->authorize('create', Menu::class);
        return view('menu.create');
    }

    public function store(Request $request)
    {
        $this->authorize('create', Menu::class);
        $user = Auth::user();

        $validated = $request->validate([
            'nama'      => 'required|string|max:255',
            'harga'     => 'required|numeric|min:0',
            'stok'      => 'required|integer|min:0',
             'diskon'    => 'nullable|integer|min:0|max:100',
            'kategori'  => 'required|string',
            'deskripsi' => 'required|string',
            'gambar'    => 'nullable|image|mimes:jpeg,png,jpg,svg,webp|max:3048',
        ]);

        $menu = new Menu($validated);
        $menu->deskripsi = strip_tags($validated['deskripsi']);
        $menu->user_id = $user->id;

        if ($request->hasFile('gambar')) {
            $menu->gambar = $request->file('gambar')->store('images', 'public');
        }

        $menu->save();

        return redirect()
            ->route('menu.index')
            ->with('success', 'Menu berhasil ditambahkan!');
    }

    public function edit(Menu $menu)
    {
        $this->authorize('update', $menu);
        return view('menu.edit', compact('menu'));
    }

    public function update(Request $request, Menu $menu)
    {
        $this->authorize('update', $menu);

        $validated = $request->validate([
            'nama'      => 'required|string|max:255',
            'harga'     => 'required|numeric',
            'stok'      => 'required|numeric',
            'diskon'    => 'nullable|numeric',
            'kategori'  => 'required|string',
            'deskripsi' => 'required|string',
            'gambar'    => 'nullable|image|mimes:jpeg,png,jpg,svg,webp|max:3048',
        ]);

        $menu->fill($validated);
        $menu->deskripsi = strip_tags($validated['deskripsi']);

        if ($request->hasFile('gambar')) {
            if ($menu->gambar) {
                Storage::delete('public/' . $menu->gambar);
            }
            $menu->gambar = $request->file('gambar')->store('images', 'public');
        }

        $menu->save();
        return redirect()->route('menu.index')->with('success', 'Menu berhasil diperbarui!');
    }

    public function destroy(Menu $menu)
    {
        $this->authorize('delete', $menu);

        if ($menu->gambar) {
            Storage::delete('public/' . $menu->gambar);
        }
        $menu->delete();
        return redirect()->back()->with('success', 'Menu berhasil dihapus!');
    }
}
