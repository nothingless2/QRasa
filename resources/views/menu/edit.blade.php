<x-app-layout>
    <div class="flex min-h-screen bg-gray-50">
        <x-admin-sidebar />

        <!-- Main Content -->
        <main class="flex-1 md:ml-64 p-4 md:p-8">
            <!-- Header -->
            <div class="mb-8">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">Edit Menu</h1>
                        <p class="text-gray-600 mt-1">Perbarui informasi menu di bawah ini</p>
                    </div>
                    <div class="flex items-center space-x-2">
                        <a href="{{ route('menu.index') }}"
                            class="inline-flex items-center px-3 py-2 border border-gray-400 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-600">
                            <i class="fas fa-arrow-left mr-2"></i>
                            Kembali
                        </a>
                    </div>
                </div>
            </div>

            <!-- Form Card -->
            <div class="bg-white rounded-xl shadow-sm overflow-hidden max-w-4xl">
                <form action="{{ route('menu.update', $menu->id) }}" method="POST" enctype="multipart/form-data" class="p-6">
                    @csrf
                    @method('PUT')

                    <!-- Image Preview -->
                    <div class="w-full mb-6">
                        <div class="relative aspect-video rounded-lg overflow-hidden bg-gray-100 mb-4">
                            <img id="image-preview" src="{{ Storage::url($menu->gambar) }}" alt="Preview"
                                class="w-full h-full object-cover">
                            <div id="image-placeholder"
                                class="absolute inset-0 items-center justify-center text-gray-600 hidden">
                                <i class="fas fa-image text-4xl"></i>
                            </div>
                        </div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Gambar Menu
                        </label>
                        <input type="file" name="gambar" id="gambar" accept="image/*"
                            class="w-full px-3 py-2 border border-gray-400 rounded-lg focus:ring-2 focus:ring-orange-600 focus:border-orange-600 text-sm"
                            onchange="previewImage(this)">
                        @error('gambar')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Nama Menu -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nama Menu</label>
                        <input type="text" name="nama" value="{{ old('nama', $menu->nama) }}"
                            class="w-full px-3 py-2 border border-gray-400 rounded-lg focus:ring-2 focus:ring-orange-600 focus:border-orange-600">
                        @error('nama')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Deskripsi -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Deskripsi</label>
                        <textarea name="deskripsi" id="editor" rows="3"
                            class="w-full px-3 py-2 border border-gray-400 rounded-lg focus:ring-2 focus:ring-orange-600 focus:border-orange-600">{{ old('deskripsi', $menu->deskripsi) }}</textarea>
                        @error('deskripsi')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Harga dan Stok -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Harga</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-700">Rp</span>
                                <input type="number" name="harga" value="{{ old('harga', $menu->harga) }}" min="0"
                                    class="w-full pl-10 pr-3 py-2 border border-gray-400 rounded-lg focus:ring-2 focus:ring-orange-600 focus:border-orange-600">
                            </div>
                            @error('harga')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Stok</label>
                            <input type="number" name="stok" value="{{ old('stok', $menu->stok) }}" min="0"
                                class="w-full px-3 py-2 border border-gray-400 rounded-lg focus:ring-2 focus:ring-orange-600 focus:border-orange-600">
                            @error('stok')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Kategori dan Diskon -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Kategori</label>
                            <select name="kategori"
                                class="w-full px-3 py-2 border border-gray-400 rounded-lg focus:ring-2 focus:ring-orange-600 focus:border-orange-600">
                                <option value="Menu Baru" {{ old('kategori', $menu->kategori) == 'Menu Baru' ? 'selected' : '' }}>Menu Baru</option>
                                <option value="Paket Hemat" {{ old('kategori', $menu->kategori) == 'Paket Hemat' ? 'selected' : '' }}>Paket Hemat</option>
                                <option value="Makanan" {{ old('kategori', $menu->kategori) == 'Makanan' ? 'selected' : '' }}>Makanan</option>
                                <option value="Minuman" {{ old('kategori', $menu->kategori) == 'Minuman' ? 'selected' : '' }}>Minuman</option>
                                <option value="Snack" {{ old('kategori', $menu->kategori) == 'Snack' ? 'selected' : '' }}>Snack</option>
                            </select>
                            @error('kategori')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Diskon (%)</label>
                            <input type="number" name="diskon" value="{{ old('diskon', $menu->diskon) }}" min="0" max="100"
                                class="w-full px-3 py-2 border border-gray-400 rounded-lg focus:ring-2 focus:ring-orange-600 focus:border-orange-600">
                            @error('diskon')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    

                    <!-- Submit Buttons -->
                    <div class="flex justify-end space-x-3 pt-6">
                        <a href="{{ route('menu.index') }}"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors duration-150">
                            Batal
                        </a>
                        <button type="submit"
                            class="px-4 py-2 text-sm font-medium text-white bg-orange-600 hover:bg-orange-600/90 rounded-lg transition-colors duration-150">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </main>
    </div>

    <script>
        function previewImage(input) {
            const preview = document.getElementById('image-preview');
            const placeholder = document.getElementById('image-placeholder');

            if (input.files && input.files[0]) {
                const reader = new FileReader();

                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.classList.remove('hidden');
                    placeholder.classList.add('hidden');
                }

                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
</x-app-layout>
