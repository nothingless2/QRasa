@props(['title' => 'Menu Management'])

<!-- Sidebar -->
<div {{ $attributes->merge(['class' => 'w-64 bg-white shadow-lg fixed left-0 top-0 bottom-0 overflow-y-auto flex flex-col']) }}>
    <!-- Sidebar Header dengan Logo -->
    <div class="p-6 flex items-center space-x-4">
        <div>
            <h1 class="text-2xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-orange-500 to-red-600 tracking-tighter drop-shadow-sm">QRasa</h1>
            <p class="text-sm text-gray-600">Panel Admin</p>
        </div>
    </div>

    @auth
    <!-- Navigation -->
    <nav class="px-4 flex-1">
        <div class="space-y-4">
            <p class="text-gray-700 text-md font-bold">Manajemen Kelola</p>
            <!-- Dashboard Link -->
            @if (Auth::check() && Auth::user()->role !== 'admin')
            <div>
                <a href="{{ route('pesan.index') }}"
                    class="flex items-center px-4 py-2 text-sm font-medium rounded-lg {{ request()->routeIs('pesan.*') ? 'text-white bg-oren' : 'text-gray-600 hover:bg-gray-100' }} transition-colors duration-150">
                    <i class="fas fa-shopping-cart w-5 h-5 mr-3"></i>
                    <span>Pesanan</span>
                </a>
            </div>
            @if (Auth::user()->role === 'cashier')
            <div>
                <a href="{{ route('pos.index') }}"
                    class="flex items-center px-4 py-2 text-sm font-medium rounded-lg {{ request()->routeIs('pos.*') ? 'text-white bg-oren' : 'text-gray-600 hover:bg-gray-100' }} transition-colors duration-150">
                    <i class="fas fa-cash-register w-5 h-5 mr-3"></i>
                    <span>Kasir (POS)</span>
                </a>
            </div>
            @endif
            @if (Auth::user()->role === 'chef')
            <div>
                <a href="{{ route('kds.index') }}"
                    class="flex items-center px-4 py-2 text-sm font-medium rounded-lg {{ request()->routeIs('kds.*') ? 'text-white bg-oren' : 'text-gray-600 hover:bg-gray-100' }} transition-colors duration-150">
                    <i class="fas fa-fire-burner w-5 h-5 mr-3"></i>
                    <span>Layar Dapur (KDS)</span>
                </a>
            </div>
            @endif
            @endif

            <!-- Meja Management (Admin only) -->

            @if (Auth::check() && Auth::user()->role === 'admin')
             <!-- Menu Management -->
            <div>
                <a href="{{ route('menu.index') }}"
                    class="flex items-center px-4 py-2 text-sm font-medium rounded-lg {{ request()->routeIs('menu.*') ? 'text-white bg-oren' : 'text-gray-600 hover:bg-gray-100' }} transition-colors duration-150">
                    <i class="fas fa-utensils w-5 h-5 mr-3"></i>
                    <span>Menu</span>
                </a>
            </div>

            <!-- Rekap Shift Kasir -->
            <div>
                <a href="{{ route('shifts.index') }}"
                    class="flex items-center px-4 py-2 text-sm font-medium rounded-lg {{ request()->routeIs('shifts.*') ? 'text-white bg-oren' : 'text-gray-600 hover:bg-gray-100' }} transition-colors duration-150">
                    <i class="fas fa-cash-register w-5 h-5 mr-3"></i>
                    <span>Rekap Shift Kasir</span>
                </a>
            </div>
            <div>
                <a href="{{ route('meja.index') }}"
                    class="flex items-center px-4 py-2 text-sm font-medium rounded-lg {{ request()->routeIs('meja.*') ? 'text-white bg-oren' : 'text-gray-600 hover:bg-gray-100' }} transition-colors duration-150">
                    <i class="fas fa-chair w-5 h-5 mr-3"></i>
                    <span>Meja</span>
                </a>
            </div>
            <div>
                <a href="{{ route('dashboard') }}"
                    class="flex items-center px-4 py-2 text-sm font-medium rounded-lg {{ request()->routeIs('dashboard') ? 'text-white bg-oren' : 'text-gray-600 hover:bg-gray-100' }} transition-colors duration-150">
                    <i class="fas fa-tachometer-alt w-5 h-5 mr-3"></i>
                    <span>Penjualan</span>
                </a>
            </div>
            <div>
                <a href="{{ route('pesan.index') }}"
                    class="flex items-center px-4 py-2 text-sm font-medium rounded-lg {{ request()->routeIs('pesan.*') ? 'text-white bg-oren' : 'text-gray-600 hover:bg-gray-100' }} transition-colors duration-150">
                    <i class="fas fa-shopping-cart w-5 h-5 mr-3"></i>
                    <span>Pesanan</span>
                </a>
            </div>
            {{-- <div>
                <a href="{{ route('pos.index') }}"
                    class="flex items-center px-4 py-2 text-sm font-medium rounded-lg {{ request()->routeIs('pos.*') ? 'text-white bg-oren' : 'text-gray-600 hover:bg-gray-100' }} transition-colors duration-150">
                    <i class="fas fa-cash-register w-5 h-5 mr-3"></i>
                    <span>Kasir (POS)</span>
                </a>
            </div>
            <div>
                <a href="{{ route('kds.index') }}"
                    class="flex items-center px-4 py-2 text-sm font-medium rounded-lg {{ request()->routeIs('kds.*') ? 'text-white bg-oren' : 'text-gray-600 hover:bg-gray-100' }} transition-colors duration-150">
                    <i class="fas fa-fire-burner w-5 h-5 mr-3"></i>
                    <span>Layar Dapur (KDS)</span>
                </a>
            </div> --}}
             <!-- User Management -->
            <div>
                <a href="{{ route('user.index') }}"
                    class="flex items-center px-4 py-2 text-sm font-medium rounded-lg {{ request()->routeIs('user.*') ? 'text-white bg-oren' : 'text-gray-600 hover:bg-gray-100' }} transition-colors duration-150">
                    <i class="fas fa-users w-5 h-5 mr-3"></i>
                    <span>Pengguna</span>
                </a>
            </div>
             <!-- Settings Management -->
            <div>
                <a href="{{ route('settings.index') }}"
                    class="flex items-center px-4 py-2 text-sm font-medium rounded-lg {{ request()->routeIs('settings.*') ? 'text-white bg-oren shadow-sm' : 'text-gray-600 hover:bg-gray-100' }} transition-colors duration-150">
                    <i class="fas fa-tools w-5 h-5 mr-3"></i>
                    <span>Pengaturan Toko</span>
                </a>
            </div>
            @endif
        </div>
    </nav>
    @endauth
    <!-- Logo Image-->
    <div class="flex items-center justify-center mt-auto mb-4">
        @php $setting = \App\Models\Setting::first(); @endphp
        @if($setting && $setting->logo_path)
            <img src="{{ Storage::url($setting->logo_path) }}" alt="Logo Toko" class="w-20 h-20 rounded-xl object-contain bg-white shadow-sm p-1" loading="lazy">
        @else
            <img src="{{ asset('img/Logo/LogoKantin.png') }}" alt="Logo QRasa Default" class="w-20 h-20 rounded-xl object-cover shadow-sm" loading="lazy">
        @endif
    </div>
    <!-- Copyright -->
    <div class="p-4 border-t border-gray-300">

        <p class="text-xs text-gray-700 text-center">
            &copy; {{ date('Y') }} QRasa
            <br>
            <span class="text-gray-600">Version 1.0.0</span>
        </p>
    </div>
</div>
