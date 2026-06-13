<?php

namespace App\Livewire\Admin\StockMovements;

use Livewire\Component;

class StockMovementIndex extends Component
{
    public function render(): \Illuminate\View\View
    {
        return view('livewire.admin.stock-movements.index')
            ->layout('layouts.admin');
    }
}
