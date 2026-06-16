<x-layouts::app.sidebar title="Booking Berhasil">
    <flux:main class="max-w-lg mx-auto p-6 text-center space-y-6">

        <div class="flex justify-center">
            <div class="w-16 h-16 rounded-full bg-green-100 flex items-center justify-center">
                <x-heroicon-s-check-circle class="w-10 h-10 text-green-500" />
            </div>
        </div>

        <div>
            <flux:heading size="xl">Booking Diterima!</flux:heading>
            <flux:text class="mt-2 text-zinc-500">
                Admin akan mengkonfirmasi booking kamu dalam 1×24 jam via WhatsApp.
            </flux:text>
        </div>

        @php $info = session('booking_success'); @endphp
        @if ($info)
            <flux:card class="text-left space-y-3 text-sm">
                <div class="flex justify-between">
                    <span class="text-zinc-500">No. Booking</span>
                    <span class="font-semibold text-[#DC2626]">{{ $info['booking_number'] }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-zinc-500">Tanggal</span>
                    <span class="font-medium text-zinc-800">{{ $info['tanggal'] }}</span>
                </div>
                @if (! empty($info['services']))
                    <div>
                        <span class="text-zinc-500 block mb-1">Layanan</span>
                        @foreach ($info['services'] as $svc)
                            <span class="block text-zinc-800">{{ $svc }}</span>
                        @endforeach
                    </div>
                @endif
            </flux:card>
        @endif

        <div class="flex flex-col gap-3">
            <flux:button href="{{ route('booking') }}" variant="primary" wire:navigate>
                Buat Booking Baru
            </flux:button>
            <flux:button href="{{ route('dashboard') }}" variant="ghost" wire:navigate>
                Kembali ke Dashboard
            </flux:button>
        </div>
    </flux:main>
</x-layouts::app.sidebar>
