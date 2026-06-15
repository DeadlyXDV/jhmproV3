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
