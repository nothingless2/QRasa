<x-app-layout>
    <x-admin-sidebar />

    <main class="flex-1 min-w-0 lg:ml-64 p-4 lg:p-8">
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-gray-800">Edit Meja</h1>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6">
            <form action="{{ route('meja.update', $meja->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="mb-4">
                    <label for="nomor_meja" class="block text-sm font-medium text-gray-700">Nomor Meja</label>
                    <input type="number" name="nomor_meja" id="nomor_meja" value="{{ $meja->nomor_meja }}" class="mt-1 block w-full rounded-md border-gray-400 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                    @error('nomor_meja')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-end">
                    <a href="{{ route('meja.index') }}" class="mr-4 px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-400 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">Batal</a>
                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-orange-600 border border-transparent rounded-md shadow-sm hover:bg-orange-600/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-600/90">Perbarui</button>
                </div>
            </form>
        </div>
    </main>
</x-app-layout>


