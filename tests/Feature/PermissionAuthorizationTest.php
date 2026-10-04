<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Tests\TestCase;

class PermissionAuthorizationTest extends TestCase
{
    public function test_users_without_the_required_permission_cannot_manage_roles(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/roles')
            ->assertForbidden();
    }

    public function test_users_with_the_required_permission_can_manage_roles(): void
    {
        $user = User::factory()->create();
        $permission = Permission::factory()->create(['code' => 'roles.manage']);
        $role = Role::factory()->create();
        $role->permissions()->attach($permission);
        $user->roles()->attach($role);

        $this->actingAs($user)
            ->get('/roles')
            ->assertOk();
    }
}
