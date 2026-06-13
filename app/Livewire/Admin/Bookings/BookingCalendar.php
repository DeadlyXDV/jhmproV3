<?php

namespace App\Livewire\Admin\Bookings;

use Livewire\Component;

class BookingCalendar extends Component
{
    public function render(): \Illuminate\View\View
    {
        return view('livewire.admin.bookings.calendar')
            ->layout('layouts.admin');
    }
}
