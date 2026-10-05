<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        $actor = $request->user();
        $manageableIds = $actor->manageableRoles()->pluck('roles.id')->all();
        $totalPermissions = Permission::query()->count();

        return Inertia::render('Roles/Index', [
            'roles' => $actor->assignableRoles()
                ->when($actor->canRestore('roles'), fn (Builder $query): Builder => $query->withTrashed())
                ->with(['company:id,name', 'permissions:id,name,code'])
                ->orderBy('name')
                ->get()
                ->map(fn (Role $role): array => [
                    'id' => $role->id,
                    'name' => $role->name,
                    'code' => $role->code,
                    'company' => $role->company?->name,
                    'companyId' => $role->company_id,
                    'locked' => ! in_array($role->id, $manageableIds, true),
                    'totalPermissions' => $totalPermissions,
                    'deletedAt' => $role->deleted_at?->toDateTimeString(),
                    'permissions' => $role->permissions->map(fn (Permission $permission): array => [
                        'id' => $permission->id,
                        'name' => $permission->name,
                    ]),
                ]),
            'companies' => $actor->accessibleCompanies()
                ->where('is_active', true)
                ->orderBy('name')
                ->get(['id', 'name']),
            'permissions' => $actor->grantablePermissions()
                ->orderBy('name')
                ->get(['id', 'name', 'code']),
            'modules' => Permission::Modules,
            'actions' => Permission::Actions,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRoleRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $role = Role::create([
            'name' => $validated['name'],
            'code' => $validated['code'],
            'company_id' => $validated['company_id'] ?? null,
        ]);

        $role->permissions()->sync($validated['permission_ids'] ?? []);

        return Redirect::route('roles.index');
    }

    public function update(UpdateRoleRequest $request, int $role): RedirectResponse
    {
        $validated = $request->validated();
        $target = $request->user()->manageableRoles()->findOrFail($role);

        $target->update([
            'name' => $validated['name'],
            'code' => $validated['code'],
            'company_id' => $validated['company_id'] ?? null,
        ]);
        $target->permissions()->sync($validated['permission_ids'] ?? []);

        return Redirect::route('roles.index');
    }

    public function destroy(Request $request, int $role): RedirectResponse
    {
        $request->user()->manageableRoles()->findOrFail($role)->delete();

        return Redirect::route('roles.index');
    }

    public function restore(Request $request, int $role): RedirectResponse
    {
        $request->user()->manageableRoles()->onlyTrashed()->findOrFail($role)->restore();

        return Redirect::route('roles.index');
    }

    public function forceDelete(Request $request, int $role): RedirectResponse
    {
        $request->user()->manageableRoles()->onlyTrashed()->findOrFail($role)->forceDelete();

        return Redirect::route('roles.index');
    }
}
