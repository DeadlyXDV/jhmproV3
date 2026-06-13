<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $vehicle_id
 * @property int|null $invoice_id
 * @property int $user_id
 * @property string $judul
 * @property string|null $deskripsi
 * @property array|null $specs_snapshot
 * @property array|null $parts_used
 * @property array|null $foto
 * @property \Illuminate\Support\Carbon $logged_at
 */
class VehicleModificationLog extends Model
{
    protected $fillable = [
        'vehicle_id', 'invoice_id', 'user_id',
        'judul', 'deskripsi', 'specs_snapshot', 'parts_used', 'foto', 'logged_at',
    ];

    protected function casts(): array
    {
        return [
            'specs_snapshot' => 'array',
            'parts_used' => 'array',
            'foto' => 'array',
            'logged_at' => 'datetime',
        ];
    }

    /** @return BelongsTo<Vehicle, $this> */
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    /** @return BelongsTo<Invoice, $this> */
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
