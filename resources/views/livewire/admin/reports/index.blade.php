<div>
    {{-- Filter Bar --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 mb-6">
        <div class="flex flex-wrap gap-4 items-end">
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Dari Tanggal</label>
                <input wire:model.live="dateFrom" type="date"
                    class="text-sm border border-gray-200 rounded-xl px-3 py-2 focus:ring-2 focus:ring-red-500/20 focus:border-red-400 outline-none" />
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Sampai Tanggal</label>
                <input wire:model.live="dateTo" type="date"
                    class="text-sm border border-gray-200 rounded-xl px-3 py-2 focus:ring-2 focus:ring-red-500/20 focus:border-red-400 outline-none" />
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Tipe</label>
                <select wire:model.live="filterTipe"
                    class="text-sm border border-gray-200 rounded-xl px-3 py-2 focus:ring-2 focus:ring-red-500/20 focus:border-red-400 outline-none bg-white">
                    <option value="">Semua</option>
                    <option value="bengkel">Bengkel</option>
                    <option value="online">Online Shop</option>
                </select>
            </div>
        </div>
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-9 h-9 rounded-xl bg-red-50 flex items-center justify-center">
                    <x-heroicon-o-banknotes class="w-5 h-5 text-red-600" />
                </div>
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Total Pendapatan</p>
            </div>
            <p class="text-2xl font-bold text-gray-900">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</p>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-9 h-9 rounded-xl bg-blue-50 flex items-center justify-center">
                    <x-heroicon-o-shopping-cart class="w-5 h-5 text-blue-600" />
                </div>
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Total Transaksi</p>
            </div>
            <p class="text-2xl font-bold text-gray-900">{{ number_format($jumlahTransaksi) }}</p>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-9 h-9 rounded-xl bg-orange-50 flex items-center justify-center">
                    <x-heroicon-o-arrow-trending-down class="w-5 h-5 text-orange-500" />
                </div>
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Total HPP</p>
            </div>
            <p class="text-2xl font-bold text-gray-900">Rp {{ number_format($totalHpp, 0, ',', '.') }}</p>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-9 h-9 rounded-xl bg-green-50 flex items-center justify-center">
                    <x-heroicon-o-arrow-trending-up class="w-5 h-5 text-green-600" />
                </div>
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Gross Margin</p>
            </div>
            <p class="text-2xl font-bold {{ $grossMargin >= 0 ? 'text-green-600' : 'text-red-600' }}">
                {{ $grossMargin }}%
            </p>
        </div>
    </div>

    {{-- Trend + Top Tables --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Tren Pendapatan --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <h3 class="text-sm font-semibold text-gray-700 mb-4">Tren Pendapatan 6 Bulan</h3>
            @if($trenBulanan->isEmpty())
                <p class="text-sm text-gray-400 text-center py-8">Belum ada data</p>
            @else
                @php $maxVal = $trenBulanan->max('total') ?: 1; @endphp
                <div class="space-y-3">
                    @foreach($trenBulanan as $tren)
                        <div class="flex items-center gap-3">
                            <span class="text-xs text-gray-500 w-16 flex-none">
                                {{ \Carbon\Carbon::createFromFormat('Y-m', $tren->bulan)->format('M Y') }}
                            </span>
                            <div class="flex-1 h-2 bg-gray-100 rounded-full overflow-hidden">
                                <div class="h-full bg-red-500 rounded-full"
                                    style="width: {{ round($tren->total / $maxVal * 100) }}%"></div>
                            </div>
                            <span class="text-xs font-semibold text-gray-700 w-28 text-right flex-none">
                                Rp {{ number_format($tren->total, 0, ',', '.') }}
                            </span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Top Services --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <h3 class="text-sm font-semibold text-gray-700 mb-4">Top Servis (Revenue)</h3>
            @if($topServices->isEmpty())
                <p class="text-sm text-gray-400 text-center py-8">Belum ada data</p>
            @else
                <div class="space-y-3">
                    @foreach($topServices as $i => $item)
                        <div class="flex items-center gap-3">
                            <span class="w-6 h-6 rounded-lg bg-red-50 text-red-600 text-xs font-bold flex items-center justify-center flex-none">
                                {{ $i + 1 }}
                            </span>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 truncate">{{ $item->service?->nama_service ?? '—' }}</p>
                            </div>
                            <span class="text-sm font-semibold text-gray-900 flex-none">
                                Rp {{ number_format($item->total_revenue, 0, ',', '.') }}
                            </span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Top Spareparts --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 lg:col-span-2">
            <h3 class="text-sm font-semibold text-gray-700 mb-4">Top Sparepart (Qty Terjual)</h3>
            @if($topSpareparts->isEmpty())
                <p class="text-sm text-gray-400 text-center py-8">Belum ada data</p>
            @else
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-100">
                            <th class="text-left pb-3 font-semibold text-gray-600">#</th>
                            <th class="text-left pb-3 font-semibold text-gray-600">Nama Sparepart</th>
                            <th class="text-left pb-3 font-semibold text-gray-600">SKU</th>
                            <th class="text-right pb-3 font-semibold text-gray-600">Qty</th>
                            <th class="text-right pb-3 font-semibold text-gray-600">Revenue</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($topSpareparts as $i => $item)
                            <tr>
                                <td class="py-3 text-gray-400 text-xs">{{ $i + 1 }}</td>
                                <td class="py-3 font-medium text-gray-900">{{ $item->sparepart?->item_name ?? '—' }}</td>
                                <td class="py-3 text-gray-500 font-mono text-xs">{{ $item->sparepart?->sku ?? '—' }}</td>
                                <td class="py-3 text-right font-mono text-gray-700">{{ $item->total_qty }}</td>
                                <td class="py-3 text-right font-semibold text-gray-900">
                                    Rp {{ number_format($item->total_revenue, 0, ',', '.') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
</div>
