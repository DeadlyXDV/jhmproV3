<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

/**
 * @property int $id
 * @property string $wo_number
 * @property int $invoice_id
 * @property int $vehicle_id
 * @property int|null $mekanik_id
 * @property string $status
 * @property string|null $keluhan_customer
 * @property string|null $catatan_mekanik
 * @property \Illuminate\Support\Carbon|null $mulai_at
 * @property \Illuminate\Support\Carbon|null $selesai_at
 */
class WorkOrder extends Model
{
    protected $fillable = [
        'wo_number', 'invoice_id', 'vehicle_id', 'mekanik_id',
        'status', 'keluhan_customer', 'catatan_mekanik', 'mulai_at', 'selesai_at',
    ];

    protected function casts(): array
    {
        return [
            'mulai_at' => 'datetime',
            'selesai_at' => 'datetime',
        ];
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (WorkOrder $workOrder) {
            if (empty($workOrder->wo_number)) {
                $last = DB::table('work_orders')->orderByDesc('id')->value('wo_number');
                $next = $last
                    ? str_pad((int) substr($last, 3) + 1, 4, '0', STR_PAD_LEFT)
                    : '0001';
                $workOrder->wo_number = 'WO-'.$next;
            }
        });
    }

    /** @return BelongsTo<Invoice, $this> */
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    /** @return BelongsTo<Vehicle, $this> */
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    /** @return BelongsTo<User, $this> */
    public function mekanik(): BelongsTo
    {
        return $this->belongsTo(User::class, 'mekanik_id');
    }
}
