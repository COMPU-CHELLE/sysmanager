<?php

namespace App\Models;

use Database\Factories\PermissionFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[Fillable(['name', 'code'])]
class Permission extends Model
{
    public const array Modules = [
        'companies' => 'Empresas',
        'users' => 'Usuarios',
        'roles' => 'Roles',
        'plans' => 'Planes',
        'branches' => 'Sucursales',
        'employees' => 'Empleados',
        'assets' => 'Activos',
        'asset-assignments' => 'Asignaciones de activos',
        'maintenances' => 'Mantenimientos',
        'credentials' => 'Credenciales',
        'invoices' => 'Facturas',
        'tasks' => 'Tareas',
        'tickets' => 'Tickets',
        'audit-logs' => 'Auditoria',
    ];

    public const array Actions = [
        'view' => 'Ver',
        'create' => 'Crear',
        'update' => 'Actualizar',
        'delete' => 'Eliminar',
        'restore' => 'Restaurar',
        'force-delete' => 'Eliminar definitivamente',
    ];

    /** @use HasFactory<PermissionFactory> */
    use HasFactory;

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class)->withTimestamps();
    }
}
