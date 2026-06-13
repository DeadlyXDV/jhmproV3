<?php

namespace App\Livewire\Admin\Spareparts;

use Livewire\Component;

class SparepartIndex extends Component
{
    public function render(): \Illuminate\View\View
    {
        return view('livewire.admin.spareparts.index')
            ->layout('layouts.admin');
    }
}
