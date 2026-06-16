<?php

use App\Livewire\Website\Booking\BookingPage;
use App\Models\Booking;
use App\Models\BookingService;
use App\Models\BookingSlot;
use App\Models\Customer;
use App\Models\Service;
use App\Models\User;
use App\Models\Vehicle;
use Livewire\Livewire;

test('guest diredirect ke login saat akses halaman booking', function () {
    $this->get(route('booking'))->assertRedirect(route('login'));
});

test('customer bisa akses halaman booking', function () {
    $user = User::factory()->customer()->create(['no_hp' => '08123456789']);
    Customer::create(['user_id' => $user->id, 'nama' => $user->name, 'no_hp' => $user->no_hp, 'email' => $user->email]);

    $this->actingAs($user)->get(route('booking'))->assertOk();
});

test('customer tanpa no_hp diredirect ke profile', function () {
    $user = User::factory()->customer()->create(['no_hp' => null]);

    Livewire::actingAs($user)
        ->test(BookingPage::class)
        ->assertRedirect(route('profile.edit'));
});

test('customer tanpa kendaraan melihat form tambah kendaraan', function () {
    $user = User::factory()->customer()->create(['no_hp' => '08123456789']);
    Customer::create(['user_id' => $user->id, 'nama' => $user->name, 'no_hp' => $user->no_hp, 'email' => $user->email]);

    Livewire::actingAs($user)
        ->test(BookingPage::class)
        ->assertSet('step', 1)
        ->assertSee('Tambah Kendaraan Baru');
});

test('customer bisa tambah kendaraan inline', function () {
    $user = User::factory()->customer()->create(['no_hp' => '08123456789']);
    $customer = Customer::create(['user_id' => $user->id, 'nama' => $user->name, 'no_hp' => $user->no_hp]);

    Livewire::actingAs($user)
        ->test(BookingPage::class)
        ->set('showVehicleForm', true)
        ->set('vMerk', 'Honda')
        ->set('vModel', 'Beat')
        ->set('vTahun', '2020')
        ->set('vNoPolisi', 'B 1234 ABC')
        ->call('saveVehicle')
        ->assertHasNoErrors();

    expect(Vehicle::where('customer_id', $customer->id)->count())->toBe(1);
    expect(Vehicle::first()->merk)->toBe('Honda');
});

test('customer bisa memilih kendaraan dan lanjut ke step 2', function () {
    $user = User::factory()->customer()->create(['no_hp' => '08123456789']);
    $customer = Customer::create(['user_id' => $user->id, 'nama' => $user->name, 'no_hp' => $user->no_hp]);
    $vehicle = Vehicle::create(['customer_id' => $customer->id, 'merk' => 'Honda', 'model' => 'Beat', 'tahun' => 2020, 'no_polisi' => 'B 1234 ABC']);

    Livewire::actingAs($user)
        ->test(BookingPage::class)
        ->call('selectVehicle', $vehicle->id)
        ->call('nextStep')
        ->assertSet('step', 2)
        ->assertHasNoErrors();
});

test('booking berhasil dibuat dengan race condition protection', function () {
    $user = User::factory()->customer()->create(['no_hp' => '08123456789']);
    $customer = Customer::create(['user_id' => $user->id, 'nama' => $user->name, 'no_hp' => $user->no_hp]);
    $vehicle = Vehicle::create(['customer_id' => $customer->id, 'merk' => 'Honda', 'model' => 'Beat', 'tahun' => 2020, 'no_polisi' => 'B 1234 ABC']);
    $service = Service::factory()->create(['is_bookable' => true, 'is_active' => true, 'nama_service' => 'Tune Up', 'harga_default' => 150000]);

    $tanggal = now()->addDays(3)->toDateString();
    BookingSlot::create(['tanggal' => $tanggal, 'mekanik_id' => null, 'kapasitas' => 2, 'terisi' => 0]);

    Livewire::actingAs($user)
        ->test(BookingPage::class)
        ->set('vehicleId', $vehicle->id)
        ->set('selectedServices', [
            ['id' => $service->id, 'tipe' => 'service', 'nama' => 'Tune Up', 'harga_estimasi' => 150000.0],
        ])
        ->set('tanggalBooking', $tanggal)
        ->call('submit')
        ->assertHasNoErrors()
        ->assertRedirect(route('booking.success'));

    expect(Booking::count())->toBe(1);
    expect(BookingService::count())->toBe(1);
    expect(BookingSlot::where('tanggal', $tanggal)->first()->terisi)->toBe(1);
});

test('booking gagal jika slot penuh', function () {
    $user = User::factory()->customer()->create(['no_hp' => '08123456789']);
    $customer = Customer::create(['user_id' => $user->id, 'nama' => $user->name, 'no_hp' => $user->no_hp]);
    $vehicle = Vehicle::create(['customer_id' => $customer->id, 'merk' => 'Honda', 'model' => 'Beat', 'tahun' => 2020, 'no_polisi' => 'B 1234 ABC']);
    $service = Service::factory()->create(['is_bookable' => true, 'is_active' => true, 'harga_default' => 100000]);

    $tanggal = now()->addDays(3)->toDateString();
    BookingSlot::create(['tanggal' => $tanggal, 'mekanik_id' => null, 'kapasitas' => 1, 'terisi' => 1]); // slot penuh

    Livewire::actingAs($user)
        ->test(BookingPage::class)
        ->set('vehicleId', $vehicle->id)
        ->set('step', 3)
        ->set('selectedServices', [['id' => $service->id, 'tipe' => 'service', 'nama' => $service->nama_service, 'harga_estimasi' => 100000.0]])
        ->set('tanggalBooking', $tanggal)
        ->call('submit')
        ->assertSet('step', 2)
        ->assertHasErrors(['tanggalBooking']);

    expect(Booking::count())->toBe(0);
});

test('customer tanpa customer record mendapatkan auto-create customer', function () {
    $user = User::factory()->customer()->create(['no_hp' => '08123456789']);

    Livewire::actingAs($user)->test(BookingPage::class);

    expect(Customer::where('user_id', $user->id)->count())->toBe(1);
    expect(Customer::where('user_id', $user->id)->first()->nama)->toBe($user->name);
});
