<div>
    {{-- Page header --}}
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Work Order</h1>
            <p class="text-sm text-gray-500 mt-0.5">Manajemen semua work order bengkel</p>
        </div>
    </div>

    {{-- Card --}}
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">

        {{-- Toolbar --}}
        <div class="flex items-center gap-3 px-5 py-4 border-b border-gray-100">
            <div class="relative flex-1 max-w-xs">
                <x-heroicon-o-magnifying-glass class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
                <input wire:model.live.debounce.300ms="search"
                       type="text"
                       placeholder="Cari WO, customer, plat..."
                       class="w-full pl-9 pr-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent" />
            </div>

            <select wire:model.live="filterStatus"
                    class="border border-gray-300 text-gray-700 text-sm px-3 py-2 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-red-500">
                <option value="">Semua Status</option>
                @foreach($statusList as $status)
                    <option value="{{ $status }}">{{ ucfirst(str_replace('_', ' ', $status)) }}</option>
                @endforeach
            </select>
        </div>

        {{-- Tabel --}}
        <table class="w-full text-sm">
            <thead>
                <tr class="text-xs font-semibold uppercase tracking-wider text-gray-500 border-b border-gray-200">
                    <th class="text-left px-5 py-3">No. WO</th>
                    <th class="text-left px-5 py-3">Customer</th>
                    <th class="text-left px-5 py-3">Kendaraan</th>
                    <th class="text-left px-5 py-3">Mekanik</th>
                    <th class="text-left px-5 py-3">Status</th>
                    <th class="text-left px-5 py-3">Mulai</th>
                    <th class="text-left px-5 py-3">Selesai</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($workOrders as $wo)
                @php
                    $statusColor = match($wo->status) {
                        'pending'     => 'bg-amber-100 text-amber-700',
                        'in_progress' => 'bg-blue-100 text-blue-700',
                        'done'        => 'bg-green-100 text-green-700',
                        'cancelled'   => 'bg-gray-100 text-gray-500',
                        default       => 'bg-gray-100 text-gray-500',
                    };
                    $statusLabel = match($wo->status) {
                        'pending'     => 'Pending',
                        'in_progress' => 'Dikerjakan',
                        'done'        => 'Selesai',
                        'cancelled'   => 'Dibatalkan',
                        default       => $wo->status,
                    };
                @endphp
                <tr class="border-b border-gray-100 hover:bg-gray-50 transition">
                    <td class="px-5 py-4">
                        <span class="font-mono font-medium text-gray-900">{{ $wo->wo_number }}</span>
                    </td>
                    <td class="px-5 py-4 text-gray-700">
                        {{ $wo->vehicle?->customer?->nama ?? '-' }}
                    </td>
                    <td class="px-5 py-4 text-gray-600">
                        <span class="font-mono text-xs">{{ $wo->vehicle?->plat_nomor ?? '-' }}</span>
                        <br>
                        <span class="text-xs text-gray-400">{{ $wo->vehicle?->merk }} {{ $wo->vehicle?->model }}</span>
                    </td>
                    <td class="px-5 py-4 text-gray-600">
                        {{ $wo->mekanik?->name ?? '-' }}
                    </td>
                    <td class="px-5 py-4">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $statusColor }}">
                            {{ $statusLabel }}
                        </span>
                    </td>
                    <td class="px-5 py-4 text-gray-500 text-xs">
                        {{ $wo->mulai_at?->translatedFormat('d M Y, H:i') ?? '-' }}
                    </td>
                    <td class="px-5 py-4 text-gray-500 text-xs">
                        {{ $wo->selesai_at?->translatedFormat('d M Y, H:i') ?? '-' }}
                    </td>
                    <td class="px-5 py-4">
                        <a wire:navigate href="{{ route('admin.work-orders.show', $wo) }}"
                           class="text-gray-400 hover:text-gray-700 transition">
                            <x-heroicon-o-eye class="w-4 h-4" />
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-5 py-12 text-center text-sm text-gray-400">
                        Tidak ada work order ditemukan
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Pagination --}}
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

                @foreach($workOrders->getUrlRange(max(1, $workOrders->currentPage()-2), min($workOrders->lastPage(), $workOrders->currentPage()+2)) as $page => $url)
                <button wire:click="gotoPage({{ $page }})"
                        class="px-3 py-1.5 text-sm rounded {{ $page === $workOrders->currentPage() ? 'bg-red-600 text-white' : 'text-gray-700 border border-gray-300 hover:bg-gray-50' }}">
                    {{ $page }}
                </button>
                @endforeach

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
