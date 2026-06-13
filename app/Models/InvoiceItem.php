<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvoiceItem extends Model
{
    protected $fillable = [
        'invoice_id', 'service_id', 'sparepart_id', 'type',
        'nama_snapshot', 'qty', 'harga_jual', 'harga_beli_snapshot', 'subtotal',
    ];

    protected function casts(): array
    {
        return [
            'harga_jual' => 'decimal:2',
            'harga_beli_snapshot' => 'decimal:2',
            'subtotal' => 'decimal:2',
        ];
    }

    /** @return BelongsTo<Invoice, $this> */
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    /** @return BelongsTo<Service, $this> */
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    /** @return BelongsTo<Sparepart, $this> */
    public function sparepart(): BelongsTo
    {
        return $this->belongsTo(Sparepart::class);
    }
}
