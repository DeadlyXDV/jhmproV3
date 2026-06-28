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

    // Form booking manual
    public bool $showBookingForm = false;

    public string $bNamaPemesan = '';

    public string $bNoHp = '';

    public string $bTanggal = '';

    public string $bJamMulai = '';

    public string $bSource = 'walk_in';

    public string $bKeluhan = '';

    public string $bCatatanAdmin = '';

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

    public function openBookingCreate(): void
    {
        $this->resetBookingForm();
        $this->bTanggal = now()->toDateString();
        $this->showBookingForm = true;
    }

    public function saveBooking(): void
    {
        $this->validate([
            'bNamaPemesan' => ['required', 'string', 'max:255'],
            'bNoHp' => ['required', 'string', 'max:50'],
            'bTanggal' => ['required', 'date', 'after_or_equal:today'],
            'bSource' => ['required', 'in:whatsapp,walk_in'],
            'bJamMulai' => ['nullable', 'date_format:H:i'],
            'bKeluhan' => ['nullable', 'string', 'max:2000'],
            'bCatatanAdmin' => ['nullable', 'string', 'max:2000'],
        ]);

        $lastNum = Booking::whereYear('created_at', now()->year)
            ->orderByDesc('id')
            ->value('booking_number');
        $next = $lastNum
            ? str_pad((int) substr($lastNum, -4) + 1, 4, '0', STR_PAD_LEFT)
            : '0001';
        $bookingNumber = 'BK-'.now()->year.'-'.$next;

        $booking = Booking::create([
            'booking_number' => $bookingNumber,
            'nama_pemesan' => $this->bNamaPemesan,
            'no_hp_pemesan' => $this->bNoHp,
            'tanggal_booking' => $this->bTanggal,
            'jam_mulai' => $this->bJamMulai ?: null,
            'source' => $this->bSource,
            'status' => 'confirmed',
            'confirmed_at' => now(),
            'keluhan' => $this->bKeluhan ?: null,
            'catatan_admin' => $this->bCatatanAdmin ?: null,
        ]);

        session()->flash('success', "Booking #{$booking->booking_number} berhasil dibuat.");
        $this->resetBookingForm();
    }

    public function cancelBookingForm(): void
    {
        $this->resetBookingForm();
    }

    private function resetBookingForm(): void
    {
        $this->showBookingForm = false;
        $this->bNamaPemesan = '';
        $this->bNoHp = '';
        $this->bTanggal = '';
        $this->bJamMulai = '';
        $this->bSource = 'walk_in';
        $this->bKeluhan = '';
        $this->bCatatanAdmin = '';
        $this->resetValidation();
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
