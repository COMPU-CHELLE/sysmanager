<?php

namespace Tests\Feature;

use App\Models\Role;
use Database\Seeders\AccessControlSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateInitialAdminCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_a_super_admin_user(): void
    {
        $this->seed(AccessControlSeeder::class);

        $this->artisan('sysmanager:make-admin', [
            'name' => 'System Administrator',
            'code' => 'admin',
            'email' => 'admin@example.com',
        ])
            ->expectsQuestion('Contraseña', 'password123')
            ->assertSuccessful();

        $this->assertDatabaseHas('users', [
            'name' => 'System Administrator',
            'code' => 'admin',
            'email' => 'admin@example.com',
        ]);

        $this->assertDatabaseHas('role_user', [
            'role_id' => Role::query()->where('code', 'super-admin')->value('id'),
        ]);
    }
}
