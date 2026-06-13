<?php

namespace App\Livewire\Admin\Customers;

use Livewire\Component;

class CustomerCreate extends Component
{
    public function render(): \Illuminate\View\View
    {
        return view('livewire.admin.customers.create')
            ->layout('layouts.admin');
    }
}
