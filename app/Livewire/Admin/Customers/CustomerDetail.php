<?php

namespace App\Livewire\Admin\Customers;

use App\Models\Customer;
use Illuminate\View\View;
use Livewire\Component;

class CustomerDetail extends Component
{
    public int $customerId;

    public string $activeTab = 'vehicles';

    public function mount(Customer $customer): void
    {
        $this->customerId = $customer->id;
    }

    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;
    }

    public function render(): View
    {
        $customer = Customer::with([
            'rfm' => fn ($q) => $q->latest('calculated_at'),
            'vehicles',
            'invoices' => fn ($q) => $q->latest('tanggal')->take(20),
            'bookings' => fn ($q) => $q->latest('tanggal_booking')->take(20),
        ])
            ->withCount('vehicles')
            ->withSum(['invoices as total_transaksi' => fn ($q) => $q->where('payment_status', 'paid')], 'grand_total')
            ->withCount(['invoices as total_kunjungan'])
            ->findOrFail($this->customerId);

        $rfm = $customer->rfm->first();

        return view('livewire.admin.customers.detail', compact('customer', 'rfm'))
            ->layout('layouts.admin', ['title' => $customer->nama]);
    }
}
