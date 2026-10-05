<?php

namespace App\Http\Controllers\Modules;

use App\Models\Asset;
use App\Models\Maintenance;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MaintenanceController extends ModuleController
{
    protected function module(): string
    {
        return 'maintenances';
    }

    protected function modelClass(): string
    {
        return Maintenance::class;
    }

    protected function title(): string
    {
        return 'Mantenimientos';
    }

    protected function singular(): string
    {
        return 'mantenimiento';
    }

    protected function columns(): array
    {
        return [
            ['key' => 'assetName', 'label' => 'Activo'],
            ['key' => 'description', 'label' => 'Descripcion'],
            ['key' => 'performed_at', 'label' => 'Fecha'],
            ['key' => 'cost', 'label' => 'Costo'],
        ];
    }

    protected function fields(): array
    {
        return [
            ['name' => 'asset_id', 'label' => 'Activo', 'type' => 'select', 'options' => 'assets', 'required' => true],
            ['name' => 'description', 'label' => 'Descripcion', 'type' => 'textarea', 'required' => true],
            ['name' => 'performed_at', 'label' => 'Fecha', 'type' => 'date', 'required' => true],
            ['name' => 'cost', 'label' => 'Costo', 'type' => 'number'],
        ];
    }

    protected function relations(): array
    {
        return ['asset:id,name'];
    }

    protected function present(Model $record): array
    {
        return ['assetName' => $record->asset?->name];
    }

    protected function scope(Builder $query, User $user): Builder
    {
        return $query->whereHas('asset', fn (Builder $assets): Builder => $assets
            ->whereIn('assets.company_id', $user->accessibleCompanies()->select('companies.id')));
    }

    protected function options(User $user): array
    {
        return ['assets' => $this->scopedOptions(Asset::class, $user)];
    }

    protected function rules(Request $request, ?Model $record): array
    {
        return [
            'asset_id' => ['required', 'integer', Rule::exists('assets', 'id')->whereNull('deleted_at')->whereIn('company_id', $this->companyIds($request->user()))],
            'description' => ['required', 'string'],
            'performed_at' => ['required', 'date'],
            'cost' => ['nullable', 'numeric', 'min:0'],
        ];
    }
}
