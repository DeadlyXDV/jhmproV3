<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $nama
 * @property string $slug
 * @property string|null $deskripsi
 * @property float $harga
 * @property array|null $images
 * @property bool $is_active
 * @property bool $is_sold_online
 * @property bool $is_bookable
 */
class ProductBundle extends Model
{
    protected $fillable = [
        'nama', 'slug', 'deskripsi', 'harga', 'images',
        'is_active', 'is_sold_online', 'is_bookable',
    ];

    protected function casts(): array
    {
        return [
            'harga' => 'decimal:2',
            'images' => 'array',
            'is_active' => 'boolean',
            'is_sold_online' => 'boolean',
            'is_bookable' => 'boolean',
        ];
    }

    /** @return HasMany<ProductBundleItem, $this> */
    public function items(): HasMany
    {
        return $this->hasMany(ProductBundleItem::class, 'bundle_id');
    }
}
