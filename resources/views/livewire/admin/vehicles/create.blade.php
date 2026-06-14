<div>
    {{-- Flash --}}
    @if(session('success'))
    <div class="mb-4 px-4 py-3 bg-green-50 border border-green-200 text-green-700 text-sm rounded-lg flex items-center gap-2">
        <x-heroicon-o-check-circle class="w-4 h-4 flex-shrink-0" />
        {{ session('success') }}
    </div>
    @endif

    {{-- Page header --}}
    <div class="mb-6">
        <div class="flex items-center gap-2 text-sm text-gray-500 mb-1">
            <a wire:navigate href="{{ route('admin.vehicles.index') }}" class="hover:text-gray-700">Kendaraan</a>
            <span>/</span>
            <span>{{ $isEdit ? 'Edit' : 'Tambah Baru' }}</span>
        </div>
        <h1 class="text-xl font-bold text-gray-900">{{ $isEdit ? 'Edit Kendaraan' : 'Tambah Kendaraan' }}</h1>
    </div>

    <form wire:submit="save" class="max-w-2xl space-y-5">

        {{-- Pemilik --}}
        <div class="bg-white rounded-xl shadow-sm p-6 space-y-5">
            <h2 class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Data Pemilik</h2>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Pemilik / Pelanggan <span class="text-red-600">*</span></label>
                <select wire:model="customerId"
                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent
                               @error('customerId') border-red-500 @enderror">
                    <option value="">— Pilih pelanggan —</option>
                    @foreach($customers as $customer)
                    <option value="{{ $customer->id }}">{{ $customer->nama }}{{ $customer->no_hp ? ' ('.$customer->no_hp.')' : '' }}</option>
                    @endforeach
                </select>
                @error('customerId') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        {{-- Info Kendaraan (cascading) --}}
        <div class="bg-white rounded-xl shadow-sm p-6 space-y-5">
            <h2 class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Info Kendaraan</h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                {{-- MERK — Alpine combobox, pilih dari DB atau ketik baru --}}
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
                        onBlur() { setTimeout(() => { this.open = false; $wire.set('merk', this.val); }, 120); }
                    }"
                    class="relative"
                >
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Merk <span class="text-red-600">*</span>
                    </label>
                    <input type="text"
                           x-model="val"
                           @focus="open = hits.length > 0"
                           @input="open = hits.length > 0"
                           @blur="onBlur()"
                           @keydown.escape="open = false"
                           @keydown.enter.prevent="hits.length && pick(hits[0])"
                           autocomplete="off"
                           placeholder="Honda, Yamaha, Kawasaki..."
                           class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent @error('merk') border-red-500 @enderror" />
                    <ul x-show="open && hits.length > 0"
                        class="absolute z-30 mt-1 w-full bg-white border border-gray-200 rounded-lg shadow-lg max-h-48 overflow-y-auto">
                        <template x-for="o in hits" :key="o">
                            <li @mousedown.prevent="pick(o)"
                                x-text="o"
                                class="px-3 py-2.5 text-sm text-gray-700 hover:bg-red-50 hover:text-red-700 cursor-pointer first:rounded-t-lg last:rounded-b-lg"></li>
                        </template>
                    </ul>
                    @error('merk') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- MODEL — cascade dari merk --}}
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
                        onBlur() { setTimeout(() => { this.open = false; if (this.val !== '') $wire.set('model', this.val); }, 120); }
                    }"
                    class="relative"
                >
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Model <span class="text-red-600">*</span>
                        <span x-show="opts.length > 0" x-text="'(' + opts.length + ' pilihan)'"
                              class="text-xs font-normal text-gray-400 ml-1"></span>
                    </label>
                    <input type="text"
                           x-model="val"
                           :readonly="!$wire.merk"
                           @focus="$wire.merk && (open = hits.length > 0)"
                           @input="$wire.merk && (open = hits.length > 0)"
                           @blur="onBlur()"
                           @keydown.escape="open = false"
                           @keydown.enter.prevent="hits.length && pick(hits[0])"
                           autocomplete="off"
                           :placeholder="$wire.merk ? 'Pilih atau ketik model...' : 'Isi merk terlebih dahulu'"
                           :class="!$wire.merk ? 'bg-gray-50 text-gray-400 cursor-not-allowed' : ''"
                           class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent @error('model') border-red-500 @enderror" />
                    <ul x-show="open && hits.length > 0"
                        class="absolute z-30 mt-1 w-full bg-white border border-gray-200 rounded-lg shadow-lg max-h-48 overflow-y-auto">
                        <template x-for="o in hits" :key="o">
                            <li @mousedown.prevent="pick(o)"
                                x-text="o"
                                class="px-3 py-2.5 text-sm text-gray-700 hover:bg-red-50 hover:text-red-700 cursor-pointer first:rounded-t-lg last:rounded-b-lg"></li>
                        </template>
                    </ul>
                    @error('model') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- TIPE — cascade dari model --}}
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
                        onBlur() { setTimeout(() => { this.open = false; $wire.set('tipe', this.val); }, 120); }
                    }"
                    class="relative"
                >
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Tipe / Varian
                        <span x-show="opts.length > 0" x-text="'(' + opts.length + ' pilihan)'"
                              class="text-xs font-normal text-gray-400 ml-1"></span>
                    </label>
                    <input type="text"
                           x-model="val"
                           :readonly="!$wire.model"
                           @focus="$wire.model && (open = hits.length > 0)"
                           @input="$wire.model && (open = hits.length > 0)"
                           @blur="onBlur()"
                           @keydown.escape="open = false"
                           @keydown.enter.prevent="hits.length && pick(hits[0])"
                           autocomplete="off"
                           :placeholder="$wire.model ? 'Pilih atau ketik varian...' : 'Isi model terlebih dahulu'"
                           :class="!$wire.model ? 'bg-gray-50 text-gray-400 cursor-not-allowed' : ''"
                           class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent" />
                    <ul x-show="open && hits.length > 0"
                        class="absolute z-30 mt-1 w-full bg-white border border-gray-200 rounded-lg shadow-lg max-h-48 overflow-y-auto">
                        <template x-for="o in hits" :key="o">
                            <li @mousedown.prevent="pick(o)"
                                x-text="o"
                                class="px-3 py-2.5 text-sm text-gray-700 hover:bg-red-50 hover:text-red-700 cursor-pointer first:rounded-t-lg last:rounded-b-lg"></li>
                        </template>
                    </ul>
                </div>

                {{-- TAHUN — cascade dari model (+tipe jika diisi) --}}
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
                        onBlur() { setTimeout(() => { this.open = false; if (this.val) $wire.set('tahun', this.val); }, 120); }
                    }"
                    class="relative"
                >
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Tahun <span class="text-red-600">*</span>
                        <span x-show="opts.length > 0" x-text="'(' + opts.length + ' pilihan)'"
                              class="text-xs font-normal text-gray-400 ml-1"></span>
                    </label>
                    <input type="number"
                           x-model="val"
                           min="1970" max="{{ date('Y') + 1 }}"
                           :readonly="!$wire.model"
                           @focus="$wire.model && (open = hits.length > 0)"
                           @input="$wire.model && (open = hits.length > 0)"
                           @blur="onBlur()"
                           @keydown.escape="open = false"
                           @keydown.enter.prevent="hits.length && pick(hits[0])"
                           autocomplete="off"
                           :placeholder="$wire.model ? '{{ date('Y') }}' : 'Isi model terlebih dahulu'"
                           :class="!$wire.model ? 'bg-gray-50 text-gray-400 cursor-not-allowed' : ''"
                           class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent @error('tahun') border-red-500 @enderror" />
                    <ul x-show="open && hits.length > 0"
                        class="absolute z-30 mt-1 w-full bg-white border border-gray-200 rounded-lg shadow-lg max-h-48 overflow-y-auto">
                        <template x-for="o in hits" :key="o">
                            <li @mousedown.prevent="pick(o)"
                                x-text="o"
                                class="px-3 py-2.5 text-sm text-gray-700 hover:bg-red-50 hover:text-red-700 cursor-pointer first:rounded-t-lg last:rounded-b-lg"></li>
                        </template>
                    </ul>
                    @error('tahun') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- No. Polisi --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">No. Polisi <span class="text-red-600">*</span></label>
                    <input wire:model="noPolisi" type="text" placeholder="B1234XYZ"
                           class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent uppercase
                                  @error('noPolisi') border-red-500 @enderror" />
                    @error('noPolisi') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Warna --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Warna</label>
                    <input wire:model="warna" type="text" placeholder="Merah, Hitam, Putih..."
                           class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent" />
                </div>

            </div>
        </div>

        {{-- Nomor Seri --}}
        <div class="bg-white rounded-xl shadow-sm p-6 space-y-4">
            <h2 class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Nomor Seri (Opsional)</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">No. Rangka</label>
                    <input wire:model="noRangka" type="text" placeholder="MH1..."
                           class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent font-mono" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">No. Mesin</label>
                    <input wire:model="noMesin" type="text" placeholder="K15E..."
                           class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent font-mono" />
                </div>
            </div>
        </div>

        {{-- Catatan --}}
        <div class="bg-white rounded-xl shadow-sm p-6">
            <label class="block text-sm font-medium text-gray-700 mb-1">Catatan Internal</label>
            <textarea wire:model="catatan" rows="3" placeholder="Kondisi khusus, modifikasi awal, dll..."
                      class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent resize-none"></textarea>
        </div>

        {{-- Actions --}}
        <div class="flex items-center gap-3">
            <button type="submit"
                    class="bg-red-600 hover:bg-red-700 text-white text-sm font-medium px-5 py-2.5 rounded-lg transition flex items-center gap-2">
                <div wire:loading wire:target="save" class="w-3.5 h-3.5 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
                <span wire:loading.remove wire:target="save">{{ $isEdit ? 'Simpan Perubahan' : 'Tambah Kendaraan' }}</span>
                <span wire:loading wire:target="save">Menyimpan...</span>
            </button>
            <a wire:navigate href="{{ route('admin.vehicles.index') }}"
               class="text-sm text-gray-600 hover:text-gray-800 px-4 py-2.5">Batal</a>
        </div>

    </form>
</div>
