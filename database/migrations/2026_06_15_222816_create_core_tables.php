<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('no_hp');
            $table->string('email')->nullable();
            $table->text('alamat')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();
        });

        Schema::create('partners', function (Blueprint $table) {
            $table->id();
            $table->string('nama_bengkel');
            $table->string('contact_person');
            $table->string('no_hp');
            $table->text('alamat')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();
        });

        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('nama_service');
            $table->text('deskripsi')->nullable();
            $table->decimal('harga_default', 15, 2);
            $table->integer('durasi_estimasi')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_bookable')->default(false);
            $table->timestamps();
        });

        Schema::create('sparepart_categories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('icon_image')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('spareparts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('category_id')->nullable();
            $table->string('sku')->unique();
            $table->string('item_name');
            $table->string('brand')->nullable();
            $table->string('satuan');
            $table->decimal('harga_beli', 15, 2);
            $table->decimal('harga_jual', 15, 2);
            $table->decimal('harga_online', 15, 2)->nullable();
            $table->integer('stock')->default(0);
            $table->integer('minimum_stock')->default(0);
            $table->integer('berat')->nullable();
            $table->json('dimensi')->nullable();
            $table->json('images')->nullable();
            $table->text('deskripsi')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_sold_online')->default(false);
            $table->timestamps();
        });

        Schema::create('product_bundles', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('slug')->unique();
            $table->text('deskripsi')->nullable();
            $table->decimal('harga', 15, 2);
            $table->json('images')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_sold_online')->default(false);
            $table->boolean('is_bookable')->default(false);
            $table->timestamps();
        });

        Schema::create('product_bundle_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('bundle_id');
            $table->unsignedBigInteger('sparepart_id')->nullable();
            $table->unsignedBigInteger('service_id')->nullable();
            $table->enum('type', ['sparepart', 'service']);
            $table->integer('qty')->default(1);
            $table->decimal('harga_snapshot', 15, 2);
            $table->timestamps();
        });

        Schema::create('cluster_definitions', function (Blueprint $table) {
            $table->id();
            $table->string('label');
            $table->text('description')->nullable();
            $table->string('color_hex');
            $table->string('icon')->nullable();
            $table->text('action_suggestion')->nullable();
            $table->json('centroid')->nullable();
            $table->timestamps();
        });

        Schema::create('motorcycles', function (Blueprint $table) {
            $table->id();
            $table->string('brand', 100);
            $table->string('model', 100);
            $table->string('type', 100);
            $table->year('tahun');
            $table->timestamps();
        });

        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id');
            $table->string('brand', 100)->nullable();
            $table->string('merk');
            $table->string('model');
            $table->string('tipe')->nullable();
            $table->year('tahun');
            $table->string('no_polisi')->unique();
            $table->string('no_rangka')->nullable();
            $table->string('no_mesin')->nullable();
            $table->string('warna')->nullable();
            $table->string('foto')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('vehicle_engine_specs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vehicle_id')->unique();
            $table->string('cylinder_head')->nullable();
            $table->string('porting_polish')->nullable();
            $table->string('klep_in')->nullable();
            $table->string('klep_ex')->nullable();
            $table->string('per_klep')->nullable();
            $table->string('noken_as')->nullable();
            $table->string('cylinder_block')->nullable();
            $table->string('boring_size')->nullable();
            $table->string('piston')->nullable();
            $table->string('piston_ring')->nullable();
            $table->string('pen_piston')->nullable();
            $table->string('crankshaft')->nullable();
            $table->string('stroke')->nullable();
            $table->string('big_end')->nullable();
            $table->string('small_end')->nullable();
            $table->string('kopling')->nullable();
            $table->string('per_kopling')->nullable();
            $table->string('karburator_injeksi')->nullable();
            $table->string('filter_udara')->nullable();
            $table->string('knalpot')->nullable();
            $table->string('pengapian_type')->nullable();
            $table->string('cdi_ecu')->nullable();
            $table->string('koil')->nullable();
            $table->string('busi')->nullable();
            $table->string('kelistrikan_acg')->nullable();
            $table->string('kelistrikan_aki')->nullable();
            $table->string('rasio_gigi')->nullable();
            $table->string('gir_depan')->nullable();
            $table->string('gir_belakang')->nullable();
            $table->string('rantai')->nullable();
            $table->text('catatan_tambahan')->nullable();
            $table->timestamp('updated_at')->nullable();
        });

        Schema::create('vehicle_modification_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vehicle_id');
            $table->unsignedBigInteger('invoice_id')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->string('judul');
            $table->text('deskripsi')->nullable();
            $table->json('specs_snapshot')->nullable();
            $table->json('parts_used')->nullable();
            $table->json('foto')->nullable();
            $table->timestamp('logged_at')->nullable();
            $table->timestamps();
        });

        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vehicle_id')->nullable();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->unsignedBigInteger('partner_id')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('booking_id')->nullable();
            $table->string('invoice_number')->unique();
            $table->date('tanggal');
            $table->enum('tipe', ['walk_in', 'booking', 'partner', 'online']);
            $table->text('catatan')->nullable();
            $table->decimal('subtotal', 15, 2);
            $table->decimal('discount', 15, 2)->default(0);
            $table->decimal('grand_total', 15, 2);
            $table->enum('payment_status', ['unpaid', 'partial', 'paid'])->default('unpaid');
            $table->decimal('amount_paid', 15, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('invoice_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('invoice_id');
            $table->unsignedBigInteger('service_id')->nullable();
            $table->unsignedBigInteger('sparepart_id')->nullable();
            $table->enum('type', ['service', 'sparepart']);
            $table->string('nama_snapshot');
            $table->integer('qty')->default(1);
            $table->decimal('harga_jual', 15, 2);
            $table->decimal('harga_beli_snapshot', 15, 2);
            $table->decimal('subtotal', 15, 2);
            $table->timestamps();
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('payable_type');
            $table->unsignedBigInteger('payable_id');
            $table->string('midtrans_order_id')->nullable()->unique();
            $table->string('midtrans_transaction_id')->nullable();
            $table->string('midtrans_status')->nullable();
            $table->string('payment_method')->nullable();
            $table->decimal('amount', 15, 2);
            $table->string('snap_token')->nullable();
            $table->string('payment_url')->nullable();
            $table->longText('raw_response')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('expired_at')->nullable();
            $table->timestamps();
            $table->index(['payable_type', 'payable_id']);
        });

        Schema::create('work_orders', function (Blueprint $table) {
            $table->id();
            $table->string('wo_number')->unique();
            $table->unsignedBigInteger('invoice_id');
            $table->unsignedBigInteger('vehicle_id');
            $table->unsignedBigInteger('mekanik_id')->nullable();
            $table->enum('status', ['antrian', 'proses', 'selesai'])->default('antrian');
            $table->text('keluhan_customer')->nullable();
            $table->text('catatan_mekanik')->nullable();
            $table->timestamp('mulai_at')->nullable();
            $table->timestamp('selesai_at')->nullable();
            $table->timestamps();
        });

        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->unsignedBigInteger('vehicle_id')->nullable();
            $table->unsignedBigInteger('mekanik_id')->nullable();
            $table->unsignedBigInteger('invoice_id')->nullable();
            $table->string('booking_number')->unique();
            $table->date('tanggal_booking');
            $table->time('jam_mulai')->nullable();
            $table->time('jam_selesai')->nullable();
            $table->enum('status', ['pending', 'confirmed', 'in_progress', 'completed', 'cancelled'])->default('pending');
            $table->enum('source', ['website', 'whatsapp', 'walk_in'])->default('website');
            $table->text('keluhan')->nullable();
            $table->text('catatan_admin')->nullable();
            $table->string('nama_pemesan');
            $table->string('no_hp_pemesan');
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->string('cancel_reason')->nullable();
            $table->timestamps();
        });

        Schema::create('booking_slots', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('mekanik_id')->nullable();
            $table->date('tanggal');
            $table->integer('kapasitas');
            $table->integer('terisi')->default(0);
            $table->boolean('is_blocked')->default(false);
            $table->string('blocked_reason')->nullable();
            $table->timestamps();
        });

        Schema::create('booking_services', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('booking_id');
            $table->unsignedBigInteger('service_id')->nullable();
            $table->unsignedBigInteger('bundle_id')->nullable();
            $table->enum('type', ['service', 'bundle']);
            $table->string('nama_snapshot');
            $table->decimal('harga_estimasi', 15, 2);
            $table->timestamps();
        });

        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sparepart_id');
            $table->unsignedBigInteger('user_id');
            $table->enum('type', ['in', 'out', 'adjustment']);
            $table->integer('qty');
            $table->integer('stock_before');
            $table->integer('stock_after');
            $table->string('reference_type')->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();
        });

        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->string('order_number')->unique();
            $table->string('nama_penerima');
            $table->string('no_hp_penerima');
            $table->text('alamat_kirim');
            $table->string('provinsi');
            $table->string('kota');
            $table->string('kecamatan');
            $table->string('kode_pos');
            $table->decimal('subtotal', 15, 2);
            $table->decimal('ongkir', 15, 2)->default(0);
            $table->decimal('discount', 15, 2)->default(0);
            $table->decimal('grand_total', 15, 2);
            $table->enum('status', ['pending', 'processing', 'shipped', 'delivered', 'cancelled'])->default('pending');
            $table->enum('payment_status', ['unpaid', 'paid', 'expired', 'refunded'])->default('unpaid');
            $table->timestamps();
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('sparepart_id')->nullable();
            $table->unsignedBigInteger('bundle_id')->nullable();
            $table->enum('type', ['sparepart', 'bundle']);
            $table->string('nama_snapshot');
            $table->integer('qty');
            $table->decimal('harga_snapshot', 15, 2);
            $table->decimal('subtotal', 15, 2);
            $table->timestamps();
        });

        Schema::create('shipments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id');
            $table->string('shipbite_order_id')->nullable();
            $table->string('courier');
            $table->string('service_type');
            $table->string('tracking_number')->nullable();
            $table->json('origin_address');
            $table->json('destination_address');
            $table->integer('weight');
            $table->decimal('shipping_cost', 15, 2);
            $table->decimal('insurance_cost', 15, 2)->default(0);
            $table->enum('status', ['pending', 'picked_up', 'in_transit', 'delivered', 'returned'])->default('pending');
            $table->longText('raw_response')->nullable();
            $table->timestamp('shipped_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamps();
        });

        Schema::create('customer_rfm', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id');
            $table->enum('source', ['bengkel', 'online_shop', 'combined']);
            $table->unsignedInteger('recency_days');
            $table->unsignedInteger('frequency');
            $table->decimal('monetary', 15, 2);
            $table->unsignedTinyInteger('r_score');
            $table->unsignedTinyInteger('f_score');
            $table->unsignedTinyInteger('m_score');
            $table->decimal('rfm_score', 5, 2);
            $table->integer('cluster_id');
            $table->string('cluster_label');
            $table->date('period_start');
            $table->date('period_end');
            $table->timestamp('calculated_at');
        });

        Schema::create('rfm_history', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id');
            $table->enum('source', ['bengkel', 'online_shop', 'combined']);
            $table->string('year_month', 7);
            $table->unsignedInteger('recency_days');
            $table->unsignedInteger('frequency');
            $table->decimal('monetary', 15, 2);
            $table->integer('cluster_id');
            $table->string('cluster_label');
            $table->timestamp('created_at');
        });

        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('type');
            $table->morphs('notifiable');
            $table->text('data');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });

    }

    public function down(): void
    {
        Schema::dropIfExists('rfm_history');
        Schema::dropIfExists('customer_rfm');
        Schema::dropIfExists('shipments');
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('stock_movements');
        Schema::dropIfExists('booking_services');
        Schema::dropIfExists('booking_slots');
        Schema::dropIfExists('bookings');
        Schema::dropIfExists('work_orders');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('invoice_items');
        Schema::dropIfExists('invoices');
        Schema::dropIfExists('vehicle_modification_logs');
        Schema::dropIfExists('vehicle_engine_specs');
        Schema::dropIfExists('vehicles');
        Schema::dropIfExists('motorcycles');
        Schema::dropIfExists('cluster_definitions');
        Schema::dropIfExists('product_bundle_items');
        Schema::dropIfExists('product_bundles');
        Schema::dropIfExists('spareparts');
        Schema::dropIfExists('sparepart_categories');
        Schema::dropIfExists('services');
        Schema::dropIfExists('partners');
        Schema::dropIfExists('customers');
        Schema::dropIfExists('notifications');
    }
};
