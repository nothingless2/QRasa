<x-app-layout>
    <x-admin-sidebar />

    <!-- Main Content -->
    <main class="flex-1 min-w-0 lg:ml-64 p-4 lg:p-8 bg-gray-50">
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Dashboard Penjualan</h1>
                <p class="text-gray-600 mt-1">Ringkasan dan statistik aplikasi Anda.</p>
            </div>
            <form action="{{ route('dashboard') }}" method="GET" class="flex space-x-3 items-center">
                <select name="period" onchange="this.form.submit()" class="block w-40 rounded-md border-gray-400 shadow-sm focus:border-orange-300 focus:ring focus:ring-orange-200 focus:ring-opacity-50">
                    <option value="today" @if($period == 'today') selected @endif>Hari Ini</option>
                    <option value="this_week" @if($period == 'this_week') selected @endif>Minggu Ini</option>
                    <option value="this_month" @if($period == 'this_month') selected @endif>Bulan Ini</option>
                </select>
                <!-- Tombol Export PDF -->
                <a href="{{ route('dashboard.export-pdf', ['period' => $period]) }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors">
                    <i class="fas fa-file-pdf mr-2"></i> Export PDF
                </a>
                <!-- Tombol Export CSV -->
                <a href="{{ route('dashboard.export-csv', ['period' => $period]) }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors">
                    <i class="fas fa-file-excel mr-2"></i> Export Excel
                </a>
            </form>
        </div>

        <!-- KPI Cards -->
        <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
            <!-- Total Penjualan (Bruto) -->
            <div class="bg-white rounded-xl shadow-sm p-5 flex flex-col justify-center">
                <div class="flex items-center space-x-3 mb-2">
                    <div class="bg-green-100 text-green-600 rounded-lg p-2"><i class="fas fa-wallet"></i></div>
                    <p class="text-gray-700 text-xs font-bold uppercase tracking-wider">Total (Bruto)</p>
                </div>
                <h2 class="text-xl font-bold text-gray-800">Rp{{ number_format($revenue, 0, ',', '.') }}</h2>
            </div>

            <!-- Pendapatan Bersih (Subtotal) -->
            <div class="bg-white rounded-xl shadow-sm p-5 flex flex-col justify-center border-l-4 border-blue-500">
                <div class="flex items-center space-x-3 mb-2">
                    <div class="bg-blue-100 text-blue-600 rounded-lg p-2"><i class="fas fa-chart-line"></i></div>
                    <p class="text-gray-700 text-xs font-bold uppercase tracking-wider">Netto (Bersih)</p>
                </div>
                <h2 class="text-xl font-bold text-blue-700">Rp{{ number_format($totalSubtotal, 0, ',', '.') }}</h2>
            </div>

            <!-- Pajak PB1 Terkumpul -->
            <div class="bg-white rounded-xl shadow-sm p-5 flex flex-col justify-center border-l-4 border-red-500">
                <div class="flex items-center space-x-3 mb-2">
                    <div class="bg-red-100 text-red-600 rounded-lg p-2"><i class="fas fa-landmark"></i></div>
                    <p class="text-gray-700 text-xs font-bold uppercase tracking-wider">Pajak PB1 (11%)</p>
                </div>
                <h2 class="text-xl font-bold text-red-700">Rp{{ number_format($totalPb1, 0, ',', '.') }}</h2>
            </div>

            <!-- Service Charge Terkumpul -->
            <div class="bg-white rounded-xl shadow-sm p-5 flex flex-col justify-center border-l-4 border-orange-500">
                <div class="flex items-center space-x-3 mb-2">
                    <div class="bg-orange-100 text-orange-600 rounded-lg p-2"><i class="fas fa-hand-holding-usd"></i></div>
                    <p class="text-gray-700 text-xs font-bold uppercase tracking-wider">Servis (5%)</p>
                </div>
                <h2 class="text-xl font-bold text-orange-700">Rp{{ number_format($totalService, 0, ',', '.') }}</h2>
            </div>
            
            <!-- Jumlah Pesanan -->
            <div class="bg-gray-800 rounded-xl shadow-sm p-5 flex flex-col justify-center">
                <div class="flex items-center space-x-3 mb-2">
                    <div class="bg-gray-700 text-gray-300 rounded-lg p-2"><i class="fas fa-shopping-cart"></i></div>
                    <p class="text-gray-600 text-xs font-bold uppercase tracking-wider">Total TRX</p>
                </div>
                <h2 class="text-xl font-bold text-white">{{ number_format($orders, 0, ',', '.') }} Bon</h2>
            </div>
        </div>

        

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6 text-gray-900">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold">Grafik Penjualan</h3>
                    <form action="{{ route('dashboard') }}" method="GET">
                        <select name="chart_period" onchange="this.form.submit()" class="block w-full rounded-md border-gray-400 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            <option value="this_month" @if($chartPeriod == 'this_month') selected @endif>Bulan Ini</option>
                            <option value="this_year" @if($chartPeriod == 'this_year') selected @endif>Tahun Ini</option>
                            <option value="last_7_days" @if($chartPeriod == 'last_7_days') selected @endif>7 Hari Terakhir</option>
                            <option value="last_30_days" @if($chartPeriod == 'last_30_days') selected @endif>30 Hari Terakhir</option>
                        </select>
                    </form>
                </div>
                <canvas id="salesChart"></canvas>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Kolom Kiri: Top Selling & Stok -->
            <div class="space-y-6">
                <!-- Top Selling Menus -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 border-b border-gray-100">
                        <h3 class="text-lg font-bold text-gray-800 flex items-center"><i class="fas fa-trophy text-orange-500 mr-2"></i> Menu Terlaris (30 Hari)</h3>
                    </div>
                    <div class="p-6">
                        @if ($topSellingMenus->isEmpty())
                            <p class="text-gray-700 text-sm italic">Belum ada data penjualan.</p>
                        @else
                            <ul class="divide-y divide-gray-100">
                                @foreach ($topSellingMenus as $index => $ts)
                                    <li class="py-3 flex justify-between items-center">
                                        <div class="flex items-center">
                                            <span class="text-sm font-bold text-gray-600 w-6">{{ $index + 1 }}.</span>
                                            <span class="text-gray-800 font-medium">{{ $ts->nama }}</span>
                                        </div>
                                        <span class="bg-orange-100 text-orange-700 text-xs px-2 py-1 rounded-full font-bold">{{ $ts->total_quantity }} Terjual</span>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>

                <!-- Peringatan Stok Tipis -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 border-b border-gray-100">
                        <h3 class="text-lg font-bold text-gray-800 flex items-center"><i class="fas fa-exclamation-triangle text-red-500 mr-2"></i> Peringatan Stok Tipis</h3>
                    </div>
                    <div class="p-6">
                        @if ($adminLowStockMenus->isEmpty())
                            <p class="text-gray-700 text-sm italic">Semua stok aman terpantau.</p>
                        @else
                            <ul class="divide-y divide-gray-100">
                                @foreach ($adminLowStockMenus as $ls)
                                    <li class="py-3 flex justify-between items-center">
                                        <span class="text-gray-800 font-medium">{{ $ls->nama }}</span>
                                        <span class="text-red-600 font-bold text-sm">{{ $ls->stok }} Porsi Tersisa</span>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Kolom Kanan: Recent Orders -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 border-b border-gray-100 flex justify-between items-center">
                    <h3 class="text-lg font-bold text-gray-800 flex items-center"><i class="fas fa-clock text-blue-500 mr-2"></i> Pesanan Terbaru & Belum Bayar</h3>
                    <a href="{{ route('pesan.index') }}" class="text-sm text-orange-600 hover:text-orange-800 font-medium">Lihat Semua &rarr;</a>
                </div>
                <div class="p-6">
                    @if ($pesanans->isEmpty())
                        <p class="text-gray-700 text-sm italic">Belum ada pesanan masuk.</p>
                    @else
                        <div class="space-y-4">
                            @foreach ($pesanans as $order)
                                <div class="border border-gray-100 rounded-lg p-4 hover:bg-gray-50 transition">
                                    <div class="flex justify-between items-start mb-2">
                                        <div>
                                            <span class="font-bold text-gray-800 text-sm">{{ $order->meja ? 'Meja ' . $order->meja->nomor_meja : 'Bawa Pulang (Takeaway)' }}</span>
                                            <p class="text-xs text-gray-700">{{ $order->created_at->diffForHumans() }}</p>
                                        </div>
                                        <div>
                                            <span class="px-2 py-1 text-[10px] font-bold rounded-full 
                                            {{ $order->status_pembayaran == 'sudah dibayar' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                                {{ strtoupper($order->status_pembayaran ?? 'BELUM BAYAR') }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="flex justify-between items-end mt-4">
                                        <p class="text-sm text-gray-600 truncate max-w-[200px]" title="{{ $order->menus->pluck('nama')->implode(', ') }}">
                                            {{ $order->menus->pluck('nama')->implode(', ') }}
                                        </p>
                                        <p class="font-bold text-gray-900 text-sm">Rp{{ number_format($order->total, 0, ',', '.') }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </main>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const ctx = document.getElementById('salesChart');
                if (ctx) {
                    new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: @json($chartData['labels']),
                            datasets: [{
                                label: 'Total Penjualan',
                                data: @json($chartData['data']),
                                backgroundColor: 'rgba(54, 162, 235, 0.5)',
                                borderColor: 'rgba(54, 162, 235, 1)',
                                borderWidth: 1,
                                fill: true
                            }]
                        },
                        options: {
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        callback: function(value) {
                                            return 'Rp' + new Intl.NumberFormat('id-ID').format(value);
                                        }
                                    }
                                }
                            }
                        }
                    });
                }
            });
        </script>
    @endpush
</x-app-layout>


