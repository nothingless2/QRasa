<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak QR Meja {{ $meja->nomor_meja }} - QRasa</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;700;800;900&display=swap" rel="stylesheet">
    <style>
        body { 
            font-family: 'Outfit', sans-serif; 
            background: #e5e7eb; 
            display: flex; 
            justify-content: center; 
            align-items: center; 
            min-height: 100vh; 
            margin: 0; 
        }
        
        /* The physical card shape */
        .print-card { 
            background: white; 
            width: 450px; 
            padding: 50px 40px; 
            border-radius: 20px; 
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04); 
            text-align: center; 
            border: 4px solid #ea580c; /* orange-600 */
            position: relative;
            overflow: hidden;
        }

        /* Decorative top accent */
        .print-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 12px;
            background: linear-gradient(to right, #f97316, #dc2626);
        }

        /* Container for QR code to stand out */
        .qr-container { 
            padding: 25px; 
            background: white; 
            border-radius: 16px; 
            display: inline-block; 
            box-shadow: 0 4px 15px rgba(0,0,0,0.08); 
            margin: 35px 0; 
            border: 1px solid #f3f4f6;
        }

        /* Gorgeous QRasa logo styling */
        .logo-text { 
            font-size: 3rem; 
            font-weight: 900; 
            background: linear-gradient(to right, #ea580c, #dc2626); 
            -webkit-background-clip: text; 
            color: transparent; 
            margin-bottom: 5px; 
            letter-spacing: -1px;
        }

        /* Print Media Queries - Only prints the card and strips everything else */
        @media print {
            body { 
                background: white !important; 
                display: block; 
                align-items: start;
            }
            .print-card { 
                box-shadow: none !important; 
                border: 4px solid #000 !important; /* Switch bounds to black ink */
                border-radius: 15px !important;
                margin: 0 auto; 
                width: 100%; 
                max-width: 500px; 
                page-break-inside: avoid; 
            }
            .print-card::before { display: none; } /* Remove gradient top accent for printers */
            .logo-text { 
                background: none !important; 
                color: #000 !important; /* Force black ink for thermal/B&W */
                -webkit-text-fill-color: #000 !important;
            }
            .qr-container { box-shadow: none !important; border: 2px solid #e5e7eb !important; }
            .no-print { display: none !important; }
            * { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
        }
    </style>
</head>
<body onload="setTimeout(() => window.print(), 500)">

    <div class="print-card">
        <h1 class="logo-text">QRasa</h1>
        <p class="text-gray-700 font-medium text-lg">Scan barcode ini untuk memesan</p>
        
        <div class="qr-container">
            <img src="{{ asset('storage/' . $meja->qr_code) }}" alt="QR Code" class="w-56 h-56 mx-auto">
        </div>
        
        <div class="bg-gray-50 rounded-2xl py-6 mt-2 border border-gray-100">
            <p class="text-gray-700 uppercase tracking-[0.2em] text-sm font-bold mb-1">Meja Nomor</p>
            <h2 class="text-7xl font-black text-gray-800">{{ $meja->nomor_meja }}</h2>
        </div>
        
        <p class="text-sm text-gray-600 font-medium mt-8 pt-6 border-t border-gray-100">
            Terintegrasi dengan sistem pemesanan QRasa.<br>Semua pesanan akan dilayani ke nomor meja ini.
        </p>
    </div>

    <!-- Floating Action Buttons - Hidden during physical print -->
    <div class="no-print fixed bottom-8 right-8 flex flex-col space-y-3">
        <button onclick="window.print()" class="bg-orange-600 text-white px-6 py-3 rounded-xl shadow-lg font-bold hover:bg-orange-700 flex justify-center items-center transition-transform hover:scale-105">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
            Cetak Ulang Card
        </button>
        <a href="{{ route('meja.index') }}" class="bg-white text-gray-700 border border-gray-300 px-6 py-3 rounded-xl shadow-md font-bold hover:bg-gray-50 flex justify-center items-center text-center">
            Kembali ke Daftar Meja
        </a>
    </div>

</body>
</html>
