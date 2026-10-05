<?php

namespace App\Models;

use Database\Factories\MaintenanceFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['asset_id', 'description', 'cost', 'performed_at'])]
class Maintenance extends Model
{
    /** @use HasFactory<MaintenanceFactory> */
    use HasFactory, SoftDeletes;

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'performed_at' => 'date',
            'cost' => 'decimal:2',
        ];
    }
}
