<div class="flex flex-col h-screen bg-[#F3F4F6]">

    {{-- Topbar POS --}}
    <header class="h-14 bg-white border-b border-gray-100 flex items-center px-6 gap-4 flex-none shadow-sm">
        {{-- Logo --}}
        <div class="flex items-center gap-2 mr-4">
            <div class="w-8 h-8 bg-[#E11D22] rounded-lg flex items-center justify-center">
                <svg viewBox="0 0 40 42" class="w-4 h-4 text-white"><path fill="currentColor" fill-rule="evenodd" clip-rule="evenodd" d="M17.2 5.633 8.6.855 0 5.633v26.51l16.2 9 16.2-9v-8.442l7.6-4.223V9.856l-8.6-4.777-8.6 4.777V18.3l-5.6 3.111V5.633ZM38 18.301l-5.6 3.11v-6.157l5.6-3.11V18.3Zm-1.06-7.856-5.54 3.078-5.54-3.079 5.54-3.078 5.54 3.079ZM24.8 18.3v-6.157l5.6 3.111v6.158L24.8 18.3Zm-1 1.732 5.54 3.078-13.14 7.302-5.54-3.078 13.14-7.3v-.002Zm-16.2 7.89 7.6 4.222V38.3L2 30.966V7.92l5.6 3.111v16.892ZM8.6 9.3 3.06 6.222 8.6 3.143l5.54 3.08L8.6 9.3Zm21.8 15.51-13.2 7.334V38.3l13.2-7.334v-6.156ZM9.6 11.034l5.6-3.11v14.6l-5.6 3.11v-14.6Z" /></svg>
            </div>
            <span class="text-sm font-bold text-gray-900">JHM<span class="text-[#E11D22]">Pro</span> <span class="text-gray-400 font-normal">/ Kasir POS</span></span>
        </div>

        <p class="text-xs text-gray-400">{{ now()->isoFormat('dddd, D MMMM Y') }}</p>

        <div class="flex-1"></div>

        {{-- Mode Toggle --}}
        <div class="flex gap-1 bg-gray-100 rounded-xl p-1">
            <button wire:click="$set('mode', 'walk_in')"
                class="px-4 py-1.5 text-xs font-semibold rounded-lg transition-colors
                    {{ $mode === 'walk_in' ? 'bg-red-600 text-white shadow' : 'text-gray-600 hover:bg-gray-200' }}">
                Walk-In
            </button>
            <button wire:click="$set('mode', 'work_order')"
                class="px-4 py-1.5 text-xs font-semibold rounded-lg transition-colors
                    {{ $mode === 'work_order' ? 'bg-red-600 text-white shadow' : 'text-gray-600 hover:bg-gray-200' }}">
                Dari Work Order
            </button>
        </div>

        <a href="{{ route('admin.dashboard') }}" wire:navigate
            class="flex items-center gap-1.5 text-xs font-semibold text-gray-500 hover:text-gray-800 transition-colors ml-4">
            <x-heroicon-o-arrow-left class="w-4 h-4" />
            Dashboard
        </a>
    </header>

    {{-- Success Banner --}}
    @if($invoiceSuccessId)
        <div class="bg-green-50 border-b border-green-200 px-6 py-3 flex items-center gap-3">
            <x-heroicon-o-check-circle class="w-5 h-5 text-green-600 flex-none" />
            <span class="text-sm text-green-700 font-medium">Invoice berhasil dibuat!</span>
            <a href="{{ route('admin.invoices.show', $invoiceSuccessId) }}" wire:navigate
                class="text-sm font-semibold text-green-700 underline ml-1">Lihat Invoice</a>
            <button wire:click="$set('invoiceSuccessId', null)"
                class="ml-auto text-green-600 hover:text-green-800">
                <x-heroicon-o-x-mark class="w-4 h-4" />
            </button>
        </div>
    @endif

    {{-- Main Content --}}
    <div class="flex flex-1 overflow-hidden">

        {{-- Panel Kiri ~60% --}}
        <div class="flex-1 overflow-y-auto p-6 space-y-5">

            {{-- MODE: WALK-IN --}}
            @if($mode === 'walk_in')

                {{-- Tipe Transaksi --}}
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Tipe Transaksi</p>
                    <div class="flex gap-2">
                        <button wire:click="$set('walkInTipe', 'sparepart')"
                            class="px-4 py-2 text-sm font-semibold rounded-xl border-2 transition-all
                                {{ $walkInTipe === 'sparepart' ? 'border-red-600 bg-red-50 text-red-600' : 'border-gray-200 text-gray-600 hover:border-gray-300' }}">
                            Jual Sparepart
                        </button>
                        <button wire:click="$set('walkInTipe', 'servis')"
                            class="px-4 py-2 text-sm font-semibold rounded-xl border-2 transition-all
                                {{ $walkInTipe === 'servis' ? 'border-red-600 bg-red-50 text-red-600' : 'border-gray-200 text-gray-600 hover:border-gray-300' }}">
                            Servis Kendaraan
                        </button>
                    </div>
                </div>

                {{-- Customer --}}
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">
                        Pelanggan
                        @if($walkInTipe === 'servis') <span class="text-red-500 ml-1">* Wajib</span> @endif
                    </p>

                    @if($customerNama)
                        <div class="flex items-center gap-3 p-3 bg-red-50 rounded-xl">
                            <div class="w-8 h-8 rounded-full bg-red-600 text-white text-xs font-bold flex items-center justify-center flex-none">
                                {{ substr($customerNama, 0, 1) }}
                            </div>
                            <span class="text-sm font-semibold text-gray-900 flex-1">{{ $customerNama }}</span>
                            <button wire:click="clearCustomer" class="text-gray-400 hover:text-red-600 transition-colors">
                                <x-heroicon-o-x-mark class="w-4 h-4" />
                            </button>
                        </div>
                    @else
                        <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                            <div class="relative">
                                <x-heroicon-o-magnifying-glass class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" />
                                <input wire:model.live.debounce.200ms="customerSearch"
                                    x-on:focus="open = true"
                                    type="text"
                                    placeholder="Cari nama atau no. HP..."
                                    class="w-full pl-10 pr-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-red-500/20 focus:border-red-400 outline-none" />
                            </div>
                            @if(!empty($customerSuggestions))
                                <div class="absolute top-full mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-lg z-30 overflow-hidden">
                                    @foreach($customerSuggestions as $cs)
                                        <button wire:click="selectCustomer({{ $cs['id'] }}, {{ Js::from($cs['nama']) }})"
                                            class="w-full flex items-center gap-3 px-4 py-3 hover:bg-gray-50 text-left transition-colors">
                                            <div class="w-8 h-8 rounded-full bg-gray-200 text-gray-600 text-xs font-bold flex items-center justify-center flex-none">
                                                {{ substr($cs['nama'], 0, 1) }}
                                            </div>
                                            <div>
                                                <p class="text-sm font-medium text-gray-900">{{ $cs['nama'] }}</p>
                                                <p class="text-xs text-gray-400">{{ $cs['no_hp'] }}</p>
                                            </div>
                                        </button>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                        @error('customerId') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    @endif
                </div>

                {{-- Keluhan (only for servis) --}}
                @if($walkInTipe === 'servis')
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">
                            Keluhan Customer <span class="text-red-500 ml-1">* Wajib</span>
                        </p>
                        <textarea wire:model="keluhan" rows="3"
                            placeholder="Deskripsikan keluhan atau masalah kendaraan..."
                            class="w-full text-sm border border-gray-200 rounded-xl px-3 py-2.5 focus:ring-2 focus:ring-red-500/20 focus:border-red-400 outline-none resize-none
                                {{ !$keluhan ? 'border-amber-300 focus:border-amber-400' : '' }}"></textarea>
                        @error('keluhan') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                @endif

                {{-- Item Toggle (for servis) --}}
                @if($walkInTipe === 'servis')
                    <div class="flex gap-1 bg-gray-100 rounded-xl p-1 w-fit">
                        <button wire:click="$set('itemSubTipe', 'servis')"
                            class="px-3 py-1.5 text-xs font-semibold rounded-lg transition-colors
                                {{ $itemSubTipe === 'servis' ? 'bg-red-600 text-white shadow' : 'text-gray-600 hover:bg-gray-200' }}">
                            Servis
                        </button>
                        <button wire:click="$set('itemSubTipe', 'sparepart')"
                            class="px-3 py-1.5 text-xs font-semibold rounded-lg transition-colors
                                {{ $itemSubTipe === 'sparepart' ? 'bg-red-600 text-white shadow' : 'text-gray-600 hover:bg-gray-200' }}">
                            Sparepart
                        </button>
                    </div>
                @endif

            @endif

            {{-- MODE: WORK ORDER --}}
            @if($mode === 'work_order')
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Pilih Work Order</p>

                    @if($selectedWoLabel)
                        <div class="flex items-center gap-3 p-3 bg-red-50 rounded-xl mb-3">
                            <x-heroicon-o-clipboard-document-list class="w-5 h-5 text-red-600 flex-none" />
                            <span class="text-sm font-semibold text-gray-900 flex-1">{{ $selectedWoLabel }}</span>
                            <button wire:click="clearWo" class="text-gray-400 hover:text-red-600 transition-colors">
                                <x-heroicon-o-x-mark class="w-4 h-4" />
                            </button>
                        </div>
                    @else
                        <div class="relative" @click.outside="$wire.set('woSuggestions', [])">
                            <x-heroicon-o-magnifying-glass class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" />
                            <input wire:model.live.debounce.300ms="woSearch"
                                type="text"
                                placeholder="Cari no. WO, kendaraan, atau customer..."
                                class="w-full pl-10 pr-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-red-500/20 focus:border-red-400 outline-none" />
                        </div>
                        @if(!empty($woSuggestions))
                            <div class="mt-2 space-y-2">
                                @foreach($woSuggestions as $wo)
                                    <button wire:click="selectWo({{ $wo['id'] }}, {{ Js::from($wo['label']) }})"
                                        class="w-full text-left p-3 border border-gray-200 rounded-xl hover:border-red-300 hover:bg-red-50/50 transition-all">
                                        <div class="flex items-center justify-between mb-1">
                                            <span class="text-xs font-bold text-red-600">{{ $wo['wo_number'] }}</span>
                                        </div>
                                        <p class="text-sm font-medium text-gray-900">{{ $wo['kendaraan'] }}</p>
                                        <p class="text-xs text-gray-500">{{ $wo['customer'] }} — {{ $wo['keluhan'] }}</p>
                                    </button>
                                @endforeach
                            </div>
                        @endif
                    @endif
                </div>

                @if($selectedWoId)
                    {{-- Item Toggle for WO mode --}}
                    <div class="flex gap-1 bg-gray-100 rounded-xl p-1 w-fit">
                        <button wire:click="$set('itemSubTipe', 'servis')"
                            class="px-3 py-1.5 text-xs font-semibold rounded-lg transition-colors
                                {{ $itemSubTipe === 'servis' ? 'bg-red-600 text-white shadow' : 'text-gray-600 hover:bg-gray-200' }}">
                            Servis
                        </button>
                        <button wire:click="$set('itemSubTipe', 'sparepart')"
                            class="px-3 py-1.5 text-xs font-semibold rounded-lg transition-colors
                                {{ $itemSubTipe === 'sparepart' ? 'bg-red-600 text-white shadow' : 'text-gray-600 hover:bg-gray-200' }}">
                            Sparepart
                        </button>
                    </div>
                @endif
            @endif

            {{-- Tambah Item (always show for walk-in sparepart / servis with WO selected) --}}
            @if($mode === 'walk_in' || ($mode === 'work_order' && $selectedWoId))
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Tambah Item</p>
                    <div class="relative">
                        <x-heroicon-o-magnifying-glass class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" />
                        <input wire:model.live.debounce.200ms="itemSearch"
                            type="text"
                            placeholder="{{ ($walkInTipe === 'sparepart' && $mode === 'walk_in') ? 'Cari nama atau SKU sparepart...' : ($itemSubTipe === 'servis' ? 'Cari nama servis...' : 'Cari nama atau SKU sparepart...') }}"
                            class="w-full pl-10 pr-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-red-500/20 focus:border-red-400 outline-none" />
                    </div>

                    @if(!empty($itemSuggestions))
                        <div class="mt-2 divide-y divide-gray-50 border border-gray-100 rounded-xl overflow-hidden">
                            @foreach($itemSuggestions as $item)
                                @php
                                    $habis = isset($item['stok']) && $item['stok'] <= 0;
                                    $kritis = isset($item['stok']) && isset($item['min_stok']) && $item['stok'] > 0 && $item['stok'] <= $item['min_stok'];
                                @endphp
                                <div class="flex items-center gap-3 px-4 py-3 hover:bg-gray-50 transition-colors {{ $habis ? 'opacity-50' : '' }}">
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900">{{ $item['nama'] }}</p>
                                        @if(isset($item['stok']))
                                            @if($habis)
                                                <p class="text-xs text-red-600 font-medium">Stok habis</p>
                                            @elseif($kritis)
                                                <p class="text-xs text-orange-500">Stok: {{ $item['stok'] }} ⚠ hampir habis</p>
                                            @endif
                                        @endif
                                    </div>
                                    <span class="text-sm font-semibold text-gray-700">Rp {{ number_format($item['harga'], 0, ',', '.') }}</span>
                                    @if(!$habis)
                                        <button wire:click="addToCart({{ $item['id'] }}, {{ Js::from($item['tipe']) }})"
                                            class="w-8 h-8 rounded-xl bg-red-600 text-white flex items-center justify-center hover:bg-red-700 transition-colors flex-none">
                                            <x-heroicon-o-plus class="w-4 h-4" />
                                        </button>
                                    @else
                                        <div class="w-8 h-8 rounded-xl bg-gray-200 flex items-center justify-center flex-none">
                                            <x-heroicon-o-plus class="w-4 h-4 text-gray-400" />
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endif

            {{-- Catatan --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Catatan (Opsional)</p>
                <textarea wire:model="catatan" rows="2"
                    placeholder="Catatan tambahan untuk invoice..."
                    class="w-full text-sm border border-gray-200 rounded-xl px-3 py-2.5 focus:ring-2 focus:ring-red-500/20 focus:border-red-400 outline-none resize-none"></textarea>
            </div>
        </div>

        {{-- Panel Kanan ~40% --}}
        <div class="w-96 flex-none border-l border-gray-200 bg-white flex flex-col overflow-hidden">
            <div class="flex-1 overflow-y-auto p-5">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-4">Ringkasan Belanja</p>

                {{-- Cart Items --}}
                @if(empty($cart))
                    <div class="flex flex-col items-center justify-center py-12 text-center">
                        <x-heroicon-o-shopping-cart class="w-10 h-10 text-gray-300 mb-3" />
                        <p class="text-sm text-gray-400">Keranjang kosong</p>
                        <p class="text-xs text-gray-400 mt-1">Tambah item dari panel kiri</p>
                    </div>
                @else
                    <div class="space-y-2 mb-5">
                        @foreach($cart as $i => $item)
                            <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl">
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 truncate">{{ $item['nama'] }}</p>
                                    <p class="text-xs text-gray-500">Rp {{ number_format($item['harga'], 0, ',', '.') }}</p>
                                </div>
                                <div class="flex items-center gap-2">
                                    <button wire:click="decrementQty({{ $i }})"
                                        class="w-6 h-6 rounded-lg bg-white border border-gray-200 text-gray-600 text-xs flex items-center justify-center hover:border-red-300 transition-colors">
                                        <x-heroicon-o-minus class="w-3 h-3" />
                                    </button>
                                    <span class="text-sm font-bold text-gray-900 w-5 text-center">{{ $item['qty'] }}</span>
                                    <button wire:click="incrementQty({{ $i }})"
                                        class="w-6 h-6 rounded-lg bg-white border border-gray-200 text-gray-600 text-xs flex items-center justify-center hover:border-red-300 transition-colors">
                                        <x-heroicon-o-plus class="w-3 h-3" />
                                    </button>
                                </div>
                                <span class="text-sm font-semibold text-gray-900 w-20 text-right">
                                    Rp {{ number_format($item['harga'] * $item['qty'], 0, ',', '.') }}
                                </span>
                            </div>
                        @endforeach
                    </div>

                    {{-- Subtotal / Diskon / Grand Total --}}
                    <div class="border-t border-gray-100 pt-4 space-y-2">
                        <div class="flex justify-between text-sm text-gray-600">
                            <span>Subtotal</span>
                            <span class="font-medium">Rp {{ number_format($this->subtotal, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Diskon</span>
                            <div class="flex items-center gap-1">
                                <span class="text-sm text-gray-400">Rp</span>
                                <input wire:model.live="discount" type="number" min="0"
                                    class="w-28 text-sm text-right border border-gray-200 rounded-lg px-2 py-1 focus:ring-1 focus:ring-red-400 outline-none" />
                            </div>
                        </div>
                        <div class="flex justify-between text-base font-bold border-t border-gray-100 pt-2 mt-1">
                            <span class="text-gray-900">Grand Total</span>
                            <span class="text-red-600 text-lg">Rp {{ number_format($this->grandTotal, 0, ',', '.') }}</span>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Pembayaran & Tombol --}}
            <div class="border-t border-gray-100 p-5 space-y-4 flex-none">
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Metode Pembayaran</p>
                    <div class="grid grid-cols-2 gap-2">
                        @foreach(['tunai' => 'Tunai', 'transfer' => 'Transfer', 'qris' => 'QRIS', 'kredit' => 'Kredit'] as $key => $label)
                            <button wire:click="$set('metodePembayaran', '{{ $key }}')"
                                class="py-2 text-xs font-semibold rounded-xl border-2 transition-all
                                    {{ $metodePembayaran === $key
                                        ? 'bg-red-600 border-red-600 text-white'
                                        : 'bg-white border-gray-200 text-gray-700 hover:border-gray-300' }}">
                                {{ $label }}
                            </button>
                        @endforeach
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Jumlah Bayar</label>
                    <div class="flex items-center gap-2">
                        <span class="text-sm text-gray-400">Rp</span>
                        <input wire:model.live="jumlahBayar" type="number" min="0"
                            placeholder="0"
                            class="flex-1 text-sm border border-gray-200 rounded-xl px-3 py-2.5 focus:ring-2 focus:ring-red-500/20 focus:border-red-400 outline-none" />
                    </div>
                    @if($jumlahBayar)
                        <p class="text-sm mt-2 font-semibold
                            {{ $this->kembalian >= 0 ? 'text-green-600' : 'text-red-600' }}">
                            @if($this->kembalian > 0)
                                Kembalian: Rp {{ number_format($this->kembalian, 0, ',', '.') }}
                            @elseif($this->kembalian == 0)
                                Lunas
                            @else
                                Kurang: Rp {{ number_format(abs($this->kembalian), 0, ',', '.') }}
                            @endif
                        </p>
                    @endif
                </div>

                <button wire:click="buatInvoice"
                    @disabled(empty($cart))
                    class="w-full py-3.5 text-sm font-bold rounded-2xl transition-colors
                        {{ empty($cart)
                            ? 'bg-gray-300 text-gray-500 cursor-not-allowed'
                            : 'bg-red-600 text-white hover:bg-red-700 shadow-lg shadow-red-600/20' }}">
                    Buat Invoice
                </button>
                @if(empty($cart))
                    <p class="text-xs text-gray-400 text-center -mt-2">Keranjang masih kosong</p>
                @endif
            </div>
        </div>
    </div>
</div>
