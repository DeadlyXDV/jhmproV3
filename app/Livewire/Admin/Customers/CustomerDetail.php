<?php

namespace App\Livewire\Admin\Customers;

use Livewire\Component;

class CustomerDetail extends Component
{
    public function render(): \Illuminate\View\View
    {
        return view('livewire.admin.customers.detail')
            ->layout('layouts.admin');
    }
}
