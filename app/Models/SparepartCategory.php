<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int|null $parent_id
 * @property string $name
 * @property string $slug
 * @property string|null $icon_image
 * @property string|null $description
 */
class SparepartCategory extends Model
{
    protected $fillable = ['parent_id', 'name', 'slug', 'icon_image', 'description'];

    /** @return BelongsTo<SparepartCategory, $this> */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(SparepartCategory::class, 'parent_id');
    }

    /** @return HasMany<SparepartCategory, $this> */
    public function children(): HasMany
    {
        return $this->hasMany(SparepartCategory::class, 'parent_id');
    }

    /** @return HasMany<Sparepart, $this> */
    public function spareparts(): HasMany
    {
        return $this->hasMany(Sparepart::class, 'category_id');
    }
}
