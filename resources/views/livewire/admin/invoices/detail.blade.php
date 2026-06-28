<div>
    {{-- Page header --}}
    <div class="mb-6 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a wire:navigate href="{{ route('admin.invoices.index') }}" class="text-gray-400 hover:text-gray-600 transition">
                <x-heroicon-o-arrow-left class="w-5 h-5" />
            </a>
            <div>
                <h1 class="text-xl font-bold text-gray-900 font-mono">{{ $invoice->invoice_number }}</h1>
                <p class="text-sm text-gray-500 mt-0.5">{{ $invoice->tanggal->translatedFormat('d F Y') }}</p>
            </div>
        </div>
        @php
            $payColor = match($invoice->payment_status) {
                'paid'    => 'bg-green-100 text-green-700',
                'partial' => 'bg-amber-100 text-amber-700',
                'unpaid'  => 'bg-red-100 text-red-700',
                'voided'  => 'bg-gray-200 text-gray-500 line-through',
                default   => 'bg-gray-100 text-gray-500',
            };
            $payLabel = match($invoice->payment_status) {
                'paid'    => 'Lunas',
                'partial' => 'Sebagian',
                'unpaid'  => 'Belum Bayar',
                'voided'  => 'Void',
                default   => $invoice->payment_status,
            };
        @endphp
        <div class="flex items-center gap-3">
            <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium {{ $payColor }}">
                {{ $payLabel }}
            </span>
            @if(auth('admin')->user()?->isSuperAdmin() && $invoice->payment_status !== 'voided')
            <button wire:click="voidInvoice"
                    wire:confirm="Void invoice ini? Stok sparepart akan dikembalikan ke gudang dan tindakan ini tidak dapat dibatalkan."
                    class="flex items-center gap-1.5 border border-red-300 text-red-600 hover:bg-red-50 text-xs font-medium px-3 py-1.5 rounded-lg transition">
                <x-heroicon-o-x-circle class="w-3.5 h-3.5" />
                Void Invoice
            </button>
            @endif
        </div>
    </div>

    @if(session('success'))
    <div class="mb-4 px-4 py-3 bg-green-50 border border-green-200 text-green-700 text-sm rounded-lg">
        {{ session('success') }}
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Kolom kiri: items --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Item Invoice --}}
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wide">Item Invoice</h2>
                </div>
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-xs font-semibold uppercase tracking-wider text-gray-500 border-b border-gray-200">
                            <th class="text-left px-6 py-3">Nama Item</th>
                            <th class="text-left px-6 py-3">Tipe</th>
                            <th class="text-right px-6 py-3">Qty</th>
                            <th class="text-right px-6 py-3">Harga Jual</th>
                            <th class="text-right px-6 py-3">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($invoice->items as $item)
                        <tr class="border-b border-gray-100">
                            <td class="px-6 py-3 font-medium text-gray-900">{{ $item->nama_snapshot }}</td>
                            <td class="px-6 py-3">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $item->type === 'jasa' ? 'bg-blue-50 text-blue-600' : 'bg-orange-50 text-orange-600' }}">
                                    {{ ucfirst($item->type) }}
                                </span>
                            </td>
                            <td class="px-6 py-3 text-right text-gray-600">{{ $item->qty }}</td>
                            <td class="px-6 py-3 text-right text-gray-600">Rp {{ number_format($item->harga_jual, 0, ',', '.') }}</td>
                            <td class="px-6 py-3 text-right font-medium text-gray-900">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="border-t border-gray-100 text-sm">
                            <td colspan="4" class="px-6 py-3 text-right text-gray-500">Subtotal</td>
                            <td class="px-6 py-3 text-right text-gray-700">Rp {{ number_format($invoice->subtotal, 0, ',', '.') }}</td>
                        </tr>
                        @if($invoice->discount > 0)
                        <tr class="text-sm">
                            <td colspan="4" class="px-6 py-2 text-right text-gray-500">Diskon</td>
                            <td class="px-6 py-2 text-right text-red-600">- Rp {{ number_format($invoice->discount, 0, ',', '.') }}</td>
                        </tr>
                        @endif
                        <tr class="border-t border-gray-200">
                            <td colspan="4" class="px-6 py-3 text-right font-semibold text-gray-700">Grand Total</td>
                            <td class="px-6 py-3 text-right font-bold text-gray-900 text-base">Rp {{ number_format($invoice->grand_total, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td colspan="4" class="px-6 py-2 text-right text-gray-500 text-sm">Terbayar</td>
                            <td class="px-6 py-2 text-right text-green-600 text-sm">Rp {{ number_format($invoice->amount_paid, 0, ',', '.') }}</td>
                        </tr>
                        @php $sisa = $invoice->grand_total - $invoice->amount_paid; @endphp
                        @if($sisa > 0)
                        <tr>
                            <td colspan="4" class="px-6 py-2 text-right text-gray-500 text-sm">Sisa Tagihan</td>
                            <td class="px-6 py-2 text-right text-red-600 font-semibold text-sm">Rp {{ number_format($sisa, 0, ',', '.') }}</td>
                        </tr>
                        @endif
                    </tfoot>
                </table>
            </div>

            {{-- Riwayat Pembayaran --}}
            @if($invoice->payments->isNotEmpty())
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wide">Riwayat Pembayaran</h2>
                </div>
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-xs font-semibold uppercase tracking-wider text-gray-500 border-b border-gray-200">
                            <th class="text-left px-6 py-3">Tanggal</th>
                            <th class="text-left px-6 py-3">Metode</th>
                            <th class="text-right px-6 py-3">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($invoice->payments as $payment)
                        <tr class="border-b border-gray-100">
                            <td class="px-6 py-3 text-gray-600">{{ $payment->created_at->translatedFormat('d M Y, H:i') }}</td>
                            <td class="px-6 py-3 text-gray-600 capitalize">{{ $payment->payment_method ?? '-' }}</td>
                            <td class="px-6 py-3 text-right font-medium text-gray-900">Rp {{ number_format($payment->amount, 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif

            {{-- Catatan --}}
            @if($invoice->catatan)
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h2 class="text-sm font-semibold text-gray-700 mb-2 uppercase tracking-wide">Catatan</h2>
                <p class="text-sm text-gray-600">{{ $invoice->catatan }}</p>
            </div>
            @endif
        </div>

        {{-- Kolom kanan: info --}}
        <div class="space-y-6">

            {{-- Info Customer & Kendaraan --}}
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h2 class="text-sm font-semibold text-gray-700 mb-4 uppercase tracking-wide">Informasi</h2>
                <dl class="space-y-3 text-sm">
                    @if($invoice->customer)
                    <div>
                        <dt class="text-gray-400 text-xs mb-0.5">Customer</dt>
                        <dd class="font-medium text-gray-900">{{ $invoice->customer->nama }}</dd>
                        <dd class="text-gray-500 text-xs">{{ $invoice->customer->no_hp }}</dd>
                    </div>
                    @endif

                    @if($invoice->vehicle)
                    <div>
                        <dt class="text-gray-400 text-xs mb-0.5">Kendaraan</dt>
                        <dd class="font-medium text-gray-900">
                            {{ $invoice->vehicle->merk }} {{ $invoice->vehicle->model }} ({{ $invoice->vehicle->tahun }})
                        </dd>
                        <dd class="font-mono text-xs text-gray-500">{{ $invoice->vehicle->plat_nomor }}</dd>
                    </div>
                    @endif

                    <div>
                        <dt class="text-gray-400 text-xs mb-0.5">Tipe Invoice</dt>
                        <dd class="capitalize text-gray-700">{{ $invoice->tipe }}</dd>
                    </div>

                    <div>
                        <dt class="text-gray-400 text-xs mb-0.5">Dibuat oleh</dt>
                        <dd class="text-gray-700">{{ $invoice->user?->name ?? '-' }}</dd>
                    </div>

                    @if($invoice->workOrder)
                    <div>
                        <dt class="text-gray-400 text-xs mb-0.5">Work Order</dt>
                        <dd>
                            <a wire:navigate href="{{ route('admin.work-orders.show', $invoice->workOrder) }}"
                               class="font-mono text-red-600 hover:underline text-sm">
                                {{ $invoice->workOrder->wo_number }}
                            </a>
                        </dd>
                    </div>
                    @endif
                </dl>
            </div>
        </div>
    </div>
</div>
