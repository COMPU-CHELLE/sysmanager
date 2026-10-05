<?php

namespace App\Http\Controllers\Modules;

use App\Models\Branch;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class InvoiceController extends ModuleController
{
    protected function module(): string
    {
        return 'invoices';
    }

    protected function modelClass(): string
    {
        return Invoice::class;
    }

    protected function title(): string
    {
        return 'Facturas';
    }

    protected function singular(): string
    {
        return 'factura';
    }

    protected function columns(): array
    {
        return [
            ['key' => 'number', 'label' => 'Numero'],
            ['key' => 'provider', 'label' => 'Proveedor'],
            ['key' => 'date', 'label' => 'Fecha'],
            ['key' => 'category', 'label' => 'Categoria'],
            ['key' => 'companyName', 'label' => 'Empresa'],
            ['key' => 'branchName', 'label' => 'Sucursal'],
            ['key' => 'total', 'label' => 'Total'],
        ];
    }

    protected function fields(): array
    {
        return [
            ['name' => 'company_id', 'label' => 'Empresa', 'type' => 'select', 'options' => 'companies', 'required' => true],
            ['name' => 'branch_id', 'label' => 'Sucursal', 'type' => 'select', 'options' => 'branches', 'filterBy' => 'company_id', 'required' => true],
            ['name' => 'number', 'label' => 'Numero', 'type' => 'text', 'required' => true],
            ['name' => 'provider', 'label' => 'Proveedor', 'type' => 'text', 'required' => true],
            ['name' => 'date', 'label' => 'Fecha', 'type' => 'date', 'required' => true],
            ['name' => 'category', 'label' => 'Categoria', 'type' => 'text', 'required' => true],
            ['name' => 'items', 'label' => 'Items', 'type' => 'items'],
        ];
    }

    protected function relations(): array
    {
        return ['company:id,name', 'branch:id,name', 'items'];
    }

    protected function present(Model $record): array
    {
        return [
            'companyName' => $record->company?->name,
            'branchName' => $record->branch?->name,
            'total' => number_format((float) $record->items->sum('total'), 2, '.', ''),
            'items' => $record->items->map(fn (InvoiceItem $item): array => [
                'description' => $item->description,
                'quantity' => $item->quantity,
                'unit_price' => $item->unit_price,
                'tax' => $item->tax,
            ])->all(),
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
            'branch_id' => $this->sameCompanyRule('branches', $request),
            'number' => ['required', 'string', 'max:100'],
            'provider' => ['required', 'string', 'max:255'],
            'date' => ['required', 'date'],
            'category' => ['required', 'string', 'max:255'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.description' => ['required', 'string', 'max:255'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
            'items.*.tax' => ['nullable', 'numeric', 'min:0'],
        ];
    }

    protected function persist(?Model $record, array $data): Model
    {
        return DB::transaction(function () use ($record, $data): Model {
            $items = Arr::pull($data, 'items');
            $invoice = parent::persist($record, $data);

            $invoice->items()->delete();

            foreach ($items as $item) {
                $invoice->items()->create([
                    ...$item,
                    'tax' => $item['tax'] ?? null,
                    'total' => $item['quantity'] * $item['unit_price'] + ($item['tax'] ?? 0),
                ]);
            }

            return $invoice;
        });
    }
}
