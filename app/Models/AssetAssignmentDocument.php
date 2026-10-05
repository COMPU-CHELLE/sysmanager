<?php

namespace App\Models;

use Database\Factories\AssetAssignmentDocumentFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['asset_assignment_id', 'document_number', 'generated_at', 'generated_by_id'])]
class AssetAssignmentDocument extends Model
{
    /** @use HasFactory<AssetAssignmentDocumentFactory> */
    use HasFactory;

    public function assignment(): BelongsTo
    {
        return $this->belongsTo(AssetAssignment::class);
    }

    public function generatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'generated_by_id');
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'generated_at' => 'datetime',
        ];
    }
}
