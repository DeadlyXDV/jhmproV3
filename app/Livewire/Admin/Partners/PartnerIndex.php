<?php

namespace App\Livewire\Admin\Partners;

use Livewire\Component;

class PartnerIndex extends Component
{
    public function render(): \Illuminate\View\View
    {
        return view('livewire.admin.partners.index')
            ->layout('layouts.admin');
    }
}
