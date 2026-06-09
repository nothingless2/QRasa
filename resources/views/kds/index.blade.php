<x-app-layout>
    <div class="h-screen flex flex-col bg-gray-900 text-gray-100 overflow-hidden" x-data="kdsApp()">
        
        <!-- Top Navbar KDS -->
        <div class="h-16 bg-gray-800 shadow flex items-center justify-between px-6 flex-shrink-0 border-b border-gray-700">
            <div class="flex items-center space-x-4">
                <a href="{{ route('pesan.index') }}" class="text-gray-600 hover:text-white transition" title="Kembali ke Pesanan">
                    <i class="fas fa-arrow-left text-xl"></i>
                </a>
                <h1 class="text-xl font-bold text-white tracking-widest uppercase">
                    <i class="fas fa-fire-burner text-orange-500 mr-2"></i> KDS (Kitchen Display System)
                </h1>
            </div>
            <div class="flex items-center space-x-5">
                <!-- Clock Realtime -->
                <div class="text-xl font-mono text-gray-300 bg-gray-700 px-4 py-1 rounded-lg" x-text="currentTime"></div>
                <!-- Connection Status Ping -->
                <div class="flex items-center">
                    <span class="relative flex h-3 w-3 mr-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75" x-show="isOnline"></span>
                        <span class="relative inline-flex rounded-full h-3 w-3" :class="isOnline ? 'bg-green-500' : 'bg-red-500'"></span>
                    </span>
                    <span class="text-sm font-semibold" :class="isOnline ? 'text-green-400' : 'text-red-400'" x-text="isOnline ? 'LIVE' : 'OFFLINE'"></span>
                </div>
            </div>
        </div>

        <!-- Main Kanban Board Area -->
        <div class="flex-1 overflow-x-auto overflow-y-hidden p-6">
            <template x-if="isLoading && orders.length === 0">
                <div class="h-full flex items-center justify-center">
                    <i class="fas fa-spinner fa-spin text-4xl text-gray-700"></i>
                </div>
            </template>
            
            <template x-if="!isLoading && orders.length === 0">
                <div class="h-full flex flex-col items-center justify-center text-gray-700">
                    <i class="fas fa-utensils text-6xl mb-4 text-gray-700"></i>
                    <p class="text-2xl font-light">Tidak ada pesanan masuk.</p>
                    <p class="text-gray-600 mt-2">Dapur sedang bersantai.</p>
                </div>
            </template>
            
            <!-- Cards Container (Horizontal Scroll) -->
            <div class="flex space-x-6 h-full items-start" style="width: max-content;">
                
                <template x-for="order in orders" :key="order.id">
                    <!-- Ticket Card -->
                    <div class="w-80 h-full max-h-[95%] bg-white rounded-xl shadow-2xl overflow-hidden flex flex-col flex-shrink-0 transition-all duration-500"
                         :class="{
                            'border-t-8 border-green-500': order.is_new,
                            'border-t-8 border-yellow-400': !order.is_new,
                            'transform scale-100 opacity-100': true,
                            'animate-pulse shadow-[0_0_15px_rgba(34,197,94,0.5)]': order.is_new && order.time_minutes < 1 // Visual pop for very new orders
                         }">
                        
                        <!-- Card Header -->
                        <div class="px-5 py-4 flex justify-between items-center" :class="order.is_new ? 'bg-green-50' : 'bg-yellow-50'">
                            <div>
                                <h3 class="font-black text-2xl text-gray-800" x-text="'#' + order.id"></h3>
                                <p class="text-sm font-bold text-gray-600 uppercase tracking-widest mt-1" x-text="order.meja"></p>
                            </div>
                            <div class="text-right">
                                <span class="bg-gray-800 text-white text-xs font-mono px-2 py-1 rounded" x-text="order.created_at"></span>
                                <p class="text-xs font-bold mt-1" :class="order.time_minutes >= 15 ? 'text-red-600' : 'text-gray-700'" x-text="formatWaitTime(order.time_minutes)"></p>
                            </div>
                        </div>
                        
                        <!-- Order Items List -->
                        <div class="flex-1 overflow-y-auto bg-white p-5">
                            <ul class="space-y-4">
                                <template x-for="item in order.menus">
                                    <li class="flex items-start">
                                        <div class="bg-gray-100 font-bold text-gray-800 min-w-[32px] h-[32px] flex items-center justify-center rounded pt-0.5 text-lg mr-3" x-text="item.quantity"></div>
                                        <div>
                                            <p class="text-lg font-bold text-gray-800 leading-tight" x-text="item.name"></p>
                                            <template x-if="item.notes && item.notes !== '-'">
                                                <p class="text-sm text-red-500 font-semibold mt-1 bg-red-50 px-2 py-0.5 rounded italic">Catatan: <span x-text="item.notes"></span></p>
                                            </template>
                                        </div>
                                    </li>
                                </template>
                            </ul>
                        </div>
                        
                        <!-- Actions Footer -->
                        <div class="p-4 bg-gray-50 border-t border-gray-300">
                            <!-- Tombol Jika Masih Baru -->
                            <template x-if="order.is_new">
                                <button @click="changeStatus(order.id, 'sedang diproses', order)" 
                                        class="w-full bg-green-500 hover:bg-green-600 text-white font-bold text-lg py-4 rounded-lg shadow uppercase tracking-wide flex items-center justify-center transition focus:outline-none">
                                    <i class="fas fa-fire mr-2"></i> Mulai Masak
                                </button>
                            </template>

                            <!-- Tombol Jika Sedang Diproses -->
                            <template x-if="!order.is_new">
                                <button @click="changeStatus(order.id, 'siap disajikan', order)" 
                                        class="w-full bg-yellow-400 hover:bg-yellow-500 text-gray-900 font-black text-lg py-4 rounded-lg shadow uppercase tracking-wide flex items-center justify-center transition focus:outline-none">
                                    <i class="fas fa-bell mr-2"></i> Selesai (Siap Antar)
                                </button>
                            </template>
                        </div>
                    </div>
                </template>
                
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('kdsApp', () => ({
                orders: [],
                isLoading: true,
                isOnline: true,
                currentTime: '00:00:00',
                pollingInterval: null,
                lastOrderIds: [],
                
                // Audio Element natively via JS
                notificationSound: new Audio('https://actions.google.com/sounds/v1/alarms/beep_short.ogg'), 

                init() {
                    this.updateClock();
                    setInterval(() => this.updateClock(), 1000);
                    
                    this.fetchOrders();
                    
                    // Polling data every 10 seconds (10000ms)
                    this.pollingInterval = setInterval(() => {
                        this.fetchOrders(true);
                    }, 10000);
                },
                
                updateClock() {
                    const now = new Date();
                    this.currentTime = now.toLocaleTimeString('id-ID', { hour12: false });
                },

                formatWaitTime(minutes) {
                    if (minutes < 1) return "Baru saja";
                    if (minutes === 1) return "1 mnt lalu";
                    return minutes + " mnt lalu";
                },

                async fetchOrders(isBackground = false) {
                    try {
                        const response = await fetch('/api/kds/orders', {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });
                        
                        if (!response.ok) throw new Error('Network error');
                        
                        const data = await response.json();
                        this.isOnline = true;
                        
                        // Check if there are completely NEW order IDs that we didn't have before
                        if (isBackground) {
                            const newOrderIds = data.map(o => o.id);
                            const hasArrivals = newOrderIds.some(id => !this.lastOrderIds.includes(id));
                            
                            if (hasArrivals) {
                                // Play short bip
                                this.notificationSound.play().catch(e => console.log('Audio autoplay blocked', e));
                            }
                            this.lastOrderIds = newOrderIds;
                        } else {
                            this.lastOrderIds = data.map(o => o.id);
                        }
                        
                        this.orders = data;
                        this.isLoading = false;
                        
                    } catch (error) {
                        console.error("Gagal mengambil data KDS:", error);
                        this.isOnline = false;
                        if (!isBackground) this.isLoading = false;
                    }
                },

                async changeStatus(orderId, nextStatus, orderObj) {
                    // Optimistic UI Update: Sematkan sementara style loading
                    const originalStatus = orderObj.status;
                    
                    try {
                        const response = await fetch(`/api/kds/update/${orderId}`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: JSON.stringify({ status: nextStatus })
                        });
                        
                        const result = await response.json();
                        if (result.success) {
                            if (nextStatus === 'siap disajikan' || nextStatus === 'sudah diantar') {
                                // If ready to deliver, remove it from the Chef's board completely
                                this.orders = this.orders.filter(o => o.id !== orderId);
                            } else {
                                // If moving to Sedang Diproses, sync it manually until next poll
                                this.fetchOrders(true); 
                            }
                        } else {
                            alert(result.message || "Gagal mengubah status");
                        }
                    } catch (error) {
                        alert("Koneksi bermasalah saat mengubah status.");
                    }
                }
            }));
        });
    </script>
    @endpush
</x-app-layout>
