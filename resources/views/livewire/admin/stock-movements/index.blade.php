<div>
    {{-- Page header --}}
    <div class="mb-6">
        <h1 class="text-xl font-bold text-gray-900">Riwayat Stok</h1>
        <p class="text-sm text-gray-500 mt-0.5">Log semua pergerakan stok sparepart</p>
    </div>

    {{-- Card tabel --}}
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">

        {{-- Toolbar --}}
        <div class="px-5 py-4 border-b border-gray-100 flex flex-wrap gap-3 items-center">
            <div class="relative flex-1 min-w-[200px] max-w-xs">
                <x-heroicon-o-magnifying-glass class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
                <input wire:model.live.debounce.300ms="search"
                       type="text"
                       placeholder="Nama atau SKU sparepart..."
                       class="w-full pl-9 pr-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent" />
            </div>

            <select wire:model.live="filterType"
                    class="text-sm border border-gray-300 rounded-lg px-3 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-red-500">
                <option value="">Semua Tipe</option>
                <option value="in">Masuk</option>
                <option value="out">Keluar</option>
                <option value="adjustment">Penyesuaian</option>
            </select>
        </div>

        {{-- Tabel --}}
        <table class="w-full text-sm">
            <thead>
                <tr class="text-xs font-semibold uppercase tracking-wider text-gray-500 border-b border-gray-200">
                    <th class="text-left px-5 py-3">Tanggal</th>
                    <th class="text-left px-5 py-3">Sparepart</th>
                    <th class="text-center px-5 py-3">Tipe</th>
                    <th class="text-center px-5 py-3">Qty</th>
                    <th class="text-center px-5 py-3">Sebelum</th>
                    <th class="text-center px-5 py-3">Sesudah</th>
                    <th class="text-left px-5 py-3">Dicatat oleh</th>
                    <th class="text-left px-5 py-3">Catatan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($movements as $movement)
                <tr class="border-b border-gray-100 hover:bg-gray-50 transition">
                    <td class="px-5 py-4 text-gray-500 whitespace-nowrap text-xs">
                        {{ $movement->created_at->format('d M Y, H:i') }}
                    </td>
                    <td class="px-5 py-4">
                        <span class="font-medium text-gray-900">{{ $movement->sparepart->item_name }}</span>
                        <p class="text-xs text-gray-400 font-mono">{{ $movement->sparepart->sku }}</p>
                    </td>
                    <td class="px-5 py-4 text-center">
                        @if($movement->type === 'in')
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">
                            <x-heroicon-o-arrow-down-tray class="w-3 h-3" /> Masuk
                        </span>
                        @elseif($movement->type === 'out')
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700">
                            <x-heroicon-o-arrow-up-tray class="w-3 h-3" /> Keluar
                        </span>
                        @else
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700">
                            <x-heroicon-o-adjustments-horizontal class="w-3 h-3" /> Adjust
                        </span>
                        @endif
                    </td>
                    <td class="px-5 py-4 text-center font-semibold {{ $movement->type === 'in' ? 'text-green-600' : ($movement->type === 'out' ? 'text-red-600' : 'text-yellow-600') }}">
                        {{ $movement->type === 'in' ? '+' : ($movement->type === 'out' ? '-' : '±') }}{{ $movement->qty }}
                    </td>
                    <td class="px-5 py-4 text-center text-gray-500">{{ $movement->stock_before }}</td>
                    <td class="px-5 py-4 text-center font-medium text-gray-800">{{ $movement->stock_after }}</td>
                    <td class="px-5 py-4 text-gray-500 text-xs">{{ $movement->user->name ?? '—' }}</td>
                    <td class="px-5 py-4 text-gray-400 text-xs max-w-[160px] truncate">{{ $movement->catatan ?? '—' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-5 py-12 text-center text-sm text-gray-400">
                        Tidak ada riwayat stok ditemukan
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Pagination --}}
        @if($movements->hasPages())
        <div class="flex items-center justify-between px-5 py-3 border-t border-gray-100">
            <p class="text-sm text-gray-500">
                Menampilkan {{ $movements->firstItem() }}–{{ $movements->lastItem() }} dari {{ $movements->total() }}
            </p>
            <div class="flex items-center gap-1">
                @if($movements->onFirstPage())
                <span class="px-3 py-1.5 text-sm text-gray-400 border border-gray-200 rounded cursor-not-allowed">Sebelumnya</span>
                @else
                <button wire:click="previousPage" class="px-3 py-1.5 text-sm text-gray-700 border border-gray-300 rounded hover:bg-gray-50">Sebelumnya</button>
                @endif

                @if($movements->hasMorePages())
                <button wire:click="nextPage" class="px-3 py-1.5 text-sm text-gray-700 border border-gray-300 rounded hover:bg-gray-50">Selanjutnya</button>
                @else
                <span class="px-3 py-1.5 text-sm text-gray-400 border border-gray-200 rounded cursor-not-allowed">Selanjutnya</span>
                @endif
            </div>
        </div>
        @endif
    </div>
</div>
