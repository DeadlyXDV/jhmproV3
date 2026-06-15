<?php

namespace Database\Factories;

use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Service>
 */
class ServiceFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nama_service' => fake()->words(3, true),
            'deskripsi' => fake()->sentence(),
            'harga_default' => fake()->randomFloat(2, 50000, 2000000),
            'durasi_estimasi' => fake()->numberBetween(30, 480),
            'is_active' => true,
            'is_bookable' => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
