<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class AccessControlSeeder extends Seeder
{
    private const array Modules = [
        'companies' => 'Empresas',
        'users' => 'Usuarios',
        'roles' => 'Roles',
    ];

    private const array Actions = [
        'view' => 'Ver',
        'create' => 'Crear',
        'update' => 'Actualizar',
        'delete' => 'Eliminar',
        'restore' => 'Restaurar',
        'force-delete' => 'Eliminar definitivamente',
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = collect(self::Modules)
            ->flatMap(fn (string $moduleName, string $moduleCode) => collect(self::Actions)
                ->map(fn (string $actionName, string $actionCode): array => [
                    'name' => "{$actionName} {$moduleName}",
                    'code' => "{$moduleCode}.{$actionCode}",
                ])
                ->values())
            ->map(fn (array $permission): Permission => Permission::query()->updateOrCreate(
                ['code' => $permission['code']],
                ['name' => $permission['name']],
            ));

        $roles = [
            'super-admin' => [
                'name' => 'SuperAdmin',
                'permission_codes' => $permissions->pluck('code'),
            ],
            'administrator' => [
                'name' => 'Administrador',
                'permission_codes' => collect([
                    'companies.view',
                    'companies.create',
                    'companies.update',
                    'users.view',
                    'users.create',
                    'users.update',
                    'roles.view',
                ]),
            ],
            'viewer' => [
                'name' => 'Consulta',
                'permission_codes' => collect(self::Modules)
                    ->keys()
                    ->map(fn (string $moduleCode): string => "{$moduleCode}.view"),
            ],
        ];

        foreach ($roles as $roleCode => $roleDefinition) {
            $role = Role::query()->updateOrCreate(
                ['code' => $roleCode, 'company_id' => null],
                ['name' => $roleDefinition['name']],
            );

            $role->permissions()->sync(
                $permissions
                    ->whereIn('code', $roleDefinition['permission_codes'])
                    ->pluck('id'),
            );
        }

        Permission::query()
            ->whereIn('code', ['companies.manage', 'users.manage', 'roles.manage'])
            ->delete();
    }
}
