<?php

namespace App\Http\Requests;

use App\Models\Company;
use App\Models\Role;
use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;

class UpdateUserRequest extends FormRequest
{
    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $actor = $this->user();

        return [
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:255', Rule::unique(User::class)->ignore($this->route('user'))],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)->ignore($this->route('user'))],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            'branch_id' => ['nullable', 'integer', Rule::exists('branches', 'id')->whereNull('deleted_at')->whereIn('company_id', $actor->accessibleCompanies()->pluck('companies.id'))],
            'company_ids' => ['nullable', 'array'],
            'company_ids.*' => [
                'integer',
                Rule::exists(Company::class, 'id')
                    ->where('is_active', true)
                    ->whereIn('id', $actor->accessibleCompanies()->pluck('companies.id')),
            ],
            'role_ids' => ['nullable', 'array'],
            'role_ids.*' => [
                'integer',
                Rule::exists(Role::class, 'id')
                    ->whereIn('id', $actor->assignableRoles()->pluck('roles.id')),
            ],
        ];
    }
}
