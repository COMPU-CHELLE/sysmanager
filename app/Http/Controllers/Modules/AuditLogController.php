<?php

namespace App\Http\Controllers\Modules;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AuditLogController extends Controller
{
    public function index(Request $request): Response
    {
        $user = $request->user();

        $rows = AuditLog::query()
            ->when(! $user->isSupport(), fn (Builder $query): Builder => $query
                ->whereIn('company_id', $user->accessibleCompanies()->select('companies.id')))
            ->with(['user:id,name', 'company:id,name'])
            ->latest('id')
            ->limit(500)
            ->get()
            ->map(fn (AuditLog $log): array => [
                'id' => $log->id,
                'createdAt' => $log->created_at?->format('Y-m-d H:i:s'),
                'userName' => $log->user?->name,
                'companyName' => $log->company?->name,
                'action' => $log->action,
                'entityId' => $log->entity_id,
                'method' => $log->method,
                'ip' => $log->ip,
                'statusCode' => $log->status_code,
            ]);

        return Inertia::render('Modules/Index', [
            'module' => [
                'key' => 'audit-logs',
                'title' => 'Auditoria',
                'singular' => 'registro',
                'group' => 'Administracion',
                'readOnly' => true,
                'columns' => [
                    ['key' => 'createdAt', 'label' => 'Fecha'],
                    ['key' => 'userName', 'label' => 'Usuario'],
                    ['key' => 'companyName', 'label' => 'Empresa'],
                    ['key' => 'action', 'label' => 'Accion'],
                    ['key' => 'entityId', 'label' => 'ID'],
                    ['key' => 'method', 'label' => 'Metodo'],
                    ['key' => 'statusCode', 'label' => 'Estado'],
                    ['key' => 'ip', 'label' => 'IP'],
                ],
                'fields' => [],
            ],
            'rows' => $rows,
            'options' => [],
        ]);
    }
}
