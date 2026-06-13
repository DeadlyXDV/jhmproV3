<?php

namespace App\Livewire\Admin\Invoices;

use Livewire\Component;

class InvoiceIndex extends Component
{
    public function render(): \Illuminate\View\View
    {
        return view('livewire.admin.invoices.index')
            ->layout('layouts.admin');
    }
}
