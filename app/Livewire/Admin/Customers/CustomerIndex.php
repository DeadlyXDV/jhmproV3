<?php

namespace App\Livewire\Admin\Customers;

use App\Models\Customer;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class CustomerIndex extends Component
{
    use WithPagination;

    public string $search = '';

    public string $filterSegmen = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedFilterSegmen(): void
    {
        $this->resetPage();
    }

    public function render(): View
    {
        $customers = Customer::query()
            ->when($this->search, function ($q) {
                $q->where(function ($q2) {
                    $q2->where('nama', 'like', "%{$this->search}%")
                        ->orWhere('no_hp', 'like', "%{$this->search}%")
                        ->orWhere('email', 'like', "%{$this->search}%");
                });
            })
            ->when($this->filterSegmen, function ($q) {
                $q->whereHas('rfm', fn ($r) => $r->where('cluster_label', $this->filterSegmen));
            })
            ->withCount('vehicles')
            ->withSum(['invoices as total_transaksi' => fn ($q) => $q->where('payment_status', 'paid')], 'grand_total')
            ->with(['rfm' => fn ($q) => $q->latest('calculated_at')])
            ->latest()
            ->paginate(15);

        $segmenList = ['Champion', 'Loyal', 'Potential', 'At Risk', 'Lost'];

        return view('livewire.admin.customers.index', [
            'customers' => $customers,
            'segmenList' => $segmenList,
        ])->layout('layouts.admin', ['title' => 'Pelanggan']);
    }
}
