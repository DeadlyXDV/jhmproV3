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
            <h1 class="text-xl font-bold text-gray-900">Sparepart</h1>
            <p class="text-sm text-gray-500 mt-0.5">Daftar semua part dan komponen inventori</p>
        </div>
        @if(!$showForm)
        <button wire:click="openCreate"
                class="flex items-center gap-1.5 bg-red-600 hover:bg-red-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition">
            <x-heroicon-o-plus class="w-4 h-4" />
            Tambah Sparepart
        </button>
        @endif
    </div>

    {{-- Form tambah/edit --}}
    @if($showForm)
    <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
        <h2 class="text-sm font-semibold text-gray-700 mb-4 uppercase tracking-wide">
            {{ $editingId ? 'Edit Sparepart' : 'Tambah Sparepart Baru' }}
        </h2>
        <form wire:submit="save" class="grid grid-cols-1 md:grid-cols-3 gap-4">

            {{-- Baris 1: SKU, Nama, Kategori --}}
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">SKU <span class="text-red-500">*</span></label>
                <input wire:model="sku" type="text"
                       class="w-full border border-gray-300 text-sm px-3 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 uppercase"
                       placeholder="OLI-YAMALUBE-1L" />
                @error('sku') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Nama Part <span class="text-red-500">*</span></label>
                <input wire:model="itemName" type="text"
                       class="w-full border border-gray-300 text-sm px-3 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500"
                       placeholder="Oli Yamalube 1 Liter" />
                @error('itemName') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Kategori</label>
                <select wire:model="categoryId"
                        class="w-full border border-gray-300 text-sm px-3 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 bg-white">
                    <option value="">— Tanpa Kategori —</option>
                    @foreach($categories as $cat)
                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </select>
                @error('categoryId') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Baris 2: Merek, Satuan, Berat --}}
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Merek</label>
                <input wire:model="brand" type="text"
                       class="w-full border border-gray-300 text-sm px-3 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500"
                       placeholder="Yamalube, NGK, dll" />
                @error('brand') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Satuan <span class="text-red-500">*</span></label>
                <input wire:model="satuan" type="text"
                       class="w-full border border-gray-300 text-sm px-3 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500"
                       placeholder="pcs, liter, set" />
                @error('satuan') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Berat (gram)</label>
                <input wire:model="berat" type="number" min="1"
                       class="w-full border border-gray-300 text-sm px-3 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500"
                       placeholder="500" />
                @error('berat') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Baris 3: Harga Beli, Harga Jual, Harga Online --}}
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Harga Beli (Rp) <span class="text-red-500">*</span></label>
                <input wire:model="hargaBeli" type="number" min="0" step="500"
                       class="w-full border border-gray-300 text-sm px-3 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500"
                       placeholder="25000" />
                @error('hargaBeli') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Harga Jual (Rp) <span class="text-red-500">*</span></label>
                <input wire:model="hargaJual" type="number" min="0" step="500"
                       class="w-full border border-gray-300 text-sm px-3 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500"
                       placeholder="35000" />
                @error('hargaJual') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Harga Online (Rp)</label>
                <input wire:model="hargaOnline" type="number" min="0" step="500"
                       class="w-full border border-gray-300 text-sm px-3 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500"
                       placeholder="Opsional" />
                @error('hargaOnline') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Baris 4: Stok, Min Stok --}}
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Stok Awal <span class="text-red-500">*</span></label>
                <input wire:model="stock" type="number" min="0"
                       class="w-full border border-gray-300 text-sm px-3 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500"
                       placeholder="10" />
                @error('stock') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Minimum Stok <span class="text-red-500">*</span></label>
                <input wire:model="minimumStock" type="number" min="0"
                       class="w-full border border-gray-300 text-sm px-3 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500"
                       placeholder="2" />
                @error('minimumStock') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Baris 5: Deskripsi --}}
            <div class="md:col-span-3">
                <label class="block text-xs font-medium text-gray-600 mb-1">Deskripsi</label>
                <textarea wire:model="deskripsi" rows="2"
                          class="w-full border border-gray-300 text-sm px-3 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500"
                          placeholder="Deskripsi produk (opsional)"></textarea>
                @error('deskripsi') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Toggle --}}
            <div class="md:col-span-3 flex items-center gap-6">
                <label class="flex items-center gap-2 cursor-pointer select-none">
                    <input wire:model="isActive" type="checkbox"
                           class="w-4 h-4 text-red-600 border-gray-300 rounded focus:ring-red-500" />
                    <span class="text-sm text-gray-700">Aktif</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer select-none">
                    <input wire:model="isSoldOnline" type="checkbox"
                           class="w-4 h-4 text-red-600 border-gray-300 rounded focus:ring-red-500" />
                    <span class="text-sm text-gray-700">Dijual Online</span>
                </label>
            </div>

            <div class="md:col-span-3 flex items-center gap-3">
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
        <div class="px-5 py-4 border-b border-gray-100 flex flex-wrap gap-3 items-center">
            <div class="relative flex-1 min-w-[200px] max-w-xs">
                <x-heroicon-o-magnifying-glass class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
                <input wire:model.live.debounce.300ms="search"
                       type="text"
                       placeholder="SKU, nama, merek..."
                       class="w-full pl-9 pr-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent" />
            </div>

            <select wire:model.live="filterCategory"
                    class="text-sm border border-gray-300 rounded-lg px-3 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-red-500">
                <option value="">Semua Kategori</option>
                @foreach($categories as $cat)
                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                @endforeach
            </select>

            <label class="flex items-center gap-2 cursor-pointer select-none text-sm text-gray-600">
                <input wire:model.live="filterCritical" type="checkbox"
                       class="w-4 h-4 text-red-600 border-gray-300 rounded focus:ring-red-500" />
                Stok kritis saja
            </label>
        </div>

        {{-- Tabel --}}
        <table class="w-full text-sm">
            <thead>
                <tr class="text-xs font-semibold uppercase tracking-wider text-gray-500 border-b border-gray-200">
                    <th class="text-left px-5 py-3">SKU</th>
                    <th class="text-left px-5 py-3">Nama Part</th>
                    <th class="text-left px-5 py-3">Kategori</th>
                    <th class="text-left px-5 py-3">Merek</th>
                    <th class="text-center px-5 py-3">Stok</th>
                    <th class="text-center px-5 py-3">Min</th>
                    <th class="text-right px-5 py-3">Harga Jual</th>
                    <th class="text-center px-5 py-3">Aktif</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($spareparts as $part)
                @php $critical = $part->stock <= $part->minimum_stock; @endphp
                <tr class="border-b border-gray-100 hover:bg-gray-50 transition {{ $critical ? 'bg-red-50/40' : '' }}">
                    <td class="px-5 py-4 font-mono text-xs text-gray-500">{{ $part->sku }}</td>
                    <td class="px-5 py-4 font-medium text-gray-900">
                        {{ $part->item_name }}
                        @if($critical)
                        <span class="ml-1.5 inline-flex items-center gap-1 text-xs text-red-600 font-medium">
                            <x-heroicon-o-exclamation-triangle class="w-3.5 h-3.5" /> Kritis
                        </span>
                        @endif
                    </td>
                    <td class="px-5 py-4 text-gray-500">{{ $part->category?->name ?? '—' }}</td>
                    <td class="px-5 py-4 text-gray-500">{{ $part->brand ?? '—' }}</td>
                    <td class="px-5 py-4 text-center">
                        <span class="font-semibold {{ $critical ? 'text-red-600' : 'text-gray-800' }}">
                            {{ $part->stock }}
                        </span>
                        <span class="text-xs text-gray-400 ml-0.5">{{ $part->satuan }}</span>
                    </td>
                    <td class="px-5 py-4 text-center text-gray-400 text-xs">{{ $part->minimum_stock }}</td>
                    <td class="px-5 py-4 text-right text-gray-700">
                        Rp {{ number_format($part->harga_jual, 0, ',', '.') }}
                    </td>
                    <td class="px-5 py-4 text-center">
                        <button wire:click="toggleActive({{ $part->id }})"
                                class="inline-flex items-center justify-center w-9 h-5 rounded-full transition {{ $part->is_active ? 'bg-green-500' : 'bg-gray-200' }}">
                            <span class="w-3.5 h-3.5 bg-white rounded-full shadow transform transition {{ $part->is_active ? 'translate-x-2' : '-translate-x-2' }}"></span>
                        </button>
                    </td>
                    <td class="px-5 py-4">
                        <button wire:click="openEdit({{ $part->id }})"
                                class="text-gray-400 hover:text-blue-600 transition">
                            <x-heroicon-o-pencil class="w-4 h-4" />
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="px-5 py-12 text-center text-sm text-gray-400">
                        Tidak ada sparepart ditemukan
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Pagination --}}
        @if($spareparts->hasPages())
        <div class="flex items-center justify-between px-5 py-3 border-t border-gray-100">
            <p class="text-sm text-gray-500">
                Menampilkan {{ $spareparts->firstItem() }}–{{ $spareparts->lastItem() }} dari {{ $spareparts->total() }}
            </p>
            <div class="flex items-center gap-1">
                @if($spareparts->onFirstPage())
                <span class="px-3 py-1.5 text-sm text-gray-400 border border-gray-200 rounded cursor-not-allowed">Sebelumnya</span>
                @else
                <button wire:click="previousPage" class="px-3 py-1.5 text-sm text-gray-700 border border-gray-300 rounded hover:bg-gray-50">Sebelumnya</button>
                @endif

                @if($spareparts->hasMorePages())
                <button wire:click="nextPage" class="px-3 py-1.5 text-sm text-gray-700 border border-gray-300 rounded hover:bg-gray-50">Selanjutnya</button>
                @else
                <span class="px-3 py-1.5 text-sm text-gray-400 border border-gray-200 rounded cursor-not-allowed">Selanjutnya</span>
                @endif
            </div>
        </div>
        @endif
    </div>
</div>
