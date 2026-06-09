<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="overflow-x-hidden">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @php $setting = \App\Models\Setting::first(); @endphp
    <title>{{ $setting ? $setting->store_name : 'Kantin QRasa' }} - Nikmati Sensasi Rasa Terbaik</title>
    <meta name="description" content="{{ $setting ? $setting->store_name : 'QRasa' }} — Restoran dengan menu pilihan terbaik. Scan QR dan pesan langsung dari meja Anda!">
    <meta name="theme-color" content="#ea580c">

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('img/Logo/LogoKantin.png') }}"/>

    <!-- DNS Prefetch & Preconnect -->
    <link rel="preconnect" href="https://fonts.bunny.net" crossorigin>
    <link rel="preconnect" href="https://fonts.googleapis.com" crossorigin>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin>
    <link rel="dns-prefetch" href="https://unpkg.com">
    <link rel="dns-prefetch" href="https://images.unsplash.com">

    <!-- App CSS (Vite bundled — Tailwind) — load first as it is critical -->
    @vite(['resources/css/app.css'])

    <!-- Fonts with font-display:swap (text visible during font load) -->
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@800&display=swap" rel="stylesheet">

    <!-- Font Awesome — non-blocking (icons are NOT critical for first paint) -->
    <link rel="preload" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" as="style">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
          media="print" onload="this.media='all'; this.onload=null;">
    <noscript><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"></noscript>

    <!-- AOS Animation CSS — non-blocking (animation is progressive enhancement) -->
    <link rel="stylesheet" href="https://unpkg.com/aos@2.3.1/dist/aos.css"
          media="print" onload="this.media='all'; this.onload=null;">
    <noscript><link rel="stylesheet" href="https://unpkg.com/aos@2.3.1/dist/aos.css"></noscript>

    @php
        $heroUrl = ($setting && $setting->hero_bg)
            ? Storage::url($setting->hero_bg)
            : 'https://images.unsplash.com/photo-1554118811-1e0d58224f24?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80&fm=webp';
        $heroUrlMobile = ($setting && $setting->hero_bg)
            ? Storage::url($setting->hero_bg)
            : 'https://images.unsplash.com/photo-1554118811-1e0d58224f24?ixlib=rb-4.0.3&auto=format&fit=crop&w=768&q=65&fm=webp';
    @endphp
    <!-- Preload LCP hero image — biggest content element on screen (critical for LCP score) -->
    <link rel="preload" as="image"
          href="{{ $heroUrlMobile }}"
          imagesrcset="{{ $heroUrlMobile }} 768w, {{ $heroUrl }} 1920w"
          imagesizes="100vw"
          fetchpriority="high">

    <!-- Critical inline styles (prevents FOUC for above-the-fold content) -->
    <style>
        html { scroll-behavior: smooth; }
        #beranda { background-color: #1c1917; min-height: 100svh; }
        img { max-width: 100%; height: auto; }
    </style>

    @if($setting && $setting->google_analytics_id)
        <script async src="https://www.googletagmanager.com/gtag/js?id={{ $setting->google_analytics_id }}"></script>
        <script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}gtag('js',new Date());gtag('config','{{ $setting->google_analytics_id }}');</script>
    @endif
    @if($setting && $setting->facebook_pixel_id)
        <script>!function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,document,'script','https://connect.facebook.net/en_US/fbevents.js');fbq('init','{{ $setting->facebook_pixel_id }}');fbq('track','PageView');</script>
        <noscript><img height="1" width="1" style="display:none" src="https://www.facebook.com/tr?id={{ $setting->facebook_pixel_id }}&ev=PageView&noscript=1"/></noscript>
    @endif
</head>
<body class="antialiased bg-stone-50 text-gray-800 font-sans overflow-x-hidden">

    <!-- Navbar -->
    <header class="fixed inset-x-0 top-0 z-50 bg-white/90 backdrop-blur-md shadow-sm">
        <nav class="flex items-center justify-between p-4 lg:px-8" aria-label="Global">
            <div class="flex lg:flex-1">
                <a href="#beranda" class="-m-1.5 p-1.5 flex items-center gap-3">
                   @if($setting && $setting->logo_path)
                       <img src="{{ Storage::url($setting->logo_path) }}" class="h-10 w-10 rounded-full shadow-sm object-cover bg-white" alt="Logo {{ $setting->store_name }}">
                   @else
                       <img src="{{ asset('img/Logo/LogoKantin.png') }}" class="h-10 w-10 rounded-full shadow-sm object-cover" alt="Logo QRasa Default">
                   @endif
                   <span class="text-2xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-orange-500 to-red-600 tracking-tighter drop-shadow-sm" style="font-family: 'Outfit', sans-serif;">{{ $setting ? $setting->store_name : 'Kantin QRasa' }}</span>
                </a>
            </div>

            <div class="hidden lg:flex lg:gap-x-8">
                <a href="#beranda" class="text-sm font-bold leading-6 text-gray-900 hover:text-orange-600 transition">Beranda</a>
                <a href="#tentang-kami" class="text-sm font-bold leading-6 text-gray-900 hover:text-orange-600 transition">Tentang Kami</a>
                <a href="#menu-andalan" class="text-sm font-bold leading-6 text-gray-900 hover:text-orange-600 transition">Menu Andalan</a>
                <a href="#fasilitas" class="text-sm font-bold leading-6 text-gray-900 hover:text-orange-600 transition">Fasilitas</a>
                <a href="#lokasi" class="text-sm font-bold leading-6 text-gray-900 hover:text-orange-600 transition">Lokasi & Jam Buka</a>
            </div>

            <!-- Right Actions -->
            <div class="flex flex-1 justify-end gap-2 sm:gap-3 items-center">
                <a href="#scanner" class="hidden lg:inline-flex items-center justify-center rounded-md bg-orange-100 px-4 py-2.5 text-sm font-semibold text-orange-700 hover:bg-orange-200 transition shrink-0"><i class="fas fa-qrcode mr-2"></i> Pindai Meja</a>
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/pesan') }}" class="inline-flex items-center justify-center rounded-md bg-orange-600 px-4 py-2 sm:px-5 sm:py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-orange-500 transition shrink-0">Dashboard <span aria-hidden="true" class="hidden sm:inline-block ml-2">&rarr;</span></a>
                    @else
                        <a href="{{ route('login') }}" class="inline-flex items-center justify-center rounded-md bg-orange-600 px-4 py-2 sm:px-5 sm:py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-orange-500 transition shrink-0">Log in <span aria-hidden="true" class="hidden sm:inline-block ml-2">&rarr;</span></a>
                    @endauth
                @endif
            </div>
        </nav>
    </header>

    <main>
        <!-- Hero Section -->
        <div id="beranda" class="relative isolate overflow-hidden bg-stone-900 h-screen flex items-center">
            <!-- Hero BG image — fetchpriority=high + explicit dimensions prevents layout shift and improves LCP -->
            <img src="{{ $heroUrlMobile }}"
                 srcset="{{ $heroUrlMobile }} 768w, {{ $heroUrl }} 1920w"
                 sizes="100vw"
                 alt="Cafe Background"
                 width="1920" height="1080"
                 fetchpriority="high"
                 loading="eager"
                 decoding="async"
                 class="absolute inset-0 -z-10 h-full w-full object-cover opacity-40">
            <div class="absolute inset-x-0 -top-40 -z-10 transform-gpu overflow-hidden blur-3xl sm:-top-80" aria-hidden="true"></div>
            <div class="mx-auto max-w-7xl px-6 lg:px-8 relative z-10 w-full">
                <div class="mx-auto max-w-3xl text-center" data-aos="zoom-in">
                    <h1 class="text-3xl font-bold tracking-tight text-white sm:text-5xl lg:text-6xl mb-4 sm:mb-6">{{ $setting ? $setting->welcome_title : 'Nikmati Suasana Nyaman & Hidangan Lezat' }}</h1>
                    <p class="text-base sm:text-lg leading-7 sm:leading-8 text-gray-300">
                        {{ $setting ? $setting->welcome_subtitle : 'Tempat nongkrong terbaik dengan aneka kopi spesial, makanan ringan, dan berat yang siap menemani hari Anda. Pesan langsung dari meja tanpa antre.' }}
                    </p>
                    <div class="mt-10 flex flex-col sm:flex-row items-center justify-center gap-4">
                        <a href="#scanner" class="rounded-full bg-orange-600 px-8 py-3.5 text-base font-semibold text-white shadow-lg hover:bg-orange-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-orange-600 transition-transform hover:scale-105 w-full sm:w-auto">
                            Pesan Sekarang (Scan QR)
                        </a>
                        <a href="#menu-andalan" class="text-base font-semibold leading-6 text-white hover:text-orange-400 transition ml-0 sm:ml-4">Lihat Menu Rekomendasi <span aria-hidden="true">→</span></a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tentang Kami Section -->
        <div id="tentang-kami" class="overflow-hidden bg-white py-24 sm:py-32">
            <div class="mx-auto max-w-7xl px-6 lg:px-8">
                <div class="mx-auto grid max-w-2xl grid-cols-1 gap-x-8 gap-y-16 sm:gap-y-20 lg:mx-0 lg:max-w-none lg:grid-cols-2 lg:items-center">
                    <div class="lg:pr-8 lg:pt-4">
                        <div class="lg:max-w-lg" data-aos="fade-right">
                            <h2 class="text-base font-semibold leading-7 text-orange-600">Kisah Kami</h2>
                            <p class="mt-2 text-2xl font-bold tracking-tight text-stone-900 sm:text-4xl">{{ $setting->about_title ?? 'Lebih Dari Sekadar Tempat Makan' }}</p>
                            <p class="mt-4 sm:mt-6 text-base sm:text-lg leading-7 sm:leading-8 text-stone-600 whitespace-pre-line">{{ $setting->about_text ?? "Berdiri dengan visi untuk menyajikan masakan berkualitas dengan harga sahabat. Kami menggunakan bahan-bahan segar setiap harinya untuk memastikan setiap gigitan dan tegukan memberikan kepuasan tersendiri.\n\nKini kami mengadopsi teknologi digital untuk masa depan pesanan, sehingga pelanggan hanya perlu memindai QR Code di meja, memilih menu, dan pesanan akan langsung diantar oleh pelayan kami yang ramah." }}</p>
                        </div>
                    </div>
                    <img src="{{ $setting && $setting->about_image ? Storage::url($setting->about_image) : 'https://cdn.pixabay.com/photo/2016/08/21/14/49/cafe-1609795_1280.jpg' }}"
                         alt="Tentang QRasa"
                         width="800" height="500"
                         loading="lazy"
                         decoding="async"
                         class="w-full h-auto max-h-[500px] object-cover rounded-2xl shadow-xl ring-1 ring-stone-400/10" data-aos="fade-left">
                </div>
            </div>
        </div>

        <!-- Menu Andalan (Featured Menu) -->
        <div id="menu-andalan" class="bg-stone-50 py-24 sm:py-32">
            <div class="mx-auto max-w-7xl px-6 lg:px-8">
                <div class="mx-auto max-w-2xl text-center" data-aos="fade-up">
                    <h2 class="text-base font-semibold leading-7 text-orange-600">Spesial Hari Ini</h2>
                    <p class="mt-2 text-2xl font-bold tracking-tight text-stone-900 sm:text-4xl">Menu Terfavorit Pelanggan Kami</p>
                </div>
                <div class="mx-auto mt-12 grid max-w-2xl grid-cols-2 gap-x-4 gap-y-6 sm:gap-x-8 sm:gap-y-12 sm:grid-cols-2 lg:mx-0 lg:max-w-none lg:grid-cols-3">

                    @php
                        $menuCount = $setting->featured_menu_count ?? 3;
                        $menus = \App\Models\Menu::inRandomOrder()->take($menuCount)->get();
                    @endphp

                    @forelse($menus as $index => $item)
                        <!-- Item -->
                        <div class="flex flex-col bg-white rounded-2xl sm:rounded-3xl shadow-sm ring-1 ring-stone-200 overflow-hidden transform transition duration-300 hover:scale-105 hover:shadow-xl" data-aos="fade-up" data-aos-delay="{{ ($index + 1) * 100 }}">
                            @if($item->gambar)
                                <img src="{{ Storage::url($item->gambar) }}"
                                     alt="{{ $item->nama }}"
                                     width="400" height="192"
                                     loading="lazy"
                                     decoding="async"
                                     class="h-32 sm:h-48 w-full object-cover">
                            @else
                                <div class="h-32 sm:h-48 w-full bg-stone-100 flex items-center justify-center text-stone-400">
                                    <i class="fas fa-utensils text-3xl sm:text-4xl"></i>
                                </div>
                            @endif
                            <div class="p-3 sm:p-6 flex-1 flex flex-col justify-between">
                                <div>
                                    <h3 class="text-sm sm:text-xl font-bold text-stone-900 line-clamp-2">{{ $item->nama }}</h3>
                                    <p class="mt-1 sm:mt-2 text-xs sm:text-sm text-stone-600 line-clamp-2 sm:line-clamp-3">{{ $item->deskripsi }}</p>
                                </div>
                                <div class="mt-2 sm:mt-4 flex items-center justify-between">
                                    <span class="text-sm sm:text-lg font-bold text-orange-600">Rp {{ number_format($item->harga, 0, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full text-center py-10 text-stone-500">
                            Belum ada menu yang ditambahkan.
                        </div>
                    @endforelse

                </div>
            </div>
        </div>

        <!-- Fasilitas Section -->
        <div id="fasilitas" class="bg-white py-24 sm:py-32">
            <div class="mx-auto max-w-7xl px-6 lg:px-8">
                <div class="mx-auto max-w-2xl lg:text-center" data-aos="fade-up">
                    <h2 class="text-base font-semibold leading-7 text-orange-600">Berbagai Fasilitas Terbaik</h2>
                    <p class="mt-2 text-2xl font-bold tracking-tight text-stone-900 sm:text-4xl">Mengapa Memilih Tempat Kami?</p>
                </div>
                <div class="mx-auto mt-10 sm:mt-16 max-w-2xl lg:mt-24 lg:max-w-4xl">
                    <dl class="grid max-w-xl grid-cols-2 gap-x-4 gap-y-6 sm:gap-x-8 sm:gap-y-10 lg:max-w-none lg:grid-cols-2 lg:gap-y-16">
                        <div class="relative pl-10 sm:pl-16" data-aos="fade-up" data-aos-delay="100">
                            <dt class="text-sm sm:text-base font-bold leading-6 sm:leading-7 text-stone-900">
                                <div class="absolute left-0 top-0 flex h-8 w-8 sm:h-10 sm:w-10 items-center justify-center rounded-lg bg-orange-100 ring-1 ring-orange-200">
                                    <i class="fas fa-wifi text-orange-600 text-sm sm:text-lg"></i>
                                </div>
                                Wi-Fi Kecepatan Tinggi
                            </dt>
                            <dd class="mt-1 sm:mt-2 text-xs sm:text-base leading-5 sm:leading-7 text-stone-600">Koneksi internet stabil secara gratis cocok untuk menemani rapat online atau waktu nugas Anda.</dd>
                        </div>
                        <div class="relative pl-10 sm:pl-16" data-aos="fade-up" data-aos-delay="200">
                            <dt class="text-sm sm:text-base font-bold leading-6 sm:leading-7 text-stone-900">
                                <div class="absolute left-0 top-0 flex h-8 w-8 sm:h-10 sm:w-10 items-center justify-center rounded-lg bg-orange-100 ring-1 ring-orange-200">
                                    <i class="fas fa-couch text-orange-600 text-sm sm:text-lg"></i>
                                </div>
                                Area Nyaman & Area Merokok
                            </dt>
                            <dd class="mt-1 sm:mt-2 text-xs sm:text-base leading-5 sm:leading-7 text-stone-600">Pemisahan area indoor ber-AC penuh dan area outdoor untuk merokok yang sejuk dipayungi daun-daun hijau.</dd>
                        </div>
                        <div class="relative pl-10 sm:pl-16" data-aos="fade-up" data-aos-delay="300">
                            <dt class="text-sm sm:text-base font-bold leading-6 sm:leading-7 text-stone-900">
                                <div class="absolute left-0 top-0 flex h-8 w-8 sm:h-10 sm:w-10 items-center justify-center rounded-lg bg-orange-100 ring-1 ring-orange-200">
                                    <i class="fas fa-music text-orange-600 text-sm sm:text-lg"></i>
                                </div>
                                Live Music Mingguan
                            </dt>
                            <dd class="mt-1 sm:mt-2 text-xs sm:text-base leading-5 sm:leading-7 text-stone-600">Nikmati alunan musik akustik secara langsung setiap malam akhir pekan untuk memeriahkan suasana.</dd>
                        </div>
                        <div class="relative pl-10 sm:pl-16" data-aos="fade-up" data-aos-delay="400">
                            <dt class="text-sm sm:text-base font-bold leading-6 sm:leading-7 text-stone-900">
                                <div class="absolute left-0 top-0 flex h-8 w-8 sm:h-10 sm:w-10 items-center justify-center rounded-lg bg-orange-100 ring-1 ring-orange-200">
                                    <i class="fas fa-motorcycle text-orange-600 text-sm sm:text-lg"></i>
                                </div>
                                Parkir Luas & Aman
                            </dt>
                            <dd class="mt-1 sm:mt-2 text-xs sm:text-base leading-5 sm:leading-7 text-stone-600">Tersedia area parkir yang memadai dengan pengawasan cctv 24 jam dengan jasa petugas parkir ahli.</dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>

        <!-- QR Scanner Interactive Box -->
        <div id="scanner" class="bg-orange-600 py-16 sm:py-24 relative overflow-hidden">
            <div class="absolute inset-0 bg-black/10"></div>
            <div class="mx-auto max-w-7xl px-6 lg:px-8 relative z-10 text-center text-white" data-aos="zoom-in">
                <h2 class="text-2xl font-bold tracking-tight sm:text-4xl">Pesan Makanan Tanpa Antre</h2>
                <p class="mt-4 sm:mt-6 text-base sm:text-lg leading-7 sm:leading-8 text-orange-50 max-w-2xl mx-auto">
                    Arahkan kamera ke meja Anda, atau klik tombol di bawah untuk membuka pemindai bawaan sistem kami.
                </p>
                <div class="mx-auto mt-10 max-w-sm bg-white rounded-3xl p-6 text-stone-900 shadow-xl" data-aos="flip-up" data-aos-delay="200">
                    <div id="reader" class="w-full rounded-lg text-left"></div>
                    <button id="scanBtn" class="mt-4 w-full rounded-full bg-orange-600 px-4 py-3 text-sm font-semibold text-white hover:bg-orange-500 flex items-center justify-center gap-x-2 transition shadow-md">
                        <i class="fas fa-camera text-lg"></i> Buka Kamera Scanner
                    </button>
                    <!-- Loading State / Additional UI if needed -->
                    <p class="text-xs text-stone-500 mt-4">Pastikan Anda memberikan izin kamera untuk memindai.</p>
                </div>
            </div>
        </div>

        <!-- Lokasi & Jam Buka Section -->
        <div id="lokasi" class="bg-stone-50 py-24 sm:py-32">
            <div class="mx-auto max-w-7xl px-6 lg:px-8">
                <div class="mx-auto max-w-2xl text-center mb-16" data-aos="fade-up">
                    <h2 class="text-base font-semibold leading-7 text-orange-600">Kunjungi Kami</h2>
                    <p class="mt-2 text-2xl font-bold tracking-tight text-stone-900 sm:text-4xl">Lokasi & Jam Operasional</p>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-stretch">
                    <!-- Google Maps Iframe (Kiri, Diperbesar) -->
                    <div class="lg:col-span-7 rounded-2xl sm:rounded-3xl overflow-hidden shadow-lg h-64 sm:h-96 lg:h-full min-h-[250px] sm:min-h-[400px] lg:min-h-[500px]" data-aos="fade-right">
                        @if($setting && $setting->map_iframe)
                            <div class="w-full h-full [&>iframe]:w-full [&>iframe]:h-full">
                                {!! $setting->map_iframe !!}
                            </div>
                        @else
                            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d126938.16701831835!2d106.75628509373204!3d-6.155457850550117!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e69f3e945e34b9d%3A0x100c5e82dd4b820!2sJakarta%2CSouth%20Jakarta%20City%2C%20Jakarta!5e0!3m2!1sen!2sid!4v1700632599665!5m2!1sen!2sid" width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                        @endif
                    </div>

                    <!-- Jam Operasional & Kontak (Kanan, Lebih Rapi) -->
                    <div class="lg:col-span-5 bg-white rounded-2xl sm:rounded-3xl shadow-sm ring-1 ring-stone-200 p-4 sm:p-8 flex flex-col justify-center" data-aos="fade-left">
                        <h3 class="text-lg sm:text-xl font-bold text-stone-900 mb-3 sm:mb-4 border-b pb-2 sm:pb-3"><i class="far fa-clock text-orange-600 mr-2"></i> Jam Buka</h3>
                        <ul class="space-y-2 sm:space-y-3 text-stone-700 text-xs sm:text-base">
                            @php
                                $hours = ($setting && is_array($setting->operational_hours)) ? $setting->operational_hours : [
                                    ['day' => 'Senin - Kamis', 'time' => '10:00 - 22:00'],
                                    ['day' => 'Jumat', 'time' => '13:00 - 23:00'],
                                    ['day' => 'Sabtu - Minggu', 'time' => '08:00 - 23:30']
                                ];
                            @endphp
                            @foreach($hours as $index => $hour)
                                @if(!empty($hour['day']))
                                <li class="flex justify-between items-center py-1.5 sm:py-2 border-b border-stone-100 border-dashed {{ $index === count($hours) - 1 ? 'text-orange-600' : '' }}">
                                    <span>{{ $hour['day'] }}</span>
                                    <span class="font-bold">{{ $hour['time'] }}</span>
                                </li>
                                @endif
                            @endforeach
                        </ul>

                        <h3 class="text-lg sm:text-xl font-bold text-stone-900 mb-3 sm:mb-4 border-b pb-2 sm:pb-3 mt-6 sm:mt-8"><i class="fas fa-phone-alt text-orange-600 mr-2"></i> Kontak Reservasi</h3>
                        <div class="space-y-2 sm:space-y-3 text-stone-700 text-xs sm:text-base">
                            <p class="flex items-start gap-2 sm:gap-3"><i class="fab fa-whatsapp text-green-500 text-base sm:text-lg w-4 sm:w-5 mt-0.5"></i> <span>+{{ $setting->contact_whatsapp ?? '62 822 5555 7777' }}</span></p>
                            <p class="flex items-start gap-2 sm:gap-3"><i class="fab fa-instagram text-pink-500 text-base sm:text-lg w-4 sm:w-5 mt-0.5"></i> <span>{{ $setting->contact_instagram ?? '@qrasa.cafe' }}</span></p>
                            <p class="flex items-start gap-2 sm:gap-3"><i class="fas fa-map-marker-alt text-red-500 text-base sm:text-lg w-4 sm:w-5 mt-0.5"></i> <span>{{ $setting->store_address ?? 'Jl. Jend. Sudirman Kav 1, Jakarta Selatan' }}</span></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <footer class="bg-gradient-to-r from-orange-500 to-orange-600 border-t border-orange-400" aria-labelledby="footer-heading">
        <h2 id="footer-heading" class="sr-only">Footer</h2>
        <div class="mx-auto max-w-7xl px-6 pb-8 pt-12 lg:px-8">
            <div class="md:flex md:items-center md:justify-between">
                <div class="flex justify-center md:order-2 space-x-6">
                    <a href="#" class="text-orange-100 hover:text-white transition">
                        <span class="sr-only">Facebook</span>
                        <i class="fab fa-facebook-f text-xl"></i>
                    </a>
                    <a href="#" class="text-orange-100 hover:text-white transition">
                        <span class="sr-only">Instagram</span>
                        <i class="fab fa-instagram text-xl"></i>
                    </a>
                    <a href="#" class="text-orange-100 hover:text-white transition">
                        <span class="sr-only">Tiktok</span>
                        <i class="fab fa-tiktok text-xl"></i>
                    </a>
                </div>
                <div class="mt-8 md:order-1 md:mt-0 flex flex-col items-center md:items-start">
                    <div class="flex items-center gap-2 mb-2">
                        @if($setting && $setting->logo_path)
                            <img src="{{ Storage::url($setting->logo_path) }}" class="h-8 w-8 rounded-full object-cover bg-white" alt="Logo">
                        @else
                            <img src="{{ asset('img/Logo/LogoKantin.png') }}" class="h-8 w-8 rounded-full object-cover bg-white p-1" alt="Logo">
                        @endif
                        <span class="text-lg font-bold text-white">Kantin QRasa</span>
                    </div>
                    <p class="text-center md:text-left text-sm leading-5 text-orange-100">
                        {!! $setting ? $setting->welcome_footer : '&copy; 2026 Kantin QRasa. All rights reserved.' !!}
                    </p>
                </div>
            </div>
        </div>
    </footer>

    <!-- QR Scanner Script -->
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
    <script>
        const cameraButton = document.getElementById('scanBtn');
        const readerDiv = document.getElementById('reader');

        cameraButton.addEventListener('click', () => {
            cameraButton.style.display = 'none';
            readerDiv.style.display = 'block';

            const html5QrCode = new Html5Qrcode("reader");
            Html5Qrcode.getCameras().then(cameras => {
                if (cameras && cameras.length) {
                    const cameraId = cameras[0].id;
                    html5QrCode.start(
                        cameraId,
                        { fps: 10, qrbox: { width: 250, height: 250 } },
                        (decodedText, decodedResult) => {
                            html5QrCode.stop().then(() => {
                                try {
                                    const scannedUrl = new URL(decodedText);
                                    window.location.href = scannedUrl.toString();
                                } catch (e) {
                                    const url = new URL('/menu', window.location.origin);
                                    url.searchParams.append('meja_id', decodedText);
                                    window.location.href = url.toString();
                                }
                            }).catch(err => console.error("Failed to stop QR code scanner.", err));
                        },
                        (errorMessage) => { }
                    ).catch(err => {
                        console.error(`Unable to start scanning, error: ${err}`);
                        alert(`Error: Tidak dapat memulai kamera. Pastikan Anda memberikan izin kamera.`);
                        cameraButton.style.display = 'flex';
                        readerDiv.style.display = 'none';
                    });
                } else {
                    console.error("No cameras found.");
                    alert("Error: Tidak ada kamera yang ditemukan.");
                }
            }).catch(err => {
                console.error(`Camera permission error: ${err}`);
                alert("Error: Izin kamera ditolak. Mohon izinkan akses kamera untuk memindai QR code.");
            });
        });

        // Initially hide the reader
        readerDiv.style.display = 'none';
    </script>

    <!-- Initialize AOS -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            AOS.init({ duration: 800, easing: 'ease-in-out', once: true, offset: 50 });
        });
    </script>

    <!-- WhatsApp Floating Button -->
    @php
        $waLink = $setting && $setting->contact_whatsapp ? 'https://wa.me/' . preg_replace('/[^0-9]/', '', $setting->contact_whatsapp) : 'https://wa.me/6282255557777';
    @endphp
    <a href="{{ $waLink }}" target="_blank" rel="noopener noreferrer" class="fixed bottom-6 right-6 z-50 rounded-full bg-green-500 w-14 h-14 sm:w-16 sm:h-16 text-white shadow-lg hover:bg-green-600 hover:scale-110 transition-transform duration-300 flex items-center justify-center group" aria-label="Reservasi WhatsApp">
        <i class="fab fa-whatsapp text-3xl sm:text-4xl"></i>
    </a>
</body>
</html>
