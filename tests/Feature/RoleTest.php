<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleTest extends TestCase
{
    use RefreshDatabase;

    public function test_users_can_receive_global_and_company_roles_with_permissions(): void
    {
        $user = User::factory()->create();
        $company = Company::factory()->create();
        $globalRole = Role::factory()->create();
        $companyRole = Role::factory()->for($company)->create();
        $permission = Permission::factory()->create();

        $user->roles()->attach([$globalRole->id, $companyRole->id]);
        $companyRole->permissions()->attach($permission);

        $this->assertNull($globalRole->company_id);
        $this->assertTrue($user->roles->contains($companyRole));
        $this->assertTrue($companyRole->permissions->contains($permission));
        $this->assertTrue($company->roles->contains($companyRole));
    }
}
