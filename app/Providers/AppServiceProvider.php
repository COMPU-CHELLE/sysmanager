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

        foreach (['companies', 'users', 'roles'] as $module) {
            foreach (['view', 'create', 'update', 'delete', 'restore', 'force-delete'] as $action) {
                $permission = "{$module}.{$action}";

                Gate::define($permission, fn (User $user): bool => $user->hasPermission($permission));
            }
        }
    }
}
