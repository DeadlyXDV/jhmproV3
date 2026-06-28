<?php

namespace App\Livewire\Admin\ProductBundles;

use App\Models\ProductBundle;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Livewire\Component;

class ProductBundleCreate extends Component
{
    public ?int $editingId = null;

    public string $nama = '';

    public string $slug = '';

    public string $harga = '';

    public string $deskripsi = '';

    public bool $isActive = true;

    public bool $isBookable = false;

    public bool $isSoldOnline = false;

    public function mount(?ProductBundle $productBundle = null): void
    {
        if ($productBundle && $productBundle->exists) {
            $this->editingId = $productBundle->id;
            $this->nama = $productBundle->nama;
            $this->slug = $productBundle->slug;
            $this->harga = (string) $productBundle->harga;
            $this->deskripsi = $productBundle->deskripsi ?? '';
            $this->isActive = $productBundle->is_active;
            $this->isBookable = $productBundle->is_bookable;
            $this->isSoldOnline = $productBundle->is_sold_online;
        }
    }

    public function updatedNama(): void
    {
        if (! $this->editingId) {
            $this->slug = Str::slug($this->nama);
        }
    }

    public function save(): void
    {
        $this->slug = Str::slug($this->slug ?: $this->nama);

        $this->validate([
            'nama' => ['required', 'string', 'max:255'],
            'slug' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-z0-9\-]+$/',
                $this->editingId
                    ? 'unique:product_bundles,slug,'.$this->editingId
                    : 'unique:product_bundles,slug',
            ],
            'harga' => ['required', 'numeric', 'min:0'],
            'deskripsi' => ['nullable', 'string', 'max:2000'],
        ]);

        $data = [
            'nama' => $this->nama,
            'slug' => $this->slug,
            'harga' => (float) $this->harga,
            'deskripsi' => $this->deskripsi ?: null,
            'is_active' => $this->isActive,
            'is_bookable' => $this->isBookable,
            'is_sold_online' => $this->isSoldOnline,
        ];

        if ($this->editingId) {
            ProductBundle::findOrFail($this->editingId)->update($data);
            session()->flash('success', 'Bundle berhasil diperbarui.');
        } else {
            ProductBundle::create($data);
            session()->flash('success', 'Bundle berhasil ditambahkan.');
        }

        $this->redirect(route('admin.product-bundles.index'), navigate: true);
    }

    public function render(): View
    {
        return view('livewire.admin.product-bundles.create')
            ->layout('layouts.admin', ['title' => $this->editingId ? 'Edit Paket' : 'Tambah Paket Baru']);
    }
}
