<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        $customers = [
            ['nama' => 'Budi Santoso', 'no_hp' => '081234567890', 'email' => 'budi@gmail.com', 'alamat' => 'Jl. Mawar No. 5, Jakarta Selatan'],
            ['nama' => 'Agus Priyanto', 'no_hp' => '082345678901', 'email' => 'agus.priyanto@yahoo.com', 'alamat' => 'Jl. Anggrek No. 12, Depok'],
            ['nama' => 'Siti Rahayu', 'no_hp' => '083456789012', 'email' => 'siti.rahayu@gmail.com', 'alamat' => 'Jl. Kenanga No. 8, Bekasi Timur'],
            ['nama' => 'Doni Kusuma', 'no_hp' => '085678901234', 'email' => null, 'alamat' => 'Jl. Melati No. 3, Tangerang'],
            ['nama' => 'Rizky Firmansyah', 'no_hp' => '089012345678', 'email' => 'rizky.f@gmail.com', 'alamat' => 'Jl. Dahlia No. 17, Jakarta Barat'],
            ['nama' => 'Hendro Wijaya', 'no_hp' => '081122334455', 'email' => 'hendro.w@hotmail.com', 'alamat' => 'Jl. Cempaka No. 21, Bogor'],
            ['nama' => 'Wahyu Setiawan', 'no_hp' => '082233445566', 'email' => null, 'alamat' => 'Jl. Flamboyan No. 9, Karawang'],
            ['nama' => 'Fajar Nugroho', 'no_hp' => '083344556677', 'email' => 'fajar.nugroho@gmail.com', 'alamat' => 'Jl. Bougenvil No. 14, Jakarta Timur'],
            ['nama' => 'Eko Prasetyo', 'no_hp' => '085566778899', 'email' => 'eko.prasetyo@gmail.com', 'alamat' => 'Jl. Teratai No. 6, Bekasi Barat'],
            ['nama' => 'Andika Saputra', 'no_hp' => '087788990011', 'email' => null, 'alamat' => 'Jl. Seruni No. 25, Depok'],
            ['nama' => 'Hendra Permana', 'no_hp' => '088899001122', 'email' => 'hendra.p@gmail.com', 'alamat' => 'Jl. Lotus No. 33, Jakarta Utara'],
            ['nama' => 'Yusuf Hidayat', 'no_hp' => '081344556677', 'email' => 'yusuf.h@gmail.com', 'alamat' => 'Jl. Tulip No. 11, Tangerang Selatan'],
            ['nama' => 'Bagas Pramono', 'no_hp' => '082455667788', 'email' => null, 'alamat' => 'Jl. Sakura No. 4, Bekasi Selatan'],
            ['nama' => 'Gilang Ramadhan', 'no_hp' => '083566778899', 'email' => 'gilang.r@gmail.com', 'alamat' => 'Jl. Chrysant No. 7, Jakarta Pusat'],
            ['nama' => 'Dimas Ardiansyah', 'no_hp' => '085788990011', 'email' => 'dimas.a@yahoo.com', 'alamat' => 'Jl. Lavender No. 19, Bogor Barat'],
        ];

        foreach ($customers as $data) {
            Customer::create($data);
        }
    }
}
