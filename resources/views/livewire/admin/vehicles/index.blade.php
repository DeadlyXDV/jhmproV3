<div>
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Kendaraan</h1>
            <p class="text-sm text-gray-500 mt-0.5">Semua kendaraan terdaftar di bengkel</p>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm overflow-hidden">

        <div class="flex items-center gap-3 px-5 py-4 border-b border-gray-100">
            <div class="relative flex-1 max-w-xs">
                <x-heroicon-o-magnifying-glass class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
                <input wire:model.live.debounce.300ms="search"
                       type="text"
                       placeholder="Cari no polisi, merk, model, pemilik..."
                       class="w-full pl-9 pr-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent" />
            </div>
        </div>

        <table class="w-full text-sm">
            <thead>
                <tr class="text-xs font-semibold uppercase tracking-wider text-gray-500 border-b border-gray-200">
                    <th class="text-left px-5 py-3">Kendaraan</th>
                    <th class="text-left px-5 py-3">No. Polisi</th>
                    <th class="text-left px-5 py-3">Pemilik</th>
                    <th class="text-left px-5 py-3">Tahun</th>
                    <th class="text-left px-5 py-3">Warna</th>
                    <th class="text-left px-5 py-3">Total Servis</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($vehicles as $vehicle)
                <tr class="border-b border-gray-100 hover:bg-gray-50 transition">
                    <td class="px-5 py-4">
                        <p class="font-medium text-gray-900">{{ $vehicle->merk }} {{ $vehicle->model }}</p>
                        @if($vehicle->tipe)
                        <p class="text-xs text-gray-400 mt-0.5">{{ $vehicle->tipe }}</p>
                        @endif
                    </td>
                    <td class="px-5 py-4">
                        <span class="text-red-600 font-semibold text-sm tracking-wide">{{ $vehicle->no_polisi ?: '-' }}</span>
                    </td>
                    <td class="px-5 py-4">
                        @if($vehicle->customer)
                        <a wire:navigate href="{{ route('admin.customers.show', $vehicle->customer) }}"
                           class="text-red-600 hover:underline">{{ $vehicle->customer->nama }}</a>
                        @else
                        <span class="text-gray-400">-</span>
                        @endif
                    </td>
                    <td class="px-5 py-4 text-gray-600">{{ $vehicle->tahun ?: '-' }}</td>
                    <td class="px-5 py-4 text-gray-600">{{ $vehicle->warna ?: '-' }}</td>
                    <td class="px-5 py-4 text-gray-700">{{ $vehicle->total_servis }}</td>
                    <td class="px-5 py-4">
                        <a wire:navigate href="{{ route('admin.vehicles.show', $vehicle) }}"
                           class="text-gray-400 hover:text-gray-700 transition">
                            <x-heroicon-o-eye class="w-4 h-4" />
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-5 py-12 text-center text-sm text-gray-400">
                        Tidak ada kendaraan ditemukan
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        @if($vehicles->hasPages())
        <div class="flex items-center justify-between px-5 py-3 border-t border-gray-100">
            <p class="text-sm text-gray-500">
                Menampilkan {{ $vehicles->firstItem() }}–{{ $vehicles->lastItem() }} dari {{ $vehicles->total() }}
            </p>
            <div class="flex items-center gap-1">
                @if($vehicles->onFirstPage())
                <span class="px-3 py-1.5 text-sm text-gray-400 border border-gray-200 rounded cursor-not-allowed">Sebelumnya</span>
                @else
                <button wire:click="previousPage" class="px-3 py-1.5 text-sm text-gray-700 border border-gray-300 rounded hover:bg-gray-50">Sebelumnya</button>
                @endif
                @if($vehicles->hasMorePages())
                <button wire:click="nextPage" class="px-3 py-1.5 text-sm text-gray-700 border border-gray-300 rounded hover:bg-gray-50">Selanjutnya</button>
                @else
                <span class="px-3 py-1.5 text-sm text-gray-400 border border-gray-200 rounded cursor-not-allowed">Selanjutnya</span>
                @endif
            </div>
        </div>
        @endif

    </div>
</div>
