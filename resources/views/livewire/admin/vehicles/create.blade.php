<div class="max-w-6xl mx-auto pb-12">

    {{-- Alert Notification --}}
    @if(session('success'))
    <div class="mb-6">
        <flux:callout variant="success" icon="check-circle" class="bg-green-50 border-green-200 text-green-800">
            {{ session('success') }}
        </flux:callout>
    </div>
    @endif

    {{-- Content Grid with Alpine Scrollspy --}}
    <div x-data="{
        activeSection: 'pemilik',
        customerId: @entangle('customerId'),
        noPolisi: @entangle('noPolisi'),
        merk: @entangle('merk'),
        model: @entangle('model'),
        tipe: @entangle('tipe'),
        tahun: @entangle('tahun'),
        warna: @entangle('warna'),
        noRangka: @entangle('noRangka'),
        noMesin: @entangle('noMesin'),
        init() {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        this.activeSection = entry.target.id;
                    }
                });
            }, { rootMargin: '-20% 0px -60% 0px' });
            
            document.querySelectorAll('.form-section').forEach(el => observer.observe(el));
        },
        scrollToSection(id) {
            const el = document.getElementById(id);
            if (el) {
                el.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        }
    }" class="grid grid-cols-1 lg:grid-cols-4 gap-8 items-start">

        {{-- Left Form Columns (3/4 width) --}}
        <div class="lg:col-span-3">
            <form wire:submit="save" class="space-y-6">
                
                {{-- Card 1: Data Pemilik & Identitas --}}
                <div id="pemilik" class="form-section bg-white rounded-xl border border-gray-200/80 shadow-xs overflow-hidden scroll-mt-6">
                    <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-2.5">
                        <x-heroicon-o-user class="w-5 h-5 text-gray-500" />
                        <h2 class="text-base font-bold text-gray-800">Data Pemilik & Identitas</h2>
                    </div>
                    
                    <div class="p-6 space-y-5">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                            {{-- Pemilik --}}
                            <div class="md:col-span-2">
                                <flux:field>
                                    <flux:label>Pemilik / Pelanggan <span class="text-red-500">*</span></flux:label>
                                    <flux:select wire:model="customerId" placeholder="— Pilih pelanggan —">
                                        @foreach($customers as $customer)
                                        <option value="{{ $customer->id }}">{{ $customer->nama }}{{ $customer->no_hp ? ' ('.$customer->no_hp.')' : '' }}</option>
                                        @endforeach
                                    </flux:select>
                                    <flux:error name="customerId" />
                                </flux:field>
                            </div>

                            {{-- No. Polisi --}}
                            <div class="md:col-span-1">
                                <flux:field>
                                    <flux:label>No. Polisi <span class="text-red-500">*</span></flux:label>
                                    <flux:input wire:model="noPolisi" placeholder="B1234XYZ" class="uppercase" />
                                    <flux:error name="noPolisi" />
                                </flux:field>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Card 2: Spesifikasi Fisik Kendaraan --}}
                <div id="spesifikasi" class="form-section bg-white rounded-xl border border-gray-200/80 shadow-xs overflow-hidden scroll-mt-6">
                    <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-2.5">
                        <x-heroicon-o-wrench-screwdriver class="w-5 h-5 text-gray-500" />
                        <h2 class="text-base font-bold text-gray-800">Spesifikasi Fisik</h2>
                    </div>

                    <div class="p-6 space-y-5">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            
                            {{-- MERK --}}
                            <div
                                x-data="{
                                    val: @js($merk),
                                    open: false,
                                    opts: @js($availableMerks),
                                    get hits() {
                                        if (!this.val) return this.opts;
                                        const q = this.val.toLowerCase();
                                        return this.opts.filter(o => o.toLowerCase().includes(q));
                                    },
                                    pick(o) { this.val = o; this.open = false; $wire.set('merk', o); },
                                    onBlur() { setTimeout(() => { this.open = false; $wire.set('merk', this.val); }, 150); }
                                }"
                                class="relative"
                            >
                                <flux:field>
                                    <flux:label>Merk <span class="text-red-500">*</span></flux:label>
                                    <flux:input
                                        type="text"
                                        x-model="val"
                                        @focus="open = hits.length > 0"
                                        @input="open = hits.length > 0"
                                        @blur="onBlur()"
                                        @keydown.escape="open = false"
                                        @keydown.enter.prevent="hits.length && pick(hits[0])"
                                        autocomplete="off"
                                        placeholder="Honda, Yamaha, Kawasaki..."
                                    />
                                    <flux:error name="merk" />
                                </flux:field>

                                <ul x-show="open && hits.length > 0"
                                    x-transition:enter="transition ease-out duration-100"
                                    x-transition:enter-start="opacity-0 scale-95"
                                    x-transition:enter-end="opacity-100 scale-100"
                                    x-transition:leave="transition ease-in duration-75"
                                    x-transition:leave-start="opacity-100 scale-100"
                                    x-transition:leave-end="opacity-0 scale-95"
                                    class="absolute z-30 mt-1 w-full bg-white border border-gray-200 rounded-lg shadow-lg max-h-48 overflow-y-auto"
                                >
                                    <template x-for="o in hits" :key="o">
                                        <li @mousedown.prevent="pick(o)"
                                            x-text="o"
                                            class="px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 cursor-pointer first:rounded-t-lg last:rounded-b-lg"></li>
                                    </template>
                                </ul>
                            </div>

                            {{-- MODEL --}}
                            <div
                                x-data="{
                                    val: @js($model),
                                    open: false,
                                    opts: @js($availableModels),
                                    init() {
                                        $wire.$watch('availableModels', v => { this.opts = v ?? []; this.val = ''; });
                                    },
                                    get hits() {
                                        if (!this.val) return this.opts;
                                        const q = this.val.toLowerCase();
                                        return this.opts.filter(o => o.toLowerCase().includes(q));
                                    },
                                    pick(o) { this.val = o; this.open = false; $wire.set('model', o); },
                                    onBlur() { setTimeout(() => { this.open = false; if (this.val !== '') $wire.set('model', this.val); }, 150); }
                                }"
                                class="relative"
                            >
                                <flux:field>
                                    <flux:label>
                                        Model <span class="text-red-500">*</span>
                                        <span x-show="opts.length > 0" x-text="'(' + opts.length + ' pilihan)'"
                                              class="text-[10px] font-normal text-gray-400 ml-1"></span>
                                    </flux:label>
                                    <flux:input
                                        type="text"
                                        x-model="val"
                                        x-bind:readonly="!$wire.merk"
                                        @focus="$wire.merk && (open = hits.length > 0)"
                                        @input="$wire.merk && (open = hits.length > 0)"
                                        @blur="onBlur()"
                                        @keydown.escape="open = false"
                                        @keydown.enter.prevent="hits.length && pick(hits[0])"
                                        autocomplete="off"
                                        x-bind:placeholder="$wire.merk ? 'Pilih atau ketik model...' : 'Isi merk terlebih dahulu'"
                                        x-bind:class="!$wire.merk ? 'bg-gray-50 text-gray-400 cursor-not-allowed border-gray-200' : ''"
                                    />
                                    <flux:error name="model" />
                                </flux:field>

                                <ul x-show="open && hits.length > 0"
                                    x-transition:enter="transition ease-out duration-100"
                                    x-transition:enter-start="opacity-0 scale-95"
                                    x-transition:enter-end="opacity-100 scale-100"
                                    x-transition:leave="transition ease-in duration-75"
                                    x-transition:leave-start="opacity-100 scale-100"
                                    x-transition:leave-end="opacity-0 scale-95"
                                    class="absolute z-30 mt-1 w-full bg-white border border-gray-200 rounded-lg shadow-lg max-h-48 overflow-y-auto"
                                >
                                    <template x-for="o in hits" :key="o">
                                        <li @mousedown.prevent="pick(o)"
                                            x-text="o"
                                            class="px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 cursor-pointer first:rounded-t-lg last:rounded-b-lg"></li>
                                    </template>
                                </ul>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                            
                            {{-- TIPE --}}
                            <div
                                x-data="{
                                    val: @js($tipe),
                                    open: false,
                                    opts: @js($availableTipes),
                                    init() {
                                        $wire.$watch('availableTipes', v => { this.opts = v ?? []; this.val = ''; });
                                    },
                                    get hits() {
                                        if (!this.val) return this.opts;
                                        const q = this.val.toLowerCase();
                                        return this.opts.filter(o => o.toLowerCase().includes(q));
                                    },
                                    pick(o) { this.val = o; this.open = false; $wire.set('tipe', o); },
                                    onBlur() { setTimeout(() => { this.open = false; $wire.set('tipe', this.val); }, 150); }
                                }"
                                class="relative"
                            >
                                <flux:field>
                                    <flux:label>
                                        Tipe / Varian
                                        <span x-show="opts.length > 0" x-text="'(' + opts.length + ' pilihan)'"
                                              class="text-[10px] font-normal text-gray-400 ml-1"></span>
                                    </flux:label>
                                    <flux:input
                                        type="text"
                                        x-model="val"
                                        x-bind:readonly="!$wire.model"
                                        @focus="$wire.model && (open = hits.length > 0)"
                                        @input="$wire.model && (open = hits.length > 0)"
                                        @blur="onBlur()"
                                        @keydown.escape="open = false"
                                        @keydown.enter.prevent="hits.length && pick(hits[0])"
                                        autocomplete="off"
                                        x-bind:placeholder="$wire.model ? 'Pilih atau ketik varian...' : 'Isi model terlebih dahulu'"
                                        x-bind:class="!$wire.model ? 'bg-gray-50 text-gray-400 cursor-not-allowed border-gray-200' : ''"
                                    />
                                    <flux:error name="tipe" />
                                </flux:field>

                                <ul x-show="open && hits.length > 0"
                                    x-transition:enter="transition ease-out duration-100"
                                    x-transition:enter-start="opacity-0 scale-95"
                                    x-transition:enter-end="opacity-100 scale-100"
                                    x-transition:leave="transition ease-in duration-75"
                                    x-transition:leave-start="opacity-100 scale-100"
                                    x-transition:leave-end="opacity-0 scale-95"
                                    class="absolute z-30 mt-1 w-full bg-white border border-gray-200 rounded-lg shadow-lg max-h-48 overflow-y-auto"
                                >
                                    <template x-for="o in hits" :key="o">
                                        <li @mousedown.prevent="pick(o)"
                                            x-text="o"
                                            class="px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 cursor-pointer first:rounded-t-lg last:rounded-b-lg"></li>
                                    </template>
                                </ul>
                            </div>

                            {{-- TAHUN --}}
                            <div
                                x-data="{
                                    val: @js($tahun),
                                    open: false,
                                    opts: @js($availableYears),
                                    init() {
                                        $wire.$watch('availableYears', v => { this.opts = v ?? []; this.val = ''; });
                                    },
                                    get hits() {
                                        if (!this.val) return this.opts;
                                        return this.opts.filter(o => String(o).includes(String(this.val)));
                                    },
                                    pick(o) { this.val = String(o); this.open = false; $wire.set('tahun', String(o)); },
                                    onBlur() { setTimeout(() => { this.open = false; if (this.val) $wire.set('tahun', this.val); }, 150); }
                                }"
                                class="relative"
                            >
                                <flux:field>
                                    <flux:label>
                                        Tahun <span class="text-red-500">*</span>
                                        <span x-show="opts.length > 0" x-text="'(' + opts.length + ' pilihan)'"
                                              class="text-[10px] font-normal text-gray-400 ml-1"></span>
                                    </flux:label>
                                    <flux:input
                                        type="number"
                                        min="1970"
                                        max="{{ date('Y') + 1 }}"
                                        x-model="val"
                                        x-bind:readonly="!$wire.model"
                                        @focus="$wire.model && (open = hits.length > 0)"
                                        @input="$wire.model && (open = hits.length > 0)"
                                        @blur="onBlur()"
                                        @keydown.escape="open = false"
                                        @keydown.enter.prevent="hits.length && pick(hits[0])"
                                        autocomplete="off"
                                        x-bind:placeholder="$wire.model ? '{{ date('Y') }}' : 'Isi model terlebih dahulu'"
                                        x-bind:class="!$wire.model ? 'bg-gray-50 text-gray-400 cursor-not-allowed border-gray-200' : ''"
                                    />
                                    <flux:error name="tahun" />
                                </flux:field>

                                <ul x-show="open && hits.length > 0"
                                    x-transition:enter="transition ease-out duration-100"
                                    x-transition:enter-start="opacity-0 scale-95"
                                    x-transition:enter-end="opacity-100 scale-100"
                                    x-transition:leave="transition ease-in duration-75"
                                    x-transition:leave-start="opacity-100 scale-100"
                                    x-transition:leave-end="opacity-0 scale-95"
                                    class="absolute z-30 mt-1 w-full bg-white border border-gray-200 rounded-lg shadow-lg max-h-48 overflow-y-auto"
                                >
                                    <template x-for="o in hits" :key="o">
                                        <li @mousedown.prevent="pick(o)"
                                            x-text="o"
                                            class="px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 cursor-pointer first:rounded-t-lg last:rounded-b-lg"></li>
                                    </template>
                                </ul>
                            </div>

                            {{-- WARNA --}}
                            <div>
                                <flux:field>
                                    <flux:label>Warna</flux:label>
                                    <flux:input wire:model="warna" placeholder="Contoh: Hitam, Merah Doff..." />
                                    <flux:error name="warna" />
                                </flux:field>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Card 3: Rincian Dokumen & Nomor Seri --}}
                <div id="seri" class="form-section bg-white rounded-xl border border-gray-200/80 shadow-xs overflow-hidden scroll-mt-6">
                    <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-2.5">
                        <x-heroicon-o-identification class="w-5 h-5 text-gray-500" />
                        <h2 class="text-base font-bold text-gray-800">Nomor Seri Kendaraan (Opsional)</h2>
                    </div>

                    <div class="p-6 space-y-5">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            {{-- No. Rangka --}}
                            <div>
                                <flux:field>
                                    <flux:label>Nomor Rangka</flux:label>
                                    <flux:input wire:model="noRangka" placeholder="MH1..." class="uppercase" />
                                    <flux:error name="noRangka" />
                                </flux:field>
                            </div>
                            
                            {{-- No. Mesin --}}
                            <div>
                                <flux:field>
                                    <flux:label>Nomor Mesin</flux:label>
                                    <flux:input wire:model="noMesin" placeholder="K15E..." class="uppercase" />
                                    <flux:error name="noMesin" />
                                </flux:field>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Card 4: Catatan Tambahan --}}
                <div id="catatan" class="form-section bg-white rounded-xl border border-gray-200/80 shadow-xs overflow-hidden scroll-mt-6">
                    <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-2.5">
                        <x-heroicon-o-document-text class="w-5 h-5 text-gray-500" />
                        <h2 class="text-base font-bold text-gray-800">Catatan Internal</h2>
                    </div>

                    <div class="p-6 space-y-5">
                        <div>
                            <flux:field>
                                <flux:label>Catatan Tambahan</flux:label>
                                <flux:textarea wire:model="catatan" rows="5" placeholder="Tulis catatan khusus..." class="resize-none" />
                                <flux:error name="catatan" />
                            </flux:field>
                        </div>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="flex items-center gap-3 pt-4">
                    <flux:button type="submit" variant="primary" class="bg-red-600 hover:bg-red-700 text-white border-none flex items-center gap-2 cursor-pointer shadow-sm">
                        <div wire:loading wire:target="save" class="w-3.5 h-3.5 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
                        <span wire:loading.remove wire:target="save">
                            <x-heroicon-o-check class="w-4 h-4 inline-block -mt-0.5" />
                            {{ $isEdit ? 'Simpan Perubahan' : 'Tambah Kendaraan' }}
                        </span>
                        <span wire:loading wire:target="save">Menyimpan...</span>
                    </flux:button>
                    <flux:button href="{{ route('admin.vehicles.index') }}" wire:navigate variant="ghost" class="hover:bg-gray-100 cursor-pointer">
                        Batal
                    </flux:button>
                </div>

            </form>
        </div>

        {{-- Right Navigation Sidebar (1/4 width) --}}
        <div class="lg:col-span-1 lg:sticky lg:top-6 space-y-6">
            
            {{-- Navigation List --}}
            <div class="space-y-3.5 pl-1">
                <button type="button" @click="scrollToSection('pemilik')" class="w-full text-left block border-l-2 pl-4 py-1 text-sm font-medium transition cursor-pointer"
                   :class="activeSection === 'pemilik' ? 'border-red-600 text-red-600 font-semibold' : 'border-transparent text-gray-500 hover:text-gray-800'">
                    Data Pemilik & Identitas
                </button>
                <button type="button" @click="scrollToSection('spesifikasi')" class="w-full text-left block border-l-2 pl-4 py-1 text-sm font-medium transition cursor-pointer"
                   :class="activeSection === 'spesifikasi' ? 'border-red-600 text-red-600 font-semibold' : 'border-transparent text-gray-500 hover:text-gray-800'">
                    Spesifikasi Fisik
                </button>
                <button type="button" @click="scrollToSection('seri')" class="w-full text-left block border-l-2 pl-4 py-1 text-sm font-medium transition cursor-pointer"
                   :class="activeSection === 'seri' ? 'border-red-600 text-red-600 font-semibold' : 'border-transparent text-gray-500 hover:text-gray-800'">
                    Nomor Seri Kendaraan
                </button>
                <button type="button" @click="scrollToSection('catatan')" class="w-full text-left block border-l-2 pl-4 py-1 text-sm font-medium transition cursor-pointer"
                   :class="activeSection === 'catatan' ? 'border-red-600 text-red-600 font-semibold' : 'border-transparent text-gray-500 hover:text-gray-800'">
                    Catatan Internal
                </button>
            </div>

            {{-- Progress Card --}}
            <div class="bg-white rounded-xl border border-gray-200 p-5 space-y-3 shadow-xs">
                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block">Progress</span>
                
                {{-- Dynamic Progress Bar --}}
                <div class="w-full bg-gray-100 rounded-full h-1.5 overflow-hidden">
                    <div class="bg-red-600 h-1.5 rounded-full transition-all duration-500"
                         :style="{
                             width: (
                                 (customerId ? 25 : 0) + 
                                 (noPolisi ? 15 : 0) + 
                                 (merk ? 15 : 0) + 
                                 (model ? 15 : 0) + 
                                 (tahun ? 15 : 0) +
                                 (noRangka || noMesin ? 15 : 0)
                             ) + '%'
                         }">
                    </div>
                </div>
                
                <p class="text-xs text-gray-500 leading-relaxed">
                    Lengkapi informasi untuk melanjutkan pendaftaran.
                </p>
            </div>
        </div>

    </div>
</div>
