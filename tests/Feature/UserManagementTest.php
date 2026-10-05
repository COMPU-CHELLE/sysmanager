<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_users_can_create_users_with_companies_and_roles(): void
    {
        $this->withoutMiddleware(PreventRequestForgery::class);

        $administrator = User::factory()->create();
        $company = Company::factory()->create();
        $role = Role::factory()->create();
        $permission = Permission::factory()->create(['code' => 'users.create']);
        $role->permissions()->attach($permission);
        $administrator->roles()->attach($role);

        $response = $this->actingAs($administrator)->post('/users', [
            'name' => 'Maria Lopez',
            'code' => 'maria.lopez',
            'email' => 'maria@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'company_ids' => [$company->id],
            'role_ids' => [$role->id],
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('users.index', absolute: false));

        $user = User::query()->where('email', 'maria@example.com')->firstOrFail();

        $this->assertTrue($user->companies->contains($company));
        $this->assertTrue($user->roles->contains($role));
    }
}
