<?php

namespace Database\Factories;

use App\Models\Branch;
use App\Models\Company;
use App\Models\Invoice;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Invoice>
 */
class InvoiceFactory extends Factory
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
            'branch_id' => Branch::factory(),
            'number' => fake()->unique()->bothify('FAC-#####'),
            'provider' => fake()->company(),
            'date' => fake()->date(),
            'category' => fake()->randomElement(['Hardware', 'Software', 'Servicios']),
        ];
    }
}
