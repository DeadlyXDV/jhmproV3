<?php

namespace App\Livewire\Admin\Vehicles;

use App\Models\Vehicle;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class VehicleIndex extends Component
{
    use WithPagination;

    public string $search = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function render(): View
    {
        $vehicles = Vehicle::query()
            ->with('customer')
            ->when($this->search, function ($q) {
                $q->where(function ($q2) {
                    $q2->where('no_polisi', 'like', "%{$this->search}%")
                        ->orWhere('merk', 'like', "%{$this->search}%")
                        ->orWhere('model', 'like', "%{$this->search}%")
                        ->orWhereHas('customer', fn ($c) => $c->where('nama', 'like', "%{$this->search}%"));
                });
            })
            ->withCount('invoices as total_servis')
            ->latest()
            ->paginate(15);

        return view('livewire.admin.vehicles.index', compact('vehicles'))
            ->layout('layouts.admin', ['title' => 'Kendaraan']);
    }
}
