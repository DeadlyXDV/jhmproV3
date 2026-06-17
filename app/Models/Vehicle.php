<?php

namespace App\Models;

use Database\Factories\VehicleFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property int $customer_id
 * @property string $merk
 * @property string $model
 * @property string|null $tipe
 * @property int $tahun
 * @property string $no_polisi
 * @property string|null $no_rangka
 * @property string|null $no_mesin
 * @property string|null $warna
 * @property string|null $foto
 * @property string|null $catatan
 */
class Vehicle extends Model
{
    /** @use HasFactory<VehicleFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'customer_id', 'merk', 'model', 'tipe', 'tahun',
        'no_polisi', 'no_rangka', 'no_mesin', 'warna', 'foto', 'catatan',
    ];

    protected function casts(): array
    {
        return ['tahun' => 'integer'];
    }

    /** @return BelongsTo<Customer, $this> */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /** @return HasOne<VehicleEngineSpec, $this> */
    public function engineSpecs(): HasOne
    {
        return $this->hasOne(VehicleEngineSpec::class);
    }

    /** @return HasMany<VehicleModificationLog, $this> */
    public function modificationLogs(): HasMany
    {
        return $this->hasMany(VehicleModificationLog::class)->latest('logged_at');
    }

    /** @return HasMany<Invoice, $this> */
    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    /** @return HasMany<WorkOrder, $this> */
    public function workOrders(): HasMany
    {
        return $this->hasMany(WorkOrder::class);
    }

    /** @return HasMany<Booking, $this> */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }
}
