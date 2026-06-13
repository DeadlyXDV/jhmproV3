<?php

namespace App\Livewire\Admin\WorkOrders;

use Livewire\Component;

class WorkOrderDetail extends Component
{
    public function render(): \Illuminate\View\View
    {
        return view('livewire.admin.work-orders.detail')
            ->layout('layouts.admin');
    }
}
