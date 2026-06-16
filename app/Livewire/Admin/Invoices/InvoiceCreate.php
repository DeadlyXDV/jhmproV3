<?php

namespace App\Livewire\Admin\Invoices;

use App\Models\Booking;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Partner;
use App\Models\Service;
use App\Models\Sparepart;
use App\Models\StockMovement;
use App\Models\Vehicle;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Livewire\Component;

class InvoiceCreate extends Component
{
    public string $tipe = 'walk_in';

    // Customer
    public string $customerSearch = '';

    public ?int $customerId = null;

    public string $customerNama = '';

    /** @var array<int, array{id: int, nama: string, no_hp: string}> */
    public array $customerSuggestions = [];

    // Inline new customer
    public bool $showNewCustomer = false;

    public string $newCustomerNama = '';

    public string $newCustomerNoHp = '';

    public string $newCustomerEmail = '';

    // Vehicle
    public ?int $vehicleId = null;

    // Partner (tipe=partner)
    public string $partnerSearch = '';

    public ?int $partnerId = null;

    public string $partnerNama = '';

    /** @var array<int, array{id: int, nama: string}> */
    public array $partnerSuggestions = [];

    // Booking (tipe=booking)
    public string $bookingSearch = '';

    public ?int $bookingId = null;

    public string $bookingLabel = '';

    /** @var array<int, array{id: int, label: string}> */
    public array $bookingSuggestions = [];

    // Item search
    public string $itemSearch = '';

    public string $itemTipe = 'service';

    /** @var array<int, array{id: int, nama: string, harga: float, tipe: string}> */
    public array $itemSuggestions = [];

    // Items (repeater)
    // Each: ['id' => int, 'tipe' => string, 'nama' => string, 'harga' => string, 'qty' => string]
    /** @var array<int, array{id: int, tipe: string, nama: string, harga: string, qty: string}> */
    public array $items = [];

    public string $catatan = '';

    public string $discount = '0';

    public string $metodePembayaran = 'tunai';

    public string $jumlahBayar = '0';

    // ==================== TIPE ====================

    public function updatedTipe(): void
    {
        $this->partnerId = null;
        $this->partnerNama = '';
        $this->partnerSearch = '';
        $this->partnerSuggestions = [];
        $this->bookingId = null;
        $this->bookingLabel = '';
        $this->bookingSearch = '';
        $this->bookingSuggestions = [];
    }

    // ==================== CUSTOMER ====================

    public function updatedCustomerSearch(): void
    {
        if (strlen($this->customerSearch) < 2) {
            $this->customerSuggestions = [];

            return;
        }

        $this->customerSuggestions = Customer::query()
            ->where(function ($q) {
                $q->where('nama', 'like', "%{$this->customerSearch}%")
                    ->orWhere('no_hp', 'like', "%{$this->customerSearch}%");
            })
            ->limit(6)
            ->get()
            ->map(fn ($c) => ['id' => $c->id, 'nama' => $c->nama, 'no_hp' => $c->no_hp])
            ->toArray();
    }

    public function selectCustomer(int $id): void
    {
        $customer = Customer::findOrFail($id);
        $this->customerId = $id;
        $this->customerNama = $customer->nama;
        $this->customerSearch = '';
        $this->customerSuggestions = [];
        $this->vehicleId = null;
        $this->showNewCustomer = false;
    }

    public function clearCustomer(): void
    {
        $this->customerId = null;
        $this->customerNama = '';
        $this->vehicleId = null;
        $this->customerSearch = '';
        $this->customerSuggestions = [];
    }

    public function openNewCustomer(): void
    {
        $this->showNewCustomer = true;
        $this->customerId = null;
        $this->customerNama = '';
        $this->customerSearch = '';
        $this->customerSuggestions = [];
    }

    public function cancelNewCustomer(): void
    {
        $this->showNewCustomer = false;
        $this->newCustomerNama = '';
        $this->newCustomerNoHp = '';
        $this->newCustomerEmail = '';
    }

    // ==================== PARTNER ====================

    public function updatedPartnerSearch(): void
    {
        if (strlen($this->partnerSearch) < 2) {
            $this->partnerSuggestions = [];

            return;
        }

        $this->partnerSuggestions = Partner::query()
            ->where('nama_bengkel', 'like', "%{$this->partnerSearch}%")
            ->limit(6)
            ->get()
            ->map(fn ($p) => ['id' => $p->id, 'nama' => $p->nama_bengkel])
            ->toArray();
    }

    public function selectPartner(int $id): void
    {
        $partner = Partner::findOrFail($id);
        $this->partnerId = $id;
        $this->partnerNama = $partner->nama_bengkel;
        $this->partnerSearch = '';
        $this->partnerSuggestions = [];
    }

    public function clearPartner(): void
    {
        $this->partnerId = null;
        $this->partnerNama = '';
        $this->partnerSearch = '';
    }

    // ==================== BOOKING ====================

    public function updatedBookingSearch(): void
    {
        if (strlen($this->bookingSearch) < 1) {
            $this->bookingSuggestions = [];

            return;
        }

        $this->bookingSuggestions = Booking::query()
            ->with(['customer'])
            ->whereIn('status', ['confirmed', 'in_progress'])
            ->whereNull('invoice_id')
            ->where(function ($q) {
                $q->where('booking_number', 'like', "%{$this->bookingSearch}%")
                    ->orWhere('nama_pemesan', 'like', "%{$this->bookingSearch}%")
                    ->orWhereHas('customer', fn ($c) => $c->where('nama', 'like', "%{$this->bookingSearch}%"));
            })
            ->limit(6)
            ->get()
            ->map(fn ($b) => [
                'id' => $b->id,
                'label' => $b->booking_number.' — '.$b->nama_pemesan.' ('.$b->tanggal_booking?->format('d/m/Y').')',
            ])
            ->toArray();
    }

    public function selectBooking(int $id): void
    {
        $booking = Booking::with(['customer'])->findOrFail($id);
        $this->bookingId = $id;
        $this->bookingLabel = $booking->booking_number.' — '.$booking->nama_pemesan;
        $this->bookingSearch = '';
        $this->bookingSuggestions = [];

        if ($booking->customer_id) {
            $this->customerId = $booking->customer_id;
            $this->customerNama = $booking->customer?->nama ?? $booking->nama_pemesan;
        }

        if ($booking->vehicle_id) {
            $this->vehicleId = $booking->vehicle_id;
        }
    }

    public function clearBooking(): void
    {
        $this->bookingId = null;
        $this->bookingLabel = '';
        $this->bookingSearch = '';
        $this->customerId = null;
        $this->customerNama = '';
        $this->vehicleId = null;
    }

    // ==================== ITEMS ====================

    public function updatedItemTipe(): void
    {
        $this->itemSearch = '';
        $this->itemSuggestions = [];
    }

    public function updatedItemSearch(): void
    {
        if (strlen($this->itemSearch) < 1) {
            $this->itemSuggestions = [];

            return;
        }

        if ($this->itemTipe === 'sparepart') {
            $this->itemSuggestions = Sparepart::query()
                ->where('is_active', true)
                ->where(function ($q) {
                    $q->where('item_name', 'like', "%{$this->itemSearch}%")
                        ->orWhere('sku', 'like', "%{$this->itemSearch}%");
                })
                ->limit(8)
                ->get()
                ->map(fn ($s) => [
                    'id' => $s->id,
                    'nama' => $s->item_name,
                    'harga' => (float) $s->harga_jual,
                    'tipe' => 'sparepart',
                ])
                ->toArray();
        } else {
            $this->itemSuggestions = Service::query()
                ->where('is_active', true)
                ->where('nama_service', 'like', "%{$this->itemSearch}%")
                ->limit(8)
                ->get()
                ->map(fn ($s) => [
                    'id' => $s->id,
                    'nama' => $s->nama_service,
                    'harga' => (float) $s->harga_default,
                    'tipe' => 'service',
                ])
                ->toArray();
        }
    }

    public function addItem(int $id, string $tipe): void
    {
        abort_unless(in_array($tipe, ['service', 'sparepart']), 422);

        if ($tipe === 'sparepart') {
            $model = Sparepart::where('is_active', true)->findOrFail($id);
            $nama = $model->item_name;
            $harga = (float) $model->harga_jual;
        } else {
            $model = Service::where('is_active', true)->findOrFail($id);
            $nama = $model->nama_service;
            $harga = (float) $model->harga_default;
        }

        foreach ($this->items as $i => $item) {
            if ($item['id'] === $id && $item['tipe'] === $tipe) {
                $this->items[$i]['qty'] = (string) ((int) $this->items[$i]['qty'] + 1);
                $this->itemSearch = '';
                $this->itemSuggestions = [];

                return;
            }
        }

        $this->items[] = [
            'id' => $id,
            'tipe' => $tipe,
            'nama' => $nama,
            'harga' => (string) $harga,
            'qty' => '1',
        ];

        $this->itemSearch = '';
        $this->itemSuggestions = [];
    }

    public function removeItem(int $index): void
    {
        array_splice($this->items, $index, 1);
    }

    // ==================== COMPUTED ====================

    public function getSubtotalProperty(): float
    {
        return array_sum(array_map(
            fn ($item) => (float) $item['harga'] * max(1, (int) $item['qty']),
            $this->items
        ));
    }

    public function getGrandTotalProperty(): float
    {
        return max(0, $this->subtotal - (float) $this->discount);
    }

    // ==================== SAVE ====================

    public function save(): void
    {
        $this->validate([
            'tipe' => 'required|in:walk_in,booking,partner',
            'items' => 'required|array|min:1',
            'items.*.qty' => 'required|integer|min:1',
            'items.*.harga' => 'required|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'jumlahBayar' => 'nullable|numeric|min:0',
        ], [
            'items.required' => 'Minimal 1 item harus ditambahkan.',
            'items.min' => 'Minimal 1 item harus ditambahkan.',
        ]);

        if ($this->tipe === 'partner' && ! $this->partnerId) {
            $this->addError('partnerId', 'Partner wajib dipilih untuk tipe partner.');

            return;
        }

        if ($this->tipe === 'booking' && ! $this->bookingId) {
            $this->addError('bookingId', 'Booking wajib dipilih untuk tipe booking.');

            return;
        }

        if ($this->showNewCustomer && ! $this->customerId) {
            $this->validate([
                'newCustomerNama' => 'required|string|max:255',
                'newCustomerNoHp' => 'required|string|max:20',
            ]);
        }

        DB::transaction(function () {
            $customerId = $this->customerId;

            if ($this->showNewCustomer && ! $customerId) {
                $customer = Customer::create([
                    'nama' => $this->newCustomerNama,
                    'no_hp' => $this->newCustomerNoHp,
                    'email' => $this->newCustomerEmail ?: null,
                ]);
                $customerId = $customer->id;
            }

            $subtotal = array_sum(array_map(
                fn ($item) => (float) $item['harga'] * max(1, (int) $item['qty']),
                $this->items
            ));
            $discount = max(0, (float) $this->discount);
            $grandTotal = max(0, $subtotal - $discount);
            $jumlahBayar = max(0, (float) $this->jumlahBayar);
            $amountPaid = min($jumlahBayar, $grandTotal);

            $paymentStatus = 'unpaid';
            if ($grandTotal > 0 && $jumlahBayar >= $grandTotal) {
                $paymentStatus = 'paid';
            } elseif ($jumlahBayar > 0) {
                $paymentStatus = 'partial';
            }

            $invoice = Invoice::create([
                'customer_id' => $customerId,
                'vehicle_id' => $this->vehicleId,
                'partner_id' => $this->partnerId,
                'booking_id' => $this->bookingId,
                'user_id' => auth('admin')->id(),
                'invoice_number' => 'INV-'.strtoupper(uniqid()),
                'tanggal' => now(),
                'tipe' => $this->tipe,
                'catatan' => $this->catatan ?: null,
                'subtotal' => $subtotal,
                'discount' => $discount,
                'grand_total' => $grandTotal,
                'payment_status' => $paymentStatus,
                'amount_paid' => $amountPaid,
            ]);

            foreach ($this->items as $item) {
                $itemType = $item['tipe'];
                $harga = (float) $item['harga'];
                $qty = max(1, (int) $item['qty']);
                $hargaBeli = 0;

                if ($itemType === 'sparepart') {
                    $sparepart = Sparepart::findOrFail($item['id']);
                    $hargaBeli = (float) $sparepart->harga_beli;
                    $stockBefore = $sparepart->stock;
                    $sparepart->decrement('stock', $qty);

                    StockMovement::create([
                        'sparepart_id' => $sparepart->id,
                        'user_id' => auth('admin')->id(),
                        'type' => 'out',
                        'qty' => $qty,
                        'stock_before' => $stockBefore,
                        'stock_after' => $stockBefore - $qty,
                        'reference_type' => Invoice::class,
                        'reference_id' => $invoice->id,
                    ]);
                }

                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'service_id' => $itemType === 'service' ? $item['id'] : null,
                    'sparepart_id' => $itemType === 'sparepart' ? $item['id'] : null,
                    'type' => $itemType,
                    'nama_snapshot' => $item['nama'],
                    'qty' => $qty,
                    'harga_jual' => $harga,
                    'harga_beli_snapshot' => $hargaBeli,
                    'subtotal' => $harga * $qty,
                ]);
            }

            if ($amountPaid > 0) {
                $invoice->payments()->create([
                    'payment_method' => $this->metodePembayaran,
                    'amount' => $amountPaid,
                    'paid_at' => now(),
                ]);
            }

            if ($this->bookingId) {
                Booking::where('id', $this->bookingId)->update(['invoice_id' => $invoice->id]);
            }

            $this->redirect(route('admin.invoices.show', $invoice), navigate: true);
        });
    }

    public function render(): View
    {
        $vehicles = $this->customerId
            ? Vehicle::where('customer_id', $this->customerId)->get()
            : collect();

        return view('livewire.admin.invoices.create', [
            'vehicles' => $vehicles,
            'subtotal' => $this->subtotal,
            'grandTotal' => $this->grandTotal,
        ])->layout('layouts.admin', ['title' => 'Buat Invoice']);
    }
}
