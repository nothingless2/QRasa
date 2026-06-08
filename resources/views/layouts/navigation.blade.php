<nav x-data="{ open: false }" class="bg-orange-600 border-gray-100">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-end h-16">

            <!-- Low Stock Notification -->
            @auth
            <div x-data="{ lowStockOpen: false }" x-init="lowStockOpen = false" class="relative hidden sm:flex sm:items-center mr-4">
                <button @click="lowStockOpen = ! lowStockOpen" class="relative p-2 rounded-full text-putih hover:bg-orange-600/80 focus:ring-putih transition-all duration-200">
                    <i class="fas fa-bell text-xl"></i>
                    @if (Auth::user()->role === 'admin' && isset($adminLowStockMenus) && $adminLowStockMenus->count() > 0)
                        <span class="absolute top-2 right-1 transform translate-x-1/2 -translate-y-1/2 bg-white text-red-600 shadow-md border border-red-100 text-xs font-bold rounded-full h-5 w-5 flex items-center justify-center">
                            {{ $adminLowStockMenus->count() }}
                        </span>
                    @elseif (isset($lowStockMenus) && $lowStockMenus->count() > 0)
                        <span class="absolute top-2 right-1 transform translate-x-1/2 -translate-y-1/2 bg-white text-red-600 shadow-md border border-red-100 text-xs font-bold rounded-full h-5 w-5 flex items-center justify-center">
                            {{ $lowStockMenus->count() }}
                        </span>
                    @endif
                </button>

                <!-- Low Stock Dropdown Content -->
                <div x-show="lowStockOpen" x-cloak @click.away="lowStockOpen = false" class="absolute z-50 mt-44 w-80 rounded-md shadow-lg  right-0 bg-white ring-1 ring-black ring-opacity-5 focus:outline-none" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95">
                    <div class="py-1">
                        <div class="block px-4 py-2 text-xs text-gray-700 font-semibold border-b border-gray-100">
                            Peringatan Stok Rendah
                        </div>
                        @if (Auth::user()->role === 'admin' && isset($adminLowStockMenus))
                            @forelse ($adminLowStockMenus as $menu)
                                <div class="flex justify-between items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors duration-150">
                                    <span>{{ $menu->nama }}</span>
                                    <span class="font-medium text-red-600">Stok: {{ $menu->stok }}</span>
                                </div>
                            @empty
                                <div class="block px-4 py-2 text-sm text-gray-700">
                                    Tidak ada menu dengan stok rendah.
                                </div>
                            @endforelse
                        @else
                            @forelse ($lowStockMenus as $menu)
                                <div class="flex justify-between items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors duration-150">
                                    <span>{{ $menu->nama }}</span>
                                    <span class="font-medium text-red-600">Stok: {{ $menu->stok }}</span>
                                </div>
                            @empty
                                <div class="block px-4 py-2 text-sm text-gray-700">
                                    Tidak ada menu dengan stok rendah.
                                </div>
                            @endforelse
                        @endif
                    </div>
                </div>
            </div>
            @endauth

            <!-- Settings Dropdown -->
            @auth
            <div class="hidden sm:flex sm:items-center sm:ml-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="flex items-center text-sm font-medium text-white hover:text-gray-200 focus:outline-none transition duration-150 ease-in-out">
                            @php
                                $avatarPath = Auth::user()->avatar;
                                if (str_starts_with($avatarPath, 'public/')) {
                                    $avatarPath = substr($avatarPath, 7);
                                }
                            @endphp
                            @if (Auth::user()->avatar)
                                <img class="h-8 w-8 rounded-full object-cover" src="{{ Storage::url($avatarPath) }}" alt="{{ Auth::user()->name }}">
                            @else
                                <img class="h-8 w-8 rounded-full object-cover" src="{{ asset('img/default-avatar.png') }}" alt="{{ Auth::user()->name }}">
                            @endif
                            <div class="ml-3">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>
            @endauth

            <!-- Hamburger -->
            <div class="-mr-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-600 hover:text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-700 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" x-cloak class="hidden sm:hidden">
        @auth
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('menu.index')" :active="request()->routeIs('menu.index')">
                {{ __('Menu') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('pesan.index')" :active="request()->routeIs('pesan.index')">
                {{ __('Pesan') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('meja.index')" :active="request()->routeIs('meja.index')">
                {{ __('Meja') }}
            </x-responsive-nav-link>

            @if (Auth::user()->role == 'admin')
            <x-responsive-nav-link :href="route('user.index')" :active="request()->routeIs('user.index')">
                {{ __('User') }}
            </x-responsive-nav-link>
            @endif
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-300">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-700">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                        onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
        @endauth
    </div>
</nav>
