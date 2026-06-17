<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Partner;
use App\Models\Service;
use App\Models\Sparepart;
use App\Models\User;
use App\Models\WorkOrder;
use Illuminate\Database\Seeder;

class InvoiceSeeder extends Seeder
{
    private int $invCounter = 0;

    private int $woCounter = 0;

    private function nextInvNumber(): string
    {
        $this->invCounter++;

        return 'INV-'.date('Ym').'-'.str_pad($this->invCounter, 4, '0', STR_PAD_LEFT);
    }

    private function nextWoNumber(): string
    {
        $this->woCounter++;

        return 'WO-'.str_pad($this->woCounter, 4, '0', STR_PAD_LEFT);
    }

    public function run(): void
    {
        $admin = User::where('role', 'admin')->first();
        $mekanik = User::where('role', 'mekanik')->first();
        $customers = Customer::with('vehicles')->get();
        $partner = Partner::first();

        $svc = fn (string $name) => Service::where('nama_service', $name)->first();
        $spr = fn (string $sku) => Sparepart::where('sku', $sku)->first();

        $invoiceScenarios = [
            // Walk-in — servis ringan
            [
                'customer' => $customers->get(0), 'tipe' => 'walk_in',
                'tanggal' => now()->subDays(2),
                'payment_status' => 'paid',
                'items' => [
                    ['type' => 'service', 'ref' => $svc('Tune Up Standar'), 'qty' => 1],
                    ['type' => 'sparepart', 'ref' => $spr('OLI-SHL-800'), 'qty' => 1],
                    ['type' => 'sparepart', 'ref' => $spr('BUS-NGK-CR8E'), 'qty' => 1],
                ],
                'wo_status' => 'selesai',
            ],
            // Walk-in — ganti oli saja
            [
                'customer' => $customers->get(1), 'tipe' => 'walk_in',
                'tanggal' => now()->subDays(4),
                'payment_status' => 'paid',
                'items' => [
                    ['type' => 'service', 'ref' => $svc('Ganti Oli Mesin'), 'qty' => 1],
                    ['type' => 'sparepart', 'ref' => $spr('OLI-MTL-1L'), 'qty' => 1],
                    ['type' => 'sparepart', 'ref' => $spr('FOL-HND-CBR'), 'qty' => 1],
                ],
                'wo_status' => 'selesai',
            ],
            // Walk-in — ganti kampas rem
            [
                'customer' => $customers->get(2), 'tipe' => 'walk_in',
                'tanggal' => now()->subDays(6),
                'payment_status' => 'paid',
                'items' => [
                    ['type' => 'service', 'ref' => $svc('Ganti Kampas Rem Depan'), 'qty' => 1],
                    ['type' => 'service', 'ref' => $svc('Ganti Kampas Rem Belakang'), 'qty' => 1],
                    ['type' => 'sparepart', 'ref' => $spr('KAM-HND-DEP'), 'qty' => 1],
                    ['type' => 'sparepart', 'ref' => $spr('KAM-HND-BLK'), 'qty' => 1],
                ],
                'wo_status' => 'selesai',
            ],
            // Modifikasi besar — bore up + porting polish
            [
                'customer' => $customers->get(3), 'tipe' => 'walk_in',
                'tanggal' => now()->subDays(10),
                'payment_status' => 'paid',
                'discount' => 100000,
                'items' => [
                    ['type' => 'service', 'ref' => $svc('Bore Up 125cc ke 150cc'), 'qty' => 1],
                    ['type' => 'service', 'ref' => $svc('Porting Polish Head'), 'qty' => 1],
                    ['type' => 'sparepart', 'ref' => $spr('PIS-RCG-56MM'), 'qty' => 1],
                    ['type' => 'sparepart', 'ref' => $spr('OLI-MTL-1L'), 'qty' => 1],
                    ['type' => 'sparepart', 'ref' => $spr('NOK-KWH-RCG'), 'qty' => 1],
                ],
                'wo_status' => 'selesai',
            ],
            // Pasang knalpot racing
            [
                'customer' => $customers->get(4), 'tipe' => 'walk_in',
                'tanggal' => now()->subDays(12),
                'payment_status' => 'paid',
                'items' => [
                    ['type' => 'service', 'ref' => $svc('Pasang Knalpot Racing'), 'qty' => 1],
                    ['type' => 'sparepart', 'ref' => $spr('KNL-R9-TIT'), 'qty' => 1],
                ],
                'wo_status' => 'selesai',
            ],
            // Belum bayar — sedang proses
            [
                'customer' => $customers->get(5), 'tipe' => 'walk_in',
                'tanggal' => now()->subDays(1),
                'payment_status' => 'unpaid',
                'items' => [
                    ['type' => 'service', 'ref' => $svc('Overhaul Mesin'), 'qty' => 1],
                    ['type' => 'sparepart', 'ref' => $spr('OLI-CST-1L'), 'qty' => 2],
                ],
                'wo_status' => 'proses',
            ],
            // Bayar sebagian (partial)
            [
                'customer' => $customers->get(6), 'tipe' => 'walk_in',
                'tanggal' => now()->subDays(3),
                'payment_status' => 'partial',
                'items' => [
                    ['type' => 'service', 'ref' => $svc('Tune Up Racing'), 'qty' => 1],
                    ['type' => 'sparepart', 'ref' => $spr('CDI-BRT-DB'), 'qty' => 1],
                    ['type' => 'sparepart', 'ref' => $spr('BUS-NGK-IRD'), 'qty' => 1],
                ],
                'amount_paid_pct' => 0.5,
                'wo_status' => 'selesai',
            ],
            // Partner invoice
            [
                'customer' => null, 'tipe' => 'partner',
                'partner' => $partner,
                'tanggal' => now()->subDays(8),
                'payment_status' => 'paid',
                'items' => [
                    ['type' => 'service', 'ref' => $svc('Tune Up Standar'), 'qty' => 2],
                    ['type' => 'sparepart', 'ref' => $spr('OLI-SHL-800'), 'qty' => 2],
                ],
                'wo_status' => 'selesai',
            ],
            // Booking yang sudah selesai
            [
                'customer' => $customers->get(7), 'tipe' => 'booking',
                'tanggal' => now()->subDays(15),
                'payment_status' => 'paid',
                'items' => [
                    ['type' => 'service', 'ref' => $svc('Tune Up Standar'), 'qty' => 1],
                    ['type' => 'sparepart', 'ref' => $spr('OLI-AHM-800'), 'qty' => 1],
                    ['type' => 'sparepart', 'ref' => $spr('FIL-HND-BT'), 'qty' => 1],
                ],
                'wo_status' => 'selesai',
            ],
            // Servis rantai & gir
            [
                'customer' => $customers->get(8), 'tipe' => 'walk_in',
                'tanggal' => now()->subDays(20),
                'payment_status' => 'paid',
                'items' => [
                    ['type' => 'service', 'ref' => $svc('Ganti Rantai & Gir Set'), 'qty' => 1],
                    ['type' => 'sparepart', 'ref' => $spr('RAN-DID-428'), 'qty' => 1],
                    ['type' => 'sparepart', 'ref' => $spr('GIR-DEP-15T'), 'qty' => 1],
                    ['type' => 'sparepart', 'ref' => $spr('GIR-BLK-34T'), 'qty' => 1],
                ],
                'wo_status' => 'selesai',
            ],
            // Modif kelistrikan
            [
                'customer' => $customers->get(9), 'tipe' => 'walk_in',
                'tanggal' => now()->subDays(25),
                'payment_status' => 'paid',
                'items' => [
                    ['type' => 'service', 'ref' => $svc('Modifikasi Kelistrikan'), 'qty' => 1],
                    ['type' => 'sparepart', 'ref' => $spr('CDI-BRT-DB'), 'qty' => 1],
                    ['type' => 'sparepart', 'ref' => $spr('AKI-YUA-YTZ'), 'qty' => 1],
                ],
                'wo_status' => 'selesai',
            ],
            // Walk-in antrian hari ini
            [
                'customer' => $customers->get(10), 'tipe' => 'walk_in',
                'tanggal' => now(),
                'payment_status' => 'unpaid',
                'items' => [
                    ['type' => 'service', 'ref' => $svc('Periksa & Setel Klep'), 'qty' => 1],
                    ['type' => 'service', 'ref' => $svc('Servis Karburator'), 'qty' => 1],
                ],
                'wo_status' => 'antrian',
            ],
        ];

        foreach ($invoiceScenarios as $scenario) {
            $customer = $scenario['customer'];
            $vehicle = $customer?->vehicles->first();
            $partner = $scenario['partner'] ?? null;

            // Hitung total dari items
            $subtotal = 0;
            $itemData = [];

            foreach ($scenario['items'] as $item) {
                $ref = $item['ref'];
                if (! $ref) {
                    continue;
                }

                $hargaJual = $item['type'] === 'service' ? $ref->harga_default : $ref->harga_jual;
                $hargaBeli = $item['type'] === 'sparepart' ? $ref->harga_beli : 0;
                $qty = $item['qty'];
                $itemSubtotal = $hargaJual * $qty;
                $subtotal += $itemSubtotal;

                $itemData[] = [
                    'type' => $item['type'],
                    'ref' => $ref,
                    'qty' => $qty,
                    'harga_jual' => $hargaJual,
                    'harga_beli' => $hargaBeli,
                    'subtotal' => $itemSubtotal,
                ];
            }

            $discount = $scenario['discount'] ?? 0;
            $grandTotal = $subtotal - $discount;

            $amountPaid = match ($scenario['payment_status']) {
                'paid' => $grandTotal,
                'partial' => $grandTotal * ($scenario['amount_paid_pct'] ?? 0.5),
                default => 0,
            };

            /** @var Invoice $invoice */
            $invoice = Invoice::create([
                'user_id' => $admin->id,
                'customer_id' => $customer?->id,
                'vehicle_id' => $vehicle?->id,
                'partner_id' => $partner?->id,
                'invoice_number' => $this->nextInvNumber(),
                'tanggal' => $scenario['tanggal'],
                'tipe' => $scenario['tipe'],
                'catatan' => null,
                'subtotal' => $subtotal,
                'discount' => $discount,
                'grand_total' => $grandTotal,
                'payment_status' => $scenario['payment_status'],
                'amount_paid' => $amountPaid,
            ]);

            foreach ($itemData as $item) {
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'type' => $item['type'],
                    'service_id' => $item['type'] === 'service' ? $item['ref']->id : null,
                    'sparepart_id' => $item['type'] === 'sparepart' ? $item['ref']->id : null,
                    'nama_snapshot' => $item['type'] === 'service' ? $item['ref']->nama_service : $item['ref']->item_name,
                    'qty' => $item['qty'],
                    'harga_jual' => $item['harga_jual'],
                    'harga_beli_snapshot' => $item['harga_beli'],
                    'subtotal' => $item['subtotal'],
                ]);
            }

            // Buat work order jika ada vehicle (set wo_number manual karena WithoutModelEvents menekan boot event)
            if ($vehicle && isset($scenario['wo_status'])) {
                WorkOrder::create([
                    'wo_number' => $this->nextWoNumber(),
                    'invoice_id' => $invoice->id,
                    'vehicle_id' => $vehicle->id,
                    'mekanik_id' => $mekanik?->id,
                    'status' => $scenario['wo_status'],
                    'keluhan_customer' => 'Keluhan dari invoice '.$invoice->invoice_number,
                    'mulai_at' => in_array($scenario['wo_status'], ['proses', 'selesai']) ? $scenario['tanggal'] : null,
                    'selesai_at' => $scenario['wo_status'] === 'selesai' ? $scenario['tanggal']->addHours(2) : null,
                ]);
            }
        }
    }
}
