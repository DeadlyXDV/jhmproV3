<div>
    {{-- Page header --}}
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Partner</h1>
            <p class="text-sm text-gray-500 mt-0.5">Daftar bengkel mitra</p>
        </div>
        <button class="flex items-center gap-1.5 bg-red-600 hover:bg-red-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition">
            <x-heroicon-o-plus class="w-4 h-4" />
            Tambah Partner
        </button>
    </div>

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
                            <button class="text-gray-400 hover:text-blue-600 transition">
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
