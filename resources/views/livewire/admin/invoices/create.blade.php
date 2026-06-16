<div class="max-w-6xl mx-auto pb-12">

    {{-- Page Header --}}
    <div class="mb-6 flex items-center gap-3">
        <a wire:navigate href="{{ route('admin.invoices.index') }}" class="text-gray-400 hover:text-gray-600 transition">
            <x-heroicon-o-arrow-left class="w-5 h-5" />
        </a>
        <div>
            <h1 class="text-xl font-bold text-gray-900">Buat Invoice</h1>
            <p class="text-sm text-gray-500 mt-0.5">Invoice baru untuk bengkel</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- ===== LEFT COLUMN (2/3) ===== --}}
        <div class="lg:col-span-2 space-y-5">

            {{-- CARD 1: Tipe Invoice --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wide">Tipe Invoice</h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-3 gap-3">
                        @foreach(['walk_in' => ['label' => 'Walk-In', 'desc' => 'Pelanggan datang langsung', 'icon' => 'heroicon-o-user'], 'booking' => ['label' => 'Dari Booking', 'desc' => 'Invoice dari booking online', 'icon' => 'heroicon-o-calendar'], 'partner' => ['label' => 'Mitra Bengkel', 'desc' => 'Invoice untuk mitra', 'icon' => 'heroicon-o-building-office']] as $value => $info)
                        <button type="button"
                                wire:click="$set('tipe', '{{ $value }}')"
                                class="relative p-4 rounded-lg border-2 text-left transition {{ $tipe === $value ? 'border-red-500 bg-red-50' : 'border-gray-200 hover:border-gray-300' }}">
                            @if($tipe === $value)
                            <span class="absolute top-2 right-2 w-4 h-4 bg-red-500 rounded-full flex items-center justify-center">
                                <x-heroicon-o-check class="w-2.5 h-2.5 text-white" />
                            </span>
                            @endif
                            <p class="font-semibold text-sm {{ $tipe === $value ? 'text-red-700' : 'text-gray-700' }}">{{ $info['label'] }}</p>
                            <p class="text-xs mt-0.5 {{ $tipe === $value ? 'text-red-500' : 'text-gray-400' }}">{{ $info['desc'] }}</p>
                        </button>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- CARD 2: Informasi Pihak --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wide">
                        @if($tipe === 'partner') Mitra Bengkel
                        @elseif($tipe === 'booking') Booking
                        @else Customer
                        @endif
                    </h2>
                </div>
                <div class="p-6 space-y-4">

                    {{-- BOOKING tipe --}}
                    @if($tipe === 'booking')
                    @if($bookingId)
                    <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg border border-blue-100">
                        <div>
                            <p class="text-sm font-medium text-blue-900">{{ $bookingLabel }}</p>
                            @if($customerNama)
                            <p class="text-xs text-blue-600 mt-0.5">Customer: {{ $customerNama }}</p>
                            @endif
                        </div>
                        <button type="button" wire:click="clearBooking" class="text-blue-400 hover:text-blue-600">
                            <x-heroicon-o-x-mark class="w-4 h-4" />
                        </button>
                    </div>
                    @else
                    <div x-data="{ open: false }" x-on:click.outside="open = false" class="relative">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Cari Booking <span class="text-red-500">*</span></label>
                        <input wire:model.live="bookingSearch"
                               type="text"
                               placeholder="Nomor booking, nama pemesan..."
                               x-on:input="open = true"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent" />
                        @if(count($bookingSuggestions))
                        <div x-show="open" class="absolute z-50 w-full bg-white border border-gray-200 rounded-lg shadow-lg mt-1 overflow-hidden">
                            @foreach($bookingSuggestions as $s)
                            <button type="button"
                                    wire:click="selectBooking({{ $s['id'] }})"
                                    x-on:click="open = false"
                                    class="w-full text-left px-4 py-2.5 hover:bg-gray-50 border-b border-gray-50 last:border-0">
                                <p class="text-sm font-medium text-gray-900">{{ $s['label'] }}</p>
                            </button>
                            @endforeach
                        </div>
                        @endif
                    </div>
                    @if($errors->has('bookingId'))
                    <p class="text-red-600 text-xs">{{ $errors->first('bookingId') }}</p>
                    @endif
                    @endif
                    @endif

                    {{-- PARTNER tipe --}}
                    @if($tipe === 'partner')
                    @if($partnerId)
                    <div class="flex items-center justify-between p-3 bg-amber-50 rounded-lg border border-amber-100">
                        <div>
                            <p class="text-sm font-medium text-amber-900">{{ $partnerNama }}</p>
                            <p class="text-xs text-amber-600 mt-0.5">Mitra Bengkel</p>
                        </div>
                        <button type="button" wire:click="clearPartner" class="text-amber-400 hover:text-amber-600">
                            <x-heroicon-o-x-mark class="w-4 h-4" />
                        </button>
                    </div>
                    @else
                    <div x-data="{ open: false }" x-on:click.outside="open = false" class="relative">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Cari Mitra <span class="text-red-500">*</span></label>
                        <input wire:model.live="partnerSearch"
                               type="text"
                               placeholder="Nama bengkel mitra..."
                               x-on:input="open = true"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent" />
                        @if(count($partnerSuggestions))
                        <div x-show="open" class="absolute z-50 w-full bg-white border border-gray-200 rounded-lg shadow-lg mt-1 overflow-hidden">
                            @foreach($partnerSuggestions as $s)
                            <button type="button"
                                    wire:click="selectPartner({{ $s['id'] }})"
                                    x-on:click="open = false"
                                    class="w-full text-left px-4 py-2.5 hover:bg-gray-50 border-b border-gray-50 last:border-0">
                                <p class="text-sm font-medium text-gray-900">{{ $s['nama'] }}</p>
                            </button>
                            @endforeach
                        </div>
                        @endif
                    </div>
                    @if($errors->has('partnerId'))
                    <p class="text-red-600 text-xs">{{ $errors->first('partnerId') }}</p>
                    @endif
                    @endif
                    @endif

                    {{-- CUSTOMER (walk_in atau opsional untuk partner/booking) --}}
                    @if($tipe === 'walk_in' || $tipe === 'partner')
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label class="block text-sm font-medium text-gray-700">
                                Customer
                                @if($tipe === 'walk_in') <span class="text-gray-400">(opsional)</span> @endif
                            </label>
                            @if(!$customerId && !$showNewCustomer)
                            <button type="button" wire:click="openNewCustomer"
                                    class="text-xs text-red-600 hover:text-red-700 font-medium flex items-center gap-1">
                                <x-heroicon-o-plus class="w-3 h-3" /> Tambah Baru
                            </button>
                            @endif
                        </div>

                        @if($customerId)
                        <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg border border-green-100">
                            <div>
                                <p class="text-sm font-medium text-green-900">{{ $customerNama }}</p>
                                <p class="text-xs text-green-600 mt-0.5">Customer terpilih</p>
                            </div>
                            <button type="button" wire:click="clearCustomer" class="text-green-400 hover:text-green-600">
                                <x-heroicon-o-x-mark class="w-4 h-4" />
                            </button>
                        </div>
                        @elseif($showNewCustomer)
                        <div class="border border-blue-200 rounded-lg p-4 bg-blue-50 space-y-3">
                            <p class="text-xs font-semibold text-blue-700 uppercase tracking-wide">Customer Baru</p>
                            <div class="grid grid-cols-2 gap-3">
                                <div class="col-span-2 md:col-span-1">
                                    <label class="block text-xs font-medium text-gray-600 mb-1">Nama <span class="text-red-500">*</span></label>
                                    <input wire:model="newCustomerNama" type="text" placeholder="Nama lengkap"
                                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500" />
                                    @error('newCustomerNama') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>
                                <div class="col-span-2 md:col-span-1">
                                    <label class="block text-xs font-medium text-gray-600 mb-1">No. HP <span class="text-red-500">*</span></label>
                                    <input wire:model="newCustomerNoHp" type="text" placeholder="08xx..."
                                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500" />
                                    @error('newCustomerNoHp') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>
                                <div class="col-span-2">
                                    <label class="block text-xs font-medium text-gray-600 mb-1">Email <span class="text-gray-400">(opsional)</span></label>
                                    <input wire:model="newCustomerEmail" type="email" placeholder="email@..."
                                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500" />
                                </div>
                            </div>
                            <button type="button" wire:click="cancelNewCustomer" class="text-xs text-gray-500 hover:text-gray-700">
                                Batalkan & cari customer existing
                            </button>
                        </div>
                        @else
                        <div x-data="{ open: false }" x-on:click.outside="open = false" class="relative">
                            <input wire:model.live="customerSearch"
                                   type="text"
                                   placeholder="Cari nama atau no. HP..."
                                   x-on:input="open = true"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent" />
                            @if(count($customerSuggestions))
                            <div x-show="open" class="absolute z-50 w-full bg-white border border-gray-200 rounded-lg shadow-lg mt-1 overflow-hidden">
                                @foreach($customerSuggestions as $s)
                                <button type="button"
                                        wire:click="selectCustomer({{ $s['id'] }})"
                                        x-on:click="open = false"
                                        class="w-full text-left px-4 py-2.5 hover:bg-gray-50 border-b border-gray-50 last:border-0">
                                    <p class="text-sm font-medium text-gray-900">{{ $s['nama'] }}</p>
                                    <p class="text-xs text-gray-500">{{ $s['no_hp'] }}</p>
                                </button>
                                @endforeach
                            </div>
                            @endif
                        </div>
                        @endif
                    </div>
                    @endif

                    {{-- Kendaraan (jika ada customer terpilih) --}}
                    @if($customerId && $vehicles->isNotEmpty())
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kendaraan <span class="text-gray-400">(opsional)</span></label>
                        <select wire:model="vehicleId"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                            <option value="">— Pilih kendaraan —</option>
                            @foreach($vehicles as $v)
                            <option value="{{ $v->id }}">{{ $v->merk }} {{ $v->model }} - {{ $v->no_polisi }}</option>
                            @endforeach
                        </select>
                    </div>
                    @endif

                </div>
            </div>

            {{-- CARD 3: Tambah Item --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wide">Tambah Item</h2>
                </div>
                <div class="p-6">
                    {{-- Type toggle --}}
                    <div class="flex gap-2 mb-4">
                        <button type="button"
                                wire:click="$set('itemTipe', 'service')"
                                class="px-4 py-1.5 rounded-full text-sm font-medium transition {{ $itemTipe === 'service' ? 'bg-red-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                            Jasa/Servis
                        </button>
                        <button type="button"
                                wire:click="$set('itemTipe', 'sparepart')"
                                class="px-4 py-1.5 rounded-full text-sm font-medium transition {{ $itemTipe === 'sparepart' ? 'bg-red-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                            Sparepart
                        </button>
                    </div>

                    {{-- Search --}}
                    <div x-data="{ open: false }" x-on:click.outside="open = false" class="relative">
                        <div class="relative">
                            <x-heroicon-o-magnifying-glass class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
                            <input wire:model.live="itemSearch"
                                   type="text"
                                   placeholder="{{ $itemTipe === 'sparepart' ? 'Cari nama atau SKU sparepart...' : 'Cari nama layanan...' }}"
                                   x-on:input="open = true"
                                   class="w-full border border-gray-300 rounded-lg pl-9 pr-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent" />
                        </div>
                        @if(count($itemSuggestions))
                        <div x-show="open" class="absolute z-50 w-full bg-white border border-gray-200 rounded-lg shadow-lg mt-1 overflow-hidden">
                            @foreach($itemSuggestions as $s)
                            <button type="button"
                                    wire:click="addItem({{ $s['id'] }}, '{{ $s['tipe'] }}')"
                                    x-on:click="open = false"
                                    class="w-full text-left px-4 py-2.5 hover:bg-gray-50 border-b border-gray-50 last:border-0 flex items-center justify-between">
                                <span class="text-sm font-medium text-gray-900">{{ $s['nama'] }}</span>
                                <span class="text-sm text-gray-500">Rp {{ number_format($s['harga'], 0, ',', '.') }}</span>
                            </button>
                            @endforeach
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- CARD 4: Daftar Item --}}
            @if(count($items))
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wide">Daftar Item</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="text-xs font-semibold uppercase tracking-wider text-gray-500 border-b border-gray-200 bg-gray-50">
                                <th class="text-left px-6 py-3">Item</th>
                                <th class="text-right px-4 py-3 w-24">Qty</th>
                                <th class="text-right px-4 py-3 w-36">Harga (Rp)</th>
                                <th class="text-right px-4 py-3 w-32">Subtotal</th>
                                <th class="px-4 py-3 w-10"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($items as $i => $item)
                            <tr class="border-b border-gray-100">
                                <td class="px-6 py-3">
                                    <p class="font-medium text-gray-900">{{ $item['nama'] }}</p>
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium {{ $item['tipe'] === 'service' ? 'bg-blue-50 text-blue-600' : 'bg-orange-50 text-orange-600' }}">
                                        {{ $item['tipe'] === 'service' ? 'Jasa' : 'Sparepart' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <input wire:model.lazy="items.{{ $i }}.qty"
                                           type="number" min="1"
                                           class="w-full border border-gray-200 rounded px-2 py-1 text-right text-sm focus:outline-none focus:ring-1 focus:ring-red-500" />
                                </td>
                                <td class="px-4 py-3">
                                    <input wire:model.lazy="items.{{ $i }}.harga"
                                           type="number" min="0" step="500"
                                           class="w-full border border-gray-200 rounded px-2 py-1 text-right text-sm focus:outline-none focus:ring-1 focus:ring-red-500" />
                                </td>
                                <td class="px-4 py-3 text-right font-medium text-gray-900 whitespace-nowrap">
                                    Rp {{ number_format((float)$item['harga'] * max(1,(int)$item['qty']), 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-3">
                                    <button type="button" wire:click="removeItem({{ $i }})"
                                            class="text-gray-300 hover:text-red-500 transition">
                                        <x-heroicon-o-trash class="w-4 h-4" />
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @else
            @error('items')
            <div class="bg-red-50 border border-red-200 rounded-lg px-4 py-3">
                <p class="text-sm text-red-600">{{ $message }}</p>
            </div>
            @enderror
            @endif

        </div>

        {{-- ===== RIGHT COLUMN (1/3) ===== --}}
        <div class="space-y-5">

            {{-- Ringkasan Total --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wide">Ringkasan</h2>
                </div>
                <div class="p-6 space-y-4">
                    {{-- Subtotal --}}
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-500">Subtotal</span>
                        <span class="font-medium text-gray-900">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                    </div>

                    {{-- Diskon --}}
                    <div>
                        <label class="block text-sm text-gray-500 mb-1">Diskon (Rp)</label>
                        <input wire:model.lazy="discount"
                               type="number" min="0" step="1000"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500 text-right" />
                        @error('discount') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="border-t border-gray-100 pt-4">
                        <div class="flex items-center justify-between">
                            <span class="font-semibold text-gray-700">Grand Total</span>
                            <span class="text-xl font-bold text-gray-900">Rp {{ number_format($grandTotal, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Pembayaran --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wide">Pembayaran</h2>
                </div>
                <div class="p-6 space-y-4">
                    {{-- Metode --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Metode Pembayaran</label>
                        <select wire:model="metodePembayaran"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                            <option value="tunai">Tunai</option>
                            <option value="transfer">Transfer Bank</option>
                            <option value="qris">QRIS</option>
                            <option value="kartu_debit">Kartu Debit</option>
                        </select>
                    </div>

                    {{-- Jumlah Bayar --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah Dibayar (Rp)</label>
                        <input wire:model.lazy="jumlahBayar"
                               type="number" min="0" step="1000"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500 text-right" />
                        @error('jumlahBayar') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Status indicator --}}
                    @php
                        $bayar = (float) $jumlahBayar;
                        if ($grandTotal > 0 && $bayar >= $grandTotal) {
                            $statusColor = 'bg-green-50 border-green-200 text-green-700';
                            $statusLabel = 'Lunas';
                        } elseif ($bayar > 0) {
                            $statusColor = 'bg-amber-50 border-amber-200 text-amber-700';
                            $statusLabel = 'Bayar Sebagian (sisa Rp ' . number_format(max(0, $grandTotal - $bayar), 0, ',', '.') . ')';
                        } else {
                            $statusColor = 'bg-red-50 border-red-200 text-red-700';
                            $statusLabel = 'Belum Dibayar';
                        }
                    @endphp
                    <div class="px-3 py-2 rounded-lg border text-xs font-medium {{ $statusColor }}">
                        {{ $statusLabel }}
                    </div>

                    {{-- Kembalian --}}
                    @if($bayar > $grandTotal && $grandTotal > 0)
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-500">Kembalian</span>
                        <span class="font-semibold text-green-600">Rp {{ number_format($bayar - $grandTotal, 0, ',', '.') }}</span>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Catatan --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wide">Catatan</h2>
                </div>
                <div class="p-6">
                    <textarea wire:model="catatan"
                              rows="3"
                              placeholder="Catatan tambahan (opsional)..."
                              class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500 resize-none"></textarea>
                </div>
            </div>

            {{-- Submit --}}
            <button type="button"
                    wire:click="save"
                    wire:loading.attr="disabled"
                    class="w-full bg-red-600 hover:bg-red-700 disabled:opacity-60 text-white font-semibold py-3 px-6 rounded-xl transition flex items-center justify-center gap-2">
                <span wire:loading.remove wire:target="save">
                    <x-heroicon-o-document-plus class="w-5 h-5 inline mr-1" />
                    Simpan Invoice
                </span>
                <span wire:loading wire:target="save" class="flex items-center gap-2">
                    <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                    Menyimpan...
                </span>
            </button>

        </div>
    </div>
</div>
