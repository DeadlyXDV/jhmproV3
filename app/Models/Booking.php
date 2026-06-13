<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property int $id
 * @property int|null $customer_id
 * @property int|null $vehicle_id
 * @property int|null $mekanik_id
 * @property int|null $invoice_id
 * @property string $booking_number
 * @property \Illuminate\Support\Carbon $tanggal_booking
 * @property string|null $jam_mulai
 * @property string|null $jam_selesai
 * @property string $status
 * @property string $source
 * @property string|null $keluhan
 * @property string|null $catatan_admin
 * @property string $nama_pemesan
 * @property string $no_hp_pemesan
 * @property \Illuminate\Support\Carbon|null $confirmed_at
 * @property \Illuminate\Support\Carbon|null $cancelled_at
 * @property string|null $cancel_reason
 */
class Booking extends Model
{
    protected $fillable = [
        'customer_id', 'vehicle_id', 'mekanik_id', 'invoice_id',
        'booking_number', 'tanggal_booking', 'jam_mulai', 'jam_selesai',
        'status', 'source', 'keluhan', 'catatan_admin',
        'nama_pemesan', 'no_hp_pemesan',
        'confirmed_at', 'cancelled_at', 'cancel_reason',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_booking' => 'date',
            'confirmed_at' => 'datetime',
            'cancelled_at' => 'datetime',
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

    /** @return BelongsTo<User, $this> */
    public function mekanik(): BelongsTo
    {
        return $this->belongsTo(User::class, 'mekanik_id');
    }

    /** @return BelongsTo<Invoice, $this> */
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    /** @return HasMany<BookingService, $this> */
    public function services(): HasMany
    {
        return $this->hasMany(BookingService::class);
    }
}
