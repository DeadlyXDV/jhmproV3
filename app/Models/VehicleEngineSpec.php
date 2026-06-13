<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VehicleEngineSpec extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'vehicle_id',
        'cylinder_head', 'porting_polish', 'klep_in', 'klep_ex', 'per_klep', 'noken_as',
        'cylinder_block', 'boring_size', 'piston', 'piston_ring', 'pen_piston',
        'crankshaft', 'stroke', 'big_end', 'small_end',
        'kopling', 'per_kopling',
        'karburator_injeksi', 'filter_udara', 'knalpot',
        'pengapian_type', 'cdi_ecu', 'koil', 'busi',
        'kelistrikan_acg', 'kelistrikan_aki',
        'rasio_gigi', 'gir_depan', 'gir_belakang', 'rantai',
        'catatan_tambahan',
    ];

    /** @return BelongsTo<Vehicle, $this> */
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }
}
