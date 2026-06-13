<div>
    {{-- Page header --}}
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Pelanggan</h1>
            <p class="text-sm text-gray-500 mt-0.5">Daftar semua pelanggan bengkel</p>
        </div>
        <a href="{{ route('admin.customers.create') }}"
           class="flex items-center gap-1.5 bg-red-600 hover:bg-red-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition">
            <x-heroicon-o-plus class="w-4 h-4" />
            Tambah Pelanggan
        </a>
    </div>

    {{-- Card --}}
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">

        {{-- Toolbar --}}
        <div class="flex items-center gap-3 px-5 py-4 border-b border-gray-100">
            <div class="relative flex-1 max-w-xs">
                <x-heroicon-o-magnifying-glass class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
                <input wire:model.live.debounce.300ms="search"
                       type="text"
                       placeholder="Cari nama, HP, email..."
                       class="w-full pl-9 pr-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent" />
            </div>

            <select wire:model.live="filterSegmen"
                    class="border border-gray-300 text-gray-700 text-sm px-3 py-2 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-red-500">
                <option value="">Semua Segmen</option>
                @foreach($segmenList as $segmen)
                <option value="{{ $segmen }}">{{ $segmen }}</option>
                @endforeach
            </select>
        </div>

        {{-- Tabel --}}
        <table class="w-full text-sm">
            <thead>
                <tr class="text-xs font-semibold uppercase tracking-wider text-gray-500 border-b border-gray-200">
                    <th class="text-left px-5 py-3">Pelanggan</th>
                    <th class="text-left px-5 py-3">No. HP</th>
                    <th class="text-left px-5 py-3">Email</th>
                    <th class="text-left px-5 py-3">Kendaraan</th>
                    <th class="text-left px-5 py-3">Total Transaksi</th>
                    <th class="text-left px-5 py-3">Segmen RFM</th>
                    <th class="text-left px-5 py-3">Terdaftar</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($customers as $customer)
                @php
                    $rfm = $customer->rfm->first();
                    $colors = ['bg-red-100 text-red-700','bg-blue-100 text-blue-700','bg-green-100 text-green-700','bg-purple-100 text-purple-700','bg-orange-100 text-orange-700','bg-pink-100 text-pink-700','bg-teal-100 text-teal-700','bg-amber-100 text-amber-700'];
                    $avatarColor = $colors[abs(crc32($customer->nama)) % count($colors)];
                    $initials = collect(explode(' ', $customer->nama))->take(2)->map(fn($w) => strtoupper($w[0]))->join('');
                    $segmenColor = match($rfm?->cluster_label) {
                        'Champion'  => 'bg-green-100 text-green-700',
                        'Loyal'     => 'bg-blue-100 text-blue-700',
                        'Potential' => 'bg-purple-100 text-purple-700',
                        'At Risk'   => 'bg-amber-100 text-amber-700',
                        'Lost'      => 'bg-red-100 text-red-700',
                        default     => 'bg-gray-100 text-gray-600',
                    };
                @endphp
                <tr class="border-b border-gray-100 hover:bg-gray-50 transition">
                    <td class="px-5 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0 {{ $avatarColor }}">
                                {{ $initials }}
                            </div>
                            <span class="font-medium text-gray-900">{{ $customer->nama }}</span>
                        </div>
                    </td>
                    <td class="px-5 py-4 text-gray-600">{{ $customer->no_hp ?: '-' }}</td>
                    <td class="px-5 py-4 text-gray-600">{{ $customer->email ?: '-' }}</td>
                    <td class="px-5 py-4 text-gray-700">{{ $customer->vehicles_count }}</td>
                    <td class="px-5 py-4 text-gray-700">
                        Rp {{ number_format($customer->total_transaksi ?? 0, 0, ',', '.') }}
                    </td>
                    <td class="px-5 py-4">
                        @if($rfm)
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $segmenColor }}">
                            {{ $rfm->cluster_label }}
                        </span>
                        @else
                        <span class="text-gray-400 text-xs">-</span>
                        @endif
                    </td>
                    <td class="px-5 py-4 text-gray-500 text-xs">
                        {{ $customer->created_at->translatedFormat('d M Y') }}
                    </td>
                    <td class="px-5 py-4">
                        <div class="flex items-center gap-2 justify-end">
                            <a href="{{ route('admin.customers.show', $customer) }}"
                               class="text-gray-400 hover:text-gray-700 transition">
                                <x-heroicon-o-eye class="w-4 h-4" />
                            </a>
                            <a href="{{ route('admin.customers.edit', $customer) }}"
                               class="text-gray-400 hover:text-blue-600 transition">
                                <x-heroicon-o-pencil class="w-4 h-4" />
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-5 py-12 text-center text-sm text-gray-400">
                        Tidak ada pelanggan ditemukan
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Pagination --}}
        @if($customers->hasPages())
        <div class="flex items-center justify-between px-5 py-3 border-t border-gray-100">
            <p class="text-sm text-gray-500">
                Menampilkan {{ $customers->firstItem() }}–{{ $customers->lastItem() }} dari {{ $customers->total() }}
            </p>
            <div class="flex items-center gap-1">
                @if($customers->onFirstPage())
                <span class="px-3 py-1.5 text-sm text-gray-400 border border-gray-200 rounded cursor-not-allowed">Sebelumnya</span>
                @else
                <button wire:click="previousPage" class="px-3 py-1.5 text-sm text-gray-700 border border-gray-300 rounded hover:bg-gray-50">Sebelumnya</button>
                @endif

                @foreach($customers->getUrlRange(max(1, $customers->currentPage()-2), min($customers->lastPage(), $customers->currentPage()+2)) as $page => $url)
                <button wire:click="gotoPage({{ $page }})"
                        class="px-3 py-1.5 text-sm rounded {{ $page === $customers->currentPage() ? 'bg-red-600 text-white' : 'text-gray-700 border border-gray-300 hover:bg-gray-50' }}">
                    {{ $page }}
                </button>
                @endforeach

                @if($customers->hasMorePages())
                <button wire:click="nextPage" class="px-3 py-1.5 text-sm text-gray-700 border border-gray-300 rounded hover:bg-gray-50">Selanjutnya</button>
                @else
                <span class="px-3 py-1.5 text-sm text-gray-400 border border-gray-200 rounded cursor-not-allowed">Selanjutnya</span>
                @endif
            </div>
        </div>
        @endif
    </div>

</div>
