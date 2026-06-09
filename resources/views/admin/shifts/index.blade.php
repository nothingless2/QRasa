<x-app-layout>
    <x-admin-sidebar />

    <main class="flex-1 min-w-0 lg:ml-64 p-4 lg:p-8 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto">
            <div class="mb-6 flex flex-col md:flex-row md:items-end justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Laporan Rekap Laci Kasir (Shift)</h1>
                    <p class="text-sm text-gray-600 mt-1">Pantau dan kelola riwayat modal laci kasir harian.</p>
                </div>

                <div class="flex items-center space-x-3">
                    <form method="GET" class="w-full md:max-w-xs">
                        <div class="relative text-gray-600 focus-within:text-orange-600">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-search"></i>
                            </div>
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Nama Kasir atau Status..." class="block w-full pl-10 pr-3 py-2 border-gray-300 focus:outline-none focus:ring-orange-500 focus:border-orange-500 sm:text-sm rounded-lg shadow-sm transition-colors">
                        </div>
                    </form>
                    <a href="{{ route('shifts.export-csv') }}" class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors shadow-sm text-sm font-bold whitespace-nowrap">
                        <i class="fas fa-file-excel mr-2"></i> Export Excel
                    </a>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-300 overflow-hidden">
                <div class="p-4 md:p-6 overflow-x-auto">
                    <table class="w-full text-left border-collapse" id="shiftTable">
                        <thead>
                            <tr class="bg-gray-50 text-gray-700 border-b border-gray-300 text-sm">
                                <th class="p-4 font-semibold uppercase tracking-wider">Waktu Shift</th>
                                <th class="p-4 font-semibold uppercase tracking-wider">Kasir</th>
                                <th class="p-4 font-semibold uppercase tracking-wider">Modal Awal</th>
                                <th class="p-4 font-semibold uppercase tracking-wider">Pengeluaran</th>
                                <th class="p-4 font-semibold uppercase tracking-wider">Target Sistem</th>
                                <th class="p-4 font-semibold uppercase tracking-wider">Uang Fisik</th>
                                <th class="p-4 font-semibold uppercase tracking-wider text-center">Selisih</th>
                                <th class="p-4 font-semibold uppercase tracking-wider">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($shifts as $shift)
                                <tr class="hover:bg-orange-50/30 transition-colors">
                                    <td class="p-4 text-sm text-gray-700 whitespace-nowrap">
                                        <div class="font-medium text-gray-800">{{ $shift->shift_start->format('d/m/Y') }}</div>
                                        <div class="text-xs text-gray-700">{{ $shift->shift_start->format('H:i') }} - {{ $shift->shift_end ? $shift->shift_end->format('H:i') : 'Sekarang' }}</div>
                                    </td>
                                    <td class="p-4">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-8 h-8 rounded-full bg-orange-100 text-orange-600 flex items-center justify-center font-bold text-xs uppercase">
                                                {{ substr($shift->user->name, 0, 2) }}
                                            </div>
                                            <span class="font-semibold text-gray-800 whitespace-nowrap">{{ $shift->user->name }}</span>
                                        </div>
                                    </td>
                                    <td class="p-4 font-medium text-gray-700 whitespace-nowrap">Rp {{ number_format($shift->starting_cash, 0, ',', '.') }}</td>
                                    <td class="p-4 font-medium whitespace-nowrap">
                                        @if($shift->total_expenses > 0)
                                            <span class="text-red-600 font-bold" title="Kasbon/Pengeluaran selama shift">-Rp {{ number_format($shift->total_expenses, 0, ',', '.') }}</span>
                                        @else
                                            <span class="text-gray-600">Rp 0</span>
                                        @endif
                                    </td>
                                    <td class="p-4 font-medium text-blue-600 whitespace-nowrap">Rp {{ number_format($shift->ending_cash_expected ?? 0, 0, ',', '.') }}</td>
                                    <td class="p-4 font-medium text-gray-700 whitespace-nowrap">Rp {{ number_format($shift->ending_cash_actual ?? 0, 0, ',', '.') }}</td>
                                    <td class="p-4 font-bold whitespace-nowrap text-center">
                                        @if($shift->status === 'open')
                                            <span class="text-gray-600 font-normal italic">-</span>
                                        @else
                                            @if($shift->variance < 0)
                                                <span class="text-red-600 bg-red-50 px-3 py-1 rounded-lg border border-red-100 shadow-sm">-Rp {{ number_format(abs($shift->variance), 0, ',', '.') }}</span>
                                            @elseif($shift->variance > 0)
                                                <span class="text-green-600 px-3 py-1 bg-green-50 border border-green-100 rounded-lg whitespace-nowrap">+Rp {{ number_format($shift->variance, 0, ',', '.') }}</span>
                                            @else
                                                <span class="text-gray-700 bg-gray-50 border border-gray-300 px-3 py-1 rounded-lg">Pas (Rp 0)</span>
                                            @endif
                                        @endif
                                    </td>
                                    <td class="p-4 whitespace-nowrap">
                                        @if($shift->status === 'open')
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-green-100 text-green-800">
                                                Aktif
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-gray-100 text-gray-800 border border-gray-300">
                                                Ditutup
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            
                            @if($shifts->isEmpty())
                            <tr>
                                <td colspan="8" class="p-8 text-center text-gray-700 italic">Belum ada rekam jejak shift kasir yang tersimpan.</td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
                </div>
            </div>

            <!-- Pagination Links -->
            <div class="mt-6">
                {{ $shifts->appends(request()->query())->links() }}
            </div>
        </div>
    </main>
</x-app-layout>


