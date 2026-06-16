<?php

use App\Models\BookingSlot;
use App\Models\Setting;
use Illuminate\Console\Command;

test('command menghasilkan slot untuk hari operasional ke depan', function () {
    Setting::set('booking_kapasitas', '3');
    Setting::set('booking_advance_days', '7');
    Setting::set('booking_hari', json_encode([1, 2, 3, 4, 5])); // Senin–Jumat

    $this->artisan('booking:generate-slots')
        ->assertExitCode(Command::SUCCESS);

    $slots = BookingSlot::whereNull('mekanik_id')
        ->where('tanggal', '>=', now()->addDay()->toDateString())
        ->where('tanggal', '<=', now()->addDays(7)->toDateString())
        ->get();

    expect($slots)->not->toBeEmpty();
    expect($slots->first()->kapasitas)->toBe(3);

    foreach ($slots as $slot) {
        expect(now()->parse($slot->tanggal)->isoWeekday())->toBeLessThanOrEqual(5);
    }
});

test('command tidak membuat slot untuk hari yang tidak operasional', function () {
    Setting::set('booking_hari', json_encode([1, 2, 3, 4, 5, 6])); // Sen–Sab

    $this->artisan('booking:generate-slots', ['--days' => 14])
        ->assertExitCode(Command::SUCCESS);

    $sundaySlots = BookingSlot::whereNull('mekanik_id')
        ->get()
        ->filter(fn ($s) => now()->parse($s->tanggal)->isoWeekday() === 7);

    expect($sundaySlots)->toBeEmpty();
});

test('command mengupdate kapasitas jika slot sudah ada', function () {
    $date = now()->addDay()->toDateString();

    BookingSlot::create([
        'tanggal' => $date,
        'mekanik_id' => null,
        'kapasitas' => 2,
        'terisi' => 1,
    ]);

    Setting::set('booking_kapasitas', '5');
    Setting::set('booking_advance_days', '2');
    Setting::set('booking_hari', json_encode([1, 2, 3, 4, 5, 6, 7]));

    $this->artisan('booking:generate-slots')
        ->assertExitCode(Command::SUCCESS);

    $slot = BookingSlot::where('tanggal', $date)->whereNull('mekanik_id')->first();
    expect($slot->kapasitas)->toBe(5);
    expect($slot->terisi)->toBe(1);
});

test('command dengan --days option override setting advance_days', function () {
    Setting::set('booking_advance_days', '30');
    Setting::set('booking_hari', json_encode([1, 2, 3, 4, 5, 6, 7]));
    Setting::set('booking_kapasitas', '2');

    $this->artisan('booking:generate-slots', ['--days' => 3])
        ->assertExitCode(Command::SUCCESS);

    // Slot untuk hari ke-3 dari sekarang harus ada (membuktikan --days 3 digunakan)
    $slotDay3 = BookingSlot::whereNull('mekanik_id')
        ->where('tanggal', now()->addDays(3)->toDateString())
        ->first();
    expect($slotDay3)->not->toBeNull();

    // Slot untuk hari ke-4 tidak boleh ada (membuktikan limit 3 hari)
    $slotDay4 = BookingSlot::whereNull('mekanik_id')
        ->where('tanggal', now()->addDays(4)->toDateString())
        ->first();
    expect($slotDay4)->toBeNull();
});
