<?php

namespace App\Models;

use Database\Factories\AuditLogFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['user_id', 'company_id', 'action', 'entity', 'entity_id', 'method', 'path', 'ip', 'payload', 'before_data', 'after_data', 'status_code', 'error'])]
class AuditLog extends Model
{
    public const UPDATED_AT = null;

    /** @use HasFactory<AuditLogFactory> */
    use HasFactory;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'payload' => 'array',
            'before_data' => 'array',
            'after_data' => 'array',
        ];
    }
}
