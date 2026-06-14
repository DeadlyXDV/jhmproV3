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
            <h1 class="text-xl font-bold text-gray-900">Kategori Sparepart</h1>
            <p class="text-sm text-gray-500 mt-0.5">Kelola kategori dan sub-kategori sparepart</p>
        </div>
        @if(!$showForm)
        <button wire:click="openCreate"
                class="flex items-center gap-1.5 bg-red-600 hover:bg-red-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition">
            <x-heroicon-o-plus class="w-4 h-4" />
            Tambah Kategori
        </button>
        @endif
    </div>

    {{-- Form tambah/edit --}}
    @if($showForm)
    <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
        <h2 class="text-sm font-semibold text-gray-700 mb-4 uppercase tracking-wide">
            {{ $editingId ? 'Edit Kategori' : 'Tambah Kategori Baru' }}
        </h2>
        <form wire:submit="save" class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Nama Kategori <span class="text-red-500">*</span></label>
                <input wire:model.live="name" type="text"
                       class="w-full border border-gray-300 text-sm px-3 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500"
                       placeholder="Oli Mesin, Filter Udara, dll" />
                @error('name') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Slug <span class="text-red-500">*</span></label>
                <input wire:model="slug" type="text"
                       class="w-full border border-gray-300 text-sm px-3 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 font-mono"
                       placeholder="oli-mesin" />
                @error('slug') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Parent Kategori</label>
                <select wire:model="parentId"
                        class="w-full border border-gray-300 text-sm px-3 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 bg-white">
                    <option value="">— Tidak ada (kategori utama) —</option>
                    @foreach($allCategories as $cat)
                        @if(!$editingId || $cat->id !== $editingId)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endif
                    @endforeach
                </select>
                @error('parentId') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Deskripsi</label>
                <input wire:model="description" type="text"
                       class="w-full border border-gray-300 text-sm px-3 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500"
                       placeholder="Opsional" />
                @error('description') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
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
                       placeholder="Cari nama kategori..."
                       class="w-full pl-9 pr-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent" />
            </div>
        </div>

        {{-- Tabel --}}
        <table class="w-full text-sm">
            <thead>
                <tr class="text-xs font-semibold uppercase tracking-wider text-gray-500 border-b border-gray-200">
                    <th class="text-left px-5 py-3">Nama Kategori</th>
                    <th class="text-left px-5 py-3">Slug</th>
                    <th class="text-left px-5 py-3">Parent</th>
                    <th class="text-center px-5 py-3">Jumlah Sparepart</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($categories as $category)
                <tr class="border-b border-gray-100 hover:bg-gray-50 transition">
                    <td class="px-5 py-4">
                        <span class="font-medium text-gray-900">{{ $category->name }}</span>
                        @if($category->description)
                        <p class="text-xs text-gray-400 mt-0.5 truncate max-w-[180px]">{{ $category->description }}</p>
                        @endif
                    </td>
                    <td class="px-5 py-4 font-mono text-xs text-gray-500">{{ $category->slug }}</td>
                    <td class="px-5 py-4 text-gray-500">{{ $category->parent?->name ?? '—' }}</td>
                    <td class="px-5 py-4 text-center">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-700">
                            {{ $category->spareparts_count }}
                        </span>
                    </td>
                    <td class="px-5 py-4">
                        <button wire:click="openEdit({{ $category->id }})"
                                class="text-gray-400 hover:text-blue-600 transition">
                            <x-heroicon-o-pencil class="w-4 h-4" />
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-5 py-12 text-center text-sm text-gray-400">
                        Tidak ada kategori ditemukan
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Pagination --}}
        @if($categories->hasPages())
        <div class="flex items-center justify-between px-5 py-3 border-t border-gray-100">
            <p class="text-sm text-gray-500">
                Menampilkan {{ $categories->firstItem() }}–{{ $categories->lastItem() }} dari {{ $categories->total() }}
            </p>
            <div class="flex items-center gap-1">
                @if($categories->onFirstPage())
                <span class="px-3 py-1.5 text-sm text-gray-400 border border-gray-200 rounded cursor-not-allowed">Sebelumnya</span>
                @else
                <button wire:click="previousPage" class="px-3 py-1.5 text-sm text-gray-700 border border-gray-300 rounded hover:bg-gray-50">Sebelumnya</button>
                @endif

                @if($categories->hasMorePages())
                <button wire:click="nextPage" class="px-3 py-1.5 text-sm text-gray-700 border border-gray-300 rounded hover:bg-gray-50">Selanjutnya</button>
                @else
                <span class="px-3 py-1.5 text-sm text-gray-400 border border-gray-200 rounded cursor-not-allowed">Selanjutnya</span>
                @endif
            </div>
        </div>
        @endif
    </div>
</div>
