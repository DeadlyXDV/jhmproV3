# MEMORY.md — JHMPro Project Shared Memory

> File ini adalah **sumber kebenaran bersama** untuk semua AI provider (Claude, Codex, Gemini, dll).
> WAJIB diperbarui setiap kali: task selesai, keputusan arsitektur berubah, atau bug krusial ditemukan.
> Jangan ubah bagian yang tidak terkait — cukup perbarui statusnya saja.

---

## 0. AI Task Ownership

> Tabel ini mendefinisikan pembagian scope kerja antar AI. Patuhi batas ini untuk menghindari konflik.

| AI | Scope | DILARANG menyentuh |
|---|---|---|
| **Claude** | Backend PHP: Models, Controllers, Livewire component class (`.php`), Migrations, Routes, Tests, Auth | File Blade/view murni yang sudah dikerjakan Antigravity, aset CSS/JS murni |
| **Antigravity** | Frontend: file Blade (`.blade.php`), Tailwind class di view, komponen UI, layout, design system | File `.php` (Model, Controller, Livewire class), Migrations, Routes, Tests |

---

## CURRENT SESSION

- **Sedang dikerjakan:** Test plan RFM & K-Means untuk Tugas Akhir — NFR tests selesai
- **File yang dimodifikasi (Claude, 2026-07-01):**
  - `tests/Feature/RfmKMeansBoundaryTest.php` — BARU: 4 test K-Means boundary value (n<k, n=k, n>k) + ranking cluster (A1+A2)
  - `tests/Feature/CalculateRfmCommandTest.php` — Tambah range assertion skor 1-5 (B.1) + test pelanggan 0 transaksi (B.2)
  - `tests/Feature/AdminRfmIndexTest.php` — Tambah empty state test (B.3)
  - `tests/Feature/RfmPerformanceTest.php` — BARU (2026-07-01): 8 NFR test (PERF-001, STRESS-001a/b/c, ENDR-001, REL-001, SEC-001a/b)
- **Total test:** 155 pass, 1 skip, 0 fail
- **AI sebelumnya:** Claude → Gemini (Antigravity) → Claude (session ini)
- **NFR gap dilaporkan:**
  - Rate limiting: tidak ada throttle middleware di route `/rfm` (hanya login yang di-throttle via Fortify)
  - Data isolation: tidak ada multi-tenancy, isolasi hanya di level role (bukan per-user/tenant) — test dianggap tidak relevan

---

## 1. Ringkasan Produk

**JHMPro** adalah sistem manajemen bengkel motor modifikasi milik keluarga (single bengkel, bukan multi-tenant).

Fitur utama: invoice, POS kasir, work order, booking, inventory, customer/vehicle management, RFM analytics.

---

## 2. Tech Stack

| Layer | Teknologi |
|---|---|
| Backend | PHP 8.4 + Laravel 13 |
| Frontend | Livewire 4 + Alpine.js + Tailwind CSS v4 |
| Auth | Laravel Fortify (customer) + custom AdminAuthController (admin) |
| UI Components | Flux UI v2 (Livewire-native) |
| Testing | PestPHP v4 + PHPUnit v12 |
| DB (dev) | MySQL lokal |
| DB (prod) | PostgreSQL (Heroku) |
| Linter | Laravel Pint v1 |
| Static Analysis | Larastan v3 |

**PENTING:** Proyek ini **TIDAK menggunakan Filament**. Admin panel dibangun custom dengan Blade + Livewire + Tailwind.

---

## 3. Arsitektur Auth

- Guard `web` → customer, login di `/login` via Fortify
- Guard `admin` → super_admin / admin / mekanik, login di `/admin/login` via `AdminAuthController`
- Dua sesi terpisah, tabel `users` sama
- Middleware `CheckRole` (alias `role`) terdaftar di `bootstrap/app.php`
- `CreateNewUser` hardcode `role='customer'` untuk mencegah eskalasi privilege

---

## 4. Design System

- Sidebar: `bg-[#111827]`, nav aktif: `bg-red-600 text-white`
- Brand primary: `#DC2626` (red-600)
- Page background: `#F3F4F6`, card background: `#FFFFFF`
- Font: Inter
- Ikon: **Heroicons** via `blade-ui-kit/blade-heroicons` — **DILARANG** pakai emoji sebagai ikon
- Status indicator: `<span class="w-2 h-2 rounded-full bg-green-500"></span>` bukan emoji

---

## 5. Struktur Direktori Kunci

```
app/
  Http/
    Controllers/Admin/AuthController.php   ← login/logout admin
    Middleware/CheckRole.php               ← cek role + guard
  Livewire/Admin/                          ← semua komponen admin
  Livewire/Mekanik/                        ← komponen mekanik
  Models/                                  ← semua Eloquent model
resources/views/
  layouts/admin.blade.php                  ← layout sidebar dark
  layouts/mekanik.blade.php                ← layout sidebar mekanik
  components/admin/nav-item.blade.php
routes/web.php                             ← semua route (web + admin + mekanik)
```

---

## 6. Status Development

**Fase saat ini:** Fase 5 SELESAI 100% · Fase 4 DI-SKIP · Fase 6 belum dimulai | **Terakhir diperbarui:** 2026-06-17

> **Sumber kebenaran rencana lengkap:** `/home/voldemort/Downloads/plan.md` (4092 baris). Path ini hanya bisa diakses di mesin developer — tidak bisa diakses AI lain.

---

### ✅ FASE 1 — FONDASI (Hampir Selesai)

#### Database / Migrasi
- `bookings.source`: ubah enum → `('website','whatsapp','walk_in')`
- `bookings.status`: tambah nilai `'in_progress'`
- `work_orders.wo_number`: kolom baru format `WO-NNNN`
- `settings` tabel baru (key-value store, PK = `key`)
- Semua alter migrations diproteksi SQLite guard untuk test compatibility
- `create_core_tables` migration untuk SQLite in-memory test env

#### Models (semua ada di `app/Models/`)
User, Customer, Partner, Vehicle, VehicleEngineSpec, VehicleModificationLog,
Service, Invoice, InvoiceItem, WorkOrder, Booking, BookingService, BookingSlot,
Sparepart, SparepartCategory, StockMovement, ProductBundle, ProductBundleItem,
Order, OrderItem, Payment, Shipment, ClusterDefinition, CustomerRfm, RfmHistory, Setting

#### Observer
- `VehicleObserver`: auto-create `vehicle_engine_specs` saat kendaraan dibuat — terdaftar di `AppServiceProvider`

#### Livewire Admin Panel — Sudah Implementasi Penuh
| Komponen | Path |
|---|---|
| AdminDashboard | `app/Livewire/Admin/Dashboard.php` |
| CustomerIndex/Create/Detail | `app/Livewire/Admin/Customers/` |
| PartnerIndex | `app/Livewire/Admin/Partners/PartnerIndex.php` |
| VehicleIndex/Create/Detail | `app/Livewire/Admin/Vehicles/` |
| UserIndex | `app/Livewire/Admin/Users/UserIndex.php` |
| WorkOrderIndex + WorkOrderDetail | `app/Livewire/Admin/WorkOrders/` |
| InvoiceIndex + InvoiceDetail | `app/Livewire/Admin/Invoices/` |
| BookingIndex + BookingCalendar | `app/Livewire/Admin/Bookings/` |
| ServiceIndex | `app/Livewire/Admin/Services/ServiceIndex.php` |
| SparepartCategoryIndex | `app/Livewire/Admin/SparepartCategories/` |
| SparepartIndex | `app/Livewire/Admin/Spareparts/SparepartIndex.php` |
| StockMovementIndex | `app/Livewire/Admin/StockMovements/StockMovementIndex.php` |
| ProductBundleIndex | `app/Livewire/Admin/ProductBundles/ProductBundleIndex.php` |
| OrderIndex | `app/Livewire/Admin/Orders/OrderIndex.php` |
| RfmIndex | `app/Livewire/Admin/Rfm/RfmIndex.php` |
| ReportIndex | `app/Livewire/Admin/Reports/ReportIndex.php` |
| SettingIndex | `app/Livewire/Admin/Settings/SettingIndex.php` |
| PosPage | `app/Livewire/Admin/Pos/PosPage.php` |

#### Livewire Mekanik Panel — Sudah Implementasi Penuh
| Komponen | Path |
|---|---|
| MekanikDashboard | `app/Livewire/Mekanik/Dashboard.php` |
| MekanikWorkOrderIndex | `app/Livewire/Mekanik/WorkOrders/WorkOrderIndex.php` |
| MekanikWorkOrderDetail | `app/Livewire/Mekanik/WorkOrders/MekanikWorkOrderDetail.php` |

#### Security
- `UserIndex.toggleActive()`: verifikasi `super_admin` dilakukan server-side
- `PosPage.addToCart()`: harga resolve dari DB, bukan dari client
- `SettingIndex.openUserCreate/Edit()`: dilindungi `abort_unless(isSuperAdmin())`

#### Tests
- **72/73 pass, 1 skip, 0 fail**
- Factories: CustomerFactory, OrderFactory, SparepartFactory, ServiceFactory, InvoiceFactory + states di UserFactory (superAdmin/admin/mekanik/customer)

#### ✅ Fase 1 SELESAI 100%

- [x] **`InvoiceCreate`** — implementasi penuh: tipe (walk_in/booking/partner), customer search + inline baru, kendaraan dropdown, repeater items (service/sparepart, qty + harga override), discount, payment (metode + jumlah), post-save: stok berkurang + stock_movements, redirect ke detail
- [x] **Seeder users** — `UserSeeder`: super_admin@jhmpro.test, admin@jhmpro.test, mekanik@jhmpro.test (semua password: `password`)

---

### ✅ FASE 2 — OPERASIONAL BENGKEL (Selesai)

- [x] Stock movement **observer** otomatis — `InvoiceItemObserver` dibuat di `app/Observers/`; dihapus dari InvoiceCreate + PosPage
- [x] Alert **stok minimum** di dashboard — `AdminDashboard` sudah ada `stokKritis` (`whereColumn('stock', '<=', 'minimum_stock')`)
- [x] **Modification log** otomatis saat spek mesin diupdate — `VehicleDetail.saveSpecs()` sudah auto-create log
- [x] **Export CSV laporan keuangan** — `ReportIndex.exportCsv()` download CSV dengan filter tanggal + tipe aktif

---

### ✅ FASE 3 — BOOKING ONLINE (Selesai)

- [x] **Booking slot generator** — `booking:generate-slots` Artisan command; jadwal harian via `routes/console.php`; setting via `Setting::get('booking_kapasitas', 'booking_advance_days', 'booking_hari')`
- [x] **Halaman booking publik** `/booking` — 3 step: pilih kendaraan (inline add) → pilih tanggal dari kalender slot → konfirmasi + submit dengan `lockForUpdate` race condition protection
- [x] **Integrasi booking → invoice** — `BookingIndex.createInvoice(int $id)` redirect ke InvoiceCreate; `InvoiceCreate.mount()` auto-load booking via `from_booking` query param
- [x] **Customer auto-create** — `BookingPage.mount()` auto-buat record Customer jika user customer belum punya record
- [x] **Sidebar customer** — link "Booking Servis" sudah ditambahkan
- [x] **Tests:** 20 test baru (7 AdminBookingIndex + 9 WebsiteBookingPage + 4 GenerateBookingSlots command)

---

### ⏳ FASE 4 — ONLINE SHOP & PAYMENT (Belum Dimulai)

- [ ] **Katalog produk publik** `/shop` — sparepart + bundle WHERE `is_sold_online = true`
- [ ] **Detail produk** `/shop/{slug}`
- [ ] **Keranjang belanja** (Livewire, session-based atau DB)
- [ ] **Checkout** → buat `Order` + `OrderItems`
- [ ] **Integrasi Midtrans Snap** — generate snap_token, redirect ke payment page
- [ ] **Webhook Midtrans** — update `payment_status` order/invoice saat pembayaran berhasil
- [ ] **Integrasi Shipbite** — hitung ongkir, buat shipment, dapat tracking number
- [ ] **Webhook Shipbite** — update status pengiriman
- [ ] **Halaman tracking order** `/orders/{id}` di akun customer

---

### ✅ FASE 5 — RFM & ANALITIK (Selesai)

- [x] **Scheduled job RFM** (daily 00:00) — `rfm:calculate` command; hitung R/F/M per customer, sumber `bengkel` + `combined`
- [x] **K-Means clustering** (inline PHP, tanpa library) — assign cluster ke setiap customer; label berdasarkan centroid sum ranking
- [x] **Export CSV per segmen** dari RfmIndex — `exportCsv()` di RfmIndex, filter by source + cluster
- [x] **Config RFM** — `k_clusters`, `weight_r/f/m`, `period_months` via SettingIndex (super_admin only)
- [ ] **Badge segmen** di profil customer (website publik) — ditunda ke Fase 6

---

### ⏳ FASE 6 — POLISH (Belum Dimulai)

- [ ] **Notifikasi WhatsApp** (Fonnte atau WA Cloud API) — konfirmasi booking, invoice lunas, dll
- [ ] **Website Publik lengkap** — landing page, profil customer, motor saya, riwayat booking/order

---

### 🌐 WEBSITE PUBLIK — Scope Lengkap (Belum Ada)

Semua halaman ini belum dibuat sama sekali. Guard `web`, Livewire + Tailwind.

| Halaman | Route | Keterangan |
|---|---|---|
| Landing Page | `/` | Hero, layanan unggulan, produk featured, info bengkel |
| Login/Register | `/login`, `/register` | Fortify — hanya buat akun `customer` |
| Dashboard Customer | `/dashboard` | Booking aktif, order terbaru, badge segmen RFM |
| Motor Saya | `/vehicles` | List + tambah kendaraan |
| Detail Motor | `/vehicles/{id}` | Spek mesin (read-only) + history modifikasi timeline |
| Booking Online | `/booking` | 3-step: kendaraan → tanggal → konfirmasi |
| Riwayat Booking | `/bookings` | List booking milik customer, bisa cancel jika pending |
| Toko Online | `/shop` | Katalog sparepart + bundle online |
| Detail Produk | `/shop/{slug}` | Foto, deskripsi, harga, tambah ke keranjang |
| Keranjang | `/cart` | Review item, pilih pengiriman, checkout |
| Riwayat Order | `/orders` | List order + status pengiriman + no resi |
| Profil | `/profile` | Edit data diri, ganti password, badge segmen RFM |

---

## 7. Keputusan Arsitektur Penting

| Keputusan | Alasan |
|---|---|
| Custom admin panel (bukan Filament) | Kontrol penuh atas desain & business logic bengkel spesifik |
| Dua guard auth terpisah | Keamanan: customer tidak bisa akses route admin |
| Livewire untuk semua interaksi | Minim JavaScript, state server-side, konsisten |
| Alter migration + SQLite guard | Agar CI/CD dan test suite tidak pecah di env lain |
| Enum `source` booking diperluas | Tambah channel `whatsapp` dan `walk_in` selain website |
| `CustomerRfm.$table = 'customer_rfm'` | Tabel di DB dibuat singular (bukan `customer_rfms`); wajib override agar Eloquent tidak cari tabel yang salah |
| `RfmHistory.$table = 'rfm_history'` | Sama — tabel di DB singular `rfm_history`, bukan `rfm_histories` |
| `Setting` model key-value (tabel `settings`) | Tidak ada tabel settings di DB awal; dibuat migration baru dengan PK `key` (string) untuk menyimpan config bengkel, booking, dll |
| `PosPage` pakai `layouts/pos.blade.php` | POS fullscreen tanpa sidebar/topbar admin — layout terpisah sesuai plan |
| `ReportIndex` trend: kondisional semua driver | `strftime` (SQLite) / `DATE_FORMAT` (MySQL) / `TO_CHAR` (PostgreSQL) — auto-detect via `DB::getDriverName()` |
| `BookingSlot.tanggal` **tidak di-cast** ke `date` | Jika di-cast, SQLite menyimpan sebagai `Y-m-d H:i:s` sehingga `where('tanggal', 'Y-m-d')` gagal. Kolom dibiarkan sebagai string agar query `->where('tanggal', $date->toDateString())` bekerja di semua driver |

---

## 8. Konvensi Wajib

- Jalankan `vendor/bin/pint --dirty --format agent` setiap kali modifikasi file PHP
- Setiap perubahan kode **harus ada test** — jalankan dengan `php artisan test --compact`
- Gunakan `php artisan make:` untuk membuat file baru
- Route: selalu gunakan named routes + fungsi `route()`
- Jangan commit file `.env` atau credentials

### Frontend (Gemini)

- Ikon: **selalu** gunakan Heroicons via `blade-ui-kit/blade-heroicons` — **jangan pakai emoji** sebagai ikon dalam kondisi apapun
- Class Tailwind harus konsisten dengan design system di section 4: sidebar `bg-[#111827]`, brand `#DC2626`, page bg `#F3F4F6`, card bg `#FFFFFF`, font Inter
- **Jangan modifikasi file PHP** (`.php`) — serahkan ke Claude. Perubahan frontend cukup di file `.blade.php` dan asset CSS/JS

---

## 9. Referensi

- **Rencana lengkap:** `/home/voldemort/Downloads/plan.md` (4000+ baris — sumber kebenaran utama)
  > ⚠️ Path ini adalah **path lokal mesin developer** dan **tidak dapat diakses oleh AI lain** (Gemini, Codex, dll). Jika ada keputusan penting dari `plan.md` yang relevan untuk dikerjakan AI lain, ringkasannya harus didokumentasikan langsung di file `MEMORY.md` ini — khususnya di section 7 (Keputusan Arsitektur) atau section 6 (Status Development).
- **Git log:** `git log --oneline` untuk melihat semua commit
- **Test:** `php artisan test --compact`

---

## 10. Interface Contracts

> Dokumentasikan kontrak setiap Livewire component di sini: public properties, events yang di-emit, dan wire actions yang tersedia. Update saat komponen selesai diimplementasi.

### Template entri

```
### NamaKomponen
- **Path:** `app/Livewire/.../NamaKomponen.php`
- **View:** `resources/views/livewire/.../nama-komponen.blade.php`
- **Public properties:** daftar `$properti` beserta tipenya
- **Wire actions:** daftar method yang dipanggil dari view (`wire:click`, `wire:submit`, dll)
- **Events emitted:** daftar event yang di-dispatch (jika ada)
- **Events listened:** daftar event yang di-listen (jika ada)
```

---

### WorkOrderIndex (Admin)

- **Path:** `app/Livewire/Admin/WorkOrders/WorkOrderIndex.php`
- **View:** `resources/views/livewire/admin/work-orders/index.blade.php`
- **Status:** ✅ Implemented
- **Public properties:** `$search` (string), `$filterStatus` (string)
- **Wire actions:** `updatedSearch()`, `updatedFilterStatus()`
- **Kolom:** WO Number, Customer, Kendaraan (plat+merk/model), Mekanik, Status badge, Mulai, Selesai

### WorkOrderDetail (Admin)

- **Path:** `app/Livewire/Admin/WorkOrders/WorkOrderDetail.php`
- **View:** `resources/views/livewire/admin/work-orders/detail.blade.php`
- **Status:** ✅ Implemented
- **Public properties:** `$workOrderId` (int), `$status` (string), `$mekanikId` (?int), `$catatanMekanik` (string)
- **Wire actions:** `save()` — update status, mekanik, catatan; auto-set `mulai_at`/`selesai_at`

### MekanikWorkOrderDetail

- **Path:** `app/Livewire/Mekanik/WorkOrders/MekanikWorkOrderDetail.php`
- **View:** `resources/views/livewire/mekanik/work-orders/detail.blade.php`
- **Status:** ✅ Implemented
- **Public properties:** `$workOrderId` (int), `$status` (string), `$catatanMekanik` (string)
- **Wire actions:** `updateStatus(string $newStatus)`, `saveCatatan()` — keduanya dilindungi `abort_unless` per-mekanik

### InvoiceIndex

- **Path:** `app/Livewire/Admin/Invoices/InvoiceIndex.php`
- **View:** `resources/views/livewire/admin/invoices/index.blade.php`
- **Status:** ✅ Implemented
- **Public properties:** `$search` (string), `$filterStatus` (string), `$filterTipe` (string)
- **Wire actions:** `updatedSearch()`, `updatedFilterStatus()`, `updatedFilterTipe()`

### InvoiceDetail

- **Path:** `app/Livewire/Admin/Invoices/InvoiceDetail.php`
- **View:** `resources/views/livewire/admin/invoices/detail.blade.php`
- **Status:** ✅ Implemented (read-only)
- **Public properties:** `$invoiceId` (int)
- **Menampilkan:** item list, subtotal/diskon/grand total/sisa, riwayat payment, link ke WO terkait

### BookingIndex

- **Path:** `app/Livewire/Admin/Bookings/BookingIndex.php`
- **View:** `resources/views/livewire/admin/bookings/index.blade.php`
- **Status:** ✅ Implemented
- **Public properties:** `$search` (string), `$filterStatus` (string), `$filterSource` (string)
- **Wire actions:** `confirm(int $id)`, `cancel(int $id)`, `createInvoice(int $id)`, `updatedSearch()`, `updatedFilterStatus()`, `updatedFilterSource()`

### BookingCalendar

- **Path:** `app/Livewire/Admin/Bookings/BookingCalendar.php`
- **View:** `resources/views/livewire/admin/bookings/calendar.blade.php`
- **Status:** ✅ Implemented
- **Public properties:** `$year` (int), `$month` (int)
- **Wire actions:** `previousMonth()`, `nextMonth()`
- **Catatan:** Grid kalender dimulai Senin; booking dikelompok per tanggal, maks 3 ditampilkan + counter lebih

### ServiceIndex

- **Path:** `app/Livewire/Admin/Services/ServiceIndex.php`
- **View:** `resources/views/livewire/admin/services/index.blade.php`
- **Status:** ✅ Implemented
- **Public properties:** `$search`, `$showForm`, `$editingId`, `$namaService`, `$deskripsi`, `$hargaDefault`, `$durasiEstimasi`, `$isActive`, `$isBookable`
- **Wire actions:** `openCreate()`, `openEdit(int $id)`, `save()`, `toggleActive(int $id)`, `toggleBookable(int $id)`, `cancelForm()`

### SparepartCategoryIndex

- **Path:** `app/Livewire/Admin/SparepartCategories/SparepartCategoryIndex.php`
- **View:** `resources/views/livewire/admin/sparepart-categories/index.blade.php`
- **Status:** ✅ Implemented
- **Public properties:** `$search`, `$showForm`, `$editingId`, `$name`, `$slug` (auto dari name), `$parentId`, `$description`
- **Wire actions:** `openCreate()`, `openEdit(int $id)`, `save()`, `cancelForm()`

### SparepartIndex

- **Path:** `app/Livewire/Admin/Spareparts/SparepartIndex.php`
- **View:** `resources/views/livewire/admin/spareparts/index.blade.php`
- **Status:** ✅ Implemented
- **Public properties:** `$search`, `$filterCategory`, `$filterCritical` (bool)
- **Wire actions:** `toggleActive(int $id)`
- **Catatan:** Filter kritis pakai `whereColumn('stock', '<=', 'minimum_stock')`

### StockMovementIndex

- **Path:** `app/Livewire/Admin/StockMovements/StockMovementIndex.php`
- **View:** `resources/views/livewire/admin/stock-movements/index.blade.php`
- **Status:** ✅ Implemented (read-only)
- **Public properties:** `$search`, `$filterType` (in/out/adjustment)

### ProductBundleIndex

- **Path:** `app/Livewire/Admin/ProductBundles/ProductBundleIndex.php`
- **View:** `resources/views/livewire/admin/product-bundles/index.blade.php`
- **Status:** ✅ Implemented
- **Public properties:** `$search`, `$showForm`, `$editingId`, `$nama`, `$slug`, `$harga`, `$deskripsi`, `$isActive`, `$isBookable`, `$isSoldOnline`
- **Wire actions:** `openCreate()`, `openEdit(int $id)`, `save()`, `toggleActive(int $id)`, `toggleBookable(int $id)`, `toggleSoldOnline(int $id)`, `cancelForm()`

---

## 11. Known Issues

> Catat bug atau masalah yang ditemukan namun belum diperbaiki. Hapus baris saat sudah resolved.

| Status | Komponen | Deskripsi | Ditemukan di commit |
|---|---|---|---|
| 🔍 Perlu verifikasi | UserIndex | Toggle active hanya diproteksi di server-side; pastikan UI tidak render tombol untuk non-super_admin | `77c3f8f` |
| 🔍 Perlu verifikasi | tests/Browser/sidebar.spec.js | Assert `active state styling` gagal karena ekspresi reguler `/bg-\\[#E11D22\\]/` mencari literal backslash di string class, padahal class di DOM adalah `bg-[#E11D22]`. | `2026-06-29` |
| ✅ Fixed | PartnerIndex | Tambah Create/Edit inline form — openCreate/openEdit/save/cancelForm | 2026-06-17 |
| ✅ Fixed | SparepartIndex | Tambah Create/Edit inline form — semua field kecuali images/dimensi; SKU auto-uppercase | 2026-06-17 |
| ✅ Fixed | CustomerRfm | Tabel DB bernama `customer_rfm` (singular) — sudah ditambahkan `$table = 'customer_rfm'` di model | session 2026-06-15 |
| ✅ Fixed | RfmHistory | Tabel DB bernama `rfm_history` (singular) — sudah ditambahkan `$table = 'rfm_history'` di model | session 2026-06-15 |
| ✅ Fixed | PosPage | XSS: `addslashes()` diganti `Js::from()` di 3 titik `wire:click` pada blade POS | `d856758` |
| ✅ Fixed | PosPage | Price manipulation: `addToCart()` sekarang hanya terima `$id`+`$tipe`, harga/nama di-resolve dari DB; `buatInvoice()` juga re-resolve canonical price | `d856758` |
| ✅ Fixed | SettingIndex | Missing auth: `openUserCreate/Edit()` kini dilindungi `abort_unless(isSuperAdmin())` + `abort_if(super_admin)` | `d856758` |
