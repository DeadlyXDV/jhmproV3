<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductBundleItem extends Model
{
    protected $fillable = [
        'bundle_id', 'sparepart_id', 'service_id', 'type', 'qty', 'harga_snapshot',
    ];

    protected function casts(): array
    {
        return ['harga_snapshot' => 'decimal:2'];
    }

    /** @return BelongsTo<ProductBundle, $this> */
    public function bundle(): BelongsTo
    {
        return $this->belongsTo(ProductBundle::class);
    }

    /** @return BelongsTo<Sparepart, $this> */
    public function sparepart(): BelongsTo
    {
        return $this->belongsTo(Sparepart::class);
    }

    /** @return BelongsTo<Service, $this> */
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }
}
