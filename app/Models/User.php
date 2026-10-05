<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'code', 'email', 'branch_id', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function companies(): BelongsToMany
    {
        return $this->belongsToMany(Company::class)->withTimestamps();
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class)->withTimestamps();
    }

    public function hasPermission(string $permissionCode): bool
    {
        return $this->roles()
            ->whereHas('permissions', fn (Builder $query): Builder => $query->where('code', $permissionCode))
            ->exists();
    }

    public function isSupport(): bool
    {
        return $this->roles()
            ->whereNull('roles.company_id')
            ->where('roles.code', Role::SupportCode)
            ->exists();
    }

    /**
     * @return Builder<Company>
     */
    public function accessibleCompanies(): Builder
    {
        return Company::query()->when(
            ! $this->isSupport(),
            fn (Builder $query): Builder => $query->whereIn('companies.id', $this->companies()->withTrashed()->select('companies.id')),
        );
    }

    /**
     * Users the actor can see: everyone for support, otherwise non-admin users of their companies.
     *
     * @return Builder<User>
     */
    public function manageableUsers(): Builder
    {
        return User::query()
            ->when(! $this->isSupport(), fn (Builder $query): Builder => $query
                ->whereHas('companies', fn (Builder $companies): Builder => $companies
                    ->whereIn('companies.id', $this->companies()->withTrashed()->select('companies.id')))
                ->whereDoesntHave('roles', fn (Builder $roles): Builder => $roles
                    ->whereNull('roles.company_id')
                    ->whereIn('roles.code', [Role::SupportCode, Role::AdministratorCode])));
    }

    /**
     * @return Builder<User>
     */
    public function editableUsers(): Builder
    {
        return $this->manageableUsers()->whereKeyNot($this->getKey());
    }

    public function canRestore(string $module): bool
    {
        return $this->hasPermission("{$module}.restore");
    }

    /**
     * @return Builder<Role>
     */
    public function manageableRoles(): Builder
    {
        return Role::query()
            ->where('roles.code', '!=', Role::SupportCode)
            ->when(! $this->isSupport(), fn (Builder $query): Builder => $query
                ->whereIn('roles.company_id', $this->companies()->withTrashed()->select('companies.id')));
    }

    /**
     * @return Builder<Role>
     */
    public function assignableRoles(): Builder
    {
        return Role::query()->when(
            ! $this->isSupport(),
            fn (Builder $query): Builder => $query->where(fn (Builder $query): Builder => $query
                ->where(fn (Builder $global): Builder => $global
                    ->whereNull('roles.company_id')
                    ->where('roles.code', '!=', Role::SupportCode))
                ->orWhereIn('roles.company_id', $this->companies()->withTrashed()->select('companies.id'))),
        );
    }

    /**
     * @return Builder<Permission>
     */
    public function grantablePermissions(): Builder
    {
        return Permission::query()->when(
            ! $this->isSupport(),
            fn (Builder $query): Builder => $query->whereIn('permissions.id', Permission::query()
                ->select('permissions.id')
                ->whereHas('roles', fn (Builder $roles): Builder => $roles->whereIn('roles.id', $this->roles()->select('roles.id')))),
        );
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
