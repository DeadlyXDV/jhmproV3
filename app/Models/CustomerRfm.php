<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $customer_id
 * @property string $source
 * @property int $recency_days
 * @property int $frequency
 * @property float $monetary
 * @property int $r_score
 * @property int $f_score
 * @property int $m_score
 * @property float $rfm_score
 * @property int $cluster_id
 * @property string $cluster_label
 * @property \Illuminate\Support\Carbon $period_start
 * @property \Illuminate\Support\Carbon $period_end
 * @property \Illuminate\Support\Carbon $calculated_at
 */
class CustomerRfm extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'customer_id', 'source',
        'recency_days', 'frequency', 'monetary',
        'r_score', 'f_score', 'm_score', 'rfm_score',
        'cluster_id', 'cluster_label',
        'period_start', 'period_end', 'calculated_at',
    ];

    protected function casts(): array
    {
        return [
            'monetary' => 'decimal:2',
            'rfm_score' => 'decimal:2',
            'period_start' => 'date',
            'period_end' => 'date',
            'calculated_at' => 'datetime',
        ];
    }

    /** @return BelongsTo<Customer, $this> */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /** @return BelongsTo<ClusterDefinition, $this> */
    public function cluster(): BelongsTo
    {
        return $this->belongsTo(ClusterDefinition::class, 'cluster_id');
    }
}
