<?php

namespace App\Http\Requests;

use App\Models\Company;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRoleRequest extends FormRequest
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
            'code' => ['required', 'string', 'alpha_dash', 'max:100', Rule::unique(Role::class)],
            'company_id' => [
                $this->user()->isSupport() ? 'nullable' : 'required',
                'integer',
                Rule::exists(Company::class, 'id')
                    ->whereIn('id', $this->user()->accessibleCompanies()->select('companies.id')->pluck('id')),
            ],
            'permission_ids' => ['nullable', 'array'],
            'permission_ids.*' => [
                'integer',
                Rule::exists(Permission::class, 'id')
                    ->whereIn('id', $this->user()->grantablePermissions()->select('permissions.id')->pluck('id')),
            ],
        ];
    }
}
