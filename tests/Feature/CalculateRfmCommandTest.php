<?php

use App\Models\ClusterDefinition;
use App\Models\Customer;
use App\Models\CustomerRfm;
use App\Models\Invoice;
use App\Models\RfmHistory;
use App\Models\User;
use Database\Seeders\ClusterDefinitionSeeder;

beforeEach(function () {
    $this->seed(ClusterDefinitionSeeder::class);
});

test('command selesai tanpa error ketika tidak ada customer', function () {
    $this->artisan('rfm:calculate')
        ->assertSuccessful()
        ->expectsOutputToContain('Kalkulasi RFM selesai');
});

test('command membuat customer_rfm dari invoice paid', function () {
    $customer = Customer::factory()->create();
    $adminUser = User::factory()->admin()->create();

    Invoice::factory()->paid()->count(3)->create([
        'customer_id' => $customer->id,
        'user_id' => $adminUser->id,
        'tipe' => 'walk_in',
        'tanggal' => now()->subDays(10)->toDateString(),
    ]);

    $this->artisan('rfm:calculate --source=bengkel')
        ->assertSuccessful();

    expect(CustomerRfm::where('customer_id', $customer->id)->where('source', 'bengkel')->exists())->toBeTrue();
});

test('invoice unpaid tidak masuk kalkulasi RFM', function () {
    $customer = Customer::factory()->create();
    $adminUser = User::factory()->admin()->create();

    Invoice::factory()->count(2)->create([
        'customer_id' => $customer->id,
        'user_id' => $adminUser->id,
        'tipe' => 'walk_in',
        'payment_status' => 'unpaid',
        'tanggal' => now()->subDays(5)->toDateString(),
    ]);

    $this->artisan('rfm:calculate --source=bengkel')
        ->assertSuccessful();

    expect(CustomerRfm::where('customer_id', $customer->id)->exists())->toBeFalse();
});

test('command membuat rfm_history untuk bulan ini', function () {
    $customer = Customer::factory()->create();
    $adminUser = User::factory()->admin()->create();

    Invoice::factory()->paid()->create([
        'customer_id' => $customer->id,
        'user_id' => $adminUser->id,
        'tipe' => 'walk_in',
        'tanggal' => now()->subDays(15)->toDateString(),
    ]);

    $this->artisan('rfm:calculate --source=bengkel')
        ->assertSuccessful();

    expect(
        RfmHistory::where('customer_id', $customer->id)
            ->where('source', 'bengkel')
            ->where('year_month', now()->format('Y-m'))
            ->exists()
    )->toBeTrue();
});

test('rfm_history tidak duplikat jika command dijalankan dua kali dalam bulan yang sama', function () {
    $customer = Customer::factory()->create();
    $adminUser = User::factory()->admin()->create();

    Invoice::factory()->paid()->create([
        'customer_id' => $customer->id,
        'user_id' => $adminUser->id,
        'tipe' => 'walk_in',
        'tanggal' => now()->subDays(5)->toDateString(),
    ]);

    $this->artisan('rfm:calculate --source=bengkel')->assertSuccessful();
    $this->artisan('rfm:calculate --source=bengkel')->assertSuccessful();

    $count = RfmHistory::where('customer_id', $customer->id)
        ->where('source', 'bengkel')
        ->where('year_month', now()->format('Y-m'))
        ->count();

    expect($count)->toBe(1);
});

test('rfm_score dihitung dengan bobot default', function () {
    $customer = Customer::factory()->create();
    $adminUser = User::factory()->admin()->create();

    Invoice::factory()->paid()->create([
        'customer_id' => $customer->id,
        'user_id' => $adminUser->id,
        'tipe' => 'walk_in',
        'grand_total' => 500000,
        'tanggal' => now()->subDays(3)->toDateString(),
    ]);

    $this->artisan('rfm:calculate --source=bengkel')->assertSuccessful();

    $rfm = CustomerRfm::where('customer_id', $customer->id)->where('source', 'bengkel')->first();

    expect($rfm)->not->toBeNull()
        ->and($rfm->rfm_score)->toBeGreaterThan(0)
        ->and($rfm->cluster_label)->not->toBeEmpty();
});

test('customer dengan lebih banyak transaksi mendapat f_score lebih tinggi', function () {
    $adminUser = User::factory()->admin()->create();

    $customerHigh = Customer::factory()->create();
    $customerLow = Customer::factory()->create();

    Invoice::factory()->paid()->count(5)->create([
        'customer_id' => $customerHigh->id,
        'user_id' => $adminUser->id,
        'tipe' => 'walk_in',
        'tanggal' => now()->subDays(10)->toDateString(),
    ]);

    Invoice::factory()->paid()->count(1)->create([
        'customer_id' => $customerLow->id,
        'user_id' => $adminUser->id,
        'tipe' => 'walk_in',
        'tanggal' => now()->subDays(10)->toDateString(),
    ]);

    $this->artisan('rfm:calculate --source=bengkel')->assertSuccessful();

    $high = CustomerRfm::where('customer_id', $customerHigh->id)->where('source', 'bengkel')->first();
    $low = CustomerRfm::where('customer_id', $customerLow->id)->where('source', 'bengkel')->first();

    expect($high->f_score)->toBeGreaterThanOrEqual($low->f_score);
});

test('customer lebih baru mendapat r_score lebih tinggi', function () {
    $adminUser = User::factory()->admin()->create();

    $recent = Customer::factory()->create();
    $old = Customer::factory()->create();

    Invoice::factory()->paid()->create([
        'customer_id' => $recent->id,
        'user_id' => $adminUser->id,
        'tipe' => 'walk_in',
        'tanggal' => now()->subDays(3)->toDateString(),
    ]);

    Invoice::factory()->paid()->create([
        'customer_id' => $old->id,
        'user_id' => $adminUser->id,
        'tipe' => 'walk_in',
        'tanggal' => now()->subDays(300)->toDateString(),
    ]);

    $this->artisan('rfm:calculate --source=bengkel')->assertSuccessful();

    $recentRfm = CustomerRfm::where('customer_id', $recent->id)->where('source', 'bengkel')->first();
    $oldRfm = CustomerRfm::where('customer_id', $old->id)->where('source', 'bengkel')->first();

    expect($recentRfm->r_score)->toBeGreaterThanOrEqual($oldRfm->r_score);
});

test('cluster_id merujuk ke ClusterDefinition yang valid', function () {
    $customer = Customer::factory()->create();
    $adminUser = User::factory()->admin()->create();

    Invoice::factory()->paid()->create([
        'customer_id' => $customer->id,
        'user_id' => $adminUser->id,
        'tipe' => 'walk_in',
        'tanggal' => now()->subDays(5)->toDateString(),
    ]);

    $this->artisan('rfm:calculate --source=bengkel')->assertSuccessful();

    $rfm = CustomerRfm::where('customer_id', $customer->id)->where('source', 'bengkel')->first();

    expect(ClusterDefinition::find($rfm->cluster_id))->not->toBeNull();
});
