<?php

namespace Database\Factories;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Customer>
 */
class CustomerFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nama' => fake()->name(),
            'no_hp' => fake()->numerify('08##########'),
            'email' => fake()->unique()->safeEmail(),
            'alamat' => fake()->address(),
        ];
    }
}
