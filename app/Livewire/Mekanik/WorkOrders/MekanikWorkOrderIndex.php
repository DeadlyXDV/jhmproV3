<?php

namespace App\Livewire\Mekanik\WorkOrders;

use Livewire\Component;

class MekanikWorkOrderIndex extends Component
{
    public function render(): \Illuminate\View\View
    {
        return view('livewire.mekanik.work-orders.index')
            ->layout('layouts.mekanik');
    }
}
