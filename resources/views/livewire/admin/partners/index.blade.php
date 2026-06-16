<div>
    {{-- Flash message --}}
    @if(session('success'))
    <div class="mb-4 px-4 py-3 bg-green-50 border border-green-200 text-green-700 text-sm rounded-lg flex items-center gap-2">
        <x-heroicon-o-check-circle class="w-4 h-4 flex-shrink-0" />
        {{ session('success') }}
    </div>
    @endif

    {{-- Page header --}}
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Partner</h1>
            <p class="text-sm text-gray-500 mt-0.5">Daftar bengkel mitra</p>
        </div>
        @if(!$showForm)
        <button wire:click="openCreate"
                class="flex items-center gap-1.5 bg-red-600 hover:bg-red-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition">
            <x-heroicon-o-plus class="w-4 h-4" />
            Tambah Partner
        </button>
        @endif
    </div>

    {{-- Form tambah/edit --}}
    @if($showForm)
    <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
        <h2 class="text-sm font-semibold text-gray-700 mb-4 uppercase tracking-wide">
            {{ $editingId ? 'Edit Partner' : 'Tambah Partner Baru' }}
        </h2>
        <form wire:submit="save" class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Nama Bengkel <span class="text-red-500">*</span></label>
                <input wire:model="namaBengkel" type="text"
                       class="w-full border border-gray-300 text-sm px-3 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500"
                       placeholder="Bengkel ABC Motor" />
                @error('namaBengkel') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Contact Person <span class="text-red-500">*</span></label>
                <input wire:model="contactPerson" type="text"
                       class="w-full border border-gray-300 text-sm px-3 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500"
                       placeholder="Nama PIC" />
                @error('contactPerson') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">No. HP <span class="text-red-500">*</span></label>
                <input wire:model="noHp" type="text"
                       class="w-full border border-gray-300 text-sm px-3 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500"
                       placeholder="08123456789" />
                @error('noHp') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Alamat</label>
                <input wire:model="alamat" type="text"
                       class="w-full border border-gray-300 text-sm px-3 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500"
                       placeholder="Opsional" />
                @error('alamat') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="md:col-span-2">
                <label class="block text-xs font-medium text-gray-600 mb-1">Catatan</label>
                <textarea wire:model="catatan" rows="2"
                          class="w-full border border-gray-300 text-sm px-3 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500"
                          placeholder="Catatan tambahan (opsional)"></textarea>
                @error('catatan') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="md:col-span-2 flex items-center gap-3">
                <button type="submit"
                        class="bg-red-600 hover:bg-red-700 text-white text-sm font-medium px-5 py-2 rounded-lg transition flex items-center gap-2">
                    <div wire:loading wire:target="save" class="w-3.5 h-3.5 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
                    <span wire:loading.remove wire:target="save">
                        <x-heroicon-o-check class="w-4 h-4 inline -mt-0.5" /> Simpan
                    </span>
                    <span wire:loading wire:target="save">Menyimpan...</span>
                </button>
                <button type="button" wire:click="cancelForm"
                        class="text-sm text-gray-500 hover:text-gray-700 px-4 py-2 rounded-lg border border-gray-300 hover:bg-gray-50 transition">
                    Batal
                </button>
            </div>
        </form>
    </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm overflow-hidden">

        {{-- Toolbar --}}
        <div class="flex items-center gap-3 px-5 py-4 border-b border-gray-100">
            <div class="relative flex-1 max-w-xs">
                <x-heroicon-o-magnifying-glass class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
                <input wire:model.live.debounce.300ms="search"
                       type="text"
                       placeholder="Cari nama bengkel, contact person..."
                       class="w-full pl-9 pr-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent" />
            </div>
        </div>

        <table class="w-full text-sm">
            <thead>
                <tr class="text-xs font-semibold uppercase tracking-wider text-gray-500 border-b border-gray-200">
                    <th class="text-left px-5 py-3">Nama Bengkel</th>
                    <th class="text-left px-5 py-3">Contact Person</th>
                    <th class="text-left px-5 py-3">No. HP</th>
                    <th class="text-left px-5 py-3">Alamat</th>
                    <th class="text-left px-5 py-3">Total Kunjungan</th>
                    <th class="text-left px-5 py-3">Total Transaksi</th>
                    <th class="text-left px-5 py-3">Catatan</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($partners as $partner)
                <tr class="border-b border-gray-100 hover:bg-gray-50 transition">
                    <td class="px-5 py-4">
                        <div class="flex items-center gap-2">
                            <div class="w-7 h-7 rounded bg-gray-100 flex items-center justify-center flex-shrink-0">
                                <x-heroicon-o-building-storefront class="w-4 h-4 text-gray-400" />
                            </div>
                            <span class="font-medium text-gray-900">{{ $partner->nama_bengkel }}</span>
                        </div>
                    </td>
                    <td class="px-5 py-4 text-gray-600">{{ $partner->contact_person ?: '-' }}</td>
                    <td class="px-5 py-4 text-gray-600">{{ $partner->no_hp ?: '-' }}</td>
                    <td class="px-5 py-4 text-gray-600 max-w-[180px] truncate">{{ $partner->alamat ?: '-' }}</td>
                    <td class="px-5 py-4 text-gray-700">{{ $partner->total_kunjungan }}</td>
                    <td class="px-5 py-4 text-gray-700">Rp {{ number_format($partner->total_transaksi ?? 0, 0, ',', '.') }}</td>
                    <td class="px-5 py-4 text-gray-500 max-w-[160px] truncate">{{ $partner->catatan ?: '-' }}</td>
                    <td class="px-5 py-4">
                        <div class="flex items-center gap-2 justify-end">
                            <button wire:click="openEdit({{ $partner->id }})"
                                    class="text-gray-400 hover:text-blue-600 transition">
                                <x-heroicon-o-pencil class="w-4 h-4" />
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-5 py-12 text-center text-sm text-gray-400">
                        Tidak ada partner ditemukan
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        @if($partners->hasPages())
        <div class="flex items-center justify-between px-5 py-3 border-t border-gray-100">
            <p class="text-sm text-gray-500">
                Menampilkan {{ $partners->firstItem() }}–{{ $partners->lastItem() }} dari {{ $partners->total() }}
            </p>
            <div class="flex items-center gap-1">
                @if($partners->onFirstPage())
                <span class="px-3 py-1.5 text-sm text-gray-400 border border-gray-200 rounded cursor-not-allowed">Sebelumnya</span>
                @else
                <button wire:click="previousPage" class="px-3 py-1.5 text-sm text-gray-700 border border-gray-300 rounded hover:bg-gray-50">Sebelumnya</button>
                @endif
                @if($partners->hasMorePages())
                <button wire:click="nextPage" class="px-3 py-1.5 text-sm text-gray-700 border border-gray-300 rounded hover:bg-gray-50">Selanjutnya</button>
                @else
                <span class="px-3 py-1.5 text-sm text-gray-400 border border-gray-200 rounded cursor-not-allowed">Selanjutnya</span>
                @endif
            </div>
        </div>
        @endif

    </div>
</div>
