<?php

namespace App\Livewire\Admin\Spareparts;

use App\Models\Sparepart;
use App\Models\SparepartCategory;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class SparepartIndex extends Component
{
    use WithPagination;

    public string $search = '';

    public string $filterCategory = '';

    public bool $filterCritical = false;

    public bool $showForm = false;

    public ?int $editingId = null;

    public string $categoryId = '';

    public string $sku = '';

    public string $itemName = '';

    public string $brand = '';

    public string $satuan = '';

    public string $hargaBeli = '';

    public string $hargaJual = '';

    public string $hargaOnline = '';

    public string $stock = '0';

    public string $minimumStock = '0';

    public string $berat = '';

    public string $deskripsi = '';

    public bool $isActive = true;

    public bool $isSoldOnline = false;

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedFilterCategory(): void
    {
        $this->resetPage();
    }

    public function updatedFilterCritical(): void
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
        $part = Sparepart::findOrFail($id);
        $this->editingId = $id;
        $this->categoryId = (string) ($part->category_id ?? '');
        $this->sku = $part->sku;
        $this->itemName = $part->item_name;
        $this->brand = $part->brand ?? '';
        $this->satuan = $part->satuan;
        $this->hargaBeli = (string) $part->harga_beli;
        $this->hargaJual = (string) $part->harga_jual;
        $this->hargaOnline = (string) ($part->harga_online ?? '');
        $this->stock = (string) $part->stock;
        $this->minimumStock = (string) $part->minimum_stock;
        $this->berat = (string) ($part->berat ?? '');
        $this->deskripsi = $part->deskripsi ?? '';
        $this->isActive = $part->is_active;
        $this->isSoldOnline = $part->is_sold_online;
        $this->showForm = true;
    }

    public function save(): void
    {
        $skuRule = $this->editingId
            ? "required|string|max:255|unique:spareparts,sku,{$this->editingId}"
            : 'required|string|max:255|unique:spareparts,sku';

        $this->validate([
            'sku' => $skuRule,
            'itemName' => 'required|string|max:255',
            'categoryId' => 'nullable|exists:sparepart_categories,id',
            'satuan' => 'required|string|max:50',
            'hargaBeli' => 'required|numeric|min:0',
            'hargaJual' => 'required|numeric|min:0',
            'hargaOnline' => 'nullable|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'minimumStock' => 'required|integer|min:0',
            'berat' => 'nullable|integer|min:1',
            'deskripsi' => 'nullable|string|max:2000',
        ]);

        $data = [
            'category_id' => $this->categoryId ?: null,
            'sku' => strtoupper(trim($this->sku)),
            'item_name' => $this->itemName,
            'brand' => $this->brand ?: null,
            'satuan' => $this->satuan,
            'harga_beli' => (float) $this->hargaBeli,
            'harga_jual' => (float) $this->hargaJual,
            'harga_online' => $this->hargaOnline !== '' ? (float) $this->hargaOnline : null,
            'stock' => (int) $this->stock,
            'minimum_stock' => (int) $this->minimumStock,
            'berat' => $this->berat !== '' ? (int) $this->berat : null,
            'deskripsi' => $this->deskripsi ?: null,
            'is_active' => $this->isActive,
            'is_sold_online' => $this->isSoldOnline,
        ];

        if ($this->editingId) {
            Sparepart::findOrFail($this->editingId)->update($data);
            session()->flash('success', 'Sparepart berhasil diperbarui.');
        } else {
            Sparepart::create($data);
            session()->flash('success', 'Sparepart berhasil ditambahkan.');
        }

        $this->resetForm();
    }

    public function toggleActive(int $id): void
    {
        $sparepart = Sparepart::findOrFail($id);
        $sparepart->update(['is_active' => ! $sparepart->is_active]);
    }

    public function cancelForm(): void
    {
        $this->resetForm();
    }

    private function resetForm(): void
    {
        $this->showForm = false;
        $this->editingId = null;
        $this->categoryId = '';
        $this->sku = '';
        $this->itemName = '';
        $this->brand = '';
        $this->satuan = '';
        $this->hargaBeli = '';
        $this->hargaJual = '';
        $this->hargaOnline = '';
        $this->stock = '0';
        $this->minimumStock = '0';
        $this->berat = '';
        $this->deskripsi = '';
        $this->isActive = true;
        $this->isSoldOnline = false;
        $this->resetValidation();
    }

    public function render(): View
    {
        $spareparts = Sparepart::query()
            ->with('category')
            ->when($this->search, function ($q) {
                $q->where(function ($q2) {
                    $q2->where('sku', 'like', "%{$this->search}%")
                        ->orWhere('item_name', 'like', "%{$this->search}%")
                        ->orWhere('brand', 'like', "%{$this->search}%");
                });
            })
            ->when($this->filterCategory, fn ($q) => $q->where('category_id', $this->filterCategory))
            ->when($this->filterCritical, fn ($q) => $q->whereColumn('stock', '<=', 'minimum_stock'))
            ->orderBy('item_name')
            ->paginate(20);

        $categories = SparepartCategory::query()
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('livewire.admin.spareparts.index', compact('spareparts', 'categories'))
            ->layout('layouts.admin', ['title' => 'Sparepart']);
    }
}
