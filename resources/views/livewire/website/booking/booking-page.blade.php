<div class="max-w-2xl mx-auto p-6 space-y-6">

    {{-- Header --}}
    <div>
        <flux:heading size="xl">Booking Servis</flux:heading>
        <flux:text class="mt-1 text-zinc-500">Langkah {{ $step }} dari 3</flux:text>
    </div>

    {{-- Progress --}}
    <div class="flex items-center gap-2">
        @foreach ([1 => 'Kendaraan', 2 => 'Layanan & Jadwal', 3 => 'Konfirmasi'] as $n => $label)
            <div class="flex items-center gap-2 {{ $loop->last ? '' : 'flex-1' }}">
                <div @class([
                    'w-8 h-8 rounded-full flex items-center justify-center text-sm font-semibold shrink-0',
                    'bg-[#DC2626] text-white' => $step >= $n,
                    'bg-zinc-200 text-zinc-500' => $step < $n,
                ])>{{ $n }}</div>
                <span @class(['text-sm hidden sm:block', 'font-medium text-zinc-800' => $step === $n, 'text-zinc-400' => $step !== $n])>{{ $label }}</span>
                @if (! $loop->last)
                    <div @class(['flex-1 h-0.5', 'bg-[#DC2626]' => $step > $n, 'bg-zinc-200' => $step <= $n])></div>
                @endif
            </div>
        @endforeach
    </div>

    {{-- Flash errors --}}
    @if (session('error'))
        <flux:callout variant="danger" icon="exclamation-triangle">
            {{ session('error') }}
        </flux:callout>
    @endif

    {{-- STEP 1: Pilih Kendaraan --}}
    @if ($step === 1)
        <flux:card class="space-y-4">
            <flux:heading size="lg">Pilih Kendaraan</flux:heading>

            @if ($this->vehicles->isEmpty() && ! $showVehicleForm)
                <flux:callout variant="warning" icon="exclamation-circle">
                    Kamu belum punya kendaraan terdaftar.
                </flux:callout>
                <flux:button wire:click="$set('showVehicleForm', true)" icon="plus">
                    Tambah Kendaraan Baru
                </flux:button>
            @elseif ($showVehicleForm)
                <form wire:submit.prevent="saveVehicle" class="space-y-4">
                    <flux:heading size="sm" class="text-zinc-600">Tambah Kendaraan Baru</flux:heading>
                    <div class="grid grid-cols-2 gap-4">
                        <flux:input wire:model="vMerk" label="Merk" placeholder="Honda, Yamaha..." required />
                        <flux:input wire:model="vModel" label="Model" placeholder="Beat, NMAX..." required />
                        <flux:input wire:model="vTipe" label="Tipe / Varian" placeholder="Opsional" />
                        <flux:input wire:model="vTahun" label="Tahun" type="number" placeholder="{{ now()->year }}" required />
                        <flux:input wire:model="vNoPolisi" label="No. Polisi" placeholder="B 1234 ABC" class="col-span-2" required />
                    </div>
                    @error('vMerk') <flux:error>{{ $message }}</flux:error> @enderror
                    @error('vModel') <flux:error>{{ $message }}</flux:error> @enderror
                    @error('vTahun') <flux:error>{{ $message }}</flux:error> @enderror
                    @error('vNoPolisi') <flux:error>{{ $message }}</flux:error> @enderror
                    <div class="flex gap-3">
                        <flux:button type="submit" variant="primary" wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="saveVehicle">Simpan Kendaraan</span>
                            <span wire:loading wire:target="saveVehicle">Menyimpan...</span>
                        </flux:button>
                        @if ($this->vehicles->isNotEmpty())
                            <flux:button wire:click="$set('showVehicleForm', false)">Batal</flux:button>
                        @endif
                    </div>
                </form>
            @else
                <div class="space-y-3">
                    @foreach ($this->vehicles as $vehicle)
                        <button
                            wire:click="selectVehicle({{ $vehicle->id }})"
                            @class([
                                'w-full text-left p-4 rounded-lg border-2 transition-colors',
                                'border-[#DC2626] bg-red-50' => $vehicleId === $vehicle->id,
                                'border-zinc-200 hover:border-zinc-400' => $vehicleId !== $vehicle->id,
                            ])
                        >
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="font-semibold text-zinc-900">{{ $vehicle->merk }} {{ $vehicle->model }} {{ $vehicle->tipe }}</p>
                                    <p class="text-sm text-zinc-500">{{ $vehicle->tahun }} · {{ $vehicle->no_polisi }}</p>
                                </div>
                                @if ($vehicleId === $vehicle->id)
                                    <x-heroicon-s-check-circle class="w-6 h-6 text-[#DC2626]" />
                                @endif
                            </div>
                        </button>
                    @endforeach
                </div>
                <flux:button wire:click="$set('showVehicleForm', true)" icon="plus" size="sm" variant="ghost">
                    Tambah Kendaraan Lain
                </flux:button>
            @endif

            @error('vehicleId') <flux:error>{{ $message }}</flux:error> @enderror

            @if (! $this->vehicles->isEmpty() || $vehicleId)
                <div class="flex justify-end pt-2">
                    <flux:button wire:click="nextStep" variant="primary" :disabled="! $vehicleId">
                        Lanjut
                    </flux:button>
                </div>
            @endif
        </flux:card>
    @endif

    {{-- STEP 2: Pilih Layanan & Jadwal --}}
    @if ($step === 2)
        <flux:card class="space-y-6">
            <flux:heading size="lg">Pilih Layanan</flux:heading>
            @if ($this->bookableServices->isEmpty())
                <flux:callout variant="info" icon="information-circle">
                    Belum ada layanan yang tersedia untuk booking saat ini.
                </flux:callout>
            @else
                <div class="space-y-2">
                    @foreach ($this->bookableServices as $svc)
                        @php $selected = $this->isServiceSelected($svc->id, 'service'); @endphp
                        <button
                            wire:click="toggleService({{ $svc->id }}, 'service', @js($svc->nama_service), {{ $svc->harga_default }})"
                            @class([
                                'w-full text-left p-4 rounded-lg border-2 transition-colors',
                                'border-[#DC2626] bg-red-50' => $selected,
                                'border-zinc-200 hover:border-zinc-400' => ! $selected,
                            ])
                        >
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="font-semibold text-zinc-900">{{ $svc->nama_service }}</p>
                                    @if ($svc->deskripsi)
                                        <p class="text-sm text-zinc-500 mt-0.5">{{ $svc->deskripsi }}</p>
                                    @endif
                                </div>
                                <div class="text-right shrink-0 ml-4">
                                    <p class="text-sm font-medium text-zinc-700">~ Rp {{ number_format($svc->harga_default, 0, ',', '.') }}</p>
                                    @if ($selected)
                                        <x-heroicon-s-check-circle class="w-5 h-5 text-[#DC2626] ml-auto mt-1" />
                                    @endif
                                </div>
                            </div>
                        </button>
                    @endforeach
                </div>
                @error('selectedServices') <flux:error>{{ $message }}</flux:error> @enderror
            @endif

            {{-- Calendar --}}
            <div>
                <flux:heading size="lg" class="mb-4">Pilih Tanggal</flux:heading>

                {{-- Month navigation --}}
                <div class="flex items-center justify-between mb-4">
                    <flux:button wire:click="previousMonth" size="sm" variant="ghost" icon="chevron-left" />
                    <span class="font-semibold text-zinc-800">
                        {{ Carbon\Carbon::create($calendarYear, $calendarMonth, 1)->translatedFormat('F Y') }}
                    </span>
                    <flux:button wire:click="nextMonth" size="sm" variant="ghost" icon="chevron-right" />
                </div>

                {{-- Day headers --}}
                <div class="grid grid-cols-7 text-center text-xs font-medium text-zinc-500 mb-1">
                    @foreach (['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'] as $day)
                        <div class="py-1">{{ $day }}</div>
                    @endforeach
                </div>

                {{-- Calendar grid --}}
                <div class="space-y-1">
                    @foreach ($this->calendarDays as $week)
                        <div class="grid grid-cols-7 gap-1">
                            @foreach ($week as $day)
                                @php
                                    $isDisabled = ! $day['isCurrentMonth']
                                        || $day['isPast']
                                        || $day['isTooFar']
                                        || ! $day['isOperational']
                                        || $day['isFull']
                                        || $day['isBlocked']
                                        || (! $day['isAvailable'] && ! $day['isSelected']);
                                @endphp
                                <button
                                    @if (! $isDisabled && $day['isCurrentMonth'])
                                        wire:click="selectDate('{{ $day['date'] }}')"
                                    @endif
                                    @class([
                                        'h-9 w-full rounded-lg text-sm font-medium transition-colors',
                                        'text-zinc-300 cursor-not-allowed' => ! $day['isCurrentMonth'],
                                        'bg-[#DC2626] text-white' => $day['isSelected'] && $day['isCurrentMonth'],
                                        'bg-red-100 text-[#DC2626] cursor-not-allowed' => $day['isFull'] && $day['isCurrentMonth'] && ! $day['isSelected'],
                                        'bg-zinc-100 text-zinc-400 cursor-not-allowed' => ($day['isPast'] || $day['isBlocked'] || ! $day['isOperational'] || $day['isTooFar']) && $day['isCurrentMonth'] && ! $day['isSelected'],
                                        'hover:bg-red-50 hover:text-[#DC2626] text-zinc-700' => $day['isAvailable'] && ! $day['isSelected'] && $day['isCurrentMonth'],
                                    ])
                                    :disabled="$isDisabled || ! $day['isCurrentMonth']"
                                >
                                    {{ $day['day'] }}
                                </button>
                            @endforeach
                        </div>
                    @endforeach
                </div>

                {{-- Legend --}}
                <div class="mt-3 flex flex-wrap gap-4 text-xs text-zinc-500">
                    <span class="flex items-center gap-1"><span class="w-3 h-3 rounded bg-[#DC2626] inline-block"></span> Terpilih</span>
                    <span class="flex items-center gap-1"><span class="w-3 h-3 rounded bg-red-100 inline-block"></span> Penuh</span>
                    <span class="flex items-center gap-1"><span class="w-3 h-3 rounded bg-zinc-100 inline-block"></span> Tidak tersedia</span>
                </div>

                @error('tanggalBooking') <flux:error class="mt-2">{{ $message }}</flux:error> @enderror
            </div>

            {{-- Keluhan --}}
            <flux:textarea
                wire:model="keluhan"
                label="Keluhan / Kebutuhan (opsional)"
                placeholder="Ceritakan keluhan atau kebutuhan kamu..."
                rows="3"
            />
        </flux:card>

        <div class="flex justify-between">
            <flux:button wire:click="prevStep" variant="ghost" icon="arrow-left">Kembali</flux:button>
            <flux:button wire:click="nextStep" variant="primary">Lanjut ke Konfirmasi</flux:button>
        </div>
    @endif

    {{-- STEP 3: Konfirmasi --}}
    @if ($step === 3)
        <flux:card class="space-y-5">
            <flux:heading size="lg">Konfirmasi Booking</flux:heading>

            @php
                $selectedVehicle = $this->vehicles->firstWhere('id', $vehicleId);
                $totalEstimasi = array_sum(array_column($selectedServices, 'harga_estimasi'));
            @endphp

            <div class="space-y-3 text-sm">
                <div class="flex justify-between border-b pb-3">
                    <span class="text-zinc-500">Kendaraan</span>
                    <span class="font-medium text-zinc-900 text-right">
                        {{ $selectedVehicle?->merk }} {{ $selectedVehicle?->model }}<br>
                        <span class="text-zinc-400 text-xs">{{ $selectedVehicle?->no_polisi }}</span>
                    </span>
                </div>

                <div class="flex justify-between border-b pb-3">
                    <span class="text-zinc-500">Tanggal</span>
                    <span class="font-medium text-zinc-900">
                        {{ Carbon\Carbon::parse($tanggalBooking)->translatedFormat('l, d M Y') }}
                    </span>
                </div>

                <div class="border-b pb-3">
                    <span class="text-zinc-500 block mb-2">Layanan</span>
                    @foreach ($selectedServices as $svc)
                        <div class="flex justify-between text-zinc-800">
                            <span>{{ $svc['nama'] }}</span>
                            <span class="text-zinc-500">~ Rp {{ number_format($svc['harga_estimasi'], 0, ',', '.') }}</span>
                        </div>
                    @endforeach
                </div>

                <div class="flex justify-between font-semibold text-zinc-900">
                    <span>Estimasi Total</span>
                    <span>Rp {{ number_format($totalEstimasi, 0, ',', '.') }}</span>
                </div>

                @if ($keluhan)
                    <div class="pt-2 text-zinc-600 text-xs bg-zinc-50 rounded-lg p-3">
                        <span class="font-medium block mb-1">Keluhan:</span>
                        {{ $keluhan }}
                    </div>
                @endif
            </div>

            <flux:callout variant="info" icon="information-circle">
                Harga final dikonfirmasi saat motor tiba di bengkel. Admin akan menghubungi kamu dalam 1×24 jam.
            </flux:callout>
        </flux:card>

        <div class="flex justify-between">
            <flux:button wire:click="prevStep" variant="ghost" icon="arrow-left">Kembali</flux:button>
            <flux:button
                wire:click="submit"
                variant="primary"
                wire:loading.attr="disabled"
                wire:target="submit"
            >
                <span wire:loading.remove wire:target="submit">Kirim Booking</span>
                <span wire:loading wire:target="submit">Memproses...</span>
            </flux:button>
        </div>
    @endif
</div>
