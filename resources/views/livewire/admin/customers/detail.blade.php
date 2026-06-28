<div>
    @php
        $colors = ['bg-red-100 text-red-700','bg-blue-100 text-blue-700','bg-green-100 text-green-700','bg-purple-100 text-purple-700','bg-orange-100 text-orange-700','bg-teal-100 text-teal-700','bg-amber-100 text-amber-700'];
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

    {{-- Breadcrumb --}}
    <div class="flex items-center gap-2 text-sm text-gray-500 mb-4">
        <a wire:navigate href="{{ route('admin.customers.index') }}" class="hover:text-gray-700">Pelanggan</a>
        <span>/</span>
        <span class="text-gray-900 font-medium">{{ $customer->nama }}</span>
    </div>

    {{-- Header profil --}}
    <div class="bg-white rounded-xl shadow-sm p-6 mb-5">
        <div class="flex items-start justify-between">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-full flex items-center justify-center text-xl font-bold flex-shrink-0 {{ $avatarColor }}">
                    {{ $initials }}
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h1 class="text-xl font-bold text-gray-900">{{ $customer->nama }}</h1>
                        @if($rfm)
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $segmenColor }}">
                            {{ $rfm->cluster_label }}
                        </span>
                        @endif
                    </div>
                    <div class="flex items-center gap-4 mt-1 text-sm text-gray-500">
                        @if($customer->no_hp)
                        <span class="flex items-center gap-1">
                            <x-heroicon-o-phone class="w-3.5 h-3.5" />{{ $customer->no_hp }}
                        </span>
                        @endif
                        @if($customer->email)
                        <span class="flex items-center gap-1">
                            <x-heroicon-o-envelope class="w-3.5 h-3.5" />{{ $customer->email }}
                        </span>
                        @endif
                    </div>
                </div>
            </div>
            <a wire:navigate href="{{ route('admin.customers.edit', $customer) }}"
               class="flex items-center gap-1.5 border border-gray-300 text-gray-700 text-sm px-4 py-2 rounded-lg hover:bg-gray-50 transition">
                <x-heroicon-o-pencil class="w-4 h-4" />
                Edit
            </a>
        </div>

        {{-- Stat --}}
        <div class="grid grid-cols-4 gap-4 mt-5 pt-5 border-t border-gray-100">
            <div>
                <p class="text-xs text-gray-500">Total Kunjungan</p>
                <p class="text-lg font-bold text-gray-900 mt-0.5">{{ $customer->total_kunjungan }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500">Total Transaksi</p>
                <p class="text-lg font-bold text-gray-900 mt-0.5">Rp {{ number_format($customer->total_transaksi ?? 0, 0, ',', '.') }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500">Jumlah Kendaraan</p>
                <p class="text-lg font-bold text-gray-900 mt-0.5">{{ $customer->vehicles_count }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500">Terdaftar Sejak</p>
                <p class="text-lg font-bold text-gray-900 mt-0.5">{{ $customer->created_at->translatedFormat('M Y') }}</p>
            </div>
        </div>
    </div>

    {{-- Tabs --}}
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="flex border-b border-gray-200">
            @foreach(['vehicles' => 'Kendaraan', 'invoices' => 'Invoice', 'bookings' => 'Booking'] as $tab => $label)
            <button wire:click="setTab('{{ $tab }}')"
                    class="px-5 py-3 text-sm font-medium transition border-b-2
                           {{ $activeTab === $tab ? 'border-red-600 text-red-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                {{ $label }}
            </button>
            @endforeach
        </div>

        {{-- Tab: Kendaraan --}}
        @if($activeTab === 'vehicles')
        <div class="p-5">
            <div class="flex items-center justify-between mb-4">
                <p class="text-sm text-gray-500">{{ $customer->vehicles->count() }} kendaraan terdaftar</p>
                <a wire:navigate href="{{ route('admin.vehicles.create', ['customer_id' => $customer->id]) }}"
                   class="flex items-center gap-1.5 bg-red-600 hover:bg-red-700 text-white text-xs font-medium px-3 py-1.5 rounded-lg transition">
                    <x-heroicon-o-plus class="w-3.5 h-3.5" />
                    Tambah Kendaraan
                </a>
            </div>
            @if($customer->vehicles->isEmpty())
            <p class="text-sm text-gray-400 text-center py-8">Belum ada kendaraan terdaftar</p>
            @else
            <div class="grid gap-3">
                @foreach($customer->vehicles as $vehicle)
                <div class="flex items-center justify-between border border-gray-100 rounded-lg px-4 py-3 hover:border-gray-200 transition">
                    <div>
                        <p class="font-medium text-gray-900">{{ $vehicle->merk }} {{ $vehicle->model }} {{ $vehicle->tahun }}</p>
                        <p class="text-sm text-gray-500 mt-0.5">
                            <span class="text-red-600 font-semibold">{{ $vehicle->no_polisi }}</span>
                            @if($vehicle->warna) · {{ $vehicle->warna }} @endif
                        </p>
                    </div>
                    <a wire:navigate href="{{ route('admin.vehicles.show', $vehicle) }}"
                       class="text-gray-400 hover:text-gray-700 transition">
                        <x-heroicon-o-arrow-right class="w-4 h-4" />
                    </a>
                </div>
                @endforeach
            </div>
            @endif
        </div>
        @endif

        {{-- Tab: Invoice --}}
        @if($activeTab === 'invoices')
        <table class="w-full text-sm">
            <thead>
                <tr class="text-xs font-semibold uppercase tracking-wider text-gray-500 border-b border-gray-100">
                    <th class="text-left px-5 py-3">No. Invoice</th>
                    <th class="text-left px-5 py-3">Tanggal</th>
                    <th class="text-left px-5 py-3">Total</th>
                    <th class="text-left px-5 py-3">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($customer->invoices as $invoice)
                <tr class="border-b border-gray-50 hover:bg-gray-50 transition">
                    <td class="px-5 py-3">
                        <span class="text-red-600 font-semibold">{{ $invoice->invoice_number }}</span>
                    </td>
                    <td class="px-5 py-3 text-gray-600">
                        {{ \Carbon\Carbon::parse($invoice->tanggal)->translatedFormat('d M Y') }}
                    </td>
                    <td class="px-5 py-3 text-gray-700">
                        Rp {{ number_format($invoice->grand_total, 0, ',', '.') }}
                    </td>
                    <td class="px-5 py-3">
                        @php
                            [$dot, $label] = match($invoice->payment_status) {
                                'paid'    => ['bg-green-500', 'Lunas'],
                                'partial' => ['bg-amber-500', 'Sebagian'],
                                default   => ['bg-red-600', 'Belum Lunas'],
                            };
                        @endphp
                        <span class="flex items-center gap-1.5 text-xs font-medium
                            {{ $invoice->payment_status === 'paid' ? 'text-green-700' : ($invoice->payment_status === 'partial' ? 'text-amber-700' : 'text-red-700') }}">
                            <span class="w-2 h-2 rounded-full {{ $dot }}"></span>{{ $label }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" class="px-5 py-8 text-center text-sm text-gray-400">Belum ada invoice</td></tr>
                @endforelse
            </tbody>
        </table>
        @endif

        {{-- Tab: Booking --}}
        @if($activeTab === 'bookings')
        <table class="w-full text-sm">
            <thead>
                <tr class="text-xs font-semibold uppercase tracking-wider text-gray-500 border-b border-gray-100">
                    <th class="text-left px-5 py-3">No. Booking</th>
                    <th class="text-left px-5 py-3">Tanggal</th>
                    <th class="text-left px-5 py-3">Keluhan</th>
                    <th class="text-left px-5 py-3">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($customer->bookings as $booking)
                @php
                    [$dot, $label] = match($booking->status) {
                        'pending'     => ['bg-amber-500', 'Menunggu'],
                        'confirmed'   => ['bg-teal-500', 'Dikonfirmasi'],
                        'in_progress' => ['bg-blue-500', 'Dikerjakan'],
                        'completed'   => ['bg-green-500', 'Selesai'],
                        'cancelled'   => ['bg-red-600', 'Dibatalkan'],
                        default       => ['bg-gray-400', $booking->status],
                    };
                @endphp
                <tr class="border-b border-gray-50 hover:bg-gray-50 transition">
                    <td class="px-5 py-3">
                        <span class="text-red-600 font-semibold">{{ $booking->booking_number }}</span>
                    </td>
                    <td class="px-5 py-3 text-gray-600">
                        {{ \Carbon\Carbon::parse($booking->tanggal_booking)->translatedFormat('d M Y') }}
                    </td>
                    <td class="px-5 py-3 text-gray-600 max-w-xs truncate">{{ $booking->keluhan ?: '-' }}</td>
                    <td class="px-5 py-3">
                        <span class="flex items-center gap-1.5 text-xs font-medium text-gray-700">
                            <span class="w-2 h-2 rounded-full {{ $dot }}"></span>{{ $label }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" class="px-5 py-8 text-center text-sm text-gray-400">Belum ada booking</td></tr>
                @endforelse
            </tbody>
        </table>
        @endif

    </div>

</div>
