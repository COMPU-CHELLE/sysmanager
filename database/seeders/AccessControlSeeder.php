<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class AccessControlSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = collect([
            ['name' => 'Administrar empresas', 'code' => 'companies.manage'],
            ['name' => 'Administrar usuarios', 'code' => 'users.manage'],
            ['name' => 'Administrar roles',    'code' => 'roles.manage'],
        ])->map(fn (array $permission): Permission => Permission::query()->updateOrCreate(
            ['code' => $permission['code']],
            ['name' => $permission['name']],
        ));

        $superAdmin = Role::query()->updateOrCreate(
            ['code' => 'super-admin'],
            ['name' => 'SuperAdmin', 'company_id' => null],
        );

        $superAdmin->permissions()->sync($permissions->pluck('id'));
    }
}
