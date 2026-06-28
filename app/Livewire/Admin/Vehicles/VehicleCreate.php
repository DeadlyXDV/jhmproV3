<?php

namespace App\Livewire\Admin\Vehicles;

use App\Models\Customer;
use App\Models\Vehicle;
use Illuminate\View\View;
use Livewire\Component;

class VehicleCreate extends Component
{
    public ?int $vehicleId = null;

    public string $customerId = '';

    public string $merk = '';

    public string $model = '';

    public string $tipe = '';

    public string $tahun = '';

    public string $noPolisi = '';

    public string $noRangka = '';

    public string $noMesin = '';

    public string $warna = '';

    public string $catatan = '';

    /** @var array<int, string> */
    public array $availableMerks = [];

    /** @var array<int, string> */
    public array $availableModels = [];

    /** @var array<int, string> */
    public array $availableTipes = [];

    /** @var array<int, int|string> */
    public array $availableYears = [];

    public bool $customerLocked = false;

    public function mount(?Vehicle $vehicle = null): void
    {
        $this->availableMerks = Vehicle::whereNull('deleted_at')
            ->distinct()
            ->orderBy('merk')
            ->pluck('merk')
            ->filter()
            ->values()
            ->toArray();

        if ($vehicle && $vehicle->exists) {
            $this->vehicleId = $vehicle->id;
            $this->customerId = (string) $vehicle->customer_id;
            $this->merk = $vehicle->merk;
            $this->model = $vehicle->model;
            $this->tipe = $vehicle->tipe ?? '';
            $this->tahun = (string) $vehicle->tahun;
            $this->noPolisi = $vehicle->no_polisi;
            $this->noRangka = $vehicle->no_rangka ?? '';
            $this->noMesin = $vehicle->no_mesin ?? '';
            $this->warna = $vehicle->warna ?? '';
            $this->catatan = $vehicle->catatan ?? '';

            $this->reloadModels();
            $this->reloadTipes();
            $this->reloadYears();
        } elseif ($fromCustomerId = request()->query('customer_id')) {
            $this->customerId = (string) $fromCustomerId;
            $this->customerLocked = true;
        }
    }

    public function updatedMerk(): void
    {
        $this->model = '';
        $this->tipe = '';
        $this->tahun = '';
        $this->reloadModels();
        $this->availableTipes = [];
        $this->availableYears = [];
    }

    public function updatedModel(): void
    {
        $this->tipe = '';
        $this->tahun = '';
        $this->reloadTipes();
        $this->reloadYears();
    }

    public function updatedTipe(): void
    {
        $this->tahun = '';
        $this->reloadYears();
    }

    public function save(): void
    {
        $this->validate([
            'customerId' => ['required', 'integer', 'exists:customers,id'],
            'merk' => ['required', 'string', 'max:255'],
            'model' => ['required', 'string', 'max:255'],
            'tipe' => ['nullable', 'string', 'max:255'],
            'tahun' => ['required', 'integer', 'min:1970', 'max:'.(date('Y') + 1)],
            'noPolisi' => [
                'required',
                'string',
                'max:20',
                $this->vehicleId
                    ? 'unique:vehicles,no_polisi,'.$this->vehicleId
                    : 'unique:vehicles,no_polisi',
            ],
            'noRangka' => ['nullable', 'string', 'max:50'],
            'noMesin' => ['nullable', 'string', 'max:50'],
            'warna' => ['nullable', 'string', 'max:100'],
            'catatan' => ['nullable', 'string'],
        ]);

        $data = [
            'customer_id' => (int) $this->customerId,
            'merk' => $this->merk,
            'model' => $this->model,
            'tipe' => $this->tipe ?: null,
            'tahun' => (int) $this->tahun,
            'no_polisi' => strtoupper(preg_replace('/\s+/', '', $this->noPolisi)),
            'no_rangka' => $this->noRangka ?: null,
            'no_mesin' => $this->noMesin ?: null,
            'warna' => $this->warna ?: null,
            'catatan' => $this->catatan ?: null,
        ];

        if ($this->vehicleId) {
            Vehicle::findOrFail($this->vehicleId)->update($data);
            session()->flash('success', 'Data kendaraan berhasil diperbarui.');
            $this->redirect(route('admin.vehicles.show', $this->vehicleId), navigate: true);
        } else {
            $vehicle = Vehicle::create($data);
            session()->flash('success', 'Kendaraan berhasil ditambahkan.');

            if ($this->customerLocked) {
                $this->redirect(route('admin.customers.show', $vehicle->customer_id), navigate: true);
            } else {
                $this->redirect(route('admin.vehicles.show', $vehicle), navigate: true);
            }
        }
    }

    private function reloadModels(): void
    {
        $this->availableModels = $this->merk
            ? Vehicle::whereNull('deleted_at')
                ->where('merk', $this->merk)
                ->distinct()
                ->orderBy('model')
                ->pluck('model')
                ->toArray()
            : [];
    }

    private function reloadTipes(): void
    {
        $this->availableTipes = ($this->merk && $this->model)
            ? Vehicle::whereNull('deleted_at')
                ->where('merk', $this->merk)
                ->where('model', $this->model)
                ->whereNotNull('tipe')
                ->distinct()
                ->orderBy('tipe')
                ->pluck('tipe')
                ->toArray()
            : [];
    }

    private function reloadYears(): void
    {
        if (! $this->merk || ! $this->model) {
            $this->availableYears = [];

            return;
        }

        $this->availableYears = Vehicle::whereNull('deleted_at')
            ->where('merk', $this->merk)
            ->where('model', $this->model)
            ->when($this->tipe, fn ($q) => $q->where('tipe', $this->tipe))
            ->distinct()
            ->orderByDesc('tahun')
            ->pluck('tahun')
            ->toArray();
    }

    public function render(): View
    {
        $customers = Customer::query()
            ->orderBy('nama')
            ->get(['id', 'nama', 'no_hp']);

        $isEdit = (bool) $this->vehicleId;

        return view('livewire.admin.vehicles.create', compact('customers', 'isEdit'))
            ->layout('layouts.admin', ['title' => $isEdit ? 'Edit Kendaraan' : 'Tambah Kendaraan']);
    }
}
