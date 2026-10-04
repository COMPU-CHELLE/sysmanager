<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Vite::prefetch(concurrency: 3);

        Gate::define('companies.manage', fn (User $user): bool => $user->hasPermission('companies.manage'));
        Gate::define('users.manage', fn (User $user): bool => $user->hasPermission('users.manage'));
        Gate::define('roles.manage', fn (User $user): bool => $user->hasPermission('roles.manage'));
    }
}
