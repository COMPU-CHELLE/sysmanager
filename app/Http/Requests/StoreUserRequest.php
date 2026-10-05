<?php

namespace App\Http\Requests;

use App\Models\Company;
use App\Models\Role;
use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;

class StoreUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:255', Rule::unique(User::class)],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'branch_id' => ['nullable', 'integer', Rule::exists('branches', 'id')->whereNull('deleted_at')->whereIn('company_id', $this->user()->accessibleCompanies()->pluck('companies.id'))],
            'company_ids' => ['nullable', 'array'],
            'company_ids.*' => [
                'integer',
                Rule::exists(Company::class, 'id')
                    ->where('is_active', true)
                    ->whereIn('id', $this->user()->accessibleCompanies()->select('companies.id')->pluck('id')),
            ],
            'role_ids' => ['nullable', 'array'],
            'role_ids.*' => [
                'integer',
                Rule::exists(Role::class, 'id')
                    ->whereIn('id', $this->user()->assignableRoles()->select('roles.id')->pluck('id')),
            ],
        ];
    }
}
