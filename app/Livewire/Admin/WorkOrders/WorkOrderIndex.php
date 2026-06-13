<?php

namespace App\Livewire\Admin\WorkOrders;

use Livewire\Component;

class WorkOrderIndex extends Component
{
    public function render(): \Illuminate\View\View
    {
        return view('livewire.admin.work-orders.index')
            ->layout('layouts.admin');
    }
}
