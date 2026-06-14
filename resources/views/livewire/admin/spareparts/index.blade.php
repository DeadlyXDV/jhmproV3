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
    </div>

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
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-5 py-12 text-center text-sm text-gray-400">
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
