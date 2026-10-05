<?php

namespace App\Models;

use Database\Factories\CompanyFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['name', 'code', 'nit', 'logo', 'plan_id', 'is_active'])]
class Company extends Model
{
    /** @use HasFactory<CompanyFactory> */
    use HasFactory, SoftDeletes;

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->withTimestamps();
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    public function roles(): HasMany
    {
        return $this->hasMany(Role::class);
    }

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }
}
