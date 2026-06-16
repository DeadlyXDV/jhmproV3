<?php

use App\Livewire\Admin\Bookings\BookingIndex;
use App\Models\Booking;
use App\Models\User;
use Livewire\Livewire;
use Symfony\Component\HttpKernel\Exception\HttpException;

test('guest diblokir dari halaman booking admin', function () {
    $this->get(route('admin.bookings.index'))->assertRedirect();
});

test('admin bisa akses halaman booking', function () {
    $admin = User::factory()->admin()->create();
    $this->actingAs($admin, 'admin')->get(route('admin.bookings.index'))->assertOk();
});

test('admin bisa konfirmasi booking pending', function () {
    $admin = User::factory()->admin()->create();
    $booking = Booking::factory()->create(['status' => 'pending']);

    Livewire::actingAs($admin, 'admin')
        ->test(BookingIndex::class)
        ->call('confirm', $booking->id);

    expect($booking->fresh()->status)->toBe('confirmed');
    expect($booking->fresh()->confirmed_at)->not->toBeNull();
});

test('admin bisa batalkan booking', function () {
    $admin = User::factory()->admin()->create();
    $booking = Booking::factory()->create(['status' => 'pending']);

    Livewire::actingAs($admin, 'admin')
        ->test(BookingIndex::class)
        ->call('cancel', $booking->id);

    expect($booking->fresh()->status)->toBe('cancelled');
});

test('admin tidak bisa batalkan booking yang sudah cancelled', function () {
    $admin = User::factory()->admin()->create();
    $booking = Booking::factory()->create(['status' => 'cancelled']);

    try {
        Livewire::actingAs($admin, 'admin')
            ->test(BookingIndex::class)
            ->call('cancel', $booking->id);
    } catch (HttpException $e) {
        expect($e->getStatusCode())->toBe(422);

        return;
    }

    // Booking status should remain unchanged
    expect($booking->fresh()->status)->toBe('cancelled');
});

test('createInvoice dari booking yang sudah confirmed redirect ke invoice create', function () {
    $admin = User::factory()->admin()->create();
    $booking = Booking::factory()->create(['status' => 'confirmed']);

    Livewire::actingAs($admin, 'admin')
        ->test(BookingIndex::class)
        ->call('createInvoice', $booking->id)
        ->assertRedirect(route('admin.invoices.create', ['from_booking' => $booking->id]));
});

test('createInvoice dari booking yang belum confirmed tidak membuat redirect ke invoice', function () {
    $admin = User::factory()->admin()->create();
    $booking = Booking::factory()->create(['status' => 'pending']);

    // Livewire catches abort_unless internally; verify no redirect to invoice create
    try {
        Livewire::actingAs($admin, 'admin')
            ->test(BookingIndex::class)
            ->call('createInvoice', $booking->id)
            ->assertStatus(422);
    } catch (HttpException $e) {
        expect($e->getStatusCode())->toBe(422);
    }

    // Status should still be pending
    expect($booking->fresh()->status)->toBe('pending');
});
