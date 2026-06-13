<?php

namespace App\Livewire\Admin\Settings;

use Livewire\Component;

class SettingIndex extends Component
{
    public function render(): \Illuminate\View\View
    {
        return view('livewire.admin.settings.index')
            ->layout('layouts.admin');
    }
}
