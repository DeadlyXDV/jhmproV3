<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        foreach ([
            'order_items', 'orders',
            'stock_movements',
            'work_orders', 'invoice_items', 'invoices',
            'booking_services', 'bookings',
            'product_bundle_items', 'product_bundles',
            'services',
            'spareparts', 'sparepart_categories',
            'partners',
            'vehicles', 'customers',
            'users',
            'cluster_definitions',
        ] as $table) {
            DB::table($table)->truncate();
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $this->call([
            ClusterDefinitionSeeder::class,
            UserSeeder::class,
            CustomerSeeder::class,
            VehicleSeeder::class,
            PartnerSeeder::class,
            SparepartCategorySeeder::class,
            SparepartSeeder::class,
            ServiceSeeder::class,
            ProductBundleSeeder::class,
            BookingSeeder::class,
            InvoiceSeeder::class,
            StockMovementSeeder::class,
            OrderSeeder::class,
        ]);
    }
}
