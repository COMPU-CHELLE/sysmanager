<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Models\Company;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        return Inertia::render('Users/Index', [
            'users' => User::query()
                ->with(['companies:id,name,code', 'roles:id,name,code'])
                ->orderBy('name')
                ->get()
                ->map(fn (User $user): array => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'code' => $user->code,
                    'email' => $user->email,
                    'companies' => $user->companies->map(fn (Company $company): array => [
                        'id' => $company->id,
                        'name' => $company->name,
                    ]),
                    'roles' => $user->roles->map(fn (Role $role): array => [
                        'id' => $role->id,
                        'name' => $role->name,
                    ]),
                ]),
            'companies' => Company::query()
                ->where('is_active', true)
                ->orderBy('name')
                ->get(['id', 'name', 'code']),
            'roles' => Role::query()
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
            'password' => Hash::make($validated['password']),
        ]);

        $user->companies()->sync($validated['company_ids'] ?? []);
        $user->roles()->sync($validated['role_ids'] ?? []);

        return Redirect::route('users.index');
    }
}
