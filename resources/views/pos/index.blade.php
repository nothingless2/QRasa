<x-app-layout>
    @php $setting = \App\Models\Setting::first(); @endphp
    <!-- Inject SweetAlert globally for the POS layout -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <div class="flex h-screen bg-gray-100 overflow-hidden" x-data="posApp()">
        @if(!$activeShift)
        <!-- Modal Buka Shift (Locking POS) -->
        <div class="fixed inset-0 z-[100] bg-black/60 backdrop-blur-sm flex items-center justify-center">
            <div class="bg-white rounded-3xl shadow-2xl w-full max-w-md p-8 transform transition-all border border-gray-100">
                <div class="text-center mb-6">
                    <div class="w-20 h-20 bg-orange-50 rounded-full flex items-center justify-center mx-auto mb-4 border border-orange-100 shadow-inner">
                        <i class="fas fa-cash-register text-orange-500 text-3xl"></i>
                    </div>
                    <h2 class="text-2xl font-extrabold text-gray-800 tracking-tight">Buka Shift Kasir</h2>
                    <p class="text-gray-700 mt-2 text-sm">Pastikan Anda memasukkan modal awal laci fisik kasir dengan cermat sebelum beroperasi.</p>
                </div>
                
                <form onsubmit="event.preventDefault(); startShift()">
                    <div class="mb-6">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Modal Awal / Uang Laci (Rp)</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-700 font-bold">Rp</span>
                            <input type="number" id="starting_cash" required min="0" 
                                class="w-full pl-12 pr-4 py-3.5 rounded-xl border border-gray-400 focus:ring-4 focus:ring-orange-500/20 focus:border-orange-500 transition-all font-bold text-lg bg-gray-50 focus:bg-white"
                                placeholder="0">
                        </div>
                    </div>
                    
                    <button type="submit" id="btnStartShift"
                        class="w-full bg-gradient-to-r from-orange-500 to-orange-600 text-white font-extrabold py-3.5 rounded-xl hover:from-orange-600 hover:to-orange-700 shadow-md shadow-orange-500/40 transition-all flex justify-center items-center">
                        <span><i class="fas fa-lock-open mr-2"></i> Mulai Shift Baru</span>
                    </button>
                </form>
            </div>
        </div>
        <script>
            async function startShift() {
                const btn = document.getElementById('btnStartShift');
                const cash = document.getElementById('starting_cash').value;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Memproses...';
                btn.disabled = true;

                try {
                    const response = await fetch('{{ route('shift.start') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ starting_cash: cash })
                    });
                    
                    const result = await response.json();
                    if (result.success) {
                        window.location.reload();
                    } else {
                        Swal.fire({ icon: 'error', title: 'Gagal', text: result.message || 'Gagal membuka shift. Silakan coba lagi.' });
                        btn.innerHTML = '<span><i class="fas fa-lock-open mr-2"></i> Mulai Shift Baru</span>';
                        btn.disabled = false;
                    }
                } catch (error) {
                    Swal.fire({ icon: 'error', title: 'Oops...', text: 'Terjadi kesalahan jaringan.' });
                    btn.innerHTML = '<span><i class="fas fa-lock-open mr-2"></i> Mulai Shift Baru</span>';
                    btn.disabled = false;
                }
            }
        </script>
        @endif

        <!-- Navbar Kasir (2-Row Responsive) -->
        <div class="fixed top-0 left-0 right-0 bg-white shadow z-10">
            <!-- Row 1: Brand + User -->
            <div class="flex items-center justify-between px-4 sm:px-6 h-12 border-b border-gray-100">
                <div class="flex items-center gap-3 shrink-0">
                    <a href="{{ route('pesan.index') }}" class="text-gray-600 hover:text-orange-600 transition" title="Kembali ke Daftar Pesanan">
                        <i class="fas fa-arrow-left text-lg"></i>
                    </a>
                    <h1 class="text-base font-bold text-gray-800">QRasa Layar Kasir</h1>
                </div>
                <span class="text-gray-600 font-medium flex items-center text-sm">
                    <i class="fas fa-user-circle mr-2 text-orange-500"></i>{{ auth()->user()->name }}
                </span>
            </div>
            <!-- Row 2: Action Buttons -->
            <div class="flex items-center gap-2 px-4 sm:px-6 h-10 overflow-x-auto" style="scrollbar-width:none;">
                <button onclick="document.getElementById('activeOrdersModal').classList.remove('hidden')" class="shrink-0 bg-indigo-600 text-white font-bold px-3 py-1 rounded-md hover:bg-indigo-700 transition flex items-center whitespace-nowrap text-xs">
                    <i class="fas fa-list-alt mr-1.5"></i> Pesanan Aktif ({{ $activeOrders->count() }})
                </button>
                @if($activeShift)
                <button onclick="document.getElementById('expenseModal').classList.remove('hidden')" class="shrink-0 bg-yellow-500 text-white font-bold px-3 py-1 rounded-md hover:bg-yellow-600 transition flex items-center whitespace-nowrap text-xs">
                    <i class="fas fa-receipt mr-1.5"></i> Catat Pengeluaran
                </button>
                <button onclick="document.getElementById('endShiftModal').classList.remove('hidden')" class="shrink-0 bg-red-600 text-white font-bold px-3 py-1 rounded-md hover:bg-red-700 transition flex items-center whitespace-nowrap text-xs">
                    <i class="fas fa-flag-checkered mr-1.5"></i> Tutup Shift
                </button>
                @endif
            </div>
        </div>

        <!-- Left Content: Menu Grid -->
        <div class="w-full lg:w-7/12 pt-[88px] flex flex-col h-full bg-gray-50 border-r border-gray-300">
            <!-- Category Filter & Search -->
            <div class="bg-white p-4 shadow-sm z-0">
                <div class="flex space-x-2 overflow-x-auto pb-2" style="scrollbar-width: none;">
                    <button @click="selectedCategory = 'all'" 
                            :class="selectedCategory === 'all' ? 'bg-orange-500 text-white shadow-md shadow-orange-500/30' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                            class="px-5 py-2 rounded-full font-semibold text-sm whitespace-nowrap transition">
                        Semua Menu
                    </button>
                    @foreach($kategori as $cat)
                        <button @click="selectedCategory = '{{ $cat }}'"
                                :class="selectedCategory === '{{ $cat }}' ? 'bg-orange-500 text-white shadow-md shadow-orange-500/30' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                                class="px-5 py-2 rounded-full font-semibold text-sm whitespace-nowrap transition">
                            {{ ucfirst($cat) }}
                        </button>
                    @endforeach
                </div>
            </div>

            <!-- Menus Grid Layout -->
            <div class="flex-1 overflow-y-auto p-4 md:p-6 pb-24">
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-4 gap-4 md:gap-6">
                    <template x-for="item in filteredMenus" :key="item.id">
                        <div @click="addToCart(item)" 
                             class="bg-white rounded-2xl shadow-sm hover:border-orange-400 border-2 border-transparent cursor-pointer transition transform hover:-translate-y-1 overflow-hidden flex flex-col"
                             :class="{'opacity-50 pointer-events-none grayscale': item.stok <= 0}">
                            <div class="h-32 md:h-40 w-full bg-gray-100 relative">
                                <img :src="item.image" :alt="item.name" class="w-full h-full object-cover" loading="lazy">
                                <template x-if="item.stok <= 0">
                                    <div class="absolute inset-0 bg-black bg-opacity-60 flex items-center justify-center">
                                        <span class="text-white font-bold tracking-widest text-sm bg-red-600 px-3 py-1 rounded">HABIS</span>
                                    </div>
                                </template>
                                <template x-if="item.discount > 0">
                                    <div class="absolute top-2 right-2 bg-red-500 text-white text-xs font-bold px-2 py-1 rounded-full shadow">
                                        -<span x-text="item.discount"></span>%
                                    </div>
                                </template>
                            </div>
                            <div class="p-3 md:p-4 flex flex-col flex-1">
                                <h3 class="text-sm font-bold text-gray-800 line-clamp-2 leading-snug" x-text="item.name"></h3>
                                <div class="mt-auto pt-3 flex justify-between items-end">
                                    <div>
                                        <template x-if="item.discount > 0">
                                            <p class="text-[10px] text-gray-600 line-through" x-text="'Rp' + formatPrice(item.price)"></p>
                                        </template>
                                        <p class="text-orange-600 font-extrabold text-sm md:text-base" x-text="'Rp' + formatPrice(calculateDiscountedPrice(item.price, item.discount))"></p>
                                    </div>
                                    <p class="text-[11px] font-semibold text-gray-600" x-text="'Sisa: ' + item.stok"></p>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
                <!-- Empty State Kategori -->
                <template x-if="filteredMenus.length === 0">
                    <div class="flex flex-col items-center justify-center h-64 text-gray-700">
                        <i class="fas fa-hamburger text-5xl mb-3 text-gray-300"></i>
                        <p class="font-medium text-lg">Menu untuk kategori ini kosong.</p>
                    </div>
                </template>
            </div>
        </div>

        <!-- Right Content: Cart Area (5/12 on desktop) -->
        <div class="w-full lg:w-5/12 pt-[88px] flex flex-col h-full bg-white shadow-[-10px_0_20px_-5px_rgba(0,0,0,0.05)] z-20 absolute lg:relative right-0 transition-transform duration-300"
             :class="{'translate-x-0': cartOpen, 'translate-x-full lg:translate-x-0': !cartOpen}">
            
            <!-- Mobile Cart Closer -->
            <button @click="cartOpen = false" class="lg:hidden absolute top-20 -left-12 bg-orange-600 text-white w-12 h-12 rounded-l-xl flex items-center justify-center shadow-lg">
                <i class="fas fa-chevron-right"></i>
            </button>

            <!-- Order Type / Table Selection -->
            <div class="px-4 pt-4 pb-3 border-b border-gray-100 bg-white shrink-0">
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Penempatan (*Wajib)</label>
                <div class="relative">
                    <i class="fas fa-map-marker-alt absolute left-3 top-1/2 -translate-y-1/2 text-orange-500 text-sm"></i>
                    <select x-model="selectedMeja" class="w-full pl-9 border-gray-300 rounded-lg shadow-sm focus:border-orange-500 focus:ring focus:ring-orange-200 py-2 text-sm font-medium text-gray-700">
                        <option value="" disabled selected>-- Pilih Meja / Posisi --</option>
                        <option value="bawa_pulang">🛒 Bawa Pulang / Takeaway</option>
                        @foreach($mejas as $meja)
                            <option value="{{ $meja->id }}">🪑 Meja {{ $meja->nomor_meja }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Totals Summary (TOP) -->
            <div class="px-4 pt-3 pb-2 border-b border-gray-100 bg-gray-50 shrink-0">
                <div class="space-y-1 text-xs text-gray-500 mb-2">
                    <div class="flex justify-between">
                        <span>Subtotal</span>
                        <span x-text="'Rp' + formatPrice(grossTotal)"></span>
                    </div>
                    <div class="flex justify-between text-green-600">
                        <span>Diskon</span>
                        <span x-text="'- Rp' + formatPrice(totalDiscount)"></span>
                    </div>
                    <div class="flex justify-between">
                        <span>Servis ({{ $setting ? $setting->service_percent : 5 }}%)</span>
                        <span x-text="'Rp' + formatPrice(serviceCharge)"></span>
                    </div>
                    <div class="flex justify-between">
                        <span>PB1 ({{ $setting ? $setting->tax_percent : 11 }}%)</span>
                        <span x-text="'Rp' + formatPrice(pajakPb1)"></span>
                    </div>
                </div>
                <div class="flex justify-between items-center border-t border-dashed border-gray-300 pt-2">
                    <span class="font-black text-base text-gray-800">TOTAL</span>
                    <span class="font-black text-xl text-orange-600" x-text="'Rp' + formatPrice(grandTotal)"></span>
                </div>
            </div>

            <!-- Payment Method (TOP, below totals) -->
            <div class="px-4 py-2.5 border-b border-gray-100 bg-white shrink-0">
                <p class="text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1.5">Pilih Pembayaran</p>
                <div class="flex flex-wrap gap-1.5">
                    <button @click="paymentMethod = 'tunai'" :class="paymentMethod === 'tunai' ? 'bg-orange-500 text-white border-orange-500' : 'bg-white text-gray-600 border-gray-200 hover:border-orange-300'" class="flex items-center gap-1 px-2.5 py-1 border rounded-full text-[11px] font-semibold transition">
                        <i class="fas fa-money-bill-wave text-[10px]"></i> Tunai
                    </button>
                    <button @click="paymentMethod = 'transfer'" :class="paymentMethod === 'transfer' ? 'bg-orange-500 text-white border-orange-500' : 'bg-white text-gray-600 border-gray-200 hover:border-orange-300'" class="flex items-center gap-1 px-2.5 py-1 border rounded-full text-[11px] font-semibold transition">
                        <i class="fas fa-exchange-alt text-[10px]"></i> Transfer
                    </button>
                    <button @click="paymentMethod = 'qris'" :class="paymentMethod === 'qris' ? 'bg-orange-500 text-white border-orange-500' : 'bg-white text-gray-600 border-gray-200 hover:border-orange-300'" class="flex items-center gap-1 px-2.5 py-1 border rounded-full text-[11px] font-semibold transition">
                        <i class="fas fa-qrcode text-[10px]"></i> QRIS
                    </button>
                    <button @click="paymentMethod = 'gopay'" :class="paymentMethod === 'gopay' ? 'bg-green-500 text-white border-green-500' : 'bg-white text-gray-600 border-gray-200 hover:border-green-300'" class="flex items-center gap-1 px-2.5 py-1 border rounded-full text-[11px] font-semibold transition">
                        <i class="fas fa-wallet text-[10px]"></i> GoPay
                    </button>
                    <button @click="paymentMethod = 'ovo'" :class="paymentMethod === 'ovo' ? 'bg-purple-600 text-white border-purple-600' : 'bg-white text-gray-600 border-gray-200 hover:border-purple-300'" class="flex items-center gap-1 px-2.5 py-1 border rounded-full text-[11px] font-semibold transition">
                        <i class="fas fa-wallet text-[10px]"></i> OVO
                    </button>
                    <button @click="paymentMethod = 'dana'" :class="paymentMethod === 'dana' ? 'bg-blue-500 text-white border-blue-500' : 'bg-white text-gray-600 border-gray-200 hover:border-blue-300'" class="flex items-center gap-1 px-2.5 py-1 border rounded-full text-[11px] font-semibold transition">
                        <i class="fas fa-wallet text-[10px]"></i> DANA
                    </button>
                    <button @click="paymentMethod = 'shopeepay'" :class="paymentMethod === 'shopeepay' ? 'bg-orange-500 text-white border-orange-500' : 'bg-white text-gray-600 border-gray-200 hover:border-orange-300'" class="flex items-center gap-1 px-2.5 py-1 border rounded-full text-[11px] font-semibold transition">
                        <i class="fas fa-wallet text-[10px]"></i> ShopeePay
                    </button>
                </div>
            </div>

            <!-- Cart Items List (MIDDLE, scrollable) -->
            <div class="flex-1 overflow-y-auto p-3 space-y-2 bg-gray-50/50 min-h-0">
                <template x-if="cart.length === 0">
                    <div class="flex flex-col items-center justify-center h-full text-gray-600 py-6">
                        <i class="fas fa-shopping-basket text-4xl mb-2 text-gray-200"></i>
                        <p class="font-bold text-gray-600 text-sm">Keranjang masih kosong</p>
                        <p class="text-xs text-gray-400">Klik menu di kiri untuk menambah</p>
                    </div>
                </template>

                <template x-for="(cartItem, index) in cart" :key="cartItem.id">
                    <div class="flex items-center bg-white p-2.5 rounded-xl border border-gray-100 shadow-sm hover:border-orange-200 transition gap-2">
                        <div class="flex-1 min-w-0">
                            <h4 class="text-sm font-bold text-gray-800 leading-snug truncate" x-text="cartItem.name"></h4>
                            <p class="text-orange-500 font-bold text-xs mt-0.5" x-text="'Rp' + formatPrice(cartItem.price * cartItem.quantity)"></p>
                            <input type="text" x-model="cartItem.notes"
                                :placeholder="(cartItem.category || '').toLowerCase().includes('minuman') ? 'less sugar, no ice...' : 'pedas, tanpa sayur...'"
                                class="w-full text-[11px] border-0 border-b border-gray-200 focus:border-orange-400 focus:ring-0 py-0.5 px-0 text-gray-500 placeholder-gray-300 bg-transparent mt-1">
                        </div>
                        <div class="flex items-center gap-1 shrink-0">
                            <button @click="updateQuantity(index, -1)" class="w-7 h-7 flex items-center justify-center bg-gray-100 rounded-lg text-gray-600 hover:text-red-500 hover:bg-red-50 transition border border-gray-200">
                                <i class="fas fa-minus text-[10px]"></i>
                            </button>
                            <span class="w-6 text-center font-black text-sm text-gray-800" x-text="cartItem.quantity"></span>
                            <button @click="updateQuantity(index, 1)" class="w-7 h-7 flex items-center justify-center bg-gray-100 rounded-lg text-gray-600 hover:text-orange-500 hover:bg-orange-50 transition border border-gray-200">
                                <i class="fas fa-plus text-[10px]"></i>
                            </button>
                            <button @click="removeItem(index)" class="w-7 h-7 flex items-center justify-center text-gray-300 hover:text-red-600 hover:bg-red-50 rounded-lg transition ml-1">
                                <i class="fas fa-trash-alt text-xs"></i>
                            </button>
                        </div>
                    </div>
                </template>
            </div>

            <!-- Checkout Button (PINNED BOTTOM) -->
            <div class="px-4 py-3 border-t border-gray-200 bg-white shrink-0">
                <button @click="checkout()"
                        :disabled="cart.length === 0 || isProcessing"
                        :class="cart.length === 0 ? 'bg-gray-200 text-gray-400 cursor-not-allowed' : (isProcessing ? 'bg-orange-400 cursor-wait text-white' : 'bg-orange-600 text-white hover:bg-orange-700 shadow-lg shadow-orange-500/30 active:scale-[0.99]')"
                        class="w-full font-bold py-3 rounded-xl text-base flex justify-center items-center transition-all">
                    <span x-show="!isProcessing"><i class="fas fa-check-circle mr-2"></i> Proses Bayar</span>
                    <span x-show="isProcessing"><i class="fas fa-spinner fa-spin mr-2"></i> Memproses...</span>
                </button>
            </div>
        </div>
        
        <!-- Mobile Floating Cart Button -->
        <div class="lg:hidden fixed bottom-6 right-6 z-10 w-auto rounded-full shadow-2xl overflow-hidden" x-show="!cartOpen" x-transition.opacity>
            <button @click="cartOpen = true" class="bg-orange-600 text-white font-bold py-3 px-6 flex items-center shadow-lg shadow-orange-600/50 relative">
                <i class="fas fa-shopping-basket text-xl mr-2"></i>
                Lihat Tagihan <!-- Total Bubble -->
                <span x-show="cart.length > 0" class="absolute -top-1 -right-1 bg-red-500 text-white text-[10px] w-5 h-5 flex items-center justify-center rounded-full border border-white" x-text="cart.length"></span>
            </button>
        </div>

    </div>

    @push('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('posApp', () => ({
                menus: @json($menus),
                selectedCategory: 'all',
                cart: [],
                selectedMeja: '',
                paymentMethod: 'tunai',
                isProcessing: false,
                cartOpen: false, // For responsive mobile sidebar

                get filteredMenus() {
                    if (this.selectedCategory === 'all') {
                        return this.menus;
                    }
                    return this.menus.filter(m => m.category === this.selectedCategory);
                },

                addToCart(item) {
                    if (item.stok <= 0) return;
                    
                    const existingItem = this.cart.find(c => c.id === item.id);
                    if (existingItem) {
                        if (existingItem.quantity < item.stok) {
                            existingItem.quantity++;
                        } else {
                            // Max warning can be silent or subtle
                        }
                    } else {
                        // Discount pre-calculation so frontend doesn't overcomplicate
                        const finalPrice = this.calculateDiscountedPrice(item.price, item.discount);
                        this.cart.push({ ...item, quantity: 1, base_price: item.price, price: finalPrice, notes: '' });
                    }
                    // Auto-open cart on mobile if buying something
                    if(window.innerWidth < 1024) this.cartOpen = true;
                },

                updateQuantity(index, modifier) {
                    const item = this.cart[index];
                    const newQuant = item.quantity + modifier;
                    
                    if (newQuant > 0 && newQuant <= item.stok) {
                        item.quantity = newQuant;
                    }
                },

                removeItem(index) {
                    this.cart.splice(index, 1);
                    if(this.cart.length === 0) this.cartOpen = false;
                },

                calculateDiscountedPrice(price, discountPercentage) {
                    if (!discountPercentage) return price;
                    return price - (price * (discountPercentage / 100));
                },

                get grossTotal() {
                    return this.cart.reduce((sum, item) => sum + (item.base_price * item.quantity), 0);
                },

                get totalDiscount() {
                    return this.cart.reduce((sum, item) => {
                        const originalTotal = item.base_price * item.quantity;
                        const discountedTotal = item.price * item.quantity;
                        return sum + (originalTotal - discountedTotal);
                    }, 0);
                },

                get netSubtotal() {
                    return this.cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
                },

                get serviceCharge() {
                    return Math.round(this.netSubtotal * {{ ($setting ? $setting->service_percent : 5) / 100 }});
                },

                get pajakPb1() {
                    return Math.round((this.netSubtotal + this.serviceCharge) * {{ ($setting ? $setting->tax_percent : 11) / 100 }});
                },

                get grandTotal() {
                    return this.netSubtotal + this.serviceCharge + this.pajakPb1;
                },

                formatPrice(value) {
                    return new Intl.NumberFormat('id-ID').format(value);
                },

                checkout() {
                    if (this.cart.length === 0) return;
                    if (!this.selectedMeja) {
                        Swal.fire({ icon: 'warning', title: 'Perhatian', text: 'Mohon pilih Penempatan meja atau Takeaway sebelum memproses pembayaran!' });
                        return;
                    }
                    
                    this.isProcessing = true;

                    // Match PesanController structure
                    const payload = {
                        cartItems: this.cart.map(item => ({
                            id: item.id,
                            quantity: item.quantity,
                            notes: item.notes || null
                        })),
                        total: this.grandTotal,
                        payment_method: this.paymentMethod,
                        meja_id: this.selectedMeja === 'bawa_pulang' ? null : this.selectedMeja
                    };

                    fetch('{{ route('pesan.store') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify(payload)
                    })
                    .then(async response => {
                        const isJson = response.headers.get('content-type')?.includes('application/json');
                        const data = isJson ? await response.json() : null;

                        if (!response.ok) {
                            // Automatically catches 422, 500, etc from Laravel
                            const errorMsg = (data && data.message) ? data.message : response.statusText;
                            throw new Error(errorMsg);
                        }
                        
                        if (!isJson) {
                            throw new Error('Sistem gagal merespon data yang valid (HTML terdeteksi).');
                        }
                        return data;
                    })
                    .then(data => {
                        this.isProcessing = false;
                        if (data.success) {
                            Swal.fire({ 
                                icon: 'success', 
                                title: 'Berhasil!', 
                                text: 'Struk Berhasil Tercatat untuk ' + (this.selectedMeja ? 'Meja ' + this.selectedMeja : 'Bawa Pulang') 
                            }).then(() => {
                                if (data.created_pesan_ids && data.created_pesan_ids.length > 0) {
                                    // Trigger thermal POS printing via popup directly from a user click event to prevent pop-up blockers
                                    window.open('/pesan/' + data.created_pesan_ids[0] + '/struk-kasir', 'printWindow', 'width=400,height=600');
                                }
                                setTimeout(() => window.location.reload(), 300);
                            });
                        } else {
                            Swal.fire({ icon: 'error', title: 'Kesalahan', text: data.message || 'Kesalahan Server!' });
                        }
                    })
                    .catch(err => {
                        this.isProcessing = false;
                        console.error(err);
                        Swal.fire({ icon: 'error', title: 'Gagal', text: err.message || 'Koneksi internet / Jaringan bermasalah.' });
                    });
                }
            }));
        });
    </script>
    @endpush

    <!-- Modal Pesanan Aktif (Unified Panel) -->
    <div id="activeOrdersModal" class="fixed inset-0 z-[90] bg-black/60 backdrop-blur-sm flex items-center justify-center hidden">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-6xl h-[90vh] flex flex-col transform transition-all border border-gray-100">
            <div class="flex justify-between items-center p-4 border-b border-gray-300 bg-gray-50 rounded-t-2xl">
                <h2 class="text-xl font-extrabold text-gray-800 tracking-tight"><i class="fas fa-clipboard-list mr-2 text-indigo-600"></i>Daftar Pesanan Hari Ini</h2>
                <button onclick="document.getElementById('activeOrdersModal').classList.add('hidden')" class="text-gray-700 hover:text-red-500 transition border border-gray-400 rounded-full w-8 h-8 flex items-center justify-center bg-white shadow-sm">
                    <i class="fas fa-times text-sm"></i>
                </button>
            </div>
            
            <div class="flex-1 overflow-y-auto p-4 bg-gray-50">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @forelse($activeOrders as $ord)
                        <div class="bg-white rounded-xl border border-gray-300 shadow-sm p-4 relative flex flex-col">
                            <div class="flex justify-between items-start mb-2 border-b border-gray-100 pb-2">
                                <div>
                                    <span class="text-xs font-bold text-gray-600">Order #{{ $ord->id }}</span>
                                    <h3 class="font-bold text-gray-800 text-lg">{{ $ord->meja ? 'Meja ' . $ord->meja->nomor_meja : 'Bawa Pulang' }}</h3>
                                </div>
                                <div class="text-right">
                                    <span class="text-[10px] text-gray-700">{{ $ord->created_at->format('H:i') }}</span>
                                    @if ($ord->status_pembayaran === 'belum dibayar' || $ord->status_pembayaran === null)
                                        <span class="block text-xs font-bold text-red-600 bg-red-50 px-2 rounded mt-1">Belum Bayar</span>
                                    @elseif ($ord->status_pembayaran === 'pending')
                                        <span class="block text-xs font-bold text-yellow-600 bg-yellow-50 px-2 rounded mt-1">Pending</span>
                                    @else
                                        <span class="block text-xs font-bold text-green-600 bg-green-50 px-2 rounded mt-1">LUNAS ({{ strtoupper($ord->payment_method) }})</span>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="flex-1 overflow-y-auto max-h-32 mb-3 text-sm space-y-1">
                                @php $totalHargaPrint = 0; @endphp
                                @foreach($ord->menus as $om)
                                    @php 
                                        $qty = (int) ($om->pivot->quantity ?? $om->pivot->jumlah ?? 1); 
                                        $hargaDiscount = $om->harga - ($om->harga * (($om->diskon ?? 0)/100));
                                        $totalHargaPrint += ($qty * $hargaDiscount);
                                    @endphp
                                    <div class="flex justify-between text-gray-600">
                                        <span>{{ $qty }}x {{ $om->nama }}</span>
                                        <span>Rp{{ number_format($qty * $hargaDiscount, 0, ',', '.') }}</span>
                                    </div>
                                @endforeach
                            </div>
                            
                            <div class="border-t border-gray-100 pt-2 mb-3 flex justify-between font-bold text-gray-800">
                                <span>Total:</span>
                                <span>Rp{{ number_format($totalHargaPrint, 0, ',', '.') }}</span>
                            </div>

                            <!-- Actions -->
                            <div class="mt-auto space-y-2">
                                @if ($ord->status_pembayaran === 'belum dibayar' || $ord->status_pembayaran === null)
                                    <form action="{{ route('pesan.updateStatusPembayaran', $ord->id) }}" method="POST" id="form-pay-{{ $ord->id }}">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="payment_method" id="pay-method-{{ $ord->id }}" value="{{ $ord->payment_method }}">
                                        <button type="button" onclick="confirmPayment({{ $ord->id }}, '{{ strtolower($ord->payment_method) }}')" class="w-full bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 rounded text-sm shadow-sm transition">
                                            <i class="fas fa-check-circle mr-1"></i> Selesaikan Kasir
                                        </button>
                                    </form>
                                    
                                    @php
                                        $splitMenus = $ord->menus->map(function($m) {
                                            return [
                                                "id" => $m->id,
                                                "nama" => $m->nama,
                                                "harga" => $m->pivot->harga,
                                                "jumlah" => $m->pivot->jumlah ?? $m->pivot->quantity ?? 0
                                            ];
                                        });
                                    @endphp
                                    <div class="flex space-x-2">
                                        <button onclick="openMoveTableModal({{ $ord->id }}, {{ $ord->meja_id ?? 'null' }})" class="w-1/2 border border-gray-400 hover:bg-gray-100 text-gray-700 font-bold py-1.5 rounded text-xs transition">Pindah Meja</button>
                                        <button onclick='openSplitBillModal({{ $ord->id }}, @json($splitMenus))' class="w-1/2 border border-indigo-200 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-bold py-1.5 rounded text-xs transition">Pisah Bon</button>
                                    </div>
                                @elseif ($ord->status_pembayaran === 'pending')
                                    <form action="{{ route('pesan.updateStatusPembayaran', $ord->id) }}" method="POST" id="form-pay-{{ $ord->id }}">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="payment_method" id="pay-method-{{ $ord->id }}" value="{{ $ord->payment_method }}">
                                        <button type="button" onclick="confirmPayment({{ $ord->id }}, '{{ strtolower($ord->payment_method) }}')" class="w-full bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 rounded text-sm shadow-sm transition">
                                            <i class="fas fa-check-circle mr-1"></i> Tandai Lunas
                                        </button>
                                    </form>
                                @elseif ($ord->status_pembayaran === 'sudah dibayar')
                                    <button onclick="window.open('/pesan/{{ $ord->id }}/struk-kasir', 'printWindow', 'width=400,height=600')" class="w-full bg-gray-800 hover:bg-gray-900 text-white font-bold py-2 rounded text-sm shadow-sm transition">
                                        <i class="fas fa-print mr-1"></i> Cetak Ulang Struk
                                    </button>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full py-10 text-center text-gray-600 font-bold">
                            Kosong. Belum ada transaksi tercatat hari ini.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Active Orders Internal JS Logic -->
    <script>
        function openMoveTableModal(pesanId, currentMejaId) {
            Swal.fire({
                title: 'Pilih Meja Target',
                html: `
                    <select id="swal-meja-select" class="swal2-select" style="display: flex;">
                        @foreach($mejas as $mejaItem)
                            <option value="{{ $mejaItem->id }}">Meja {{ $mejaItem->nomor_meja }}</option>
                        @endforeach
                    </select>
                `,
                showCancelButton: true,
                confirmButtonText: 'Pindahkan',
                cancelButtonText: 'Batal',
                preConfirm: () => {
                    const newMejaId = document.getElementById('swal-meja-select').value;
                    if (newMejaId == currentMejaId) {
                        Swal.showValidationMessage('Pesanan sudah berada di meja tersebut.');
                    }
                    return newMejaId;
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/pesan/${pesanId}/move-table`, {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ meja_id: result.value })
                    })
                    .then(res => res.json())
                    .then(data => {
                        if(data.success) {
                            Swal.fire('Berhasil!', data.message, 'success').then(() => location.reload());
                        } else {
                            Swal.fire('Gagal!', data.message, 'error');
                        }
                    });
                }
            });
        }

        function openSplitBillModal(pesanId, menus) {
            let htmlOptions = '<div class="text-left"><p class="mb-2 text-sm text-gray-700">Pilih kuantitas menu yang ingin displit ke BONG BARU (Pesanan Baru).</p><table class="w-full text-sm"><tbody>';
            menus.forEach(menu => {
                htmlOptions += `
                    <tr class="border-b">
                        <td class="py-2">${menu.nama}</td>
                        <td class="py-2 text-right">
                            <input type="number" id="split-qty-${menu.id}" min="0" max="${menu.jumlah}" value="0" class="w-16 p-1 border rounded text-center">
                            <span class="text-gray-600 text-xs">/ ${menu.jumlah}</span>
                        </td>
                    </tr>
                `;
            });
            htmlOptions += '</tbody></table></div>';

            Swal.fire({
                title: 'Pisah Bon (Split Bill)',
                html: htmlOptions,
                showCancelButton: true,
                confirmButtonText: 'Split & Buat Tagihan Baru',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#4f46e5',
                preConfirm: () => {
                    let itemsToSplit = {};
                    let hasItems = false;
                    menus.forEach(menu => {
                        const qty = parseInt(document.getElementById(`split-qty-${menu.id}`).value) || 0;
                        if (qty > 0) {
                            itemsToSplit[menu.id] = qty;
                            hasItems = true;
                        }
                    });
                    
                    if (!hasItems) {
                        Swal.showValidationMessage('Belum ada item ditentukan untuk dipisah.');
                    }
                    return itemsToSplit;
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/pesan/${pesanId}/split-bill`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ items: result.value })
                    })
                    .then(res => res.json())
                    .then(data => {
                        if(data.success) {
                            Swal.fire('Berhasil Dipisah!', data.message, 'success').then(() => location.reload());
                        } else {
                            Swal.fire('Gagal!', data.message, 'error');
                        }
                    });
                }
            });
        }

        function confirmPayment(pesanId, currentMethod) {
            Swal.fire({
                title: 'Konfirmasi Pembayaran Aktif',
                html: `
                    <div class="mb-3 text-sm text-gray-600">Pilih pembayaran untuk Pesanan #${pesanId}:</div>
                    <div class="flex flex-col space-y-2 text-left">
                        <label class="flex items-center space-x-3 p-3 border rounded-lg cursor-pointer hover:bg-green-50">
                            <input type="radio" name="swal_payment_method" value="tunai" ${(!currentMethod || currentMethod === 'tunai') ? 'checked' : ''} class="text-green-600 h-4 w-4">
                            <span class="font-bold text-gray-700">Tunai</span>
                        </label>
                        <label class="flex items-center space-x-3 p-3 border rounded-lg cursor-pointer hover:bg-blue-50">
                            <input type="radio" name="swal_payment_method" value="qris" ${currentMethod === 'qris' ? 'checked' : ''} class="text-blue-600 h-4 w-4">
                            <span class="font-bold text-gray-700">QRIS</span>
                        </label>
                        <label class="flex items-center space-x-3 p-3 border rounded-lg cursor-pointer hover:bg-purple-50">
                            <input type="radio" name="swal_payment_method" value="transfer" ${currentMethod === 'transfer' ? 'checked' : ''} class="text-purple-600 h-4 w-4">
                            <span class="font-bold text-gray-700">Transfer</span>
                        </label>
                    </div>
                `,
                showCancelButton: true,
                confirmButtonText: 'Selesaikan',
            }).then((result) => {
                if (result.isConfirmed) {
                    const sel = document.querySelector('input[name="swal_payment_method"]:checked').value;
                    document.getElementById('pay-method-' + pesanId).value = sel;
                    document.getElementById('form-pay-' + pesanId).submit();
                }
            });
        }
    </script>

    @if($activeShift)
    <!-- Modal Tutup Shift -->
    <div id="endShiftModal" class="fixed inset-0 z-[100] bg-black/60 backdrop-blur-sm flex items-center justify-center hidden">
        <div class="bg-white rounded-3xl shadow-2xl w-full max-w-md p-8 transform transition-all border border-gray-100 max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-extrabold text-gray-800 tracking-tight">Tutup Shift Kasir</h2>
                <button onclick="document.getElementById('endShiftModal').classList.add('hidden')" class="text-gray-600 hover:text-red-500 transition">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <div class="bg-blue-50 border border-blue-100 p-4 rounded-xl mb-6">
                <p class="text-sm font-medium text-blue-800">
                    <i class="fas fa-info-circle mr-2"></i>Sistem akan menghitung uang laci berdasarkan Modal Awal dan semua transaksi Tunai.
                </p>
            </div>

            <form onsubmit="event.preventDefault(); endShift()">
                <div class="mb-6">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Hitung & Masukkan Total Uang Fisik Laci Saat Ini (Rp)</label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-700 font-bold">Rp</span>
                        <input type="number" id="ending_cash_actual" required min="0" 
                            class="w-full pl-12 pr-4 py-3.5 rounded-xl border border-gray-400 focus:ring-4 focus:ring-red-500/20 focus:border-red-500 transition-all font-bold text-lg bg-gray-50 focus:bg-white"
                            placeholder="0">
                    </div>
                </div>
                
                <button type="submit" id="btnEndShift"
                    class="w-full bg-gradient-to-r from-red-500 to-red-600 text-white font-extrabold py-3.5 rounded-xl hover:from-red-600 hover:to-red-700 shadow-md shadow-red-500/40 transition-all flex justify-center items-center">
                    <span><i class="fas fa-flag-checkered mr-2"></i> Konfirmasi Penutupan Shift</span>
                </button>
            </form>

            <!-- Result Summary (Hidden initially) -->
            <div id="endShiftResult" class="hidden mt-6 border-t border-dashed border-gray-300 pt-6">
                <h3 class="font-bold text-gray-800 mb-4 text-center">Ringkasan Selesai Shift</h3>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-700">Uang Laci Seharusnya (Sistem):</span>
                        <span id="resExpected" class="font-bold text-gray-800">Rp 0</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-700">Uang Fisik Dihitung Kasir:</span>
                        <span id="resActual" class="font-bold text-gray-800">Rp 0</span>
                    </div>
                    <div class="flex justify-between pt-3 border-t border-gray-100">
                        <span class="text-gray-800 font-bold">Selisih (Variance):</span>
                        <span id="resVariance" class="font-bold text-xl">Rp 0</span>
                    </div>
                </div>
                <button onclick="window.location.reload()" class="w-full mt-6 bg-gray-100 text-gray-800 font-bold py-3.5 rounded-xl hover:bg-gray-200 transition-all shadow-sm">
                    Kembali ke Dashboard Utama
                </button>
            </div>
        </div>
    </div>
    <script>
        async function endShift() {
            const btn = document.getElementById('btnEndShift');
            const cash = document.getElementById('ending_cash_actual').value;
            
            const confirmResult = await Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Hitungan fisik uang laci sudah benar dan final? Aksi ini tidak dapat dibatalkan.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Tutup Shift!'
            });

            if (!confirmResult.isConfirmed) return;

            btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Memproses...';
            btn.disabled = true;

            try {
                const response = await fetch('{{ route('shift.end') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ ending_cash_actual: cash })
                });
                
                const result = await response.json();
                if (result.success) {
                    const fmt = (num) => 'Rp ' + parseInt(num).toLocaleString('id-ID');
                    document.getElementById('resExpected').innerText = fmt(result.summary.expected);
                    document.getElementById('resActual').innerText = fmt(result.summary.actual);
                    
                    const varianceEl = document.getElementById('resVariance');
                    varianceEl.innerText = result.summary.variance < 0 ? '-' + fmt(Math.abs(result.summary.variance)) : '+' + fmt(result.summary.variance);
                    if (result.summary.variance < 0) {
                        varianceEl.classList.add('text-red-600');
                    } else if (result.summary.variance > 0) {
                        varianceEl.classList.add('text-green-600');
                    } else {
                        varianceEl.classList.add('text-gray-800');
                        varianceEl.innerText = "PAS (Rp 0)";
                    }

                    document.getElementById('endShiftResult').classList.remove('hidden');
                    btn.style.display = 'none';
                    document.getElementById('ending_cash_actual').disabled = true;
                } else {
                    Swal.fire({ icon: 'error', title: 'Gagal', text: result.message || 'Gagal menutup shift.' });
                    btn.innerHTML = '<span><i class="fas fa-flag-checkered mr-2"></i> Konfirmasi Penutupan Shift</span>';
                    btn.disabled = false;
                }
            } catch (error) {
                Swal.fire({ icon: 'error', title: 'Networking Error', text: 'Terjadi kesalahan jaringan.' });
                btn.innerHTML = '<span><i class="fas fa-flag-checkered mr-2"></i> Konfirmasi Penutupan Shift</span>';
                btn.disabled = false;
            }
        }
    </script>
    @endif

    @if($activeShift)
    <!-- Modal Pengeluaran / Kasbon -->
    <div id="expenseModal" class="fixed inset-0 z-[100] bg-black/60 backdrop-blur-sm flex items-center justify-center hidden">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md transform transition-all border border-gray-100 max-h-[90vh] flex flex-col">
            <div class="flex justify-between items-center p-5 border-b border-gray-300 bg-gray-50 rounded-t-2xl">
                <h2 class="text-lg font-extrabold text-gray-800 tracking-tight"><i class="fas fa-receipt mr-2 text-yellow-600"></i>Catat Pengeluaran</h2>
                <button onclick="document.getElementById('expenseModal').classList.add('hidden')" class="text-gray-700 hover:text-red-500 transition border border-gray-400 rounded-full w-8 h-8 flex items-center justify-center bg-white shadow-sm">
                    <i class="fas fa-times text-sm"></i>
                </button>
            </div>
            <div class="p-5 flex-1 overflow-y-auto">
                <form onsubmit="event.preventDefault(); submitExpense()">
                    <div class="mb-4">
                        <label class="block text-sm font-bold text-gray-700 mb-1">Nominal (Rp)</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-700 font-bold text-sm">Rp</span>
                            <input type="number" id="expense_amount" required min="1" max="10000000"
                                class="w-full pl-10 pr-4 py-2.5 rounded-lg border border-gray-400 focus:ring-2 focus:ring-yellow-500/20 focus:border-yellow-500 font-bold text-gray-800 text-sm" placeholder="0">
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-bold text-gray-700 mb-1">Keterangan</label>
                        <input type="text" id="expense_description" required maxlength="255"
                            class="w-full px-3 py-2.5 rounded-lg border border-gray-400 focus:ring-2 focus:ring-yellow-500/20 focus:border-yellow-500 text-sm" placeholder="Beli es batu, gas 3kg, dll...">
                    </div>
                    <button type="submit" id="btnSubmitExpense"
                        class="w-full bg-yellow-500 text-white font-bold py-2.5 rounded-lg hover:bg-yellow-600 transition shadow-sm flex items-center justify-center">
                        <i class="fas fa-plus-circle mr-2"></i> Catat Pengeluaran
                    </button>
                </form>
                <div class="mt-5 border-t border-gray-300 pt-4">
                    <div class="flex justify-between items-center mb-3">
                        <h3 class="text-sm font-bold text-gray-600">Riwayat Shift Ini</h3>
                        <span id="expenseTotalBadge" class="text-xs font-bold bg-red-50 text-red-600 px-2 py-1 rounded-lg border border-red-100">Total: Rp 0</span>
                    </div>
                    <div id="expenseList" class="space-y-2 max-h-48 overflow-y-auto">
                        <div class="text-center text-gray-600 text-sm py-4 italic">Memuat data...</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        const expenseModalEl = document.getElementById('expenseModal');
        new MutationObserver(() => { if (!expenseModalEl.classList.contains('hidden')) loadExpenses(); }).observe(expenseModalEl, { attributes: true, attributeFilter: ['class'] });
        async function loadExpenses() {
            try {
                const res = await fetch('{{ route("expense.shiftExpenses") }}', { headers: { 'Accept': 'application/json' } });
                const data = await res.json();
                document.getElementById('expenseTotalBadge').textContent = 'Total: Rp ' + new Intl.NumberFormat('id-ID').format(data.total || 0);
                const listEl = document.getElementById('expenseList');
                if (!data.expenses || data.expenses.length === 0) { listEl.innerHTML = '<div class="text-center text-gray-600 text-sm py-4 italic">Belum ada pengeluaran di shift ini.</div>'; return; }
                listEl.innerHTML = data.expenses.map(exp => '<div class="flex justify-between items-center bg-gray-50 rounded-lg p-3 border border-gray-100"><div><p class="text-sm font-semibold text-gray-800">'+exp.description+'</p><p class="text-[10px] text-gray-600">'+new Date(exp.created_at).toLocaleTimeString('id-ID',{hour:'2-digit',minute:'2-digit'})+'</p></div><span class="text-sm font-bold text-red-600">-Rp '+new Intl.NumberFormat('id-ID').format(exp.amount)+'</span></div>').join('');
            } catch (e) { console.error(e); }
        }
        async function submitExpense() {
            const btn = document.getElementById('btnSubmitExpense');
            const amount = document.getElementById('expense_amount').value;
            const desc = document.getElementById('expense_description').value;
            if (!amount || !desc) return;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Menyimpan...'; btn.disabled = true;
            try {
                const res = await fetch('{{ route("expense.store") }}', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'), 'Accept': 'application/json' }, body: JSON.stringify({ amount: parseInt(amount), description: desc }) });
                const data = await res.json();
                if (data.success) { document.getElementById('expense_amount').value = ''; document.getElementById('expense_description').value = ''; loadExpenses(); Swal.fire({ icon: 'success', title: 'Tercatat!', text: data.message, timer: 1500, showConfirmButton: false }); }
                else { Swal.fire({ icon: 'error', title: 'Gagal', text: data.message }); }
            } catch (e) { Swal.fire({ icon: 'error', title: 'Error', text: 'Gagal menyimpan pengeluaran.' }); }
            btn.innerHTML = '<i class="fas fa-plus-circle mr-2"></i> Catat Pengeluaran'; btn.disabled = false;
        }
    </script>
    @endif

</x-app-layout>
