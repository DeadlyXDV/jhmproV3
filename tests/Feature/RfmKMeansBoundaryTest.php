<?php

use App\Models\ClusterDefinition;
use App\Models\Customer;
use App\Models\CustomerRfm;
use App\Models\Invoice;
use App\Models\User;
use Database\Seeders\ClusterDefinitionSeeder;

beforeEach(function () {
    $this->seed(ClusterDefinitionSeeder::class);
    $this->admin = User::factory()->admin()->create();
});

// ─── A1: Boundary Value — effectiveK menyesuaikan jumlah pelanggan ──────────

test('n < k: 4 pelanggan menghasilkan 4 cluster berbeda (masing-masing pelanggan 1 cluster)', function () {
    // k = 5 (dari Setting rfm_k_clusters default), n = 4 → effectiveK = min(5,4) = 4
    // Code path: n <= k → setiap pelanggan langsung dapat cluster sendiri
    $customers = Customer::factory()->count(4)->create();

    foreach ($customers as $i => $customer) {
        Invoice::factory()->paid()->create([
            'customer_id' => $customer->id,
            'user_id' => $this->admin->id,
            'tipe' => 'walk_in',
            'tanggal' => now()->subDays(10 + $i * 5)->toDateString(),
            'grand_total' => 100_000 * ($i + 1),
            'subtotal' => 100_000 * ($i + 1),
        ]);
    }

    $this->artisan('rfm:calculate --source=bengkel')->assertSuccessful();

    $rfmRows = CustomerRfm::where('source', 'bengkel')->get();

    expect($rfmRows)->toHaveCount(4);

    $uniqueClusterIds = $rfmRows->pluck('cluster_id')->unique();
    expect($uniqueClusterIds)->toHaveCount(4);
});

test('n = k: tepat 5 pelanggan menghasilkan tepat 5 cluster unik, semua cluster_label terisi', function () {
    // effectiveK = min(5, 5) = 5 → n <= k path, setiap pelanggan dapat cluster sendiri
    $customers = Customer::factory()->count(5)->create();

    foreach ($customers as $i => $customer) {
        Invoice::factory()->paid()->create([
            'customer_id' => $customer->id,
            'user_id' => $this->admin->id,
            'tipe' => 'walk_in',
            'tanggal' => now()->subDays(10 + $i * 5)->toDateString(),
            'grand_total' => 100_000 * ($i + 1),
            'subtotal' => 100_000 * ($i + 1),
        ]);
    }

    $this->artisan('rfm:calculate --source=bengkel')->assertSuccessful();

    $rfmRows = CustomerRfm::where('source', 'bengkel')->get();

    expect($rfmRows)->toHaveCount(5);

    $uniqueClusterIds = $rfmRows->pluck('cluster_id')->unique();
    expect($uniqueClusterIds)->toHaveCount(5);

    $rfmRows->each(fn ($rfm) => expect($rfm->cluster_label)->not->toBeEmpty());
});

test('n > k: 6 pelanggan menghasilkan tepat 5 cluster unik dan semua 6 pelanggan terklasifikasi', function () {
    // effectiveK = min(5, 6) = 5 → K-Means berjalan, menghasilkan 5 cluster
    $customers = Customer::factory()->count(6)->create();

    // Buat data bervariasi jelas agar K-Means tidak konvergen ke satu klaster
    $tiers = [
        ['days' => 2,   'grand_total' => 1_000_000],
        ['days' => 20,  'grand_total' => 500_000],
        ['days' => 60,  'grand_total' => 200_000],
        ['days' => 120, 'grand_total' => 80_000],
        ['days' => 200, 'grand_total' => 30_000],
        ['days' => 300, 'grand_total' => 10_000],
    ];

    foreach ($customers as $i => $customer) {
        Invoice::factory()->paid()->create([
            'customer_id' => $customer->id,
            'user_id' => $this->admin->id,
            'tipe' => 'walk_in',
            'tanggal' => now()->subDays($tiers[$i]['days'])->toDateString(),
            'grand_total' => $tiers[$i]['grand_total'],
            'subtotal' => $tiers[$i]['grand_total'],
        ]);
    }

    $this->artisan('rfm:calculate --source=bengkel')->assertSuccessful();

    $rfmRows = CustomerRfm::where('source', 'bengkel')->get();

    // Semua 6 pelanggan harus terklasifikasi
    expect($rfmRows)->toHaveCount(6);

    // Jumlah cluster unik tepat = k (5), tidak lebih
    $uniqueClusterIds = $rfmRows->pluck('cluster_id')->unique();
    expect($uniqueClusterIds)->toHaveCount(5);

    $rfmRows->each(fn ($rfm) => expect($rfm->cluster_label)->not->toBeEmpty());
});

// ─── A2: Ranking Label Cluster (relatif, bukan threshold absolut) ────────────

test('pelanggan dengan skor RFM tertinggi mendapat cluster dengan centroid sum tertinggi, terendah mendapat terendah', function () {
    // 10 pelanggan dalam 5 tier (2 per tier) — skor RFM tersebar sempurna
    // Quintile scoring pada n=10 menghasilkan skor 1-5 yang bersih per tier
    //
    // Tier 1 (TERBAIK): freq=5, monetary=5jt, recency=2 hari → r=5, f=5, m=5 → rfm_score=5.0
    // Tier 5 (TERBURUK): freq=1, monetary=30rb, recency=250 hari → r=1, f=1, m=1 → rfm_score=1.0
    //
    // Cluster label mapping: centroid sum tertinggi → Champion (id=1), terendah → Lost (id=5)
    $tiers = [
        ['invoices' => 5, 'days' => 2,   'per_invoice' => 1_000_000],
        ['invoices' => 4, 'days' => 20,  'per_invoice' => 500_000],
        ['invoices' => 3, 'days' => 60,  'per_invoice' => 250_000],
        ['invoices' => 2, 'days' => 120, 'per_invoice' => 100_000],
        ['invoices' => 1, 'days' => 250, 'per_invoice' => 30_000],
    ];

    $tierCustomers = [];

    foreach ($tiers as $tierIdx => $tier) {
        $pair = Customer::factory()->count(2)->create();

        foreach ($pair as $customer) {
            Invoice::factory()->paid()->count($tier['invoices'])->create([
                'customer_id' => $customer->id,
                'user_id' => $this->admin->id,
                'tipe' => 'walk_in',
                'tanggal' => now()->subDays($tier['days'])->toDateString(),
                'grand_total' => $tier['per_invoice'],
                'subtotal' => $tier['per_invoice'],
            ]);
        }

        $tierCustomers[$tierIdx] = $pair;
    }

    $this->artisan('rfm:calculate --source=bengkel')->assertSuccessful();

    // Ambil pelanggan dengan rfm_score tertinggi dan terendah
    $best = CustomerRfm::where('source', 'bengkel')->orderByDesc('rfm_score')->first();
    $worst = CustomerRfm::where('source', 'bengkel')->orderBy('rfm_score')->first();

    expect($best)->not->toBeNull();
    expect($worst)->not->toBeNull();
    expect($best->cluster_id)->not->toEqual($worst->cluster_id);

    // Ambil ClusterDefinition (fresh dari DB setelah command memperbarui centroid)
    $bestCluster = ClusterDefinition::find($best->cluster_id);
    $worstCluster = ClusterDefinition::find($worst->cluster_id);

    expect($bestCluster->centroid)->not->toBeNull();
    expect($worstCluster->centroid)->not->toBeNull();

    // Centroid sum cluster pelanggan terbaik harus lebih besar dari cluster pelanggan terburuk
    $bestSum = array_sum($bestCluster->centroid);
    $worstSum = array_sum($worstCluster->centroid);

    expect($bestSum)->toBeGreaterThan($worstSum);
});
