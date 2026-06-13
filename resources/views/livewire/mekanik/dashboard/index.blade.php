<div>
    <div class="mb-6">
        <h1 class="text-xl font-bold text-gray-900">Halo, {{ auth('admin')->user()?->name }}</h1>
        <p class="text-sm text-gray-500 mt-0.5">Ini adalah daftar pekerjaan kamu hari ini</p>
    </div>

    {{-- Stat kecil --}}
    <div class="grid grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow-sm p-5">
            <p class="text-sm text-gray-500">Work Order Antrian</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $antrian->count() }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-5">
            <p class="text-sm text-gray-500">Sedang Dikerjakan</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $sedangProses->count() }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-5">
            <p class="text-sm text-gray-500">Selesai Hari Ini</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $selesaiHariIni }}</p>
        </div>
    </div>

    {{-- WO Sedang Proses --}}
    @if($sedangProses->isNotEmpty())
    <div class="bg-blue-50 border border-blue-200 rounded-xl p-5 mb-5">
        <h2 class="text-sm font-semibold text-blue-800 mb-3">Sedang Dikerjakan</h2>
        <div class="space-y-3">
            @foreach($sedangProses as $wo)
            <div class="bg-white rounded-lg px-4 py-3 flex items-center justify-between">
                <div>
                    <p class="font-medium text-gray-900">{{ $wo->wo_number }}</p>
                    <p class="text-sm text-gray-500">
                        {{ $wo->vehicle?->merk }} {{ $wo->vehicle?->model }}
                        · {{ $wo->vehicle?->customer?->nama ?? '-' }}
                    </p>
                    @if($wo->keluhan_customer)
                    <p class="text-xs text-gray-400 mt-0.5">{{ Str::limit($wo->keluhan_customer, 60) }}</p>
                    @endif
                </div>
                <a href="{{ route('mekanik.work-orders.show', $wo) }}"
                   class="text-blue-600 hover:underline text-sm font-medium">Detail →</a>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- WO Antrian --}}
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100">
            <h2 class="text-sm font-semibold text-gray-900">Antrian Work Order</h2>
        </div>

        @if($antrian->isEmpty())
        <div class="px-5 py-10 text-center text-sm text-gray-400">Tidak ada antrian saat ini</div>
        @else
        <div class="divide-y divide-gray-50">
            @foreach($antrian as $wo)
            <div class="px-5 py-4 flex items-center justify-between hover:bg-gray-50 transition">
                <div>
                    <p class="font-medium text-gray-900">{{ $wo->wo_number }}</p>
                    <p class="text-sm text-gray-500 mt-0.5">
                        {{ $wo->vehicle?->merk }} {{ $wo->vehicle?->model }} ·
                        <span class="text-red-600 font-medium">{{ $wo->vehicle?->no_polisi }}</span>
                    </p>
                    <p class="text-xs text-gray-400">{{ $wo->vehicle?->customer?->nama ?? '-' }}</p>
                </div>
                <a href="{{ route('mekanik.work-orders.show', $wo) }}"
                   class="text-sm text-gray-400 hover:text-gray-700 transition">
                    <x-heroicon-o-arrow-right class="w-4 h-4" />
                </a>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</div>
