<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $sparepart_id
 * @property int $user_id
 * @property string $type
 * @property int $qty
 * @property int $stock_before
 * @property int $stock_after
 * @property string|null $reference_type
 * @property int|null $reference_id
 * @property string|null $catatan
 */
class StockMovement extends Model
{
    protected $fillable = [
        'sparepart_id', 'user_id', 'type', 'qty',
        'stock_before', 'stock_after', 'reference_type', 'reference_id', 'catatan',
    ];

    /** @return BelongsTo<Sparepart, $this> */
    public function sparepart(): BelongsTo
    {
        return $this->belongsTo(Sparepart::class);
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
