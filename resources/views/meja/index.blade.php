<x-app-layout>
    <x-admin-sidebar />

    <main class="flex-1 lg:ml-64 p-4 lg:p-8 bg-gray-50">
          <!-- Header -->
            <div class="mb-8">
                <h1 class="text-2xl font-bold text-gray-800">Daftar Meja</h1>
                <p class="text-gray-600 mt-1">Kelola informasi meja kantin</p>
            </div>

        <div class="mb-6 flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <div class="relative">
                    <input type="text" id="searchInput" placeholder="Cari meja..."
                        class="w-64 pl-10 pr-4 py-2 border border-gray-400 rounded-lg focus:ring-2 focus:ring-orange-600 focus:border-orange-600 text-sm"
                        onkeyup="searchTable()">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-600"></i>
                    </div>
                </div>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('meja.printAll') }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-white text-orange-600 border border-orange-300 rounded-lg hover:bg-orange-50 transition-colors duration-150 font-bold shadow-sm">
                    <i class="fas fa-qrcode mr-2"></i>
                    <span>Cetak Semua QR</span>
                </a>
                <a href="{{ route('meja.create') }}" class="inline-flex items-center px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-600/90 transition-colors duration-150">
                    <i class="fas fa-plus mr-2"></i>
                    <span>Tambah Meja</span>
                </a>
            </div>
        </div>

        @if (session('success'))
            <div class="mb-8 bg-green-100 p-4 rounded-lg">
                <p class="text-sm text-green-700">{{ session('success') }}</p>
            </div>
        @endif

        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="p-6">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Nomor Meja</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">QR Code</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($mejas as $meja)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">{{ $meja->nomor_meja }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <img src="{{ asset('storage/' . $meja->qr_code) }}" alt="QR Code Meja {{ $meja->nomor_meja }}" class="h-20 w-20">
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex items-center space-x-3">
                                        <a href="{{ route('meja.print', $meja->id) }}" target="_blank" class="inline-flex items-center px-3 py-1.5 bg-orange-50 text-orange-700 hover:bg-orange-100 rounded-lg transition-colors border border-orange-200 font-bold shadow-sm">
                                            <i class="fas fa-print mr-1.5"></i> Cetak QR
                                        </a>
                                        <a href="{{ route('meja.edit', $meja->id) }}" class="text-blue-600 hover:text-blue-900 bg-blue-50 px-3 py-1.5 rounded-lg border border-transparent hover:border-blue-200 transition-colors">Edit</a>
                                        <form action="{{ route('meja.destroy', $meja->id) }}" method="POST" class="inline m-0" onsubmit="return confirm('Yakin ingin menghapus meja ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900 bg-red-50 px-3 py-1.5 rounded-lg border border-transparent hover:border-red-200 transition-colors">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-6 py-4 text-center text-gray-700">Tidak ada meja ditemukan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <!-- Pagination -->
            <div class="px-6 py-4 border-t">
                {{ $mejas->appends(request()->query())->links() }}
            </div>
        </div>

        <script>
            function searchTable() {
                const searchInput = document.getElementById('searchInput');
                const filter = searchInput.value.toLowerCase();
                const table = document.querySelector('table');
                const rows = table.getElementsByTagName('tr');

                for (let i = 1; i < rows.length; i++) { // Start from 1 to skip header row
                    const nomorMejaCell = rows[i].getElementsByTagName('td')[0]; // Index 0 is the Nomor Meja column

                    if (nomorMejaCell) {
                        const nomorMeja = nomorMejaCell.textContent || nomorMejaCell.innerText;

                        if (nomorMeja.toLowerCase().indexOf(filter) > -1) {
                            rows[i].style.display = '';
                        } else {
                            rows[i].style.display = 'none';
                        }
                    }
                }
            }
        </script>
    </main>
</x-app-layout>

