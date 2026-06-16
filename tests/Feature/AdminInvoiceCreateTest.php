<?php

use App\Livewire\Admin\Invoices\InvoiceCreate;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Service;
use App\Models\Sparepart;
use App\Models\StockMovement;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Livewire\Livewire;

test('guest diblokir dari halaman buat invoice', function () {
    $this->get(route('admin.invoices.create'))
        ->assertRedirect(route('login'));
});

test('mekanik tidak bisa akses halaman buat invoice', function () {
    $mekanik = User::factory()->mekanik()->create();

    $this->actingAs($mekanik, 'admin')
        ->get(route('admin.invoices.create'))
        ->assertForbidden();
});

test('admin bisa akses halaman buat invoice', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin, 'admin')
        ->get(route('admin.invoices.create'))
        ->assertOk();
});

test('super_admin bisa akses halaman buat invoice', function () {
    $superAdmin = User::factory()->superAdmin()->create();

    $this->actingAs($superAdmin, 'admin')
        ->get(route('admin.invoices.create'))
        ->assertOk();
});

test('addItem mengambil nama dan harga service dari database', function () {
    $admin = User::factory()->admin()->create();
    $service = Service::factory()->create(['nama_service' => 'Servis Mesin', 'harga_default' => 150000, 'is_active' => true]);

    Livewire::actingAs($admin, 'admin')
        ->test(InvoiceCreate::class)
        ->call('addItem', $service->id, 'service')
        ->assertSet('items.0.nama', 'Servis Mesin')
        ->assertSet('items.0.harga', '150000')
        ->assertSet('items.0.qty', '1');
});

test('addItem mengambil nama dan harga sparepart dari database', function () {
    $admin = User::factory()->admin()->create();
    $sparepart = Sparepart::factory()->create(['item_name' => 'Oli Shell', 'harga_jual' => 45000, 'is_active' => true]);

    Livewire::actingAs($admin, 'admin')
        ->test(InvoiceCreate::class)
        ->call('addItem', $sparepart->id, 'sparepart')
        ->assertSet('items.0.nama', 'Oli Shell')
        ->assertSet('items.0.harga', '45000')
        ->assertSet('items.0.tipe', 'sparepart');
});

test('addItem menolak tipe yang tidak valid', function () {
    $admin = User::factory()->admin()->create();
    $service = Service::factory()->create(['is_active' => true]);

    Livewire::actingAs($admin, 'admin')
        ->test(InvoiceCreate::class)
        ->call('addItem', $service->id, 'invalid_tipe')
        ->assertStatus(422);
});

test('addItem menambah qty jika item sudah ada', function () {
    $admin = User::factory()->admin()->create();
    $service = Service::factory()->create(['is_active' => true]);

    Livewire::actingAs($admin, 'admin')
        ->test(InvoiceCreate::class)
        ->call('addItem', $service->id, 'service')
        ->call('addItem', $service->id, 'service')
        ->assertSet('items.0.qty', '2');
});

test('addItem menolak sparepart yang tidak aktif', function () {
    $admin = User::factory()->admin()->create();
    $sparepart = Sparepart::factory()->inactive()->create();

    expect(fn () => Livewire::actingAs($admin, 'admin')
        ->test(InvoiceCreate::class)
        ->call('addItem', $sparepart->id, 'sparepart')
    )->toThrow(ModelNotFoundException::class);
});

test('save gagal jika items kosong', function () {
    $admin = User::factory()->admin()->create();

    Livewire::actingAs($admin, 'admin')
        ->test(InvoiceCreate::class)
        ->call('save')
        ->assertHasErrors(['items']);

    expect(Invoice::count())->toBe(0);
});

test('save walk_in dengan service membuat invoice dengan status dan total yang benar', function () {
    $admin = User::factory()->admin()->create();
    $service = Service::factory()->create(['harga_default' => 200000, 'is_active' => true]);

    Livewire::actingAs($admin, 'admin')
        ->test(InvoiceCreate::class)
        ->call('addItem', $service->id, 'service')
        ->set('jumlahBayar', '200000')
        ->call('save');

    expect(Invoice::count())->toBe(1);
    expect(InvoiceItem::count())->toBe(1);

    $invoice = Invoice::first();
    expect($invoice->tipe)->toBe('walk_in');
    expect($invoice->payment_status)->toBe('paid');
    expect((float) $invoice->grand_total)->toBe(200000.0);
    expect($invoice->user_id)->toBe($admin->id);
});

test('save sparepart mengurangi stok dan membuat stock_movement', function () {
    $admin = User::factory()->admin()->create();
    $sparepart = Sparepart::factory()->create([
        'harga_jual' => 100000,
        'harga_beli' => 70000,
        'stock' => 10,
        'is_active' => true,
    ]);

    Livewire::actingAs($admin, 'admin')
        ->test(InvoiceCreate::class)
        ->call('addItem', $sparepart->id, 'sparepart')
        ->set('jumlahBayar', '100000')
        ->call('save');

    expect($sparepart->fresh()->stock)->toBe(9);
    expect(StockMovement::where('type', 'out')->count())->toBe(1);

    $movement = StockMovement::first();
    expect($movement->stock_before)->toBe(10);
    expect($movement->stock_after)->toBe(9);
});

test('save membuat payment record jika jumlahBayar lebih dari 0', function () {
    $admin = User::factory()->admin()->create();
    $service = Service::factory()->create(['harga_default' => 150000, 'is_active' => true]);

    Livewire::actingAs($admin, 'admin')
        ->test(InvoiceCreate::class)
        ->call('addItem', $service->id, 'service')
        ->set('jumlahBayar', '100000')
        ->set('metodePembayaran', 'transfer')
        ->call('save');

    $invoice = Invoice::first();
    expect($invoice->payments()->count())->toBe(1);
    expect((float) $invoice->payments()->first()->amount)->toBe(100000.0);
    expect($invoice->payments()->first()->payment_method)->toBe('transfer');
    expect($invoice->payment_status)->toBe('partial');
});

test('save tidak membuat payment record jika jumlahBayar adalah 0', function () {
    $admin = User::factory()->admin()->create();
    $service = Service::factory()->create(['harga_default' => 150000, 'is_active' => true]);

    Livewire::actingAs($admin, 'admin')
        ->test(InvoiceCreate::class)
        ->call('addItem', $service->id, 'service')
        ->set('jumlahBayar', '0')
        ->call('save');

    expect(Invoice::first()->payments()->count())->toBe(0);
    expect(Invoice::first()->payment_status)->toBe('unpaid');
});

test('save menghitung grand_total dengan benar setelah diskon', function () {
    $admin = User::factory()->admin()->create();
    $service = Service::factory()->create(['harga_default' => 200000, 'is_active' => true]);

    Livewire::actingAs($admin, 'admin')
        ->test(InvoiceCreate::class)
        ->call('addItem', $service->id, 'service')
        ->set('discount', '50000')
        ->set('jumlahBayar', '150000')
        ->call('save');

    $invoice = Invoice::first();
    expect((float) $invoice->subtotal)->toBe(200000.0);
    expect((float) $invoice->discount)->toBe(50000.0);
    expect((float) $invoice->grand_total)->toBe(150000.0);
    expect($invoice->payment_status)->toBe('paid');
});

test('save dengan new customer inline membuat customer baru', function () {
    $admin = User::factory()->admin()->create();
    $service = Service::factory()->create(['harga_default' => 100000, 'is_active' => true]);

    Livewire::actingAs($admin, 'admin')
        ->test(InvoiceCreate::class)
        ->call('addItem', $service->id, 'service')
        ->call('openNewCustomer')
        ->set('newCustomerNama', 'Budi Santoso')
        ->set('newCustomerNoHp', '081234567890')
        ->call('save');

    expect(Customer::count())->toBe(1);
    expect(Customer::first()->nama)->toBe('Budi Santoso');
    expect(Invoice::first()->customer_id)->toBe(Customer::first()->id);
});

test('save new customer inline gagal jika nama kosong', function () {
    $admin = User::factory()->admin()->create();
    $service = Service::factory()->create(['is_active' => true]);

    Livewire::actingAs($admin, 'admin')
        ->test(InvoiceCreate::class)
        ->call('addItem', $service->id, 'service')
        ->call('openNewCustomer')
        ->set('newCustomerNoHp', '081234567890')
        ->call('save')
        ->assertHasErrors(['newCustomerNama']);

    expect(Invoice::count())->toBe(0);
});

test('save tipe partner tanpa partnerId menampilkan error', function () {
    $admin = User::factory()->admin()->create();
    $service = Service::factory()->create(['is_active' => true]);

    Livewire::actingAs($admin, 'admin')
        ->test(InvoiceCreate::class)
        ->set('tipe', 'partner')
        ->call('addItem', $service->id, 'service')
        ->call('save')
        ->assertHasErrors(['partnerId']);

    expect(Invoice::count())->toBe(0);
});

test('save tipe booking tanpa bookingId menampilkan error', function () {
    $admin = User::factory()->admin()->create();
    $service = Service::factory()->create(['is_active' => true]);

    Livewire::actingAs($admin, 'admin')
        ->test(InvoiceCreate::class)
        ->set('tipe', 'booking')
        ->call('addItem', $service->id, 'service')
        ->call('save')
        ->assertHasErrors(['bookingId']);

    expect(Invoice::count())->toBe(0);
});

test('removeItem menghapus item dari daftar', function () {
    $admin = User::factory()->admin()->create();
    $service1 = Service::factory()->create(['is_active' => true]);
    $service2 = Service::factory()->create(['is_active' => true]);

    Livewire::actingAs($admin, 'admin')
        ->test(InvoiceCreate::class)
        ->call('addItem', $service1->id, 'service')
        ->call('addItem', $service2->id, 'service')
        ->call('removeItem', 0)
        ->assertCount('items', 1);
});
