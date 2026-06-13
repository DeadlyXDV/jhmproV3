<?php

namespace App\Livewire\Admin\SparepartCategories;

use Livewire\Component;

class SparepartCategoryIndex extends Component
{
    public function render(): \Illuminate\View\View
    {
        return view('livewire.admin.sparepart-categories.index')
            ->layout('layouts.admin');
    }
}
