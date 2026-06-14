<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RfmHistory extends Model
{
    protected $table = 'rfm_history';

    public $timestamps = false;

    protected $fillable = [
        'customer_id', 'source', 'year_month',
        'recency_days', 'frequency', 'monetary',
        'cluster_id', 'cluster_label',
    ];

    protected function casts(): array
    {
        return ['monetary' => 'decimal:2'];
    }

    /** @return BelongsTo<Customer, $this> */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
}
