<nav class="bg-orange-600 border-gray-100 lg:ml-64 shadow-sm relative" style="z-index:30">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">

            <!-- Sidebar Hamburger (Mobile) -->
            <div class="flex items-center lg:hidden">
                <button @click="sidebarOpen = !sidebarOpen"
                    class="inline-flex items-center justify-center p-2 rounded-md text-white hover:bg-orange-700 focus:outline-none transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>

            <!-- Right Side: Bell + Profile -->
            <div class="flex items-center justify-end flex-1 gap-2">

                @auth
                <!-- ─── Bell / Low Stock Notification ─── -->
                <div x-data="{ lowStockOpen: false }" class="relative">
                    <button
                        @click="lowStockOpen = !lowStockOpen"
                        class="relative p-2 rounded-full text-white hover:bg-orange-700 focus:outline-none transition-all duration-200"
                        aria-label="Notifikasi Stok Rendah"
                    >
                        <i class="fas fa-bell text-xl"></i>
                        @if (Auth::user()->role === 'admin' && isset($adminLowStockMenus) && $adminLowStockMenus->count() > 0)
                            <span class="absolute top-1 right-1 bg-white text-red-600 text-xs font-bold rounded-full h-4 w-4 flex items-center justify-center border border-red-100">
                                {{ $adminLowStockMenus->count() }}
                            </span>
                        @elseif (isset($lowStockMenus) && $lowStockMenus->count() > 0)
                            <span class="absolute top-1 right-1 bg-white text-red-600 text-xs font-bold rounded-full h-4 w-4 flex items-center justify-center border border-red-100">
                                {{ $lowStockMenus->count() }}
                            </span>
                        @endif
                    </button>

                    <!-- Bell Dropdown -->
                    <div
                        x-show="lowStockOpen"
                        x-cloak
                        @click.outside="lowStockOpen = false"
                        style="display:none"
                        class="absolute right-0 top-full mt-2 w-80 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 scale-95"
                        x-transition:enter-end="opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-75"
                        x-transition:leave-start="opacity-100 scale-100"
                        x-transition:leave-end="opacity-0 scale-95"
                        style="z-index:9999"
                    >
                        <div class="py-1">
                            <div class="px-4 py-2 text-xs text-orange-600 font-semibold border-b border-gray-100">
                                <i class="fas fa-exclamation-triangle mr-1"></i> Peringatan Stok Rendah
                            </div>
                            @if (Auth::user()->role === 'admin' && isset($adminLowStockMenus))
                                @forelse ($adminLowStockMenus as $menu)
                                    <div class="flex justify-between items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                        <span>{{ $menu->nama }}</span>
                                        <span class="font-medium text-red-600">Stok: {{ $menu->stok }}</span>
                                    </div>
                                @empty
                                    <p class="px-4 py-2 text-sm text-gray-400 italic">Tidak ada menu dengan stok rendah.</p>
                                @endforelse
                            @else
                                @forelse ($lowStockMenus as $menu)
                                    <div class="flex justify-between items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                        <span>{{ $menu->nama }}</span>
                                        <span class="font-medium text-red-600">Stok: {{ $menu->stok }}</span>
                                    </div>
                                @empty
                                    <p class="px-4 py-2 text-sm text-gray-400 italic">Tidak ada menu dengan stok rendah.</p>
                                @endforelse
                            @endif
                        </div>
                    </div>
                </div>

                <!-- ─── Profile Dropdown ─── -->
                <div x-data="{ profileOpen: false }" class="relative ml-3">
                    <button
                        @click="profileOpen = !profileOpen"
                        class="flex items-center gap-2 text-sm text-white hover:text-gray-200 focus:outline-none transition duration-150 ease-in-out"
                        aria-label="Menu Profil"
                    >
                        @php
                            $avatarPath = Auth::user()->avatar;
                            if ($avatarPath && str_starts_with($avatarPath, 'public/')) {
                                $avatarPath = substr($avatarPath, 7);
                            }
                        @endphp
                        @if (Auth::user()->avatar)
                            <img class="h-8 w-8 rounded-full object-cover ring-2 ring-white/50"
                                 src="{{ Storage::url($avatarPath) }}" alt="{{ Auth::user()->name }}">
                        @else
                            <img class="h-8 w-8 rounded-full object-cover ring-2 ring-white/50"
                                 src="{{ asset('img/default-avatar.png') }}" alt="{{ Auth::user()->name }}">
                        @endif
                        <svg class="fill-current h-4 w-4 transition-transform duration-200"
                             :class="profileOpen ? 'rotate-180' : ''"
                             xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>

                    <!-- Profile Dropdown Menu -->
                    <div
                        x-show="profileOpen"
                        x-cloak
                        @click.outside="profileOpen = false"
                        style="display:none; z-index:9999"
                        class="absolute right-0 top-full mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 scale-95"
                        x-transition:enter-end="opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-75"
                        x-transition:leave-start="opacity-100 scale-100"
                        x-transition:leave-end="opacity-0 scale-95"
                    >
                        <div class="py-1 rounded-md">
                            <!-- Profile info -->
                            <div class="px-4 py-2 border-b border-gray-100">
                                <p class="text-sm font-semibold text-gray-800 truncate">{{ Auth::user()->name }}</p>
                                <p class="text-xs text-gray-500 truncate">{{ Auth::user()->email }}</p>
                            </div>
                            <a href="{{ route('profile.edit') }}"
                               class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-orange-50 hover:text-orange-600 transition-colors"
                               @click="profileOpen = false">
                                <i class="fas fa-user-circle w-4"></i> Profile
                            </a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                    class="flex w-full items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-red-50 hover:text-red-600 transition-colors">
                                    <i class="fas fa-sign-out-alt w-4"></i> Log Out
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                @endauth

            </div>
        </div>
    </div>
</nav>

