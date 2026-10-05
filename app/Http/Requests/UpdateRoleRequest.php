<?php

namespace App\Http\Requests;

use App\Models\Company;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRoleRequest extends FormRequest
{
    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $actor = $this->user();

        return [
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'alpha_dash', 'max:100', Rule::unique(Role::class)->ignore($this->route('role'))],
            'company_id' => [
                $actor->isSupport() ? 'nullable' : 'required',
                'integer',
                Rule::exists(Company::class, 'id')
                    ->whereIn('id', $actor->accessibleCompanies()->pluck('companies.id')),
            ],
            'permission_ids' => ['nullable', 'array'],
            'permission_ids.*' => [
                'integer',
                Rule::exists(Permission::class, 'id')
                    ->whereIn('id', $actor->grantablePermissions()->pluck('permissions.id')),
            ],
        ];
    }
}
