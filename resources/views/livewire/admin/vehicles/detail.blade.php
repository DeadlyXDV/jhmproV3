<div>
    @php
        $specCategories = [
            'Cylinder Head'  => ['cylinder_head','porting_polish','klep_in','klep_ex','per_klep','noken_as'],
            'Cylinder Block' => ['cylinder_block','boring_size','piston','piston_ring','pen_piston'],
            'Crankshaft'     => ['crankshaft','stroke','big_end','small_end'],
            'Kopling'        => ['kopling','per_kopling'],
            'Fuel System'    => ['karburator_injeksi','filter_udara','knalpot'],
            'Pengapian'      => ['pengapian_type','cdi_ecu','koil','busi'],
            'Kelistrikan'    => ['kelistrikan_acg','kelistrikan_aki'],
            'Transmisi'      => ['rasio_gigi','gir_depan','gir_belakang','rantai'],
        ];
        $specLabels = [
            'cylinder_head'=>'Cylinder Head','porting_polish'=>'Porting & Polish','klep_in'=>'Klep In',
            'klep_ex'=>'Klep Ex','per_klep'=>'Per Klep','noken_as'=>'Noken As',
            'cylinder_block'=>'Cylinder Block','boring_size'=>'Boring Size','piston'=>'Piston',
            'piston_ring'=>'Piston Ring','pen_piston'=>'Pen Piston',
            'crankshaft'=>'Crankshaft','stroke'=>'Stroke','big_end'=>'Big End','small_end'=>'Small End',
            'kopling'=>'Kopling','per_kopling'=>'Per Kopling',
            'karburator_injeksi'=>'Karburator / Injeksi','filter_udara'=>'Filter Udara','knalpot'=>'Knalpot',
            'pengapian_type'=>'Tipe Pengapian','cdi_ecu'=>'CDI / ECU','koil'=>'Koil','busi'=>'Busi',
            'kelistrikan_acg'=>'ACG / Generator','kelistrikan_aki'=>'Aki',
            'rasio_gigi'=>'Rasio Gigi','gir_depan'=>'Gir Depan','gir_belakang'=>'Gir Belakang','rantai'=>'Rantai',
        ];
    @endphp

    {{-- Flash message --}}
    @if(session('success'))
    <div class="mb-4 px-4 py-3 bg-green-50 border border-green-200 text-green-700 text-sm rounded-lg flex items-center gap-2">
        <x-heroicon-o-check-circle class="w-4 h-4 flex-shrink-0" />
        {{ session('success') }}
    </div>
    @endif

    {{-- Breadcrumb --}}
    <div class="flex items-center gap-2 text-sm text-gray-500 mb-4">
        <a wire:navigate href="{{ route('admin.vehicles.index') }}" class="hover:text-gray-700">Kendaraan</a>
        <span>/</span>
        <span class="text-gray-900 font-medium">{{ $vehicle->merk }} {{ $vehicle->model }}</span>
    </div>

    {{-- Header kendaraan --}}
    <div class="bg-white rounded-xl shadow-sm p-6 mb-5">
        <div class="flex items-start justify-between">
            <div>
                <h1 class="text-xl font-bold text-gray-900">
                    {{ $vehicle->merk }} {{ $vehicle->model }} {{ $vehicle->tahun }}
                </h1>
                <div class="flex items-center gap-4 mt-1.5">
                    <span class="text-red-600 font-semibold text-sm tracking-wide">{{ $vehicle->no_polisi ?: '-' }}</span>
                    @if($vehicle->warna)
                    <span class="text-sm text-gray-500">· {{ $vehicle->warna }}</span>
                    @endif
                    @if($vehicle->tipe)
                    <span class="text-sm text-gray-500">· {{ $vehicle->tipe }}</span>
                    @endif
                </div>
                @if($vehicle->customer)
                <p class="text-sm text-gray-500 mt-1">
                    Pemilik:
                    <a wire:navigate href="{{ route('admin.customers.show', $vehicle->customer) }}"
                       class="text-red-600 hover:underline">{{ $vehicle->customer->nama }}</a>
                </p>
                @endif
            </div>
            <a wire:navigate href="{{ route('admin.vehicles.edit', $vehicle) }}"
               class="flex items-center gap-1.5 text-sm text-gray-600 hover:text-blue-600 border border-gray-200 hover:border-blue-300 px-3 py-1.5 rounded-lg transition">
                <x-heroicon-o-pencil class="w-4 h-4" />
                Edit
            </a>
        </div>

        <div class="grid grid-cols-3 gap-4 mt-5 pt-5 border-t border-gray-100">
            <div>
                <p class="text-xs text-gray-500">Total Servis</p>
                <p class="text-lg font-bold text-gray-900 mt-0.5">{{ $vehicle->total_servis }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500">No. Rangka</p>
                <p class="text-sm font-medium text-gray-700 mt-0.5">{{ $vehicle->no_rangka ?: '-' }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500">No. Mesin</p>
                <p class="text-sm font-medium text-gray-700 mt-0.5">{{ $vehicle->no_mesin ?: '-' }}</p>
            </div>
        </div>
    </div>

    @if(session('success'))
    <div class="mb-4 flex items-center gap-2 bg-green-50 border border-green-200 rounded-lg px-4 py-3 text-sm text-green-700">
        <x-heroicon-o-check-circle class="w-4 h-4 flex-shrink-0" />
        {{ session('success') }}
    </div>
    @endif

    {{-- Tabs --}}
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="flex border-b border-gray-200">
            @foreach(['specs' => 'Spek Mesin', 'logs' => 'History Modifikasi', 'invoices' => 'Invoice'] as $tab => $label)
            <button wire:click="setTab('{{ $tab }}')"
                    class="px-5 py-3 text-sm font-medium transition border-b-2
                           {{ $activeTab === $tab ? 'border-red-600 text-red-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                {{ $label }}
            </button>
            @endforeach
        </div>

        {{-- Tab: Spek Mesin --}}
        @if($activeTab === 'specs')
        <div class="p-6">
            @if(!$editingSpecs)
            <div class="flex justify-end mb-5">
                <button wire:click="startEditSpecs"
                        class="flex items-center gap-1.5 border border-gray-300 text-gray-700 text-sm px-4 py-2 rounded-lg hover:bg-gray-50 transition">
                    <x-heroicon-o-pencil class="w-4 h-4" />
                    Edit Spek Mesin
                </button>
            </div>
            @endif

            <div class="grid grid-cols-2 gap-4">
                @foreach($specCategories as $categoryName => $fields)
                <div class="border border-gray-100 rounded-xl p-4">
                    <h3 class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-3">{{ $categoryName }}</h3>
                    <div class="space-y-2">
                        @foreach($fields as $field)
                        <div class="flex items-start justify-between gap-2">
                            <span class="text-xs text-gray-500 w-36 flex-shrink-0">{{ $specLabels[$field] }}</span>
                            @if($editingSpecs)
                            <input wire:model="specs.{{ $field }}"
                                   type="text"
                                   class="flex-1 text-xs px-2 py-1 border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-red-500" />
                            @else
                            <span class="text-xs text-gray-700 font-medium text-right">
                                {{ $specs[$field] ?: '-' }}
                            </span>
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>

            {{-- Catatan tambahan --}}
            <div class="mt-4 border border-gray-100 rounded-xl p-4">
                <h3 class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-2">Catatan Tambahan</h3>
                @if($editingSpecs)
                <textarea wire:model="specs.catatan_tambahan" rows="3"
                          class="w-full text-sm px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-red-500 resize-none"></textarea>
                @else
                <p class="text-sm text-gray-600">{{ $specs['catatan_tambahan'] ?: '-' }}</p>
                @endif
            </div>

            @if($editingSpecs)
            <div class="flex items-center gap-3 mt-5">
                <button wire:click="saveSpecs"
                        class="bg-red-600 hover:bg-red-700 text-white text-sm font-medium px-5 py-2.5 rounded-lg transition">
                    Simpan Spek
                </button>
                <button wire:click="cancelEditSpecs"
                        class="text-sm text-gray-600 hover:text-gray-800 px-4 py-2.5">Batal</button>
            </div>
            @endif
        </div>
        @endif

        {{-- Tab: History Modifikasi --}}
        @if($activeTab === 'logs')
        <div class="p-6">
            @if($modLogs->isEmpty())
            <p class="text-sm text-gray-400 text-center py-8">Belum ada history modifikasi</p>
            @else
            <div class="space-y-4">
                @foreach($modLogs as $log)
                <div class="border border-gray-100 rounded-xl p-4">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="font-medium text-gray-900">{{ $log->judul }}</p>
                            @if($log->deskripsi)
                            <p class="text-sm text-gray-500 mt-1">{{ $log->deskripsi }}</p>
                            @endif
                        </div>
                        <div class="text-right flex-shrink-0 ml-4">
                            <p class="text-xs text-gray-500">
                                {{ \Carbon\Carbon::parse($log->logged_at)->translatedFormat('d M Y, H:i') }}
                            </p>
                            @if($log->user)
                            <p class="text-xs text-gray-400 mt-0.5">oleh {{ $log->user->name }}</p>
                            @endif
                        </div>
                    </div>
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
                @forelse($invoices as $invoice)
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
                            [$dot, $label, $textColor] = match($invoice->payment_status) {
                                'paid'    => ['bg-green-500', 'Lunas', 'text-green-700'],
                                'partial' => ['bg-amber-500', 'Sebagian', 'text-amber-700'],
                                default   => ['bg-red-600', 'Belum Lunas', 'text-red-700'],
                            };
                        @endphp
                        <span class="flex items-center gap-1.5 text-xs font-medium {{ $textColor }}">
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

    </div>
</div>
