<?php

namespace App\Livewire\Mekanik\Dashboard;

use Livewire\Component;

class MekanikDashboard extends Component
{
    public function render(): \Illuminate\View\View
    {
        return view('livewire.mekanik.dashboard.index')
            ->layout('layouts.mekanik');
    }
}
