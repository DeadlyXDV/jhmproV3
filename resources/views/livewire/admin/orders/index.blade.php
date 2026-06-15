<div>
    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <p class="text-sm text-gray-500">Daftar pesanan online shop</p>
        </div>
    </div>

    {{-- Filter Bar --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 mb-6">
        <div class="flex flex-wrap gap-3">
            <div class="relative flex-1 min-w-[200px]">
                <x-heroicon-o-magnifying-glass class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" />
                <input wire:model.live.debounce.300ms="search"
                    type="text"
                    placeholder="Cari no. order atau nama penerima..."
                    class="w-full pl-10 pr-4 py-2 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-red-500/20 focus:border-red-400 outline-none" />
            </div>

            <select wire:model.live="filterStatus"
                class="text-sm border border-gray-200 rounded-xl px-3 py-2 focus:ring-2 focus:ring-red-500/20 focus:border-red-400 outline-none bg-white">
                <option value="">Semua Status Order</option>
                @foreach($statusList as $s)
                    <option value="{{ $s }}">{{ ucfirst($s) }}</option>
                @endforeach
            </select>

            <select wire:model.live="filterPayment"
                class="text-sm border border-gray-200 rounded-xl px-3 py-2 focus:ring-2 focus:ring-red-500/20 focus:border-red-400 outline-none bg-white">
                <option value="">Semua Status Bayar</option>
                @foreach($paymentList as $p)
                    <option value="{{ $p }}">{{ ucfirst($p) }}</option>
                @endforeach
            </select>
        </div>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-100 bg-gray-50/50">
                    <th class="text-left px-6 py-3.5 font-semibold text-gray-600">No. Order</th>
                    <th class="text-left px-6 py-3.5 font-semibold text-gray-600">Penerima</th>
                    <th class="text-left px-6 py-3.5 font-semibold text-gray-600">Total</th>
                    <th class="text-left px-6 py-3.5 font-semibold text-gray-600">Status Order</th>
                    <th class="text-left px-6 py-3.5 font-semibold text-gray-600">Status Bayar</th>
                    <th class="text-left px-6 py-3.5 font-semibold text-gray-600">Kurir / Resi</th>
                    <th class="text-left px-6 py-3.5 font-semibold text-gray-600">Tanggal</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($orders as $order)
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-6 py-4">
                            <span class="font-mono text-xs font-bold text-red-600">{{ $order->order_number }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <p class="font-medium text-gray-900">{{ $order->nama_penerima }}</p>
                            @if($order->customer)
                                <p class="text-xs text-gray-400">{{ $order->customer->nama }}</p>
                            @else
                                <p class="text-xs text-gray-400">Guest</p>
                            @endif
                        </td>
                        <td class="px-6 py-4 font-semibold text-gray-900">
                            Rp {{ number_format($order->grand_total, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4">
                            @php
                                $statusColor = match($order->status) {
                                    'pending' => 'bg-yellow-100 text-yellow-700',
                                    'processing' => 'bg-blue-100 text-blue-700',
                                    'shipped' => 'bg-purple-100 text-purple-700',
                                    'delivered' => 'bg-green-100 text-green-700',
                                    'cancelled' => 'bg-red-100 text-red-600',
                                    default => 'bg-gray-100 text-gray-600',
                                };
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold {{ $statusColor }}">
                                {{ ucfirst($order->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            @php
                                $payColor = match($order->payment_status) {
                                    'paid' => 'bg-green-100 text-green-700',
                                    'unpaid' => 'bg-yellow-100 text-yellow-700',
                                    'refunded' => 'bg-gray-100 text-gray-600',
                                    default => 'bg-gray-100 text-gray-600',
                                };
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold {{ $payColor }}">
                                {{ ucfirst($order->payment_status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-gray-500 text-xs">
                            @if($order->shipment)
                                <p class="font-medium text-gray-700">{{ strtoupper($order->shipment->courier) }}</p>
                                <p class="font-mono">{{ $order->shipment->tracking_number ?? '—' }}</p>
                            @else
                                <span class="text-gray-400">—</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-gray-400 text-xs whitespace-nowrap">
                            {{ $order->created_at->format('d M Y') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-16 text-center">
                            <x-heroicon-o-shopping-bag class="w-10 h-10 text-gray-300 mx-auto mb-3" />
                            <p class="text-gray-400 text-sm">Belum ada order</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if($orders->hasPages())
            <div class="px-6 py-4 border-t border-gray-100">
                {{ $orders->links() }}
            </div>
        @endif
    </div>
</div>
