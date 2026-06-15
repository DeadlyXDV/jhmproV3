<?php

namespace Database\Factories;

use App\Models\Sparepart;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Sparepart>
 */
class SparepartFactory extends Factory
{
    public function definition(): array
    {
        $hargaBeli = fake()->randomFloat(2, 10000, 500000);

        return [
            'sku' => strtoupper(fake()->unique()->bothify('SPR-####??')),
            'item_name' => fake()->words(3, true),
            'brand' => fake()->company(),
            'satuan' => fake()->randomElement(['pcs', 'set', 'liter', 'meter']),
            'harga_beli' => $hargaBeli,
            'harga_jual' => $hargaBeli * 1.3,
            'stock' => fake()->numberBetween(5, 100),
            'minimum_stock' => 3,
            'is_active' => true,
            'is_sold_online' => false,
        ];
    }

    public function outOfStock(): static
    {
        return $this->state(fn (array $attributes) => [
            'stock' => 0,
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
