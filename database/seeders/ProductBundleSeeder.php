<?php

namespace Database\Seeders;

use App\Models\ProductBundle;
use App\Models\ProductBundleItem;
use App\Models\Service;
use App\Models\Sparepart;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductBundleSeeder extends Seeder
{
    public function run(): void
    {
        $svc = fn (string $name) => Service::where('nama_service', $name)->first();
        $spr = fn (string $sku) => Sparepart::where('sku', $sku)->first();

        $bundles = [
            [
                'nama' => 'Paket Tune Up Standar',
                'deskripsi' => 'Paket servis tune up lengkap: jasa tune up + oli mesin Shell Advance + busi NGK CR8E. Hemat dibanding beli terpisah.',
                'harga' => 220000,
                'is_active' => true,
                'is_sold_online' => false,
                'is_bookable' => true,
                'items' => [
                    ['type' => 'service', 'ref' => $svc('Tune Up Standar'), 'qty' => 1],
                    ['type' => 'sparepart', 'ref' => $spr('OLI-SHL-800'), 'qty' => 1],
                    ['type' => 'sparepart', 'ref' => $spr('BUS-NGK-CR8E'), 'qty' => 1],
                ],
            ],
            [
                'nama' => 'Paket Tune Up Plus',
                'deskripsi' => 'Tune up lengkap dengan penggantian kampas rem depan: tune up standar + oli Motul + busi iridium + kampas rem depan.',
                'harga' => 380000,
                'is_active' => true,
                'is_sold_online' => false,
                'is_bookable' => true,
                'items' => [
                    ['type' => 'service', 'ref' => $svc('Tune Up Standar'), 'qty' => 1],
                    ['type' => 'service', 'ref' => $svc('Ganti Kampas Rem Depan'), 'qty' => 1],
                    ['type' => 'sparepart', 'ref' => $spr('OLI-MTL-1L'), 'qty' => 1],
                    ['type' => 'sparepart', 'ref' => $spr('BUS-NGK-IRD'), 'qty' => 1],
                    ['type' => 'sparepart', 'ref' => $spr('KAM-HND-DEP'), 'qty' => 1],
                ],
            ],
            [
                'nama' => 'Paket Ganti Oli Lengkap',
                'deskripsi' => 'Paket ganti oli mesin + oli gardan (khusus matic). Termasuk jasa dan bahan.',
                'harga' => 85000,
                'is_active' => true,
                'is_sold_online' => true,
                'is_bookable' => false,
                'items' => [
                    ['type' => 'service', 'ref' => $svc('Ganti Oli Mesin'), 'qty' => 1],
                    ['type' => 'service', 'ref' => $svc('Ganti Oli Gardan'), 'qty' => 1],
                    ['type' => 'sparepart', 'ref' => $spr('OLI-SHL-800'), 'qty' => 1],
                    ['type' => 'sparepart', 'ref' => $spr('OLG-END-130'), 'qty' => 1],
                ],
            ],
            [
                'nama' => 'Paket Bore Up 125→150cc',
                'deskripsi' => 'Paket modifikasi bore up lengkap: jasa bore up + piston racing 56mm + oli. Cocok untuk CBR150R dan Satria FU.',
                'harga' => 1950000,
                'is_active' => true,
                'is_sold_online' => false,
                'is_bookable' => true,
                'items' => [
                    ['type' => 'service', 'ref' => $svc('Bore Up 125cc ke 150cc'), 'qty' => 1],
                    ['type' => 'sparepart', 'ref' => $spr('PIS-RCG-56MM'), 'qty' => 1],
                    ['type' => 'sparepart', 'ref' => $spr('OLI-MTL-1L'), 'qty' => 1],
                ],
            ],
            [
                'nama' => 'Paket Knalpot Racing Full',
                'deskripsi' => 'Pasang knalpot R9 Titanium lengkap dengan jasa setting ulang karbu/injeksi dan tune up.',
                'harga' => 2800000,
                'is_active' => true,
                'is_sold_online' => false,
                'is_bookable' => true,
                'items' => [
                    ['type' => 'service', 'ref' => $svc('Pasang Knalpot Racing'), 'qty' => 1],
                    ['type' => 'service', 'ref' => $svc('Tune Up Standar'), 'qty' => 1],
                    ['type' => 'sparepart', 'ref' => $spr('KNL-R9-TIT'), 'qty' => 1],
                ],
            ],
        ];

        foreach ($bundles as $bundleData) {
            $items = $bundleData['items'];
            unset($bundleData['items']);

            $bundle = ProductBundle::create(array_merge($bundleData, [
                'slug' => Str::slug($bundleData['nama']),
            ]));

            foreach ($items as $item) {
                if (! $item['ref']) {
                    continue;
                }

                $harga = $item['type'] === 'service'
                    ? $item['ref']->harga_default
                    : $item['ref']->harga_jual;

                ProductBundleItem::create([
                    'bundle_id' => $bundle->id,
                    'type' => $item['type'],
                    'service_id' => $item['type'] === 'service' ? $item['ref']->id : null,
                    'sparepart_id' => $item['type'] === 'sparepart' ? $item['ref']->id : null,
                    'qty' => $item['qty'],
                    'harga_snapshot' => $harga,
                ]);
            }
        }
    }
}
