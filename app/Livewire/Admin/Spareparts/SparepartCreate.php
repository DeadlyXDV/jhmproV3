<?php

namespace App\Livewire\Admin\Spareparts;

use App\Models\Sparepart;
use App\Models\SparepartCategory;
use Illuminate\View\View;
use Livewire\Component;

class SparepartCreate extends Component
{
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

    public function mount(?Sparepart $sparepart = null): void
    {
        if ($sparepart && $sparepart->exists) {
            $this->editingId = $sparepart->id;
            $this->categoryId = (string) ($sparepart->category_id ?? '');
            $this->sku = $sparepart->sku;
            $this->itemName = $sparepart->item_name;
            $this->brand = $sparepart->brand ?? '';
            $this->satuan = $sparepart->satuan;
            $this->hargaBeli = (string) $sparepart->harga_beli;
            $this->hargaJual = (string) $sparepart->harga_jual;
            $this->hargaOnline = (string) ($sparepart->harga_online ?? '');
            $this->stock = (string) $sparepart->stock;
            $this->minimumStock = (string) $sparepart->minimum_stock;
            $this->berat = (string) ($sparepart->berat ?? '');
            $this->deskripsi = $sparepart->deskripsi ?? '';
            $this->isActive = $sparepart->is_active;
            $this->isSoldOnline = $sparepart->is_sold_online;
        }
    }

    public function save(): void
    {
        $skuRule = $this->editingId
            ? "required|string|max:255|unique:spareparts,sku,{$this->editingId}"
            : 'required|string|max:255|unique:spareparts,sku';

        $this->validate([
            'sku' => $skuRule,
            'itemName' => ['required', 'string', 'max:255'],
            'categoryId' => ['nullable', 'exists:sparepart_categories,id'],
            'satuan' => ['required', 'string', 'max:50'],
            'hargaBeli' => ['required', 'numeric', 'min:0'],
            'hargaJual' => ['required', 'numeric', 'min:0'],
            'hargaOnline' => ['nullable', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'minimumStock' => ['required', 'integer', 'min:0'],
            'berat' => ['nullable', 'integer', 'min:1'],
            'deskripsi' => ['nullable', 'string', 'max:2000'],
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

        $this->redirect(route('admin.spareparts.index'), navigate: true);
    }

    public function render(): View
    {
        $categories = SparepartCategory::query()
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('livewire.admin.spareparts.create', compact('categories'))
            ->layout('layouts.admin', ['title' => $this->editingId ? 'Edit Sparepart' : 'Tambah Sparepart']);
    }
}
