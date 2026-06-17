<?php

namespace Database\Seeders;

use App\Models\Partner;
use Illuminate\Database\Seeder;

class PartnerSeeder extends Seeder
{
    public function run(): void
    {
        $partners = [
            [
                'nama_bengkel' => 'Bengkel Honda Jaya Motor',
                'contact_person' => 'Pak Hasan',
                'no_hp' => '021-5551234',
                'alamat' => 'Jl. Raya Fatmawati No. 88, Jakarta Selatan',
                'catatan' => 'Partner resmi Honda. Komisi 10% per referral.',
            ],
            [
                'nama_bengkel' => 'Yamaha Authorized Service Center',
                'contact_person' => 'Bu Dewi',
                'no_hp' => '021-7779012',
                'alamat' => 'Jl. Margonda Raya No. 45, Depok',
                'catatan' => 'Partner resmi Yamaha. Kirim customer yang butuh garansi resmi.',
            ],
            [
                'nama_bengkel' => 'Suzuki Service Center Pusat',
                'contact_person' => 'Pak Bejo',
                'no_hp' => '021-8886789',
                'alamat' => 'Jl. Pondok Kelapa No. 12, Jakarta Timur',
                'catatan' => 'Partner Suzuki. Biasa ambil sparepart original bersama.',
            ],
            [
                'nama_bengkel' => 'Kawasaki Workshop Mandiri',
                'contact_person' => 'Mas Tono',
                'no_hp' => '0812-9990011',
                'alamat' => 'Jl. Bekasi Raya No. 67, Bekasi Barat',
                'catatan' => 'Spesialis Kawasaki. Kolaborasi untuk modif Ninja dan Z series.',
            ],
            [
                'nama_bengkel' => 'Bengkel Umum Bersama Motor',
                'contact_person' => 'Pak Slamet',
                'no_hp' => '0857-1122334',
                'alamat' => 'Jl. Cibubur No. 30, Jakarta Timur',
                'catatan' => 'Partner general. Rujuk customer saat bengkel sedang penuh.',
            ],
        ];

        foreach ($partners as $data) {
            Partner::create($data);
        }
    }
}
