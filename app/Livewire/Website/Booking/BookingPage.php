<?php

namespace App\Livewire\Website\Booking;

use App\Models\Booking;
use App\Models\BookingService;
use App\Models\BookingSlot;
use App\Models\Customer;
use App\Models\Service;
use App\Models\Setting;
use App\Models\Vehicle;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;

class BookingPage extends Component
{
    public int $step = 1;

    // Step 1 — Pilih Kendaraan
    public ?int $vehicleId = null;

    // Inline vehicle form (shown when no vehicles exist)
    public bool $showVehicleForm = false;

    public string $vMerk = '';

    public string $vModel = '';

    public string $vTipe = '';

    public string $vTahun = '';

    public string $vNoPolisi = '';

    // Step 2 — Pilih Service & Jadwal
    /** @var array<int, array{id: int, tipe: string, nama: string, harga_estimasi: float}> */
    public array $selectedServices = [];

    public string $tanggalBooking = '';

    public string $keluhan = '';

    // Calendar navigation
    public int $calendarYear;

    public int $calendarMonth;

    public function mount(): void
    {
        $this->calendarYear = now()->year;
        $this->calendarMonth = now()->month;

        $user = auth('web')->user();

        if (! $user->no_hp) {
            session()->flash('error', 'Lengkapi nomor HP di profil terlebih dahulu sebelum booking.');
            $this->redirect(route('profile.edit'));

            return;
        }

        // Ensure customer record exists and is linked to this user
        if (! $user->customer) {
            Customer::create([
                'user_id' => $user->id,
                'nama' => $user->name,
                'no_hp' => $user->no_hp,
                'email' => $user->email,
            ]);
        }
    }

    #[Computed]
    public function customer(): ?Customer
    {
        return auth('web')->user()?->customer;
    }

    /** @return Collection<int, Vehicle> */
    #[Computed]
    public function vehicles(): Collection
    {
        return $this->customer?->vehicles()->orderBy('merk')->get() ?? collect();
    }

    /** @return Collection<int, Service> */
    #[Computed]
    public function bookableServices(): Collection
    {
        return Service::where('is_bookable', true)
            ->where('is_active', true)
            ->orderBy('nama_service')
            ->get();
    }

    /** @return array<int, array<int, array{date: string, day: int, isCurrentMonth: bool, isPast: bool, isTooFar: bool, isOperational: bool, isAvailable: bool, isFull: bool, isBlocked: bool, isSelected: bool}>> */
    #[Computed]
    public function calendarDays(): array
    {
        $advanceDays = (int) Setting::get('booking_advance_days', 30);
        $hariOperasional = json_decode(
            Setting::get('booking_hari', json_encode([1, 2, 3, 4, 5, 6])),
            true
        );

        $firstOfMonth = Carbon::create($this->calendarYear, $this->calendarMonth, 1)->startOfDay();
        $lastOfMonth = $firstOfMonth->copy()->endOfMonth()->startOfDay();
        $today = now()->startOfDay();
        $maxDate = $today->copy()->addDays($advanceDays)->startOfDay();

        $slots = BookingSlot::whereNull('mekanik_id')
            ->whereBetween('tanggal', [$firstOfMonth->toDateString(), $lastOfMonth->toDateString()])
            ->get()
            ->keyBy('tanggal');

        $startOfGrid = $firstOfMonth->copy()->startOfWeek(Carbon::MONDAY);
        $endOfGrid = $lastOfMonth->copy()->endOfWeek(Carbon::SUNDAY);

        $weeks = [];
        $current = $startOfGrid->copy();

        while ($current->lte($endOfGrid)) {
            $week = [];
            for ($i = 0; $i < 7; $i++) {
                $dateStr = $current->toDateString();
                $isoDay = $current->isoWeekday();
                $slot = $slots->get($dateStr);

                $week[] = [
                    'date' => $dateStr,
                    'day' => $current->day,
                    'isCurrentMonth' => $current->month === $this->calendarMonth,
                    'isPast' => $current->lt($today),
                    'isTooFar' => $current->gt($maxDate),
                    'isOperational' => in_array($isoDay, $hariOperasional),
                    'isAvailable' => $slot !== null && ! $slot->is_blocked && $slot->terisi < $slot->kapasitas,
                    'isFull' => $slot !== null && ! $slot->is_blocked && $slot->terisi >= $slot->kapasitas,
                    'isBlocked' => $slot !== null && $slot->is_blocked,
                    'isSelected' => $this->tanggalBooking === $dateStr,
                ];
                $current->addDay();
            }
            $weeks[] = $week;
        }

        return $weeks;
    }

    public function previousMonth(): void
    {
        $date = Carbon::create($this->calendarYear, $this->calendarMonth, 1)->subMonth();
        // Don't go before current month
        if ($date->isBefore(now()->startOfMonth())) {
            return;
        }
        $this->calendarYear = $date->year;
        $this->calendarMonth = $date->month;
        $this->tanggalBooking = '';
        unset($this->calendarDays);
    }

    public function nextMonth(): void
    {
        $advanceDays = (int) Setting::get('booking_advance_days', 30);
        $maxDate = now()->addDays($advanceDays);
        $date = Carbon::create($this->calendarYear, $this->calendarMonth, 1)->addMonth();

        if ($date->isAfter($maxDate->endOfMonth())) {
            return;
        }
        $this->calendarYear = $date->year;
        $this->calendarMonth = $date->month;
        $this->tanggalBooking = '';
        unset($this->calendarDays);
    }

    public function selectVehicle(int $id): void
    {
        // Ensure the vehicle belongs to this customer before accepting it
        $vehicle = Vehicle::where('id', $id)
            ->where('customer_id', $this->customer->id)
            ->firstOrFail();

        $this->vehicleId = $vehicle->id;
    }

    public function saveVehicle(): void
    {
        $this->validate([
            'vMerk' => 'required|string|max:100',
            'vModel' => 'required|string|max:100',
            'vTipe' => 'nullable|string|max:100',
            'vTahun' => 'required|integer|min:1900|max:'.(now()->year + 1),
            'vNoPolisi' => 'required|string|max:20',
        ]);

        $vehicle = Vehicle::create([
            'customer_id' => $this->customer->id,
            'merk' => $this->vMerk,
            'model' => $this->vModel,
            'tipe' => $this->vTipe ?: null,
            'tahun' => (int) $this->vTahun,
            'no_polisi' => strtoupper($this->vNoPolisi),
        ]);

        $this->vehicleId = $vehicle->id;
        $this->showVehicleForm = false;
        $this->vMerk = $this->vModel = $this->vTipe = $this->vTahun = $this->vNoPolisi = '';
        unset($this->vehicles);
    }

    public function toggleService(int $id, string $tipe, string $nama, float $hargaEstimasi): void
    {
        $key = $this->serviceKey($id, $tipe);

        if (isset($this->selectedServices[$key])) {
            unset($this->selectedServices[$key]);
        } else {
            $this->selectedServices[$key] = [
                'id' => $id,
                'tipe' => $tipe,
                'nama' => $nama,
                'harga_estimasi' => $hargaEstimasi,
            ];
        }

        $this->selectedServices = array_values($this->selectedServices);
    }

    public function isServiceSelected(int $id, string $tipe): bool
    {
        foreach ($this->selectedServices as $svc) {
            if ($svc['id'] === $id && $svc['tipe'] === $tipe) {
                return true;
            }
        }

        return false;
    }

    public function selectDate(string $date): void
    {
        $this->tanggalBooking = $date;
        unset($this->calendarDays);
    }

    public function nextStep(): void
    {
        if ($this->step === 1) {
            $this->validate([
                'vehicleId' => [
                    'required', 'integer',
                    Rule::exists('vehicles', 'id')->where('customer_id', $this->customer->id),
                ],
            ]);
        } elseif ($this->step === 2) {
            $this->validate([
                'selectedServices' => 'required|array|min:1',
                'tanggalBooking' => 'required|date|after:today',
            ], [
                'selectedServices.required' => 'Pilih minimal satu layanan.',
                'selectedServices.min' => 'Pilih minimal satu layanan.',
                'tanggalBooking.required' => 'Pilih tanggal booking.',
                'tanggalBooking.after' => 'Tanggal booking harus setelah hari ini.',
            ]);
        }

        $this->step++;
    }

    public function prevStep(): void
    {
        $this->step = max(1, $this->step - 1);
    }

    public function submit(): void
    {
        $this->validate([
            'vehicleId' => [
                'required', 'integer',
                Rule::exists('vehicles', 'id')->where('customer_id', $this->customer->id),
            ],
            'selectedServices' => 'required|array|min:1',
            'tanggalBooking' => 'required|date|after:today',
            'keluhan' => 'nullable|string|max:2000',
        ]);

        $customer = $this->customer;
        $user = auth('web')->user();

        try {
            $booking = DB::transaction(function () use ($customer, $user) {
                $slot = BookingSlot::whereNull('mekanik_id')
                    ->where('tanggal', $this->tanggalBooking)
                    ->lockForUpdate()
                    ->first();

                if (! $slot || $slot->is_blocked || $slot->terisi >= $slot->kapasitas) {
                    throw new \RuntimeException('Slot tanggal ini sudah penuh atau tidak tersedia, pilih tanggal lain.');
                }

                $lastNum = Booking::whereYear('created_at', now()->year)
                    ->orderByDesc('id')
                    ->value('booking_number');
                $next = $lastNum
                    ? str_pad((int) substr($lastNum, -4) + 1, 4, '0', STR_PAD_LEFT)
                    : '0001';
                $bookingNumber = 'BK-'.now()->year.'-'.$next;

                $booking = Booking::create([
                    'customer_id' => $customer->id,
                    'vehicle_id' => $this->vehicleId,
                    'booking_number' => $bookingNumber,
                    'tanggal_booking' => $this->tanggalBooking,
                    'status' => 'pending',
                    'source' => 'website',
                    'keluhan' => $this->keluhan ?: null,
                    'nama_pemesan' => $user->name,
                    'no_hp_pemesan' => $user->no_hp ?? $customer->no_hp,
                ]);

                foreach ($this->selectedServices as $svc) {
                    BookingService::create([
                        'booking_id' => $booking->id,
                        'service_id' => $svc['tipe'] === 'service' ? $svc['id'] : null,
                        'bundle_id' => $svc['tipe'] === 'bundle' ? $svc['id'] : null,
                        'type' => $svc['tipe'],
                        'nama_snapshot' => $svc['nama'],
                        'harga_estimasi' => $svc['harga_estimasi'],
                    ]);
                }

                $slot->increment('terisi');

                return $booking;
            });

            session([
                'booking_success' => [
                    'booking_number' => $booking->booking_number,
                    'tanggal' => Carbon::parse($this->tanggalBooking)->translatedFormat('d M Y'),
                    'services' => array_column($this->selectedServices, 'nama'),
                ],
            ]);

            $this->redirect(route('booking.success'));

        } catch (\RuntimeException $e) {
            $this->addError('tanggalBooking', $e->getMessage());
            $this->step = 2;
        }
    }

    private function serviceKey(int $id, string $tipe): string
    {
        return $tipe.'_'.$id;
    }

    public function render(): View
    {
        return view('livewire.website.booking.booking-page')
            ->layout('layouts.app', ['title' => 'Booking Online']);
    }
}
