<div>
    {{-- Page header --}}
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Invoice</h1>
            <p class="text-sm text-gray-500 mt-0.5">Daftar semua invoice bengkel</p>
        </div>
    </div>

    {{-- Card --}}
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">

        {{-- Toolbar --}}
        <div class="flex items-center gap-3 px-5 py-4 border-b border-gray-100 flex-wrap">
            <div class="relative flex-1 min-w-[200px] max-w-xs">
                <x-heroicon-o-magnifying-glass class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
                <input wire:model.live.debounce.300ms="search"
                       type="text"
                       placeholder="Cari no. invoice, customer..."
                       class="w-full pl-9 pr-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent" />
            </div>

            <select wire:model.live="filterStatus"
                    class="border border-gray-300 text-gray-700 text-sm px-3 py-2 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-red-500">
                <option value="">Semua Status</option>
                @foreach($statusList as $s)
                    <option value="{{ $s }}">{{ ucfirst($s) }}</option>
                @endforeach
            </select>

            <select wire:model.live="filterTipe"
                    class="border border-gray-300 text-gray-700 text-sm px-3 py-2 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-red-500">
                <option value="">Semua Tipe</option>
                @foreach($tipeList as $t)
                    <option value="{{ $t }}">{{ ucfirst($t) }}</option>
                @endforeach
            </select>
        </div>

        {{-- Tabel --}}
        <table class="w-full text-sm">
            <thead>
                <tr class="text-xs font-semibold uppercase tracking-wider text-gray-500 border-b border-gray-200">
                    <th class="text-left px-5 py-3">No. Invoice</th>
                    <th class="text-left px-5 py-3">Customer</th>
                    <th class="text-left px-5 py-3">Tipe</th>
                    <th class="text-left px-5 py-3">Status Bayar</th>
                    <th class="text-right px-5 py-3">Grand Total</th>
                    <th class="text-left px-5 py-3">Tanggal</th>
                    <th class="text-left px-5 py-3">Dibuat oleh</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($invoices as $invoice)
                @php
                    $payColor = match($invoice->payment_status) {
                        'paid'    => 'bg-green-100 text-green-700',
                        'partial' => 'bg-amber-100 text-amber-700',
                        'unpaid'  => 'bg-red-100 text-red-700',
                        default   => 'bg-gray-100 text-gray-500',
                    };
                    $payLabel = match($invoice->payment_status) {
                        'paid'    => 'Lunas',
                        'partial' => 'Sebagian',
                        'unpaid'  => 'Belum Bayar',
                        default   => $invoice->payment_status,
                    };
                @endphp
                <tr class="border-b border-gray-100 hover:bg-gray-50 transition">
                    <td class="px-5 py-4">
                        <span class="font-mono font-medium text-gray-900">{{ $invoice->invoice_number }}</span>
                    </td>
                    <td class="px-5 py-4 text-gray-700">
                        {{ $invoice->customer?->nama ?? '-' }}
                    </td>
                    <td class="px-5 py-4">
                        <span class="text-xs text-gray-600 capitalize">{{ $invoice->tipe }}</span>
                    </td>
                    <td class="px-5 py-4">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $payColor }}">
                            {{ $payLabel }}
                        </span>
                    </td>
                    <td class="px-5 py-4 text-right font-semibold text-gray-900">
                        Rp {{ number_format($invoice->grand_total, 0, ',', '.') }}
                    </td>
                    <td class="px-5 py-4 text-gray-500 text-xs">
                        {{ $invoice->tanggal->translatedFormat('d M Y') }}
                    </td>
                    <td class="px-5 py-4 text-gray-500 text-xs">
                        {{ $invoice->user?->name ?? '-' }}
                    </td>
                    <td class="px-5 py-4">
                        <a wire:navigate href="{{ route('admin.invoices.show', $invoice) }}"
                           class="text-gray-400 hover:text-gray-700 transition">
                            <x-heroicon-o-eye class="w-4 h-4" />
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-5 py-12 text-center text-sm text-gray-400">
                        Tidak ada invoice ditemukan
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Pagination --}}
        @if($invoices->hasPages())
        <div class="flex items-center justify-between px-5 py-3 border-t border-gray-100">
            <p class="text-sm text-gray-500">
                Menampilkan {{ $invoices->firstItem() }}–{{ $invoices->lastItem() }} dari {{ $invoices->total() }}
            </p>
            <div class="flex items-center gap-1">
                @if($invoices->onFirstPage())
                <span class="px-3 py-1.5 text-sm text-gray-400 border border-gray-200 rounded cursor-not-allowed">Sebelumnya</span>
                @else
                <button wire:click="previousPage" class="px-3 py-1.5 text-sm text-gray-700 border border-gray-300 rounded hover:bg-gray-50">Sebelumnya</button>
                @endif

                @foreach($invoices->getUrlRange(max(1, $invoices->currentPage()-2), min($invoices->lastPage(), $invoices->currentPage()+2)) as $page => $url)
                <button wire:click="gotoPage({{ $page }})"
                        class="px-3 py-1.5 text-sm rounded {{ $page === $invoices->currentPage() ? 'bg-red-600 text-white' : 'text-gray-700 border border-gray-300 hover:bg-gray-50' }}">
                    {{ $page }}
                </button>
                @endforeach

                @if($invoices->hasMorePages())
                <button wire:click="nextPage" class="px-3 py-1.5 text-sm text-gray-700 border border-gray-300 rounded hover:bg-gray-50">Selanjutnya</button>
                @else
                <span class="px-3 py-1.5 text-sm text-gray-400 border border-gray-200 rounded cursor-not-allowed">Selanjutnya</span>
                @endif
            </div>
        </div>
        @endif
    </div>
</div>
