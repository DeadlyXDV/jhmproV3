<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        $services = [
            // Tune Up
            ['nama_service' => 'Tune Up Standar', 'deskripsi' => 'Servis rutin: bersihkan karbu/injektor, setel klep, cek rantai keteng, setel celah busi.', 'harga_default' => 150000, 'durasi_estimasi' => 60, 'is_active' => true, 'is_bookable' => true],
            ['nama_service' => 'Tune Up Racing', 'deskripsi' => 'Tune up performa tinggi: porting polish head, setting spuyer, CDI racing, dyno test.', 'harga_default' => 500000, 'durasi_estimasi' => 180, 'is_active' => true, 'is_bookable' => true],

            // Ganti Oli
            ['nama_service' => 'Ganti Oli Mesin', 'deskripsi' => 'Jasa penggantian oli mesin (tidak termasuk oli).', 'harga_default' => 25000, 'durasi_estimasi' => 15, 'is_active' => true, 'is_bookable' => false],
            ['nama_service' => 'Ganti Oli Gardan', 'deskripsi' => 'Jasa penggantian oli gardan/transmisi matic (tidak termasuk oli).', 'harga_default' => 15000, 'durasi_estimasi' => 10, 'is_active' => true, 'is_bookable' => false],

            // Rem
            ['nama_service' => 'Ganti Kampas Rem Depan', 'deskripsi' => 'Jasa penggantian kampas rem depan dan bleeding minyak rem.', 'harga_default' => 35000, 'durasi_estimasi' => 20, 'is_active' => true, 'is_bookable' => false],
            ['nama_service' => 'Ganti Kampas Rem Belakang', 'deskripsi' => 'Jasa penggantian kampas rem belakang.', 'harga_default' => 30000, 'durasi_estimasi' => 20, 'is_active' => true, 'is_bookable' => false],

            // Mesin berat
            ['nama_service' => 'Overhaul Mesin', 'deskripsi' => 'Pembongkaran total mesin: bersihkan, ganti gasket, setel kerenggangan komponen.', 'harga_default' => 1500000, 'durasi_estimasi' => 480, 'is_active' => true, 'is_bookable' => true],
            ['nama_service' => 'Bore Up 125cc ke 150cc', 'deskripsi' => 'Modifikasi bore up blok silinder dari 125cc ke 150cc menggunakan piston racing.', 'harga_default' => 1200000, 'durasi_estimasi' => 360, 'is_active' => true, 'is_bookable' => true],
            ['nama_service' => 'Porting Polish Head', 'deskripsi' => 'Penghalusan dan pembesaran lubang isap/buang di head silinder untuk performa optimal.', 'harga_default' => 800000, 'durasi_estimasi' => 240, 'is_active' => true, 'is_bookable' => true],
            ['nama_service' => 'Periksa & Setel Klep', 'deskripsi' => 'Pemeriksaan dan penyetelan celah klep sesuai spesifikasi.', 'harga_default' => 100000, 'durasi_estimasi' => 45, 'is_active' => true, 'is_bookable' => false],

            // Lainnya
            ['nama_service' => 'Pasang Knalpot Racing', 'deskripsi' => 'Jasa pemasangan knalpot racing termasuk setting ulang karbu/injeksi.', 'harga_default' => 150000, 'durasi_estimasi' => 60, 'is_active' => true, 'is_bookable' => false],
            ['nama_service' => 'Ganti Rantai & Gir Set', 'deskripsi' => 'Penggantian rantai dan gir depan+belakang (tidak termasuk spare part).', 'harga_default' => 75000, 'durasi_estimasi' => 30, 'is_active' => true, 'is_bookable' => false],
            ['nama_service' => 'Servis Karburator', 'deskripsi' => 'Bongkar, bersihkan, dan setting ulang karburator.', 'harga_default' => 100000, 'durasi_estimasi' => 60, 'is_active' => true, 'is_bookable' => false],
            ['nama_service' => 'Balancing Roda', 'deskripsi' => 'Pemeriksaan dan balancing roda depan/belakang.', 'harga_default' => 50000, 'durasi_estimasi' => 30, 'is_active' => true, 'is_bookable' => false],
            ['nama_service' => 'Modifikasi Kelistrikan', 'deskripsi' => 'Pasang CDI racing, koil racing, atau modifikasi sistem kelistrikan lainnya.', 'harga_default' => 200000, 'durasi_estimasi' => 90, 'is_active' => true, 'is_bookable' => true],
        ];

        foreach ($services as $data) {
            Service::create($data);
        }
    }
}
