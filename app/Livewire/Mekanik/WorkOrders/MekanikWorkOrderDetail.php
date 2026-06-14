<?php

namespace App\Livewire\Mekanik\WorkOrders;

use App\Models\WorkOrder;
use Illuminate\View\View;
use Livewire\Component;

class MekanikWorkOrderDetail extends Component
{
    public int $workOrderId;

    public string $status = '';

    public string $catatanMekanik = '';

    public function mount(WorkOrder $workOrder): void
    {
        abort_unless(
            $workOrder->mekanik_id === auth('admin')->id(),
            403,
            'Anda tidak memiliki akses ke work order ini.'
        );

        $this->workOrderId = $workOrder->id;
        $this->status = $workOrder->status;
        $this->catatanMekanik = $workOrder->catatan_mekanik ?? '';
    }

    public function updateStatus(string $newStatus): void
    {
        abort_unless(
            in_array($newStatus, ['in_progress', 'done']),
            422
        );

        $workOrder = WorkOrder::findOrFail($this->workOrderId);

        abort_unless($workOrder->mekanik_id === auth('admin')->id(), 403);

        $mulaiAt = $workOrder->mulai_at;
        $selesaiAt = $workOrder->selesai_at;

        if ($newStatus === 'in_progress' && ! $mulaiAt) {
            $mulaiAt = now();
        }

        if ($newStatus === 'done' && ! $selesaiAt) {
            $selesaiAt = now();
        }

        $workOrder->update([
            'status' => $newStatus,
            'mulai_at' => $mulaiAt,
            'selesai_at' => $selesaiAt,
        ]);

        $this->status = $newStatus;
        session()->flash('success', 'Status work order diperbarui.');
    }

    public function saveCatatan(): void
    {
        $this->validate([
            'catatanMekanik' => 'nullable|string|max:2000',
        ]);

        $workOrder = WorkOrder::findOrFail($this->workOrderId);

        abort_unless($workOrder->mekanik_id === auth('admin')->id(), 403);

        $workOrder->update(['catatan_mekanik' => $this->catatanMekanik ?: null]);

        session()->flash('success', 'Catatan tersimpan.');
    }

    public function render(): View
    {
        $workOrder = WorkOrder::with([
            'vehicle',
            'vehicle.customer',
            'invoice',
            'invoice.items',
        ])->findOrFail($this->workOrderId);

        return view('livewire.mekanik.work-orders.detail', compact('workOrder'))
            ->layout('layouts.mekanik', ['title' => $workOrder->wo_number]);
    }
}
