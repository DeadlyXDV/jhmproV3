<?php

namespace App\Livewire\Admin\Invoices;

use App\Models\Invoice;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class InvoiceIndex extends Component
{
    use WithPagination;

    public string $search = '';

    public string $filterStatus = '';

    public string $filterTipe = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedFilterStatus(): void
    {
        $this->resetPage();
    }

    public function updatedFilterTipe(): void
    {
        $this->resetPage();
    }

    public function render(): View
    {
        $invoices = Invoice::query()
            ->with(['customer', 'vehicle', 'user'])
            ->when($this->search, function ($q) {
                $q->where(function ($q2) {
                    $q2->where('invoice_number', 'like', "%{$this->search}%")
                        ->orWhereHas('customer', fn ($r) => $r->where('nama', 'like', "%{$this->search}%"));
                });
            })
            ->when($this->filterStatus, fn ($q) => $q->where('payment_status', $this->filterStatus))
            ->when($this->filterTipe, fn ($q) => $q->where('tipe', $this->filterTipe))
            ->latest('tanggal')
            ->paginate(15);

        $statusList = ['unpaid', 'partial', 'paid'];
        $tipeList = ['jasa', 'sparepart', 'bundle', 'campuran'];

        return view('livewire.admin.invoices.index', compact('invoices', 'statusList', 'tipeList'))
            ->layout('layouts.admin', ['title' => 'Invoice']);
    }
}
