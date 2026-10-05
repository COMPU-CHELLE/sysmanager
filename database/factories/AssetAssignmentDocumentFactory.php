<?php

namespace Database\Factories;

use App\Models\AssetAssignment;
use App\Models\AssetAssignmentDocument;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AssetAssignmentDocument>
 */
class AssetAssignmentDocumentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'asset_assignment_id' => AssetAssignment::factory(),
            'document_number' => fake()->unique()->bothify('DOC-#####'),
        ];
    }
}
