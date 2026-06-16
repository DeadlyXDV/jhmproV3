<?php

namespace Database\Factories;

use App\Models\Booking;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Booking>
 */
class BookingFactory extends Factory
{
    public function definition(): array
    {
        static $counter = 0;
        $counter++;

        return [
            'booking_number' => 'BK-'.now()->year.'-'.str_pad($counter, 4, '0', STR_PAD_LEFT),
            'tanggal_booking' => fake()->dateTimeBetween('+1 day', '+30 days')->format('Y-m-d'),
            'status' => 'pending',
            'source' => fake()->randomElement(['website', 'whatsapp', 'walk_in']),
            'nama_pemesan' => fake()->name(),
            'no_hp_pemesan' => fake()->numerify('08##########'),
            'keluhan' => fake()->optional()->sentence(),
        ];
    }

    public function confirmed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'confirmed',
            'confirmed_at' => now(),
        ]);
    }

    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'cancelled',
            'cancelled_at' => now(),
        ]);
    }
}
