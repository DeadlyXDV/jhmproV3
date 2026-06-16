<?php

namespace App\Observers;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Sparepart;
use App\Models\StockMovement;

class InvoiceItemObserver
{
    public function created(InvoiceItem $item): void
    {
        if (! $item->sparepart_id) {
            return;
        }

        $sparepart = Sparepart::lockForUpdate()->findOrFail($item->sparepart_id);
        $stockBefore = $sparepart->stock;
        $sparepart->decrement('stock', $item->qty);

        StockMovement::create([
            'sparepart_id' => $sparepart->id,
            'user_id' => auth('admin')->id() ?? auth()->id(),
            'type' => 'out',
            'qty' => $item->qty,
            'stock_before' => $stockBefore,
            'stock_after' => $stockBefore - $item->qty,
            'reference_type' => Invoice::class,
            'reference_id' => $item->invoice_id,
        ]);
    }
}
