<?php

namespace Database\Factories;

// Laravel
use Illuminate\Database\Eloquent\Factories\Factory;

// Models
use App\Models\User;
use App\Models\Product;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Purchase>
 */
class PurchaseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id'    => User::factory(),
            'product_id' => Product::factory(),
            'price'      => $this->faker->randomFloat(2, 1, 100),
        ];
    }
}
