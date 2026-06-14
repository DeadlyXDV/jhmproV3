<?php

namespace App\Livewire\Admin\WorkOrders;

use App\Models\User;
use App\Models\WorkOrder;
use Illuminate\View\View;
use Livewire\Component;

class WorkOrderDetail extends Component
{
    public int $workOrderId;

    public string $status = '';

    public ?int $mekanikId = null;

    public string $catatanMekanik = '';

    public function mount(WorkOrder $workOrder): void
    {
        $this->workOrderId = $workOrder->id;
        $this->status = $workOrder->status;
        $this->mekanikId = $workOrder->mekanik_id;
        $this->catatanMekanik = $workOrder->catatan_mekanik ?? '';
    }

    public function save(): void
    {
        $this->validate([
            'status' => 'required|in:pending,in_progress,done,cancelled',
            'mekanikId' => 'nullable|exists:users,id',
            'catatanMekanik' => 'nullable|string|max:2000',
        ]);

        $workOrder = WorkOrder::findOrFail($this->workOrderId);

        $mulaiAt = $workOrder->mulai_at;
        $selesaiAt = $workOrder->selesai_at;

        if ($this->status === 'in_progress' && ! $mulaiAt) {
            $mulaiAt = now();
        }

        if ($this->status === 'done' && ! $selesaiAt) {
            $selesaiAt = now();
        }

        $workOrder->update([
            'status' => $this->status,
            'mekanik_id' => $this->mekanikId,
            'catatan_mekanik' => $this->catatanMekanik ?: null,
            'mulai_at' => $mulaiAt,
            'selesai_at' => $selesaiAt,
        ]);

        session()->flash('success', 'Work order berhasil diperbarui.');
    }

    public function render(): View
    {
        $workOrder = WorkOrder::with([
            'vehicle',
            'vehicle.customer',
            'mekanik',
            'invoice',
            'invoice.items',
        ])->findOrFail($this->workOrderId);

        $mekanikList = User::where('role', 'mekanik')->where('is_active', true)->orderBy('name')->get();

        $statusList = ['pending', 'in_progress', 'done', 'cancelled'];

        return view('livewire.admin.work-orders.detail', compact('workOrder', 'mekanikList', 'statusList'))
            ->layout('layouts.admin', ['title' => $workOrder->wo_number]);
    }
}
