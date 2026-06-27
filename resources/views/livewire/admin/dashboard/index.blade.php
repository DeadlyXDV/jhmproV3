<x-slot name="title">Dashboard</x-slot>

<x-slot name="breadcrumbs">
    <nav class="flex items-center gap-2 text-[13px] text-gray-400 mb-0.5">
        <a wire:navigate href="{{ route('admin.dashboard') }}" wire:navigate class="hover:text-gray-600 transition-colors">Beranda</a>
        <x-heroicon-m-chevron-right class="w-3 h-3" />
        <span class="text-gray-500">Dashboard</span>
    </nav>
</x-slot>

<div class="space-y-8">
    {{-- Stats Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        {{-- Card 1: Pendapatan --}}
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-col justify-between h-40 group hover:shadow-md transition-shadow">
            <div class="flex items-start justify-between">
                <div class="p-2.5 bg-red-50 rounded-xl">
                    <x-heroicon-o-banknotes class="w-6 h-6 text-red-600" />
                </div>
                <div class="w-20 h-8">
                    <svg viewBox="0 0 100 40" class="w-full h-full">
                        <path d="M0 35 Q 25 35, 50 25 T 100 5" fill="none" stroke="#EF4444" stroke-width="2" />
                        <path d="M0 35 Q 25 35, 50 25 T 100 5 V 40 H 0 Z" fill="url(#grad-red)" opacity="0.1" />
                        <defs>
                            <linearGradient id="grad-red" x1="0" x2="0" y1="0" y2="1">
                                <stop offset="0%" stop-color="#EF4444" /><stop offset="100%" stop-color="#EF4444" stop-opacity="0" />
                            </linearGradient>
                        </defs>
                    </svg>
                </div>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-400">Pendapatan Bulan Ini</p>
                <div class="flex items-baseline gap-2">
                    @php
                        $juta = $pendapatanBulanIni / 1_000_000;
                        $label = $juta >= 1
                            ? number_format($juta, 1, '.', '') . ' jt'
                            : 'Rp ' . number_format($pendapatanBulanIni, 0, ',', '.');
                    @endphp
                    <h3 class="text-2xl font-bold text-gray-900">
                        {{ $juta >= 1 ? 'Rp ' . $label : $label }}
                    </h3>
                    @if ($deltaPercent !== null)
                        <span class="flex items-center text-[11px] font-bold {{ $deltaPercent >= 0 ? 'text-green-500 bg-green-50' : 'text-red-500 bg-red-50' }} px-1.5 py-0.5 rounded">
                            @if ($deltaPercent >= 0)
                                <x-heroicon-m-arrow-trending-up class="w-3 h-3 mr-0.5" />
                            @else
                                <x-heroicon-m-arrow-trending-down class="w-3 h-3 mr-0.5" />
                            @endif
                            {{ abs($deltaPercent) }}%
                        </span>
                    @endif
                </div>
            </div>
        </div>

        {{-- Card 2: Total Booking --}}
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-col justify-between h-40 group hover:shadow-md transition-shadow">
            <div class="flex items-start justify-between">
                <div class="p-2.5 bg-blue-50 rounded-xl">
                    <x-heroicon-o-calendar-days class="w-6 h-6 text-blue-600" />
                </div>
                <div class="w-20 h-8">
                    <svg viewBox="0 0 100 40" class="w-full h-full">
                        <path d="M0 30 Q 20 20, 40 35 T 80 15 T 100 20" fill="none" stroke="#3B82F6" stroke-width="2" />
                        <path d="M0 30 Q 20 20, 40 35 T 80 15 T 100 20 V 40 H 0 Z" fill="url(#grad-blue)" opacity="0.1" />
                        <defs>
                            <linearGradient id="grad-blue" x1="0" x2="0" y1="0" y2="1">
                                <stop offset="0%" stop-color="#3B82F6" /><stop offset="100%" stop-color="#3B82F6" stop-opacity="0" />
                            </linearGradient>
                        </defs>
                    </svg>
                </div>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-400">Booking Bulan Ini</p>
                <div class="flex items-baseline gap-2">
                    <h3 class="text-2xl font-bold text-gray-900">{{ $totalBookingBulanIni }}</h3>
                    @if ($deltaBookingPercent !== null)
                        <span class="flex items-center text-[11px] font-bold {{ $deltaBookingPercent >= 0 ? 'text-green-500 bg-green-50' : 'text-red-500 bg-red-50' }} px-1.5 py-0.5 rounded">
                            @if ($deltaBookingPercent >= 0)
                                <x-heroicon-m-arrow-trending-up class="w-3 h-3 mr-0.5" />
                            @else
                                <x-heroicon-m-arrow-trending-down class="w-3 h-3 mr-0.5" />
                            @endif
                            {{ abs($deltaBookingPercent) }}%
                        </span>
                    @endif
                </div>
            </div>
        </div>

        {{-- Card 3: Work Order Selesai --}}
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-col justify-between h-40 group hover:shadow-md transition-shadow">
            <div class="flex items-start justify-between">
                <div class="p-2.5 bg-green-50 rounded-xl">
                    <x-heroicon-o-wrench-screwdriver class="w-6 h-6 text-green-600" />
                </div>
                <div class="w-20 h-8">
                    <svg viewBox="0 0 100 40" class="w-full h-full">
                        <path d="M0 35 L 20 25 L 40 30 L 60 15 L 80 20 L 100 5" fill="none" stroke="#10B981" stroke-width="2" />
                        <path d="M0 35 L 20 25 L 40 30 L 60 15 L 80 20 L 100 5 V 40 H 0 Z" fill="url(#grad-green)" opacity="0.1" />
                        <defs>
                            <linearGradient id="grad-green" x1="0" x2="0" y1="0" y2="1">
                                <stop offset="0%" stop-color="#10B981" /><stop offset="100%" stop-color="#10B981" stop-opacity="0" />
                            </linearGradient>
                        </defs>
                    </svg>
                </div>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-400">Work Order Selesai</p>
                <div class="flex items-baseline gap-2">
                    <h3 class="text-2xl font-bold text-gray-900">{{ $woSelesaiBulanIni }}</h3>
                    @if ($deltaWoPercent !== null)
                        <span class="flex items-center text-[11px] font-bold {{ $deltaWoPercent >= 0 ? 'text-green-500 bg-green-50' : 'text-red-500 bg-red-50' }} px-1.5 py-0.5 rounded">
                            @if ($deltaWoPercent >= 0)
                                <x-heroicon-m-arrow-trending-up class="w-3 h-3 mr-0.5" />
                            @else
                                <x-heroicon-m-arrow-trending-down class="w-3 h-3 mr-0.5" />
                            @endif
                            {{ abs($deltaWoPercent) }}%
                        </span>
                    @endif
                </div>
            </div>
        </div>

        {{-- Card 4: Pelanggan Baru --}}
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-col justify-between h-40 group hover:shadow-md transition-shadow">
            <div class="flex items-start justify-between">
                <div class="p-2.5 bg-purple-50 rounded-xl">
                    <x-heroicon-o-users class="w-6 h-6 text-purple-600" />
                </div>
                <div class="w-20 h-8">
                    <svg viewBox="0 0 100 40" class="w-full h-full">
                        <path d="M0 5 Q 25 15, 50 10 T 100 35" fill="none" stroke="#8B5CF6" stroke-width="2" />
                        <path d="M0 5 Q 25 15, 50 10 T 100 35 V 40 H 0 Z" fill="url(#grad-purple)" opacity="0.1" />
                        <defs>
                            <linearGradient id="grad-purple" x1="0" x2="0" y1="0" y2="1">
                                <stop offset="0%" stop-color="#8B5CF6" /><stop offset="100%" stop-color="#8B5CF6" stop-opacity="0" />
                            </linearGradient>
                        </defs>
                    </svg>
                </div>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-400">Pelanggan Baru</p>
                <div class="flex items-baseline gap-2">
                    <h3 class="text-2xl font-bold text-gray-900">{{ $pelangganBaru }}</h3>
                    @if ($deltaPelangganPercent !== null)
                        <span class="flex items-center text-[11px] font-bold {{ $deltaPelangganPercent >= 0 ? 'text-green-500 bg-green-50' : 'text-red-500 bg-red-50' }} px-1.5 py-0.5 rounded">
                            @if ($deltaPelangganPercent >= 0)
                                <x-heroicon-m-arrow-trending-up class="w-3 h-3 mr-0.5" />
                            @else
                                <x-heroicon-m-arrow-trending-down class="w-3 h-3 mr-0.5" />
                            @endif
                            {{ abs($deltaPelangganPercent) }}%
                        </span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Charts Row --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Revenue Chart --}}
        <div class="lg:col-span-2 bg-white p-8 rounded-3xl shadow-sm border border-gray-100">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h3 class="text-lg font-bold text-gray-900">Statistik Pendapatan</h3>
                    <p class="text-sm text-gray-400">Servis bengkel vs. penjualan online shop — {{ now()->year }}</p>
                </div>
                <div class="flex items-center gap-4">
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-red-600"></span>
                        <span class="text-xs font-semibold text-gray-500">Servis</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-indigo-400"></span>
                        <span class="text-xs font-semibold text-gray-500">Online Shop</span>
                    </div>
                </div>
            </div>

            <div class="h-72 w-full relative">
                @php
                    $svgW = 800;
                    $svgH = 200;
                    $padTop = 20;
                    $usableH = $svgH - $padTop;

                    $servisPoints = '';
                    $shopPoints   = '';
                    foreach ($revenueData as $m => $d) {
                        $x = round(($m - 1) * $svgW / 11);
                        $yS = round($svgH - ($d['servis'] / $maxRevenue * $usableH));
                        $yO = round($svgH - ($d['shop']   / $maxRevenue * $usableH));
                        $servisPoints .= "{$x},{$yS} ";
                        $shopPoints   .= "{$x},{$yO} ";
                    }
                    $servisPoints = trim($servisPoints);
                    $shopPoints   = trim($shopPoints);

                    // convert to polyline path
                    $toPath = function (string $pts): string {
                        $coords = explode(' ', $pts);
                        $d = '';
                        foreach ($coords as $i => $pt) {
                            $d .= ($i === 0 ? 'M' : ' L') . $pt;
                        }
                        return $d;
                    };
                    $servisPath = $toPath($servisPoints);
                    $shopPath   = $toPath($shopPoints);

                    // close path for fill area
                    $lastX = round(($m - 1) * $svgW / 11);
                    $servisFill = $servisPath . " L{$lastX},{$svgH} L0,{$svgH} Z";
                    $shopFill   = $shopPath   . " L{$lastX},{$svgH} L0,{$svgH} Z";
                @endphp

                <svg viewBox="0 0 {{ $svgW }} {{ $svgH }}" class="w-full h-full overflow-visible">
                    <line x1="0" y1="0" x2="{{ $svgW }}" y2="0" stroke="#F3F4F6" stroke-width="1" />
                    <line x1="0" y1="50" x2="{{ $svgW }}" y2="50" stroke="#F3F4F6" stroke-width="1" />
                    <line x1="0" y1="100" x2="{{ $svgW }}" y2="100" stroke="#F3F4F6" stroke-width="1" />
                    <line x1="0" y1="150" x2="{{ $svgW }}" y2="150" stroke="#F3F4F6" stroke-width="1" />
                    <line x1="0" y1="{{ $svgH }}" x2="{{ $svgW }}" y2="{{ $svgH }}" stroke="#F3F4F6" stroke-width="2" />

                    <defs>
                        <linearGradient id="grad-indigo-fill" x1="0" x2="0" y1="0" y2="1">
                            <stop offset="0%" stop-color="#818CF8" /><stop offset="100%" stop-color="#818CF8" stop-opacity="0" />
                        </linearGradient>
                        <linearGradient id="grad-red-fill" x1="0" x2="0" y1="0" y2="1">
                            <stop offset="0%" stop-color="#DC2626" /><stop offset="100%" stop-color="#DC2626" stop-opacity="0" />
                        </linearGradient>
                    </defs>

                    <path d="{{ $shopFill }}" fill="url(#grad-indigo-fill)" opacity="0.1" />
                    <path d="{{ $shopPath }}" fill="none" stroke="#818CF8" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" />

                    <path d="{{ $servisFill }}" fill="url(#grad-red-fill)" opacity="0.15" />
                    <path d="{{ $servisPath }}" fill="none" stroke="#DC2626" stroke-width="4" stroke-linecap="round" stroke-linejoin="round" />
                </svg>

                <div class="flex justify-between mt-4 text-[11px] font-bold text-gray-400 uppercase tracking-widest px-1">
                    <span>Jan</span><span>Feb</span><span>Mar</span><span>Apr</span><span>Mei</span><span>Jun</span>
                    <span>Jul</span><span>Agu</span><span>Sep</span><span>Okt</span><span>Nov</span><span>Des</span>
                </div>
            </div>
        </div>

        {{-- Work Order Status Chart --}}
        <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100 flex flex-col">
            <div class="flex items-center justify-between mb-8">
                <h3 class="text-lg font-bold text-gray-900">Status Work Order</h3>
            </div>

            @php
                $pct = fn ($v) => $woTotal > 0 ? round($v / $woTotal * 100) : 0;
                $pSelesai  = $pct($woSelesai);
                $pProses   = $pct($woProses);
                $pAntrian  = $pct($woAntrian);

                $circumference = 100.53; // 2π × r=16
                $offset = 0;
            @endphp

            <div class="flex-1 flex flex-col items-center justify-center relative">
                <div class="relative w-52 h-52">
                    <svg viewBox="0 0 36 36" class="w-full h-full transform -rotate-90">
                        <circle cx="18" cy="18" r="16" fill="none" stroke="#F3F4F6" stroke-width="4" />
                        @if ($woSelesai > 0)
                        <circle cx="18" cy="18" r="16" fill="none" stroke="#10B981" stroke-width="4"
                            stroke-dasharray="{{ $pSelesai }} 100"
                            stroke-dashoffset="{{ -$offset }}" />
                        @php $offset += $pSelesai; @endphp
                        @endif
                        @if ($woProses > 0)
                        <circle cx="18" cy="18" r="16" fill="none" stroke="#3B82F6" stroke-width="4"
                            stroke-dasharray="{{ $pProses }} 100"
                            stroke-dashoffset="{{ -$offset }}" />
                        @php $offset += $pProses; @endphp
                        @endif
                        @if ($woAntrian > 0)
                        <circle cx="18" cy="18" r="16" fill="none" stroke="#F59E0B" stroke-width="4"
                            stroke-dasharray="{{ $pAntrian }} 100"
                            stroke-dashoffset="{{ -$offset }}" />
                        @endif
                    </svg>
                    <div class="absolute inset-0 flex flex-col items-center justify-center text-center">
                        <span class="text-3xl font-black text-gray-900 leading-none">{{ $woTotal }}</span>
                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-1">Total WO</span>
                    </div>
                </div>

                <div class="w-full mt-10 space-y-3">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <span class="w-2.5 h-2.5 rounded-full bg-green-500"></span>
                            <span class="text-sm font-semibold text-gray-500">Selesai</span>
                        </div>
                        <span class="text-sm font-bold text-gray-900">{{ $woSelesai }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <span class="w-2.5 h-2.5 rounded-full bg-blue-500"></span>
                            <span class="text-sm font-semibold text-gray-500">Dikerjakan</span>
                        </div>
                        <span class="text-sm font-bold text-gray-900">{{ $woProses }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                            <span class="text-sm font-semibold text-gray-500">Antrian</span>
                        </div>
                        <span class="text-sm font-bold text-gray-900">{{ $woAntrian }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Booking Terbaru --}}
    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-8 border-b border-gray-50 flex items-center justify-between bg-white">
            <div>
                <h3 class="text-lg font-bold text-gray-900">Booking Terbaru</h3>
                <p class="text-sm text-gray-400">5 booking paling baru</p>
            </div>
            <a wire:navigate href="{{ route('admin.bookings.index') }}"
               class="px-5 py-2.5 bg-gray-50 text-gray-600 text-sm font-bold rounded-xl hover:bg-gray-100 transition-colors">
                Lihat semua
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="text-[11px] font-bold text-gray-400 uppercase tracking-[0.15em] bg-gray-50/50">
                        <th class="px-8 py-5">No. Booking</th>
                        <th class="px-6 py-5">Pelanggan</th>
                        <th class="px-6 py-5">Kendaraan</th>
                        <th class="px-6 py-5">Layanan</th>
                        <th class="px-6 py-5">Jadwal</th>
                        <th class="px-6 py-5">Mekanik</th>
                        <th class="px-8 py-5">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse ($bookingTerbaru as $booking)
                        @php
                            $initials = collect(explode(' ', $booking->customer?->nama ?? 'U'))
                                ->map(fn ($w) => strtoupper($w[0]))
                                ->take(2)->implode('');
                            $statusMap = [
                                'pending'     => ['label' => 'Menunggu',   'dot' => 'bg-amber-500'],
                                'confirmed'   => ['label' => 'Dikonfirmasi','dot' => 'bg-blue-500'],
                                'in_progress' => ['label' => 'Dikerjakan', 'dot' => 'bg-indigo-500'],
                                'completed'   => ['label' => 'Selesai',    'dot' => 'bg-green-500'],
                                'cancelled'   => ['label' => 'Dibatalkan', 'dot' => 'bg-red-400'],
                            ];
                            $s = $statusMap[$booking->status] ?? ['label' => $booking->status, 'dot' => 'bg-gray-400'];
                            $layanan = $booking->services->first()?->service?->nama_service ?? '—';
                        @endphp
                        <tr class="group hover:bg-gray-50/50 transition-colors">
                            <td class="px-8 py-5">
                                <span class="text-sm font-bold text-red-600">{{ $booking->booking_number }}</span>
                            </td>
                            <td class="px-6 py-5">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-red-50 flex items-center justify-center text-[11px] font-bold text-red-600">
                                        {{ $initials }}
                                    </div>
                                    <span class="text-sm font-bold text-gray-700">{{ $booking->customer?->nama ?? '—' }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-5">
                                <div class="flex items-center gap-2 text-gray-500">
                                    <x-heroicon-o-wrench-screwdriver class="w-4 h-4 opacity-40" />
                                    <span class="text-sm font-medium">
                                        {{ $booking->vehicle ? $booking->vehicle->merk . ' ' . $booking->vehicle->model : '—' }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-5">
                                <span class="text-sm font-medium text-gray-600">{{ $layanan }}</span>
                            </td>
                            <td class="px-6 py-5 text-sm">
                                <p class="font-bold text-gray-700">
                                    {{ \Carbon\Carbon::parse($booking->tanggal_booking)->translatedFormat('d M Y') }}
                                </p>
                                <p class="text-[11px] font-medium text-gray-400">{{ $booking->jam_mulai }} WIB</p>
                            </td>
                            <td class="px-6 py-5">
                                <span class="text-sm font-bold text-gray-700">{{ $booking->mekanik?->name ?? '—' }}</span>
                            </td>
                            <td class="px-8 py-5">
                                <div class="flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full {{ $s['dot'] }}"></span>
                                    <span class="text-sm font-bold text-gray-900">{{ $s['label'] }}</span>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-8 py-10 text-center text-sm text-gray-400">Belum ada booking.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- RFM Segmentation --}}
    @php
        $rfmConfig = [
            'Champion' => ['desc' => 'Sering & baru servis',  'color' => 'bg-green-500'],
            'Loyal'    => ['desc' => 'Pelanggan setia',        'color' => 'bg-blue-600'],
            'Potential'=> ['desc' => 'Berpotensi loyal',       'color' => 'bg-indigo-500'],
            'At Risk'  => ['desc' => 'Lama tak kembali',       'color' => 'bg-amber-500'],
            'Lost'     => ['desc' => 'Hampir hilang',          'color' => 'bg-red-500'],
        ];
    @endphp
    <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h3 class="text-lg font-bold text-gray-900">Segmentasi RFM</h3>
                <p class="text-sm text-gray-400">Klaster pelanggan berbasis K-Means</p>
            </div>
            <span class="px-3 py-1 bg-red-50 text-red-600 text-[11px] font-bold rounded-full uppercase tracking-wider">
                {{ $rfmTotal }} Pelanggan
            </span>
        </div>

        @if ($rfmTotal > 0)
            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-8">
                @foreach ($rfmConfig as $label => $cfg)
                    @php $count = $rfmSegments[$label] ?? 0; @endphp
                    <div>
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm font-bold text-gray-900">{{ $label }}</span>
                            <span class="text-sm font-bold text-gray-900">{{ $count }}</span>
                        </div>
                        <div class="h-2 bg-gray-50 rounded-full overflow-hidden">
                            <div class="h-full {{ $cfg['color'] }} rounded-full"
                                 style="width: {{ $rfmMax > 0 ? round($count / $rfmMax * 100) : 0 }}%"></div>
                        </div>
                        <p class="text-[11px] text-gray-400 mt-2 font-medium">{{ $cfg['desc'] }}</p>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-sm text-gray-400 text-center py-4">Belum ada data RFM. Jalankan <code class="font-mono text-xs bg-gray-100 px-1 rounded">php artisan rfm:calculate</code> terlebih dahulu.</p>
        @endif
    </div>

    {{-- Low Stock Table --}}
    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-8 border-b border-gray-50 flex items-center justify-between bg-white">
            <div>
                <h3 class="text-lg font-bold text-gray-900">Stok Sparepart Menipis</h3>
                <p class="text-sm text-gray-400">Perlu segera di-restock</p>
            </div>
            <a wire:navigate href="{{ route('admin.spareparts.index') }}"
               class="px-5 py-2.5 bg-gray-50 text-gray-600 text-sm font-bold rounded-xl hover:bg-gray-100 transition-colors flex items-center gap-2">
                <x-heroicon-o-arrow-right class="w-4 h-4" />
                Lihat semua
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="text-[11px] font-bold text-gray-400 uppercase tracking-[0.15em] bg-gray-50/50">
                        <th class="px-8 py-5">SKU</th>
                        <th class="px-6 py-5">Nama Item</th>
                        <th class="px-6 py-5">Kategori</th>
                        <th class="px-6 py-5">Merek</th>
                        <th class="px-6 py-5">Stok</th>
                        <th class="px-6 py-5 text-right">Harga Jual</th>
                        <th class="px-8 py-5">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse ($sparepartKritis as $sp)
                        <tr class="group hover:bg-gray-50/50 transition-colors">
                            <td class="px-8 py-5">
                                <span class="text-xs font-mono text-gray-500">{{ $sp->sku }}</span>
                            </td>
                            <td class="px-6 py-5">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-lg bg-gray-50 border border-gray-100 flex items-center justify-center text-gray-400">
                                        <x-heroicon-o-cube class="w-5 h-5" />
                                    </div>
                                    <span class="text-sm font-bold text-gray-900">{{ $sp->item_name }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-5 text-sm text-gray-600">{{ $sp->category?->name ?? '—' }}</td>
                            <td class="px-6 py-5 text-sm text-gray-600">{{ $sp->brand }}</td>
                            <td class="px-6 py-5">
                                @if ($sp->stock == 0)
                                    <span class="text-sm font-black text-red-500">0 <span class="text-[11px] font-bold text-gray-400">/ {{ $sp->minimum_stock }}</span></span>
                                @else
                                    <span class="text-sm font-black text-amber-500">{{ $sp->stock }} <span class="text-[11px] font-bold text-gray-400">/ {{ $sp->minimum_stock }}</span></span>
                                @endif
                            </td>
                            <td class="px-6 py-5 text-sm font-bold text-gray-900 text-right">
                                Rp {{ number_format($sp->harga_jual, 0, ',', '.') }}
                            </td>
                            <td class="px-8 py-5">
                                @if ($sp->stock == 0)
                                    <span class="px-2.5 py-1 bg-red-50 text-red-600 text-[10px] font-bold rounded-full uppercase tracking-wider">Habis</span>
                                @else
                                    <span class="px-2.5 py-1 bg-amber-50 text-amber-600 text-[10px] font-bold rounded-full uppercase tracking-wider">Stok Menipis</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-8 py-10 text-center text-sm text-gray-400">Semua stok aman.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
