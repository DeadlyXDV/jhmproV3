<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProductBundle;
use App\Models\Sparepart;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    private int $counter = 0;

    private function nextOrderNumber(): string
    {
        $this->counter++;

        return 'ORD-'.date('Ym').'-'.str_pad($this->counter, 4, '0', STR_PAD_LEFT);
    }

    public function run(): void
    {
        $customers = Customer::orderBy('id')->get();
        $spr = fn (string $sku) => Sparepart::where('sku', $sku)->first();
        $bundle = fn (string $nama) => ProductBundle::where('nama', $nama)->first();

        $orders = [
            [
                'customer' => $customers->get(0),
                'status' => 'delivered',
                'payment_status' => 'paid',
                'items' => [
                    ['type' => 'sparepart', 'ref' => $spr('OLI-SHL-800'), 'qty' => 2],
                    ['type' => 'sparepart', 'ref' => $spr('BUS-NGK-CR8E'), 'qty' => 2],
                ],
            ],
            [
                'customer' => $customers->get(1),
                'status' => 'shipped',
                'payment_status' => 'paid',
                'items' => [
                    ['type' => 'bundle', 'ref' => $bundle('Paket Ganti Oli Lengkap'), 'qty' => 1],
                ],
            ],
            [
                'customer' => $customers->get(2),
                'status' => 'processing',
                'payment_status' => 'paid',
                'items' => [
                    ['type' => 'sparepart', 'ref' => $spr('KAM-HND-DEP'), 'qty' => 1],
                    ['type' => 'sparepart', 'ref' => $spr('OLI-MTL-1L'), 'qty' => 1],
                ],
            ],
            [
                'customer' => $customers->get(4),
                'status' => 'pending',
                'payment_status' => 'unpaid',
                'items' => [
                    ['type' => 'sparepart', 'ref' => $spr('CDI-BRT-DB'), 'qty' => 1],
                    ['type' => 'sparepart', 'ref' => $spr('BUS-NGK-IRD'), 'qty' => 1],
                ],
            ],
            [
                'customer' => $customers->get(7),
                'status' => 'delivered',
                'payment_status' => 'paid',
                'items' => [
                    ['type' => 'sparepart', 'ref' => $spr('RAN-DID-428'), 'qty' => 1],
                    ['type' => 'sparepart', 'ref' => $spr('GIR-DEP-15T'), 'qty' => 1],
                    ['type' => 'sparepart', 'ref' => $spr('GIR-BLK-34T'), 'qty' => 1],
                ],
            ],
            [
                'customer' => $customers->get(9),
                'status' => 'cancelled',
                'payment_status' => 'refunded',
                'items' => [
                    ['type' => 'sparepart', 'ref' => $spr('SHK-YSS-RCG'), 'qty' => 1],
                ],
            ],
            [
                'customer' => $customers->get(11),
                'status' => 'delivered',
                'payment_status' => 'paid',
                'items' => [
                    ['type' => 'bundle', 'ref' => $bundle('Paket Tune Up Standar'), 'qty' => 1],
                    ['type' => 'sparepart', 'ref' => $spr('OLI-SHL-800'), 'qty' => 1],
                ],
            ],
            [
                'customer' => $customers->get(13),
                'status' => 'processing',
                'payment_status' => 'paid',
                'items' => [
                    ['type' => 'sparepart', 'ref' => $spr('KNL-R9-TIT'), 'qty' => 1],
                ],
            ],
        ];

        $provinces = ['DKI Jakarta', 'Jawa Barat', 'Banten', 'Jawa Tengah'];
        $cities = ['Jakarta Selatan', 'Depok', 'Bekasi', 'Tangerang', 'Bogor'];

        foreach ($orders as $data) {
            /** @var Customer $customer */
            $customer = $data['customer'];
            if (! $customer) {
                continue;
            }

            $subtotal = 0;
            $itemData = [];

            foreach ($data['items'] as $item) {
                $ref = $item['ref'];
                if (! $ref) {
                    continue;
                }

                $harga = $item['type'] === 'bundle' ? $ref->harga : $ref->harga_online ?? $ref->harga_jual;
                $nama = $item['type'] === 'bundle' ? $ref->nama : $ref->item_name;
                $qty = $item['qty'];
                $sub = $harga * $qty;
                $subtotal += $sub;

                $itemData[] = [
                    'type' => $item['type'],
                    'ref' => $ref,
                    'nama' => $nama,
                    'qty' => $qty,
                    'harga' => $harga,
                    'subtotal' => $sub,
                ];
            }

            $ongkir = fake()->randomFloat(2, 15000, 45000);
            $grandTotal = $subtotal + $ongkir;

            /** @var Order $order */
            $order = Order::create([
                'customer_id' => $customer->id,
                'order_number' => $this->nextOrderNumber(),
                'nama_penerima' => $customer->nama,
                'no_hp_penerima' => $customer->no_hp,
                'alamat_kirim' => $customer->alamat ?? 'Jl. Contoh No. 1',
                'provinsi' => fake()->randomElement($provinces),
                'kota' => fake()->randomElement($cities),
                'kecamatan' => 'Kecamatan '.fake()->word(),
                'kode_pos' => fake()->numerify('#####'),
                'subtotal' => $subtotal,
                'ongkir' => $ongkir,
                'discount' => 0,
                'grand_total' => $grandTotal,
                'status' => $data['status'],
                'payment_status' => $data['payment_status'],
            ]);

            foreach ($itemData as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'type' => $item['type'],
                    'sparepart_id' => $item['type'] === 'sparepart' ? $item['ref']->id : null,
                    'bundle_id' => $item['type'] === 'bundle' ? $item['ref']->id : null,
                    'nama_snapshot' => $item['nama'],
                    'qty' => $item['qty'],
                    'harga_snapshot' => $item['harga'],
                    'subtotal' => $item['subtotal'],
                ]);
            }
        }
    }
}
