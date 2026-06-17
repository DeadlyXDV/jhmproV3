<?php

namespace Database\Factories;

use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Vehicle>
 */
class VehicleFactory extends Factory
{
    /** @var array<string, array<string>> */
    private static array $catalog = [
        'Honda' => ['Beat Street', 'Vario 125', 'Vario 160', 'CBR150R', 'Supra GTR 150', 'Genio', 'PCX 160'],
        'Yamaha' => ['NMAX 155', 'Aerox 155', 'R15 V4', 'Mio M3', 'FreeGo', 'Lexi LX 155', 'WR 155R'],
        'Suzuki' => ['GSX-R150', 'Satria F150', 'Smash 115', 'Address 115'],
        'Kawasaki' => ['Ninja ZX25R', 'KLX 150BF', 'Z250SL', 'W175', 'Versys-X 250'],
    ];

    public function definition(): array
    {
        $merk = fake()->randomElement(array_keys(self::$catalog));
        $model = fake()->randomElement(self::$catalog[$merk]);

        return [
            'merk' => $merk,
            'model' => $model,
            'tipe' => fake()->randomElement(['Sport', 'Matic', 'Trail', 'Sport Touring', 'Bebek']),
            'tahun' => fake()->numberBetween(2016, 2024),
            'no_polisi' => strtoupper(fake()->bothify('B #### ???')),
            'no_rangka' => strtoupper(fake()->bothify('MH1JFD1??HK######')),
            'no_mesin' => strtoupper(fake()->bothify('JFD1E?######')),
            'warna' => fake()->randomElement(['Merah', 'Hitam', 'Putih', 'Biru', 'Abu-abu', 'Hijau Matte', 'Kuning Racing']),
            'catatan' => fake()->optional(0.4)->sentence(),
        ];
    }
}
