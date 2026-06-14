<div>
    {{-- Flash message --}}
    @if(session('success'))
    <div class="mb-4 px-4 py-3 bg-green-50 border border-green-200 text-green-700 text-sm rounded-lg flex items-center gap-2">
        <x-heroicon-o-check-circle class="w-4 h-4 flex-shrink-0" />
        {{ session('success') }}
    </div>
    @endif

    {{-- Page header --}}
    <div class="mb-6 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a wire:navigate href="{{ route('admin.work-orders.index') }}" class="text-gray-400 hover:text-gray-600 transition">
                <x-heroicon-o-arrow-left class="w-5 h-5" />
            </a>
            <div>
                <h1 class="text-xl font-bold text-gray-900 font-mono">{{ $workOrder->wo_number }}</h1>
                <p class="text-sm text-gray-500 mt-0.5">Detail Work Order</p>
            </div>
        </div>
        @php
            $statusColor = match($workOrder->status) {
                'pending'     => 'bg-amber-100 text-amber-700',
                'in_progress' => 'bg-blue-100 text-blue-700',
                'done'        => 'bg-green-100 text-green-700',
                'cancelled'   => 'bg-gray-100 text-gray-500',
                default       => 'bg-gray-100 text-gray-500',
            };
            $statusLabel = match($workOrder->status) {
                'pending'     => 'Pending',
                'in_progress' => 'Sedang Dikerjakan',
                'done'        => 'Selesai',
                'cancelled'   => 'Dibatalkan',
                default       => $workOrder->status,
            };
        @endphp
        <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium {{ $statusColor }}">
            {{ $statusLabel }}
        </span>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Kolom kiri: info kendaraan + invoice items --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Info Kendaraan & Customer --}}
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h2 class="text-sm font-semibold text-gray-700 mb-4 uppercase tracking-wide">Informasi Kendaraan</h2>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <p class="text-gray-400 text-xs mb-0.5">Customer</p>
                        <p class="font-medium text-gray-900">{{ $workOrder->vehicle?->customer?->nama ?? '-' }}</p>
                        <p class="text-gray-500 text-xs">{{ $workOrder->vehicle?->customer?->no_hp }}</p>
                    </div>
                    <div>
                        <p class="text-gray-400 text-xs mb-0.5">Kendaraan</p>
                        <p class="font-medium text-gray-900">
                            {{ $workOrder->vehicle?->merk }} {{ $workOrder->vehicle?->model }} ({{ $workOrder->vehicle?->tahun }})
                        </p>
                        <p class="font-mono text-xs text-gray-500">{{ $workOrder->vehicle?->plat_nomor }}</p>
                    </div>
                    <div>
                        <p class="text-gray-400 text-xs mb-0.5">Mulai Dikerjakan</p>
                        <p class="text-gray-700">{{ $workOrder->mulai_at?->translatedFormat('d M Y, H:i') ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-400 text-xs mb-0.5">Selesai</p>
                        <p class="text-gray-700">{{ $workOrder->selesai_at?->translatedFormat('d M Y, H:i') ?? '-' }}</p>
                    </div>
                </div>

                @if($workOrder->keluhan_customer)
                <div class="mt-4 pt-4 border-t border-gray-100">
                    <p class="text-gray-400 text-xs mb-1">Keluhan Customer</p>
                    <p class="text-sm text-gray-700">{{ $workOrder->keluhan_customer }}</p>
                </div>
                @endif
            </div>

            {{-- Item Invoice --}}
            @if($workOrder->invoice)
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wide">Item Pekerjaan</h2>
                    <a wire:navigate href="{{ route('admin.invoices.show', $workOrder->invoice) }}"
                       class="text-xs text-red-600 hover:underline flex items-center gap-1">
                        <x-heroicon-o-document-text class="w-3.5 h-3.5" />
                        Lihat Invoice {{ $workOrder->invoice->invoice_number }}
                    </a>
                </div>
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-xs font-semibold uppercase tracking-wider text-gray-500 border-b border-gray-200">
                            <th class="text-left px-6 py-3">Item</th>
                            <th class="text-left px-6 py-3">Tipe</th>
                            <th class="text-right px-6 py-3">Qty</th>
                            <th class="text-right px-6 py-3">Harga</th>
                            <th class="text-right px-6 py-3">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($workOrder->invoice->items as $item)
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
                        <tr class="border-t border-gray-200">
                            <td colspan="4" class="px-6 py-3 text-right text-sm font-semibold text-gray-700">Grand Total</td>
                            <td class="px-6 py-3 text-right text-sm font-bold text-gray-900">
                                Rp {{ number_format($workOrder->invoice->grand_total, 0, ',', '.') }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            @endif
        </div>

        {{-- Kolom kanan: form update status & mekanik --}}
        <div class="space-y-6">

            {{-- Form Update Status --}}
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h2 class="text-sm font-semibold text-gray-700 mb-4 uppercase tracking-wide">Update Status</h2>

                <form wire:submit="save" class="space-y-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Mekanik</label>
                        <select wire:model="mekanikId"
                                class="w-full border border-gray-300 text-sm px-3 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500">
                            <option value="">— Belum ditugaskan —</option>
                            @foreach($mekanikList as $mekanik)
                                <option value="{{ $mekanik->id }}">{{ $mekanik->name }}</option>
                            @endforeach
                        </select>
                        @error('mekanikId') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Status</label>
                        <select wire:model="status"
                                class="w-full border border-gray-300 text-sm px-3 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500">
                            @foreach($statusList as $s)
                                <option value="{{ $s }}">{{ ucfirst(str_replace('_', ' ', $s)) }}</option>
                            @endforeach
                        </select>
                        @error('status') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Catatan Mekanik</label>
                        <textarea wire:model="catatanMekanik"
                                  rows="4"
                                  placeholder="Temuan & catatan pengerjaan..."
                                  class="w-full border border-gray-300 text-sm px-3 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 resize-none"></textarea>
                        @error('catatanMekanik') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <button type="submit"
                            class="w-full bg-red-600 hover:bg-red-700 text-white text-sm font-medium py-2 px-4 rounded-lg transition flex items-center justify-center gap-2">
                        <div wire:loading wire:target="save" class="w-3.5 h-3.5 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
                        <span wire:loading.remove wire:target="save">
                            <x-heroicon-o-check class="w-4 h-4 inline -mt-0.5" /> Simpan
                        </span>
                        <span wire:loading wire:target="save">Menyimpan...</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
