<?php

namespace App\Http\Controllers\Modules;

use App\Models\Branch;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class EmployeeController extends ModuleController
{
    protected function module(): string
    {
        return 'employees';
    }

    protected function modelClass(): string
    {
        return Employee::class;
    }

    protected function title(): string
    {
        return 'Empleados';
    }

    protected function singular(): string
    {
        return 'empleado';
    }

    protected function columns(): array
    {
        return [
            ['key' => 'name', 'label' => 'Nombre'],
            ['key' => 'position', 'label' => 'Cargo'],
            ['key' => 'companyName', 'label' => 'Empresa'],
            ['key' => 'branchName', 'label' => 'Sucursal'],
            ['key' => 'email', 'label' => 'Correo'],
            ['key' => 'phone', 'label' => 'Telefono'],
        ];
    }

    protected function fields(): array
    {
        return [
            ['name' => 'company_id', 'label' => 'Empresa', 'type' => 'select', 'options' => 'companies', 'required' => true],
            ['name' => 'branch_id', 'label' => 'Sucursal', 'type' => 'select', 'options' => 'branches', 'filterBy' => 'company_id'],
            ['name' => 'name', 'label' => 'Nombre', 'type' => 'text', 'required' => true],
            ['name' => 'position', 'label' => 'Cargo', 'type' => 'text'],
            ['name' => 'email', 'label' => 'Correo', 'type' => 'email'],
            ['name' => 'phone', 'label' => 'Telefono', 'type' => 'text'],
        ];
    }

    protected function relations(): array
    {
        return ['company:id,name', 'branch:id,name'];
    }

    protected function present(Model $record): array
    {
        return ['companyName' => $record->company?->name, 'branchName' => $record->branch?->name];
    }

    protected function options(User $user): array
    {
        return [
            'companies' => $this->companyOptions($user),
            'branches' => $this->scopedOptions(Branch::class, $user),
        ];
    }

    protected function rules(Request $request, ?Model $record): array
    {
        return [
            'company_id' => $this->companyRule($request->user()),
            'branch_id' => $this->sameCompanyRule('branches', $request, true),
            'name' => ['required', 'string', 'max:255'],
            'position' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
        ];
    }
}
