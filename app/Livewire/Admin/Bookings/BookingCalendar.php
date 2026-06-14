<?php

namespace App\Livewire\Admin\Bookings;

use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\View\View;
use Livewire\Component;

class BookingCalendar extends Component
{
    public int $year;

    public int $month;

    public function mount(): void
    {
        $this->year = now()->year;
        $this->month = now()->month;
    }

    public function previousMonth(): void
    {
        $date = Carbon::create($this->year, $this->month, 1)->subMonth();
        $this->year = $date->year;
        $this->month = $date->month;
    }

    public function nextMonth(): void
    {
        $date = Carbon::create($this->year, $this->month, 1)->addMonth();
        $this->year = $date->year;
        $this->month = $date->month;
    }

    public function render(): View
    {
        $startOfMonth = Carbon::create($this->year, $this->month, 1)->startOfMonth();
        $endOfMonth = $startOfMonth->copy()->endOfMonth();

        $bookings = Booking::query()
            ->with(['customer', 'vehicle'])
            ->whereBetween('tanggal_booking', [$startOfMonth, $endOfMonth])
            ->orderBy('tanggal_booking')
            ->orderBy('jam_mulai')
            ->get()
            ->groupBy(fn ($b) => $b->tanggal_booking->format('Y-m-d'));

        $calendarStart = $startOfMonth->copy()->startOfWeek(Carbon::MONDAY);
        $calendarEnd = $endOfMonth->copy()->endOfWeek(Carbon::SUNDAY);

        $weeks = [];
        $current = $calendarStart->copy();

        while ($current->lte($calendarEnd)) {
            $week = [];
            for ($i = 0; $i < 7; $i++) {
                $key = $current->format('Y-m-d');
                $week[] = [
                    'date' => $current->copy(),
                    'inMonth' => $current->month === $this->month,
                    'isToday' => $current->isToday(),
                    'bookings' => $bookings->get($key, collect()),
                ];
                $current->addDay();
            }
            $weeks[] = $week;
        }

        $monthLabel = Carbon::create($this->year, $this->month, 1)->translatedFormat('F Y');

        return view('livewire.admin.bookings.calendar', compact('weeks', 'monthLabel'))
            ->layout('layouts.admin', ['title' => 'Kalender Booking']);
    }
}
