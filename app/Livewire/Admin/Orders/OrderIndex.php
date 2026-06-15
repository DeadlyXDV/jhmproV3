<?php

namespace App\Livewire\Admin\Orders;

use App\Models\Order;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class OrderIndex extends Component
{
    use WithPagination;

    public string $search = '';

    public string $filterStatus = '';

    public string $filterPayment = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedFilterStatus(): void
    {
        $this->resetPage();
    }

    public function updatedFilterPayment(): void
    {
        $this->resetPage();
    }

    public function render(): View
    {
        $orders = Order::query()
            ->with(['customer', 'shipment'])
            ->when($this->search, function ($q) {
                $q->where(function ($q2) {
                    $q2->where('order_number', 'like', "%{$this->search}%")
                        ->orWhere('nama_penerima', 'like', "%{$this->search}%");
                });
            })
            ->when($this->filterStatus, fn ($q) => $q->where('status', $this->filterStatus))
            ->when($this->filterPayment, fn ($q) => $q->where('payment_status', $this->filterPayment))
            ->latest()
            ->paginate(15);

        $statusList = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];
        $paymentList = ['unpaid', 'paid', 'refunded'];

        return view('livewire.admin.orders.index', compact('orders', 'statusList', 'paymentList'))
            ->layout('layouts.admin', ['title' => 'Orders']);
    }
}
