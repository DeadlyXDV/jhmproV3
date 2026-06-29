<?php

use App\Models\User;

test('guest diblokir dari halaman rfm', function () {
    $this->get(route('admin.rfm.index'))
        ->assertRedirect(route('login'));
});

test('admin biasa tidak bisa akses halaman rfm', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin, 'admin')
        ->get(route('admin.rfm.index'))
        ->assertForbidden();
});

test('mekanik tidak bisa akses halaman rfm', function () {
    $mekanik = User::factory()->mekanik()->create();

    $this->actingAs($mekanik, 'admin')
        ->get(route('admin.rfm.index'))
        ->assertForbidden();
});

test('super_admin bisa akses halaman rfm', function () {
    $superAdmin = User::factory()->superAdmin()->create();

    $this->actingAs($superAdmin, 'admin')
        ->get(route('admin.rfm.index'))
        ->assertOk();
});

test('halaman rfm menampilkan tab sumber', function () {
    $superAdmin = User::factory()->superAdmin()->create();

    $this->actingAs($superAdmin, 'admin')
        ->get(route('admin.rfm.index'))
        ->assertOk()
        ->assertSee('Bengkel')
        ->assertSee('Online Shop')
        ->assertSee('Combined');
});

test('halaman rfm menampilkan stat cards', function () {
    $superAdmin = User::factory()->superAdmin()->create();

    $this->actingAs($superAdmin, 'admin')
        ->get(route('admin.rfm.index'))
        ->assertOk()
        ->assertSee('Total Customer')
        ->assertSee('Avg. Monetary');
});

// B.3: empty state — halaman tetap 200 OK dan menampilkan pesan kosong
// ketika tabel customer_rfm benar-benar kosong (kondisi awal sebelum rfm:calculate dijalankan)
test('halaman rfm tetap 200 OK dan menampilkan empty state ketika belum ada data RFM', function () {
    $superAdmin = User::factory()->superAdmin()->create();

    // RefreshDatabase memastikan customer_rfm kosong — tidak perlu truncate manual
    $this->actingAs($superAdmin, 'admin')
        ->get(route('admin.rfm.index'))
        ->assertOk()
        ->assertSee('Belum ada data RFM untuk sumber ini');
});
