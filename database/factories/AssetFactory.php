<?php

namespace Database\Factories;

use App\Models\Asset;
use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Asset>
 */
class AssetFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'company_id' => Company::factory(),
            'name' => fake()->words(2, true),
            'type' => fake()->randomElement(['Laptop', 'Desktop', 'Printer']),
            'serial' => fake()->unique()->bothify('SN-#######'),
            'brand' => fake()->company(),
            'model' => fake()->bothify('M-###'),
            'purchase_date' => fake()->date(),
            'cost' => fake()->randomFloat(2, 100, 3000),
        ];
    }
}
