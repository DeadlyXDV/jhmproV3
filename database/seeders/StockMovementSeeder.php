<?php

namespace Database\Seeders;

use App\Models\Sparepart;
use App\Models\StockMovement;
use App\Models\User;
use Illuminate\Database\Seeder;

class StockMovementSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('role', 'admin')->first();
        $spr = fn (string $sku) => Sparepart::where('sku', $sku)->first();

        // Stok masuk awal (initial stock-in) untuk semua sparepart
        $initialStocks = [
            'OLI-SHL-800' => 60,
            'OLI-MTL-1L' => 40,
            'OLI-CST-1L' => 35,
            'OLI-AHM-800' => 70,
            'OLG-END-130' => 50,
            'OLG-YAM-130' => 45,
            'BUS-NGK-CR8E' => 60,
            'BUS-NGK-IRD' => 25,
            'BUS-DNS-U22' => 30,
            'KAM-HND-DEP' => 35,
            'KAM-HND-BLK' => 30,
            'KAM-YAM-DEP' => 25,
            'FIL-HND-BT' => 25,
            'FIL-YAM-NMX' => 20,
            'FOL-HND-CBR' => 20,
            'RAN-DID-428' => 15,
            'GIR-DEP-15T' => 12,
            'GIR-BLK-34T' => 10,
            'PIS-RCG-56MM' => 6,
            'CDI-BRT-DB' => 10,
            'AKI-YUA-YTZ' => 12,
            'KOP-YAM-MIO' => 15,
            'KNL-R9-TIT' => 4,
            'KNL-AHR-SSS' => 5,
            'SHK-YSS-RCG' => 6,
            'NOK-KWH-RCG' => 6,
        ];

        foreach ($initialStocks as $sku => $qty) {
            $sparepart = $spr($sku);
            if (! $sparepart) {
                continue;
            }

            StockMovement::create([
                'sparepart_id' => $sparepart->id,
                'user_id' => $admin->id,
                'type' => 'in',
                'qty' => $qty,
                'stock_before' => 0,
                'stock_after' => $qty,
                'reference_type' => null,
                'reference_id' => null,
                'catatan' => 'Stok awal seeder',
            ]);
        }

        // Beberapa pengeluaran manual (keluar stok non-invoice)
        $manualOuts = [
            ['sku' => 'OLI-SHL-800', 'qty' => 5, 'catatan' => 'Pemakaian internal bengkel — test campuran'],
            ['sku' => 'BUS-NGK-CR8E', 'qty' => 3, 'catatan' => 'Rusak saat pengiriman, dibuang'],
            ['sku' => 'KAM-HND-DEP', 'qty' => 2, 'catatan' => 'Dipinjam bengkel partner Honda Jaya'],
        ];

        foreach ($manualOuts as $out) {
            $sparepart = $spr($out['sku']);
            if (! $sparepart) {
                continue;
            }

            $before = $sparepart->stock + $out['qty']; // reverse: hitung stock sebelum
            StockMovement::create([
                'sparepart_id' => $sparepart->id,
                'user_id' => $admin->id,
                'type' => 'out',
                'qty' => $out['qty'],
                'stock_before' => $before,
                'stock_after' => $sparepart->stock,
                'reference_type' => null,
                'reference_id' => null,
                'catatan' => $out['catatan'],
            ]);
        }

        // Adjustment stok (koreksi fisik)
        $adjustments = [
            ['sku' => 'OLG-END-130', 'qty_diff' => -2, 'catatan' => 'Koreksi stock opname: fisik kurang 2 dari sistem'],
            ['sku' => 'FIL-HND-BT', 'qty_diff' => 3, 'catatan' => 'Koreksi stock opname: ditemukan 3 pcs di gudang lama'],
        ];

        foreach ($adjustments as $adj) {
            $sparepart = $spr($adj['sku']);
            if (! $sparepart) {
                continue;
            }

            $before = $sparepart->stock - $adj['qty_diff'];
            StockMovement::create([
                'sparepart_id' => $sparepart->id,
                'user_id' => $admin->id,
                'type' => 'adjustment',
                'qty' => abs($adj['qty_diff']),
                'stock_before' => $before,
                'stock_after' => $sparepart->stock,
                'reference_type' => null,
                'reference_id' => null,
                'catatan' => $adj['catatan'],
            ]);
        }
    }
}
