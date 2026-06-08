<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Pesanan #{{ $pesan->id }}</title>
    <!-- Tailwind CSS for utility classes -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @page { margin: 0; }
        body { 
            margin: 0;
            padding: 10px;
            font-family: 'Courier New', Courier, monospace; /* Monospace for receipts */
            font-size: 12px;
            color: #000;
            width: 58mm; /* Standard Thermal paper width */
            text-align: left;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .flex { display: flex; }
        .justify-between { justify-content: space-between; }
        .border-b { border-bottom: 1px dashed #000; }
        .border-t { border-top: 1px dashed #000; }
        .mb-1 { margin-bottom: 4px; }
        .mb-2 { margin-bottom: 8px; }
        .my-2 { margin-top: 8px; margin-bottom: 8px; }
        
        @media print {
            body { 
                width: 100%;
            }
        }
    </style>
</head>
<body>
    @php $setting = \App\Models\Setting::first(); @endphp
    <div class="text-center font-bold mb-1" style="font-size: 16px;">
        {{ $setting ? $setting->store_name : 'QRasa Resto' }}
    </div>
    <div class="text-center mb-2" style="font-size: 10px;">
        {!! $setting ? nl2br(e($setting->store_address)) : 'Jl. Kuliner No. 1, Jakarta<br>Telp: 0812-3456-7890' !!}
    </div>
    
    <div class="border-b my-2"></div>
    
    <div style="font-size: 11px;">
        <div>No: #{{ $pesan->id }}</div>
        <div>Tgl: {{ $pesan->created_at->format('d/m/Y H:i') }}</div>
        <div>Kasir: {{ auth()->user()->name ?? 'Kasir' }}</div>
        <div>Meja: {{ $pesan->meja->nomor_meja ?? 'Bawa Pulang' }}</div>
    </div>
    
    <div class="border-b my-2"></div>
    
    @php
        $subtotal = 0;
    @endphp

    @foreach ($pesan->menus as $menu)
        @php
            $qty = (int) ($menu->pivot->quantity ?? $menu->pivot->jumlah ?? 1);
            $hargaSatuan = $menu->harga - ($menu->harga * (($menu->diskon ?? 0)/100));
            $totalItem = $qty * $hargaSatuan;
            $subtotal += $totalItem;
        @endphp
        <div class="mb-1 uppercase" style="font-size: 11px;">{{ substr($menu->nama, 0, 19) }}</div>
        @if($menu->pivot->notes)
            <div style="font-size: 9px; font-style: italic; color: #666; margin-bottom: 2px;">  >> {{ $menu->pivot->notes }}</div>
        @endif
        <div class="flex justify-between" style="font-size: 11px;">
            <div>{{ $qty }} x {{ number_format($hargaSatuan, 0, ',', '.') }}</div>
            <div>{{ number_format($totalItem, 0, ',', '.') }}</div>
        </div>
    @endforeach
    
    <div class="border-t my-2"></div>
    
    <div class="flex justify-between" style="font-size: 11px;">
        <div>Subtotal</div>
        <div>Rp{{ number_format($pesan->subtotal > 0 ? $pesan->subtotal : $pesan->total, 0, ',', '.') }}</div>
    </div>
    <div class="flex justify-between" style="font-size: 11px;">
        <div>Service ({{ $setting ? $setting->service_percent : 5 }}%)</div>
        <div>Rp{{ number_format($pesan->service_charge, 0, ',', '.') }}</div>
    </div>
    <div class="flex justify-between" style="font-size: 11px;">
        <div>PB1 ({{ $setting ? $setting->tax_percent : 11 }}%)</div>
        <div>Rp{{ number_format($pesan->pajak_pb1, 0, ',', '.') }}</div>
    </div>
    
    <div class="border-t my-2"></div>
    
    <div class="flex justify-between font-bold" style="font-size: 12px;">
        <div>GRAND TOTAL</div>
        <div>Rp{{ number_format($pesan->total, 0, ',', '.') }}</div>
    </div>
    <div class="flex justify-between" style="font-size: 11px;">
        <div>Metode Pay:</div>
        <div class="uppercase font-bold">{{ $pesan->payment_method ?? 'TUNAI' }}</div>
    </div>
    
    <div class="border-t my-2"></div>
    
    <div class="text-center mt-4" style="font-size: 10px;">
        {!! $setting ? nl2br(e($setting->receipt_footer)) : 'Terima kasih atas kunjungan Anda!<br>Silakan datang kembali :)' !!}
    </div>

    <!-- Auto Print Interceptor Script -->
    <script>
        window.onload = function() {
            window.print();
            // Automatically close the window popup right after printing dialogue finishes
            setTimeout(function() {
                window.close();
            }, 1000);
        }
    </script>
</body>
</html>
