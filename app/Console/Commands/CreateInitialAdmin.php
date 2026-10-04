<?php

namespace App\Console\Commands;

use App\Models\Role;
use App\Models\User;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('sysmanager:make-admin {name} {code} {email}')]
#[Description('Crea o actualiza el administrador inicial de SysManager')]
class CreateInitialAdmin extends Command
{
    public function handle(): int
    {
        $name = (string) $this->argument('name');
        $code = (string) $this->argument('code');
        $email = (string) $this->argument('email');
        $password = $this->secret('Contraseña');

        if (! is_string($password) || mb_strlen($password) < 8) {
            $this->components->error('La contraseña debe tener al menos 8 caracteres.');

            return self::FAILURE;
        }

        if (User::query()->where('code', $code)->where('email', '!=', $email)->exists()) {
            $this->components->error('El código indicado ya pertenece a otro usuario.');

            return self::FAILURE;
        }

        $superAdmin = Role::query()
            ->where('code', 'super-admin')
            ->whereNull('company_id')
            ->first();

        if ($superAdmin === null) {
            $this->components->error('No existe el rol SuperAdmin. Ejecute primero php artisan db:seed.');

            return self::FAILURE;
        }

        $user = User::query()->firstOrNew(['email' => $email]);
        $user->fill([
            'name' => $name,
            'code' => $code,
            'password' => $password,
        ]);
        $user->save();
        $user->roles()->syncWithoutDetaching([$superAdmin->id]);

        $this->components->info("Administrador {$user->email} configurado correctamente.");

        return self::SUCCESS;
    }
}
