<?php

namespace App\Livewire\Admin\WorkOrders;

use App\Models\WorkOrder;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class WorkOrderIndex extends Component
{
    use WithPagination;

    public string $search = '';

    public string $filterStatus = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedFilterStatus(): void
    {
        $this->resetPage();
    }

    public function render(): View
    {
        $workOrders = WorkOrder::query()
            ->with(['vehicle', 'vehicle.customer', 'mekanik'])
            ->when($this->search, function ($q) {
                $q->where(function ($q2) {
                    $q2->where('wo_number', 'like', "%{$this->search}%")
                        ->orWhereHas('vehicle.customer', fn ($r) => $r->where('nama', 'like', "%{$this->search}%"))
                        ->orWhereHas('vehicle', fn ($r) => $r->where('plat_nomor', 'like', "%{$this->search}%"));
                });
            })
            ->when($this->filterStatus, fn ($q) => $q->where('status', $this->filterStatus))
            ->latest()
            ->paginate(15);

        $statusList = ['pending', 'in_progress', 'done', 'cancelled'];

        return view('livewire.admin.work-orders.index', compact('workOrders', 'statusList'))
            ->layout('layouts.admin', ['title' => 'Work Order']);
    }
}
