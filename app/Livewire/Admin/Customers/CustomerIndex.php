<?php

namespace App\Livewire\Admin\Customers;

use Livewire\Component;

class CustomerIndex extends Component
{
    public function render(): \Illuminate\View\View
    {
        return view('livewire.admin.customers.index')
            ->layout('layouts.admin');
    }
}
