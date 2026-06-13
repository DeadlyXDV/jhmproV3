<?php

namespace App\Livewire\Admin\Rfm;

use Livewire\Component;

class RfmIndex extends Component
{
    public function render(): \Illuminate\View\View
    {
        return view('livewire.admin.rfm.index')
            ->layout('layouts.admin');
    }
}
