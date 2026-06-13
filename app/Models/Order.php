<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * @property int $id
 * @property int|null $customer_id
 * @property string $order_number
 * @property string $nama_penerima
 * @property string $no_hp_penerima
 * @property string $alamat_kirim
 * @property string $provinsi
 * @property string $kota
 * @property string $kecamatan
 * @property string $kode_pos
 * @property float $subtotal
 * @property float $ongkir
 * @property float $discount
 * @property float $grand_total
 * @property string $status
 * @property string $payment_status
 */
class Order extends Model
{
    protected $fillable = [
        'customer_id', 'order_number',
        'nama_penerima', 'no_hp_penerima', 'alamat_kirim',
        'provinsi', 'kota', 'kecamatan', 'kode_pos',
        'subtotal', 'ongkir', 'discount', 'grand_total',
        'status', 'payment_status',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'ongkir' => 'decimal:2',
            'discount' => 'decimal:2',
            'grand_total' => 'decimal:2',
        ];
    }

    /** @return BelongsTo<Customer, $this> */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /** @return HasMany<OrderItem, $this> */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /** @return HasOne<Shipment, $this> */
    public function shipment(): HasOne
    {
        return $this->hasOne(Shipment::class);
    }

    /** @return MorphMany<Payment, $this> */
    public function payments(): MorphMany
    {
        return $this->morphMany(Payment::class, 'payable');
    }
}
