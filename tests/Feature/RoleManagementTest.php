<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_users_can_create_company_roles_with_permissions(): void
    {
        $this->withoutMiddleware(PreventRequestForgery::class);

        $administrator = User::factory()->create();
        $company = Company::factory()->create();
        $permission = Permission::factory()->create();
        $role = Role::factory()->create();
        $role->permissions()->attach(Permission::query()->firstOrCreate(
            ['code' => 'roles.manage'],
            ['name' => 'Administrar roles'],
        ));
        $administrator->roles()->attach($role);

        $response = $this->actingAs($administrator)->post('/roles', [
            'name' => 'Administrador',
            'code' => 'ADMINISTRADOR-CHELLNOVA',
            'company_id' => $company->id,
            'permission_ids' => [$permission->id],
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('roles.index', absolute: false));

        $this->assertDatabaseHas('roles', [
            'name' => 'Administrador',
            'code' => 'ADMINISTRADOR-CHELLNOVA',
            'company_id' => $company->id,
        ]);
        $this->assertDatabaseHas('permission_role', [
            'permission_id' => $permission->id,
        ]);
    }
}
