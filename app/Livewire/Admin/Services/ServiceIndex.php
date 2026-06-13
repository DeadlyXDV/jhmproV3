<?php

namespace App\Livewire\Admin\Services;

use Livewire\Component;

class ServiceIndex extends Component
{
    public function render(): \Illuminate\View\View
    {
        return view('livewire.admin.services.index')
            ->layout('layouts.admin');
    }
}
