<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PermissionAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_users_without_the_view_permission_cannot_see_roles(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/roles')
            ->assertForbidden();
    }

    public function test_users_with_the_view_permission_can_see_roles(): void
    {
        $user = User::factory()->create();
        $permission = Permission::factory()->create(['code' => 'roles.view']);
        $role = Role::factory()->create();
        $role->permissions()->attach($permission);
        $user->roles()->attach($role);

        $this->actingAs($user)
            ->get('/roles')
            ->assertOk();
    }
}
