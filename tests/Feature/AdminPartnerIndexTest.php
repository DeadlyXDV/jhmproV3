<?php

use App\Livewire\Admin\Partners\PartnerIndex;
use App\Models\Partner;
use App\Models\User;
use Livewire\Livewire;

test('guest diblokir dari halaman partner', function () {
    $this->get(route('admin.partners.index'))->assertRedirect();
});

test('admin bisa akses halaman partner', function () {
    $admin = User::factory()->admin()->create();
    $this->actingAs($admin, 'admin')->get(route('admin.partners.index'))->assertOk();
});

test('admin bisa tambah partner baru', function () {
    $admin = User::factory()->admin()->create();

    Livewire::actingAs($admin, 'admin')
        ->test(PartnerIndex::class)
        ->call('openCreate')
        ->assertSet('showForm', true)
        ->set('namaBengkel', 'Bengkel Maju Jaya')
        ->set('contactPerson', 'Pak Budi')
        ->set('noHp', '081234567890')
        ->set('alamat', 'Jl. Raya No. 10')
        ->call('save');

    expect(Partner::where('nama_bengkel', 'Bengkel Maju Jaya')->exists())->toBeTrue();
});

test('validasi wajib saat tambah partner', function () {
    $admin = User::factory()->admin()->create();

    Livewire::actingAs($admin, 'admin')
        ->test(PartnerIndex::class)
        ->call('openCreate')
        ->call('save')
        ->assertHasErrors(['namaBengkel', 'contactPerson', 'noHp']);
});

test('admin bisa edit partner', function () {
    $admin = User::factory()->admin()->create();
    $partner = Partner::create([
        'nama_bengkel' => 'Bengkel Lama',
        'contact_person' => 'Andi',
        'no_hp' => '08111111111',
    ]);

    Livewire::actingAs($admin, 'admin')
        ->test(PartnerIndex::class)
        ->call('openEdit', $partner->id)
        ->assertSet('showForm', true)
        ->assertSet('namaBengkel', 'Bengkel Lama')
        ->set('namaBengkel', 'Bengkel Baru')
        ->call('save');

    expect($partner->fresh()->nama_bengkel)->toBe('Bengkel Baru');
});

test('form tertutup setelah batal', function () {
    $admin = User::factory()->admin()->create();

    Livewire::actingAs($admin, 'admin')
        ->test(PartnerIndex::class)
        ->call('openCreate')
        ->assertSet('showForm', true)
        ->call('cancelForm')
        ->assertSet('showForm', false);
});

test('admin bisa cari partner berdasarkan nama', function () {
    $admin = User::factory()->admin()->create();
    Partner::create(['nama_bengkel' => 'Spesial Motor', 'contact_person' => 'Rudi', 'no_hp' => '08100000001']);
    Partner::create(['nama_bengkel' => 'Bengkel Umum', 'contact_person' => 'Sari', 'no_hp' => '08100000002']);

    Livewire::actingAs($admin, 'admin')
        ->test(PartnerIndex::class)
        ->set('search', 'Spesial')
        ->assertSee('Spesial Motor')
        ->assertDontSee('Bengkel Umum');
});
