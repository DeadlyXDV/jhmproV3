<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property int $id
 * @property int|null $user_id
 * @property string $nama
 * @property string $no_hp
 * @property string|null $email
 * @property string|null $alamat
 * @property string|null $catatan
 */
class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'nama',
        'no_hp',
        'email',
        'alamat',
        'catatan',
    ];

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** @return HasMany<Vehicle, $this> */
    public function vehicles(): HasMany
    {
        return $this->hasMany(Vehicle::class);
    }

    /** @return HasMany<Invoice, $this> */
    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    /** @return HasMany<Booking, $this> */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    /** @return HasMany<Order, $this> */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /** @return HasOne<CustomerRfm, $this> */
    public function latestRfm(): HasOne
    {
        return $this->hasOne(CustomerRfm::class)->where('source', 'combined')->latestOfMany('calculated_at');
    }

    /** @return HasMany<CustomerRfm, $this> */
    public function rfm(): HasMany
    {
        return $this->hasMany(CustomerRfm::class);
    }
}
