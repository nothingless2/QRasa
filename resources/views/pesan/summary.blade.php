{{-- pesan/summary.blade.php --}}
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ringkasan Pesanan - QRasa</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@800&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" href="{{ asset('img/Logo/LogoKantin.png') }}">

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        orange-600: "#3f7d58",
                        orange-700: "#537d5d",
                        oren: "#ef9651",
                        orenTua: "#ec5228",
                        putih: "#efefef",
                    }
                }
            }
        }
    </script>
</head>

<body class="bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-md border-b border-gray-100">
        <div class="container mx-auto px-4">
            <div class="flex items-center justify-center p-4">
                <a href="{{ route('menu.show') }}" class="flex items-center gap-3">
                    <img src="{{ asset('img/Logo/LogoKantin.png') }}" alt="Logo QRasa"
                        class="w-10 h-10 rounded-full object-cover shadow-sm">
                    <span class="text-2xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-orange-500 to-red-600 tracking-tighter drop-shadow-sm" style="font-family: 'Outfit', sans-serif;">QRasa</span>
                </a>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="py-10">
        <div class="container mx-auto px-4">
            <div class="max-w-2xl mx-auto bg-white rounded-3xl shadow-xl p-8 border border-gray-50">
                <div class="text-center mb-8">
                    <div class="w-20 h-20 bg-green-50 rounded-full flex items-center justify-center mx-auto mb-5 shadow-inner border border-green-100">
                        <i class="fas fa-check text-green-500 text-3xl"></i>
                    </div>
                    <h1 class="text-3xl font-extrabold text-gray-800 tracking-tight mb-2">Pesanan Berhasil!</h1>
                    <p class="text-gray-700 font-medium">Terima kasih atas pesanan Anda</p>
                </div>

                <div class="border-b border-dashed border-gray-300 pb-5 mb-5">
                    <h2 class="text-xl font-bold text-gray-800 mb-4">Detail Pesanan</h2>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-600">ID Pesanan:</span>
                            <span class="font-medium">#{{ $pesan->id }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Meja:</span>
                            <span class="font-medium">{{ $pesan->meja ? $pesan->meja->nomor_meja : 'N/A' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Status:</span>
                            <span
                                class="px-2 py-1 rounded-full text-xs font-medium
                                {{ $pesan->status === 'sedang diproses' ? 'bg-orange-100 text-orenTua'  : 'bg-green-100 text-green-800' }}">
                                {{ ucfirst($pesan->status) }}
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Metode Pembayaran:</span>
                            <span class="font-medium">{{ ucfirst($pesan->payment_method) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Waktu Pesan:</span>
                            <span class="font-medium">{{ $pesan->created_at->format('d/m/Y H:i') }}</span>
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <h3 class="text-lg font-semibold mb-3">Item Pesanan</h3>
                    <div class="space-y-3">
                        @foreach ($pesan->menus as $menu)
                            <div class="flex justify-between items-start py-3 border-b last:border-b-0">
                                <div class="flex-1">
                                    <p class="font-medium text-gray-800">{{ $menu->nama }}</p>
                                    <p class="text-sm text-gray-600">
                                        {{ $menu->pivot->quantity }} x Rp
                                        {{ number_format($menu->harga, 0, ',', '.') }}
                                    </p>
                                </div>
                                <div class="text-right">
                                    <p class="font-medium text-gray-800">
                                        Rp {{ number_format($menu->harga * $menu->pivot->quantity, 0, ',', '.') }}
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="border-t border-dashed border-gray-300 pt-5 mb-8 mt-2 space-y-3">
                    <div class="flex justify-between items-center text-sm font-medium text-gray-600">
                        <span>Subtotal Netto</span>
                        <span>Rp {{ number_format($pesan->subtotal > 0 ? $pesan->subtotal : $pesan->total, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between items-center text-sm font-medium text-gray-700">
                        <span>Servis (5%)</span>
                        <span>Rp {{ number_format($pesan->service_charge, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between items-center text-sm font-medium text-gray-700 pb-3 border-b border-gray-100">
                        <span>Pajak Restoran PB1 (11%)</span>
                        <span>Rp {{ number_format($pesan->pajak_pb1, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between items-center text-xl font-black text-gray-800 pt-1">
                        <span>Total Pembayaran Akhir</span>
                        <span class="text-orange-600">Rp {{ number_format($pesan->total, 0, ',', '.') }}</span>
                    </div>
                </div>

                <div class="text-center space-y-4">
                    <div class="bg-orange-50 border border-orange-100 rounded-2xl p-4 shadow-sm">
                        <p class="text-sm font-medium text-orange-800">
                            <i class="fas fa-clock mr-2 text-orange-500"></i>
                            Pesanan Anda sedang diproses. Silakan menunggu hidangan disajikan.
                        </p>
                    </div>

                    <a href="{{ route('menu.show') }}"
                        class="inline-flex items-center justify-center w-full sm:w-auto bg-gradient-to-r from-orange-500 to-orange-600 text-white px-8 py-3.5 rounded-xl hover:from-orange-600 hover:to-orange-700 transition-all font-bold shadow-md shadow-orange-500/30">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Kembali ke Menu Utama
                    </a>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-gray-50 text-gray-600 py-6 mt-4">
        <div class="container mx-auto text-center border-t border-gray-300 pt-6">
            <p class="text-sm font-semibold">© {{ date('Y') }} QRasa. Hak cipta dilindungi.</p>
        </div>
    </footer>
</body>

</html>
