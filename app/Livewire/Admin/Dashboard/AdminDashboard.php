<?php

namespace App\Livewire\Admin\Dashboard;

use Livewire\Component;

class AdminDashboard extends Component
{
    public function render(): \Illuminate\View\View
    {
        return view('livewire.admin.dashboard.index')
            ->layout('layouts.admin');
    }
}
