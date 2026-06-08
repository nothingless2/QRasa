<?php

namespace App\Http\Controllers;

use App\Models\Meja;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class MejaController extends Controller
{
    public function index(Request $request)
    {
        $query = Meja::query();

        if ($request->has('search')) {
            $query->where('nomor_meja', 'like', '%' . $request->search . '%');
        }

        $mejas = $query->paginate(5);

        return view('meja.index', compact('mejas'));
    }

    public function create()
    {
        return view('meja.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nomor_meja' => 'required|integer|unique:mejas',
        ]);

        $meja = new Meja();
        $meja->nomor_meja = $request->nomor_meja;
        $meja->save(); // Save to get the ID

        $qrCodeUrl = route('menu.show', ['meja_id' => $meja->id]);
        $qrCode = QrCode::size(200)->generate($qrCodeUrl);

        $meja->qr_code = 'qrcodes/meja-' . $meja->id . '.svg';

        // Ensure the directory exists
        $directory = storage_path('app/public/qrcodes');
        if (!file_exists($directory)) {
            mkdir($directory, 0755, true);
        }

        file_put_contents(storage_path('app/public/' . $meja->qr_code), $qrCode);

        $meja->save();

        return redirect()->route('meja.index')->with('success', 'Meja berhasil ditambahkan.');
    }

    public function show(Meja $meja)
    {
        return view('meja.show', compact('meja'));
    }

    public function print(Meja $meja)
    {
        return view('meja.print', compact('meja'));
    }

    public function edit(Meja $meja)
    {
        return view('meja.edit', compact('meja'));
    }

    public function update(Request $request, Meja $meja)
    {
        $request->validate([
            'nomor_meja' => 'required|integer|unique:mejas,nomor_meja,' . $meja->id,
        ]);

        $meja->nomor_meja = $request->nomor_meja;
        $meja->save();

        return redirect()->route('meja.index')->with('success', 'Meja berhasil diperbarui.');
    }

    public function destroy(Meja $meja)
    {
        // Delete the QR code file
        if (file_exists(storage_path('app/public/' . $meja->qr_code))) {
            unlink(storage_path('app/public/' . $meja->qr_code));
        }

        $meja->delete();

        return redirect()->route('meja.index')->with('success', 'Meja berhasil dihapus.');
    }

    /**
     * Print all table QR codes in a grid layout for batch printing.
     */
    public function printAll()
    {
        $mejas = Meja::orderBy('nomor_meja', 'asc')->get();
        return view('meja.print-all', compact('mejas'));
    }
}
