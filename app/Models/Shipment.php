<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Shipment extends Model
{
    protected $fillable = [
        'order_id', 'shipbite_order_id', 'courier', 'service_type', 'tracking_number',
        'origin_address', 'destination_address', 'weight',
        'shipping_cost', 'insurance_cost', 'status', 'raw_response',
        'shipped_at', 'delivered_at',
    ];

    protected function casts(): array
    {
        return [
            'origin_address' => 'array',
            'destination_address' => 'array',
            'raw_response' => 'array',
            'shipping_cost' => 'decimal:2',
            'insurance_cost' => 'decimal:2',
            'shipped_at' => 'datetime',
            'delivered_at' => 'datetime',
        ];
    }

    /** @return BelongsTo<Order, $this> */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
