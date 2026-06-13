<?php

namespace App\Livewire\Admin\Vehicles;

use Livewire\Component;

class VehicleIndex extends Component
{
    public function render(): \Illuminate\View\View
    {
        return view('livewire.admin.vehicles.index')
            ->layout('layouts.admin');
    }
}
