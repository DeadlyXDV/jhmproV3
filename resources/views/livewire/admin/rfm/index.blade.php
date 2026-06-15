<div>
    {{-- Source Tabs --}}
    <div class="flex gap-1 mb-6 bg-white rounded-2xl border border-gray-100 p-1 w-fit shadow-sm">
        @foreach(['bengkel' => 'Bengkel', 'online' => 'Online Shop', 'combined' => 'Combined'] as $key => $label)
            <button wire:click="$set('activeSource', '{{ $key }}')"
                class="px-5 py-2 text-sm font-semibold rounded-xl transition-colors
                    {{ $activeSource === $key ? 'bg-red-600 text-white shadow' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-50' }}">
                {{ $label }}
            </button>
        @endforeach
    </div>

    {{-- Stat Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Total Customer</p>
            <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total']) }}</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Avg. Monetary</p>
            <p class="text-2xl font-bold text-gray-900">Rp {{ number_format($stats['avg_monetary'], 0, ',', '.') }}</p>
        </div>
        @foreach($clusters->take(2) as $cluster)
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">{{ $cluster->label }}</p>
                <p class="text-2xl font-bold" style="color: {{ $cluster->color_hex }}">
                    {{ $clusterStats[$cluster->label] ?? 0 }}
                </p>
            </div>
        @endforeach
    </div>

    {{-- Cluster Distribution --}}
    @if($clusters->isNotEmpty())
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mb-6">
            <h3 class="text-sm font-semibold text-gray-700 mb-4">Distribusi Segmen</h3>
            <div class="flex flex-wrap gap-3">
                @foreach($clusters as $cluster)
                    @php $count = $clusterStats[$cluster->label] ?? 0; @endphp
                    <button wire:click="$set('filterCluster', '{{ $filterCluster === $cluster->label ? '' : $cluster->label }}')"
                        class="flex items-center gap-2 px-4 py-2.5 rounded-xl border-2 text-sm font-semibold transition-all
                            {{ $filterCluster === $cluster->label ? 'shadow-md' : 'border-gray-200 hover:border-gray-300' }}"
                        style="{{ $filterCluster === $cluster->label ? 'border-color: ' . $cluster->color_hex . '; background-color: ' . $cluster->color_hex . '18;' : '' }}">
                        <span class="w-2.5 h-2.5 rounded-full flex-none" style="background-color: {{ $cluster->color_hex }}"></span>
                        <span style="{{ $filterCluster === $cluster->label ? 'color: ' . $cluster->color_hex : '' }}">
                            {{ $cluster->label }}
                        </span>
                        <span class="text-xs font-bold px-1.5 py-0.5 rounded-md"
                            style="background-color: {{ $cluster->color_hex }}22; color: {{ $cluster->color_hex }}">
                            {{ $count }}
                        </span>
                    </button>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Table --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <div class="flex items-center gap-3">
                <h3 class="text-sm font-semibold text-gray-700">
                    Daftar Customer
                    @if($filterCluster) — <span class="text-red-600">{{ $filterCluster }}</span> @endif
                </h3>
                @if($latestCalculatedAt)
                    <span class="text-xs text-gray-400">
                        Terakhir dihitung: {{ \Carbon\Carbon::parse($latestCalculatedAt)->format('d M Y H:i') }}
                    </span>
                @endif
            </div>
            <div class="relative">
                <x-heroicon-o-magnifying-glass class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" />
                <input wire:model.live.debounce.300ms="search"
                    type="text"
                    placeholder="Cari nama customer..."
                    class="pl-10 pr-4 py-2 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-red-500/20 focus:border-red-400 outline-none" />
            </div>
        </div>

        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-100 bg-gray-50/50">
                    <th class="text-left px-6 py-3.5 font-semibold text-gray-600">Customer</th>
                    <th class="text-right px-6 py-3.5 font-semibold text-gray-600">Recency (hari)</th>
                    <th class="text-right px-6 py-3.5 font-semibold text-gray-600">Frequency</th>
                    <th class="text-right px-6 py-3.5 font-semibold text-gray-600">Monetary</th>
                    <th class="text-right px-6 py-3.5 font-semibold text-gray-600">RFM Score</th>
                    <th class="text-left px-6 py-3.5 font-semibold text-gray-600">Segmen</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($rows as $row)
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-6 py-4">
                            <p class="font-medium text-gray-900">{{ $row->customer?->nama ?? '—' }}</p>
                            <p class="text-xs text-gray-400">{{ $row->customer?->no_hp }}</p>
                        </td>
                        <td class="px-6 py-4 text-right font-mono text-gray-700">{{ $row->recency_days }}</td>
                        <td class="px-6 py-4 text-right font-mono text-gray-700">{{ $row->frequency }}</td>
                        <td class="px-6 py-4 text-right font-semibold text-gray-900">
                            Rp {{ number_format($row->monetary, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 text-right font-bold text-gray-900">{{ number_format($row->rfm_score, 2) }}</td>
                        <td class="px-6 py-4">
                            @php
                                $clusterDef = $clusters->firstWhere('label', $row->cluster_label);
                            @endphp
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold"
                                style="background-color: {{ ($clusterDef?->color_hex ?? '#6b7280') . '22' }}; color: {{ $clusterDef?->color_hex ?? '#6b7280' }}">
                                <span class="w-1.5 h-1.5 rounded-full" style="background-color: {{ $clusterDef?->color_hex ?? '#6b7280' }}"></span>
                                {{ $row->cluster_label }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-16 text-center">
                            <x-heroicon-o-chart-bar class="w-10 h-10 text-gray-300 mx-auto mb-3" />
                            <p class="text-gray-400 text-sm">Belum ada data RFM untuk sumber ini</p>
                            <p class="text-gray-400 text-xs mt-1">Jalankan job RFM terlebih dahulu</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if($rows->hasPages())
            <div class="px-6 py-4 border-t border-gray-100">
                {{ $rows->links() }}
            </div>
        @endif
    </div>
</div>
