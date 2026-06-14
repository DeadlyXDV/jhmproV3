<?php

namespace App\Livewire\Admin\SparepartCategories;

use App\Models\SparepartCategory;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class SparepartCategoryIndex extends Component
{
    use WithPagination;

    public string $search = '';

    public bool $showForm = false;

    public ?int $editingId = null;

    public string $name = '';

    public string $slug = '';

    public ?int $parentId = null;

    public string $description = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedName(): void
    {
        if (! $this->editingId) {
            $this->slug = Str::slug($this->name);
        }
    }

    public function openCreate(): void
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function openEdit(int $id): void
    {
        $category = SparepartCategory::findOrFail($id);
        $this->editingId = $id;
        $this->name = $category->name;
        $this->slug = $category->slug;
        $this->parentId = $category->parent_id;
        $this->description = $category->description ?? '';
        $this->showForm = true;
    }

    public function save(): void
    {
        $this->slug = Str::slug($this->slug ?: $this->name);

        $this->validate([
            'name' => 'required|string|max:255',
            'slug' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-z0-9\-]+$/',
                $this->editingId
                    ? 'unique:sparepart_categories,slug,'.$this->editingId
                    : 'unique:sparepart_categories,slug',
            ],
            'parentId' => 'nullable|integer|exists:sparepart_categories,id',
            'description' => 'nullable|string|max:1000',
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
        $this->name = '';
        $this->slug = '';
        $this->parentId = null;
        $this->description = '';
        $this->resetValidation();
    }

    public function render(): View
    {
        $categories = SparepartCategory::query()
            ->with('parent')
            ->withCount('spareparts')
            ->when($this->search, fn ($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->orderBy('name')
            ->paginate(20);

        $allCategories = SparepartCategory::query()
            ->orderBy('name')
            ->get(['id', 'name', 'parent_id']);

        return view('livewire.admin.sparepart-categories.index', compact('categories', 'allCategories'))
            ->layout('layouts.admin', ['title' => 'Kategori Sparepart']);
    }
}
