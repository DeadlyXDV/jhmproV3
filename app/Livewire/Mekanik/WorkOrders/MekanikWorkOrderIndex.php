<?php

namespace App\Livewire\Mekanik\WorkOrders;

use App\Models\WorkOrder;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class MekanikWorkOrderIndex extends Component
{
    use WithPagination;

    public string $filterStatus = '';

    public function updatedFilterStatus(): void
    {
        $this->resetPage();
    }

    public function render(): View
    {
        $workOrders = WorkOrder::query()
            ->where('mekanik_id', auth('admin')->id())
            ->with(['vehicle', 'vehicle.customer'])
            ->when($this->filterStatus, fn ($q) => $q->where('status', $this->filterStatus))
            ->latest()
            ->paginate(15);

        return view('livewire.mekanik.work-orders.index', compact('workOrders'))
            ->layout('layouts.mekanik', ['title' => 'Work Order Saya']);
    }
}
