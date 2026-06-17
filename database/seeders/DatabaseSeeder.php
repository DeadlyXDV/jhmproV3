<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->call([
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
