<?php

namespace App\Livewire\Mekanik\WorkOrders;

use Livewire\Component;

class MekanikWorkOrderDetail extends Component
{
    public function render(): \Illuminate\View\View
    {
        return view('livewire.mekanik.work-orders.detail')
            ->layout('layouts.mekanik');
    }
}
