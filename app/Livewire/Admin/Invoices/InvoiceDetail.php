<?php

namespace App\Livewire\Admin\Invoices;

use Livewire\Component;

class InvoiceDetail extends Component
{
    public function render(): \Illuminate\View\View
    {
        return view('livewire.admin.invoices.detail')
            ->layout('layouts.admin');
    }
}
