<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Vehicle;
use Illuminate\Database\Seeder;

class VehicleSeeder extends Seeder
{
    public function run(): void
    {
        $vehicles = [
            // Budi Santoso
            ['merk' => 'Honda', 'model' => 'CBR150R', 'tipe' => 'Sport', 'tahun' => 2022, 'no_polisi' => 'B 1234 BDI', 'warna' => 'Merah'],
            ['merk' => 'Yamaha', 'model' => 'NMAX 155', 'tipe' => 'Matic', 'tahun' => 2021, 'no_polisi' => 'B 5678 BDI', 'warna' => 'Hitam'],
            // Agus Priyanto
            ['merk' => 'Yamaha', 'model' => 'R15 V4', 'tipe' => 'Sport', 'tahun' => 2023, 'no_polisi' => 'B 2345 AGS', 'warna' => 'Biru'],
            // Siti Rahayu
            ['merk' => 'Honda', 'model' => 'Vario 160', 'tipe' => 'Matic', 'tahun' => 2023, 'no_polisi' => 'B 3456 STI', 'warna' => 'Putih'],
            ['merk' => 'Honda', 'model' => 'Beat Street', 'tipe' => 'Matic', 'tahun' => 2020, 'no_polisi' => 'B 4567 STI', 'warna' => 'Hitam'],
            // Doni Kusuma
            ['merk' => 'Kawasaki', 'model' => 'Ninja ZX25R', 'tipe' => 'Sport', 'tahun' => 2022, 'no_polisi' => 'B 5678 DNI', 'warna' => 'Hijau Matte', 'catatan' => 'Full modif racing, knalpot R9'],
            // Rizky Firmansyah
            ['merk' => 'Yamaha', 'model' => 'Aerox 155', 'tipe' => 'Matic', 'tahun' => 2021, 'no_polisi' => 'B 6789 RZK', 'warna' => 'Putih'],
            ['merk' => 'Suzuki', 'model' => 'GSX-R150', 'tipe' => 'Sport', 'tahun' => 2019, 'no_polisi' => 'B 7890 RZK', 'warna' => 'Merah', 'catatan' => 'Bore up 180cc'],
            // Hendro Wijaya
            ['merk' => 'Honda', 'model' => 'Supra GTR 150', 'tipe' => 'Bebek', 'tahun' => 2020, 'no_polisi' => 'F 1234 HND', 'warna' => 'Merah'],
            // Wahyu Setiawan
            ['merk' => 'Yamaha', 'model' => 'WR 155R', 'tipe' => 'Trail', 'tahun' => 2022, 'no_polisi' => 'T 2345 WHY', 'warna' => 'Kuning Racing'],
            // Fajar Nugroho
            ['merk' => 'Honda', 'model' => 'PCX 160', 'tipe' => 'Matic', 'tahun' => 2023, 'no_polisi' => 'B 3456 FJR', 'warna' => 'Abu-abu'],
            ['merk' => 'Kawasaki', 'model' => 'KLX 150BF', 'tipe' => 'Trail', 'tahun' => 2021, 'no_polisi' => 'B 4567 FJR', 'warna' => 'Hijau Matte'],
            // Eko Prasetyo
            ['merk' => 'Yamaha', 'model' => 'Mio M3', 'tipe' => 'Matic', 'tahun' => 2019, 'no_polisi' => 'B 5678 EKO', 'warna' => 'Putih'],
            // Andika Saputra
            ['merk' => 'Honda', 'model' => 'Vario 125', 'tipe' => 'Matic', 'tahun' => 2022, 'no_polisi' => 'B 6789 ADK', 'warna' => 'Hitam'],
            ['merk' => 'Suzuki', 'model' => 'Satria F150', 'tipe' => 'Sport', 'tahun' => 2020, 'no_polisi' => 'B 7890 ADK', 'warna' => 'Biru', 'catatan' => 'Modif street racing'],
            // Hendra Permana
            ['merk' => 'Kawasaki', 'model' => 'Z250SL', 'tipe' => 'Sport', 'tahun' => 2021, 'no_polisi' => 'B 8901 HDR', 'warna' => 'Hitam'],
            // Yusuf Hidayat
            ['merk' => 'Honda', 'model' => 'CBR150R', 'tipe' => 'Sport', 'tahun' => 2020, 'no_polisi' => 'B 9012 YSF', 'warna' => 'Putih'],
            // Bagas Pramono
            ['merk' => 'Yamaha', 'model' => 'FreeGo', 'tipe' => 'Matic', 'tahun' => 2023, 'no_polisi' => 'B 0123 BGS', 'warna' => 'Abu-abu'],
            // Gilang Ramadhan
            ['merk' => 'Honda', 'model' => 'Genio', 'tipe' => 'Matic', 'tahun' => 2021, 'no_polisi' => 'B 1357 GLG', 'warna' => 'Merah'],
            ['merk' => 'Kawasaki', 'model' => 'Versys-X 250', 'tipe' => 'Sport Touring', 'tahun' => 2022, 'no_polisi' => 'B 2468 GLG', 'warna' => 'Hijau Matte'],
            // Dimas Ardiansyah
            ['merk' => 'Yamaha', 'model' => 'Lexi LX 155', 'tipe' => 'Matic', 'tahun' => 2023, 'no_polisi' => 'F 3579 DMS', 'warna' => 'Biru'],
        ];

        $customers = Customer::orderBy('id')->get();

        // Distribute vehicles ke customers berdasarkan index
        $customerVehicleMap = [
            0 => [0, 1],   // Budi: 2 motor
            1 => [2],       // Agus: 1 motor
            2 => [3, 4],   // Siti: 2 motor
            3 => [5],       // Doni: 1 motor
            4 => [6, 7],   // Rizky: 2 motor
            5 => [8],       // Hendro: 1 motor
            6 => [9],       // Wahyu: 1 motor
            7 => [10, 11], // Fajar: 2 motor
            8 => [12],      // Eko: 1 motor
            9 => [13, 14], // Andika: 2 motor
            10 => [15],     // Hendra: 1 motor
            11 => [16],     // Yusuf: 1 motor
            12 => [17],     // Bagas: 1 motor
            13 => [18, 19], // Gilang: 2 motor
            14 => [20],     // Dimas: 1 motor
        ];

        foreach ($customerVehicleMap as $customerIdx => $vehicleIdxList) {
            $customer = $customers->get($customerIdx);
            if (! $customer) {
                continue;
            }

            foreach ($vehicleIdxList as $vehicleIdx) {
                if (isset($vehicles[$vehicleIdx])) {
                    Vehicle::create(array_merge($vehicles[$vehicleIdx], ['customer_id' => $customer->id]));
                }
            }
        }
    }
}
