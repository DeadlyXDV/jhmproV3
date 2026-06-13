<?php

namespace App\Livewire\Admin\Invoices;

use Livewire\Component;

class InvoiceCreate extends Component
{
    public function render(): \Illuminate\View\View
    {
        return view('livewire.admin.invoices.create')
            ->layout('layouts.admin');
    }
}
