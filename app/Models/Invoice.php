<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * @property int $id
 * @property int|null $vehicle_id
 * @property int|null $customer_id
 * @property int|null $partner_id
 * @property int $user_id
 * @property int|null $booking_id
 * @property string $invoice_number
 * @property \Illuminate\Support\Carbon $tanggal
 * @property string $tipe
 * @property string|null $catatan
 * @property float $subtotal
 * @property float $discount
 * @property float $grand_total
 * @property string $payment_status
 * @property float $amount_paid
 */
class Invoice extends Model
{
    protected $fillable = [
        'vehicle_id', 'customer_id', 'partner_id', 'user_id', 'booking_id',
        'invoice_number', 'tanggal', 'tipe', 'catatan',
        'subtotal', 'discount', 'grand_total', 'payment_status', 'amount_paid',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
            'subtotal' => 'decimal:2',
            'discount' => 'decimal:2',
            'grand_total' => 'decimal:2',
            'amount_paid' => 'decimal:2',
        ];
    }

    /** @return BelongsTo<Customer, $this> */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /** @return BelongsTo<Vehicle, $this> */
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    /** @return BelongsTo<Partner, $this> */
    public function partner(): BelongsTo
    {
        return $this->belongsTo(Partner::class);
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** @return BelongsTo<Booking, $this> */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    /** @return HasMany<InvoiceItem, $this> */
    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    /** @return HasOne<WorkOrder, $this> */
    public function workOrder(): HasOne
    {
        return $this->hasOne(WorkOrder::class);
    }

    /** @return MorphMany<Payment, $this> */
    public function payments(): MorphMany
    {
        return $this->morphMany(Payment::class, 'payable');
    }
}
