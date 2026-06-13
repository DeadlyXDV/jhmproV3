<?php

namespace App\Livewire\Admin\Pos;

use Livewire\Component;

class PosPage extends Component
{
    public function render(): \Illuminate\View\View
    {
        return view('livewire.admin.pos.index')
            ->layout('layouts.admin');
    }
}
