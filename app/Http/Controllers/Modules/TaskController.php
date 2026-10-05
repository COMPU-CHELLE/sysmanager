<?php

namespace App\Http\Controllers\Modules;

use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TaskController extends ModuleController
{
    private const array Statuses = [
        ['value' => 'OPEN', 'label' => 'Abierta'],
        ['value' => 'IN_PROGRESS', 'label' => 'En progreso'],
        ['value' => 'DONE', 'label' => 'Terminada'],
        ['value' => 'CANCELED', 'label' => 'Cancelada'],
    ];

    private const array Priorities = [
        ['value' => 'LOW', 'label' => 'Baja'],
        ['value' => 'MEDIUM', 'label' => 'Media'],
        ['value' => 'HIGH', 'label' => 'Alta'],
    ];

    protected function module(): string
    {
        return 'tasks';
    }

    protected function modelClass(): string
    {
        return Task::class;
    }

    protected function title(): string
    {
        return 'Tareas';
    }

    protected function singular(): string
    {
        return 'tarea';
    }

    protected function columns(): array
    {
        return [
            ['key' => 'title', 'label' => 'Titulo'],
            ['key' => 'status', 'label' => 'Estado'],
            ['key' => 'priority', 'label' => 'Prioridad'],
            ['key' => 'assignedToName', 'label' => 'Asignada a'],
            ['key' => 'companyName', 'label' => 'Empresa'],
            ['key' => 'due_date', 'label' => 'Vence'],
        ];
    }

    protected function fields(): array
    {
        return [
            ['name' => 'company_id', 'label' => 'Empresa', 'type' => 'select', 'options' => 'companies', 'required' => true],
            ['name' => 'title', 'label' => 'Titulo', 'type' => 'text', 'required' => true],
            ['name' => 'description', 'label' => 'Descripcion', 'type' => 'textarea'],
            ['name' => 'status', 'label' => 'Estado', 'type' => 'select', 'choices' => self::Statuses, 'required' => true],
            ['name' => 'priority', 'label' => 'Prioridad', 'type' => 'select', 'choices' => self::Priorities],
            ['name' => 'assigned_to_id', 'label' => 'Asignada a', 'type' => 'select', 'options' => 'users'],
            ['name' => 'due_date', 'label' => 'Fecha limite', 'type' => 'datetime-local'],
            ['name' => 'solution', 'label' => 'Solucion', 'type' => 'textarea'],
        ];
    }

    protected function relations(): array
    {
        return ['company:id,name', 'assignedTo:id,name'];
    }

    protected function present(Model $record): array
    {
        return ['companyName' => $record->company?->name, 'assignedToName' => $record->assignedTo?->name];
    }

    protected function options(User $user): array
    {
        return [
            'companies' => $this->companyOptions($user),
            'users' => $this->userOptions($user),
        ];
    }

    protected function rules(Request $request, ?Model $record): array
    {
        $userIds = array_column($this->userOptions($request->user()), 'value');

        return [
            'company_id' => $this->companyRule($request->user()),
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['required', Rule::in(array_column(self::Statuses, 'value'))],
            'priority' => ['nullable', Rule::in(array_column(self::Priorities, 'value'))],
            'assigned_to_id' => ['nullable', 'integer', Rule::in($userIds)],
            'due_date' => ['nullable', 'date'],
            'solution' => ['nullable', 'string'],
        ];
    }

    protected function prepare(array $data, User $user, ?Model $record): array
    {
        if ($record === null) {
            $data['created_by_id'] = $user->id;
        }

        $data['closed_at'] = in_array($data['status'], ['DONE', 'CANCELED'], true)
            ? ($record?->closed_at ?? now())
            : null;

        return $data;
    }
}
