<?php

namespace App\Livewire\Admin\Orders;

use Livewire\Component;

class OrderIndex extends Component
{
    public function render(): \Illuminate\View\View
    {
        return view('livewire.admin.orders.index')
            ->layout('layouts.admin');
    }
}
