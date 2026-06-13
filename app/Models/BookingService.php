<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookingService extends Model
{
    protected $fillable = [
        'booking_id', 'service_id', 'bundle_id', 'type', 'nama_snapshot', 'harga_estimasi',
    ];

    protected function casts(): array
    {
        return ['harga_estimasi' => 'decimal:2'];
    }

    /** @return BelongsTo<Booking, $this> */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    /** @return BelongsTo<Service, $this> */
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    /** @return BelongsTo<ProductBundle, $this> */
    public function bundle(): BelongsTo
    {
        return $this->belongsTo(ProductBundle::class);
    }
}
