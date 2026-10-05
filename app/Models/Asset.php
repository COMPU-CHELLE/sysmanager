<?php

namespace App\Models;

use Database\Factories\AssetFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['company_id', 'branch_id', 'name', 'type', 'serial', 'brand', 'model', 'purchase_date', 'cost'])]
class Asset extends Model
{
    /** @use HasFactory<AssetFactory> */
    use HasFactory, SoftDeletes;

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function detail(): HasOne
    {
        return $this->hasOne(AssetDetail::class);
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(AssetAssignment::class);
    }

    public function maintenances(): HasMany
    {
        return $this->hasMany(Maintenance::class);
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'purchase_date' => 'date',
            'cost' => 'decimal:2',
        ];
    }
}
