<?php

namespace App\Livewire\Admin\Partners;

use App\Models\Partner;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class PartnerIndex extends Component
{
    use WithPagination;

    public string $search = '';

    public bool $showForm = false;

    public ?int $editingId = null;

    public string $namaBengkel = '';

    public string $contactPerson = '';

    public string $noHp = '';

    public string $alamat = '';

    public string $catatan = '';

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
        $partner = Partner::findOrFail($id);
        $this->editingId = $id;
        $this->namaBengkel = $partner->nama_bengkel;
        $this->contactPerson = $partner->contact_person;
        $this->noHp = $partner->no_hp;
        $this->alamat = $partner->alamat ?? '';
        $this->catatan = $partner->catatan ?? '';
        $this->showForm = true;
    }

    public function save(): void
    {
        $this->validate([
            'namaBengkel' => 'required|string|max:255',
            'contactPerson' => 'required|string|max:255',
            'noHp' => 'required|string|max:255',
            'alamat' => 'nullable|string|max:1000',
            'catatan' => 'nullable|string|max:1000',
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

        $this->resetForm();
    }

    public function cancelForm(): void
    {
        $this->resetForm();
    }

    private function resetForm(): void
    {
        $this->showForm = false;
        $this->editingId = null;
        $this->namaBengkel = '';
        $this->contactPerson = '';
        $this->noHp = '';
        $this->alamat = '';
        $this->catatan = '';
        $this->resetValidation();
    }

    public function render(): View
    {
        $partners = Partner::query()
            ->when($this->search, function ($q) {
                $q->where(function ($q2) {
                    $q2->where('nama_bengkel', 'like', "%{$this->search}%")
                        ->orWhere('contact_person', 'like', "%{$this->search}%");
                });
            })
            ->withCount(['invoices as total_kunjungan'])
            ->withSum(['invoices as total_transaksi' => fn ($q) => $q->where('payment_status', 'paid')], 'grand_total')
            ->latest()
            ->paginate(15);

        return view('livewire.admin.partners.index', compact('partners'))
            ->layout('layouts.admin', ['title' => 'Partner']);
    }
}
