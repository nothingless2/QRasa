<x-app-layout>
    <div class="flex min-h-screen bg-gray-50">
        <x-admin-sidebar />
        <!-- Main Content -->
        <main class="flex-1 ml-64 p-8">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-2xl font-bold text-gray-800">
                    Daftar Menu
                </h1>
                <p class="text-gray-600 mt-1">Kelola menu kantin dengan mudah</p>
            </div>

            <!-- Action Buttons -->
            <div class="mb-6 flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="relative">
                        <input type="text" id="searchInput" placeholder="Cari menu..."
                            class="w-64 pl-10 pr-4 py-2 border border-gray-400 rounded-lg focus:ring-2 focus:ring-orange-600 focus:border-orange-600 text-sm"
                            onkeyup="searchTable()">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-600"></i>
                        </div>
                    </div>
                    <div>
                        <label for="kategori_filter" class="sr-only">Filter by Kategori</label>
                        <select id="kategori_filter" name="kategori_filter" onchange="window.location.href=this.value;"
                            class="block w-full pl-3 pr-10 py-2 text-base border-gray-400 focus:outline-none focus:ring-orange-600 focus:border-orange-600 sm:text-sm rounded-lg">
                            <option value="{{ route('menu.index') }}">Semua Kategori</option>
                            @foreach ($kategori as $kat)
                                <option value="{{ route('menu.index', ['kategori' => $kat]) }}" @if(request('kategori') == $kat) selected @endif>
                                    {{ $kat }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <a href="{{ route('menu.create') }}"
                    class="inline-flex items-center px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-600/90 transition-colors duration-150">
                    <i class="fas fa-plus mr-2"></i>
                    <span>Tambah Menu</span>
                </a>
            </div>

            <!-- Success Message -->
            @if (session('success'))
                <div class="mb-8 bg-green-100 p-4 rounded-lg">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-check-circle text-green-400"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-green-700">
                                {{ session('success') }}
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Menu Table -->
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="p-6">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr>
                            <th class="px-6 py-3  text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Gambar</th>
                            <th class="px-6 py-3  text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Nama Menu</th>
                            <th class="px-6 py-3  text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Deskripsi</th>
                            <th class="px-6 py-3  text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Harga</th>
                            <th class="px-6 py-3  text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Kategori</th>
                            <th class="px-6 py-3  text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Stok</th>
                            <th class="px-6 py-3  text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Diskon</th>
                            <th class="px-6 py-3  text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($menu as $item)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex-shrink-0 h-20 w-20">
                                    <img src="{{ Storage::url($item->gambar) }}" alt="{{ $item->nama }}"
                                         class="h-20 w-20 rounded object-cover" loading="lazy">
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">{{ $item->nama }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-700">
                                    {!! Str::limit($item->deskripsi, 100) !!}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">
                                    Rp{{ number_format($item->harga, 0, ',', '.') }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $item->kategori }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $item->stok }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $item->diskon }}%</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="{{ route('menu.edit', $item->id) }}"
                                   class="text-blue-600 hover:text-blue-900 mr-3">Edit</a>
                                <form action="{{ route('menu.destroy', $item->id) }}"
                                      method="POST"
                                      class="inline"
                                      onsubmit="return confirm('Yakin ingin menghapus menu ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="text-red-600 hover:text-red-900">
                                        Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <!-- Pagination -->
            <div class="px-6 py-4 border-t">
                {{ $menu->appends(request()->query())->links() }}
            </div>
    </div>

    <script>
        function searchTable() {
            const searchInput = document.getElementById('searchInput');
            const filter = searchInput.value.toLowerCase();
            const table = document.querySelector('table');
            const rows = table.getElementsByTagName('tr');

            for (let i = 1; i < rows.length; i++) { // Start from 1 to skip header row
                const nameCell = rows[i].getElementsByTagName('td')[1]; // Index 1 is the name column
                const descCell = rows[i].getElementsByTagName('td')[2]; // Index 2 is the description column
                const categoryCell = rows[i].getElementsByTagName('td')[4]; // Index 4 is the category column

                if (nameCell && descCell && categoryCell) {
                    const name = nameCell.textContent || nameCell.innerText;
                    const description = descCell.textContent || descCell.innerText;
                    const category = categoryCell.textContent || categoryCell.innerText;

                    if (name.toLowerCase().indexOf(filter) > -1 ||
                        description.toLowerCase().indexOf(filter) > -1 ||
                        category.toLowerCase().indexOf(filter) > -1) {
                        rows[i].style.display = '';
                    } else {
                        rows[i].style.display = 'none';
                    }
                }
            }
        }

    
    </script>


</x-app-layout>

