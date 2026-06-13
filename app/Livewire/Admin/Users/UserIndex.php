<?php

namespace App\Livewire\Admin\Users;

use Livewire\Component;

class UserIndex extends Component
{
    public function render(): \Illuminate\View\View
    {
        return view('livewire.admin.users.index')
            ->layout('layouts.admin');
    }
}
