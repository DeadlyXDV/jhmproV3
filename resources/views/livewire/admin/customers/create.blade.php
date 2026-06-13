<div>
    {{-- Page header --}}
    <div class="mb-6">
        <div class="flex items-center gap-2 text-sm text-gray-500 mb-1">
            <a href="{{ route('admin.customers.index') }}" class="hover:text-gray-700">Pelanggan</a>
            <span>/</span>
            <span>{{ $isEdit ? 'Edit' : 'Tambah Baru' }}</span>
        </div>
        <h1 class="text-xl font-bold text-gray-900">{{ $isEdit ? 'Edit Pelanggan' : 'Tambah Pelanggan' }}</h1>
    </div>

    <form wire:submit="save" class="max-w-2xl">
        <div class="bg-white rounded-xl shadow-sm p-6 space-y-5">

            {{-- Nama --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap <span class="text-red-600">*</span></label>
                <input wire:model="nama" type="text" placeholder="Contoh: Budi Santoso"
                       class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent
                              @error('nama') border-red-500 @enderror" />
                @error('nama') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- No. HP --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nomor HP</label>
                <input wire:model="no_hp" type="text" placeholder="Contoh: 081234567890"
                       class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent
                              @error('no_hp') border-red-500 @enderror" />
                @error('no_hp') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Email --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input wire:model="email" type="email" placeholder="Contoh: budi@email.com"
                       class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent
                              @error('email') border-red-500 @enderror" />
                @error('email') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Alamat --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Alamat</label>
                <textarea wire:model="alamat" rows="3" placeholder="Alamat lengkap..."
                          class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent resize-none"></textarea>
            </div>

            {{-- Catatan --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Catatan Internal</label>
                <textarea wire:model="catatan" rows="2" placeholder="Catatan khusus tentang pelanggan ini..."
                          class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent resize-none"></textarea>
            </div>

        </div>

        {{-- Actions --}}
        <div class="flex items-center gap-3 mt-5">
            <button type="submit"
                    class="bg-red-600 hover:bg-red-700 text-white text-sm font-medium px-5 py-2.5 rounded-lg transition">
                {{ $isEdit ? 'Simpan Perubahan' : 'Tambah Pelanggan' }}
            </button>
            <a href="{{ route('admin.customers.index') }}"
               class="text-sm text-gray-600 hover:text-gray-800 px-4 py-2.5">Batal</a>
        </div>
    </form>

</div>
