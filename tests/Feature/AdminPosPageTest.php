<?php

use App\Livewire\Admin\Pos\PosPage;
use App\Models\Invoice;
use App\Models\Service;
use App\Models\Sparepart;
use App\Models\StockMovement;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Livewire\Livewire;

test('guest diblokir dari halaman pos', function () {
    $this->get(route('admin.pos'))
        ->assertRedirect(route('login'));
});

test('mekanik tidak bisa akses halaman pos', function () {
    $mekanik = User::factory()->mekanik()->create();

    $this->actingAs($mekanik, 'admin')
        ->get(route('admin.pos'))
        ->assertForbidden();
});

test('admin bisa akses halaman pos', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin, 'admin')
        ->get(route('admin.pos'))
        ->assertOk();
});

test('super_admin bisa akses halaman pos', function () {
    $superAdmin = User::factory()->superAdmin()->create();

    $this->actingAs($superAdmin, 'admin')
        ->get(route('admin.pos'))
        ->assertOk();
});

test('addToCart mengambil harga dari database bukan dari client', function () {
    $admin = User::factory()->admin()->create();
    $sparepart = Sparepart::factory()->create(['harga_jual' => 75000, 'is_active' => true]);

    Livewire::actingAs($admin, 'admin')
        ->test(PosPage::class)
        ->call('addToCart', $sparepart->id, 'sparepart')
        ->assertSet('cart.0.harga', 75000.0)
        ->assertSet('cart.0.nama', $sparepart->item_name);
});

test('addToCart untuk service mengambil harga dari database', function () {
    $admin = User::factory()->admin()->create();
    $service = Service::factory()->create(['harga_default' => 150000, 'is_active' => true]);

    Livewire::actingAs($admin, 'admin')
        ->test(PosPage::class)
        ->call('addToCart', $service->id, 'service')
        ->assertSet('cart.0.harga', 150000.0)
        ->assertSet('cart.0.nama', $service->nama_service);
});

test('addToCart menolak tipe yang tidak valid', function () {
    $admin = User::factory()->admin()->create();
    $sparepart = Sparepart::factory()->create(['is_active' => true]);

    Livewire::actingAs($admin, 'admin')
        ->test(PosPage::class)
        ->call('addToCart', $sparepart->id, 'invalid_type')
        ->assertStatus(422);
});

test('addToCart menambah qty jika item sudah ada di keranjang', function () {
    $admin = User::factory()->admin()->create();
    $sparepart = Sparepart::factory()->create(['is_active' => true]);

    Livewire::actingAs($admin, 'admin')
        ->test(PosPage::class)
        ->call('addToCart', $sparepart->id, 'sparepart')
        ->call('addToCart', $sparepart->id, 'sparepart')
        ->assertSet('cart.0.qty', 2);
});

test('addToCart menolak sparepart yang tidak aktif', function () {
    $admin = User::factory()->admin()->create();
    $sparepart = Sparepart::factory()->inactive()->create();

    expect(fn () => Livewire::actingAs($admin, 'admin')
        ->test(PosPage::class)
        ->call('addToCart', $sparepart->id, 'sparepart')
    )->toThrow(ModelNotFoundException::class);
});

test('buatInvoice membuat invoice dan mengurangi stok sparepart', function () {
    $admin = User::factory()->admin()->create();
    $sparepart = Sparepart::factory()->create([
        'harga_jual' => 100000,
        'harga_beli' => 70000,
        'stock' => 10,
        'is_active' => true,
    ]);

    Livewire::actingAs($admin, 'admin')
        ->test(PosPage::class)
        ->call('addToCart', $sparepart->id, 'sparepart')
        ->set('metodePembayaran', 'tunai')
        ->set('jumlahBayar', '100000')
        ->call('buatInvoice');

    expect(Invoice::count())->toBe(1);
    expect($sparepart->fresh()->stock)->toBe(9);
    expect(StockMovement::where('type', 'out')->count())->toBe(1);
});

test('buatInvoice menggunakan harga canonical dari DB bukan dari cart yang dimanipulasi', function () {
    $admin = User::factory()->admin()->create();
    $sparepart = Sparepart::factory()->create([
        'harga_jual' => 100000,
        'harga_beli' => 70000,
        'stock' => 10,
        'is_active' => true,
    ]);

    $component = Livewire::actingAs($admin, 'admin')
        ->test(PosPage::class)
        ->call('addToCart', $sparepart->id, 'sparepart');

    // Simulasi manipulasi harga di cart
    $cart = $component->get('cart');
    $cart[0]['harga'] = 1;
    $component->set('cart', $cart)->call('buatInvoice');

    // Invoice harus memakai harga dari DB (100000), bukan harga manipulasi (1)
    expect((float) Invoice::first()->grand_total)->toBe(100000.0);
});

test('buatInvoice tidak membuat invoice jika keranjang kosong', function () {
    $admin = User::factory()->admin()->create();

    Livewire::actingAs($admin, 'admin')
        ->test(PosPage::class)
        ->call('buatInvoice');

    expect(Invoice::count())->toBe(0);
});
