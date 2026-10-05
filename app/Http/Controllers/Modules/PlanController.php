<?php

namespace App\Http\Controllers\Modules;

use App\Models\Plan;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class PlanController extends ModuleController
{
    protected function module(): string
    {
        return 'plans';
    }

    protected function modelClass(): string
    {
        return Plan::class;
    }

    protected function title(): string
    {
        return 'Planes';
    }

    protected function singular(): string
    {
        return 'plan';
    }

    protected function group(): string
    {
        return 'Administracion';
    }

    protected function columns(): array
    {
        return [
            ['key' => 'name', 'label' => 'Nombre'],
            ['key' => 'price', 'label' => 'Precio'],
            ['key' => 'max_users', 'label' => 'Max. usuarios'],
            ['key' => 'max_assets', 'label' => 'Max. activos'],
        ];
    }

    protected function fields(): array
    {
        return [
            ['name' => 'name', 'label' => 'Nombre', 'type' => 'text', 'required' => true],
            ['name' => 'price', 'label' => 'Precio', 'type' => 'number', 'required' => true],
            ['name' => 'max_users', 'label' => 'Maximo de usuarios', 'type' => 'number', 'required' => true],
            ['name' => 'max_assets', 'label' => 'Maximo de activos', 'type' => 'number', 'required' => true],
        ];
    }

    protected function scope(Builder $query, User $user): Builder
    {
        return $query;
    }

    protected function rules(Request $request, ?Model $record): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
            'max_users' => ['required', 'integer', 'min:1'],
            'max_assets' => ['required', 'integer', 'min:1'],
        ];
    }
}
