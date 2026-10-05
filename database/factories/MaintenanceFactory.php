<?php

namespace Database\Factories;

use App\Models\Asset;
use App\Models\Maintenance;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Maintenance>
 */
class MaintenanceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'asset_id' => Asset::factory(),
            'description' => fake()->sentence(),
            'cost' => fake()->randomFloat(2, 10, 500),
            'performed_at' => fake()->date(),
        ];
    }
}
