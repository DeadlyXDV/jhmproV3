<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id', 'sparepart_id', 'bundle_id', 'type',
        'nama_snapshot', 'qty', 'harga_snapshot', 'subtotal',
    ];

    protected function casts(): array
    {
        return [
            'harga_snapshot' => 'decimal:2',
            'subtotal' => 'decimal:2',
        ];
    }

    /** @return BelongsTo<Order, $this> */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /** @return BelongsTo<Sparepart, $this> */
    public function sparepart(): BelongsTo
    {
        return $this->belongsTo(Sparepart::class);
    }

    /** @return BelongsTo<ProductBundle, $this> */
    public function bundle(): BelongsTo
    {
        return $this->belongsTo(ProductBundle::class);
    }
}
