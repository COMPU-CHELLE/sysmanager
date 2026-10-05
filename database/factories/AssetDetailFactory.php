<?php

namespace Database\Factories;

use App\Models\Asset;
use App\Models\AssetDetail;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AssetDetail>
 */
class AssetDetailFactory extends Factory
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
            'ram_type' => 'DDR4',
            'ram_capacity' => '16GB',
        ];
    }
}
