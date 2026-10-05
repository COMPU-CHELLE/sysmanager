<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\Branch;
use App\Models\Company;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        $actor = $request->user();

        return Inertia::render('Users/Index', [
            'users' => $actor->manageableUsers()
                ->when($actor->canRestore('users'), fn (Builder $query): Builder => $query->withTrashed())
                ->with(['companies:id,name,code', 'roles:id,name,code', 'branch:id,name'])
                ->orderBy('name')
                ->get()
                ->map(fn (User $user): array => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'code' => $user->code,
                    'email' => $user->email,
                    'branchId' => $user->branch_id,
                    'branchName' => $user->branch?->name,
                    'locked' => $user->is($actor),
                    'deletedAt' => $user->deleted_at?->toDateTimeString(),
                    'companies' => $user->companies->map(fn (Company $company): array => [
                        'id' => $company->id,
                        'name' => $company->name,
                    ]),
                    'roles' => $user->roles->map(fn (Role $role): array => [
                        'id' => $role->id,
                        'name' => $role->name,
                    ]),
                ]),
            'branches' => Branch::query()
                ->whereIn('company_id', $actor->accessibleCompanies()->select('companies.id'))
                ->orderBy('name')
                ->get(['id', 'name', 'company_id']),
            'companies' => $actor->accessibleCompanies()
                ->where('is_active', true)
                ->orderBy('name')
                ->get(['id', 'name', 'code']),
            'roles' => $actor->assignableRoles()
                ->with('company:id,name')
                ->orderBy('name')
                ->get()
                ->map(fn (Role $role): array => [
                    'id' => $role->id,
                    'name' => $role->name,
                    'code' => $role->code,
                    'company' => $role->company?->name,
                ]),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $user = User::create([
            'name' => $validated['name'],
            'code' => $validated['code'],
            'email' => $validated['email'],
            'branch_id' => $validated['branch_id'] ?? null,
            'password' => Hash::make($validated['password']),
        ]);

        $user->companies()->sync($validated['company_ids'] ?? []);
        $user->roles()->sync($validated['role_ids'] ?? []);

        return Redirect::route('users.index');
    }

    public function update(UpdateUserRequest $request, int $user): RedirectResponse
    {
        $validated = $request->validated();
        $target = $request->user()->editableUsers()->findOrFail($user);

        $target->fill(collect($validated)->only(['name', 'code', 'email', 'branch_id'])->all());

        if (! empty($validated['password'])) {
            $target->password = $validated['password'];
        }

        $target->save();
        $actor = $request->user();
        $target->companies()->sync([
            ...$target->companies()->whereNotIn('companies.id', $actor->accessibleCompanies()->select('companies.id'))->pluck('companies.id')->all(),
            ...($validated['company_ids'] ?? []),
        ]);
        $target->roles()->sync([
            ...$target->roles()->whereNotIn('roles.id', $actor->assignableRoles()->select('roles.id'))->pluck('roles.id')->all(),
            ...($validated['role_ids'] ?? []),
        ]);

        return Redirect::route('users.index');
    }

    public function destroy(Request $request, int $user): RedirectResponse
    {
        $request->user()->editableUsers()->findOrFail($user)->delete();

        return Redirect::route('users.index');
    }

    public function restore(Request $request, int $user): RedirectResponse
    {
        $request->user()->editableUsers()->onlyTrashed()->findOrFail($user)->restore();

        return Redirect::route('users.index');
    }

    public function forceDelete(Request $request, int $user): RedirectResponse
    {
        $request->user()->editableUsers()->onlyTrashed()->findOrFail($user)->forceDelete();

        return Redirect::route('users.index');
    }
}
