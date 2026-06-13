<?php

namespace App\Observers;

use App\Models\Vehicle;

class VehicleObserver
{
    public function created(Vehicle $vehicle): void
    {
        // Auto-create engine specs saat kendaraan baru didaftarkan (semua field null)
        $vehicle->engineSpecs()->create([]);
    }
}
