<?php

namespace Database\Seeders;

use App\Models\Sparepart;
use App\Models\SparepartCategory;
use Illuminate\Database\Seeder;

class SparepartSeeder extends Seeder
{
    public function run(): void
    {
        $cat = fn (string $name) => SparepartCategory::where('name', $name)->value('id');

        $spareparts = [
            // Oli Mesin
            ['category_id' => $cat('Oli Mesin'), 'sku' => 'OLI-SHL-800', 'item_name' => 'Shell Advance AX7 800ml', 'brand' => 'Shell', 'satuan' => 'botol', 'harga_beli' => 42000, 'harga_jual' => 55000, 'harga_online' => 52000, 'stock' => 48, 'minimum_stock' => 10, 'berat' => 850, 'is_sold_online' => true],
            ['category_id' => $cat('Oli Mesin'), 'sku' => 'OLI-MTL-1L', 'item_name' => 'Motul 5100 4T 10W40 1L', 'brand' => 'Motul', 'satuan' => 'botol', 'harga_beli' => 85000, 'harga_jual' => 110000, 'harga_online' => 105000, 'stock' => 30, 'minimum_stock' => 8, 'berat' => 1050, 'is_sold_online' => true],
            ['category_id' => $cat('Oli Mesin'), 'sku' => 'OLI-CST-1L', 'item_name' => 'Castrol Power1 4T 10W40 1L', 'brand' => 'Castrol', 'satuan' => 'botol', 'harga_beli' => 72000, 'harga_jual' => 95000, 'harga_online' => 90000, 'stock' => 25, 'minimum_stock' => 8, 'berat' => 1050, 'is_sold_online' => true],
            ['category_id' => $cat('Oli Mesin'), 'sku' => 'OLI-AHM-800', 'item_name' => 'AHM Oil MPX2 800ml', 'brand' => 'AHM', 'satuan' => 'botol', 'harga_beli' => 35000, 'harga_jual' => 48000, 'stock' => 60, 'minimum_stock' => 12],

            // Oli Gardan
            ['category_id' => $cat('Oli Gardan'), 'sku' => 'OLG-END-130', 'item_name' => 'Enduro Matic Gear Oil 130ml', 'brand' => 'Enduro', 'satuan' => 'botol', 'harga_beli' => 18000, 'harga_jual' => 28000, 'stock' => 40, 'minimum_stock' => 10],
            ['category_id' => $cat('Oli Gardan'), 'sku' => 'OLG-YAM-130', 'item_name' => 'Yamalube Gear Oil 130ml', 'brand' => 'Yamaha', 'satuan' => 'botol', 'harga_beli' => 20000, 'harga_jual' => 32000, 'stock' => 35, 'minimum_stock' => 8],

            // Busi
            ['category_id' => $cat('Busi'), 'sku' => 'BUS-NGK-CR8E', 'item_name' => 'NGK CR8E Standar', 'brand' => 'NGK', 'satuan' => 'pcs', 'harga_beli' => 18000, 'harga_jual' => 28000, 'harga_online' => 25000, 'stock' => 50, 'minimum_stock' => 15, 'berat' => 50, 'is_sold_online' => true],
            ['category_id' => $cat('Busi'), 'sku' => 'BUS-NGK-IRD', 'item_name' => 'NGK Iridium CPR8EAIX-9', 'brand' => 'NGK', 'satuan' => 'pcs', 'harga_beli' => 75000, 'harga_jual' => 110000, 'harga_online' => 105000, 'stock' => 20, 'minimum_stock' => 5, 'berat' => 50, 'is_sold_online' => true],
            ['category_id' => $cat('Busi'), 'sku' => 'BUS-DNS-U22', 'item_name' => 'Denso U22FSR-U Racing', 'brand' => 'Denso', 'satuan' => 'pcs', 'harga_beli' => 55000, 'harga_jual' => 80000, 'stock' => 25, 'minimum_stock' => 5],

            // Kampas Rem
            ['category_id' => $cat('Kampas Rem'), 'sku' => 'KAM-HND-DEP', 'item_name' => 'Kampas Rem Depan Honda OEM', 'brand' => 'Honda', 'satuan' => 'set', 'harga_beli' => 35000, 'harga_jual' => 55000, 'harga_online' => 52000, 'stock' => 30, 'minimum_stock' => 8, 'berat' => 150, 'is_sold_online' => true],
            ['category_id' => $cat('Kampas Rem'), 'sku' => 'KAM-HND-BLK', 'item_name' => 'Kampas Rem Belakang Honda OEM', 'brand' => 'Honda', 'satuan' => 'set', 'harga_beli' => 28000, 'harga_jual' => 45000, 'stock' => 25, 'minimum_stock' => 8],
            ['category_id' => $cat('Kampas Rem'), 'sku' => 'KAM-YAM-DEP', 'item_name' => 'Kampas Rem Depan Yamaha OEM', 'brand' => 'Yamaha', 'satuan' => 'set', 'harga_beli' => 38000, 'harga_jual' => 58000, 'stock' => 20, 'minimum_stock' => 6],

            // Filter
            ['category_id' => $cat('Filter Udara'), 'sku' => 'FIL-HND-BT', 'item_name' => 'Filter Udara Honda Beat/Vario OEM', 'brand' => 'Honda', 'satuan' => 'pcs', 'harga_beli' => 28000, 'harga_jual' => 45000, 'stock' => 20, 'minimum_stock' => 5],
            ['category_id' => $cat('Filter Udara'), 'sku' => 'FIL-YAM-NMX', 'item_name' => 'Filter Udara Yamaha NMAX OEM', 'brand' => 'Yamaha', 'satuan' => 'pcs', 'harga_beli' => 32000, 'harga_jual' => 50000, 'stock' => 15, 'minimum_stock' => 5],
            ['category_id' => $cat('Filter Oli'), 'sku' => 'FOL-HND-CBR', 'item_name' => 'Filter Oli Honda CBR150R OEM', 'brand' => 'Honda', 'satuan' => 'pcs', 'harga_beli' => 22000, 'harga_jual' => 38000, 'stock' => 15, 'minimum_stock' => 5],

            // Rantai & Gir
            ['category_id' => $cat('Rantai & Gir'), 'sku' => 'RAN-DID-428', 'item_name' => 'Rantai DID 428 110L', 'brand' => 'DID', 'satuan' => 'pcs', 'harga_beli' => 95000, 'harga_jual' => 140000, 'harga_online' => 135000, 'stock' => 12, 'minimum_stock' => 3, 'berat' => 800, 'is_sold_online' => true],
            ['category_id' => $cat('Rantai & Gir'), 'sku' => 'GIR-DEP-15T', 'item_name' => 'Gir Depan 15T Racing', 'brand' => 'TDR', 'satuan' => 'pcs', 'harga_beli' => 45000, 'harga_jual' => 75000, 'stock' => 10, 'minimum_stock' => 3],
            ['category_id' => $cat('Rantai & Gir'), 'sku' => 'GIR-BLK-34T', 'item_name' => 'Gir Belakang 34T Racing', 'brand' => 'TDR', 'satuan' => 'pcs', 'harga_beli' => 85000, 'harga_jual' => 130000, 'stock' => 8, 'minimum_stock' => 3],

            // Piston
            ['category_id' => $cat('Piston & Blok Silinder'), 'sku' => 'PIS-RCG-56MM', 'item_name' => 'Piston Racing 56mm (bore up kit)', 'brand' => 'Kawahara', 'satuan' => 'set', 'harga_beli' => 350000, 'harga_jual' => 520000, 'stock' => 5, 'minimum_stock' => 2],

            // CDI & Pengapian
            ['category_id' => $cat('CDI & ECU'), 'sku' => 'CDI-BRT-DB', 'item_name' => 'CDI BRT Dual Band', 'brand' => 'BRT', 'satuan' => 'pcs', 'harga_beli' => 380000, 'harga_jual' => 550000, 'harga_online' => 525000, 'stock' => 8, 'minimum_stock' => 2, 'berat' => 200, 'is_sold_online' => true],
            ['category_id' => $cat('Aki & Kelistrikan'), 'sku' => 'AKI-YUA-YTZ', 'item_name' => 'Aki Yuasa YTZ7S MF', 'brand' => 'Yuasa', 'satuan' => 'pcs', 'harga_beli' => 280000, 'harga_jual' => 380000, 'harga_online' => 365000, 'stock' => 10, 'minimum_stock' => 3, 'berat' => 2500, 'is_sold_online' => true],

            // Kopling
            ['category_id' => $cat('Kopling'), 'sku' => 'KOP-YAM-MIO', 'item_name' => 'Kampas Kopling Yamaha Mio OEM', 'brand' => 'Yamaha', 'satuan' => 'set', 'harga_beli' => 65000, 'harga_jual' => 95000, 'stock' => 12, 'minimum_stock' => 4],

            // Knalpot
            ['category_id' => $cat('Knalpot & Exhaust'), 'sku' => 'KNL-R9-TIT', 'item_name' => 'Knalpot R9 Titanium Full System', 'brand' => 'R9', 'satuan' => 'pcs', 'harga_beli' => 1800000, 'harga_jual' => 2500000, 'harga_online' => 2400000, 'stock' => 3, 'minimum_stock' => 1, 'berat' => 3500, 'is_sold_online' => true],
            ['category_id' => $cat('Knalpot & Exhaust'), 'sku' => 'KNL-AHR-SSS', 'item_name' => 'Knalpot Ahrs SSS Carbon', 'brand' => 'Ahrs', 'satuan' => 'pcs', 'harga_beli' => 1200000, 'harga_jual' => 1750000, 'stock' => 4, 'minimum_stock' => 1],

            // Shockbreaker
            ['category_id' => $cat('Shockbreaker'), 'sku' => 'SHK-YSS-RCG', 'item_name' => 'Shockbreaker YSS Racing G-Sport', 'brand' => 'YSS', 'satuan' => 'pcs', 'harga_beli' => 650000, 'harga_jual' => 950000, 'harga_online' => 920000, 'stock' => 5, 'minimum_stock' => 1, 'berat' => 1800, 'is_sold_online' => true],

            // Noken As
            ['category_id' => $cat('Klep & Noken As'), 'sku' => 'NOK-KWH-RCG', 'item_name' => 'Noken As Racing Kawahara Stage 2', 'brand' => 'Kawahara', 'satuan' => 'pcs', 'harga_beli' => 450000, 'harga_jual' => 680000, 'stock' => 5, 'minimum_stock' => 1],
        ];

        foreach ($spareparts as $data) {
            if (! $data['category_id']) {
                continue;
            }

            Sparepart::create(array_merge([
                'harga_online' => null,
                'minimum_stock' => 3,
                'berat' => null,
                'is_active' => true,
                'is_sold_online' => false,
            ], $data));
        }
    }
}
