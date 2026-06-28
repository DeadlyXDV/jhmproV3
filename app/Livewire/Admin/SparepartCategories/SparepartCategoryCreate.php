<?php

namespace App\Livewire\Admin\SparepartCategories;

use App\Models\SparepartCategory;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Livewire\Component;

class SparepartCategoryCreate extends Component
{
    public ?int $editingId = null;

    public string $name = '';

    public string $slug = '';

    public ?int $parentId = null;

    public string $description = '';

    public function mount(?SparepartCategory $sparepartCategory = null): void
    {
        if ($sparepartCategory && $sparepartCategory->exists) {
            $this->editingId = $sparepartCategory->id;
            $this->name = $sparepartCategory->name;
            $this->slug = $sparepartCategory->slug;
            $this->parentId = $sparepartCategory->parent_id;
            $this->description = $sparepartCategory->description ?? '';
        }
    }

    public function updatedName(): void
    {
        if (! $this->editingId) {
            $this->slug = Str::slug($this->name);
        }
    }

    public function save(): void
    {
        $this->slug = Str::slug($this->slug ?: $this->name);

        $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-z0-9\-]+$/',
                $this->editingId
                    ? 'unique:sparepart_categories,slug,'.$this->editingId
                    : 'unique:sparepart_categories,slug',
            ],
            'parentId' => ['nullable', 'integer', 'exists:sparepart_categories,id'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        $data = [
            'name' => $this->name,
            'slug' => $this->slug,
            'parent_id' => $this->parentId ?: null,
            'description' => $this->description ?: null,
        ];

        if ($this->editingId) {
            SparepartCategory::findOrFail($this->editingId)->update($data);
            session()->flash('success', 'Kategori berhasil diperbarui.');
        } else {
            SparepartCategory::create($data);
            session()->flash('success', 'Kategori berhasil ditambahkan.');
        }

        $this->redirect(route('admin.sparepart-categories.index'), navigate: true);
    }

    public function render(): View
    {
        $allCategories = SparepartCategory::query()
            ->orderBy('name')
            ->get(['id', 'name', 'parent_id']);

        return view('livewire.admin.sparepart-categories.create', compact('allCategories'))
            ->layout('layouts.admin', ['title' => $this->editingId ? 'Edit Kategori' : 'Tambah Kategori']);
    }
}
