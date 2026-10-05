<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\Role;
use Database\Seeders\AccessControlSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccessControlSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_seeds_the_permission_matrix_and_default_roles(): void
    {
        Permission::factory()->create(['code' => 'companies.manage']);

        $this->seed(AccessControlSeeder::class);
        $this->seed(AccessControlSeeder::class);

        $this->assertDatabaseCount('permissions', 18);
        $this->assertDatabaseMissing('permissions', ['code' => 'companies.manage']);
        $this->assertDatabaseCount('roles', 3);

        $superAdmin = Role::query()->where('code', 'super-admin')->firstOrFail();
        $administrator = Role::query()->where('code', 'administrator')->firstOrFail();
        $viewer = Role::query()->where('code', 'viewer')->firstOrFail();

        $this->assertCount(18, $superAdmin->permissions);
        $this->assertSame([
            'companies.create',
            'companies.update',
            'companies.view',
            'roles.view',
            'users.create',
            'users.update',
            'users.view',
        ], $administrator->permissions()->orderBy('code')->pluck('code')->all());
        $this->assertSame([
            'companies.view',
            'roles.view',
            'users.view',
        ], $viewer->permissions()->orderBy('code')->pluck('code')->all());
    }
}
