<?php

namespace App\Models;

use Database\Factories\AssetAssignmentFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['asset_id', 'employee_id', 'assigned_at', 'returned_at'])]
class AssetAssignment extends Model
{
    /** @use HasFactory<AssetAssignmentFactory> */
    use HasFactory, SoftDeletes;

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function document(): HasOne
    {
        return $this->hasOne(AssetAssignmentDocument::class);
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'assigned_at' => 'datetime',
            'returned_at' => 'datetime',
        ];
    }
}
