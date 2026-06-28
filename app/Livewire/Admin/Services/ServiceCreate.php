<?php

namespace App\Livewire\Admin\Services;

use App\Models\Service;
use Illuminate\View\View;
use Livewire\Component;

class ServiceCreate extends Component
{
    public ?int $editingId = null;

    public string $namaService = '';

    public string $deskripsi = '';

    public string $hargaDefault = '';

    public string $durasiEstimasi = '';

    public bool $isActive = true;

    public bool $isBookable = false;

    public function mount(?Service $service = null): void
    {
        if ($service && $service->exists) {
            $this->editingId = $service->id;
            $this->namaService = $service->nama_service;
            $this->deskripsi = $service->deskripsi ?? '';
            $this->hargaDefault = (string) $service->harga_default;
            $this->durasiEstimasi = (string) ($service->durasi_estimasi ?? '');
            $this->isActive = $service->is_active;
            $this->isBookable = $service->is_bookable;
        }
    }

    public function save(): void
    {
        $this->validate([
            'namaService' => ['required', 'string', 'max:255'],
            'deskripsi' => ['nullable', 'string', 'max:1000'],
            'hargaDefault' => ['required', 'numeric', 'min:0'],
            'durasiEstimasi' => ['nullable', 'integer', 'min:1'],
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

        $this->redirect(route('admin.services.index'), navigate: true);
    }

    public function render(): View
    {
        return view('livewire.admin.services.create')
            ->layout('layouts.admin', ['title' => $this->editingId ? 'Edit Layanan' : 'Tambah Layanan']);
    }
}
