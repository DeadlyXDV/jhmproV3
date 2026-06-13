<div>
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Work Order Saya</h1>
            <p class="text-sm text-gray-500 mt-0.5">Daftar semua pekerjaan yang ditugaskan</p>
        </div>

        <select wire:model.live="filterStatus"
                class="border border-gray-300 text-gray-700 text-sm px-3 py-2 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-red-500">
            <option value="">Semua Status</option>
            <option value="antrian">Antrian</option>
            <option value="proses">Proses</option>
            <option value="selesai">Selesai</option>
        </select>
    </div>

    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-xs font-semibold uppercase tracking-wider text-gray-500 border-b border-gray-200">
                    <th class="text-left px-5 py-3">WO#</th>
                    <th class="text-left px-5 py-3">Kendaraan</th>
                    <th class="text-left px-5 py-3">Customer</th>
                    <th class="text-left px-5 py-3">Keluhan</th>
                    <th class="text-left px-5 py-3">Mulai</th>
                    <th class="text-left px-5 py-3">Status</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($workOrders as $wo)
                @php
                    [$dot, $label] = match($wo->status) {
                        'antrian' => ['bg-amber-500', 'Antrian'],
                        'proses'  => ['bg-blue-500', 'Proses'],
                        'selesai' => ['bg-green-500', 'Selesai'],
                        default   => ['bg-gray-400', $wo->status],
                    };
                @endphp
                <tr class="border-b border-gray-100 hover:bg-gray-50 transition">
                    <td class="px-5 py-4">
                        <span class="text-red-600 font-semibold">{{ $wo->wo_number }}</span>
                    </td>
                    <td class="px-5 py-4">
                        <p class="font-medium text-gray-900">{{ $wo->vehicle?->merk }} {{ $wo->vehicle?->model }}</p>
                        @if($wo->vehicle?->no_polisi)
                        <p class="text-xs text-red-600 font-medium">{{ $wo->vehicle->no_polisi }}</p>
                        @endif
                    </td>
                    <td class="px-5 py-4 text-gray-600">{{ $wo->vehicle?->customer?->nama ?? '-' }}</td>
                    <td class="px-5 py-4 text-gray-500 max-w-[200px] truncate">{{ $wo->keluhan_customer ?: '-' }}</td>
                    <td class="px-5 py-4 text-gray-500 text-xs">
                        {{ $wo->mulai_at ? \Carbon\Carbon::parse($wo->mulai_at)->translatedFormat('d M Y, H:i') : '-' }}
                    </td>
                    <td class="px-5 py-4">
                        <span class="flex items-center gap-1.5 text-xs font-medium text-gray-700">
                            <span class="w-2 h-2 rounded-full {{ $dot }}"></span>{{ $label }}
                        </span>
                    </td>
                    <td class="px-5 py-4">
                        <a href="{{ route('mekanik.work-orders.show', $wo) }}"
                           class="text-gray-400 hover:text-gray-700 transition">
                            <x-heroicon-o-arrow-right class="w-4 h-4" />
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-5 py-12 text-center text-sm text-gray-400">
                        Tidak ada work order ditemukan
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        @if($workOrders->hasPages())
        <div class="flex items-center justify-between px-5 py-3 border-t border-gray-100">
            <p class="text-sm text-gray-500">
                Menampilkan {{ $workOrders->firstItem() }}–{{ $workOrders->lastItem() }} dari {{ $workOrders->total() }}
            </p>
            <div class="flex items-center gap-1">
                @if($workOrders->onFirstPage())
                <span class="px-3 py-1.5 text-sm text-gray-400 border border-gray-200 rounded cursor-not-allowed">Sebelumnya</span>
                @else
                <button wire:click="previousPage" class="px-3 py-1.5 text-sm text-gray-700 border border-gray-300 rounded hover:bg-gray-50">Sebelumnya</button>
                @endif
                @if($workOrders->hasMorePages())
                <button wire:click="nextPage" class="px-3 py-1.5 text-sm text-gray-700 border border-gray-300 rounded hover:bg-gray-50">Selanjutnya</button>
                @else
                <span class="px-3 py-1.5 text-sm text-gray-400 border border-gray-200 rounded cursor-not-allowed">Selanjutnya</span>
                @endif
            </div>
        </div>
        @endif
    </div>
</div>
