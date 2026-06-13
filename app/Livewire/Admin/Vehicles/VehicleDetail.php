<?php

namespace App\Livewire\Admin\Vehicles;

use App\Models\Vehicle;
use App\Models\VehicleEngineSpec;
use App\Models\VehicleModificationLog;
use Illuminate\View\View;
use Livewire\Component;

class VehicleDetail extends Component
{
    public int $vehicleId;

    public string $activeTab = 'specs';

    public bool $editingSpecs = false;

    public array $specs = [];

    public function mount(Vehicle $vehicle): void
    {
        $this->vehicleId = $vehicle->id;
        $this->loadSpecs();
    }

    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;
    }

    public function startEditSpecs(): void
    {
        $this->editingSpecs = true;
        $this->loadSpecs();
    }

    public function cancelEditSpecs(): void
    {
        $this->editingSpecs = false;
        $this->loadSpecs();
    }

    public function saveSpecs(): void
    {
        $vehicle = Vehicle::with('engineSpecs')->findOrFail($this->vehicleId);
        $specRecord = $vehicle->engineSpecs;

        if ($specRecord) {
            $snapshot = $specRecord->toArray();
            $specRecord->update($this->specs);

            VehicleModificationLog::create([
                'vehicle_id' => $vehicle->id,
                'user_id' => auth('admin')->id(),
                'judul' => 'Update Spek Mesin',
                'specs_snapshot' => json_encode($snapshot),
                'logged_at' => now(),
            ]);
        }

        $this->editingSpecs = false;
        session()->flash('success', 'Spek mesin berhasil diperbarui.');
    }

    private function loadSpecs(): void
    {
        $spec = VehicleEngineSpec::where('vehicle_id', $this->vehicleId)->first();

        $fields = [
            'cylinder_head', 'porting_polish', 'klep_in', 'klep_ex', 'per_klep', 'noken_as',
            'cylinder_block', 'boring_size', 'piston', 'piston_ring', 'pen_piston',
            'crankshaft', 'stroke', 'big_end', 'small_end',
            'kopling', 'per_kopling',
            'karburator_injeksi', 'filter_udara', 'knalpot',
            'pengapian_type', 'cdi_ecu', 'koil', 'busi',
            'kelistrikan_acg', 'kelistrikan_aki',
            'rasio_gigi', 'gir_depan', 'gir_belakang', 'rantai',
            'catatan_tambahan',
        ];

        foreach ($fields as $field) {
            $this->specs[$field] = $spec?->$field ?? '';
        }
    }

    public function render(): View
    {
        $vehicle = Vehicle::with(['customer', 'engineSpecs'])
            ->withCount('invoices as total_servis')
            ->findOrFail($this->vehicleId);

        $modLogs = VehicleModificationLog::where('vehicle_id', $this->vehicleId)
            ->with('user')
            ->latest('logged_at')
            ->get();

        $invoices = $vehicle->invoices()->latest('tanggal')->take(20)->get();

        return view('livewire.admin.vehicles.detail', compact('vehicle', 'modLogs', 'invoices'))
            ->layout('layouts.admin', ['title' => "{$vehicle->merk} {$vehicle->model}"]);
    }
}
