<div>
    @assets
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"
            integrity="sha384-e6nUZLBkQ86NJ6TVVKAeSaK8jWa3NhkYWZFomE39AvDbQWeie9PlQqM3pmYW5d1g"
            crossorigin="anonymous"></script>
    @endassets

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

    {{-- Visualisasi Scatter Plot K-Means --}}
    @if($stats['total'] > 0)
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mb-6">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-2">
                    <x-heroicon-o-chart-bar class="w-5 h-5 text-gray-400" />
                    <h3 class="text-sm font-semibold text-gray-700">Visualisasi Cluster K-Means</h3>
                </div>
                <span class="text-xs text-gray-400">Sumbu X = Recency Score (R) &middot; Sumbu Y = Frequency Score (F)</span>
            </div>
            <div wire:key="scatter-{{ $activeSource }}"
                 wire:ignore
                 x-data="{
                     init() {
                         new Chart(this.$refs.canvas, {
                             type: 'scatter',
                             data: { datasets: @js($scatterDatasets) },
                             options: {
                                 responsive: true,
                                 maintainAspectRatio: false,
                                 scales: {
                                     x: {
                                         min: 0.5, max: 5.5,
                                         title: { display: true, text: 'Recency Score (R)', font: { size: 11 } },
                                         ticks: { stepSize: 1, callback: (v) => ['', 'R1', 'R2', 'R3', 'R4', 'R5'][v] ?? v },
                                         grid: { color: '#f3f4f6' }
                                     },
                                     y: {
                                         min: 0.5, max: 5.5,
                                         title: { display: true, text: 'Frequency Score (F)', font: { size: 11 } },
                                         ticks: { stepSize: 1, callback: (v) => ['', 'F1', 'F2', 'F3', 'F4', 'F5'][v] ?? v },
                                         grid: { color: '#f3f4f6' }
                                     }
                                 },
                                 plugins: {
                                     legend: {
                                         position: 'bottom',
                                         labels: { usePointStyle: true, padding: 20, font: { size: 12 } }
                                     },
                                     tooltip: {
                                         callbacks: {
                                             label: (ctx) => ` ${ctx.dataset.label}  —  R = ${ctx.raw.x}, F = ${ctx.raw.y}`
                                         }
                                     }
                                 }
                             }
                         });
                     }
                 }">
                <div style="height: 320px">
                    <canvas x-ref="canvas"></canvas>
                </div>
            </div>
        </div>
    @endif

    {{-- Analitik Cluster K-Means --}}
    @if($clusters->isNotEmpty())
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mb-6">
            <div class="flex items-center gap-2 mb-5">
                <x-heroicon-o-cpu-chip class="w-5 h-5 text-gray-400" />
                <h3 class="text-sm font-semibold text-gray-700">Analitik Cluster K-Means</h3>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                @foreach($clusters as $cluster)
                    @php $stat = $clusterAnalytics->get($cluster->label); @endphp
                    <div class="rounded-xl border-2 p-4" style="border-color: {{ $cluster->color_hex }}33; background-color: {{ $cluster->color_hex }}08">
                        {{-- Header --}}
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center gap-1.5">
                                <span class="w-2.5 h-2.5 rounded-full flex-none" style="background-color: {{ $cluster->color_hex }}"></span>
                                <span class="text-sm font-bold" style="color: {{ $cluster->color_hex }}">{{ $cluster->label }}</span>
                            </div>
                            <span class="text-xs font-bold px-2 py-0.5 rounded-full"
                                style="background-color: {{ $cluster->color_hex }}22; color: {{ $cluster->color_hex }}">
                                {{ $stat?->total ?? 0 }}
                            </span>
                        </div>

                        {{-- Centroid K-Means --}}
                        <div class="mb-3 p-2.5 rounded-lg bg-white/70">
                            <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider mb-1.5">Centroid K-Means</p>
                            <div class="grid grid-cols-3 gap-1 text-center">
                                @foreach(['R', 'F', 'M'] as $i => $dim)
                                    <div>
                                        <p class="text-[10px] text-gray-400">{{ $dim }}</p>
                                        <p class="text-sm font-bold text-gray-800">
                                            {{ $cluster->centroid ? number_format($cluster->centroid[$i], 2) : '—' }}
                                        </p>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        {{-- Avg Stats --}}
                        <div class="space-y-1.5 mb-3 text-xs">
                            <div class="flex justify-between">
                                <span class="text-gray-400">Avg Recency</span>
                                <span class="font-semibold text-gray-700">{{ $stat ? number_format($stat->avg_recency, 0).' hari' : '—' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-400">Avg Frequency</span>
                                <span class="font-semibold text-gray-700">{{ $stat ? number_format($stat->avg_freq, 1).'x' : '—' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-400">Avg Monetary</span>
                                <span class="font-semibold text-gray-700">Rp {{ $stat ? number_format($stat->avg_monetary, 0, ',', '.') : '—' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-400">Avg R/F/M</span>
                                <span class="font-semibold text-gray-700">
                                    {{ $stat ? $stat->avg_r.'/'.$stat->avg_f.'/'.$stat->avg_m : '—' }}
                                </span>
                            </div>
                        </div>

                        {{-- Action Suggestion --}}
                        @if($cluster->action_suggestion)
                            <div class="pt-2 border-t border-gray-100">
                                <p class="text-[10px] text-gray-500 italic leading-relaxed">{{ $cluster->action_suggestion }}</p>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Tren Segmentasi per Bulan --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden mb-6">
        <div class="flex items-center gap-2 px-6 py-4 border-b border-gray-100">
            <x-heroicon-o-arrow-trending-up class="w-5 h-5 text-gray-400" />
            <h3 class="text-sm font-semibold text-gray-700">Tren Segmentasi per Bulan</h3>
        </div>
        @if($trendMonths->isEmpty())
            <div class="px-6 py-10 text-center">
                <x-heroicon-o-calendar-days class="w-8 h-8 text-gray-300 mx-auto mb-2" />
                <p class="text-gray-400 text-sm">Belum ada data historis</p>
                <p class="text-gray-400 text-xs mt-1">Jalankan <code class="bg-gray-100 px-1.5 py-0.5 rounded text-xs font-mono">rfm:calculate</code> untuk mengisi tren</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-100 bg-gray-50/50">
                            <th class="text-left px-6 py-3 font-semibold text-gray-600 whitespace-nowrap">Bulan</th>
                            @foreach($clusters as $cluster)
                                <th class="text-center px-4 py-3 font-semibold whitespace-nowrap"
                                    style="color: {{ $cluster->color_hex }}">
                                    {{ $cluster->label }}
                                </th>
                            @endforeach
                            <th class="text-center px-4 py-3 font-semibold text-gray-600">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($trendMonths as $yearMonth => $items)
                            @php
                                $monthData = $items->pluck('total', 'cluster_label');
                                $monthTotal = $monthData->sum();
                            @endphp
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-6 py-3 font-medium text-gray-700 whitespace-nowrap">
                                    {{ \Carbon\Carbon::createFromFormat('Y-m', $yearMonth)->format('M Y') }}
                                </td>
                                @foreach($clusters as $cluster)
                                    @php $count = $monthData->get($cluster->label, 0); @endphp
                                    <td class="px-4 py-3 text-center">
                                        @if($count > 0)
                                            <span class="inline-flex items-center justify-center min-w-[2rem] px-2 py-0.5 rounded text-xs font-bold"
                                                style="background-color: {{ $cluster->color_hex }}22; color: {{ $cluster->color_hex }}">
                                                {{ $count }}
                                            </span>
                                        @else
                                            <span class="text-gray-300 text-xs">—</span>
                                        @endif
                                    </td>
                                @endforeach
                                <td class="px-4 py-3 text-center font-semibold text-gray-700">{{ $monthTotal }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

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
