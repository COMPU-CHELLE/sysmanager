<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\AuditLog;
use App\Models\Branch;
use App\Models\Employee;
use App\Models\InvoiceItem;
use App\Models\Task;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $user = $request->user();
        $companies = $user->accessibleCompanies()->orderBy('name')->get(['companies.id', 'companies.name']);
        $isOperator = ! $user->isSupport() && ! $user->hasPermission('users.view');

        $selected = $request->integer('company_id') ?: null;
        $companyIds = $selected !== null && $companies->contains('id', $selected)
            ? [$selected]
            : $companies->pluck('id')->all();

        $stats = [
            'branches' => Branch::query()->whereIn('company_id', $companyIds)->count(),
            'employees' => Employee::query()->whereIn('company_id', $companyIds)->count(),
            'assets' => Asset::query()->whereIn('company_id', $companyIds)->count(),
            'users' => User::query()->whereHas('companies', fn ($query) => $query->whereIn('companies.id', $companyIds))->count(),
            'openTasks' => Task::query()->whereIn('company_id', $companyIds)->whereIn('status', ['OPEN', 'IN_PROGRESS'])->count(),
            'openTickets' => Ticket::query()->whereIn('company_id', $companyIds)->where('status', '!=', 'CLOSED')->count(),
            'invoiceTotal' => number_format((float) InvoiceItem::query()
                ->whereHas('invoice', fn ($query) => $query->whereIn('company_id', $companyIds))
                ->sum('total'), 2, '.', ''),
        ];

        $myTasks = Task::query()
            ->where('assigned_to_id', $user->id)
            ->whereIn('status', ['OPEN', 'IN_PROGRESS'])
            ->latest('id')->limit(8)->get(['id', 'title', 'status', 'priority', 'due_date'])
            ->map(fn (Task $task): array => [
                'id' => $task->id,
                'title' => $task->title,
                'status' => $task->status,
                'priority' => $task->priority,
                'dueDate' => $task->due_date?->format('Y-m-d'),
            ]);

        $activity = AuditLog::query()
            ->when($isOperator || $request->boolean('mine'), fn ($query) => $query->where('user_id', $user->id))
            ->when(! $isOperator && ! $user->isSupport(), fn ($query) => $query->whereIn('company_id', $companyIds))
            ->when($user->isSupport() && $selected !== null, fn ($query) => $query->where('company_id', $selected))
            ->with('user:id,name')
            ->latest('id')->limit(10)->get()
            ->map(fn (AuditLog $log): array => [
                'id' => $log->id,
                'action' => $log->action,
                'userName' => $log->user?->name,
                'createdAt' => $log->created_at?->format('Y-m-d H:i'),
            ]);

        return Inertia::render('Dashboard', [
            'view' => $user->isSupport() ? 'support' : ($isOperator ? 'user' : 'admin'),
            'companies' => $companies,
            'selectedCompany' => $selected !== null && $companies->contains('id', $selected) ? $selected : null,
            'stats' => $stats,
            'myTasks' => $myTasks,
            'activity' => $activity,
        ]);
    }
}
