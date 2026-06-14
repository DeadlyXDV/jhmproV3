<div>
    {{-- Flash message --}}
    @if(session('success'))
    <div class="mb-4 px-4 py-3 bg-green-50 border border-green-200 text-green-700 text-sm rounded-lg flex items-center gap-2">
        <x-heroicon-o-check-circle class="w-4 h-4 flex-shrink-0" />
        {{ session('success') }}
    </div>
    @endif

    {{-- Page header --}}
    <div class="mb-6 flex items-center gap-3">
        <a href="{{ route('mekanik.work-orders.index') }}" class="text-gray-400 hover:text-gray-600 transition">
            <x-heroicon-o-arrow-left class="w-5 h-5" />
        </a>
        <div>
            <h1 class="text-xl font-bold text-gray-900 font-mono">{{ $workOrder->wo_number }}</h1>
            <p class="text-sm text-gray-500 mt-0.5">Detail Pekerjaan</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Kolom kiri: info + item pekerjaan --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Info Kendaraan --}}
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h2 class="text-sm font-semibold text-gray-700 mb-4 uppercase tracking-wide">Kendaraan</h2>
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
                </div>

                @if($workOrder->keluhan_customer)
                <div class="mt-4 pt-4 border-t border-gray-100">
                    <p class="text-gray-400 text-xs mb-1">Keluhan Customer</p>
                    <p class="text-sm text-gray-700">{{ $workOrder->keluhan_customer }}</p>
                </div>
                @endif
            </div>

            {{-- Item Pekerjaan --}}
            @if($workOrder->invoice && $workOrder->invoice->items->isNotEmpty())
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wide">Item Pekerjaan</h2>
                </div>
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-xs font-semibold uppercase tracking-wider text-gray-500 border-b border-gray-200">
                            <th class="text-left px-6 py-3">Item</th>
                            <th class="text-left px-6 py-3">Tipe</th>
                            <th class="text-right px-6 py-3">Qty</th>
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
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>

        {{-- Kolom kanan: status + catatan --}}
        <div class="space-y-6">

            {{-- Status saat ini --}}
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h2 class="text-sm font-semibold text-gray-700 mb-4 uppercase tracking-wide">Status</h2>

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

                <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium {{ $statusColor }} mb-4">
                    {{ $statusLabel }}
                </span>

                <div class="flex flex-col gap-2">
                    @if($workOrder->status === 'pending')
                    <button wire:click="updateStatus('in_progress')"
                            class="w-full bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium py-2 px-4 rounded-lg transition">
                        <div wire:loading wire:target="updateStatus('in_progress')" class="w-3.5 h-3.5 border-2 border-white border-t-transparent rounded-full animate-spin inline-block mr-1"></div>
                        Mulai Kerjakan
                    </button>
                    @endif

                    @if($workOrder->status === 'in_progress')
                    <button wire:click="updateStatus('done')"
                            wire:confirm="Tandai pekerjaan ini selesai?"
                            class="w-full bg-green-600 hover:bg-green-700 text-white text-sm font-medium py-2 px-4 rounded-lg transition">
                        <div wire:loading wire:target="updateStatus('done')" class="w-3.5 h-3.5 border-2 border-white border-t-transparent rounded-full animate-spin inline-block mr-1"></div>
                        Tandai Selesai
                    </button>
                    @endif

                    @if(in_array($workOrder->status, ['pending', 'in_progress']))
                    <div class="text-xs text-gray-400 text-center">
                        Mulai: {{ $workOrder->mulai_at?->translatedFormat('d M Y, H:i') ?? '-' }}
                    </div>
                    @endif
                </div>
            </div>

            {{-- Catatan Mekanik --}}
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h2 class="text-sm font-semibold text-gray-700 mb-4 uppercase tracking-wide">Catatan Mekanik</h2>
                <form wire:submit="saveCatatan" class="space-y-3">
                    <textarea wire:model="catatanMekanik"
                              rows="5"
                              placeholder="Tulis temuan, kendala, atau catatan pekerjaan..."
                              class="w-full border border-gray-300 text-sm px-3 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 resize-none"
                              {{ $workOrder->status === 'done' ? 'disabled' : '' }}></textarea>
                    @error('catatanMekanik') <p class="text-xs text-red-500">{{ $message }}</p> @enderror

                    @if($workOrder->status !== 'done')
                    <button type="submit"
                            class="w-full bg-red-600 hover:bg-red-700 text-white text-sm font-medium py-2 px-4 rounded-lg transition flex items-center justify-center gap-2">
                        <div wire:loading wire:target="saveCatatan" class="w-3.5 h-3.5 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
                        <span wire:loading.remove wire:target="saveCatatan">Simpan Catatan</span>
                        <span wire:loading wire:target="saveCatatan">Menyimpan...</span>
                    </button>
                    @endif
                </form>
            </div>
        </div>
    </div>
</div>
