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
        <div>
            <h1 class="text-xl font-bold text-gray-900">Booking</h1>
            <p class="text-sm text-gray-500 mt-0.5">Daftar semua booking masuk</p>
        </div>
        <a wire:navigate href="{{ route('admin.bookings.calendar') }}"
           class="flex items-center gap-1.5 border border-gray-300 text-gray-700 hover:bg-gray-50 text-sm font-medium px-4 py-2 rounded-lg transition">
            <x-heroicon-o-calendar-days class="w-4 h-4" />
            Lihat Kalender
        </a>
    </div>

    {{-- Card --}}
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">

        {{-- Toolbar --}}
        <div class="flex items-center gap-3 px-5 py-4 border-b border-gray-100 flex-wrap">
            <div class="relative flex-1 min-w-[200px] max-w-xs">
                <x-heroicon-o-magnifying-glass class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
                <input wire:model.live.debounce.300ms="search"
                       type="text"
                       placeholder="Cari no. booking, nama, HP..."
                       class="w-full pl-9 pr-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent" />
            </div>

            <select wire:model.live="filterStatus"
                    class="border border-gray-300 text-gray-700 text-sm px-3 py-2 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-red-500">
                <option value="">Semua Status</option>
                @foreach($statusList as $s)
                    <option value="{{ $s }}">{{ ucfirst(str_replace('_', ' ', $s)) }}</option>
                @endforeach
            </select>

            <select wire:model.live="filterSource"
                    class="border border-gray-300 text-gray-700 text-sm px-3 py-2 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-red-500">
                <option value="">Semua Channel</option>
                @foreach($sourceList as $src)
                    <option value="{{ $src }}">{{ ucfirst(str_replace('_', ' ', $src)) }}</option>
                @endforeach
            </select>
        </div>

        {{-- Tabel --}}
        <table class="w-full text-sm">
            <thead>
                <tr class="text-xs font-semibold uppercase tracking-wider text-gray-500 border-b border-gray-200">
                    <th class="text-left px-5 py-3">No. Booking</th>
                    <th class="text-left px-5 py-3">Pemesan</th>
                    <th class="text-left px-5 py-3">Kendaraan</th>
                    <th class="text-left px-5 py-3">Tanggal</th>
                    <th class="text-left px-5 py-3">Waktu</th>
                    <th class="text-left px-5 py-3">Channel</th>
                    <th class="text-left px-5 py-3">Status</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($bookings as $booking)
                @php
                    $statusColor = match($booking->status) {
                        'pending'     => 'bg-amber-100 text-amber-700',
                        'confirmed'   => 'bg-blue-100 text-blue-700',
                        'in_progress' => 'bg-indigo-100 text-indigo-700',
                        'done'        => 'bg-green-100 text-green-700',
                        'cancelled'   => 'bg-gray-100 text-gray-500',
                        default       => 'bg-gray-100 text-gray-500',
                    };
                    $statusLabel = match($booking->status) {
                        'pending'     => 'Pending',
                        'confirmed'   => 'Dikonfirmasi',
                        'in_progress' => 'Dikerjakan',
                        'done'        => 'Selesai',
                        'cancelled'   => 'Dibatalkan',
                        default       => $booking->status,
                    };
                    $sourceLabel = match($booking->source) {
                        'website'  => 'Website',
                        'whatsapp' => 'WhatsApp',
                        'walk_in'  => 'Walk-in',
                        default    => $booking->source,
                    };
                @endphp
                <tr class="border-b border-gray-100 hover:bg-gray-50 transition">
                    <td class="px-5 py-4">
                        <span class="font-mono text-xs font-medium text-gray-900">{{ $booking->booking_number }}</span>
                    </td>
                    <td class="px-5 py-4">
                        <p class="font-medium text-gray-900">{{ $booking->nama_pemesan }}</p>
                        <p class="text-xs text-gray-400">{{ $booking->no_hp_pemesan }}</p>
                    </td>
                    <td class="px-5 py-4 text-gray-600">
                        @if($booking->vehicle)
                            <p class="text-xs">{{ $booking->vehicle->merk }} {{ $booking->vehicle->model }}</p>
                            <p class="font-mono text-xs text-gray-400">{{ $booking->vehicle->plat_nomor }}</p>
                        @else
                            <span class="text-gray-400">-</span>
                        @endif
                    </td>
                    <td class="px-5 py-4 text-gray-600 text-xs">
                        {{ $booking->tanggal_booking->translatedFormat('d M Y') }}
                    </td>
                    <td class="px-5 py-4 text-gray-600 text-xs">
                        {{ $booking->jam_mulai ? substr($booking->jam_mulai, 0, 5) : '-' }}
                        @if($booking->jam_selesai) – {{ substr($booking->jam_selesai, 0, 5) }} @endif
                    </td>
                    <td class="px-5 py-4">
                        <span class="text-xs text-gray-600">{{ $sourceLabel }}</span>
                    </td>
                    <td class="px-5 py-4">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $statusColor }}">
                            {{ $statusLabel }}
                        </span>
                    </td>
                    <td class="px-5 py-4">
                        <div class="flex items-center gap-2 justify-end">
                            @if($booking->status === 'pending')
                            <button wire:click="confirm({{ $booking->id }})"
                                    wire:confirm="Konfirmasi booking ini?"
                                    class="text-xs text-blue-600 hover:underline">
                                Konfirmasi
                            </button>
                            @endif
                            @if(!in_array($booking->status, ['done', 'cancelled']))
                            <button wire:click="cancel({{ $booking->id }})"
                                    wire:confirm="Batalkan booking ini?"
                                    class="text-xs text-red-500 hover:underline">
                                Batal
                            </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-5 py-12 text-center text-sm text-gray-400">
                        Tidak ada booking ditemukan
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Pagination --}}
        @if($bookings->hasPages())
        <div class="flex items-center justify-between px-5 py-3 border-t border-gray-100">
            <p class="text-sm text-gray-500">
                Menampilkan {{ $bookings->firstItem() }}–{{ $bookings->lastItem() }} dari {{ $bookings->total() }}
            </p>
            <div class="flex items-center gap-1">
                @if($bookings->onFirstPage())
                <span class="px-3 py-1.5 text-sm text-gray-400 border border-gray-200 rounded cursor-not-allowed">Sebelumnya</span>
                @else
                <button wire:click="previousPage" class="px-3 py-1.5 text-sm text-gray-700 border border-gray-300 rounded hover:bg-gray-50">Sebelumnya</button>
                @endif

                @foreach($bookings->getUrlRange(max(1, $bookings->currentPage()-2), min($bookings->lastPage(), $bookings->currentPage()+2)) as $page => $url)
                <button wire:click="gotoPage({{ $page }})"
                        class="px-3 py-1.5 text-sm rounded {{ $page === $bookings->currentPage() ? 'bg-red-600 text-white' : 'text-gray-700 border border-gray-300 hover:bg-gray-50' }}">
                    {{ $page }}
                </button>
                @endforeach

                @if($bookings->hasMorePages())
                <button wire:click="nextPage" class="px-3 py-1.5 text-sm text-gray-700 border border-gray-300 rounded hover:bg-gray-50">Selanjutnya</button>
                @else
                <span class="px-3 py-1.5 text-sm text-gray-400 border border-gray-200 rounded cursor-not-allowed">Selanjutnya</span>
                @endif
            </div>
        </div>
        @endif
    </div>
</div>
