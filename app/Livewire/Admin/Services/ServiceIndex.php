<?php

namespace App\Livewire\Admin\Services;

use App\Models\Service;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class ServiceIndex extends Component
{
    use WithPagination;

    public string $search = '';

    public bool $showForm = false;

    public ?int $editingId = null;

    public string $namaService = '';

    public string $deskripsi = '';

    public string $hargaDefault = '';

    public string $durasiEstimasi = '';

    public bool $isActive = true;

    public bool $isBookable = false;

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function openCreate(): void
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function openEdit(int $id): void
    {
        $service = Service::findOrFail($id);
        $this->editingId = $id;
        $this->namaService = $service->nama_service;
        $this->deskripsi = $service->deskripsi ?? '';
        $this->hargaDefault = (string) $service->harga_default;
        $this->durasiEstimasi = (string) ($service->durasi_estimasi ?? '');
        $this->isActive = $service->is_active;
        $this->isBookable = $service->is_bookable;
        $this->showForm = true;
    }

    public function save(): void
    {
        $this->validate([
            'namaService' => 'required|string|max:255',
            'deskripsi' => 'nullable|string|max:1000',
            'hargaDefault' => 'required|numeric|min:0',
            'durasiEstimasi' => 'nullable|integer|min:1',
        ]);

        $data = [
            'nama_service' => $this->namaService,
            'deskripsi' => $this->deskripsi ?: null,
            'harga_default' => (float) $this->hargaDefault,
            'durasi_estimasi' => $this->durasiEstimasi ? (int) $this->durasiEstimasi : null,
            'is_active' => $this->isActive,
            'is_bookable' => $this->isBookable,
        ];

        if ($this->editingId) {
            Service::findOrFail($this->editingId)->update($data);
            session()->flash('success', 'Layanan berhasil diperbarui.');
        } else {
            Service::create($data);
            session()->flash('success', 'Layanan berhasil ditambahkan.');
        }

        $this->resetForm();
    }

    public function toggleActive(int $id): void
    {
        $service = Service::findOrFail($id);
        $service->update(['is_active' => ! $service->is_active]);
    }

    public function toggleBookable(int $id): void
    {
        $service = Service::findOrFail($id);
        $service->update(['is_bookable' => ! $service->is_bookable]);
    }

    public function cancelForm(): void
    {
        $this->resetForm();
    }

    private function resetForm(): void
    {
        $this->showForm = false;
        $this->editingId = null;
        $this->namaService = '';
        $this->deskripsi = '';
        $this->hargaDefault = '';
        $this->durasiEstimasi = '';
        $this->isActive = true;
        $this->isBookable = false;
        $this->resetValidation();
    }

    public function render(): View
    {
        $services = Service::query()
            ->when($this->search, fn ($q) => $q->where('nama_service', 'like', "%{$this->search}%"))
            ->orderBy('nama_service')
            ->paginate(20);

        return view('livewire.admin.services.index', compact('services'))
            ->layout('layouts.admin', ['title' => 'Layanan']);
    }
}
