<?php

namespace App\Models;

use Database\Factories\TaskFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['company_id', 'created_by_id', 'assigned_to_id', 'title', 'description', 'status', 'priority', 'due_date', 'closed_at', 'solution'])]
class Task extends Model
{
    /** @use HasFactory<TaskFactory> */
    use HasFactory, SoftDeletes;

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to_id');
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'due_date' => 'datetime',
            'closed_at' => 'datetime',
        ];
    }
}
