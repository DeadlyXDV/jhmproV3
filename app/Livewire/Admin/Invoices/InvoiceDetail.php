<?php

namespace App\Livewire\Admin\Invoices;

use App\Models\Invoice;
use Illuminate\View\View;
use Livewire\Component;

class InvoiceDetail extends Component
{
    public int $invoiceId;

    public function mount(Invoice $invoice): void
    {
        $this->invoiceId = $invoice->id;
    }

    public function render(): View
    {
        $invoice = Invoice::with([
            'customer',
            'vehicle',
            'user',
            'items',
            'items.service',
            'items.sparepart',
            'workOrder',
            'payments',
        ])->findOrFail($this->invoiceId);

        return view('livewire.admin.invoices.detail', compact('invoice'))
            ->layout('layouts.admin', ['title' => $invoice->invoice_number]);
    }
}
