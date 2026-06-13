<?php

namespace App\Livewire\Admin\Customers;

use App\Models\Customer;
use Illuminate\View\View;
use Livewire\Component;

class CustomerCreate extends Component
{
    public ?int $customerId = null;

    public string $nama = '';

    public string $no_hp = '';

    public string $email = '';

    public string $alamat = '';

    public string $catatan = '';

    public function mount(?Customer $customer = null): void
    {
        if ($customer && $customer->exists) {
            $this->customerId = $customer->id;
            $this->nama = $customer->nama;
            $this->no_hp = $customer->no_hp ?? '';
            $this->email = $customer->email ?? '';
            $this->alamat = $customer->alamat ?? '';
            $this->catatan = $customer->catatan ?? '';
        }
    }

    public function save(): void
    {
        $validated = $this->validate([
            'nama' => ['required', 'string', 'max:255'],
            'no_hp' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'alamat' => ['nullable', 'string'],
            'catatan' => ['nullable', 'string'],
        ]);

        if ($this->customerId) {
            Customer::findOrFail($this->customerId)->update($validated);
            session()->flash('success', 'Data pelanggan berhasil diperbarui.');
        } else {
            Customer::create($validated);
            session()->flash('success', 'Pelanggan baru berhasil ditambahkan.');
        }

        $this->redirect(route('admin.customers.index'));
    }

    public function render(): View
    {
        $isEdit = (bool) $this->customerId;

        return view('livewire.admin.customers.create', compact('isEdit'))
            ->layout('layouts.admin', ['title' => $isEdit ? 'Edit Pelanggan' : 'Tambah Pelanggan']);
    }
}
