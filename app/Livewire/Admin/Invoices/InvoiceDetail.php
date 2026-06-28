<?php

namespace App\Livewire\Admin\Invoices;

use App\Models\Invoice;
use App\Models\Sparepart;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Livewire\Component;

class InvoiceDetail extends Component
{
    public int $invoiceId;

    public function mount(Invoice $invoice): void
    {
        $this->invoiceId = $invoice->id;
    }

    public function voidInvoice(): void
    {
        abort_unless(auth('admin')->user()?->isSuperAdmin(), 403);

        DB::transaction(function () {
            $invoice = Invoice::with('items.sparepart')
                ->lockForUpdate()
                ->findOrFail($this->invoiceId);

            abort_if($invoice->payment_status === 'voided', 422);

            foreach ($invoice->items as $item) {
                if (! $item->sparepart_id || ! $item->sparepart) {
                    continue;
                }

                $sparepart = Sparepart::lockForUpdate()->findOrFail($item->sparepart_id);
                $stockBefore = $sparepart->stock;
                $sparepart->increment('stock', $item->qty);

                StockMovement::create([
                    'sparepart_id' => $sparepart->id,
                    'user_id' => auth('admin')->id(),
                    'type' => 'adjustment',
                    'qty' => $item->qty,
                    'stock_before' => $stockBefore,
                    'stock_after' => $stockBefore + $item->qty,
                    'reference_type' => Invoice::class,
                    'reference_id' => $invoice->id,
                    'catatan' => 'Void invoice '.$invoice->invoice_number,
                ]);
            }

            $invoice->update(['payment_status' => 'voided']);
        });

        session()->flash('success', 'Invoice berhasil di-void dan stok dikembalikan.');
    }

    public function render(): View
    {
        $invoice = Invoice::with([
            'customer',
            'vehicle',
            'user',
            'items',
            'items.service',
            'items.sparepart',
            'workOrder',
            'payments',
        ])->findOrFail($this->invoiceId);

        return view('livewire.admin.invoices.detail', compact('invoice'))
            ->layout('layouts.admin', ['title' => $invoice->invoice_number]);
    }
}
