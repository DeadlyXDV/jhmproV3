<?php

namespace App\Livewire\Admin\Bookings;

use App\Models\Booking;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class BookingIndex extends Component
{
    use WithPagination;

    public string $search = '';

    public string $filterStatus = '';

    public string $filterSource = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedFilterStatus(): void
    {
        $this->resetPage();
    }

    public function updatedFilterSource(): void
    {
        $this->resetPage();
    }

    public function confirm(int $id): void
    {
        $booking = Booking::findOrFail($id);

        abort_unless(in_array($booking->status, ['pending', 'confirmed']), 422);

        $booking->update([
            'status' => 'confirmed',
            'confirmed_at' => now(),
        ]);

        session()->flash('success', "Booking #{$booking->booking_number} dikonfirmasi.");
    }

    public function cancel(int $id): void
    {
        $booking = Booking::findOrFail($id);

        abort_unless(! in_array($booking->status, ['done', 'cancelled']), 422);

        $booking->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
        ]);

        session()->flash('success', "Booking #{$booking->booking_number} dibatalkan.");
    }

    public function createInvoice(int $id): void
    {
        $booking = Booking::findOrFail($id);

        abort_unless($booking->status === 'confirmed', 422);

        $this->redirect(route('admin.invoices.create', ['from_booking' => $id]));
    }

    public function render(): View
    {
        $bookings = Booking::query()
            ->with(['customer', 'vehicle', 'mekanik'])
            ->when($this->search, function ($q) {
                $q->where(function ($q2) {
                    $q2->where('booking_number', 'like', "%{$this->search}%")
                        ->orWhere('nama_pemesan', 'like', "%{$this->search}%")
                        ->orWhere('no_hp_pemesan', 'like', "%{$this->search}%");
                });
            })
            ->when($this->filterStatus, fn ($q) => $q->where('status', $this->filterStatus))
            ->when($this->filterSource, fn ($q) => $q->where('source', $this->filterSource))
            ->latest('tanggal_booking')
            ->paginate(15);

        $statusList = ['pending', 'confirmed', 'in_progress', 'done', 'cancelled'];
        $sourceList = ['website', 'whatsapp', 'walk_in'];

        return view('livewire.admin.bookings.index', compact('bookings', 'statusList', 'sourceList'))
            ->layout('layouts.admin', ['title' => 'Booking']);
    }
}
