<?php

namespace App\Livewire\Admin\ProductBundles;

use App\Models\ProductBundle;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class ProductBundleIndex extends Component
{
    use WithPagination;

    public string $search = '';

    public bool $showForm = false;

    public ?int $editingId = null;

    public string $nama = '';

    public string $slug = '';

    public string $harga = '';

    public string $deskripsi = '';

    public bool $isActive = true;

    public bool $isBookable = false;

    public bool $isSoldOnline = false;

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedNama(): void
    {
        if (! $this->editingId) {
            $this->slug = Str::slug($this->nama);
        }
    }

    public function openCreate(): void
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function openEdit(int $id): void
    {
        $bundle = ProductBundle::findOrFail($id);
        $this->editingId = $id;
        $this->nama = $bundle->nama;
        $this->slug = $bundle->slug;
        $this->harga = (string) $bundle->harga;
        $this->deskripsi = $bundle->deskripsi ?? '';
        $this->isActive = $bundle->is_active;
        $this->isBookable = $bundle->is_bookable;
        $this->isSoldOnline = $bundle->is_sold_online;
        $this->showForm = true;
    }

    public function save(): void
    {
        $this->slug = Str::slug($this->slug ?: $this->nama);

        $this->validate([
            'nama' => 'required|string|max:255',
            'slug' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-z0-9\-]+$/',
                $this->editingId
                    ? 'unique:product_bundles,slug,'.$this->editingId
                    : 'unique:product_bundles,slug',
            ],
            'harga' => 'required|numeric|min:0',
            'deskripsi' => 'nullable|string|max:2000',
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

        $this->resetForm();
    }

    public function toggleActive(int $id): void
    {
        $bundle = ProductBundle::findOrFail($id);
        $bundle->update(['is_active' => ! $bundle->is_active]);
    }

    public function toggleBookable(int $id): void
    {
        $bundle = ProductBundle::findOrFail($id);
        $bundle->update(['is_bookable' => ! $bundle->is_bookable]);
    }

    public function toggleSoldOnline(int $id): void
    {
        $bundle = ProductBundle::findOrFail($id);
        $bundle->update(['is_sold_online' => ! $bundle->is_sold_online]);
    }

    public function cancelForm(): void
    {
        $this->resetForm();
    }

    private function resetForm(): void
    {
        $this->showForm = false;
        $this->editingId = null;
        $this->nama = '';
        $this->slug = '';
        $this->harga = '';
        $this->deskripsi = '';
        $this->isActive = true;
        $this->isBookable = false;
        $this->isSoldOnline = false;
        $this->resetValidation();
    }

    public function render(): View
    {
        $bundles = ProductBundle::query()
            ->withCount('items')
            ->when($this->search, fn ($q) => $q->where('nama', 'like', "%{$this->search}%"))
            ->orderBy('nama')
            ->paginate(20);

        return view('livewire.admin.product-bundles.index', compact('bundles'))
            ->layout('layouts.admin', ['title' => 'Paket / Bundle']);
    }
}
