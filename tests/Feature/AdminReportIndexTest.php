<?php

use App\Models\Invoice;
use App\Models\User;

test('guest diblokir dari halaman laporan', function () {
    $this->get(route('admin.reports.index'))
        ->assertRedirect(route('login'));
});

test('admin biasa tidak bisa akses halaman laporan', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin, 'admin')
        ->get(route('admin.reports.index'))
        ->assertForbidden();
});

test('mekanik tidak bisa akses halaman laporan', function () {
    $mekanik = User::factory()->mekanik()->create();

    $this->actingAs($mekanik, 'admin')
        ->get(route('admin.reports.index'))
        ->assertForbidden();
});

test('super_admin bisa akses halaman laporan', function () {
    $superAdmin = User::factory()->superAdmin()->create();

    $this->actingAs($superAdmin, 'admin')
        ->get(route('admin.reports.index'))
        ->assertOk();
});

test('halaman laporan menampilkan summary cards', function () {
    $superAdmin = User::factory()->superAdmin()->create();

    $this->actingAs($superAdmin, 'admin')
        ->get(route('admin.reports.index'))
        ->assertOk()
        ->assertSee('Total Pendapatan')
        ->assertSee('Total Transaksi')
        ->assertSee('Gross Margin');
});

test('laporan menghitung total pendapatan dari invoice bulan ini', function () {
    $superAdmin = User::factory()->superAdmin()->create();
    Invoice::factory()->count(3)->create([
        'user_id' => $superAdmin->id,
        'tanggal' => now(),
        'grand_total' => 100000,
        'payment_status' => 'paid',
        'amount_paid' => 100000,
    ]);

    $this->actingAs($superAdmin, 'admin')
        ->get(route('admin.reports.index'))
        ->assertOk()
        ->assertSee('300.000');
});
