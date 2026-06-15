<?php

namespace Database\Factories;

use App\Models\Invoice;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Invoice>
 */
class InvoiceFactory extends Factory
{
    public function definition(): array
    {
        $subtotal = fake()->randomFloat(2, 50000, 5000000);

        return [
            'user_id' => User::factory()->state(['role' => 'admin']),
            'invoice_number' => 'INV-'.strtoupper(fake()->unique()->bothify('####??')),
            'tanggal' => fake()->dateTimeBetween('-6 months', 'now'),
            'tipe' => fake()->randomElement(['walk_in', 'booking', 'partner']),
            'subtotal' => $subtotal,
            'discount' => 0,
            'grand_total' => $subtotal,
            'payment_status' => fake()->randomElement(['unpaid', 'partial', 'paid']),
            'amount_paid' => 0,
        ];
    }

    public function paid(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'payment_status' => 'paid',
                'amount_paid' => $attributes['grand_total'],
            ];
        });
    }
}
