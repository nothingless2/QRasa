<x-app-layout>
    <x-admin-sidebar />

    <main class="flex-1 lg:ml-64 p-4 lg:p-8 bg-gray-50 min-h-screen">
        <div class="max-w-4xl mx-auto">
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-800 tracking-tight">Pengaturan Sistem Dasar</h1>
                <p class="text-sm text-gray-700 mt-2">Atur identitas restoran, pajak operasional, serta konten halaman depan di satu tempat.</p>
            </div>

            @if(session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-r-lg shadow-sm" role="alert">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle mr-3"></i>
                        <p class="font-medium">{{ session('success') }}</p>
                    </div>
                </div>
            @endif

            @if ($errors->any())
                <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded-r-lg shadow-sm">
                    <div class="flex items-center mb-2">
                        <i class="fas fa-exclamation-triangle text-red-500 mr-2"></i>
                        <h3 class="text-red-800 font-bold">Terjadi Kesalahan Validasi</h3>
                    </div>
                    <ul class="list-disc list-inside text-sm text-red-700">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('settings.update') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
                @csrf
                @method('PATCH')

                <!-- Seksi 1: Identitas Toko & Transaksi -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="bg-gray-50 px-6 py-4 border-b border-gray-100 flex items-center">
                        <div class="bg-orange-100 p-2 rounded-lg text-orange-600 mr-3">
                            <i class="fas fa-store"></i>
                        </div>
                        <h2 class="text-lg font-bold text-gray-800">Identitas Toko & Kasir</h2>
                    </div>
                    <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Nama Toko -->
                        <div class="col-span-1 md:col-span-2">
                            <label for="store_name" class="block text-sm font-bold text-gray-700 mb-2">Nama Resto / Usaha</label>
                            <input type="text" name="store_name" id="store_name" value="{{ old('store_name', $setting->store_name) }}" class="w-full border-gray-400 rounded-lg focus:ring-orange-500 focus:border-orange-500 transition-shadow shadow-sm" required>
                        </div>
                        
                        <!-- Alamat -->
                        <div class="col-span-1 md:col-span-2">
                            <label for="store_address" class="block text-sm font-bold text-gray-700 mb-2">Alamat Fisik (Kop Struk)</label>
                            <textarea name="store_address" id="store_address" rows="3" class="w-full border-gray-400 rounded-lg focus:ring-orange-500 focus:border-orange-500 transition-shadow shadow-sm">{{ old('store_address', $setting->store_address) }}</textarea>
                        </div>

                        <!-- Pesan Bawah Struk -->
                        <div class="col-span-1 md:col-span-2">
                            <label for="receipt_footer" class="block text-sm font-bold text-gray-700 mb-2">Catatan Kaki Struk (Footer)</label>
                            <textarea name="receipt_footer" id="receipt_footer" rows="2" class="w-full border-gray-400 rounded-lg focus:ring-orange-500 focus:border-orange-500 transition-shadow shadow-sm" required>{{ old('receipt_footer', $setting->receipt_footer) }}</textarea>
                            <p class="text-xs text-gray-600 mt-1">Dicetak di bagian paling bawah kertas kasir thermal.</p>
                        </div>

                        <!-- Pajak PB1 -->
                        <div>
                            <label for="tax_percent" class="block text-sm font-bold text-gray-700 mb-2">Pajak PB1 / PPN (%)</label>
                            <div class="relative">
                                <input type="number" name="tax_percent" id="tax_percent" min="0" max="100" value="{{ old('tax_percent', $setting->tax_percent) }}" class="w-full pl-4 pr-12 border-gray-400 rounded-lg focus:ring-orange-500 focus:border-orange-500 transition-shadow shadow-sm font-bold text-red-600" required>
                                <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                                    <span class="text-gray-700 font-bold">%</span>
                                </div>
                            </div>
                        </div>

                        <!-- Service Charge -->
                        <div>
                            <label for="service_percent" class="block text-sm font-bold text-gray-700 mb-2">Service Charge (%)</label>
                            <div class="relative">
                                <input type="number" name="service_percent" id="service_percent" min="0" max="100" value="{{ old('service_percent', $setting->service_percent) }}" class="w-full pl-4 pr-12 border-gray-400 rounded-lg focus:ring-orange-500 focus:border-orange-500 transition-shadow shadow-sm font-bold text-blue-600" required>
                                <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                                    <span class="text-gray-700 font-bold">%</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Seksi 2: Konten Halaman Depan -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="bg-gray-50 px-6 py-4 border-b border-gray-100 flex items-center">
                        <div class="bg-orange-100 p-2 rounded-lg text-orange-600 mr-3">
                            <i class="fas fa-globe"></i>
                        </div>
                        <h2 class="text-lg font-bold text-gray-800">Tampilan Halaman Depan (Landing Page)</h2>
                    </div>
                    <div class="p-6 grid grid-cols-1 gap-6">
                        
                        <!-- Upload Logo -->
                        <div class="border-2 border-dashed border-gray-400 rounded-xl p-6 flex flex-col sm:flex-row items-center justify-between hover:bg-gray-50 transition-colors">
                            <div class="flex items-center mb-4 sm:mb-0">
                                @if($setting->logo_path)
                                    <img src="{{ Storage::url($setting->logo_path) }}" alt="Logo Saat ini" class="h-16 w-16 object-contain rounded-lg bg-white border shadow-sm mr-4">
                                @else
                                    <div class="h-16 w-16 bg-gray-100 rounded-lg border border-gray-300 flex items-center justify-center text-gray-600 mr-4 shadow-sm">
                                        <i class="fas fa-image text-2xl"></i>
                                    </div>
                                @endif
                                <div>
                                    <label class="block text-sm font-bold text-gray-800 mb-1">Upload Logo Restoran Baru</label>
                                    <p class="text-xs text-gray-700">Akan menggantikan teks nama toko di Pojok Kiri Atas halaman pemesanan.</p>
                                </div>
                            </div>
                            <div class="flex-shrink-0">
                                <label class="cursor-pointer bg-white border border-gray-400 text-gray-700 hover:bg-gray-50 font-medium py-2 px-4 rounded-lg shadow-sm transition">
                                    <span>Pilih Gambar</span>
                                    <input type="file" name="logo" class="hidden" accept="image/png, image/jpeg, image/webp">
                                </label>
                            </div>
                        </div>

                        <!-- Upload Hero Background -->
                        <div class="border-2 border-dashed border-gray-400 rounded-xl p-6 flex flex-col sm:flex-row items-center justify-between hover:bg-gray-50 transition-colors">
                            <div class="flex items-center mb-4 sm:mb-0">
                                @if($setting->hero_bg)
                                    <img src="{{ Storage::url($setting->hero_bg) }}" alt="Hero Background Saat ini" class="h-16 w-24 object-cover rounded-lg bg-gray-200 border shadow-sm mr-4">
                                @else
                                    <div class="h-16 w-24 bg-gray-100 rounded-lg border border-gray-300 flex items-center justify-center text-gray-600 mr-4 shadow-sm">
                                        <i class="fas fa-image text-2xl"></i>
                                    </div>
                                @endif
                                <div>
                                    <label class="block text-sm font-bold text-gray-800 mb-1">Background Hero Section</label>
                                    <p class="text-xs text-gray-700">Gambar besar di belakangk halaman utama. Kosongkan untuk bawaan sistem.</p>
                                </div>
                            </div>
                            <div class="flex-shrink-0">
                                <label class="cursor-pointer bg-white border border-gray-400 text-gray-700 hover:bg-gray-50 font-medium py-2 px-4 rounded-lg shadow-sm transition">
                                    <span>Pilih Gambar</span>
                                    <input type="file" name="hero_bg" class="hidden" accept="image/png, image/jpeg, image/webp">
                                </label>
                            </div>
                        </div>

                        <!-- Welcome Title -->
                        <div>
                            <label for="welcome_title" class="block text-sm font-bold text-gray-700 mb-2">Slogan Besar (Hero Title)</label>
                            <input type="text" name="welcome_title" id="welcome_title" value="{{ old('welcome_title', $setting->welcome_title) }}" class="w-full border-gray-400 rounded-lg focus:ring-orange-500 focus:border-orange-500 transition-shadow shadow-sm font-bold" required>
                        </div>
                        
                        <!-- Welcome Subtitle -->
                        <div>
                            <label for="welcome_subtitle" class="block text-sm font-bold text-gray-700 mb-2">Deskripsi Layanan (Hero Subtitle)</label>
                            <textarea name="welcome_subtitle" id="welcome_subtitle" rows="3" class="w-full border-gray-400 rounded-lg focus:ring-orange-500 focus:border-orange-500 transition-shadow shadow-sm">{{ old('welcome_subtitle', $setting->welcome_subtitle) }}</textarea>
                        </div>
                        
                        <!-- Menu Count -->
                        <div>
                            <label for="featured_menu_count" class="block text-sm font-bold text-gray-700 mb-2">Jumlah "Menu Spesial" di Beranda</label>
                            <input type="number" name="featured_menu_count" id="featured_menu_count" min="1" max="15" value="{{ old('featured_menu_count', $setting->featured_menu_count ?? 3) }}" class="w-full border-gray-400 rounded-lg focus:ring-orange-500 focus:border-orange-500 transition-shadow shadow-sm" required>
                            <p class="text-xs text-gray-700 mt-2">Berapa banyak kotak menu yang ingin ditampilkan di halaman depan (Saran: kelipatan 3).</p>
                        </div>

                        <!-- Welcome Footer -->
                        <div>
                            <label for="welcome_footer" class="block text-sm font-bold text-gray-700 mb-2">Teks Copyright (Bawah Halaman)</label>
                            <input type="text" name="welcome_footer" id="welcome_footer" value="{{ old('welcome_footer', $setting->welcome_footer) }}" class="w-full border-gray-400 rounded-lg focus:ring-orange-500 focus:border-orange-500 transition-shadow shadow-sm" required>
                        </div>

                    </div>
                </div>

                <!-- Seksi 3: Tentang Kami -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mt-8">
                    <div class="bg-gray-50 px-6 py-4 border-b border-gray-100 flex items-center">
                        <div class="bg-stone-100 p-2 rounded-lg text-stone-600 mr-3">
                            <i class="fas fa-info-circle"></i>
                        </div>
                        <h2 class="text-lg font-bold text-gray-800">Bagian "Tentang Kami"</h2>
                    </div>
                    <div class="p-6 grid grid-cols-1 gap-6">
                        <!-- Upload About Image -->
                        <div class="border-2 border-dashed border-gray-400 rounded-xl p-6 flex flex-col sm:flex-row items-center justify-between hover:bg-gray-50 transition-colors">
                            <div class="flex items-center mb-4 sm:mb-0">
                                @if($setting->about_image)
                                    <img src="{{ Storage::url($setting->about_image) }}" alt="Foto Tentang Kami Saat ini" class="h-16 w-16 object-cover rounded-lg bg-gray-200 border shadow-sm mr-4">
                                @else
                                    <div class="h-16 w-16 bg-gray-100 rounded-lg border border-gray-300 flex items-center justify-center text-gray-600 mr-4 shadow-sm">
                                        <i class="fas fa-camera text-2xl"></i>
                                    </div>
                                @endif
                                <div>
                                    <label class="block text-sm font-bold text-gray-800 mb-1">Foto Samping "Tentang Kami"</label>
                                    <p class="text-xs text-gray-700">Kosongkan jika tidak ingin mengubah.</p>
                                </div>
                            </div>
                            <div class="flex-shrink-0">
                                <label class="cursor-pointer bg-white border border-gray-400 text-gray-700 hover:bg-gray-50 font-medium py-2 px-4 rounded-lg shadow-sm transition">
                                    <span>Pilih Gambar</span>
                                    <input type="file" name="about_image" class="hidden" accept="image/png, image/jpeg, image/webp">
                                </label>
                            </div>
                        </div>
                        <div>
                            <label for="about_title" class="block text-sm font-bold text-gray-700 mb-2">Judul "Tentang Kami"</label>
                            <input type="text" name="about_title" id="about_title" placeholder="Kesan Pertama yang Menggoda" value="{{ old('about_title', $setting->about_title) }}" class="w-full border-gray-400 rounded-lg focus:ring-orange-500 focus:border-orange-500 transition-shadow shadow-sm font-bold">
                        </div>
                        <div>
                            <label for="about_text" class="block text-sm font-bold text-gray-700 mb-2">Deskripsi "Tentang Kami"</label>
                            <textarea name="about_text" id="about_text" rows="4" class="w-full border-gray-400 rounded-lg focus:ring-orange-500 focus:border-orange-500 transition-shadow shadow-sm">{{ old('about_text', $setting->about_text) }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Seksi 4: Kontak & Jam Operasional -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mt-8">
                    <div class="bg-gray-50 px-6 py-4 border-b border-gray-100 flex items-center">
                        <div class="bg-green-100 p-2 rounded-lg text-green-600 mr-3">
                            <i class="fas fa-map-marked-alt"></i>
                        </div>
                        <h2 class="text-lg font-bold text-gray-800">Kontak & Jam Operasional</h2>
                    </div>
                    <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Jam Buka (3 Baris Input) -->
                        <div class="col-span-1 md:col-span-2">
                            <label class="block text-sm font-bold text-gray-700 mb-2">Jam Buka</label>
                            @php
                                $hours = is_array($setting->operational_hours) ? $setting->operational_hours : [];
                            @endphp
                            <div class="space-y-3">
                                <div class="flex gap-3">
                                    <input type="text" name="operational_hours[0][day]" value="{{ $hours[0]['day'] ?? 'Senin - Kamis' }}" class="w-1/2 border-gray-400 rounded-lg text-sm" placeholder="Hari">
                                    <input type="text" name="operational_hours[0][time]" value="{{ $hours[0]['time'] ?? '10:00 - 22:00' }}" class="w-1/2 border-gray-400 rounded-lg text-sm" placeholder="Jam">
                                </div>
                                <div class="flex gap-3">
                                    <input type="text" name="operational_hours[1][day]" value="{{ $hours[1]['day'] ?? 'Jumat' }}" class="w-1/2 border-gray-400 rounded-lg text-sm" placeholder="Hari">
                                    <input type="text" name="operational_hours[1][time]" value="{{ $hours[1]['time'] ?? '13:00 - 23:00' }}" class="w-1/2 border-gray-400 rounded-lg text-sm" placeholder="Jam">
                                </div>
                                <div class="flex gap-3">
                                    <input type="text" name="operational_hours[2][day]" value="{{ $hours[2]['day'] ?? 'Sabtu - Minggu' }}" class="w-1/2 border-gray-400 rounded-lg text-sm" placeholder="Hari">
                                    <input type="text" name="operational_hours[2][time]" value="{{ $hours[2]['time'] ?? '08:00 - 23:30' }}" class="w-1/2 border-gray-400 rounded-lg text-sm" placeholder="Jam">
                                </div>
                            </div>
                        </div>

                        <!-- Data Kontak -->
                        <div>
                            <label for="contact_whatsapp" class="block text-sm font-bold text-gray-700 mb-2">Nomor WhatsApp</label>
                            <input type="text" name="contact_whatsapp" id="contact_whatsapp" placeholder="6282255557777" value="{{ old('contact_whatsapp', $setting->contact_whatsapp) }}" class="w-full border-gray-400 rounded-lg transition-shadow shadow-sm">
                        </div>
                        <div>
                            <label for="contact_instagram" class="block text-sm font-bold text-gray-700 mb-2">Username Instagram</label>
                            <input type="text" name="contact_instagram" id="contact_instagram" placeholder="@qrasa.cafe" value="{{ old('contact_instagram', $setting->contact_instagram) }}" class="w-full border-gray-400 rounded-lg transition-shadow shadow-sm">
                        </div>

                        <!-- Map Iframe -->
                        <div class="col-span-1 md:col-span-2">
                            <label for="map_iframe" class="block text-sm font-bold text-gray-700 mb-2">Google Maps Iframe HTML</label>
                            <textarea name="map_iframe" id="map_iframe" rows="3" class="w-full border-gray-400 rounded-lg transition-shadow shadow-sm font-mono text-xs">{{ old('map_iframe', $setting->map_iframe) }}</textarea>
                            <p class="text-xs text-stone-500 mt-1">Copy "Embed a map" HTML frame dari Google maps.</p>
                        </div>
                    </div>
                </div>

                <!-- Seksi 5: Integrasi Data & Marketing -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mt-8">
                    <div class="bg-gray-50 px-6 py-4 border-b border-gray-100 flex items-center">
                        <div class="bg-blue-100 p-2 rounded-lg text-blue-600 mr-3">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <h2 class="text-lg font-bold text-gray-800">Integrasi Analytics & Marketing</h2>
                    </div>
                    <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Google Analytics ID -->
                        <div>
                            <label for="google_analytics_id" class="block text-sm font-bold text-gray-700 mb-2">Google Analytics Measurement ID</label>
                            <input type="text" name="google_analytics_id" id="google_analytics_id" placeholder="G-XXXXXXXXXX" value="{{ old('google_analytics_id', $setting->google_analytics_id ?? '') }}" class="w-full border-gray-400 rounded-lg focus:ring-blue-500 focus:border-blue-500 transition-shadow shadow-sm font-mono text-sm">
                            <p class="text-xs text-gray-700 mt-2">Digunakan untuk melacak jumlah pengunjung website secara detail. Kosongkan jika tidak ada.</p>
                        </div>
                        
                        <!-- Facebook Pixel ID -->
                        <div>
                            <label for="facebook_pixel_id" class="block text-sm font-bold text-gray-700 mb-2">Facebook / Meta Pixel ID</label>
                            <input type="text" name="facebook_pixel_id" id="facebook_pixel_id" placeholder="1234567890123456" value="{{ old('facebook_pixel_id', $setting->facebook_pixel_id ?? '') }}" class="w-full border-gray-400 rounded-lg focus:ring-blue-500 focus:border-blue-500 transition-shadow shadow-sm font-mono text-sm">
                            <p class="text-xs text-gray-700 mt-2">Digunakan untuk retargeting ads Instagram & Facebook. Kosongkan jika tidak ada.</p>
                        </div>
                    </div>
                </div>

                <!-- Submit Action -->
                <div class="flex justify-end pt-4 pb-12">
                    <button type="submit" class="bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white font-bold py-3 px-8 rounded-xl shadow-md transition-all transform hover:scale-105 flex items-center">
                        <i class="fas fa-save mr-2"></i> Simpan Semua Pengaturan
                    </button>
                </div>
            </form>
        </div>
    </main>
</x-app-layout>

