# MEMORY.md — JHMPro Project Shared Memory

> File ini adalah **sumber kebenaran bersama** untuk semua AI provider (Claude, Codex, Gemini, dll).
> WAJIB diperbarui setiap kali: task selesai, keputusan arsitektur berubah, atau bug krusial ditemukan.
> Jangan ubah bagian yang tidak terkait — cukup perbarui statusnya saja.

---

## 0. AI Task Ownership

> Tabel ini mendefinisikan pembagian scope kerja antar AI. Patuhi batas ini untuk menghindari konflik.

| AI | Scope | DILARANG menyentuh |
|---|---|---|
| **Claude** | Backend PHP: Models, Controllers, Livewire component class (`.php`), Migrations, Routes, Tests, Auth | File Blade/view murni yang sudah dikerjakan Gemini, aset CSS/JS murni |
| **Gemini** | Frontend: file Blade (`.blade.php`), Tailwind class di view, komponen UI, layout, design system | File `.php` (Model, Controller, Livewire class), Migrations, Routes, Tests |

---

## CURRENT SESSION

- **Sedang dikerjakan:** Selesai perbaikan login page dan optimasi performa UI.
- **File yang terakhir dimodifikasi:** `resources/views/admin/auth/login.blade.php`, `resources/css/app.css`, `resources/views/layouts/admin.blade.php`
- **Berhenti di:** Implementasi final Dashboard, Sidebar, dan Login (Fix Password Toggle, CLS 0, No FOUC, Instant Navigation).
- **AI sebelumnya:** Gemini

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

**Fase saat ini:** Fase 1 (~99% selesai) | **Terakhir diperbarui:** 2026-06-15

### SELESAI ✅

#### Database / Migrasi
- `bookings.source`: ubah enum → `('website','whatsapp','walk_in')`
- `bookings.status`: tambah nilai `'in_progress'`
- `work_orders.wo_number`: kolom baru format `WO-NNNN`
- Semua alter migrations diproteksi SQLite guard untuk test compatibility

#### Models (semua ada di `app/Models/`)
User, Customer, Partner, Vehicle, VehicleEngineSpec, VehicleModificationLog,
Service, Invoice, InvoiceItem, WorkOrder, Booking, BookingService, BookingSlot,
Sparepart, SparepartCategory, StockMovement, ProductBundle, ProductBundleItem,
Order, OrderItem, Payment, Shipment, ClusterDefinition, CustomerRfm, RfmHistory

#### Observer
- `VehicleObserver`: auto-create `vehicle_engine_specs` saat kendaraan dibuat — terdaftar di `AppServiceProvider`

#### Livewire — Sudah Implementasi Penuh
| Komponen | Path |
|---|---|
| AdminDashboard | `app/Livewire/Admin/Dashboard.php` |
| CustomerIndex | `app/Livewire/Admin/Customers/CustomerIndex.php` |
| CustomerCreate | `app/Livewire/Admin/Customers/CustomerCreate.php` |
| CustomerDetail | `app/Livewire/Admin/Customers/CustomerDetail.php` |
| PartnerIndex | `app/Livewire/Admin/Partners/PartnerIndex.php` |
| VehicleIndex | `app/Livewire/Admin/Vehicles/VehicleIndex.php` |
| VehicleDetail | `app/Livewire/Admin/Vehicles/VehicleDetail.php` |
| UserIndex | `app/Livewire/Admin/Users/UserIndex.php` |
| MekanikDashboard | `app/Livewire/Mekanik/Dashboard.php` |
| MekanikWorkOrderIndex | `app/Livewire/Mekanik/WorkOrders/WorkOrderIndex.php` |
| WorkOrderIndex (admin) | `app/Livewire/Admin/WorkOrders/WorkOrderIndex.php` |
| WorkOrderDetail (admin) | `app/Livewire/Admin/WorkOrders/WorkOrderDetail.php` |
| MekanikWorkOrderDetail | `app/Livewire/Mekanik/WorkOrders/MekanikWorkOrderDetail.php` |
| InvoiceIndex | `app/Livewire/Admin/Invoices/InvoiceIndex.php` |
| InvoiceDetail | `app/Livewire/Admin/Invoices/InvoiceDetail.php` |
| BookingIndex | `app/Livewire/Admin/Bookings/BookingIndex.php` |
| BookingCalendar | `app/Livewire/Admin/Bookings/BookingCalendar.php` |
| ServiceIndex | `app/Livewire/Admin/Services/ServiceIndex.php` |

#### Security
- `UserIndex.toggleActive()`: verifikasi `super_admin` dilakukan server-side

#### Tests
- **29/30 pass, 1 skip, 0 fail**

---

### BELUM SELESAI — HARUS DILANJUTKAN ⏳

Semua item di bawah masih berupa **stub** (kelas kosong, view placeholder):

#### Prioritas 1 — Inti Operasional ✅ SELESAI
- [x] `WorkOrderIndex` (admin) + `WorkOrderDetail` (admin)
- [x] `MekanikWorkOrderDetail`
- [x] `InvoiceIndex` + `InvoiceDetail` (InvoiceCreate masih stub)

#### Prioritas 2 ✅ SELESAI
- [x] `BookingIndex` — tabel + search + filter status/channel + confirm/cancel inline
- [x] `BookingCalendar` — grid bulan, navigasi prev/next, booking per hari dengan dot warna status
- [x] `ServiceIndex` — tabel + toggle aktif/bookable + form tambah/edit inline

#### Prioritas 3 ✅ SELESAI
- [x] `SparepartCategoryIndex` — list + form tambah/edit inline + parent category + slug auto-generate
- [x] `SparepartIndex` — tabel + search (SKU/nama/brand) + filter kategori + filter stok kritis + toggle aktif
- [x] `StockMovementIndex` — log read-only + search sparepart + filter tipe (in/out/adjustment)
- [x] `ProductBundleIndex` — list + form tambah/edit + toggle aktif/bookable/online

#### Prioritas 4 — Laporan & Lain-lain
- [ ] `RfmIndex`, `ReportIndex`, `SettingIndex`, `OrderIndex`, `PosPage`

#### Lain-lain
- [ ] Seeder/fixture users (pastikan role + is_active benar)
- [ ] Test: admin login flow
- [ ] Test: CRUD Customer

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
- **Wire actions:** `confirm(int $id)`, `cancel(int $id)`, `updatedSearch()`, `updatedFilterStatus()`, `updatedFilterSource()`

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
| ✅ Fixed | CustomerRfm | Tabel DB bernama `customer_rfm` (singular) — sudah ditambahkan `$table = 'customer_rfm'` di model | session 2026-06-15 |
| ✅ Fixed | RfmHistory | Tabel DB bernama `rfm_history` (singular) — sudah ditambahkan `$table = 'rfm_history'` di model | session 2026-06-15 |
