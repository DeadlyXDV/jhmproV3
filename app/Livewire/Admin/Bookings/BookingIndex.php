<?php

namespace App\Livewire\Admin\Bookings;

use Livewire\Component;

class BookingIndex extends Component
{
    public function render(): \Illuminate\View\View
    {
        return view('livewire.admin.bookings.index')
            ->layout('layouts.admin');
    }
}
