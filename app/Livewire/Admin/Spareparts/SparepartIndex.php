<?php

namespace App\Livewire\Admin\Spareparts;

use App\Models\Sparepart;
use App\Models\SparepartCategory;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class SparepartIndex extends Component
{
    use WithPagination;

    public string $search = '';

    public string $filterCategory = '';

    public bool $filterCritical = false;

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedFilterCategory(): void
    {
        $this->resetPage();
    }

    public function updatedFilterCritical(): void
    {
        $this->resetPage();
    }

    public function toggleActive(int $id): void
    {
        $sparepart = Sparepart::findOrFail($id);
        $sparepart->update(['is_active' => ! $sparepart->is_active]);
    }

    public function render(): View
    {
        $spareparts = Sparepart::query()
            ->with('category')
            ->when($this->search, function ($q) {
                $q->where(function ($q2) {
                    $q2->where('sku', 'like', "%{$this->search}%")
                        ->orWhere('item_name', 'like', "%{$this->search}%")
                        ->orWhere('brand', 'like', "%{$this->search}%");
                });
            })
            ->when($this->filterCategory, fn ($q) => $q->where('category_id', $this->filterCategory))
            ->when($this->filterCritical, fn ($q) => $q->whereColumn('stock', '<=', 'minimum_stock'))
            ->orderBy('item_name')
            ->paginate(20);

        $categories = SparepartCategory::query()
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('livewire.admin.spareparts.index', compact('spareparts', 'categories'))
            ->layout('layouts.admin', ['title' => 'Sparepart']);
    }
}
