<?php

use App\Models\User;
use Database\Seeders\ClusterDefinitionSeeder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

// ──────────────────────────────────────────────────────────────────────────────
// Helper: bulk-insert customers + paid invoices langsung via DB (bukan factory)
// agar volume besar (500–2000) ter-insert cepat tanpa overhead Eloquent/faker.
// Kolom customer sesuai migration customers (nama, no_hp wajib; email nullable).
// Kolom invoice sesuai migration invoices (invoice_number unique; payment_status paid).
// ──────────────────────────────────────────────────────────────────────────────
function createRfmPerfDataset(int $count, int $adminId): void
{
    $now = now()->toDateTimeString();

    $customerRows = [];
    for ($i = 1; $i <= $count; $i++) {
        $customerRows[] = [
            'nama' => "Perf Customer {$i}",
            'no_hp' => '0812'.str_pad((string) $i, 8, '0', STR_PAD_LEFT),
            'email' => null,
            'created_at' => $now,
            'updated_at' => $now,
        ];
    }

    foreach (array_chunk($customerRows, 500) as $chunk) {
        DB::table('customers')->insert($chunk);
    }

    $customerIds = DB::table('customers')
        ->orderByDesc('id')
        ->limit($count)
        ->pluck('id')
        ->reverse()
        ->values()
        ->all();

    $invoiceRows = [];
    foreach ($customerIds as $idx => $customerId) {
        $daysAgo = ($idx % 364) + 1;
        $amount = (($idx % 40) + 1) * 50_000;
        $invoiceRows[] = [
            'customer_id' => $customerId,
            'user_id' => $adminId,
            'invoice_number' => sprintf('PERF%08d', $customerId),
            'tanggal' => now()->subDays($daysAgo)->toDateString(),
            'tipe' => 'walk_in',
            'subtotal' => $amount,
            'discount' => 0,
            'grand_total' => $amount,
            'payment_status' => 'paid',
            'amount_paid' => $amount,
            'created_at' => $now,
            'updated_at' => $now,
        ];
    }

    foreach (array_chunk($invoiceRows, 500) as $chunk) {
        DB::table('invoices')->insert($chunk);
    }
}

// ── Common setup ──────────────────────────────────────────────────────────────

beforeEach(function () {
    $this->seed(ClusterDefinitionSeeder::class);
    $this->adminUser = User::factory()->admin()->create();
});

// ── NFR-PERF-001: Load Testing ────────────────────────────────────────────────
// Mengukur waktu eksekusi rfm:calculate dari data invoice mentah (500 pelanggan).
// Threshold: < 10 detik.

test('NFR-PERF-001 — rfm:calculate selesai < 10 detik pada 500 pelanggan', function () {
    createRfmPerfDataset(500, $this->adminUser->id);

    $start = microtime(true);
    $exitCode = Artisan::call('rfm:calculate', ['--source' => 'bengkel']);
    $elapsed = round(microtime(true) - $start, 3);

    expect($exitCode)->toBe(0, "Command gagal (exit code {$exitCode})");
    expect($elapsed)->toBeLessThan(10.0, "Aktual: {$elapsed}s — melebihi threshold 10s pada 500 pelanggan");
})->group('nfr', 'performance');

// ── NFR-STRESS-001: Stress Testing (3 level data terpisah) ───────────────────
// Dipilih sebagai 3 test case terpisah agar setiap level bisa di-isolasi
// saat ada kegagalan, dan masing-masing bisa dijalankan mandiri di CI.

test('NFR-STRESS-001a — 500 pelanggan: selesai tanpa error dan < 10 detik', function () {
    createRfmPerfDataset(500, $this->adminUser->id);

    $start = microtime(true);
    $exitCode = Artisan::call('rfm:calculate', ['--source' => 'bengkel']);
    $elapsed = round(microtime(true) - $start, 3);

    expect($exitCode)->toBe(0, "Command gagal (exit code {$exitCode})");
    expect($elapsed)->toBeLessThan(10.0, "Aktual: {$elapsed}s");
})->group('nfr', 'stress');

test('NFR-STRESS-001b — 1.000 pelanggan: selesai tanpa error dan < 30 detik', function () {
    createRfmPerfDataset(1000, $this->adminUser->id);

    $start = microtime(true);
    $exitCode = Artisan::call('rfm:calculate', ['--source' => 'bengkel']);
    $elapsed = round(microtime(true) - $start, 3);

    expect($exitCode)->toBe(0, "Command gagal (exit code {$exitCode})");
    expect($elapsed)->toBeLessThan(30.0, "Aktual: {$elapsed}s — melebihi threshold 30s pada 1.000 pelanggan");
})->group('nfr', 'stress');

test('NFR-STRESS-001c — 2.000 pelanggan: selesai tanpa error (completion test)', function () {
    createRfmPerfDataset(2000, $this->adminUser->id);

    $start = microtime(true);
    $exitCode = Artisan::call('rfm:calculate', ['--source' => 'bengkel']);
    $elapsed = round(microtime(true) - $start, 3);

    // Tidak ada threshold waktu absolut untuk 2000 pelanggan — cukup selesai tanpa error.
    // Catat elapsed untuk referensi skalabilitas di laporan TA.
    expect($exitCode)->toBe(0, "Command gagal pada 2.000 pelanggan (exit code {$exitCode}), elapsed: {$elapsed}s");
})->group('nfr', 'stress');

// ── NFR-ENDR-001: Endurance / Soak Simulation ────────────────────────────────
//
// CATATAN SIMULASI: Ini adalah simulasi dipercepat dari endurance test 30 hari.
// 10 run berturut-turut mewakili beberapa hari eksekusi terjadwal (cron harian).
// Pengujian endurance sesungguhnya (30 hari kalender) harus dilakukan secara
// manual di environment produksi/staging, hasilnya dicatat terpisah di luar
// automated test suite, dan TIDAK bisa digantikan sepenuhnya oleh test ini.

test('NFR-ENDR-001 — simulasi endurance: run ke-10 tidak lebih dari 20% lebih lambat dari run ke-1', function () {
    createRfmPerfDataset(500, $this->adminUser->id);

    $times = [];

    for ($run = 0; $run < 10; $run++) {
        $start = microtime(true);
        Artisan::call('rfm:calculate', ['--source' => 'bengkel']);
        $times[] = round(microtime(true) - $start, 4);
    }

    // Deteksi degradasi sederhana: run ke-10 tidak boleh >20% lebih lambat dari run ke-1.
    // Run 2–10 menggunakan updateOrCreate (data sudah ada) — lebih cepat dari run pertama.
    // Jika ada memory leak di PHP process, run belakangan akan semakin lambat.
    $baseline = max($times[0], 0.05); // floor 50ms untuk menghindari false-positive pada environment sangat cepat
    $degradationRatio = $times[9] / $baseline;

    expect($degradationRatio)->toBeLessThanOrEqual(1.2,
        sprintf(
            'Degradasi terdeteksi: run-1=%.4fs, run-10=%.4fs (%.1f%% lebih lambat, threshold 20%%)',
            $times[0],
            $times[9],
            ($degradationRatio - 1) * 100
        )
    );
})->group('nfr', 'endurance');

// ── NFR-REL-001: Reliability Simulation ──────────────────────────────────────
//
// CATATAN SIMULASI: Ini adalah simulasi dipercepat dari reliability test 7 hari.
// 10 run berturut-turut mewakili 10 hari eksekusi terjadwal (cron harian).
// Pemantauan reliability jangka panjang (7 hari kalender di production) harus
// dilakukan secara manual dan dicatat terpisah di luar automated test suite.
// Success rate 100% dipilih sebagai threshold karena command ini adalah cron
// kritis — satu kegagalan pun seharusnya tidak terjadi tanpa alert.

test('NFR-REL-001 — simulasi reliability: 10 run berturut-turut sukses 100%', function () {
    createRfmPerfDataset(500, $this->adminUser->id);

    $successCount = 0;
    $totalRuns = 10;

    for ($run = 0; $run < $totalRuns; $run++) {
        $exitCode = Artisan::call('rfm:calculate', ['--source' => 'bengkel']);

        if ($exitCode === 0) {
            $successCount++;
        }
    }

    expect($successCount)->toBe($totalRuns,
        "Hanya {$successCount}/{$totalRuns} run berhasil — expected 100% success rate"
    );
})->group('nfr', 'reliability');

// ── NFR-SEC-001a: Rate Limiting ───────────────────────────────────────────────
//
// TEMUAN GAP: Route /rfm TIDAK memiliki throttle middleware.
// Hanya endpoint login Fortify yang di-throttle (5 req/menit via RateLimiter::for('login'...)).
// Route /rfm dilindungi oleh auth:admin + role:super_admin — tanpa rate limiting tambahan.
//
// IMPLIKASI: Endpoint yang berat secara komputasi (RFM dashboard merender stat cards
// dan tabel besar) seharusnya juga dibatasi untuk mencegah scraping atau request storm.
//
// REKOMENDASI (belum diimplementasi, perlu persetujuan tim):
//   Tambahkan ->middleware('throttle:60,1') ke route group super_admin di routes/web.php.
//
// Test di bawah SENGAJA PASS (memverifikasi kondisi saat ini, bukan kondisi yang diharapkan).
// Jika rate limiting ditambahkan di masa depan, test ini harus diupdate untuk
// assert response 429 setelah melampaui limit.

test('NFR-SEC-001a — rate limiting: /rfm tidak di-throttle saat ini (gap didokumentasikan)', function () {
    $superAdmin = User::factory()->superAdmin()->create();

    $responses = [];
    for ($i = 0; $i < 10; $i++) {
        $responses[] = $this->actingAs($superAdmin, 'admin')
            ->get(route('admin.rfm.index'))
            ->status();
    }

    // Semua request mendapat 200 — konfirmasi tidak ada throttle pada route ini.
    // GAP: tidak ada 429 yang diharapkan dari rate limiter.
    foreach ($responses as $status) {
        expect($status)->toBe(200, 'Semua 10 request harus 200 OK (tidak ada rate limiting)');
    }
})->group('nfr', 'security');

// ── NFR-SEC-001b: Data Isolation ─────────────────────────────────────────────
//
// TEMUAN: Aplikasi ini tidak mengimplementasikan multi-tenancy atau data scoping
// per-user. customer_rfm adalah data global yang bisa diakses semua super_admin.
// Isolasi data diimplementasikan di level ROLE (super_admin only), bukan per-user/tenant.
// Test data isolation per-user/per-tenant TIDAK RELEVAN untuk arsitektur saat ini
// dan tidak dipaksakan ditulis.
//
// Test di bawah memverifikasi bahwa role-based isolation berfungsi:
// super_admin bisa akses, role lain ditolak 403.

test('NFR-SEC-001b — data isolation: halaman RFM terisolasi di level role (super_admin only)', function () {
    $superAdmin = User::factory()->superAdmin()->create();
    $admin = User::factory()->admin()->create();
    $mekanik = User::factory()->mekanik()->create();

    $this->actingAs($superAdmin, 'admin')
        ->get(route('admin.rfm.index'))
        ->assertOk();

    $this->actingAs($admin, 'admin')
        ->get(route('admin.rfm.index'))
        ->assertForbidden();

    $this->actingAs($mekanik, 'admin')
        ->get(route('admin.rfm.index'))
        ->assertForbidden();
})->group('nfr', 'security');
