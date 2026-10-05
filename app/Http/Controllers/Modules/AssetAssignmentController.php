<?php

namespace App\Http\Controllers\Modules;

use App\Models\Asset;
use App\Models\AssetAssignment;
use App\Models\AssetAssignmentDocument;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AssetAssignmentController extends ModuleController
{
    protected function module(): string
    {
        return 'asset-assignments';
    }

    protected function modelClass(): string
    {
        return AssetAssignment::class;
    }

    protected function title(): string
    {
        return 'Asignaciones de activos';
    }

    protected function singular(): string
    {
        return 'asignacion';
    }

    protected function columns(): array
    {
        return [
            ['key' => 'assetName', 'label' => 'Activo'],
            ['key' => 'employeeName', 'label' => 'Empleado'],
            ['key' => 'assigned_at', 'label' => 'Asignado'],
            ['key' => 'returned_at', 'label' => 'Devuelto'],
        ];
    }

    protected function fields(): array
    {
        return [
            ['name' => 'asset_id', 'label' => 'Activo', 'type' => 'select', 'options' => 'assets', 'required' => true],
            ['name' => 'employee_id', 'label' => 'Empleado', 'type' => 'select', 'options' => 'employees', 'required' => true],
            ['name' => 'assigned_at', 'label' => 'Fecha de asignacion', 'type' => 'datetime-local', 'required' => true],
            ['name' => 'returned_at', 'label' => 'Fecha de devolucion', 'type' => 'datetime-local'],
        ];
    }

    protected function relations(): array
    {
        return ['asset:id,name', 'employee:id,name'];
    }

    protected function present(Model $record): array
    {
        return ['assetName' => $record->asset?->name, 'employeeName' => $record->employee?->name];
    }

    protected function scope(Builder $query, User $user): Builder
    {
        return $query->whereHas('asset', fn (Builder $assets): Builder => $assets
            ->whereIn('assets.company_id', $user->accessibleCompanies()->select('companies.id')));
    }

    protected function options(User $user): array
    {
        return [
            'assets' => $this->scopedOptions(Asset::class, $user),
            'employees' => $this->scopedOptions(Employee::class, $user),
        ];
    }

    public function document(Request $request, int $record): View
    {
        $assignment = $this->find($request->user(), $record)->load(['asset.company', 'employee']);

        $document = AssetAssignmentDocument::query()->firstOrCreate(
            ['asset_assignment_id' => $assignment->id],
            [
                'document_number' => 'ASG-'.str_pad((string) $assignment->id, 6, '0', STR_PAD_LEFT),
                'generated_at' => now(),
                'generated_by_id' => $request->user()->id,
            ],
        );

        return view('documents.assignment', ['assignment' => $assignment, 'document' => $document]);
    }

    protected function rules(Request $request, ?Model $record): array
    {
        $companyIds = $this->companyIds($request->user());

        return [
            'asset_id' => ['required', 'integer', Rule::exists('assets', 'id')->whereNull('deleted_at')->whereIn('company_id', $companyIds)],
            'employee_id' => [
                'required',
                'integer',
                Rule::exists('employees', 'id')->whereNull('deleted_at')->whereIn('company_id', $companyIds),
                function (string $attribute, mixed $value, callable $fail) use ($request): void {
                    if (Employee::query()->find($value)?->company_id !== Asset::query()->find($request->input('asset_id'))?->company_id) {
                        $fail('El empleado no pertenece a la misma empresa del activo.');
                    }
                },
            ],
            'assigned_at' => ['required', 'date'],
            'returned_at' => ['nullable', 'date', 'after_or_equal:assigned_at'],
        ];
    }
}
