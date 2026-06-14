<div>
    {{-- Page header --}}
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Kalender Booking</h1>
            <p class="text-sm text-gray-500 mt-0.5">Tampilan booking per bulan</p>
        </div>
        <a wire:navigate href="{{ route('admin.bookings.index') }}"
           class="flex items-center gap-1.5 border border-gray-300 text-gray-700 hover:bg-gray-50 text-sm font-medium px-4 py-2 rounded-lg transition">
            <x-heroicon-o-list-bullet class="w-4 h-4" />
            Lihat Tabel
        </a>
    </div>

    {{-- Kalender --}}
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">

        {{-- Navigasi bulan --}}
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <button wire:click="previousMonth"
                    class="p-1.5 rounded-lg text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition">
                <x-heroicon-o-chevron-left class="w-5 h-5" />
            </button>
            <h2 class="text-base font-semibold text-gray-900 capitalize">{{ $monthLabel }}</h2>
            <button wire:click="nextMonth"
                    class="p-1.5 rounded-lg text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition">
                <x-heroicon-o-chevron-right class="w-5 h-5" />
            </button>
        </div>

        {{-- Header hari --}}
        <div class="grid grid-cols-7 border-b border-gray-100">
            @foreach(['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'] as $day)
            <div class="py-2 text-center text-xs font-semibold text-gray-400 uppercase tracking-wide {{ $day === 'Min' ? 'text-red-400' : '' }}">
                {{ $day }}
            </div>
            @endforeach
        </div>

        {{-- Grid minggu --}}
        <div class="divide-y divide-gray-100">
            @foreach($weeks as $week)
            <div class="grid grid-cols-7 divide-x divide-gray-100">
                @foreach($week as $day)
                @php
                    $isWeekend = $day['date']->dayOfWeek === 0; // Minggu
                @endphp
                <div class="min-h-[100px] p-2 {{ !$day['inMonth'] ? 'bg-gray-50' : '' }}">
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-sm font-medium w-7 h-7 flex items-center justify-center rounded-full
                            {{ $day['isToday'] ? 'bg-red-600 text-white' : ($day['inMonth'] ? ($isWeekend ? 'text-red-400' : 'text-gray-700') : 'text-gray-300') }}">
                            {{ $day['date']->day }}
                        </span>
                        @if($day['bookings']->isNotEmpty())
                        <span class="text-xs text-gray-400">{{ $day['bookings']->count() }}</span>
                        @endif
                    </div>

                    @foreach($day['bookings']->take(3) as $booking)
                    @php
                        $dotColor = match($booking->status) {
                            'pending'     => 'bg-amber-400',
                            'confirmed'   => 'bg-blue-500',
                            'in_progress' => 'bg-indigo-500',
                            'done'        => 'bg-green-500',
                            'cancelled'   => 'bg-gray-300',
                            default       => 'bg-gray-400',
                        };
                    @endphp
                    <div class="flex items-center gap-1 mb-0.5 truncate">
                        <span class="w-1.5 h-1.5 rounded-full flex-shrink-0 {{ $dotColor }}"></span>
                        <span class="text-xs text-gray-600 truncate leading-4">
                            {{ $booking->jam_mulai ? substr($booking->jam_mulai, 0, 5).' ' : '' }}{{ $booking->nama_pemesan }}
                        </span>
                    </div>
                    @endforeach

                    @if($day['bookings']->count() > 3)
                    <span class="text-xs text-gray-400">+{{ $day['bookings']->count() - 3 }} lainnya</span>
                    @endif
                </div>
                @endforeach
            </div>
            @endforeach
        </div>

        {{-- Legend --}}
        <div class="px-6 py-3 border-t border-gray-100 flex items-center gap-4 flex-wrap">
            @foreach([['bg-amber-400','Pending'],['bg-blue-500','Dikonfirmasi'],['bg-indigo-500','Dikerjakan'],['bg-green-500','Selesai'],['bg-gray-300','Dibatalkan']] as [$color, $label])
            <div class="flex items-center gap-1.5 text-xs text-gray-500">
                <span class="w-2 h-2 rounded-full {{ $color }}"></span>{{ $label }}
            </div>
            @endforeach
        </div>
    </div>
</div>
