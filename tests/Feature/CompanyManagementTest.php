<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CompanyManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_users_can_create_and_update_a_company(): void
    {
        $this->withoutMiddleware(PreventRequestForgery::class);

        $user = User::factory()->create();
        $permissions = Permission::factory()->createMany([
            ['code' => 'companies.create'],
            ['code' => 'companies.update'],
        ]);
        $role = Role::factory()->create();
        $role->permissions()->attach($permissions);
        $user->roles()->attach($role);

        $response = $this->actingAs($user)->post('/companies', [
            'name' => 'Chellnova',
            'code' => 'CHELLNOVA',
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('companies.index', absolute: false));

        $company = Company::query()->firstOrFail();

        $this->assertDatabaseHas('companies', [
            'id' => $company->id,
            'name' => 'Chellnova',
            'code' => 'CHELLNOVA',
            'is_active' => true,
        ]);

        $this->actingAs($user)
            ->patch(route('companies.update', $company), ['is_active' => false])
            ->assertRedirect(route('companies.index', absolute: false));

        $this->assertDatabaseHas('companies', [
            'id' => $company->id,
            'is_active' => false,
        ]);
    }
}
