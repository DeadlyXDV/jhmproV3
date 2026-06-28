<?php

namespace App\Livewire\Admin\Partners;

use App\Models\Partner;
use Illuminate\View\View;
use Livewire\Component;

class PartnerCreate extends Component
{
    public ?int $editingId = null;

    public string $namaBengkel = '';

    public string $contactPerson = '';

    public string $noHp = '';

    public string $alamat = '';

    public string $catatan = '';

    public function mount(?Partner $partner = null): void
    {
        if ($partner && $partner->exists) {
            $this->editingId = $partner->id;
            $this->namaBengkel = $partner->nama_bengkel;
            $this->contactPerson = $partner->contact_person;
            $this->noHp = $partner->no_hp;
            $this->alamat = $partner->alamat ?? '';
            $this->catatan = $partner->catatan ?? '';
        }
    }

    public function save(): void
    {
        $this->validate([
            'namaBengkel' => ['required', 'string', 'max:255'],
            'contactPerson' => ['required', 'string', 'max:255'],
            'noHp' => ['required', 'string', 'max:255'],
            'alamat' => ['nullable', 'string', 'max:1000'],
            'catatan' => ['nullable', 'string', 'max:1000'],
        ]);

        $data = [
            'nama_bengkel' => $this->namaBengkel,
            'contact_person' => $this->contactPerson,
            'no_hp' => $this->noHp,
            'alamat' => $this->alamat ?: null,
            'catatan' => $this->catatan ?: null,
        ];

        if ($this->editingId) {
            Partner::findOrFail($this->editingId)->update($data);
            session()->flash('success', 'Partner berhasil diperbarui.');
        } else {
            Partner::create($data);
            session()->flash('success', 'Partner berhasil ditambahkan.');
        }

        $this->redirect(route('admin.partners.index'), navigate: true);
    }

    public function render(): View
    {
        return view('livewire.admin.partners.create')
            ->layout('layouts.admin', ['title' => $this->editingId ? 'Edit Partner' : 'Tambah Partner']);
    }
}
