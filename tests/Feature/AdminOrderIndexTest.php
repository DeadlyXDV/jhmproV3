<?php

use App\Livewire\Admin\Orders\OrderIndex;
use App\Models\Order;
use App\Models\User;
use Livewire\Livewire;

test('guest diblokir dari halaman orders', function () {
    $this->get(route('admin.orders.index'))
        ->assertRedirect(route('login'));
});

test('mekanik tidak bisa akses halaman orders', function () {
    $mekanik = User::factory()->mekanik()->create();

    $this->actingAs($mekanik, 'admin')
        ->get(route('admin.orders.index'))
        ->assertForbidden();
});

test('admin bisa akses halaman orders', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin, 'admin')
        ->get(route('admin.orders.index'))
        ->assertOk();
});

test('super_admin bisa akses halaman orders', function () {
    $superAdmin = User::factory()->superAdmin()->create();

    $this->actingAs($superAdmin, 'admin')
        ->get(route('admin.orders.index'))
        ->assertOk();
});

test('halaman orders menampilkan daftar orders', function () {
    $admin = User::factory()->admin()->create();
    Order::factory()->count(3)->create();

    $this->actingAs($admin, 'admin')
        ->get(route('admin.orders.index'))
        ->assertOk()
        ->assertSee('ORD-');
});

test('halaman orders bisa filter berdasarkan payment status', function () {
    $admin = User::factory()->admin()->create();
    Order::factory()->paid()->create(['order_number' => 'ORD-LUNAS']);
    Order::factory()->pending()->create(['order_number' => 'ORD-PENDING']);

    Livewire::actingAs($admin, 'admin')
        ->test(OrderIndex::class)
        ->set('filterPayment', 'paid')
        ->assertSee('ORD-LUNAS')
        ->assertDontSee('ORD-PENDING');
});

test('halaman orders bisa search berdasarkan order number', function () {
    $admin = User::factory()->admin()->create();
    Order::factory()->create(['order_number' => 'ORD-CARIINI', 'nama_penerima' => 'Test User']);
    Order::factory()->create(['order_number' => 'ORD-LAIN', 'nama_penerima' => 'Other User']);

    Livewire::actingAs($admin, 'admin')
        ->test(OrderIndex::class)
        ->set('search', 'CARIINI')
        ->assertSee('ORD-CARIINI')
        ->assertDontSee('ORD-LAIN');
});
