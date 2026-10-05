<?php

namespace App\Http\Controllers\Modules;

use App\Models\Credential;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CredentialController extends ModuleController
{
    protected function module(): string
    {
        return 'credentials';
    }

    protected function modelClass(): string
    {
        return Credential::class;
    }

    protected function title(): string
    {
        return 'Credenciales';
    }

    protected function singular(): string
    {
        return 'credencial';
    }

    protected function columns(): array
    {
        return [
            ['key' => 'name', 'label' => 'Nombre'],
            ['key' => 'type', 'label' => 'Tipo'],
            ['key' => 'url', 'label' => 'URL'],
            ['key' => 'companyName', 'label' => 'Empresa'],
            ['key' => 'access_mode', 'label' => 'Acceso'],
        ];
    }

    protected function fields(): array
    {
        return [
            ['name' => 'company_id', 'label' => 'Empresa', 'type' => 'select', 'options' => 'companies', 'required' => true],
            ['name' => 'name', 'label' => 'Nombre', 'type' => 'text', 'required' => true],
            ['name' => 'type', 'label' => 'Tipo', 'type' => 'text', 'required' => true],
            ['name' => 'url', 'label' => 'URL', 'type' => 'text'],
            ['name' => 'username', 'label' => 'Usuario', 'type' => 'text', 'required' => true],
            ['name' => 'password', 'label' => 'Contrasena', 'type' => 'password', 'secret' => true, 'required' => true],
            ['name' => 'access_mode', 'label' => 'Modo de acceso', 'type' => 'select', 'required' => true, 'choices' => [
                ['value' => 'copy_open', 'label' => 'Copiar clave y abrir URL'],
                ['value' => 'basic_auth', 'label' => 'Autenticacion basica en URL'],
                ['value' => 'local_only', 'label' => 'Solo local'],
            ]],
            ['name' => 'notes', 'label' => 'Notas', 'type' => 'textarea'],
        ];
    }

    protected function relations(): array
    {
        return ['company:id,name'];
    }

    protected function present(Model $record): array
    {
        return ['companyName' => $record->company?->name];
    }

    protected function options(User $user): array
    {
        return ['companies' => $this->companyOptions($user)];
    }

    protected function rules(Request $request, ?Model $record): array
    {
        return [
            'company_id' => $this->companyRule($request->user()),
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', 'max:255'],
            'url' => ['nullable', 'string', 'max:2048'],
            'username' => ['required', 'string'],
            'password' => [$record ? 'nullable' : 'required', 'string'],
            'access_mode' => ['required', Rule::in(['copy_open', 'basic_auth', 'local_only'])],
            'notes' => ['nullable', 'string'],
        ];
    }
}
