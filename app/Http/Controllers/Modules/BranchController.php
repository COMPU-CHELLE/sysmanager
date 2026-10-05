<?php

namespace App\Http\Controllers\Modules;

use App\Models\Branch;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BranchController extends ModuleController
{
    protected function module(): string
    {
        return 'branches';
    }

    protected function modelClass(): string
    {
        return Branch::class;
    }

    protected function title(): string
    {
        return 'Sucursales';
    }

    protected function singular(): string
    {
        return 'sucursal';
    }

    protected function columns(): array
    {
        return [
            ['key' => 'code', 'label' => 'Codigo'],
            ['key' => 'name', 'label' => 'Nombre'],
            ['key' => 'companyName', 'label' => 'Empresa'],
            ['key' => 'address', 'label' => 'Direccion'],
            ['key' => 'email', 'label' => 'Correo'],
        ];
    }

    protected function fields(): array
    {
        return [
            ['name' => 'company_id', 'label' => 'Empresa', 'type' => 'select', 'options' => 'companies', 'required' => true],
            ['name' => 'code', 'label' => 'Codigo', 'type' => 'text', 'required' => true],
            ['name' => 'name', 'label' => 'Nombre', 'type' => 'text', 'required' => true],
            ['name' => 'address', 'label' => 'Direccion', 'type' => 'text'],
            ['name' => 'email', 'label' => 'Correo', 'type' => 'email'],
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
            'code' => ['required', 'string', 'max:50', Rule::unique('branches', 'code')
                ->where('company_id', $request->input('company_id'))
                ->ignore($record?->getKey())],
            'name' => ['required', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
        ];
    }
}
