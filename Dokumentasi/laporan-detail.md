# Laporan Detail Teknis — Modul RFM & K-Means (JHMPro)

> Dokumen ini disusun langsung dari kode sumber yang ada di project pada saat penulisan (commit `f8587e0`). Semua path, nama field, dan potongan kode diverifikasi dengan membaca file aslinya — bukan asumsi. Bagian yang belum diimplementasikan ditandai eksplisit "belum ada di kode".

---

## 1. Struktur Database

Semua tabel inti didefinisikan di satu file migration konsolidasi:
`database/migrations/2026_06_15_222816_create_core_tables.php`, kecuali `users` yang berada di
`database/migrations/0001_01_01_000000_create_users_table.php`.

**Catatan penting untuk Bab III:** tidak ada satu pun migration yang mendefinisikan foreign key
constraint level database (tidak ada pemanggilan `->foreign()` atau `->constrained()` di seluruh
`database/migrations/`). Semua kolom relasi (`customer_id`, `vehicle_id`, dll.) dideklarasikan
sebagai `unsignedBigInteger` biasa. Relasi antar tabel murni bersifat **logis**, dijaga di level
Eloquent model (`belongsTo`/`hasMany`), bukan constraint SQL. Kolom "FK (logis)" di tabel bawah
merujuk pada konvensi penamaan tersebut, bukan constraint yang benar-benar ada di database.

### 1.1 `customers`

| Field | Tipe | Nullable | Default | FK (logis) |
|---|---|---|---|---|
| id | bigint unsigned, PK, auto-increment | tidak | - | - |
| user_id | unsignedBigInteger | ya | - | `users.id` (unique) |
| nama | string | tidak | - | - |
| no_hp | string | tidak | - | - |
| email | string | ya | - | - |
| alamat | text | ya | - | - |
| catatan | text | ya | - | - |
| created_at, updated_at | timestamp | ya | - | - |

Kolom `user_id` sebenarnya sudah ada langsung di migration konsolidasi ini. Ada file migration
terpisah `2026_06_17_000001_add_user_id_to_customers_table.php` yang juga menambahkan kolom yang
sama, tetapi migration tersebut memakai guard `Schema::hasColumn('customers','user_id')` sehingga
pada instalasi baru (fresh migrate) migration itu **tidak melakukan apa-apa** — kolom sudah dibuat
lebih dulu oleh migration konsolidasi. Ini adalah sisa riwayat refactor migration, bukan bug aktif.

### 1.2 `vehicles`

| Field | Tipe | Nullable | Default | FK (logis) |
|---|---|---|---|---|
| id | bigint unsigned, PK | tidak | - | - |
| customer_id | unsignedBigInteger | tidak | - | `customers.id` |
| brand | string(100) | ya | - | - |
| merk | string | tidak | - | - |
| model | string | tidak | - | - |
| tipe | string | ya | - | - |
| tahun | year | tidak | - | - |
| no_polisi | string, unique | tidak | - | - |
| no_rangka | string | ya | - | - |
| no_mesin | string | ya | - | - |
| warna | string | ya | - | - |
| foto | string | ya | - | - |
| catatan | text | ya | - | - |
| created_at, updated_at | timestamp | ya | - | - |
| deleted_at | timestamp (softDeletes) | ya | - | - |

### 1.3 `vehicle_engine_specs`

Spesifikasi mesin hasil modifikasi, relasi 1-ke-1 dengan `vehicles` (kolom `vehicle_id` unik).
Semua kolom teknis bertipe `string` nullable kecuali disebutkan lain.

| Field | Tipe | Nullable | Default | FK (logis) |
|---|---|---|---|---|
| id | bigint unsigned, PK | tidak | - | - |
| vehicle_id | unsignedBigInteger, **unique** | tidak | - | `vehicles.id` |
| cylinder_head | string | ya | - | - |
| porting_polish | string | ya | - | - |
| klep_in | string | ya | - | - |
| klep_ex | string | ya | - | - |
| per_klep | string | ya | - | - |
| noken_as | string | ya | - | - |
| cylinder_block | string | ya | - | - |
| boring_size | string | ya | - | - |
| piston | string | ya | - | - |
| piston_ring | string | ya | - | - |
| pen_piston | string | ya | - | - |
| crankshaft | string | ya | - | - |
| stroke | string | ya | - | - |
| big_end | string | ya | - | - |
| small_end | string | ya | - | - |
| kopling | string | ya | - | - |
| per_kopling | string | ya | - | - |
| karburator_injeksi | string | ya | - | - |
| filter_udara | string | ya | - | - |
| knalpot | string | ya | - | - |
| pengapian_type | string | ya | - | - |
| cdi_ecu | string | ya | - | - |
| koil | string | ya | - | - |
| busi | string | ya | - | - |
| kelistrikan_acg | string | ya | - | - |
| kelistrikan_aki | string | ya | - | - |
| rasio_gigi | string | ya | - | - |
| gir_depan | string | ya | - | - |
| gir_belakang | string | ya | - | - |
| rantai | string | ya | - | - |
| catatan_tambahan | text | ya | - | - |
| updated_at | timestamp | ya | - | - |

Catatan: tabel ini **tidak** punya kolom `created_at` — hanya `updated_at` yang dideklarasikan manual.

### 1.4 `vehicle_modification_logs`

| Field | Tipe | Nullable | Default | FK (logis) |
|---|---|---|---|---|
| id | bigint unsigned, PK | tidak | - | - |
| vehicle_id | unsignedBigInteger | tidak | - | `vehicles.id` |
| invoice_id | unsignedBigInteger | ya | - | `invoices.id` |
| user_id | unsignedBigInteger | tidak | - | `users.id` |
| judul | string | tidak | - | - |
| deskripsi | text | ya | - | - |
| specs_snapshot | json | ya | - | - |
| parts_used | json | ya | - | - |
| foto | json | ya | - | - |
| logged_at | timestamp | ya | - | - |
| created_at, updated_at | timestamp | ya | - | - |

### 1.5 `invoices`

| Field | Tipe | Nullable | Default | FK (logis) |
|---|---|---|---|---|
| id | bigint unsigned, PK | tidak | - | - |
| vehicle_id | unsignedBigInteger | ya | - | `vehicles.id` |
| customer_id | unsignedBigInteger | ya | - | `customers.id` |
| partner_id | unsignedBigInteger | ya | - | `partners.id` |
| user_id | unsignedBigInteger | tidak | - | `users.id` |
| booking_id | unsignedBigInteger | ya | - | `bookings.id` |
| invoice_number | string, unique | tidak | - | - |
| tanggal | date | tidak | - | - |
| tipe | enum(`walk_in`,`booking`,`partner`,`online`) | tidak | - | - |
| catatan | text | ya | - | - |
| subtotal | decimal(15,2) | tidak | - | - |
| discount | decimal(15,2) | tidak | 0 | - |
| grand_total | decimal(15,2) | tidak | - | - |
| payment_status | enum(`unpaid`,`partial`,`paid`) di migration dasar | tidak | `unpaid` | - |
| amount_paid | decimal(15,2) | tidak | 0 | - |
| created_at, updated_at | timestamp | ya | - | - |

**Perubahan skema lanjutan:** `database/migrations/2026_06_28_175750_alter_invoices_add_voided_payment_status.php`
menambahkan value `voided` ke enum `payment_status` via `ALTER TABLE ... MODIFY COLUMN` mentah,
sehingga skema efektif setelah migrate lengkap adalah
`enum('unpaid','partial','paid','voided')`. Migration ini **di-skip pada driver SQLite** (dijaga
oleh pengecekan `DB::connection()->getDriverName() === 'sqlite'`), jadi di lingkungan testing
(SQLite in-memory) kolom tetap berupa `text`/tanpa enforced enum — tidak masalah karena SQLite
tidak menegakkan CHECK enum secara native untuk `enum()` Laravel di driver ini.

### 1.6 `invoice_items`

| Field | Tipe | Nullable | Default | FK (logis) |
|---|---|---|---|---|
| id | bigint unsigned, PK | tidak | - | - |
| invoice_id | unsignedBigInteger | tidak | - | `invoices.id` |
| service_id | unsignedBigInteger | ya | - | `services.id` |
| sparepart_id | unsignedBigInteger | ya | - | `spareparts.id` |
| type | enum(`service`,`sparepart`) | tidak | - | - |
| nama_snapshot | string | tidak | - | - |
| qty | integer | tidak | 1 | - |
| harga_jual | decimal(15,2) | tidak | - | - |
| harga_beli_snapshot | decimal(15,2) | tidak | - | - |
| subtotal | decimal(15,2) | tidak | - | - |
| created_at, updated_at | timestamp | ya | - | - |

### 1.7 `cluster_definitions`

| Field | Tipe | Nullable | Default | FK (logis) |
|---|---|---|---|---|
| id | bigint unsigned, PK | tidak | - | - |
| label | string | tidak | - | - |
| description | text | ya | - | - |
| color_hex | string | tidak | - | - |
| icon | string | ya | - | - |
| action_suggestion | text | ya | - | - |
| centroid | json | ya | - | - |
| created_at, updated_at | timestamp | ya | - | - |

Diisi via seeder `database/seeders/ClusterDefinitionSeeder.php` dengan 5 baris tetap
(id 1–5 mengikuti urutan insert): **Champion, Loyal, Potential, At Risk, Lost** — lihat detail
di §2.5.

### 1.8 `customer_rfm`

Tabel snapshot RFM terkini per customer (di-overwrite tiap kalkulasi via `updateOrCreate`).

| Field | Tipe | Nullable | Default | FK (logis) |
|---|---|---|---|---|
| id | bigint unsigned, PK | tidak | - | - |
| customer_id | unsignedBigInteger | tidak | - | `customers.id` |
| source | enum(`bengkel`,`online_shop`,`combined`) | tidak | - | - |
| recency_days | unsignedInteger | tidak | - | - |
| frequency | unsignedInteger | tidak | - | - |
| monetary | decimal(15,2) | tidak | - | - |
| r_score | unsignedTinyInteger | tidak | - | - |
| f_score | unsignedTinyInteger | tidak | - | - |
| m_score | unsignedTinyInteger | tidak | - | - |
| rfm_score | decimal(5,2) | tidak | - | - |
| cluster_id | integer | tidak | - | `cluster_definitions.id` |
| cluster_label | string | tidak | - | - |
| period_start | date | tidak | - | - |
| period_end | date | tidak | - | - |
| calculated_at | timestamp | tidak | - | - |

Model `App\Models\CustomerRfm` menonaktifkan timestamps bawaan Eloquent (`public $timestamps = false`)
karena tabel ini memakai kolom kustom `calculated_at`, bukan `created_at`/`updated_at`. Model ini
tidak punya primary key komposit di level DB — kombinasi `(customer_id, source)` dijaga unik secara
logis lewat `updateOrCreate(['customer_id' => ..., 'source' => ...], ...)` di `CalculateRfm`, bukan
`UNIQUE` constraint di migration.

### 1.9 `rfm_history`

Snapshot bulanan (satu baris per customer per `year_month` per `source`) untuk tren histori.

| Field | Tipe | Nullable | Default | FK (logis) |
|---|---|---|---|---|
| id | bigint unsigned, PK | tidak | - | - |
| customer_id | unsignedBigInteger | tidak | - | `customers.id` |
| source | enum(`bengkel`,`online_shop`,`combined`) | tidak | - | - |
| year_month | string(7) — format `YYYY-MM` | tidak | - | - |
| recency_days | unsignedInteger | tidak | - | - |
| frequency | unsignedInteger | tidak | - | - |
| monetary | decimal(15,2) | tidak | - | - |
| cluster_id | integer | tidak | - | `cluster_definitions.id` |
| cluster_label | string | tidak | - | - |
| created_at | timestamp | tidak | - | - |

Model `RfmHistory` juga `$timestamps = false` (hanya `created_at` manual, tidak ada `updated_at`).

### 1.10 `users`

| Field | Tipe | Nullable | Default | FK (logis) |
|---|---|---|---|---|
| id | bigint unsigned, PK | tidak | - | - |
| name | string | tidak | - | - |
| email | string, unique | tidak | - | - |
| email_verified_at | timestamp | ya | - | - |
| password | string | tidak | - | - |
| role | enum(`super_admin`,`admin`,`mekanik`,`customer`) | tidak | `customer` | - |
| is_active | boolean | tidak | `true` | - |
| is_available | boolean | tidak | `true` | - |
| no_hp | string(20) | ya | - | - |
| remember_token | string | ya | - | - |
| created_at, updated_at | timestamp | ya | - | - |

---

## 2. Algoritma RFM & K-Means

### 2.1 Lokasi file

`app/Console/Commands/CalculateRfm.php` — Artisan command `rfm:calculate {--source=all : bengkel|combined|all}`.

### 2.2 Alur logika (mengikuti urutan kode di method `handle()`)

```
1.  Baca konfigurasi dari tabel `settings` (fallback default jika belum diset):
      k               = Setting::get('rfm_k_clusters', 5)
      weightR         = Setting::get('rfm_weight_r', 0.3)
      weightF         = Setting::get('rfm_weight_f', 0.3)
      weightM         = Setting::get('rfm_weight_m', 0.4)
      periodMonths    = Setting::get('rfm_period_months', 12)

2.  Tentukan window waktu:
      periodStart = now() - periodMonths bulan
      periodEnd   = now()
      today       = now()  (untuk hitung recency)

3.  Tentukan sumber data yang diproses dari opsi --source:
      'all'      -> ['bengkel', 'combined']
      lainnya    -> [opsi tersebut]   (mis. 'bengkel' saja)

4.  Ambil daftar ClusterDefinition, urut by id ASC -> $clusterDefs (id => label)
    $clusterIds = array_keys($clusterDefs)   // urutan prestise: Champion dulu

5.  UNTUK SETIAP $source DALAM $sources:
    a. $rows = fetchRawRfm($source, $periodStart)
       - jika 'bengkel': agregasi dari `invoices`
         WHERE payment_status = 'paid'
           AND tipe IN ('walk_in','booking','partner')
           AND tanggal >= periodStart
           AND customer_id IS NOT NULL
         GROUP BY customer_id
         SELECT customer_id, MAX(tanggal) as last_txn,
                COUNT(*) as freq, SUM(grand_total) as monetary
       - jika 'combined': gabungkan baris invoice (bengkel, kondisi sama)
         dengan baris `orders` (payment_status='paid', created_at >= periodStart,
         customer_id NOT NULL), lalu digabung dan di-groupBy customer_id di PHP
         (bukan SQL) menggunakan Collection::groupBy()->map(...)
       - jika tidak ada baris sama sekali -> skip source ini, lanjut ke source berikutnya

    b. Hitung nilai mentah per customer:
       recencyDays[cid] = |today->diffInDays(last_txn)|   (dibulatkan ke int, abs())
       frequencies[cid] = freq (int)
       monetaries[cid]  = monetary (float)

    c. Hitung skor kuintil (1–5):
       rScores = quintilesInverse(recencyDays)   // recency kecil -> skor tinggi
       fScores = quintiles(frequencies)          // freq besar -> skor tinggi
       mScores = quintiles(monetaries)           // monetary besar -> skor tinggi

    d. Bentuk titik 3 dimensi per customer:
       points[cid] = [ (float) rScores[cid], (float) fScores[cid], (float) mScores[cid] ]

    e. effectiveK = min(k, jumlah customer)
       result = kMeans(points, effectiveK, clusterIds)
       // result = ['assignments' => [cid => clusterDefId], 'centroids' => [clusterDefId => [r,f,m]]]

    f. UNTUK SETIAP customer:
       - clusterIdx  = result.assignments[cid]
       - clusterLabel = clusterDefs[clusterIdx] atau 'Unknown'
       - rfmScore = round( r*weightR + f*weightF + m*weightM, 2 )
       - updateOrCreate baris `customer_rfm` (key: customer_id + source)
         dengan recency/frequency/monetary/r_score/f_score/m_score/rfm_score/
         cluster_id/cluster_label/period_start/period_end/calculated_at
       - jika BELUM ADA baris `rfm_history` untuk (customer_id, source, year_month=bulan ini):
           insert baris baru rfm_history (idempotent per bulan — command boleh
           dijalankan berkali-kali dalam bulan yang sama tanpa duplikasi history)

    g. UNTUK SETIAP centroid hasil kMeans:
       update kolom `centroid` (json) di baris ClusterDefinition terkait

    h. Cetak log ringkas: jumlah customer diproses & jumlah cluster efektif

6.  Cetak "Kalkulasi RFM selesai." dan return SUCCESS (0)
```

### 2.3 Kode asli `quintiles()` dan `quintilesInverse()`

```php
/**
 * Quintile scoring: higher value → higher score (1–5).
 *
 * @param  array<int|string, int|float>  $values
 * @return array<int|string, int>
 */
private function quintiles(array $values): array
{
    $n = count($values);
    if ($n === 0) {
        return [];
    }

    asort($values);
    $sorted = array_keys($values);
    $scores = [];

    foreach ($sorted as $rank => $id) {
        $scores[$id] = (int) min(5, floor($rank * 5 / $n) + 1);
    }

    return $scores;
}

/**
 * Quintile scoring inverted: lower value → higher score (1–5). Used for Recency.
 *
 * @param  array<int|string, int|float>  $values
 * @return array<int|string, int>
 */
private function quintilesInverse(array $values): array
{
    $inverted = array_map(fn ($v) => -$v, $values);

    return $this->quintiles($inverted);
}
```

Cara kerja `quintiles()`: nilai diurutkan naik (`asort`), lalu tiap item diberi *rank* (indeks
posisi setelah sort). Skor = `floor(rank * 5 / n) + 1`, dibatasi maksimum 5 dengan `min(5, ...)`.
Ini adalah pembagian kuintil berbasis **rank/posisi** (bukan berbasis breakpoint nilai absolut),
sehingga distribusi skor 1–5 akan relatif merata mengikuti jumlah data, bukan mengikuti rentang nilai.

`quintilesInverse()` hanya membalik tanda nilai (`-$v`) sebelum memanggil `quintiles()` — trik
sederhana supaya nilai terkecil (recency paling baru/kecil harinya) mendapat skor tertinggi.

### 2.4 Kode asli `kMeans()`

```php
/**
 * K-Means clustering.
 *
 * @param  array<int|string, array<int, float>>  $points  keyed by customer_id
 * @param  array<int, int>  $clusterIds  ClusterDefinition IDs ordered by prestige (Champion first)
 * @return array{assignments: array<int|string, int>, centroids: array<int, array<int, float>>}
 */
private function kMeans(array $points, int $k, array $clusterIds): array
{
    $customerIds = array_keys($points);
    $n = count($customerIds);

    if ($n <= $k) {
        $assignments = [];
        foreach ($customerIds as $i => $cid) {
            $assignments[$cid] = $clusterIds[$i] ?? $clusterIds[0];
        }
        $centroids = [];
        foreach ($assignments as $cid => $clIdx) {
            $centroids[$clIdx] = $points[$cid];
        }

        return ['assignments' => $assignments, 'centroids' => $centroids];
    }

    // Initialize centroids: pick first k shuffled points
    $shuffled = $customerIds;
    shuffle($shuffled);
    $internalCentroids = [];
    for ($i = 0; $i < $k; $i++) {
        $internalCentroids[$i] = array_values($points[$shuffled[$i]]);
    }

    $assignments = [];
    $maxIter = 100;

    for ($iter = 0; $iter < $maxIter; $iter++) {
        $newAssignments = [];

        foreach ($customerIds as $cid) {
            $bestCluster = 0;
            $bestDist = PHP_FLOAT_MAX;
            for ($c = 0; $c < $k; $c++) {
                $dist = $this->euclidean($points[$cid], $internalCentroids[$c]);
                if ($dist < $bestDist) {
                    $bestDist = $dist;
                    $bestCluster = $c;
                }
            }
            $newAssignments[$cid] = $bestCluster;
        }

        if ($newAssignments === $assignments) {
            break;
        }
        $assignments = $newAssignments;

        $dims = count(reset($points));
        for ($c = 0; $c < $k; $c++) {
            $members = array_filter($customerIds, fn ($cid) => $assignments[$cid] === $c);
            if (! empty($members)) {
                $centroid = array_fill(0, $dims, 0.0);
                foreach ($members as $cid) {
                    foreach ($points[$cid] as $d => $val) {
                        $centroid[$d] += $val;
                    }
                }
                $cnt = count($members);
                $internalCentroids[$c] = array_map(fn ($v) => $v / $cnt, $centroid);
            }
        }
    }

    // Map internal indices (0..k-1) → ClusterDefinition IDs
    // Highest centroid sum → Champion (first in $clusterIds)
    $centroidSums = [];
    for ($c = 0; $c < $k; $c++) {
        $centroidSums[$c] = array_sum($internalCentroids[$c]);
    }
    arsort($centroidSums);

    $internalToCluster = [];
    $i = 0;
    foreach (array_keys($centroidSums) as $internalIdx) {
        $internalToCluster[$internalIdx] = $clusterIds[$i] ?? end($clusterIds);
        $i++;
    }

    $finalAssignments = [];
    foreach ($assignments as $cid => $internalIdx) {
        $finalAssignments[$cid] = $internalToCluster[$internalIdx];
    }

    $finalCentroids = [];
    foreach ($internalToCluster as $internalIdx => $clusterDefId) {
        $finalCentroids[$clusterDefId] = array_map(
            fn ($v) => round((float) $v, 4),
            $internalCentroids[$internalIdx]
        );
    }

    return ['assignments' => $finalAssignments, 'centroids' => $finalCentroids];
}

/** @param array<int, float|int> $b */
private function euclidean(array $a, array $b): float
{
    $sum = 0.0;
    foreach ($a as $i => $val) {
        $diff = $val - ($b[$i] ?? 0.0);
        $sum += $diff * $diff;
    }

    return sqrt($sum);
}
```

Ringkasan alur `kMeans()`:
1. **Inisialisasi centroid**: k titik dipilih secara acak (`shuffle()` lalu ambil k pertama) dari
   data pelanggan itu sendiri (Forgy initialization), bukan k-means++.
2. **Iterasi Lloyd's algorithm** (maksimum 100 iterasi, berhenti lebih awal jika assignment tidak
   berubah dari iterasi sebelumnya / konvergen):
   - Assignment step: tiap pelanggan diberikan ke centroid terdekat berdasarkan jarak Euclidean
     3 dimensi (R, F, M score).
   - Update step: centroid baru = rata-rata titik anggota tiap cluster (jika cluster punya anggota;
     cluster kosong tidak di-update pada iterasi itu — centroid lama dipertahankan).
3. **Pemetaan indeks internal (0..k-1) ke label bisnis**: dihitung `centroidSums` = jumlah 3 nilai
   centroid (R+F+M) untuk tiap cluster internal, diurutkan menurun (`arsort`). Cluster dengan jumlah
   centroid tertinggi dipetakan ke `clusterIds[0]` (Champion), berikutnya ke `clusterIds[1]` (Loyal),
   dan seterusnya sampai `clusterIds[4]` (Lost).

### 2.5 Penentuan jumlah cluster (k) & penanganan n < k

- `k` diambil dari `Setting::get('rfm_k_clusters', 5)` — default **5** jika belum pernah diset lewat
  halaman admin Pengaturan.
- `effectiveK = min($k, count($points))` dihitung di `handle()` sebelum memanggil `kMeans()`.
- Di dalam `kMeans()`, jika `$n <= $k` (jumlah pelanggan lebih sedikit atau sama dengan k):
  - **Tidak menjalankan iterasi K-Means sama sekali.**
  - Setiap pelanggan langsung di-assign ke satu cluster berbeda secara berurutan:
    `$assignments[$cid] = $clusterIds[$i] ?? $clusterIds[0]` (indeks `$i` mengikuti urutan
    `array_keys($points)`, bukan urutan skor).
  - Centroid tiap cluster = titik RFM pelanggan itu sendiri (karena masing-masing cluster hanya
    berisi 1 anggota).
  - Ini dikonfirmasi oleh test `RfmKMeansBoundaryTest` skenario "n < k" (4 pelanggan → 4 cluster
    berbeda) dan "n = k" (5 pelanggan → 5 cluster unik, lihat §4).

### 2.6 Penentuan label cluster dari hasil clustering

Label **tidak** ditentukan oleh nama/urutan cluster internal K-Means (0..k-1), melainkan dipetakan
ulang berdasarkan **peringkat jumlah nilai centroid** (`array_sum($internalCentroids[$c])`) —
lihat kode `arsort($centroidSums)` di §2.4:

- Cluster dengan jumlah skor R+F+M centroid **tertinggi** → dipetakan ke `ClusterDefinition` id
  pertama dalam urutan `$clusterIds` (diurutkan `orderBy('id')` di `handle()`), yaitu **Champion**.
- Cluster tertinggi kedua → **Loyal**.
- Ketiga → **Potential**.
- Keempat → **At Risk**.
- Kelima (jumlah centroid terendah) → **Lost**.

Kelima label ini bukan hardcode di `CalculateRfm.php`, melainkan berasal dari data seed
`database/seeders/ClusterDefinitionSeeder.php`:

```php
$clusters = [
    ['label' => 'Champion', 'description' => 'Recency, frequency, dan monetary tertinggi', ...],
    ['label' => 'Loyal', 'description' => 'Frequency dan monetary tinggi, recency sedang', ...],
    ['label' => 'Potential', 'description' => 'Recency tinggi, frequency dan monetary rendah', ...],
    ['label' => 'At Risk', 'description' => 'Recency menurun, dulunya aktif', ...],
    ['label' => 'Lost', 'description' => 'Recency sangat lama, semua skor rendah', ...],
];
```

Seeder di-`firstOrCreate` berurutan sehingga id 1=Champion, 2=Loyal, 3=Potential, 4=At Risk,
5=Lost (mengasumsikan auto-increment dimulai dari 1 pada instalasi baru). Test
`RfmKMeansBoundaryTest` memverifikasi hubungan relatif ini (pelanggan dengan skor RFM tertinggi
mendapat cluster dengan centroid sum tertinggi, terendah mendapat centroid sum terendah) — bukan
menguji label string secara langsung karena mapping label bergantung pada urutan id di database.

### 2.7 Pendaftaran jadwal di Laravel Scheduler

File: `routes/console.php`

```php
Schedule::command('booking:generate-slots')->daily();
Schedule::command('rfm:calculate')->dailyAt('00:00');
```

Baris 12: `rfm:calculate` dijadwalkan berjalan setiap hari pukul 00:00 (tanpa opsi `--source`,
sehingga default `--source=all` terpakai → memproses source `bengkel` dan `combined`).

`bootstrap/app.php` mendaftarkan `routes/console.php` lewat konfigurasi
`->withRouting(commands: __DIR__.'/../routes/console.php', ...)`, yang merupakan mekanisme standar
Laravel 11/12+ untuk memuat Closure-based commands & scheduler tanpa `Console/Kernel.php` terpisah.

### 2.8 Library eksternal untuk K-Means/ML

**Tidak ada library php-ml, Rubix ML, atau library machine learning eksternal apa pun yang
digunakan.** Sudah diverifikasi dengan:

```
grep -i "php-ml|rubix|phpml" composer.json composer.lock   -> tidak ada hasil
grep -rn "Phpml|PhpML|php-ml|Rubix" app/                    -> tidak ada hasil
```

Seluruh algoritma K-Means, perhitungan jarak Euclidean, dan quintile scoring adalah **implementasi
manual dalam PHP murni** di dalam `CalculateRfm.php` (private methods `kMeans()`, `euclidean()`,
`quintiles()`, `quintilesInverse()`). Tidak ada `use` statement yang mengimpor namespace library ML
pihak ketiga di file ini — hanya `App\Models\ClusterDefinition`, `App\Models\CustomerRfm`,
`App\Models\RfmHistory`, `App\Models\Setting`, dan helper Laravel bawaan (`Carbon`, `Collection`,
`DB`).

---

## 3. Routes / Halaman Aplikasi

Sumber: `routes/web.php` (satu-satunya file routing halaman selain `routes/settings.php` untuk
profil/keamanan akun dan `routes/console.php` untuk command/scheduler). Guard `admin` dan `web`
didefinisikan di `config/auth.php` — keduanya memakai driver `session` dengan provider Eloquent
`users` yang sama (satu tabel `users` dipakai bersama oleh kedua guard).

| Fitur | Method | Path | Component/Handler | Middleware |
|---|---|---|---|---|
| Login | GET | `/login` (Fortify default) | View `admin.auth.login` (di-set via `Fortify::loginView()` di `FortifyServiceProvider`) | `web` (grup middleware Fortify) |
| Login (submit) | POST | `/login` (Fortify default) | `Fortify::authenticateUsing()` closure custom di `FortifyServiceProvider::configureAuthentication()` — mengecek `is_active`, lalu login manual ke guard `admin` jika role termasuk `super_admin/admin/mekanik` | `web`, rate limit `login` (5/menit per email+ip) |
| Dashboard admin | GET | `/admin/dashboard` | `App\Livewire\Admin\Dashboard\AdminDashboard` | `auth:admin`, `role:super_admin,admin` |
| Kelola Pelanggan — index | GET | `/admin/customers` | `App\Livewire\Admin\Customers\CustomerIndex` | `auth:admin`, `role:super_admin,admin` |
| Kelola Pelanggan — create | GET | `/admin/customers/create` | `App\Livewire\Admin\Customers\CustomerCreate` | `auth:admin`, `role:super_admin,admin` |
| Kelola Pelanggan — detail | GET | `/admin/customers/{customer}` | `App\Livewire\Admin\Customers\CustomerDetail` | `auth:admin`, `role:super_admin,admin` |
| Kelola Pelanggan — edit | GET | `/admin/customers/{customer}/edit` | `App\Livewire\Admin\Customers\CustomerCreate` | `auth:admin`, `role:super_admin,admin` |
| Kelola Kendaraan — index | GET | `/admin/vehicles` | `App\Livewire\Admin\Vehicles\VehicleIndex` | `auth:admin`, `role:super_admin,admin` |
| Kelola Kendaraan — create | GET | `/admin/vehicles/create` | `App\Livewire\Admin\Vehicles\VehicleCreate` | `auth:admin`, `role:super_admin,admin` |
| Kelola Kendaraan — detail | GET | `/admin/vehicles/{vehicle}` | `App\Livewire\Admin\Vehicles\VehicleDetail` | `auth:admin`, `role:super_admin,admin` |
| Kelola Kendaraan — edit | GET | `/admin/vehicles/{vehicle}/edit` | `App\Livewire\Admin\Vehicles\VehicleCreate` | `auth:admin`, `role:super_admin,admin` |
| POS / Kasir | GET | `/admin/pos` | `App\Livewire\Admin\Pos\PosPage` | `auth:admin`, `role:super_admin,admin` |
| Invoice — index | GET | `/admin/invoices` | `App\Livewire\Admin\Invoices\InvoiceIndex` | `auth:admin`, `role:super_admin,admin` |
| Invoice — create | GET | `/admin/invoices/create` | `App\Livewire\Admin\Invoices\InvoiceCreate` | `auth:admin`, `role:super_admin,admin` |
| Invoice — detail | GET | `/admin/invoices/{invoice}` | `App\Livewire\Admin\Invoices\InvoiceDetail` | `auth:admin`, `role:super_admin,admin` |
| Booking — index | GET | `/admin/bookings` | `App\Livewire\Admin\Bookings\BookingIndex` | `auth:admin`, `role:super_admin,admin` |
| Booking — calendar | GET | `/admin/bookings/calendar` | `App\Livewire\Admin\Bookings\BookingCalendar` | `auth:admin`, `role:super_admin,admin` |
| Booking online (customer) | GET | `/booking` | `App\Livewire\Website\Booking\BookingPage` | `auth`, `verified` |
| **Dashboard Segmentasi RFM** | GET | `/admin/rfm` | `App\Livewire\Admin\Rfm\RfmIndex` | `auth:admin`, `role:super_admin` (super admin only) |
| Laporan/Reports | GET | `/admin/reports` | `App\Livewire\Admin\Reports\ReportIndex` | `auth:admin`, `role:super_admin` |
| Pengaturan | GET | `/admin/settings` | `App\Livewire\Admin\Settings\SettingIndex` | `auth:admin`, `role:super_admin` |
| Dashboard Mekanik | GET | `/mekanik/dashboard` | `App\Livewire\Mekanik\Dashboard\MekanikDashboard` | `auth:admin`, `role:mekanik` |

Ringkasan middleware/guard:
- `auth:admin` — memastikan ada sesi login pada guard `admin` (session terpisah dari guard `web`,
  meski keduanya memvalidasi ke tabel `users` yang sama).
- `role:<daftar_role>` — alias middleware `App\Http\Middleware\CheckRole`, terdaftar di
  `bootstrap/app.php` (`$middleware->alias(['role' => CheckRole::class])`). Implementasinya
  mengambil user dari `auth('admin')->user() ?? auth('web')->user()`, lalu `abort(403)` jika role
  user tidak ada dalam daftar argumen middleware.
- Halaman `/admin/rfm` **khusus** `role:super_admin` — admin biasa dan mekanik mendapat 403 (bukan
  redirect), sesuai yang diverifikasi test `AdminRfmIndexTest` dan `RfmPerformanceTest`
  (NFR-SEC-001b).

---

## 4. Hasil Pengujian (Pest)

### 4.1 Hasil eksekusi test suite (dijalankan langsung: `php artisan test` / `./vendor/bin/pest`)

```json
{"tool":"pest","result":"passed","tests":156,"passed":155,"assertions":432,"duration_ms":19343,"skipped":1}
```

- **Total test: 156** (155 passed, 1 skipped, 0 failed)
- **Total assertion: 432**
- **Durasi eksekusi penuh: ± 19,3 detik** (dua kali percobaan menghasilkan 19,3s dan 20,1s — variasi
  wajar tergantung beban CPU saat run)
- 1 test yang di-skip adalah `tests/Feature/Auth/AuthenticationTest.php` — kasus
  "users with two factor enabled are redirected to two factor challenge", yang memanggil
  `$this->skipUnlessFortifyHas(Features::twoFactorAuthentication())`. Fitur two-factor **tidak
  diaktifkan** di `config/fortify.php` (`'features' => [registration(), resetPasswords(),
  emailVerification()]` — `twoFactorAuthentication()` tidak ada di daftar), sehingga test tersebut
  otomatis di-skip, bukan gagal.

> Catatan metodologi: output mentah PHPUnit/Pest di lingkungan eksekusi ini secara otomatis
> diringkas oleh tooling menjadi JSON singkat seperti di atas (nama test individual dengan status
> pass/fail per baris tidak ditampilkan ke stdout). Daftar nama test case per file di bawah diambil
> langsung dari nama string di source code test (`test('...', ...)` / `it('...', ...)`), bukan dari
> output eksekusi.

### 4.2 Daftar file test & isi pengujian

| File | Jumlah test | Fokus pengujian |
|---|---|---|
| `tests/Feature/AdminBookingIndexTest.php` | 7 | Akses halaman booking per role, konfirmasi/batalkan booking, alur create-invoice dari booking |
| `tests/Feature/AdminInvoiceCreateTest.php` | 19 | Akses per role, `addItem`/`removeItem` cart invoice, validasi harga dari DB (bukan client), pengurangan stok, pembuatan payment record, perhitungan grand_total & diskon, customer inline |
| `tests/Feature/AdminOrderIndexTest.php` | 7 | Akses per role, tampilan daftar order, filter payment status, search order number |
| `tests/Feature/AdminPartnerIndexTest.php` | 7 | Akses, tambah/edit partner, validasi wajib, search partner |
| `tests/Feature/AdminPosPageTest.php` | 12 | Akses POS per role, `addToCart` (harga dari DB, tipe invalid, qty existing, sparepart nonaktif), `buatInvoice` (stok berkurang, harga canonical, cart kosong) |
| `tests/Feature/AdminReportIndexTest.php` | 8 | Akses laporan per role, summary cards, perhitungan total pendapatan, export CSV (izin per role) |
| `tests/Feature/AdminRfmIndexTest.php` | 7 | Akses `/admin/rfm` per role (super_admin only), tampilan tab sumber & stat cards, empty state saat belum ada data RFM |
| `tests/Feature/AdminSettingIndexTest.php` | 11 | Akses pengaturan per role, 4 tab pengaturan, simpan profil bengkel, buat/nonaktifkan user, simpan konfigurasi booking |
| `tests/Feature/AdminSparepartIndexTest.php` | 11 | Akses, tambah/edit sparepart, SKU auto-uppercase & unik, validasi, toggle aktif, filter kategori |
| `tests/Feature/CalculateRfmCommandTest.php` | 10 | Command RFM: tanpa customer, dari invoice paid, exclude unpaid, pembuatan rfm_history, no-duplicate history dalam bulan sama, perhitungan rfm_score dengan bobot default, korelasi f_score/r_score, validitas cluster_id, pelanggan tanpa transaksi tidak masuk hasil |
| `tests/Feature/DashboardTest.php` | 2 | Redirect guest, akses dashboard user login |
| `tests/Feature/ExampleTest.php` | 1 | Smoke test default Laravel |
| `tests/Feature/GenerateBookingSlotsCommandTest.php` | 4 | Command generate slot booking: hari operasional, hari nonoperasional, update kapasitas, opsi `--days` |
| `tests/Feature/RfmKMeansBoundaryTest.php` | 4 | Boundary K-Means: n<k, n=k, n>k, dan ranking label cluster relatif terhadap centroid sum |
| `tests/Feature/RfmPerformanceTest.php` | 8 | NFR performa/stress/endurance/reliability/security untuk `rfm:calculate` (detail §5) |
| `tests/Feature/WebsiteBookingPageTest.php` | 9 | Alur booking online customer: redirect guest, akses, kendaraan inline, step wizard, race condition slot, slot penuh, auto-create customer |
| `tests/Feature/Auth/AuthenticationTest.php` | 5 (1 skip) | Login screen, autentikasi sukses/gagal, two-factor (skip), logout |
| `tests/Feature/Auth/EmailVerificationTest.php` | 4 | Layar verifikasi email, verifikasi sukses/gagal, sudah terverifikasi |
| `tests/Feature/Auth/PasswordConfirmationTest.php` | 1 | Render layar konfirmasi password |
| `tests/Feature/Auth/PasswordResetTest.php` | 4 | Layar reset password, request link, reset dengan token valid |
| `tests/Feature/Auth/RegistrationTest.php` | 2 | Render layar registrasi, registrasi user baru |
| `tests/Feature/Settings/ProfileUpdateTest.php` | 5 | Halaman profil, update profil, status verifikasi email tetap, hapus akun (dengan/tanpa password benar) |
| `tests/Feature/Settings/SecurityTest.php` | 5 | Halaman keamanan, konfirmasi password, render tanpa 2FA, update password |
| `tests/Unit/ExampleTest.php` | 1 | Smoke test unit default |
| **Total** | **156** (155 pass + 1 skip) | **432 assertion** |

Khusus modul RFM/K-Means: **29 test case** tersebar di `CalculateRfmCommandTest` (10),
`RfmKMeansBoundaryTest` (4), `RfmPerformanceTest` (8), dan `AdminRfmIndexTest` (7).

---

## 5. Hasil Uji Performa

Ada implementasi test performa untuk `rfm:calculate` di `tests/Feature/RfmPerformanceTest.php`,
yang membuat dataset sintetis via bulk-insert langsung ke tabel `customers` dan `invoices`
(bukan factory, untuk menghindari overhead Eloquent saat insert volume besar), lalu mengukur waktu
eksekusi command dengan `microtime(true)` sebelum dan sesudah `Artisan::call('rfm:calculate', ...)`.

| Kode Test | Skenario | Ambang batas (threshold) | Status pada run ini |
|---|---|---|---|
| NFR-PERF-001 | 500 pelanggan | < 10 detik | **PASS** |
| NFR-STRESS-001a | 500 pelanggan | < 10 detik | **PASS** |
| NFR-STRESS-001b | 1.000 pelanggan | < 30 detik | **PASS** |
| NFR-STRESS-001c | 2.000 pelanggan | tidak ada batas waktu, hanya harus selesai tanpa error | **PASS** |
| NFR-ENDR-001 | Simulasi endurance: 10 run berturut-turut pada 500 pelanggan, run ke-10 tidak boleh >20% lebih lambat dari run ke-1 | rasio ≤ 1.2 | **PASS** |
| NFR-REL-001 | Simulasi reliability: 10 run berturut-turut, semua harus exit code 0 | 100% sukses | **PASS** |

**Belum ada angka waktu eksekusi presisi (detik) yang tercatat/tersimpan sebagai output permanen.**
Test hanya melakukan assertion pass/fail terhadap ambang batas di atas (`expect($elapsed)->toBeLessThan(...)`)
— nilai `$elapsed` aktual tidak di-log ke file atau ditampilkan ke output test runner kecuali test
tersebut gagal (barulah pesan assertion menampilkan angka aktual, mis. `"Aktual: {$elapsed}s"`).
Karena seluruh 6 test performa di atas **lulus**, tidak ada angka detik presisi yang bisa dikutip
dari hasil run ini tanpa menjalankan ulang dengan instrumentasi tambahan (mis. `dump($elapsed)` atau
menyimpan ke file log) — dan itu belum dilakukan, sehingga bagian ini **tidak mengarang angka**.
Jika dibutuhkan angka presisi untuk Bab IV/V, perlu menjalankan ulang test ini dengan modifikasi
kecil untuk mencetak `$elapsed`/`$times` ke output atau file.

Catatan tambahan dari kode test yang relevan untuk Bab V (pengujian non-fungsional):
- **NFR-SEC-001a** (grup `security`): mengonfirmasi route `/admin/rfm` **tidak** memiliki middleware
  rate-limiting (`throttle`) — hanya endpoint login Fortify yang dibatasi 5 request/menit. Test ini
  sengaja PASS untuk mendokumentasikan gap yang ada, bukan untuk memvalidasi kondisi ideal. Ini
  adalah temuan yang perlu didiskusikan di Bab V sebagai rekomendasi perbaikan (belum
  diimplementasikan).
- **NFR-SEC-001b**: mengonfirmasi isolasi akses `/admin/rfm` di level role — hanya `super_admin`
  yang mendapat 200 OK, role `admin` dan `mekanik` mendapat 403 Forbidden.

---

## 6. Evaluasi Cluster (Elbow Method & Silhouette Score)

**Belum ada implementasi Elbow Method atau Silhouette Score di kode saat ini.** Sudah diverifikasi:

```
grep -rin "elbow|silhouette" app/ database/   -> tidak ada hasil sama sekali
```

Jumlah cluster (`k`) di aplikasi ini **bukan** hasil evaluasi otomatis (Elbow/Silhouette), melainkan
nilai tetap yang dikonfigurasi manual lewat pengaturan `rfm_k_clusters` (default 5, mengikuti jumlah
baris `cluster_definitions` yang di-seed: Champion/Loyal/Potential/At Risk/Lost). Tidak ada kolom di
`cluster_definitions`, `customer_rfm`, maupun tabel lain yang menyimpan skor evaluasi kualitas
cluster (WCSS, silhouette coefficient, dsb.).

Jika Bab V/VI laporan akademik memerlukan evaluasi kualitas cluster secara kuantitatif, ini adalah
gap yang perlu **dicatat secara eksplisit sebagai keterbatasan (limitation) implementasi saat ini**,
bukan diklaim sudah ada.

---

## 7. Screenshot yang Tersedia

Berdasarkan penelusuran direktori `resources/views/livewire/admin/`, seluruh halaman berikut sudah
punya tampilan Blade jadi dan bisa langsung diakses via route masing-masing (lihat §3) untuk
di-screenshot:

| Urutan disarankan | Halaman | Route | View |
|---|---|---|---|
| 1 | Login | `/login` | `resources/views/admin/auth/login.blade.php` |
| 2 | Dashboard admin | `/admin/dashboard` | `resources/views/livewire/admin/dashboard/index.blade.php` |
| 3 | Kelola Pelanggan (index) | `/admin/customers` | `resources/views/livewire/admin/customers/` |
| 4 | Kelola Kendaraan (index) | `/admin/vehicles` | `resources/views/livewire/admin/vehicles/` |
| 5 | Transaksi Kasir / POS | `/admin/pos` | `resources/views/livewire/admin/pos/` |
| 6 | Booking (index/calendar) | `/admin/bookings`, `/admin/bookings/calendar` | `resources/views/livewire/admin/bookings/` |
| 7 | Invoice (index/create/detail) | `/admin/invoices*` | `resources/views/livewire/admin/invoices/` |
| 8 | **Dashboard Segmentasi RFM** | `/admin/rfm` | `resources/views/livewire/admin/rfm/index.blade.php` |
| 9 | Laporan | `/admin/reports` | `resources/views/livewire/admin/reports/` |
| 10 | Pengaturan | `/admin/settings` | `resources/views/livewire/admin/settings/` |

Halaman **Dashboard Segmentasi RFM** (§7 nomor 8) khususnya sudah memiliki:
- Stat cards (total pelanggan per source, rata-rata monetary)
- Tab sumber data (`bengkel` / `combined`)
- Filter cluster & search nama pelanggan
- Tabel analitik cluster (rata-rata R/F/M score, recency, frequency, monetary per cluster)
- Tren bulanan per cluster dari `rfm_history`
- **Scatter plot K-Means (Chart.js)** — R score vs F score, satu dataset per cluster dengan warna
  sesuai `color_hex` di `cluster_definitions` (ditambahkan di commit `0994111`, dengan
  `wire:ignore` untuk mencegah chart rusak saat filter berubah, commit `f8587e0`)
- Export CSV hasil RFM

Untuk pemakaian di Bab V (bukti pengujian UI), urutan screenshot yang disarankan: Login →
Dashboard admin → Kelola Pelanggan → Kelola Kendaraan → Transaksi Kasir → Dashboard Segmentasi RFM
(termasuk scatter plot), sesuai urutan alur bisnis dari yang paling dasar ke fitur analitik utama
skripsi/TA ini.
