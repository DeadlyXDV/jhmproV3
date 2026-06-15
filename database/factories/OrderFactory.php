<?php

namespace Database\Factories;

use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    public function definition(): array
    {
        $subtotal = fake()->randomFloat(2, 50000, 5000000);
        $ongkir = fake()->randomFloat(2, 10000, 100000);

        return [
            'order_number' => 'ORD-'.strtoupper(fake()->unique()->bothify('####??')),
            'nama_penerima' => fake()->name(),
            'no_hp_penerima' => fake()->numerify('08##########'),
            'alamat_kirim' => fake()->address(),
            'provinsi' => fake()->state(),
            'kota' => fake()->city(),
            'kecamatan' => fake()->word(),
            'kode_pos' => fake()->postcode(),
            'subtotal' => $subtotal,
            'ongkir' => $ongkir,
            'discount' => 0,
            'grand_total' => $subtotal + $ongkir,
            'status' => fake()->randomElement(['pending', 'processing', 'shipped', 'delivered', 'cancelled']),
            'payment_status' => fake()->randomElement(['unpaid', 'paid', 'refunded']),
        ];
    }

    public function paid(): static
    {
        return $this->state(fn (array $attributes) => [
            'payment_status' => 'paid',
            'status' => 'delivered',
        ]);
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'payment_status' => 'unpaid',
            'status' => 'pending',
        ]);
    }
}
