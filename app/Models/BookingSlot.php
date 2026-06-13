<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int|null $mekanik_id
 * @property \Illuminate\Support\Carbon $tanggal
 * @property int $kapasitas
 * @property int $terisi
 * @property bool $is_blocked
 * @property string|null $blocked_reason
 */
class BookingSlot extends Model
{
    protected $fillable = [
        'mekanik_id', 'tanggal', 'kapasitas', 'terisi', 'is_blocked', 'blocked_reason',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
            'is_blocked' => 'boolean',
        ];
    }

    public function isAvailable(): bool
    {
        return ! $this->is_blocked && $this->terisi < $this->kapasitas;
    }

    /** @return BelongsTo<User, $this> */
    public function mekanik(): BelongsTo
    {
        return $this->belongsTo(User::class, 'mekanik_id');
    }
}
