<?php

use App\Http\Controllers\CompanyController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::resource('companies', CompanyController::class)
        ->only(['index', 'store', 'update'])
        ->middlewareFor('index', 'can:companies.view')
        ->middlewareFor('store', 'can:companies.create')
        ->middlewareFor('update', 'can:companies.update');
    Route::resource('roles', RoleController::class)
        ->only(['index', 'store'])
        ->middlewareFor('index', 'can:roles.view')
        ->middlewareFor('store', 'can:roles.create');
    Route::resource('users', UserController::class)
        ->only(['index', 'store'])
        ->middlewareFor('index', 'can:users.view')
        ->middlewareFor('store', 'can:users.create');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
