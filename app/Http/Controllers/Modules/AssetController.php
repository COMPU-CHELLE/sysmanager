<?php

namespace App\Http\Controllers\Modules;

use App\Models\Asset;
use App\Models\Branch;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class AssetController extends ModuleController
{
    private const array Details = [
        'ram_type' => 'RAM tipo', 'ram_capacity' => 'RAM capacidad',
        'hdd_type' => 'Disco tipo', 'hdd_capacity' => 'Disco capacidad',
        'pro_type' => 'Procesador', 'pro_detail' => 'Procesador detalle',
        'mbr_type' => 'Placa madre', 'mbr_detail' => 'Placa madre detalle',
        'gra_type' => 'Grafica', 'gra_detail' => 'Grafica detalle',
        'monitor' => 'Monitor', 'mon_detail' => 'Monitor detalle',
        'keyboard' => 'Teclado', 'key_detail' => 'Teclado detalle',
        'mouse' => 'Mouse', 'mou_detail' => 'Mouse detalle',
    ];

    protected function module(): string
    {
        return 'assets';
    }

    protected function modelClass(): string
    {
        return Asset::class;
    }

    protected function title(): string
    {
        return 'Activos';
    }

    protected function singular(): string
    {
        return 'activo';
    }

    protected function columns(): array
    {
        return [
            ['key' => 'name', 'label' => 'Nombre'],
            ['key' => 'type', 'label' => 'Tipo'],
            ['key' => 'serial', 'label' => 'Serial'],
            ['key' => 'brand', 'label' => 'Marca'],
            ['key' => 'companyName', 'label' => 'Empresa'],
            ['key' => 'branchName', 'label' => 'Sucursal'],
        ];
    }

    protected function fields(): array
    {
        return [
            ['name' => 'company_id', 'label' => 'Empresa', 'type' => 'select', 'options' => 'companies', 'required' => true],
            ['name' => 'branch_id', 'label' => 'Sucursal', 'type' => 'select', 'options' => 'branches', 'filterBy' => 'company_id'],
            ['name' => 'name', 'label' => 'Nombre', 'type' => 'text', 'required' => true],
            ['name' => 'type', 'label' => 'Tipo', 'type' => 'text', 'required' => true],
            ['name' => 'serial', 'label' => 'Serial', 'type' => 'text'],
            ['name' => 'brand', 'label' => 'Marca', 'type' => 'text'],
            ['name' => 'model', 'label' => 'Modelo', 'type' => 'text'],
            ['name' => 'purchase_date', 'label' => 'Fecha de compra', 'type' => 'date'],
            ['name' => 'cost', 'label' => 'Costo', 'type' => 'number'],
            ...array_map(
                fn (string $name, string $label): array => ['name' => $name, 'label' => $label, 'type' => 'text'],
                array_keys(self::Details),
                array_values(self::Details),
            ),
        ];
    }

    protected function relations(): array
    {
        return ['company:id,name', 'branch:id,name', 'detail'];
    }

    protected function present(Model $record): array
    {
        return [
            'companyName' => $record->company?->name,
            'branchName' => $record->branch?->name,
            ...collect(array_keys(self::Details))->mapWithKeys(fn (string $key): array => [$key => $record->detail?->{$key}])->all(),
        ];
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
            'type' => ['required', 'string', 'max:255'],
            'serial' => ['nullable', 'string', 'max:255'],
            'brand' => ['nullable', 'string', 'max:255'],
            'model' => ['nullable', 'string', 'max:255'],
            'purchase_date' => ['nullable', 'date'],
            'cost' => ['nullable', 'numeric', 'min:0'],
            ...array_map(fn (): array => ['nullable', 'string', 'max:255'], self::Details),
        ];
    }

    protected function persist(?Model $record, array $data): Model
    {
        $details = array_intersect_key($data, self::Details);
        $asset = parent::persist($record, array_diff_key($data, self::Details));
        $asset->detail()->updateOrCreate([], $details);

        return $asset;
    }
}
