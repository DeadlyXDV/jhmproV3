<?php

namespace App\Livewire\Admin\StockMovements;

use App\Models\StockMovement;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class StockMovementIndex extends Component
{
    use WithPagination;

    public string $search = '';

    public string $filterType = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedFilterType(): void
    {
        $this->resetPage();
    }

    public function render(): View
    {
        $movements = StockMovement::query()
            ->with(['sparepart', 'user'])
            ->when($this->search, function ($q) {
                $q->whereHas('sparepart', function ($q2) {
                    $q2->where('item_name', 'like', "%{$this->search}%")
                        ->orWhere('sku', 'like', "%{$this->search}%");
                });
            })
            ->when($this->filterType, fn ($q) => $q->where('type', $this->filterType))
            ->latest()
            ->paginate(25);

        return view('livewire.admin.stock-movements.index', compact('movements'))
            ->layout('layouts.admin', ['title' => 'Riwayat Stok']);
    }
}
