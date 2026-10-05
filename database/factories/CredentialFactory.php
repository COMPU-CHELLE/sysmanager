<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\Credential;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Credential>
 */
class CredentialFactory extends Factory
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
            'name' => fake()->word(),
            'url' => fake()->url(),
            'username' => fake()->userName(),
            'password' => fake()->password(),
            'type' => fake()->randomElement(['server', 'email', 'router']),
            'access_mode' => 'copy_open',
        ];
    }
}
