<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transaction>
 */
class TransactionFactory extends Factory
{
    protected $model = Transaction::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'transaction_type' => fake()->randomElement(['IN', 'OUT']),
            'quantity' => fake()->numberBetween(1, 50),
            'unit_price' => fake()->randomFloat(2, 10, 5000),
            'remarks' => fake()->optional()->sentence(),
            'reference_number' => fake()->optional()->bothify('REF-####-????'),
            'transaction_time' => fake()->dateTimeBetween('-1 month', 'now'),
        ];
    }
}