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

                {{-- Merk — autocomplete dari DB, boleh ketik baru --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Merk <span class="text-red-600">*</span>
                    </label>
                    <input wire:model.live.debounce.300ms="merk"
                           list="merk-options"
                           type="text"
                           autocomplete="off"
                           placeholder="Honda, Yamaha, Kawasaki..."
                           class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent
                                  @error('merk') border-red-500 @enderror" />
                    <datalist id="merk-options">
                        @foreach($availableMerks as $m)
                        <option value="{{ $m }}">
                        @endforeach
                    </datalist>
                    @error('merk') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Model — cascade dari merk --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Model <span class="text-red-600">*</span>
                        @if($merk && count($availableModels) > 0)
                        <span class="text-xs font-normal text-gray-400 ml-1">({{ count($availableModels) }} pilihan)</span>
                        @endif
                    </label>
                    <input wire:model.live="model"
                           list="model-options"
                           type="text"
                           autocomplete="off"
                           placeholder="{{ $merk ? 'Pilih atau ketik model...' : 'Isi merk terlebih dahulu' }}"
                           @if(!$merk) readonly @endif
                           class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent
                                  {{ !$merk ? 'bg-gray-50 text-gray-400 cursor-not-allowed' : '' }}
                                  @error('model') border-red-500 @enderror" />
                    <datalist id="model-options">
                        @foreach($availableModels as $m)
                        <option value="{{ $m }}">
                        @endforeach
                    </datalist>
                    @error('model') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Tipe/Varian — cascade dari model --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Tipe / Varian
                        @if($model && count($availableTipes) > 0)
                        <span class="text-xs font-normal text-gray-400 ml-1">({{ count($availableTipes) }} pilihan)</span>
                        @endif
                    </label>
                    <input wire:model.live="tipe"
                           list="tipe-options"
                           type="text"
                           autocomplete="off"
                           placeholder="{{ $model ? 'Pilih atau ketik varian...' : 'Isi model terlebih dahulu' }}"
                           @if(!$model) readonly @endif
                           class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent
                                  {{ !$model ? 'bg-gray-50 text-gray-400 cursor-not-allowed' : '' }}" />
                    <datalist id="tipe-options">
                        @foreach($availableTipes as $t)
                        <option value="{{ $t }}">
                        @endforeach
                    </datalist>
                </div>

                {{-- Tahun — cascade dari model (+tipe jika diisi) --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Tahun <span class="text-red-600">*</span>
                        @if($model && count($availableYears) > 0)
                        <span class="text-xs font-normal text-gray-400 ml-1">({{ count($availableYears) }} pilihan)</span>
                        @endif
                    </label>
                    <input wire:model="tahun"
                           list="tahun-options"
                           type="number"
                           min="1970"
                           max="{{ date('Y') + 1 }}"
                           autocomplete="off"
                           placeholder="{{ $model ? date('Y') : 'Isi model terlebih dahulu' }}"
                           @if(!$model) readonly @endif
                           class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent
                                  {{ !$model ? 'bg-gray-50 text-gray-400 cursor-not-allowed' : '' }}
                                  @error('tahun') border-red-500 @enderror" />
                    <datalist id="tahun-options">
                        @foreach($availableYears as $y)
                        <option value="{{ $y }}">
                        @endforeach
                    </datalist>
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
