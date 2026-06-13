<?php

namespace App\Livewire\Admin\Vehicles;

use Livewire\Component;

class VehicleDetail extends Component
{
    public function render(): \Illuminate\View\View
    {
        return view('livewire.admin.vehicles.detail')
            ->layout('layouts.admin');
    }
}
