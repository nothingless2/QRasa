<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Semua QR Meja - QRasa</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;700;800;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Outfit', sans-serif; background: #e5e7eb; margin: 0; padding: 20px; }

        .qr-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            max-width: 900px;
            margin: 0 auto;
        }

        .qr-card {
            background: white;
            border: 3px solid #ea580c;
            border-radius: 16px;
            padding: 24px 16px;
            text-align: center;
            position: relative;
            overflow: hidden;
            break-inside: avoid;
        }

        .qr-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 6px;
            background: linear-gradient(to right, #f97316, #dc2626);
        }

        .logo-text {
            font-size: 1.5rem;
            font-weight: 900;
            background: linear-gradient(to right, #ea580c, #dc2626);
            -webkit-background-clip: text;
            color: transparent;
            margin-bottom: 4px;
            letter-spacing: -1px;
        }

        .qr-img-wrap {
            padding: 12px;
            background: white;
            border-radius: 12px;
            display: inline-block;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            margin: 12px 0;
            border: 1px solid #f3f4f6;
        }

        .meja-number {
            background: #f9fafb;
            border-radius: 12px;
            padding: 12px 8px;
            border: 1px solid #f3f4f6;
        }

        @media print {
            body { background: white !important; padding: 0 !important; }
            .qr-grid { gap: 12px; max-width: 100%; }
            .qr-card {
                border: 2px solid #000 !important;
                border-radius: 10px !important;
                padding: 16px 10px;
            }
            .qr-card::before { display: none; }
            .logo-text {
                background: none !important;
                color: #000 !important;
                -webkit-text-fill-color: #000 !important;
            }
            .qr-img-wrap { box-shadow: none !important; border: 1px solid #e5e7eb !important; }
            .no-print { display: none !important; }
            * { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }

            /* Page breaks every 6 cards (2 rows of 3) */
            .qr-card:nth-child(6n+1) { page-break-before: auto; }
            .qr-grid { page-break-inside: auto; }
        }
    </style>
</head>
<body>

    <!-- Action Buttons (Hidden on print) -->
    <div class="no-print max-w-[900px] mx-auto mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Cetak Semua QR Code Meja</h1>
            <p class="text-gray-700 text-sm mt-1">Total {{ $mejas->count() }} meja — Layout grid 3 per baris, siap dipotong dan dipasang di akrilik.</p>
        </div>
        <div class="flex space-x-3">
            <button onclick="window.print()" class="bg-orange-600 text-white px-6 py-2.5 rounded-xl font-bold hover:bg-orange-700 transition shadow-md flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                Cetak Semua
            </button>
            <a href="{{ route('meja.index') }}" class="bg-white text-gray-700 border border-gray-400 px-6 py-2.5 rounded-xl font-bold hover:bg-gray-50 transition shadow-sm">
                Kembali
            </a>
        </div>
    </div>

    <!-- QR Code Grid -->
    <div class="qr-grid">
        @foreach($mejas as $meja)
        <div class="qr-card">
            <h2 class="logo-text">QRasa</h2>
            <p class="text-gray-600 text-xs font-medium">Scan untuk memesan</p>

            <div class="qr-img-wrap">
                <img src="{{ asset('storage/' . $meja->qr_code) }}" alt="QR Meja {{ $meja->nomor_meja }}" class="w-32 h-32 mx-auto">
            </div>

            <div class="meja-number">
                <p class="text-gray-600 uppercase tracking-[0.15em] text-[10px] font-bold mb-1">Meja Nomor</p>
                <h3 class="text-4xl font-black text-gray-800">{{ $meja->nomor_meja }}</h3>
            </div>
        </div>
        @endforeach
    </div>

</body>
</html>
