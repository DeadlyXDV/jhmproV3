<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $payable_type
 * @property int $payable_id
 * @property string|null $midtrans_order_id
 * @property string|null $midtrans_transaction_id
 * @property string|null $midtrans_status
 * @property string|null $payment_method
 * @property float $amount
 * @property string|null $snap_token
 * @property string|null $payment_url
 * @property array|null $raw_response
 * @property Carbon|null $paid_at
 * @property Carbon|null $expired_at
 */
class Payment extends Model
{
    protected $fillable = [
        'payable_type', 'payable_id',
        'midtrans_order_id', 'midtrans_transaction_id', 'midtrans_status',
        'payment_method', 'amount', 'snap_token', 'payment_url', 'raw_response',
        'paid_at', 'expired_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'raw_response' => 'array',
            'paid_at' => 'datetime',
            'expired_at' => 'datetime',
        ];
    }

    /** @return MorphTo<Model, $this> */
    public function payable(): MorphTo
    {
        return $this->morphTo();
    }
}
