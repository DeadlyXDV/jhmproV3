<div>
    {{-- Page header --}}
    <div class="mb-6">
        <h1 class="text-xl font-bold text-gray-900">Dashboard</h1>
        <p class="text-sm text-gray-500 mt-0.5">Ringkasan operasional bengkel hari ini</p>
    </div>

    {{-- Alert stok kritis --}}
    @if($stokKritis > 0)
    <div class="mb-5 flex items-center gap-3 bg-red-50 border border-red-200 rounded-xl px-4 py-3">
        <x-heroicon-o-exclamation-triangle class="w-5 h-5 text-red-500 flex-shrink-0" />
        <p class="text-sm text-red-700 font-medium">
            {{ $stokKritis }} sparepart mencapai stok minimum.
            <a href="{{ route('admin.spareparts.index') }}" class="underline hover:no-underline">Lihat daftar →</a>
        </p>
    </div>
    @endif

    {{-- Stat Cards --}}
    <div class="grid grid-cols-4 gap-4 mb-6">

        {{-- Pendapatan bulan ini --}}
        <div class="bg-white rounded-xl shadow-sm p-5">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm text-gray-500">Pendapatan Bulan Ini</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">
                        Rp {{ number_format($pendapatanBulanIni, 0, ',', '.') }}
                    </p>
                    @if($deltaPercent !== null)
                    <p class="text-xs font-medium mt-1 {{ $deltaPercent >= 0 ? 'text-green-600' : 'text-red-600' }}">
                        {{ $deltaPercent >= 0 ? '+' : '' }}{{ $deltaPercent }}% vs bulan lalu
                    </p>
                    @endif
                </div>
                <div class="w-10 h-10 rounded-lg bg-red-50 flex items-center justify-center flex-shrink-0">
                    <x-heroicon-o-banknotes class="w-5 h-5 text-red-600" />
                </div>
            </div>
        </div>

        {{-- Booking pending --}}
        <div class="bg-white rounded-xl shadow-sm p-5">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm text-gray-500">Booking Menunggu</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $bookingPending }}</p>
                    <p class="text-xs text-gray-400 mt-1">Perlu dikonfirmasi</p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-blue-50 flex items-center justify-center flex-shrink-0">
                    <x-heroicon-o-calendar-days class="w-5 h-5 text-blue-600" />
                </div>
            </div>
        </div>

        {{-- Invoice belum lunas --}}
        <div class="bg-white rounded-xl shadow-sm p-5">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm text-gray-500">Invoice Belum Lunas</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $invoicePending }}</p>
                    <p class="text-xs text-gray-400 mt-1">Menunggu pembayaran</p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-amber-50 flex items-center justify-center flex-shrink-0">
                    <x-heroicon-o-document-text class="w-5 h-5 text-amber-600" />
                </div>
            </div>
        </div>

        {{-- Pelanggan baru bulan ini --}}
        <div class="bg-white rounded-xl shadow-sm p-5">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm text-gray-500">Pelanggan Baru</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $pelangganBaru }}</p>
                    <p class="text-xs text-gray-400 mt-1">Bulan {{ now()->translatedFormat('F Y') }}</p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-purple-50 flex items-center justify-center flex-shrink-0">
                    <x-heroicon-o-user-plus class="w-5 h-5 text-purple-600" />
                </div>
            </div>
        </div>

    </div>

    {{-- Tabel bawah --}}
    <div class="grid grid-cols-2 gap-5">

        {{-- Booking pending --}}
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                <h2 class="text-sm font-semibold text-gray-900">Booking Menunggu Konfirmasi</h2>
                <a href="{{ route('admin.bookings.index') }}" class="text-xs text-red-600 hover:underline">Lihat semua →</a>
            </div>

            @if($bookingsPending->isEmpty())
            <div class="px-5 py-8 text-center text-sm text-gray-400">Tidak ada booking menunggu</div>
            @else
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-xs font-semibold uppercase tracking-wider text-gray-500 border-b border-gray-100">
                        <th class="text-left px-5 py-3">Pemesan</th>
                        <th class="text-left px-5 py-3">Tanggal</th>
                        <th class="text-left px-5 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($bookingsPending as $booking)
                    <tr class="border-b border-gray-50 hover:bg-gray-50 transition">
                        <td class="px-5 py-3">
                            <p class="font-medium text-gray-900">{{ $booking->nama_pemesan }}</p>
                            <p class="text-xs text-gray-400">{{ $booking->no_hp_pemesan }}</p>
                        </td>
                        <td class="px-5 py-3 text-gray-600">
                            {{ \Carbon\Carbon::parse($booking->tanggal_booking)->translatedFormat('d M Y') }}
                        </td>
                        <td class="px-5 py-3">
                            <a href="{{ route('admin.bookings.index') }}"
                               class="text-xs text-red-600 hover:underline font-medium">Detail →</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @endif
        </div>

        {{-- Invoice belum lunas --}}
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                <h2 class="text-sm font-semibold text-gray-900">Invoice Belum Lunas</h2>
                <a href="{{ route('admin.invoices.index') }}" class="text-xs text-red-600 hover:underline">Lihat semua →</a>
            </div>

            @if($invoicesBelumLunas->isEmpty())
            <div class="px-5 py-8 text-center text-sm text-gray-400">Semua invoice sudah lunas</div>
            @else
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-xs font-semibold uppercase tracking-wider text-gray-500 border-b border-gray-100">
                        <th class="text-left px-5 py-3">No. Invoice</th>
                        <th class="text-left px-5 py-3">Customer</th>
                        <th class="text-left px-5 py-3">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($invoicesBelumLunas as $invoice)
                    <tr class="border-b border-gray-50 hover:bg-gray-50 transition">
                        <td class="px-5 py-3">
                            <span class="text-red-600 font-semibold">{{ $invoice->invoice_number }}</span>
                        </td>
                        <td class="px-5 py-3 text-gray-700">
                            {{ $invoice->customer?->nama ?? $invoice->partner?->nama_bengkel ?? '-' }}
                        </td>
                        <td class="px-5 py-3">
                            @if($invoice->payment_status === 'partial')
                            <span class="flex items-center gap-1.5 text-xs font-medium text-amber-700">
                                <span class="w-2 h-2 rounded-full bg-amber-500"></span>Sebagian
                            </span>
                            @else
                            <span class="flex items-center gap-1.5 text-xs font-medium text-red-700">
                                <span class="w-2 h-2 rounded-full bg-red-600"></span>Belum Lunas
                            </span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @endif
        </div>

    </div>

    {{-- Stok kritis --}}
    @if($sparepartKritis->isNotEmpty())
    <div class="mt-5 bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
            <h2 class="text-sm font-semibold text-gray-900">Stok Kritis</h2>
            <a href="{{ route('admin.spareparts.index') }}" class="text-xs text-red-600 hover:underline">Kelola stok →</a>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3 p-4">
            @foreach($sparepartKritis as $part)
            <div class="border border-red-100 rounded-lg p-3 bg-red-50/40">
                <p class="text-xs font-medium text-gray-700 truncate">{{ $part->item_name }}</p>
                <p class="text-xs text-gray-400 mt-0.5">{{ $part->sku }}</p>
                <p class="text-sm font-bold mt-1 {{ $part->stock === 0 ? 'text-red-600' : 'text-orange-500' }}">
                    Stok: {{ $part->stock }} {{ $part->satuan }}
                </p>
            </div>
            @endforeach
        </div>
    </div>
    @endif

</div>
