<?php

use App\Livewire\Admin\Bookings\BookingCalendar;
use App\Livewire\Admin\Bookings\BookingIndex;
use App\Livewire\Admin\Customers\CustomerCreate;
use App\Livewire\Admin\Customers\CustomerDetail;
use App\Livewire\Admin\Customers\CustomerIndex;
use App\Livewire\Admin\Dashboard\AdminDashboard;
use App\Livewire\Admin\Invoices\InvoiceCreate;
use App\Livewire\Admin\Invoices\InvoiceDetail;
use App\Livewire\Admin\Invoices\InvoiceIndex;
use App\Livewire\Admin\Orders\OrderIndex;
use App\Livewire\Admin\Partners\PartnerIndex;
use App\Livewire\Admin\Pos\PosPage;
use App\Livewire\Admin\ProductBundles\ProductBundleIndex;
use App\Livewire\Admin\Reports\ReportIndex;
use App\Livewire\Admin\Rfm\RfmIndex;
use App\Livewire\Admin\Services\ServiceIndex;
use App\Livewire\Admin\Settings\SettingIndex;
use App\Livewire\Admin\SparepartCategories\SparepartCategoryIndex;
use App\Livewire\Admin\Spareparts\SparepartIndex;
use App\Livewire\Admin\StockMovements\StockMovementIndex;
use App\Livewire\Admin\Users\UserIndex;
use App\Livewire\Admin\Vehicles\VehicleCreate;
use App\Livewire\Admin\Vehicles\VehicleDetail;
use App\Livewire\Admin\Vehicles\VehicleIndex;
use App\Livewire\Admin\WorkOrders\WorkOrderDetail;
use App\Livewire\Admin\WorkOrders\WorkOrderIndex;
use App\Livewire\Mekanik\Dashboard\MekanikDashboard;
use App\Livewire\Mekanik\WorkOrders\MekanikWorkOrderDetail;
use App\Livewire\Mekanik\WorkOrders\MekanikWorkOrderIndex;
use App\Livewire\Website\Booking\BookingPage;
use Illuminate\Support\Facades\Route;

// ── WEBSITE PUBLIK ───────────────────────────────────────────────────────────
Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');

    // Booking online (customer)
    Route::get('/booking', BookingPage::class)->name('booking');
    Route::get('/booking/success', fn () => view('website.booking.success'))->name('booking.success');
});

require __DIR__.'/settings.php';

// ── ADMIN ─────────────────────────────────────────────────────────────────────
Route::prefix('admin')->name('admin.')->group(function () {

    // ── ADMIN + SUPER_ADMIN ───────────────────────────────────────────────────
    Route::middleware(['auth:admin', 'role:super_admin,admin'])->group(function () {
        Route::get('/', fn () => redirect()->route('admin.dashboard'));
        Route::get('/dashboard', AdminDashboard::class)->name('dashboard');

        // Pelanggan
        Route::get('/customers', CustomerIndex::class)->name('customers.index');
        Route::get('/customers/create', CustomerCreate::class)->name('customers.create');
        Route::get('/customers/{customer}', CustomerDetail::class)->name('customers.show');
        Route::get('/customers/{customer}/edit', CustomerCreate::class)->name('customers.edit');

        // Kendaraan
        Route::get('/vehicles', VehicleIndex::class)->name('vehicles.index');
        Route::get('/vehicles/create', VehicleCreate::class)->name('vehicles.create');
        Route::get('/vehicles/{vehicle}', VehicleDetail::class)->name('vehicles.show');
        Route::get('/vehicles/{vehicle}/edit', VehicleCreate::class)->name('vehicles.edit');

        // Partner
        Route::get('/partners', PartnerIndex::class)->name('partners.index');

        // Invoice
        Route::get('/invoices', InvoiceIndex::class)->name('invoices.index');
        Route::get('/invoices/create', InvoiceCreate::class)->name('invoices.create');
        Route::get('/invoices/{invoice}', InvoiceDetail::class)->name('invoices.show');

        // Booking
        Route::get('/bookings', BookingIndex::class)->name('bookings.index');
        Route::get('/bookings/calendar', BookingCalendar::class)->name('bookings.calendar');

        // Servis
        Route::get('/services', ServiceIndex::class)->name('services.index');

        // Work Order
        Route::get('/work-orders', WorkOrderIndex::class)->name('work-orders.index');
        Route::get('/work-orders/{workOrder}', WorkOrderDetail::class)->name('work-orders.show');

        // Produk & Paket
        Route::get('/product-bundles', ProductBundleIndex::class)->name('product-bundles.index');

        // Pengguna (semua role bisa lihat tapi edit hanya super_admin)
        Route::get('/users', UserIndex::class)->name('users.index');

        // Inventory
        Route::get('/spareparts', SparepartIndex::class)->name('spareparts.index');
        Route::get('/sparepart-categories', SparepartCategoryIndex::class)->name('sparepart-categories.index');
        Route::get('/stock-movements', StockMovementIndex::class)->name('stock-movements.index');

        // Online Shop
        Route::get('/orders', OrderIndex::class)->name('orders.index');

        // POS
        Route::get('/pos', PosPage::class)->name('pos');
    });

    // ── SUPER ADMIN ONLY ──────────────────────────────────────────────────────
    Route::middleware(['auth:admin', 'role:super_admin'])->group(function () {
        Route::get('/rfm', RfmIndex::class)->name('rfm.index');
        Route::get('/reports', ReportIndex::class)->name('reports.index');
        Route::get('/settings', SettingIndex::class)->name('settings.index');
    });
});

// ── MEKANIK ───────────────────────────────────────────────────────────────────
Route::prefix('mekanik')->name('mekanik.')->middleware(['auth:admin', 'role:mekanik'])->group(function () {
    Route::get('/', fn () => redirect()->route('mekanik.dashboard'));
    Route::get('/dashboard', MekanikDashboard::class)->name('dashboard');
    Route::get('/work-orders', MekanikWorkOrderIndex::class)->name('work-orders.index');
    Route::get('/work-orders/{workOrder}', MekanikWorkOrderDetail::class)->name('work-orders.show');
});
