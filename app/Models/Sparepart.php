<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int|null $category_id
 * @property string $sku
 * @property string $item_name
 * @property string|null $brand
 * @property string $satuan
 * @property float $harga_beli
 * @property float $harga_jual
 * @property float|null $harga_online
 * @property int $stock
 * @property int $minimum_stock
 * @property int|null $berat
 * @property array|null $dimensi
 * @property array|null $images
 * @property string|null $deskripsi
 * @property bool $is_active
 * @property bool $is_sold_online
 */
class Sparepart extends Model
{
    protected $fillable = [
        'category_id', 'sku', 'item_name', 'brand', 'satuan',
        'harga_beli', 'harga_jual', 'harga_online',
        'stock', 'minimum_stock', 'berat', 'dimensi', 'images', 'deskripsi',
        'is_active', 'is_sold_online',
    ];

    protected function casts(): array
    {
        return [
            'harga_beli' => 'decimal:2',
            'harga_jual' => 'decimal:2',
            'harga_online' => 'decimal:2',
            'dimensi' => 'array',
            'images' => 'array',
            'is_active' => 'boolean',
            'is_sold_online' => 'boolean',
        ];
    }

    public function isStockCritical(): bool
    {
        return $this->stock <= $this->minimum_stock;
    }

    /** @return BelongsTo<SparepartCategory, $this> */
    public function category(): BelongsTo
    {
        return $this->belongsTo(SparepartCategory::class);
    }

    /** @return HasMany<StockMovement, $this> */
    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }
}
