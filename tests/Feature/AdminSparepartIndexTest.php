<?php

use App\Livewire\Admin\Spareparts\SparepartIndex;
use App\Models\Sparepart;
use App\Models\SparepartCategory;
use App\Models\User;
use Livewire\Livewire;

test('guest diblokir dari halaman sparepart', function () {
    $this->get(route('admin.spareparts.index'))->assertRedirect();
});

test('admin bisa akses halaman sparepart', function () {
    $admin = User::factory()->admin()->create();
    $this->actingAs($admin, 'admin')->get(route('admin.spareparts.index'))->assertOk();
});

test('admin bisa tambah sparepart baru', function () {
    $admin = User::factory()->admin()->create();

    Livewire::actingAs($admin, 'admin')
        ->test(SparepartIndex::class)
        ->call('openCreate')
        ->assertSet('showForm', true)
        ->set('sku', 'OLI-TEST-001')
        ->set('itemName', 'Oli Test 1 Liter')
        ->set('satuan', 'liter')
        ->set('hargaBeli', '25000')
        ->set('hargaJual', '35000')
        ->set('stock', '10')
        ->set('minimumStock', '2')
        ->call('save');

    $part = Sparepart::where('sku', 'OLI-TEST-001')->first();
    expect($part)->not->toBeNull();
    expect($part->item_name)->toBe('Oli Test 1 Liter');
    expect($part->harga_jual)->toBe('35000.00');
});

test('sku di-uppercase otomatis saat disimpan', function () {
    $admin = User::factory()->admin()->create();

    Livewire::actingAs($admin, 'admin')
        ->test(SparepartIndex::class)
        ->call('openCreate')
        ->set('sku', 'oli-lowercase-001')
        ->set('itemName', 'Oli Lower')
        ->set('satuan', 'liter')
        ->set('hargaBeli', '10000')
        ->set('hargaJual', '15000')
        ->set('stock', '5')
        ->set('minimumStock', '1')
        ->call('save');

    expect(Sparepart::where('sku', 'OLI-LOWERCASE-001')->exists())->toBeTrue();
});

test('validasi wajib saat tambah sparepart', function () {
    $admin = User::factory()->admin()->create();

    Livewire::actingAs($admin, 'admin')
        ->test(SparepartIndex::class)
        ->call('openCreate')
        ->call('save')
        ->assertHasErrors(['sku', 'itemName', 'satuan', 'hargaBeli', 'hargaJual']);
});

test('sku harus unik saat tambah sparepart', function () {
    $admin = User::factory()->admin()->create();
    Sparepart::factory()->create(['sku' => 'DUPLIKAT-001']);

    Livewire::actingAs($admin, 'admin')
        ->test(SparepartIndex::class)
        ->call('openCreate')
        ->set('sku', 'DUPLIKAT-001')
        ->set('itemName', 'Part Duplikat')
        ->set('satuan', 'pcs')
        ->set('hargaBeli', '5000')
        ->set('hargaJual', '8000')
        ->set('stock', '1')
        ->set('minimumStock', '0')
        ->call('save')
        ->assertHasErrors(['sku']);
});

test('sku boleh sama saat edit sparepart yang sama', function () {
    $admin = User::factory()->admin()->create();
    $part = Sparepart::factory()->create(['sku' => 'EDIT-SAME-001', 'item_name' => 'Part Lama']);

    Livewire::actingAs($admin, 'admin')
        ->test(SparepartIndex::class)
        ->call('openEdit', $part->id)
        ->set('itemName', 'Part Diperbarui')
        ->call('save')
        ->assertHasNoErrors(['sku']);

    expect($part->fresh()->item_name)->toBe('Part Diperbarui');
});

test('admin bisa edit sparepart', function () {
    $admin = User::factory()->admin()->create();
    $part = Sparepart::factory()->create(['harga_jual' => 30000]);

    Livewire::actingAs($admin, 'admin')
        ->test(SparepartIndex::class)
        ->call('openEdit', $part->id)
        ->assertSet('showForm', true)
        ->set('hargaJual', '45000')
        ->call('save');

    expect($part->fresh()->harga_jual)->toBe('45000.00');
});

test('admin bisa toggle active sparepart', function () {
    $admin = User::factory()->admin()->create();
    $part = Sparepart::factory()->create(['is_active' => true]);

    Livewire::actingAs($admin, 'admin')
        ->test(SparepartIndex::class)
        ->call('toggleActive', $part->id);

    expect($part->fresh()->is_active)->toBeFalse();
});

test('form tertutup setelah batal', function () {
    $admin = User::factory()->admin()->create();

    Livewire::actingAs($admin, 'admin')
        ->test(SparepartIndex::class)
        ->call('openCreate')
        ->assertSet('showForm', true)
        ->call('cancelForm')
        ->assertSet('showForm', false);
});

test('filter kategori bekerja', function () {
    $admin = User::factory()->admin()->create();
    $cat = SparepartCategory::create(['name' => 'Oli', 'slug' => 'oli']);
    Sparepart::factory()->create(['item_name' => 'Oli Premium', 'category_id' => $cat->id]);
    Sparepart::factory()->create(['item_name' => 'Busi NGK', 'category_id' => null]);

    Livewire::actingAs($admin, 'admin')
        ->test(SparepartIndex::class)
        ->set('filterCategory', (string) $cat->id)
        ->assertSee('Oli Premium')
        ->assertDontSee('Busi NGK');
});
