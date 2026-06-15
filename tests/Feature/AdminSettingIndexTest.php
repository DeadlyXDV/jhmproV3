<?php

use App\Livewire\Admin\Settings\SettingIndex;
use App\Models\Setting;
use App\Models\User;
use Livewire\Livewire;

test('guest diblokir dari halaman pengaturan', function () {
    $this->get(route('admin.settings.index'))
        ->assertRedirect(route('login'));
});

test('admin biasa tidak bisa akses halaman pengaturan', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin, 'admin')
        ->get(route('admin.settings.index'))
        ->assertForbidden();
});

test('mekanik tidak bisa akses halaman pengaturan', function () {
    $mekanik = User::factory()->mekanik()->create();

    $this->actingAs($mekanik, 'admin')
        ->get(route('admin.settings.index'))
        ->assertForbidden();
});

test('super_admin bisa akses halaman pengaturan', function () {
    $superAdmin = User::factory()->superAdmin()->create();

    $this->actingAs($superAdmin, 'admin')
        ->get(route('admin.settings.index'))
        ->assertOk();
});

test('halaman pengaturan menampilkan 4 tab', function () {
    $superAdmin = User::factory()->superAdmin()->create();

    $this->actingAs($superAdmin, 'admin')
        ->get(route('admin.settings.index'))
        ->assertOk()
        ->assertSee('Profil Bengkel')
        ->assertSee('Manajemen User')
        ->assertSee('Config Booking')
        ->assertSee('Config RFM');
});

test('saveProfil menyimpan data bengkel ke settings', function () {
    $superAdmin = User::factory()->superAdmin()->create();

    Livewire::actingAs($superAdmin, 'admin')
        ->test(SettingIndex::class)
        ->set('namaBengkel', 'Bengkel Jaya Motor')
        ->set('noHp', '081234567890')
        ->set('email', 'bengkel@example.com')
        ->call('saveProfil')
        ->assertHasNoErrors();

    expect(Setting::get('nama_bengkel'))->toBe('Bengkel Jaya Motor');
    expect(Setting::get('no_hp'))->toBe('081234567890');
    expect(Setting::get('email'))->toBe('bengkel@example.com');
});

test('saveProfil gagal jika nama bengkel kosong', function () {
    $superAdmin = User::factory()->superAdmin()->create();

    Livewire::actingAs($superAdmin, 'admin')
        ->test(SettingIndex::class)
        ->set('namaBengkel', '')
        ->call('saveProfil')
        ->assertHasErrors(['namaBengkel']);
});

test('super_admin bisa membuat user baru lewat saveUser', function () {
    $superAdmin = User::factory()->superAdmin()->create();

    Livewire::actingAs($superAdmin, 'admin')
        ->test(SettingIndex::class)
        ->call('openUserCreate')
        ->set('userName', 'Mekanik Baru')
        ->set('userEmail', 'mekanik.baru@example.com')
        ->set('userRole', 'mekanik')
        ->set('userPassword', 'password123')
        ->call('saveUser')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('users', [
        'name' => 'Mekanik Baru',
        'email' => 'mekanik.baru@example.com',
        'role' => 'mekanik',
    ]);
});

test('saveUser gagal membuat user dengan email duplikat', function () {
    $superAdmin = User::factory()->superAdmin()->create();
    User::factory()->admin()->create(['email' => 'sudahada@example.com']);

    Livewire::actingAs($superAdmin, 'admin')
        ->test(SettingIndex::class)
        ->call('openUserCreate')
        ->set('userName', 'User Duplikat')
        ->set('userEmail', 'sudahada@example.com')
        ->set('userRole', 'admin')
        ->set('userPassword', 'password123')
        ->call('saveUser')
        ->assertHasErrors(['userEmail']);
});

test('openUserEdit diblokir untuk target super_admin', function () {
    $superAdmin = User::factory()->superAdmin()->create();
    $otherSuperAdmin = User::factory()->superAdmin()->create();

    Livewire::actingAs($superAdmin, 'admin')
        ->test(SettingIndex::class)
        ->call('openUserEdit', $otherSuperAdmin->id)
        ->assertForbidden();
});

test('toggleUserActive menonaktifkan user yang aktif', function () {
    $superAdmin = User::factory()->superAdmin()->create();
    $admin = User::factory()->admin()->create(['is_active' => true]);

    Livewire::actingAs($superAdmin, 'admin')
        ->test(SettingIndex::class)
        ->call('toggleUserActive', $admin->id);

    expect($admin->fresh()->is_active)->toBeFalse();
});

test('saveBookingConfig menyimpan konfigurasi booking', function () {
    $superAdmin = User::factory()->superAdmin()->create();

    Livewire::actingAs($superAdmin, 'admin')
        ->test(SettingIndex::class)
        ->set('activeTab', 'booking')
        ->set('bookingKapasitas', 4)
        ->set('bookingAdvanceDays', 14)
        ->call('saveBookingConfig')
        ->assertHasNoErrors();

    expect(Setting::get('booking_kapasitas'))->toBe('4');
    expect(Setting::get('booking_advance_days'))->toBe('14');
});
