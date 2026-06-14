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
            <h1 class="text-xl font-bold text-gray-900">Layanan</h1>
            <p class="text-sm text-gray-500 mt-0.5">Daftar semua jenis layanan bengkel</p>
        </div>
        @if(!$showForm)
        <button wire:click="openCreate"
                class="flex items-center gap-1.5 bg-red-600 hover:bg-red-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition">
            <x-heroicon-o-plus class="w-4 h-4" />
            Tambah Layanan
        </button>
        @endif
    </div>

    {{-- Form tambah/edit --}}
    @if($showForm)
    <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
        <h2 class="text-sm font-semibold text-gray-700 mb-4 uppercase tracking-wide">
            {{ $editingId ? 'Edit Layanan' : 'Tambah Layanan Baru' }}
        </h2>
        <form wire:submit="save" class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Nama Layanan <span class="text-red-500">*</span></label>
                <input wire:model="namaService" type="text"
                       class="w-full border border-gray-300 text-sm px-3 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500"
                       placeholder="Ganti oli, Tune-up, dll" />
                @error('namaService') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Harga Default (Rp) <span class="text-red-500">*</span></label>
                <input wire:model="hargaDefault" type="number" min="0" step="1000"
                       class="w-full border border-gray-300 text-sm px-3 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500"
                       placeholder="50000" />
                @error('hargaDefault') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Deskripsi</label>
                <input wire:model="deskripsi" type="text"
                       class="w-full border border-gray-300 text-sm px-3 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500"
                       placeholder="Opsional" />
                @error('deskripsi') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Estimasi Durasi (menit)</label>
                <input wire:model="durasiEstimasi" type="number" min="1"
                       class="w-full border border-gray-300 text-sm px-3 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500"
                       placeholder="60" />
                @error('durasiEstimasi') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="md:col-span-2 flex items-center gap-6">
                <label class="flex items-center gap-2 cursor-pointer select-none">
                    <input wire:model="isActive" type="checkbox"
                           class="w-4 h-4 text-red-600 border-gray-300 rounded focus:ring-red-500" />
                    <span class="text-sm text-gray-700">Aktif</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer select-none">
                    <input wire:model="isBookable" type="checkbox"
                           class="w-4 h-4 text-red-600 border-gray-300 rounded focus:ring-red-500" />
                    <span class="text-sm text-gray-700">Dapat di-booking</span>
                </label>
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

    {{-- Card tabel --}}
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">

        {{-- Toolbar --}}
        <div class="px-5 py-4 border-b border-gray-100">
            <div class="relative max-w-xs">
                <x-heroicon-o-magnifying-glass class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
                <input wire:model.live.debounce.300ms="search"
                       type="text"
                       placeholder="Cari nama layanan..."
                       class="w-full pl-9 pr-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent" />
            </div>
        </div>

        {{-- Tabel --}}
        <table class="w-full text-sm">
            <thead>
                <tr class="text-xs font-semibold uppercase tracking-wider text-gray-500 border-b border-gray-200">
                    <th class="text-left px-5 py-3">Nama Layanan</th>
                    <th class="text-left px-5 py-3">Deskripsi</th>
                    <th class="text-right px-5 py-3">Harga Default</th>
                    <th class="text-center px-5 py-3">Durasi</th>
                    <th class="text-center px-5 py-3">Aktif</th>
                    <th class="text-center px-5 py-3">Bookable</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($services as $service)
                <tr class="border-b border-gray-100 hover:bg-gray-50 transition">
                    <td class="px-5 py-4 font-medium text-gray-900">{{ $service->nama_service }}</td>
                    <td class="px-5 py-4 text-gray-500 max-w-[200px] truncate">{{ $service->deskripsi ?: '-' }}</td>
                    <td class="px-5 py-4 text-right text-gray-700">
                        Rp {{ number_format($service->harga_default, 0, ',', '.') }}
                    </td>
                    <td class="px-5 py-4 text-center text-gray-500 text-xs">
                        {{ $service->durasi_estimasi ? $service->durasi_estimasi.' mnt' : '-' }}
                    </td>
                    <td class="px-5 py-4 text-center">
                        <button wire:click="toggleActive({{ $service->id }})"
                                class="inline-flex items-center justify-center w-9 h-5 rounded-full transition {{ $service->is_active ? 'bg-green-500' : 'bg-gray-200' }}">
                            <span class="w-3.5 h-3.5 bg-white rounded-full shadow transform transition {{ $service->is_active ? 'translate-x-2' : '-translate-x-2' }}"></span>
                        </button>
                    </td>
                    <td class="px-5 py-4 text-center">
                        <button wire:click="toggleBookable({{ $service->id }})"
                                class="inline-flex items-center justify-center w-9 h-5 rounded-full transition {{ $service->is_bookable ? 'bg-blue-500' : 'bg-gray-200' }}">
                            <span class="w-3.5 h-3.5 bg-white rounded-full shadow transform transition {{ $service->is_bookable ? 'translate-x-2' : '-translate-x-2' }}"></span>
                        </button>
                    </td>
                    <td class="px-5 py-4">
                        <button wire:click="openEdit({{ $service->id }})"
                                class="text-gray-400 hover:text-blue-600 transition">
                            <x-heroicon-o-pencil class="w-4 h-4" />
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-5 py-12 text-center text-sm text-gray-400">
                        Tidak ada layanan ditemukan
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Pagination --}}
        @if($services->hasPages())
        <div class="flex items-center justify-between px-5 py-3 border-t border-gray-100">
            <p class="text-sm text-gray-500">
                Menampilkan {{ $services->firstItem() }}–{{ $services->lastItem() }} dari {{ $services->total() }}
            </p>
            <div class="flex items-center gap-1">
                @if($services->onFirstPage())
                <span class="px-3 py-1.5 text-sm text-gray-400 border border-gray-200 rounded cursor-not-allowed">Sebelumnya</span>
                @else
                <button wire:click="previousPage" class="px-3 py-1.5 text-sm text-gray-700 border border-gray-300 rounded hover:bg-gray-50">Sebelumnya</button>
                @endif

                @if($services->hasMorePages())
                <button wire:click="nextPage" class="px-3 py-1.5 text-sm text-gray-700 border border-gray-300 rounded hover:bg-gray-50">Selanjutnya</button>
                @else
                <span class="px-3 py-1.5 text-sm text-gray-400 border border-gray-200 rounded cursor-not-allowed">Selanjutnya</span>
                @endif
            </div>
        </div>
        @endif
    </div>
</div>
