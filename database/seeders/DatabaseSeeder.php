<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    private const array OperationalModules = [
        'branches', 'employees', 'assets', 'asset-assignments', 'maintenances', 'credentials', 'invoices', 'tasks', 'tickets',
    ];

    private const array UserActions = ['view', 'create', 'update'];

    public function run(): void
    {
        $permissions = $this->seedPermissions();

        $this->seedRoles($permissions);
        $this->seedDemoCompany();
        $this->seedSupportUser();
    }

    /**
     * Modulos (Permission::Modules) x acciones (Permission::Actions).
     *
     * @return Collection<int, Permission>
     */
    private function seedPermissions(): Collection
    {
        return collect(Permission::Modules)
            ->flatMap(fn (string $moduleName, string $moduleCode): Collection => collect(Permission::Actions)
                ->map(fn (string $actionName, string $actionCode): Permission => Permission::query()->updateOrCreate(
                    ['code' => "{$moduleCode}.{$actionCode}"],
                    ['name' => "{$actionName} {$moduleName}"],
                ))
                ->values())
            ->values();
    }

    /**
     * @param  Collection<int, Permission>  $permissions
     */
    private function seedRoles(Collection $permissions): void
    {
        $userPermissions = $permissions->filter(function (Permission $permission): bool {
            [$module, $action] = explode('.', $permission->code);

            return in_array($module, self::OperationalModules, true) && in_array($action, self::UserActions, true);
        });

        $roles = [
            Role::SupportCode => ['Soporte', $permissions],
            Role::AdministratorCode => ['Administrador', $permissions],
            'user' => ['Usuario', $userPermissions],
        ];

        foreach ($roles as $code => [$name, $rolePermissions]) {
            Role::query()
                ->updateOrCreate(['code' => $code, 'company_id' => null], ['name' => $name])
                ->permissions()
                ->sync($rolePermissions->pluck('id'));
        }
    }

    /**
     * Usuario soporte global. Credenciales por SUPPORT_NAME, SUPPORT_CODE,
     * SUPPORT_EMAIL y SUPPORT_PASSWORD; sin contrasena se genera una aleatoria.
     */
    private function seedSupportUser(): void
    {
        $user = User::query()->firstOrNew(['code' => env('SUPPORT_CODE', 'SOPORTE')]);

        if (! $user->exists) {
            $password = env('SUPPORT_PASSWORD') ?: Str::password(16, symbols: false);

            $user->fill([
                'name' => env('SUPPORT_NAME', 'Soporte'),
                'email' => env('SUPPORT_EMAIL', 'soporte@sysmanager.test'),
                'password' => $password,
            ])->save();

            if (! env('SUPPORT_PASSWORD')) {
                $this->command?->warn("Usuario soporte: {$user->code} / contrasena generada: {$password}");
            }
        }

        $user->roles()->syncWithoutDetaching([Role::query()->where('code', Role::SupportCode)->firstOrFail()->id]);
    }

    private function seedDemoCompany(): void
    {
        Company::query()->updateOrCreate(
            ['code' => 'DEMO'],
            ['name' => 'Empresa Demo', 'is_active' => true],
        );
    }
}
