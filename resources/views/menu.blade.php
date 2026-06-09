<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Menu - {{ $selectedKategori ?? 'Semua Kategori' }}</title>
    <meta name="description" content="Menu makanan online">
    @vite(['resources/css/app.css']) 

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="icon" type="image/png" href="{{ asset('img/Logo/LogoKantin.png') }}" loading="lazy">

    <!-- Font Awesome & Google Fonts -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@800&display=swap" rel="stylesheet">

    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body class="bg-gray-50">
    @php $setting = \App\Models\Setting::first(); @endphp
    <div x-data="app()" x-init="init()" class="min-h-screen flex flex-col relative z-10">
        <!-- Meja Input Modal -->
        <div x-show="showMejaInput" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center"
            x-cloak x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100">
            <div class="bg-white p-8 rounded-lg shadow-2xl max-w-sm w-full mx-4">
                <h2 class="text-2xl font-bold text-gray-800 mb-6 text-center">Masukkan Nomor Meja Anda</h2>
                <form @submit.prevent="submitMejaNumber">
                    <div class="mb-4">
                        <label for="meja_number" class="block text-gray-700 text-base font-semibold mb-2">Nomor
                            Meja:</label>
                        <input type="number" id="meja_number" x-model.number="mejaNumberInput"
                            class="w-full p-3 border border-gray-400 rounded-lg focus:ring-2 focus:ring-orange-600 focus:border-orange-600 transition-all duration-200 text-lg text-center"
                            placeholder="Contoh: 4" required min="1">
                    </div>
                    <div class="flex items-center justify-center">
                        <button type="submit"
                            class="w-full bg-gradient-to-r from-orange-400 to-orange-500 hover:from-orange-500 hover:to-orange-600 text-white font-bold py-3 px-6 rounded-full shadow-lg shadow-orange-500/30 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-opacity-50 transition-all duration-300 mt-4">
                            Lanjutkan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Header -->
        <header class="sticky top-0 z-40 bg-white shadow-md border-b border-gray-100">
            <div class="container mx-auto px-4">
                <div class="flex items-center justify-between h-16">
                    <div class="flex items-center">
                        @if($setting && $setting->logo_path)
                            <img src="{{ Storage::url($setting->logo_path) }}" alt="Logo {{ $setting->store_name }}"
                                class="w-10 h-10 rounded-full mr-3 object-contain bg-white shadow-sm" loading="lazy">
                        @else
                            <img src="{{ asset('img/Logo/LogoKantin.png') }}" alt="Logo QRasa"
                                class="w-10 h-10 rounded-full mr-3 object-cover shadow-sm" loading="lazy">
                        @endif
                        <h1 class="text-2xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-orange-500 to-red-600 tracking-tighter drop-shadow-sm" style="font-family: 'Outfit', sans-serif;">
                            {{ $setting ? $setting->store_name : 'QRasa' }}
                        </h1>
                    </div>
                    <div class="flex flex-col items-center">
                        <h1 class="text-lg md:text-xl font-bold text-gray-800 truncate max-w-[200px] md:max-w-none"
                            x-text="selectedKategori ? selectedKategori : 'Semua Kategori'">
                        </h1>

                        <div x-show="mejaId" class="bg-orange-50 rounded-full px-3 py-0.5 mt-1 border border-orange-200 shadow-sm">
                            <p class="text-orange-700 text-xs font-medium tracking-wide">Meja: <span x-text="mejaId" class="font-bold"></span></p>
                        </div>


                    </div>
                    <div class="flex items-center space-x-2">
                        <button @click="historyOpen = true"
                            class="relative p-2 hover:bg-gray-100 rounded-full transition-colors text-gray-600 hover:text-orange-500">
                            <i class="fas fa-history text-xl"></i>
                        </button>
                        <button @click="cartOpen = true"
                            class="relative p-2 hover:bg-gray-100 rounded-full transition-colors text-gray-600 hover:text-orange-500">
                            <i class="fas fa-shopping-cart text-xl"></i>
                            <span x-show="getTotalItems() > 0" x-text="getTotalItems()"
                                class="absolute -top-1 -right-0 bg-red-500 text-white shadow-sm border border-white text-[10px] font-bold rounded-full h-5 w-5 flex items-center justify-center">
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </header>

        <!-- Navigation Bar -->
        <nav class="bg-white shadow-lg sticky top-16 z-30">
            <div class="container mx-auto">
                <div class="overflow-x-auto scrollbar-hide">
                    <div class="flex space-x-2 p-4 whitespace-nowrap min-w-full">
                        <a href="#" @click.prevent="filterByKategori(null)"
                            :class="{
                                'bg-gradient-to-r from-orange-400 to-orange-500 text-white shadow-md shadow-orange-500/30': selectedKategori === null,
                                'bg-gray-100 text-gray-700 hover:bg-gray-200': selectedKategori !== null
                            }"
                            class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium transition-all duration-200 transform hover:scale-105">
                            <i class="fas fa-utensils mr-2"></i>
                            <span>Semua Kategori</span>
                        </a>
                        @foreach ($kategori as $kat)
                            <a href="#" @click.prevent="filterByKategori('{{ $kat }}')"
                                :class="{
                                    'bg-gradient-to-r from-orange-400 to-orange-500 text-white shadow-md shadow-orange-500/30': selectedKategori === '{{ $kat }}',
                                    'bg-gray-100 text-gray-700 hover:bg-gray-200': selectedKategori !== '{{ $kat }}'
                                }"
                                class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium transition-all duration-200 transform hover:scale-105">
                                <span>{{ $kat }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </nav>

        <div class="flex-1">
            <!-- Main Content -->
            <main class="flex-1 min-w-0 p-4 ">
                <!-- Search -->
                <div class="container mx-auto px-4 my-8">
                    <div class="max-w-2xl mx-auto relative">
                        <input type="text" x-model="searchQuery" placeholder="Cari menu favoritmu..."
                            class="w-full p-4 pl-12 text-base border-2 border-gray-300 rounded-full focus:ring-2 focus:ring-orange-600 focus:border-orange-600 transition-all duration-200 shadow-sm">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-4">
                            <i class="fas fa-search text-gray-600 text-lg"></i>
                        </div>
                        <div x-show="searchQuery" @click="searchQuery = ''"
                            class="absolute right-4 top-1/2 -translate-y-1/2 bg-gray-100 hover:bg-gray-200 text-gray-600 hover:text-gray-800 rounded-full p-2 transition-all duration-200">
                            <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Menu Grid per Kategori -->
                <div x-show="filteredMenuItems().length > 0" class="mb-6">
                    <template x-for="group in groupedMenuItems()" :key="group.category">
                        <div class="mb-10">
                            <!-- Category Headline -->
                            <div class="flex items-center mb-6">
                                <h2 class="text-2xl font-bold text-gray-800" x-text="group.category"></h2>
                                <div class="ml-4 flex-grow border-t-2 border-gray-100"></div>
                            </div>
                            
                            <!-- Internal List Layout -->
                            <div class="flex flex-col bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden divide-y divide-gray-100">
                                <template x-for="item in group.items" :key="item.id">
                                    <div @click="if(item.stok > 0) addToCart(item)"
                                        class="p-3 sm:p-4 flex flex-row items-center cursor-pointer hover:bg-gray-50 transition-colors duration-200"
                                        :class="{ 'opacity-60 bg-gray-50': item.stok <= 0 }">
                                        <!-- Image Section -->
                                        <div class="relative w-20 h-20 sm:w-28 sm:h-28 flex-shrink-0 bg-white rounded-xl overflow-hidden shadow-sm border border-gray-100">
                                            <img :src="item.image" :alt="item.name" class="w-full h-full object-cover p-0.5 rounded-xl"
                                                loading="lazy">
                                            <div x-show="item.discount > 0"
                                                class="absolute top-1 left-1 bg-red-500 text-white text-[10px] font-bold px-1.5 py-0.5 rounded shadow-sm">
                                                -<span x-text="item.discount + '%'"></span>
                                            </div>
                                            <div x-show="item.stok <= 0"
                                                class="absolute inset-0 bg-white/60 backdrop-blur-[1px]">
                                            </div>
                                        </div>

                                        <!-- Content Section -->
                                        <div class="flex-1 ml-4 flex flex-col h-full justify-center">
                                            <h3 class="font-bold text-gray-800 text-sm sm:text-lg leading-tight mb-1 line-clamp-2"
                                                x-text="item.name">
                                            </h3>
                                            <p class="text-xs sm:text-sm text-gray-700 mb-2 line-clamp-2" x-text="item.description || ''"></p>

                                            <div class="flex items-center space-x-2 mt-auto">
                                                <p class="text-sm sm:text-base font-extrabold text-orange-600"
                                                    x-text="formatPrice(item.price - (item.price * item.discount / 100))">
                                                </p>
                                                <template x-if="item.discount > 0">
                                                    <p class="text-[10px] sm:text-xs text-gray-600 line-through" x-text="formatPrice(item.price)"></p>
                                                </template>
                                            </div>
                                        </div>

                                        <!-- Action Section -->
                                        <div class="ml-3 flex flex-col justify-center items-end">
                                            <template x-if="item.stok > 0">
                                                <button @click.stop="addToCart(item)"
                                                    class="w-8 h-8 sm:w-auto sm:px-5 sm:py-2 bg-gradient-to-r from-orange-400 to-orange-500 text-white font-bold rounded-xl hover:from-orange-500 hover:to-orange-600 transition-all shadow-md shadow-orange-500/30 flex items-center justify-center space-x-1 border border-orange-200">
                                                    <i class="fas fa-plus text-xs sm:text-sm"></i>
                                                    <span class="hidden sm:inline text-sm">Tambah</span>
                                                </button>
                                            </template>
                                            <template x-if="item.stok <= 0">
                                                <span class="text-gray-700 font-bold text-sm">Stok habis</span>
                                            </template>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                     </template>
                </div>

                 <!-- No Results Message -->
                <div x-show="filteredMenuItems().length === 0" class="text-center py-16">
                    <i class="fas fa-search text-4xl text-gray-300 mb-4"></i>
                    <h3 class="text-xl font-semibold text-gray-700">Menu Tidak Ditemukan</h3>
                    <p class="text-gray-700 mt-2">Coba kata kunci atau kategori lain.</p>
                </div>
            </main>

        <!-- Cart -->
        <div x-show="cartOpen"
            class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-end md:items-center justify-center"
            @click.self="cartOpen = false" x-cloak>
            <div class="bg-white w-full md:max-w-md p-6 rounded-t-lg md:rounded-lg max-h-full overflow-y-auto">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-semibold">Keranjang</h2>
                    <button @click="cartOpen = false" class="text-gray-700 hover:text-gray-700">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <template x-if="cart.length > 0">
                    <div class="space-y-4">
                        <template x-for="item in cart" :key="item.id">
                            <div class="flex justify-between items-center border-b pb-2">
                                <div>
                                    <h4 class="font-semibold" x-text="item.name"></h4>
                                    <div class="text-sm text-gray-700"
                                        x-text="formatPrice(item.price - (item.price * item.discount / 100)) + ' x ' + item.quantity">
                                    </div>
                                    <input type="text" x-model="item.notes" :placeholder="(item.category || '').toLowerCase().includes('minuman') ? 'Catatan (opsi): less sugar, no ice...' : 'Catatan (opsi): pedas, tanpa bawang...'" 
                                        class="w-full text-xs mt-1 p-1 border border-gray-300 rounded focus:border-orange-400 focus:ring-0">
                                </div>
                                <div class="flex items-center space-x-2">
                                    <input type="number" min="1" class="w-12 text-center border rounded"
                                        x-model.number="item.quantity"
                                        @input="updateQuantity(item.id, item.quantity)">
                                    <button @click="removeFromCart(item.id)" class="text-red-500 hover:text-red-700">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </template>
                        <div class="pt-4 space-y-2 text-sm text-gray-600">
                            <div class="flex justify-between items-center">
                                <span>Subtotal Netto:</span>
                                <span class="font-semibold" x-text="formatPrice(calculateNetSubtotal())"></span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span>Servis ({{ $setting ? $setting->service_percent : 5 }}%):</span>
                                <span class="font-semibold" x-text="formatPrice(calculateServiceCharge())"></span>
                            </div>
                            <div class="flex justify-between items-center pb-2 border-b border-gray-300">
                                <span>PB1 ({{ $setting ? $setting->tax_percent : 11 }}%):</span>
                                <span class="font-semibold" x-text="formatPrice(calculatePajakPb1())"></span>
                            </div>
                            <div class="flex justify-between items-center text-lg font-bold text-gray-800">
                                <span>Total Tagihan:</span>
                                <span class="text-orange-600" x-text="formatPrice(calculateGrandTotal())"></span>
                            </div>
                        </div>
                        <div class="pt-4">
                            <select x-model="paymentMethod"
                                class="block w-full p-3 border border-gray-400 rounded-lg shadow-sm focus:ring-orange-600 focus:border-orange-600 appearance-none mb-2">
                                <option value="">Pilih Metode Pembayaran</option>
                                <option value="tunai">Tunai</option>
                                <option value="transfer">Transfer Bank</option>
                                <option value="qris">QRIS</option>
                                <option value="gopay">GoPay</option>
                                <option value="ovo">OVO</option>
                                <option value="dana">DANA</option>
                                <option value="shopeepay">ShopeePay</option>
                            </select>
                            <button @click="checkout" x-bind:disabled="!paymentMethod || cart.length === 0"
                                class="w-full bg-gradient-to-r from-orange-400 to-orange-500 text-white font-bold py-3 px-4 rounded-xl shadow-lg shadow-orange-500/30 hover:from-orange-500 hover:to-orange-600 transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed mt-2">
                                Checkout
                            </button>
                        </div>
                    </div>
                </template>
                <template x-if="cart.length === 0">
                    <p class="text-gray-700 text-center">Keranjang kosong</p>
                </template>
            </div>
        </div>

        <!-- History Modal -->
        <div x-show="historyOpen"
            class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-end md:items-center justify-center"
            @click.self="historyOpen = false" x-cloak>
            <div class="bg-white w-full md:max-w-md p-6 rounded-t-lg md:rounded-lg max-h-full overflow-y-auto">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-semibold">Riwayat Pesanan</h2>
                    <button @click="historyOpen = false" class="text-gray-700 hover:text-gray-700">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <template x-if="historyPesanan.length > 0">
                    <div class="space-y-4">
                        <template x-for="pesanan in historyPesanan" :key="pesanan.id">
                            <div class="border-b pb-4">
                                <div class="flex justify-between items-center mb-2">
                                    <p class="font-semibold">Pesanan ID: <span x-text="pesanan.id"></span></p>
                                    <p class="text-sm text-gray-700" x-text="new Date(pesanan.created_at).toLocaleString('id-ID')"></p>
                                </div>
                                <template x-for="menu in pesanan.menus" :key="menu.id">
                                    <div class="flex items-center justify-between text-sm">
                                        <p x-text="menu.nama + ' (x' + menu.pivot.quantity + ')'"></p>
                                        <p x-text="formatPrice(menu.pivot.quantity * menu.harga)"></p>
                                    </div>
                                </template>
                                <div class="text-right font-bold mt-2">
                                    Total: <span x-text="formatPrice(pesanan.total)"></span>
                                </div>
                            </div>
                        </template>
                    </div>
                </template>
                <template x-if="historyPesanan.length === 0">
                    <p class="text-gray-700 text-center">Tidak ada riwayat pesanan untuk meja ini.</p>
                </template>
            </div>
        </div>
    </div>

    <footer>
        <div class="bg-orange-600 text-putih py-4">
            <div class="container mx-auto text-center">
                <p class="text-sm font-semibold">{!! $setting ? $setting->welcome_footer : '&copy; ' . date('Y') . ' QRasa. All rights reserved.' !!}</p>
            </div>
        </div>
    </footer>

    <script>
        function app() {
            return {
                cartOpen: false,
                historyOpen: false,
                menuItems: @json($formattedMenu),
                cart: JSON.parse(localStorage.getItem('QrasaCart') || '[]'),
                searchQuery: '',
                mejaId: null,
                mejaNumberInput: '',
                showMejaInput: false,
                selectedKategori: @json($selectedKategori ?? null),
                historyPesanan: @json($historyPesanan),
                init() {
                    const urlParams = new URLSearchParams(window.location.search);
                    this.mejaId = urlParams.get('meja_id');

                    if (!this.mejaId) {
                        this.showMejaInput = true;
                    }

                    // Auto-sync cart to localStorage whenever it changes
                    this.$watch('cart', value => {
                        localStorage.setItem('QrasaCart', JSON.stringify(value));
                    });
                },
                submitMejaNumber() {
                    if (this.mejaNumberInput && this.mejaNumberInput > 0) {
                        this.mejaId = this.mejaNumberInput;
                        this.showMejaInput = false;
                        const url = new URL(window.location.href);
                        url.searchParams.set('meja_id', this.mejaId);
                        window.history.pushState({}, '', url);
                    } else {
                        Swal.fire({ icon: 'error', title: 'Oops...', text: 'Mohon masukkan nomor meja yang valid.' });
                    }
                },
                filterByKategori(kategori) {
                    this.selectedKategori = kategori;
                    // Update URL without reloading (for bookmarking/sharing)
                    const url = new URL(window.location.href);
                    if (kategori) {
                        url.searchParams.set('kategori', kategori);
                    } else {
                        url.searchParams.delete('kategori');
                    }
                    if (this.mejaId) {
                        url.searchParams.set('meja_id', this.mejaId);
                    }
                    window.history.pushState({}, '', url.toString());
                },
                addToCart(item) {
                    if (item.stok <= 0) {
                        Swal.fire({ icon: 'error', title: 'Habis', text: 'Stok untuk menu ini sudah habis.' });
                        return;
                    }
                    if (!this.mejaId) {
                        Swal.fire({ icon: 'warning', title: 'Perhatian', text: 'Mohon masukkan nomor meja terlebih dahulu.' });
                        this.showMejaInput = true;
                        return;
                    }
                    const existing = this.cart.find(i => i.id === item.id);
                    if (existing) {
                        if (existing.quantity >= item.stok) {
                            Swal.fire({ icon: 'warning', title: 'Limit Stok', text: 'Anda tidak dapat menambahkan melebihi jumlah stok yang tersedia.' });
                            return;
                        }
                        existing.quantity++;
                    } else {
                        this.cart.push({ ...item, quantity: 1, notes: '' });
                    }
                },
                removeFromCart(id) {
                    this.cart = this.cart.filter(i => i.id !== id);
                },
                updateQuantity(id, quantity) {
                    const cartItem = this.cart.find(i => i.id === id);
                    const originItem = this.menuItems.find(i => i.id === id);
                    
                    if (cartItem && originItem) {
                        if (quantity < 1) return this.removeFromCart(id);
                        
                        if (quantity > originItem.stok) {
                            Swal.fire({ icon: 'warning', title: 'Limit Stok', text: 'Jumlah pesanan tidak boleh melebihi stok yang tersedia.' });
                            cartItem.quantity = originItem.stok;
                            return;
                        }
                        
                        cartItem.quantity = quantity;
                    }
                },
                calculateNetSubtotal() {
                    return this.cart.reduce((t, i) => t + (i.price - (i.price * (i.discount || 0) / 100)) * i.quantity, 0);
                },
                calculateServiceCharge() {
                    return Math.round(this.calculateNetSubtotal() * {{ ($setting ? $setting->service_percent : 5) / 100 }});
                },
                calculatePajakPb1() {
                    return Math.round((this.calculateNetSubtotal() + this.calculateServiceCharge()) * {{ ($setting ? $setting->tax_percent : 11) / 100 }});
                },
                calculateGrandTotal() {
                    return this.calculateNetSubtotal() + this.calculateServiceCharge() + this.calculatePajakPb1();
                },
                formatPrice(price) {
                    return 'Rp ' + price.toLocaleString('id-ID');
                },
                getTotalItems() {
                    return this.cart.reduce((t, i) => t + i.quantity, 0);
                },
                paymentMethod: '',
                checkout() {
                    if (this.cart.length === 0 || !this.paymentMethod) {
                        Swal.fire({ icon: 'warning', title: 'Perhatian', text: 'Keranjang kosong atau metode pembayaran belum dipilih!' });
                        return;
                    }
                    if (!this.mejaId) {
                        Swal.fire({ icon: 'warning', title: 'Perhatian', text: 'Mohon masukkan nomor meja terlebih dahulu sebelum checkout.' });
                        this.showMejaInput = true;
                        return;
                    }

                    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
                    const postData = {
                        cartItems: this.cart.map(item => ({ id: item.id, quantity: item.quantity, notes: item.notes || null })),
                        total: this.calculateGrandTotal(),
                        payment_method: this.paymentMethod,
                        meja_id: this.mejaId,
                    };

                    fetch('{{ route('pesan.store') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify(postData)
                    })
                    .then(response => {
                        if (!response.ok) {
                            return response.json().then(err => { throw new Error(err.message || 'Server error'); });
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            Swal.fire({ 
                                icon: 'success', 
                                title: 'Berhasil!', 
                                text: data.message || 'Pesanan berhasil dibuat!' 
                            }).then(() => {
                                this.cart = [];
                                this.cartOpen = false;
                                if (data.redirect) {
                                    window.location.href = data.redirect;
                                }
                            });
                        } else {
                            Swal.fire({ icon: 'error', title: 'Checkout Gagal', text: data.message || 'Silakan coba lagi.' });
                        }
                    })
                    .catch(error => {
                        console.error('Error saat checkout:', error);
                        Swal.fire({ icon: 'error', title: 'Kesalahan Jaringan', text: error.message || 'Terjadi kesalahan jaringan.' });
                    });
                },
                filteredMenuItems() {
                    let items = this.menuItems;

                    // Filter by selected category (client-side, instant)
                    if (this.selectedKategori) {
                        items = items.filter(item => item.category === this.selectedKategori);
                    }

                    // Filter by search query
                    if (this.searchQuery) {
                        const q = this.searchQuery.toLowerCase();
                        items = items.filter(item =>
                            item.name.toLowerCase().includes(q) ||
                            (item.description && item.description.toLowerCase().includes(q))
                        );
                    }

                    return items;
                },
                groupedMenuItems() {
                    const items = this.filteredMenuItems();
                    const groups = {};
                    items.forEach(item => {
                        const cat = item.category || 'Lainnya';
                        if (!groups[cat]) {
                            groups[cat] = [];
                        }
                        groups[cat].push(item);
                    });
                    
                    return Object.keys(groups).map(key => ({
                        category: key,
                        items: groups[key]
                    }));
                },
            };
        }
    </script>
</body>

</html>
