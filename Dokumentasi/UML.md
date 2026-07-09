# JHMPro — UML Diagrams (PlantUML)

> Dibuat berdasarkan kondisi aktual kode per **2026-07-05**: `app/Models/*`, `database/migrations/*`, `routes/web.php`, `routes/settings.php`, `app/Livewire/**`, `app/Http/Middleware/CheckRole.php`, `app/Providers/FortifyServiceProvider.php`, `app/Observers/*`, `app/Console/Commands/*`.
>
> Semua elemen (atribut, tipe, enum, relasi, use case, alur) diambil langsung dari source code — bukan asumsi. Catatan ketidaksesuaian yang ditemukan di kode asli (mis. mismatch enum) ditandai eksplisit dengan komentar `note` di diagram terkait, **tidak diperbaiki** di sini karena di luar scope dokumen ini.
>
> Render: paste tiap blok ```plantuml``` ke [PlantUML online server](https://www.plantuml.com/plantuml) atau plugin PlantUML di IDE.

---

## 1. Use Case Diagram

Aktor didasarkan pada 2 guard auth (`web` untuk customer, `admin` untuk staff) dan kolom `users.role` (`super_admin`, `admin`, `mekanik`, `customer`), sesuai `CheckRole` middleware dan pengelompokan route di `routes/web.php`. "Scheduler" adalah aktor sistem yang memicu Artisan command terjadwal (bukan user manusia).

`SuperAdmin` adalah generalisasi dari `Admin` — setiap use case yang bisa diakses `Admin` otomatis bisa diakses `SuperAdmin`, ditambah use case eksklusif (RFM, Laporan, Pengaturan, Kelola Akun). Relasi ini digambar sekali di **1.1** dan tidak diulang di sub-diagram lain agar tetap ringkas.

Gaya visual mengikuti konvensi diagram use case klasik: oval polos garis hitam (tanpa warna isi), kotak sistem bertitel di tengah, aktor (stick-figure) di kiri/kanan yang terhubung dengan garis polos (tanpa panah) ke use case, dan relasi `<<include>>`/`<<extend>>` digambar sebagai panah putus-putus berlabel. Dipecah menjadi 6 sub-diagram per modul agar tetap mudah dibaca (bukan satu diagram raksasa).

### 1.1 Aktor & Autentikasi

```plantuml
@startuml UC_Auth
skinparam backgroundColor white
skinparam shadowing false
skinparam ArrowColor black
skinparam usecase {
  BackgroundColor white
  BorderColor black
}
skinparam actor {
  BackgroundColor white
  BorderColor black
}
skinparam rectangle {
  BackgroundColor white
  BorderColor black
}

actor "Super Admin" as SuperAdmin
actor "Admin" as Admin
actor "Mekanik" as Mekanik
actor "Pelanggan\n(Customer)" as Customer

SuperAdmin --|> Admin

rectangle "Autentikasi (Fortify, guard admin/web)" {
  usecase "Login" as UC_Login
  usecase "Logout" as UC_Logout
  usecase "Registrasi Akun" as UC_Register
  usecase "Lupa / Reset Password" as UC_ForgotPassword
  usecase "Verifikasi Two-Factor" as UC_2FA
  usecase "Kelola Profil & Keamanan" as UC_Profile
}

Admin -- UC_Login
Admin -- UC_Logout
Mekanik -- UC_Login
Mekanik -- UC_Logout
Customer -- UC_Login
Customer -- UC_Logout
Customer -- UC_Register
Customer -- UC_ForgotPassword
Customer -- UC_Profile
Customer -- UC_2FA

UC_Login ..> UC_2FA : <<extend>>

note bottom of UC_Profile
  Route /settings/* pakai guard default "web",
  sehingga hanya Pelanggan yang punya menu ini
end note

@enduml
```

### 1.2 Pelanggan, Kendaraan & Partner

```plantuml
@startuml UC_MasterData
skinparam backgroundColor white
skinparam shadowing false
skinparam ArrowColor black
skinparam usecase {
  BackgroundColor white
  BorderColor black
}
skinparam actor {
  BackgroundColor white
  BorderColor black
}
skinparam rectangle {
  BackgroundColor white
  BorderColor black
}

actor "Admin" as Admin

rectangle "Data Pelanggan & Kendaraan" {
  usecase "Lihat Dashboard Admin" as UC_AdminDashboard
  usecase "Kelola Data Pelanggan\n(CRUD)" as UC_Customer
  usecase "Lihat Detail Pelanggan" as UC_CustomerDetail
  usecase "Kelola Data Kendaraan\n(CRUD)" as UC_Vehicle
  usecase "Kelola Spesifikasi Mesin" as UC_EngineSpec
  usecase "Catat Log Modifikasi\nKendaraan" as UC_ModLog
  usecase "Kelola Data Partner\nBengkel (CRUD)" as UC_Partner
}

Admin -- UC_AdminDashboard
Admin -- UC_Customer
Admin -- UC_CustomerDetail
Admin -- UC_Vehicle
Admin -- UC_Partner

UC_Vehicle ..> UC_EngineSpec : <<include>>
UC_EngineSpec ..> UC_ModLog : <<include>>

note bottom of UC_EngineSpec
  Auto-create record kosong saat kendaraan
  baru dibuat (VehicleObserver::created)
end note
note bottom of UC_ModLog
  Auto-create log saat spek mesin
  diupdate (VehicleDetail::saveSpecs())
end note

@enduml
```

### 1.3 Layanan, Inventori & Produk

```plantuml
@startuml UC_Inventory
skinparam backgroundColor white
skinparam shadowing false
skinparam ArrowColor black
skinparam usecase {
  BackgroundColor white
  BorderColor black
}
skinparam actor {
  BackgroundColor white
  BorderColor black
}
skinparam rectangle {
  BackgroundColor white
  BorderColor black
}

actor "Admin" as Admin

rectangle "Layanan & Inventori" {
  usecase "Kelola Data Servis\n(CRUD)" as UC_Service
  usecase "Kelola Kategori Sparepart\n(CRUD)" as UC_SparepartCategory
  usecase "Kelola Data Sparepart\n(CRUD)" as UC_Sparepart
  usecase "Kelola Paket Produk /\nBundle (CRUD)" as UC_Bundle
  usecase "Lihat Riwayat Mutasi Stok" as UC_StockMovement
  usecase "Lihat Daftar Order Online" as UC_OrderView
}

Admin -- UC_Service
Admin -- UC_SparepartCategory
Admin -- UC_Sparepart
Admin -- UC_Bundle
Admin -- UC_StockMovement
Admin -- UC_OrderView

UC_SparepartCategory ..> UC_Sparepart : <<extend>>

note bottom of UC_StockMovement
  Data dibuat otomatis oleh InvoiceItemObserver
  saat sparepart terjual (lihat diagram 1.4)
end note

@enduml
```

### 1.4 Transaksi Kasir (POS) & Work Order

```plantuml
@startuml UC_PosWorkOrder
skinparam backgroundColor white
skinparam shadowing false
skinparam ArrowColor black
skinparam usecase {
  BackgroundColor white
  BorderColor black
}
skinparam actor {
  BackgroundColor white
  BorderColor black
}
skinparam rectangle {
  BackgroundColor white
  BorderColor black
}

actor "Admin" as Admin
actor "Mekanik" as Mekanik

rectangle "POS & Work Order" {
  usecase "Transaksi Walk-in\n(Sparepart/Servis)" as UC_PosWalkIn
  usecase "Transaksi dari Work\nOrder Selesai" as UC_PosFromWO
  usecase "Buat Invoice" as UC_InvoiceCreate
  usecase "Lihat Daftar & Detail\nInvoice" as UC_InvoiceView
  usecase "Lihat Daftar & Detail\nWork Order" as UC_WOView
  usecase "Tetapkan Mekanik ke\nWork Order" as UC_WOAssign
  usecase "Update Status Work\nOrder (Admin)" as UC_WOUpdateAdmin
  usecase "Update Status Work\nOrder (Mekanik)" as UC_WOUpdateMekanik
  usecase "Tulis Catatan Pekerjaan" as UC_WONote
  usecase "Lihat Dashboard Mekanik" as UC_MekanikDashboard
}

Admin -- UC_PosWalkIn
Admin -- UC_PosFromWO
Admin -- UC_InvoiceView
Admin -- UC_WOView
Admin -- UC_WOAssign
Admin -- UC_WOUpdateAdmin

Mekanik -- UC_MekanikDashboard
Mekanik -- UC_WOUpdateMekanik
Mekanik -- UC_WONote

UC_PosWalkIn ..> UC_InvoiceCreate : <<include>>
UC_PosFromWO ..> UC_InvoiceCreate : <<include>>

note right of UC_WOUpdateMekanik
  Mekanik hanya bisa update status
  work order miliknya sendiri
  (abort_unless mekanik_id === auth id)
end note

@enduml
```

### 1.5 Booking Online

```plantuml
@startuml UC_Booking
skinparam backgroundColor white
skinparam shadowing false
skinparam ArrowColor black
skinparam usecase {
  BackgroundColor white
  BorderColor black
}
skinparam actor {
  BackgroundColor white
  BorderColor black
}
skinparam rectangle {
  BackgroundColor white
  BorderColor black
}

actor "Pelanggan\n(Customer)" as Customer
actor "Admin" as Admin
actor "Scheduler\n(Cron)" as Scheduler

rectangle "Booking Online" {
  usecase "Booking Servis Online\n(3 langkah)" as UC_BookingOnline
  usecase "Tambah Kendaraan Inline\nsaat Booking" as UC_BookingAddVehicle
  usecase "Lihat Kalender & Daftar\nBooking" as UC_BookingView
  usecase "Konfirmasi Booking" as UC_BookingConfirm
  usecase "Batalkan Booking" as UC_BookingCancel
  usecase "Buat Invoice dari\nBooking" as UC_BookingToInvoice
  usecase "Generate Slot Booking\nHarian" as UC_GenerateSlots
}

Customer -- UC_BookingOnline
Admin -- UC_BookingView
Admin -- UC_BookingConfirm
Admin -- UC_BookingCancel
Scheduler -- UC_GenerateSlots

UC_BookingOnline ..> UC_BookingAddVehicle : <<extend>>
UC_BookingConfirm ..> UC_BookingToInvoice : <<extend>>

note bottom of UC_BookingOnline
  Slot tanggal dikunci pakai
  lockForUpdate() untuk cegah race condition
end note

@enduml
```

### 1.6 Analitik RFM, Laporan & Pengaturan

```plantuml
@startuml UC_RfmReport
skinparam backgroundColor white
skinparam shadowing false
skinparam ArrowColor black
skinparam usecase {
  BackgroundColor white
  BorderColor black
}
skinparam actor {
  BackgroundColor white
  BorderColor black
}
skinparam rectangle {
  BackgroundColor white
  BorderColor black
}

actor "Super Admin" as SuperAdmin
actor "Scheduler\n(Cron)" as Scheduler

rectangle "Analitik & Pengaturan (Super Admin)" {
  usecase "Lihat Segmentasi\nPelanggan (RFM)" as UC_RfmView
  usecase "Lihat Analitik Cluster\nK-Means" as UC_RfmCluster
  usecase "Export CSV Segmentasi" as UC_RfmExport
  usecase "Hitung RFM Score &\nClustering Otomatis" as UC_RfmCalculate
  usecase "Lihat Laporan Keuangan" as UC_Report
  usecase "Export CSV Laporan" as UC_ReportExport
  usecase "Kelola Pengaturan\nBengkel/Booking/RFM" as UC_Settings
  usecase "Kelola Akun Pengguna\n(Buat/Edit)" as UC_UserManage
  usecase "Lihat Daftar Pengguna" as UC_UserView
}

SuperAdmin -- UC_RfmView
SuperAdmin -- UC_Report
SuperAdmin -- UC_Settings
SuperAdmin -- UC_UserManage
SuperAdmin -- UC_UserView
Scheduler -- UC_RfmCalculate

UC_RfmView ..> UC_RfmCluster : <<include>>
UC_RfmView ..> UC_RfmExport : <<extend>>
UC_Report ..> UC_ReportExport : <<extend>>

@enduml
```

---

## 2. Activity Diagrams

Empat alur inti yang merepresentasikan bisnis proses utama JHMPro, diambil langsung dari logika di masing-masing Livewire component / Observer / Console Command.

### 2.1 Login (Role-based Redirect)

Sumber: `app/Providers/FortifyServiceProvider.php` (`authenticateUsing`), `App\Http\Responses\RoleBasedLoginResponse`.

```plantuml
@startuml LoginActivity
title Activity Diagram — Login (Role-based)
start
:User buka /login (Fortify::loginView → admin.auth.login);
:Input email & password;
:Fortify authenticateUsing() dieksekusi;
if (User ditemukan & password cocok?) then (tidak)
  :Return null / gagal login;
  stop
else (ya)
endif
if (is_active == false?) then (ya)
  :Lempar ValidationException\n"Akun tidak aktif, hubungi administrator.";
  stop
else (tidak)
endif
if (role in [super_admin, admin, mekanik]?) then (ya)
  :Login juga ke guard "admin"\n(Auth::guard('admin')->login());
else (tidak)
endif
:RoleBasedLoginResponse::toResponse();
switch (role?)
case (super_admin / admin)
  :Redirect ke route admin.dashboard;
case (mekanik)
  :Redirect ke route mekanik.dashboard;
case (customer)
  :Redirect intended atau route dashboard;
endswitch
stop
@enduml
```

### 2.2 Booking Servis Online (Pelanggan)

Sumber: `app/Livewire/Website/Booking/BookingPage.php`.

```plantuml
@startuml BookingActivity
title Activity Diagram — Booking Servis Online (3 Langkah)
|Pelanggan|
start
:Akses halaman /booking;
|System|
if (no_hp kosong di profil?) then (ya)
  :Flash error "Lengkapi nomor HP";
  :Redirect ke profile.edit;
  stop
else (tidak)
endif
if (Customer record belum ada?) then (ya)
  :Auto-create Customer\n(link ke user_id);
else (tidak)
endif
|Pelanggan|
:Step 1 — Pilih kendaraan;
if (Belum punya kendaraan?) then (ya)
  :Isi form kendaraan inline\n(merk, model, tipe, tahun, no_polisi);
  |System|
  :Validasi & simpan Vehicle baru;
  :VehicleObserver auto-create VehicleEngineSpec kosong;
  |Pelanggan|
else (tidak)
  :Pilih kendaraan existing;
endif
:nextStep() → validasi vehicleId milik customer;
:Step 2 — Pilih servis/bundle (toggleService)\n+ pilih tanggal dari kalender slot;
|System|
:Hitung calendarDays() berdasarkan\nSetting booking_advance_days & booking_hari\n+ BookingSlot per tanggal;
|Pelanggan|
:nextStep() → validasi minimal 1 servis\n+ tanggal harus after:today;
:Step 3 — Isi keluhan (opsional) → submit();
|System|
:DB::transaction dimulai;
:Lock BookingSlot for update (mekanik_id null, tanggal dipilih);
if (Slot penuh / diblokir / tidak ada?) then (ya)
  :Throw RuntimeException\n"Slot penuh, pilih tanggal lain";
  :Tampilkan error di Step 2;
  stop
else (tidak)
endif
:Generate booking_number "BK-{tahun}-NNNN";
:Buat record Booking (status=pending, source=website);
:Buat record BookingService per item dipilih;
:Increment BookingSlot.terisi;
:Commit transaction;
:Simpan flash session booking_success;
:Redirect ke booking.success;
stop
@enduml
```

### 2.3 Transaksi Kasir (POS)

Sumber: `app/Livewire/Admin/Pos/PosPage.php`, `app/Observers/InvoiceItemObserver.php`.

```plantuml
@startuml PosActivity
title Activity Diagram — Transaksi POS
|Admin/Kasir|
start
:Buka halaman /admin/pos;
:Pilih mode (walk_in / work_order);
if (mode == work_order?) then (ya)
  :Cari & pilih Work Order\n(status=selesai, invoice_id null);
else (tidak)
  :Pilih tipe walk-in (sparepart / servis);
  if (tipe == servis?) then (ya)
    :Cari & pilih Pelanggan (wajib);
    :Isi keluhan (wajib);
  else (tidak)
  endif
endif
:Cari & tambah item ke cart\n(sparepart atau servis);
:Atur qty per item (increment/decrement/remove);
:Hitung subtotal, diskon, grand total, kembalian;
:Isi metode pembayaran & jumlah bayar;
:Klik "Buat Invoice" (buatInvoice());
|System|
if (cart kosong?) then (ya)
  stop
else (tidak)
endif
if (tipe servis & data pelanggan/keluhan belum lengkap?) then (ya)
  :Tampilkan validation error;
  stop
else (tidak)
endif
:DB::transaction dimulai;
:Re-resolve harga canonical dari DB\n(anti price-manipulation dari client);
:Hitung subtotal & grand_total canonical;
:Buat Invoice (tipe=walk_in,\ninvoice_number=INV-{uniqid});
:Tentukan payment_status\n(paid jika jumlah_bayar >= grand_total, else unpaid);
:Buat InvoiceItem per item cart;
note right: InvoiceItemObserver.created()\n→ jika item sparepart: lock stok,\ndecrement stock, buat StockMovement (type=out)
if (tipe == servis (mode walk_in)?) then (ya)
  :Buat WorkOrder baru (status=antrian);
else (tidak)
endif
if (mode == work_order & selectedWoId?) then (ya)
  :Update Invoice.vehicle_id dari WO terkait;
else (tidak)
endif
:Commit transaction;
:Reset form + tampilkan invoiceSuccessId;
stop
@enduml
```

### 2.4 Siklus Work Order (Admin ↔ Mekanik)

Sumber: `app/Livewire/Admin/WorkOrders/WorkOrderDetail.php`, `app/Livewire/Mekanik/WorkOrders/MekanikWorkOrderDetail.php`.

```plantuml
@startuml WorkOrderActivity
title Activity Diagram — Siklus Work Order
|Admin|
start
:Invoice servis dibuat (POS/InvoiceCreate);
:WorkOrder otomatis dibuat\n(status default = "antrian"/"pending");
:Buka Work Order Detail;
:Tetapkan mekanik (mekanikId);
:Update status / catatan_mekanik (save());
|System|
if (status berubah ke in_progress & mulai_at kosong?) then (ya)
  :Set mulai_at = now();
else (tidak)
endif
if (status berubah ke done & selesai_at kosong?) then (ya)
  :Set selesai_at = now();
else (tidak)
endif
:Simpan WorkOrder;
|Mekanik|
:Buka daftar Work Order miliknya\n(mekanik_id == auth admin id);
:Buka detail Work Order;
if (mekanik_id != auth id?) then (ya)
  :abort(403);
  stop
else (tidak)
endif
:updateStatus('in_progress') → mulai kerjakan;
:Tulis catatan pekerjaan (saveCatatan());
:updateStatus('done') → selesai;
|System|
:Set selesai_at jika kosong;
:Flash "Status work order diperbarui";
|Admin|
:Cari Work Order (status=selesai, invoice_id null)\ndi POS untuk ditagihkan (jika belum ada invoice);
stop
@enduml
```

> **Catatan temuan:** kolom `work_orders.status` di migrasi (`database/migrations/2026_06_15_222816_create_core_tables.php`) adalah ENUM `antrian|proses|selesai` (default `antrian`), sedangkan kode Livewire (`WorkOrderDetail`, `MekanikWorkOrderDetail`, `WorkOrderIndex`) memvalidasi/menampilkan status `pending|in_progress|done|cancelled`. Diagram di atas menyertakan kedua istilah persis seperti di kode — ketidaksesuaian ini nyata di codebase saat ini dan tidak diperbaiki di dokumen ini (di luar scope dokumen UML).

### 2.5 Kalkulasi RFM & K-Means (Scheduler)

Sumber: `app/Console/Commands/CalculateRfm.php`.

```plantuml
@startuml RfmActivity
title Activity Diagram — Kalkulasi RFM & K-Means (rfm:calculate)
|Scheduler| 
start
:Jalankan artisan rfm:calculate --source=all;
|System|
:Ambil setting k_clusters, weight_r/f/m, period_months;
:Hitung periode (periodStart..periodEnd);
partition "Per source (bengkel, combined)" {
  :Fetch raw data transaksi\n(invoices paid dan/atau orders paid);
  if (data kosong?) then (ya)
    :Skip source ini;
  else (tidak)
    :Hitung recency_days, frequency, monetary per customer;
    :Hitung quintile score R (inverse), F, M (1–5);
    :Jalankan K-Means clustering\n(euclidean distance, max 100 iterasi)\npada titik (R,F,M);
    :Urutkan cluster berdasarkan\ntotal centroid (tertinggi = Champion);
    :Hitung rfm_score = R*wR + F*wF + M*wM;
    :updateOrCreate CustomerRfm per customer;
    if (RfmHistory bulan ini belum ada?) then (ya)
      :Buat RfmHistory (snapshot per year_month);
    else (tidak)
    endif
    :Update ClusterDefinition.centroid;
  endif
}
:Tampilkan ringkasan proses per source;
stop
@enduml
```

---

## 3. Class Diagram

Mencakup seluruh 25 Eloquent Model di `app/Models/` beserta atribut (dari `@property` PHPDoc + `$fillable`), method non-relasi, dan relasi Eloquent aktual (`belongsTo`, `hasMany`, `hasOne`, `morphMany`, `morphTo`).

```plantuml
@startuml ClassDiagram
skinparam classAttributeIconSize 0
title JHMPro — Class Diagram (Eloquent Models)

class User {
  +id: int
  +name: string
  +email: string
  +password: string (hidden)
  +role: string <<enum: super_admin|admin|mekanik|customer>>
  +is_active: bool
  +is_available: bool
  +no_hp: string?
  +email_verified_at: datetime?
  +two_factor_confirmed_at: datetime?
  --
  +isSuperAdmin(): bool
  +isAdmin(): bool
  +isMekanik(): bool
  +isCustomer(): bool
  +initials(): string
}

class Customer {
  +id: int
  +user_id: int?
  +nama: string
  +no_hp: string
  +email: string?
  +alamat: string?
  +catatan: string?
}

class Vehicle {
  +id: int
  +customer_id: int
  +merk: string
  +model: string
  +tipe: string?
  +tahun: int
  +no_polisi: string
  +no_rangka: string?
  +no_mesin: string?
  +warna: string?
  +foto: string?
  +catatan: string?
  +deleted_at: datetime? <<SoftDeletes>>
}

class VehicleEngineSpec {
  +id: int
  +vehicle_id: int <<unique>>
  +cylinder_head..rantai: string? (29 field spek mesin)
  +catatan_tambahan: string?
}

class VehicleModificationLog {
  +id: int
  +vehicle_id: int
  +invoice_id: int?
  +user_id: int
  +judul: string
  +deskripsi: string?
  +specs_snapshot: array?
  +parts_used: array?
  +foto: array?
  +logged_at: datetime?
}

class Partner {
  +id: int
  +nama_bengkel: string
  +contact_person: string
  +no_hp: string
  +alamat: string?
  +catatan: string?
}

class Service {
  +id: int
  +nama_service: string
  +deskripsi: string?
  +harga_default: decimal
  +durasi_estimasi: int?
  +is_active: bool
  +is_bookable: bool
}

class SparepartCategory {
  +id: int
  +parent_id: int?
  +name: string
  +slug: string <<unique>>
  +icon_image: string?
  +description: string?
}

class Sparepart {
  +id: int
  +category_id: int?
  +sku: string <<unique>>
  +item_name: string
  +brand: string?
  +satuan: string
  +harga_beli: decimal
  +harga_jual: decimal
  +harga_online: decimal?
  +stock: int
  +minimum_stock: int
  +berat: int?
  +dimensi: array?
  +images: array?
  +deskripsi: string?
  +is_active: bool
  +is_sold_online: bool
  --
  +isStockCritical(): bool
}

class ProductBundle {
  +id: int
  +nama: string
  +slug: string <<unique>>
  +deskripsi: string?
  +harga: decimal
  +images: array?
  +is_active: bool
  +is_sold_online: bool
  +is_bookable: bool
}

class ProductBundleItem {
  +id: int
  +bundle_id: int
  +sparepart_id: int?
  +service_id: int?
  +type: string <<enum: sparepart|service>>
  +qty: int
  +harga_snapshot: decimal
}

class Invoice {
  +id: int
  +vehicle_id: int?
  +customer_id: int?
  +partner_id: int?
  +user_id: int
  +booking_id: int?
  +invoice_number: string <<unique>>
  +tanggal: date
  +tipe: string <<enum: walk_in|booking|partner|online>>
  +catatan: string?
  +subtotal: decimal
  +discount: decimal
  +grand_total: decimal
  +payment_status: string <<enum: unpaid|partial|paid|voided>>
  +amount_paid: decimal
}

class InvoiceItem {
  +id: int
  +invoice_id: int
  +service_id: int?
  +sparepart_id: int?
  +type: string <<enum: service|sparepart>>
  +nama_snapshot: string
  +qty: int
  +harga_jual: decimal
  +harga_beli_snapshot: decimal
  +subtotal: decimal
}

class WorkOrder {
  +id: int
  +wo_number: string <<unique, auto "WO-NNNN">>
  +invoice_id: int
  +vehicle_id: int
  +mekanik_id: int?
  +status: string <<enum DB: antrian|proses|selesai>>
  +keluhan_customer: string?
  +catatan_mekanik: string?
  +mulai_at: datetime?
  +selesai_at: datetime?
}

class Booking {
  +id: int
  +customer_id: int?
  +vehicle_id: int?
  +mekanik_id: int?
  +invoice_id: int?
  +booking_number: string <<unique, "BK-YYYY-NNNN">>
  +tanggal_booking: date
  +jam_mulai: string?
  +jam_selesai: string?
  +status: string <<enum: pending|confirmed|in_progress|completed|cancelled>>
  +source: string <<enum: website|whatsapp|walk_in>>
  +keluhan: string?
  +catatan_admin: string?
  +nama_pemesan: string
  +no_hp_pemesan: string
  +confirmed_at: datetime?
  +cancelled_at: datetime?
  +cancel_reason: string?
}

class BookingService {
  +id: int
  +booking_id: int
  +service_id: int?
  +bundle_id: int?
  +type: string <<enum: service|bundle>>
  +nama_snapshot: string
  +harga_estimasi: decimal
}

class BookingSlot {
  +id: int
  +mekanik_id: int?
  +tanggal: string (tidak di-cast date)
  +kapasitas: int
  +terisi: int
  +is_blocked: bool
  +blocked_reason: string?
  --
  +isAvailable(): bool
}

class StockMovement {
  +id: int
  +sparepart_id: int
  +user_id: int
  +type: string <<enum: in|out|adjustment>>
  +qty: int
  +stock_before: int
  +stock_after: int
  +reference_type: string?
  +reference_id: int?
  +catatan: string?
}

class Order {
  +id: int
  +customer_id: int?
  +order_number: string <<unique>>
  +nama_penerima: string
  +no_hp_penerima: string
  +alamat_kirim: string
  +provinsi: string
  +kota: string
  +kecamatan: string
  +kode_pos: string
  +subtotal: decimal
  +ongkir: decimal
  +discount: decimal
  +grand_total: decimal
  +status: string <<enum: pending|processing|shipped|delivered|cancelled>>
  +payment_status: string <<enum: unpaid|paid|expired|refunded>>
}

class OrderItem {
  +id: int
  +order_id: int
  +sparepart_id: int?
  +bundle_id: int?
  +type: string <<enum: sparepart|bundle>>
  +nama_snapshot: string
  +qty: int
  +harga_snapshot: decimal
  +subtotal: decimal
}

class Shipment {
  +id: int
  +order_id: int
  +shipbite_order_id: string?
  +courier: string
  +service_type: string
  +tracking_number: string?
  +origin_address: array
  +destination_address: array
  +weight: int
  +shipping_cost: decimal
  +insurance_cost: decimal
  +status: string <<enum: pending|picked_up|in_transit|delivered|returned>>
  +raw_response: string?
  +shipped_at: datetime?
  +delivered_at: datetime?
}

class Payment {
  +id: int
  +payable_type: string
  +payable_id: int
  +midtrans_order_id: string? <<unique>>
  +midtrans_transaction_id: string?
  +midtrans_status: string?
  +payment_method: string?
  +amount: decimal
  +snap_token: string?
  +payment_url: string?
  +raw_response: array?
  +paid_at: datetime?
  +expired_at: datetime?
}

class ClusterDefinition {
  +id: int
  +label: string
  +description: string?
  +color_hex: string
  +icon: string?
  +action_suggestion: string?
  +centroid: array?
}

class CustomerRfm {
  +id: int
  +customer_id: int
  +source: string <<enum: bengkel|online_shop|combined>>
  +recency_days: int
  +frequency: int
  +monetary: decimal
  +r_score: int
  +f_score: int
  +m_score: int
  +rfm_score: decimal
  +cluster_id: int
  +cluster_label: string
  +period_start: date
  +period_end: date
  +calculated_at: datetime
  .. table: customer_rfm, timestamps=false ..
}

class RfmHistory {
  +id: int
  +customer_id: int
  +source: string <<enum: bengkel|online_shop|combined>>
  +year_month: string(7)
  +recency_days: int
  +frequency: int
  +monetary: decimal
  +cluster_id: int
  +cluster_label: string
  +created_at: datetime
  .. table: rfm_history, timestamps=false ..
}

class Setting {
  +key: string <<PK, string>>
  +value: string
  --
  +{static} get(key, default): mixed
  +{static} set(key, value): void
}

' ── Relationships ──
User "1" -- "0..1" Customer : hasOne
User "1" -- "0..*" WorkOrder : hasMany (mekanik_id)
User "1" -- "0..*" Booking : belongsTo (mekanik_id)
User "1" -- "0..*" BookingSlot : belongsTo (mekanik_id)
User "1" -- "0..*" Invoice : belongsTo (kasir)
User "1" -- "0..*" StockMovement : belongsTo
User "1" -- "0..*" VehicleModificationLog : belongsTo

Customer "1" -- "0..*" Vehicle : hasMany
Customer "1" -- "0..*" Invoice : hasMany
Customer "1" -- "0..*" Booking : hasMany
Customer "1" -- "0..*" Order : hasMany
Customer "1" -- "0..*" CustomerRfm : hasMany
Customer "1" -- "0..1" CustomerRfm : latestRfm (source=combined)

Vehicle "1" -- "0..1" VehicleEngineSpec : hasOne
Vehicle "1" -- "0..*" VehicleModificationLog : hasMany
Vehicle "1" -- "0..*" Invoice : hasMany
Vehicle "1" -- "0..*" WorkOrder : hasMany
Vehicle "1" -- "0..*" Booking : hasMany

Partner "1" -- "0..*" Invoice : hasMany

SparepartCategory "1" -- "0..*" SparepartCategory : parent/children (self)
SparepartCategory "1" -- "0..*" Sparepart : hasMany

Sparepart "1" -- "0..*" StockMovement : hasMany
Sparepart "1" -- "0..*" InvoiceItem : belongsTo
Sparepart "1" -- "0..*" ProductBundleItem : belongsTo
Sparepart "1" -- "0..*" OrderItem : belongsTo

ProductBundle "1" -- "0..*" ProductBundleItem : hasMany
ProductBundle "1" -- "0..*" BookingService : belongsTo
ProductBundle "1" -- "0..*" OrderItem : belongsTo

Service "1" -- "0..*" InvoiceItem : belongsTo
Service "1" -- "0..*" ProductBundleItem : belongsTo
Service "1" -- "0..*" BookingService : belongsTo

Invoice "1" -- "0..*" InvoiceItem : hasMany
Invoice "1" -- "0..1" WorkOrder : hasOne
Invoice "1" -- "0..*" Payment : morphMany (payable)
Invoice "1" -- "0..1" Booking : belongsTo
Invoice "1" -- "0..*" VehicleModificationLog : belongsTo

WorkOrder "1" -- "1" Invoice : belongsTo
WorkOrder "1" -- "1" Vehicle : belongsTo

Booking "1" -- "0..*" BookingService : hasMany
Booking "1" -- "0..1" Invoice : belongsTo

Order "1" -- "0..*" OrderItem : hasMany
Order "1" -- "0..1" Shipment : hasOne
Order "1" -- "0..*" Payment : morphMany (payable)

CustomerRfm "0..*" -- "1" ClusterDefinition : belongsTo (cluster_id)
RfmHistory "0..*" -- "1" Customer : belongsTo

Payment ..> Invoice : morphTo (payable_type=Invoice)
Payment ..> Order : morphTo (payable_type=Order)

@enduml
```

---

## 4. Entity Relationship Diagram (ERD)

Mengikuti persis struktur tabel di migration:
`0001_01_01_000000_create_users_table.php`, `2026_06_15_214251_create_settings_table.php`, `2026_06_15_222816_create_core_tables.php`, dan kolom tambahan dari 3 alter migration (`bookings.status` += `in_progress`, `bookings.source` enum di-set ulang, `work_orders.wo_number`, `customers.user_id`, `invoices.payment_status` += `voided`).

> **Catatan temuan:** tabel `motorcycles` dibuat oleh migrasi (`brand, model, type, tahun`) namun **tidak ada Eloquent Model** yang memakainya — tampaknya tabel legacy/tidak terpakai, digantikan oleh `vehicles`. Tabel infrastruktur framework (`sessions`, `cache`, `jobs`, `password_reset_tokens`, `notifications`) tidak digambar karena bukan bagian domain bisnis.

```plantuml
@startuml ERD
title JHMPro — Entity Relationship Diagram
skinparam linetype ortho
hide circle

entity "users" as users {
  * id : bigint <<PK>>
  --
  name : string
  * email : string <<unique>>
  email_verified_at : timestamp
  password : string
  * role : enum('super_admin','admin','mekanik','customer') = customer
  * is_active : boolean = true
  * is_available : boolean = true
  no_hp : string(20)
  remember_token : string
  timestamps
}

entity "customers" as customers {
  * id : bigint <<PK>>
  --
  # user_id : bigint <<FK, unique, nullable>>
  nama : string
  no_hp : string
  email : string
  alamat : text
  catatan : text
  timestamps
}

entity "partners" as partners {
  * id : bigint <<PK>>
  --
  nama_bengkel : string
  contact_person : string
  no_hp : string
  alamat : text
  catatan : text
  timestamps
}

entity "services" as services {
  * id : bigint <<PK>>
  --
  nama_service : string
  deskripsi : text
  harga_default : decimal(15,2)
  durasi_estimasi : int
  is_active : boolean = true
  is_bookable : boolean = false
  timestamps
}

entity "sparepart_categories" as sparepart_categories {
  * id : bigint <<PK>>
  --
  # parent_id : bigint <<FK self, nullable>>
  name : string
  * slug : string <<unique>>
  icon_image : string
  description : text
  timestamps
}

entity "spareparts" as spareparts {
  * id : bigint <<PK>>
  --
  # category_id : bigint <<FK, nullable>>
  * sku : string <<unique>>
  item_name : string
  brand : string
  satuan : string
  harga_beli : decimal(15,2)
  harga_jual : decimal(15,2)
  harga_online : decimal(15,2)
  stock : int = 0
  minimum_stock : int = 0
  berat : int
  dimensi : json
  images : json
  deskripsi : text
  is_active : boolean = true
  is_sold_online : boolean = false
  timestamps
}

entity "product_bundles" as product_bundles {
  * id : bigint <<PK>>
  --
  nama : string
  * slug : string <<unique>>
  deskripsi : text
  harga : decimal(15,2)
  images : json
  is_active : boolean = true
  is_sold_online : boolean = false
  is_bookable : boolean = false
  timestamps
}

entity "product_bundle_items" as product_bundle_items {
  * id : bigint <<PK>>
  --
  # bundle_id : bigint <<FK>>
  # sparepart_id : bigint <<FK, nullable>>
  # service_id : bigint <<FK, nullable>>
  * type : enum('sparepart','service')
  qty : int = 1
  harga_snapshot : decimal(15,2)
  timestamps
}

entity "cluster_definitions" as cluster_definitions {
  * id : bigint <<PK>>
  --
  label : string
  description : text
  color_hex : string
  icon : string
  action_suggestion : text
  centroid : json
  timestamps
}

note as N_motorcycles
  Tabel legacy — tidak ada Eloquent Model
  yang memakainya (digantikan "vehicles")
end note

entity "motorcycles" as motorcycles {
  * id : bigint <<PK>>
  --
  brand : string(100)
  model : string(100)
  type : string(100)
  tahun : year
  timestamps
}
N_motorcycles .. motorcycles

entity "vehicles" as vehicles {
  * id : bigint <<PK>>
  --
  # customer_id : bigint <<FK>>
  brand : string(100)
  merk : string
  model : string
  tipe : string
  tahun : year
  * no_polisi : string <<unique>>
  no_rangka : string
  no_mesin : string
  warna : string
  foto : string
  catatan : text
  timestamps
  deleted_at : timestamp <<SoftDeletes>>
}

entity "vehicle_engine_specs" as vehicle_engine_specs {
  * id : bigint <<PK>>
  --
  # vehicle_id : bigint <<FK, unique>>
  cylinder_head..rantai : string (29 kolom spek mesin)
  catatan_tambahan : text
  updated_at : timestamp
}

entity "vehicle_modification_logs" as vehicle_modification_logs {
  * id : bigint <<PK>>
  --
  # vehicle_id : bigint <<FK>>
  # invoice_id : bigint <<FK, nullable>>
  # user_id : bigint <<FK>>
  judul : string
  deskripsi : text
  specs_snapshot : json
  parts_used : json
  foto : json
  logged_at : timestamp
  timestamps
}

entity "invoices" as invoices {
  * id : bigint <<PK>>
  --
  # vehicle_id : bigint <<FK, nullable>>
  # customer_id : bigint <<FK, nullable>>
  # partner_id : bigint <<FK, nullable>>
  # user_id : bigint <<FK>>
  # booking_id : bigint <<FK, nullable>>
  * invoice_number : string <<unique>>
  tanggal : date
  * tipe : enum('walk_in','booking','partner','online')
  catatan : text
  subtotal : decimal(15,2)
  discount : decimal(15,2) = 0
  grand_total : decimal(15,2)
  * payment_status : enum('unpaid','partial','paid','voided') = unpaid
  amount_paid : decimal(15,2) = 0
  timestamps
}

entity "invoice_items" as invoice_items {
  * id : bigint <<PK>>
  --
  # invoice_id : bigint <<FK>>
  # service_id : bigint <<FK, nullable>>
  # sparepart_id : bigint <<FK, nullable>>
  * type : enum('service','sparepart')
  nama_snapshot : string
  qty : int = 1
  harga_jual : decimal(15,2)
  harga_beli_snapshot : decimal(15,2)
  subtotal : decimal(15,2)
  timestamps
}

entity "payments" as payments {
  * id : bigint <<PK>>
  --
  * payable_type : string <<morph>>
  * payable_id : bigint <<morph>>
  midtrans_order_id : string <<unique, nullable>>
  midtrans_transaction_id : string
  midtrans_status : string
  payment_method : string
  amount : decimal(15,2)
  snap_token : string
  payment_url : string
  raw_response : longtext
  paid_at : timestamp
  expired_at : timestamp
  timestamps
  .. index(payable_type, payable_id) ..
}

entity "work_orders" as work_orders {
  * id : bigint <<PK>>
  --
  * wo_number : string <<unique, "WO-NNNN">>
  # invoice_id : bigint <<FK>>
  # vehicle_id : bigint <<FK>>
  # mekanik_id : bigint <<FK, nullable>>
  * status : enum('antrian','proses','selesai') = antrian
  keluhan_customer : text
  catatan_mekanik : text
  mulai_at : timestamp
  selesai_at : timestamp
  timestamps
}

entity "bookings" as bookings {
  * id : bigint <<PK>>
  --
  # customer_id : bigint <<FK, nullable>>
  # vehicle_id : bigint <<FK, nullable>>
  # mekanik_id : bigint <<FK, nullable>>
  # invoice_id : bigint <<FK, nullable>>
  * booking_number : string <<unique, "BK-YYYY-NNNN">>
  tanggal_booking : date
  jam_mulai : time
  jam_selesai : time
  * status : enum('pending','confirmed','in_progress','completed','cancelled') = pending
  * source : enum('website','whatsapp','walk_in') = website
  keluhan : text
  catatan_admin : text
  nama_pemesan : string
  no_hp_pemesan : string
  confirmed_at : timestamp
  cancelled_at : timestamp
  cancel_reason : string
  timestamps
}

entity "booking_slots" as booking_slots {
  * id : bigint <<PK>>
  --
  # mekanik_id : bigint <<FK, nullable>>
  tanggal : date
  kapasitas : int
  terisi : int = 0
  is_blocked : boolean = false
  blocked_reason : string
  timestamps
}

entity "booking_services" as booking_services {
  * id : bigint <<PK>>
  --
  # booking_id : bigint <<FK>>
  # service_id : bigint <<FK, nullable>>
  # bundle_id : bigint <<FK, nullable>>
  * type : enum('service','bundle')
  nama_snapshot : string
  harga_estimasi : decimal(15,2)
  timestamps
}

entity "stock_movements" as stock_movements {
  * id : bigint <<PK>>
  --
  # sparepart_id : bigint <<FK>>
  # user_id : bigint <<FK>>
  * type : enum('in','out','adjustment')
  qty : int
  stock_before : int
  stock_after : int
  reference_type : string
  reference_id : bigint
  catatan : text
  timestamps
}

entity "orders" as orders {
  * id : bigint <<PK>>
  --
  # customer_id : bigint <<FK, nullable>>
  * order_number : string <<unique>>
  nama_penerima : string
  no_hp_penerima : string
  alamat_kirim : text
  provinsi : string
  kota : string
  kecamatan : string
  kode_pos : string
  subtotal : decimal(15,2)
  ongkir : decimal(15,2) = 0
  discount : decimal(15,2) = 0
  grand_total : decimal(15,2)
  * status : enum('pending','processing','shipped','delivered','cancelled') = pending
  * payment_status : enum('unpaid','paid','expired','refunded') = unpaid
  timestamps
}

entity "order_items" as order_items {
  * id : bigint <<PK>>
  --
  # order_id : bigint <<FK>>
  # sparepart_id : bigint <<FK, nullable>>
  # bundle_id : bigint <<FK, nullable>>
  * type : enum('sparepart','bundle')
  nama_snapshot : string
  qty : int
  harga_snapshot : decimal(15,2)
  subtotal : decimal(15,2)
  timestamps
}

entity "shipments" as shipments {
  * id : bigint <<PK>>
  --
  # order_id : bigint <<FK>>
  shipbite_order_id : string
  courier : string
  service_type : string
  tracking_number : string
  origin_address : json
  destination_address : json
  weight : int
  shipping_cost : decimal(15,2)
  insurance_cost : decimal(15,2) = 0
  * status : enum('pending','picked_up','in_transit','delivered','returned') = pending
  raw_response : longtext
  shipped_at : timestamp
  delivered_at : timestamp
  timestamps
}

entity "customer_rfm" as customer_rfm {
  * id : bigint <<PK>>
  --
  # customer_id : bigint <<FK>>
  * source : enum('bengkel','online_shop','combined')
  recency_days : uint
  frequency : uint
  monetary : decimal(15,2)
  r_score : utinyint
  f_score : utinyint
  m_score : utinyint
  rfm_score : decimal(5,2)
  # cluster_id : int <<ref ClusterDefinition, bukan FK constraint>>
  cluster_label : string
  period_start : date
  period_end : date
  calculated_at : timestamp
  .. no timestamps() ..
}

entity "rfm_history" as rfm_history {
  * id : bigint <<PK>>
  --
  # customer_id : bigint <<FK>>
  * source : enum('bengkel','online_shop','combined')
  year_month : string(7)
  recency_days : uint
  frequency : uint
  monetary : decimal(15,2)
  cluster_id : int
  cluster_label : string
  created_at : timestamp
  .. no timestamps() ..
}

entity "settings" as settings {
  * key : string <<PK>>
  --
  value : text
  timestamps
}

' ── Relasi ──
users ||--o{ work_orders : "mekanik_id"
users ||--o{ bookings : "mekanik_id"
users ||--o{ booking_slots : "mekanik_id"
users ||--o{ invoices : "user_id (kasir)"
users ||--o{ stock_movements : "user_id"
users ||--o{ vehicle_modification_logs : "user_id"
users |o--o| customers : "user_id (nullable, unique)"

customers ||--o{ vehicles : "customer_id"
customers ||--o{ invoices : "customer_id"
customers ||--o{ bookings : "customer_id"
customers ||--o{ orders : "customer_id"
customers ||--o{ customer_rfm : "customer_id"
customers ||--o{ rfm_history : "customer_id"

partners ||--o{ invoices : "partner_id"

sparepart_categories ||--o{ sparepart_categories : "parent_id"
sparepart_categories ||--o{ spareparts : "category_id"

spareparts ||--o{ stock_movements : "sparepart_id"
spareparts ||--o{ invoice_items : "sparepart_id"
spareparts ||--o{ product_bundle_items : "sparepart_id"
spareparts ||--o{ order_items : "sparepart_id"

product_bundles ||--o{ product_bundle_items : "bundle_id"
product_bundles ||--o{ booking_services : "bundle_id"
product_bundles ||--o{ order_items : "bundle_id"

services ||--o{ invoice_items : "service_id"
services ||--o{ product_bundle_items : "service_id"
services ||--o{ booking_services : "service_id"

vehicles ||--o| vehicle_engine_specs : "vehicle_id"
vehicles ||--o{ vehicle_modification_logs : "vehicle_id"
vehicles ||--o{ invoices : "vehicle_id"
vehicles ||--o{ work_orders : "vehicle_id"
vehicles ||--o{ bookings : "vehicle_id"

invoices ||--o{ invoice_items : "invoice_id"
invoices |o--o| work_orders : "invoice_id"
invoices ||--o{ vehicle_modification_logs : "invoice_id"
invoices |o--o| bookings : "invoice_id"
invoices ||--o{ payments : "payable (morph)"

bookings ||--o{ booking_services : "booking_id"

orders ||--o{ order_items : "order_id"
orders |o--o| shipments : "order_id"
orders ||--o{ payments : "payable (morph)"

cluster_definitions ||--o{ customer_rfm : "cluster_id (referensi, bukan FK constraint)"

@enduml
```

---

## 5. Ringkasan Sumber

| Diagram | Sumber utama |
|---|---|
| Use Case | `routes/web.php`, `routes/settings.php`, `app/Http/Middleware/CheckRole.php` |
| Activity — Login | `app/Providers/FortifyServiceProvider.php`, `app/Http/Responses/RoleBasedLoginResponse.php` |
| Activity — Booking | `app/Livewire/Website/Booking/BookingPage.php` |
| Activity — POS | `app/Livewire/Admin/Pos/PosPage.php`, `app/Observers/InvoiceItemObserver.php` |
| Activity — Work Order | `app/Livewire/Admin/WorkOrders/WorkOrderDetail.php`, `app/Livewire/Mekanik/WorkOrders/MekanikWorkOrderDetail.php` |
| Activity — RFM | `app/Console/Commands/CalculateRfm.php` |
| Class Diagram | `app/Models/*.php` (25 model) |
| ERD | `database/migrations/*.php` (semua migration, termasuk 5 alter migration) |
