<?php

namespace Database\Factories;

use App\Models\Asset;
use App\Models\AssetAssignment;
use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AssetAssignment>
 */
class AssetAssignmentFactory extends Factory
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
            'employee_id' => Employee::factory(),
            'assigned_at' => now(),
        ];
    }
}
