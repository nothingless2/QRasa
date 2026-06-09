<x-app-layout>
    <div class="flex min-h-screen bg-gray-50">
        <x-admin-sidebar />
        <main class="flex-1 min-w-0 lg:ml-64 p-4 lg:p-8">
            <!-- Header -->
            <div class="flex justify-between items-center mb-6">
                <div class="mb-8">
                    <h1 class="text-2xl font-bold text-gray-800">
                        Daftar Pesanan Anda
                    </h1>
                    <p class="text-gray-600 mt-1">Kelola pesanan anda dengan mudah</p>
                </div>

                <form action="{{ route('pesan.index') }}" method="GET" class="mb-4 flex flex-wrap gap-4 items-end bg-white p-4 rounded-xl shadow-sm border border-gray-100">
                    <div class="flex-1 min-w-[200px]">
                        <label for="search" class="block text-sm font-bold text-gray-700 mb-1">Pencarian:</label>
                        <div class="relative text-gray-600 focus-within:text-orange-600">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-search"></i>
                            </div>
                            <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Ketik ID Pesanan, No. Meja, Kasir..." class="block w-full pl-10 pr-3 py-2 border-gray-300 focus:outline-none focus:ring-orange-500 focus:border-orange-500 sm:text-sm rounded-lg transition-colors">
                        </div>
                    </div>
                    <div class="w-full sm:w-auto">
                        <label for="status" class="block text-sm font-bold text-gray-700 mb-1">Filter Status:</label>
                        <select name="status" id="status" onchange="this.form.submit()"
                            class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-orange-500 focus:border-orange-500 sm:text-sm rounded-lg hover:bg-gray-50 cursor-pointer">
                            <option value="">Semuanya</option>
                            @foreach ($statuses as $statusOption)
                                <option value="{{ $statusOption }}"
                                    {{ request('status') == $statusOption ? 'selected' : '' }}>
                                    {{ ucfirst($statusOption) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="w-full sm:w-auto flex space-x-2">
                        <div class="flex-1">
                            <label for="payment_method" class="block text-sm font-bold text-gray-700 mb-1">Pembayaran:</label>
                            <select name="payment_method" id="payment_method" onchange="this.form.submit()"
                                class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-orange-500 focus:border-orange-500 sm:text-sm rounded-lg hover:bg-gray-50 cursor-pointer">
                                <option value="">Semuanya</option>
                                @foreach ($paymentMethods as $methodOption)
                                    <option value="{{ $methodOption }}"
                                        {{ request('payment_method') == $methodOption ? 'selected' : '' }}>
                                        {{ ucfirst($methodOption) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="bg-gray-800 text-white px-4 rounded-lg hover:bg-gray-900 transition flex items-center justify-center font-bold">
                            <i class="fas fa-filter mr-0 sm:mr-2"></i> <span class="hidden sm:inline">Terapkan</span>
                        </button>
                    </div>
                </form>
            </div>

            <div class="py-8">
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-2">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900">

                            @if ($pesans->isEmpty())
                                <p>Anda belum memiliki pesanan.</p>
                            @else
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead>
                                            <tr>
                                                <th scope="col"
                                                    class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">
                                                    ID Pesanan
                                                </th>
                                                <th scope="col"
                                                    class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">
                                                    Total
                                                </th>
                                                <th scope="col"
                                                    class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">
                                                    Status & Pembayaran
                                                </th>
                                                <th scope="col"
                                                    class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">
                                                    Metode Pembayaran
                                                </th>
                                                <th scope="col"
                                                    class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">
                                                    Nomor Meja
                                                </th>
                                                <th scope="col"
                                                    class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">
                                                    Menu Dipesan
                                                </th>
                                                <th scope="col"
                                                    class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">
                                                    Tanggal Pesan
                                                </th>
                                                @if (auth()->user()->role !== 'chef')
                                                <th scope="col"
                                                    class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">
                                                    Aksi
                                                </th>
                                                @endif
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            @foreach ($pesans as $pesan)
                                                <tr>
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        {{ $pesan->id }}
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        Rp{{ number_format($pesan->total, 0, ',', '.') }}
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        {{-- Status Pesan --}}
                                                        <div>
                                                            <span
                                                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                                                @if ($pesan->status == 'belum diantar') bg-red-100 text-red-800
                                                                @elseif($pesan->status == 'sudah diantar')
                                                                    bg-green-100 text-green-800
                                                                @elseif($pesan->status == 'siap disajikan')
                                                                    bg-blue-100 text-blue-800
                                                                @else
                                                                    bg-yellow-100 text-yellow-800 @endif
                                                                ">
                                                                {{ ucfirst($pesan->status) }}
                                                            </span>
                                                        </div>
                                                        {{-- Status Pembayaran --}}
                                                        <div class="mt-1">
                                                            <span
                                                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                                                @if ($pesan->status_pembayaran == 'sudah dibayar') bg-green-100 text-green-800
                                                                @elseif($pesan->status_pembayaran == 'pending')
                                                                    bg-yellow-100 text-yellow-800
                                                                @else
                                                                    bg-red-100 text-red-800 @endif
                                                                ">
                                                                {{ ucfirst($pesan->status_pembayaran ?? 'belum dibayar') }}
                                                            </span>
                                                        </div>
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        {{ ucfirst($pesan->payment_method) }}
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        {{ $pesan->meja ? $pesan->meja->nomor_meja : '-' }}
                                                    </td>
                                                    <td class="px-6 py-4">
                                                        @foreach ($pesan->menus as $menu)
                                                            <div class="flex items-center mb-2">
                                                                <img src="{{ asset('storage/' . $menu->gambar) }}"
                                                                    alt="{{ $menu->nama }}"
                                                                    class="w-10 h-10 object-cover rounded-md mr-2">
                                                                <div>
                                                                    <p class="text-sm font-medium text-gray-900">
                                                                        {{ $menu->nama }}</p>
                                                                    <p class="text-xs text-gray-700">
                                                                        ({{ $menu->pivot->quantity }}x)
                                                                    </p>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        {{ $pesan->created_at->timezone('Asia/Jakarta')->format('d M Y H:i') }}
                                                    </td>
                                                    @if (auth()->user()->role !== 'chef')
                                                    <td class="px-6 py-4 whitespace-nowrap space-y-2">
                                                        @php
                                                            $userRole = auth()->user()->role;
                                                        @endphp

                                                        {{-- Role waiter bertanggung jawab mengantar makanan setelah disiapkan --}}
                                                        @if ($userRole === 'waiter' && $pesan->status !== 'sudah diantar')
                                                            <form action="{{ route('pesan.updateStatus', $pesan) }}"
                                                                method="POST">
                                                                @csrf
                                                                @method('PATCH')
                                                                <button type="submit"
                                                                    class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded w-full">
                                                                    Sudah Diantar
                                                                </button>
                                                            </form>
                                                        @endif

                                                        {{-- Role cashier hanya bisa menekan tombol sudah dibayar --}}
                                                        @if ($userRole === 'cashier')
                                                            @if ($pesan->status_pembayaran === 'belum dibayar' || $pesan->status_pembayaran === null)
                                                                <form action="{{ route('pesan.updateStatusPembayaran', $pesan) }}"
                                                                    method="POST" class="mb-2" id="form-pay-{{ $pesan->id }}">
                                                                    @csrf
                                                                    @method('PATCH')
                                                                    <input type="hidden" name="payment_method" id="pay-method-{{ $pesan->id }}" value="{{ $pesan->payment_method }}">
                                                                    <button type="button" onclick="confirmPayment({{ $pesan->id }}, '{{ strtolower($pesan->payment_method) }}')"
                                                                        class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded w-full shadow-sm transition">
                                                                        <i class="fas fa-check-circle mr-1"></i> Sudah Dibayar
                                                                    </button>
                                                                </form>
                                                                @php
                                                                    $splitMenus = $pesan->menus->map(function($m) {
                                                                        return [
                                                                            "id" => $m->id,
                                                                            "nama" => $m->nama,
                                                                            "harga" => $m->pivot->harga,
                                                                            "jumlah" => $m->pivot->jumlah ?? $m->pivot->quantity ?? 0
                                                                        ];
                                                                    });
                                                                @endphp
                                                                <div class="flex space-x-2 mt-2">
                                                                    <button onclick="openMoveTableModal({{ $pesan->id }}, {{ $pesan->meja_id ?? 'null' }})" class="bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-bold py-2 px-2 rounded w-1/2 transition border border-gray-300 shadow-sm">Pindah Meja</button>
                                                                    <button onclick='openSplitBillModal({{ $pesan->id }}, @json($splitMenus))' class="bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-xs font-bold py-2 px-2 rounded w-1/2 transition border border-indigo-200 shadow-sm">Pisah Bon</button>
                                                                </div>
                                                            @elseif ($pesan->status_pembayaran === 'sudah dibayar')
                                                                <button onclick="window.open('/pesan/{{ $pesan->id }}/struk-kasir', 'printWindow', 'width=400,height=600')" class="bg-gray-800 hover:bg-gray-900 text-white font-bold py-2 px-4 rounded w-full shadow-sm transition mt-2">
                                                                    <i class="fas fa-print mr-1"></i> Cetak Struk
                                                                </button>
                                                            @endif
                                                        @endif

                                                        {{-- Role admin bisa menekan semua tombol --}}
                                                        @if ($userRole === 'admin')
                                                            @if ($pesan->status === 'belum diantar')
                                                                <form action="{{ route('pesan.updateStatus', $pesan) }}"
                                                                    method="POST">
                                                                    @csrf
                                                                    @method('PATCH')
                                                                    <button type="submit"
                                                                        class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded w-full">
                                                                        Sudah Diantar
                                                                    </button>
                                                                </form>
                                                            @endif

                                                            @if ($pesan->status_pembayaran === 'belum dibayar' || $pesan->status_pembayaran === null)
                                                                <form action="{{ route('pesan.updateStatusPembayaran', $pesan) }}"
                                                                    method="POST" class="mb-2" id="form-pay-{{ $pesan->id }}">
                                                                    @csrf
                                                                    @method('PATCH')
                                                                    <input type="hidden" name="payment_method" id="pay-method-{{ $pesan->id }}" value="{{ $pesan->payment_method }}">
                                                                    <button type="button" onclick="confirmPayment({{ $pesan->id }}, '{{ strtolower($pesan->payment_method) }}')"
                                                                        class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded w-full shadow-sm transition">
                                                                        <i class="fas fa-check-circle mr-1"></i> Sudah Dibayar
                                                                    </button>
                                                                </form>
                                                                @php
                                                                    $splitMenusAdmin = $pesan->menus->map(function($m) {
                                                                        return [
                                                                            "id" => $m->id,
                                                                            "nama" => $m->nama,
                                                                            "harga" => $m->pivot->harga,
                                                                            "jumlah" => $m->pivot->jumlah ?? $m->pivot->quantity ?? 0
                                                                        ];
                                                                    });
                                                                @endphp
                                                                <div class="flex space-x-2 mt-2">
                                                                    <button onclick="openMoveTableModal({{ $pesan->id }}, {{ $pesan->meja_id ?? 'null' }})" class="bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-bold py-2 px-2 rounded w-1/2 transition border border-gray-300 shadow-sm">Pindah Meja</button>
                                                                    <button onclick='openSplitBillModal({{ $pesan->id }}, @json($splitMenusAdmin))' class="bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-xs font-bold py-2 px-2 rounded w-1/2 transition border border-indigo-200 shadow-sm">Pisah Bon</button>
                                                                </div>
                                                            @elseif ($pesan->status_pembayaran === 'pending')
                                                                <form action="{{ route('pesan.updateStatusPembayaran', $pesan) }}"
                                                                    method="POST" id="form-pay-{{ $pesan->id }}">
                                                                    @csrf
                                                                    @method('PATCH')
                                                                    <input type="hidden" name="payment_method" id="pay-method-{{ $pesan->id }}" value="{{ $pesan->payment_method }}">
                                                                    <button type="button" onclick="confirmPayment({{ $pesan->id }}, '{{ strtolower($pesan->payment_method) }}')"
                                                                        class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 px-4 rounded w-full shadow-sm transition">
                                                                        <i class="fas fa-check-circle mr-1"></i> Tandai Lunas
                                                                    </button>
                                                                </form>
                                                            @elseif ($pesan->status_pembayaran === 'sudah dibayar')
                                                                <button onclick="window.open('/pesan/{{ $pesan->id }}/struk-kasir', 'printWindow', 'width=400,height=600')" class="bg-gray-800 hover:bg-gray-900 text-white font-bold py-2 px-4 rounded w-full shadow-sm transition mt-2">
                                                                    <i class="fas fa-print mr-1"></i> Cetak Struk
                                                                </button>
                                                            @endif
                                                        @endif
                                                    </td>
                                                    @endif
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                
                                <!-- Pagination Links -->
                                <div class="mt-6">
                                    {{ $pesans->appends(request()->query())->links() }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
            <script>
                let mejasData = @json($mejas ?? []);

                function openMoveTableModal(pesanId, currentMejaId) {
                    let optionsHtml = '<option value="">-- Pilih Meja --</option>';
                    mejasData.forEach(meja => {
                        if(meja.id !== currentMejaId) {
                            optionsHtml += `<option value="${meja.id}">Meja ${meja.nomor_meja}</option>`;
                        }
                    });

                    Swal.fire({
                        title: 'Pindah Meja',
                        html: `
                            <div class="text-left mb-2 text-sm text-gray-600">Pilih meja tujuan untuk pesanan #${pesanId}:</div>
                            <select id="swal-meja-select" class="w-full border-gray-400 rounded-lg p-3 text-lg focus:ring-orange-500 my-2 shadow-sm font-medium">
                                ${optionsHtml}
                            </select>
                        `,
                        showCancelButton: true,
                        confirmButtonText: 'Pindah Pesanan',
                        cancelButtonText: 'Batal',
                        confirmButtonColor: '#ea580c',
                        preConfirm: () => {
                            const targetMeja = document.getElementById('swal-meja-select').value;
                            if (!targetMeja) {
                                Swal.showValidationMessage('Silakan pilih meja tujuan');
                            }
                            return targetMeja;
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
                                    Swal.fire('Gagal!', data.message || 'Gagal memindah meja', 'error');
                                }
                            });
                        }
                    });
                }

                function openSplitBillModal(pesanId, menus) {
                    let itemsHtml = menus.map((menu, index) => `
                        <div class="flex justify-between items-center py-3 border-b border-gray-100 hover:bg-gray-50 transition p-2 rounded-lg">
                            <div class="text-left flex-1">
                                <div class="text-sm font-bold text-gray-800">${menu.nama}</div>
                                <div class="text-xs font-medium text-orange-600 bg-orange-50 px-2 py-0.5 rounded-full inline-block mt-1">Struk asli: x${menu.jumlah}</div>
                            </div>
                            <div class="flex items-center space-x-3 bg-white border border-gray-300 p-1.5 rounded-lg shadow-sm">
                                <label class="text-[10px] uppercase font-bold text-gray-700 tracking-wider">Pisah:</label>
                                <input type="number" id="split-qty-${menu.id}" min="0" max="${menu.jumlah}" value="0" class="w-16 border border-gray-400 rounded-md p-1.5 text-base font-bold text-center focus:ring-indigo-500 focus:border-indigo-500">
                            </div>
                        </div>
                    `).join('');

                    Swal.fire({
                        title: 'Pisah Bon (Split Bill)',
                        width: 550,
                        html: `
                            <div class="text-sm text-gray-700 text-left mb-4 bg-indigo-50 p-3 rounded-lg border border-indigo-100">
                                <i class="fas fa-info-circle text-indigo-500 mr-1"></i> Tentukan kuantitas item yang ingin <b>dipisahkan</b> menjadi tagihan baru. Sistem akan otomatis memecah Struk #${pesanId} menjadi 2 struk terpisah.
                            </div>
                            <div class="max-h-72 overflow-y-auto mb-2 custom-scrollbar p-1">
                                ${itemsHtml}
                            </div>
                        `,
                        showCancelButton: true,
                        confirmButtonText: '<i class="fas fa-file-invoice mr-2"></i> Buat Tagihan Baru',
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
                                Swal.showValidationMessage('Anda belum menentukan item apapun untuk dipisahkan pencatatannya.');
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
                                    Swal.fire({
                                        title: 'Berhasil Dipisah!',
                                        text: data.message,
                                        icon: 'success',
                                        confirmButtonColor: '#ea580c'
                                    }).then(() => location.reload());
                                } else {
                                    Swal.fire('Gagal!', data.message || 'Error memisah tagihan', 'error');
                                }
                            });
                        }
                    });
                }

                function confirmPayment(pesanId, currentMethod) {
                    Swal.fire({
                        title: 'Konfirmasi Pembayaran',
                        html: `
                            <div class="mb-3 text-sm text-gray-600">Pilih metode pembayaran final untuk Pesanan #${pesanId}:</div>
                            <div class="flex flex-col space-y-2 text-left">
                                <label class="flex items-center space-x-3 p-3 border rounded-lg cursor-pointer hover:bg-green-50 transition border-green-200">
                                    <input type="radio" name="swal_payment_method" value="tunai" ${currentMethod === 'tunai' || !currentMethod ? 'checked' : ''} class="text-green-600 focus:ring-green-500 h-4 w-4">
                                    <span class="font-bold text-gray-700 flex-1"><i class="fas fa-money-bill-wave text-green-500 mr-2 w-5"></i> Tunai (Cash)</span>
                                </label>
                                <label class="flex items-center space-x-3 p-3 border rounded-lg cursor-pointer hover:bg-blue-50 transition border-blue-200">
                                    <input type="radio" name="swal_payment_method" value="qris" ${currentMethod === 'qris' || currentMethod === 'QRIS' ? 'checked' : ''} class="text-blue-600 focus:ring-blue-500 h-4 w-4">
                                    <span class="font-bold text-gray-700 flex-1"><i class="fas fa-qrcode text-blue-500 mr-2 w-5"></i> QRIS</span>
                                </label>
                                <label class="flex items-center space-x-3 p-3 border rounded-lg cursor-pointer hover:bg-purple-50 transition border-purple-200">
                                    <input type="radio" name="swal_payment_method" value="transfer" ${currentMethod === 'transfer' ? 'checked' : ''} class="text-purple-600 focus:ring-purple-500 h-4 w-4">
                                    <span class="font-bold text-gray-700 flex-1"><i class="fas fa-exchange-alt text-purple-500 mr-2 w-5"></i> Transfer Bank</span>
                                </label>
                            </div>
                        `,
                        showCancelButton: true,
                        confirmButtonText: '<i class="fas fa-check-double mr-1"></i> Selesaikan',
                        cancelButtonText: 'Batal',
                        confirmButtonColor: '#3b82f6',
                        preConfirm: () => {
                            const selected = document.querySelector('input[name="swal_payment_method"]:checked');
                            if (!selected) {
                                Swal.showValidationMessage('Silakan pilih salah satu metode pembayaran');
                            }
                            return selected.value;
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            document.getElementById('pay-method-' + pesanId).value = result.value;
                            document.getElementById('form-pay-' + pesanId).submit();
                        }
                    });
                }
            </script>
            @if(session('print_struk_id'))
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    window.open('/pesan/{{ session("print_struk_id") }}/struk-kasir', 'printWindow', 'width=400,height=600');
                });
            </script>
            @endif
        </main>
    </div>
</x-app-layout>


