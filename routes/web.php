<?php

use App\Http\Controllers\CompanyController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Modules\AssetAssignmentController;
use App\Http\Controllers\Modules\AssetController;
use App\Http\Controllers\Modules\AuditLogController;
use App\Http\Controllers\Modules\BranchController;
use App\Http\Controllers\Modules\CredentialController;
use App\Http\Controllers\Modules\EmployeeController;
use App\Http\Controllers\Modules\InvoiceController;
use App\Http\Controllers\Modules\MaintenanceController;
use App\Http\Controllers\Modules\PlanController;
use App\Http\Controllers\Modules\TaskController;
use App\Http\Controllers\Modules\TicketController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use Inertia\Inertia;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login');

Route::get('/dashboard', DashboardController::class)->middleware('auth')->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/documentation', fn () => Inertia::render('Documentation/Index'))
        ->name('documentation');

    foreach ([
        'companies' => [CompanyController::class, 'company'],
        'roles' => [RoleController::class, 'role'],
        'users' => [UserController::class, 'user'],
    ] as $module => [$controller, $parameter]) {
        Route::get("/{$module}", [$controller, 'index'])->middleware("can:{$module}.view")->name("{$module}.index");
        Route::post("/{$module}", [$controller, 'store'])->middleware("can:{$module}.create")->name("{$module}.store");
        Route::patch("/{$module}/{{$parameter}}", [$controller, 'update'])->middleware("can:{$module}.update")->name("{$module}.update");
        Route::delete("/{$module}/{{$parameter}}", [$controller, 'destroy'])->middleware("can:{$module}.delete")->name("{$module}.destroy");
        Route::patch("/{$module}/{{$parameter}}/restore", [$controller, 'restore'])->middleware("can:{$module}.restore")->name("{$module}.restore");
        Route::delete("/{$module}/{{$parameter}}/force", [$controller, 'forceDelete'])->middleware("can:{$module}.force-delete")->name("{$module}.force-delete");
    }
    foreach ([
        'plans' => PlanController::class,
        'branches' => BranchController::class,
        'employees' => EmployeeController::class,
        'assets' => AssetController::class,
        'asset-assignments' => AssetAssignmentController::class,
        'maintenances' => MaintenanceController::class,
        'credentials' => CredentialController::class,
        'invoices' => InvoiceController::class,
        'tasks' => TaskController::class,
        'tickets' => TicketController::class,
    ] as $module => $controller) {
        Route::get("/{$module}", [$controller, 'index'])->middleware("can:{$module}.view")->name("{$module}.index");
        Route::post("/{$module}", [$controller, 'store'])->middleware("can:{$module}.create")->name("{$module}.store");
        Route::patch("/{$module}/{record}", [$controller, 'update'])->middleware("can:{$module}.update")->name("{$module}.update");
        Route::delete("/{$module}/{record}", [$controller, 'destroy'])->middleware("can:{$module}.delete")->name("{$module}.destroy");
        Route::patch("/{$module}/{record}/restore", [$controller, 'restore'])->middleware("can:{$module}.restore")->name("{$module}.restore");
        Route::delete("/{$module}/{record}/force", [$controller, 'forceDelete'])->middleware("can:{$module}.force-delete")->name("{$module}.force-delete");
    }

    Route::post('/tickets/{record}/messages', [TicketController::class, 'message'])->middleware('can:tickets.update')->name('tickets.messages.store');
    Route::get('/asset-assignments/{record}/document', [AssetAssignmentController::class, 'document'])->middleware('can:asset-assignments.view')->name('asset-assignments.document');
    Route::get('/audit-logs', [AuditLogController::class, 'index'])->middleware('can:audit-logs.view')->name('audit-logs.index');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
