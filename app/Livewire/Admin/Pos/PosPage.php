<?php

namespace App\Livewire\Admin\Pos;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Service;
use App\Models\Sparepart;
use App\Models\StockMovement;
use App\Models\WorkOrder;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Livewire\Component;

class PosPage extends Component
{
    // Mode: 'walk_in' | 'work_order'
    public string $mode = 'walk_in';

    // Walk-in sub-type: 'sparepart' | 'servis'
    public string $walkInTipe = 'sparepart';

    // Item search sub-type when tipe=servis: 'servis' | 'sparepart'
    public string $itemSubTipe = 'servis';

    // Customer search
    public string $customerSearch = '';

    public ?int $customerId = null;

    public string $customerNama = '';

    /** @var array<int, array{id: int, nama: string, no_hp: string}> */
    public array $customerSuggestions = [];

    // Keluhan (only for servis)
    public string $keluhan = '';

    // Item search
    public string $itemSearch = '';

    /** @var array<int, array{id: int, nama: string, harga: float, stok?: int, min_stok?: int, tipe: string}> */
    public array $itemSuggestions = [];

    // Cart
    /** @var array<int, array{id: int, nama: string, harga: float, qty: int, tipe: string}> */
    public array $cart = [];

    // Work Order selection
    public string $woSearch = '';

    public ?int $selectedWoId = null;

    public string $selectedWoLabel = '';

    /** @var array<int, array{id: int, label: string, wo_number: string, customer: string, kendaraan: string, keluhan: string}> */
    public array $woSuggestions = [];

    // Catatan
    public string $catatan = '';

    // Pembayaran
    public string $metodePembayaran = 'tunai';

    public string $jumlahBayar = '';

    public string $discount = '0';

    public ?string $invoiceSuccessId = null;

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

    public function selectCustomer(int $id, string $nama): void
    {
        $this->customerId = $id;
        $this->customerNama = $nama;
        $this->customerSearch = '';
        $this->customerSuggestions = [];
    }

    public function clearCustomer(): void
    {
        $this->customerId = null;
        $this->customerNama = '';
        $this->customerSearch = '';
    }

    public function updatedItemSearch(): void
    {
        if (strlen($this->itemSearch) < 1) {
            $this->itemSuggestions = [];

            return;
        }

        if ($this->walkInTipe === 'sparepart' || $this->itemSubTipe === 'sparepart') {
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
                    'stok' => $s->stock,
                    'min_stok' => $s->minimum_stock,
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

    public function addToCart(int $id, string $tipe): void
    {
        abort_unless(in_array($tipe, ['sparepart', 'service']), 422);

        if ($tipe === 'sparepart') {
            $model = Sparepart::where('is_active', true)->findOrFail($id);
            $nama = $model->item_name;
            $harga = (float) $model->harga_jual;
        } else {
            $model = Service::where('is_active', true)->findOrFail($id);
            $nama = $model->nama_service;
            $harga = (float) $model->harga_default;
        }

        foreach ($this->cart as $i => $item) {
            if ($item['id'] === $id && $item['tipe'] === $tipe) {
                $this->cart[$i]['qty']++;
                $this->itemSearch = '';
                $this->itemSuggestions = [];

                return;
            }
        }

        $this->cart[] = ['id' => $id, 'nama' => $nama, 'harga' => $harga, 'qty' => 1, 'tipe' => $tipe];
        $this->itemSearch = '';
        $this->itemSuggestions = [];
    }

    public function incrementQty(int $index): void
    {
        if (isset($this->cart[$index])) {
            $this->cart[$index]['qty']++;
        }
    }

    public function decrementQty(int $index): void
    {
        if (isset($this->cart[$index])) {
            if ($this->cart[$index]['qty'] <= 1) {
                $this->removeFromCart($index);
            } else {
                $this->cart[$index]['qty']--;
            }
        }
    }

    public function removeFromCart(int $index): void
    {
        array_splice($this->cart, $index, 1);
    }

    public function updatedWoSearch(): void
    {
        if (strlen($this->woSearch) < 1) {
            $this->woSuggestions = [];

            return;
        }

        $this->woSuggestions = WorkOrder::query()
            ->with(['vehicle.customer'])
            ->where('status', 'selesai')
            ->whereNull('invoice_id')
            ->where(function ($q) {
                $q->where('wo_number', 'like', "%{$this->woSearch}%")
                    ->orWhereHas('vehicle', fn ($v) => $v->where('no_polisi', 'like', "%{$this->woSearch}%"))
                    ->orWhereHas('vehicle.customer', fn ($c) => $c->where('nama', 'like', "%{$this->woSearch}%"));
            })
            ->limit(5)
            ->get()
            ->map(fn ($wo) => [
                'id' => $wo->id,
                'label' => $wo->wo_number.' — '.($wo->vehicle?->customer?->nama ?? 'Unknown'),
                'wo_number' => $wo->wo_number,
                'customer' => $wo->vehicle?->customer?->nama ?? '—',
                'kendaraan' => ($wo->vehicle?->merk ?? '').' '.($wo->vehicle?->model ?? '').' - '.($wo->vehicle?->no_polisi ?? ''),
                'keluhan' => $wo->keluhan_customer ?? '',
            ])
            ->toArray();
    }

    public function selectWo(int $id, string $label): void
    {
        $this->selectedWoId = $id;
        $this->selectedWoLabel = $label;
        $this->woSearch = '';
        $this->woSuggestions = [];
        $this->cart = [];
    }

    public function clearWo(): void
    {
        $this->selectedWoId = null;
        $this->selectedWoLabel = '';
        $this->cart = [];
    }

    public function getSubtotalProperty(): float
    {
        return array_sum(array_map(fn ($item) => $item['harga'] * $item['qty'], $this->cart));
    }

    public function getGrandTotalProperty(): float
    {
        return max(0, $this->subtotal - (float) $this->discount);
    }

    public function getKembalianProperty(): float
    {
        return (float) $this->jumlahBayar - $this->grandTotal;
    }

    public function buatInvoice(): void
    {
        if (empty($this->cart)) {
            return;
        }

        if ($this->walkInTipe === 'servis' && ! $this->customerId && $this->mode === 'walk_in') {
            $this->addError('customerId', 'Pelanggan wajib diisi untuk servis kendaraan.');

            return;
        }

        if ($this->walkInTipe === 'servis' && ! $this->keluhan && $this->mode === 'walk_in') {
            $this->addError('keluhan', 'Keluhan customer wajib diisi.');

            return;
        }

        DB::transaction(function () {
            // Resolve canonical prices from DB before writing anything
            $resolvedItems = [];
            foreach ($this->cart as $item) {
                abort_unless(in_array($item['tipe'], ['sparepart', 'service']), 422);

                if ($item['tipe'] === 'sparepart') {
                    $model = Sparepart::where('is_active', true)->findOrFail($item['id']);
                    $resolvedItems[] = [
                        'id' => $item['id'],
                        'tipe' => 'sparepart',
                        'qty' => $item['qty'],
                        'harga' => (float) $model->harga_jual,
                        'harga_beli' => (float) $model->harga_beli,
                        'nama' => $model->item_name,
                        'model' => $model,
                    ];
                } else {
                    $model = Service::where('is_active', true)->findOrFail($item['id']);
                    $resolvedItems[] = [
                        'id' => $item['id'],
                        'tipe' => 'service',
                        'qty' => $item['qty'],
                        'harga' => (float) $model->harga_default,
                        'harga_beli' => 0,
                        'nama' => $model->nama_service,
                        'model' => null,
                    ];
                }
            }

            $canonicalSubtotal = array_sum(array_map(fn ($i) => $i['harga'] * $i['qty'], $resolvedItems));
            $discount = (float) $this->discount;
            $canonicalGrandTotal = max(0, $canonicalSubtotal - $discount);

            $invoice = Invoice::create([
                'customer_id' => $this->customerId,
                'user_id' => auth('admin')->id(),
                'invoice_number' => 'INV-'.strtoupper(uniqid()),
                'tanggal' => now(),
                'tipe' => 'walk_in',
                'catatan' => $this->catatan ?: null,
                'subtotal' => $canonicalSubtotal,
                'discount' => $discount,
                'grand_total' => $canonicalGrandTotal,
                'payment_status' => $canonicalGrandTotal <= (float) $this->jumlahBayar ? 'paid' : 'unpaid',
                'amount_paid' => min((float) $this->jumlahBayar, $canonicalGrandTotal),
            ]);

            foreach ($resolvedItems as $item) {
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'service_id' => $item['tipe'] === 'service' ? $item['id'] : null,
                    'sparepart_id' => $item['tipe'] === 'sparepart' ? $item['id'] : null,
                    'type' => $item['tipe'],
                    'nama_snapshot' => $item['nama'],
                    'qty' => $item['qty'],
                    'harga_jual' => $item['harga'],
                    'harga_beli_snapshot' => $item['harga_beli'],
                    'subtotal' => $item['harga'] * $item['qty'],
                ]);

                if ($item['tipe'] === 'sparepart' && $item['model']) {
                    $sparepart = $item['model'];
                    $stockBefore = $sparepart->stock;
                    $sparepart->decrement('stock', $item['qty']);
                    StockMovement::create([
                        'sparepart_id' => $sparepart->id,
                        'user_id' => auth('admin')->id(),
                        'type' => 'out',
                        'qty' => $item['qty'],
                        'stock_before' => $stockBefore,
                        'stock_after' => $stockBefore - $item['qty'],
                        'reference_type' => Invoice::class,
                        'reference_id' => $invoice->id,
                    ]);
                }
            }

            if ($this->walkInTipe === 'servis' && $this->mode === 'walk_in') {
                WorkOrder::create([
                    'invoice_id' => $invoice->id,
                    'vehicle_id' => null,
                    'mekanik_id' => null,
                    'status' => 'antrian',
                    'keluhan_customer' => $this->keluhan,
                ]);
            }

            if ($this->mode === 'work_order' && $this->selectedWoId) {
                $wo = WorkOrder::find($this->selectedWoId);
                if ($wo) {
                    $invoice->update(['vehicle_id' => $wo->vehicle_id]);
                }
            }

            $this->invoiceSuccessId = (string) $invoice->id;
            $this->cart = [];
            $this->customerId = null;
            $this->customerNama = '';
            $this->keluhan = '';
            $this->catatan = '';
            $this->jumlahBayar = '';
            $this->discount = '0';
            $this->selectedWoId = null;
            $this->selectedWoLabel = '';
        });
    }

    public function render(): View
    {
        return view('livewire.admin.pos.index')
            ->layout('layouts.pos');
    }
}
