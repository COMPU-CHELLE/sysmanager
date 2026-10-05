<?php

namespace App\Models;

use Database\Factories\TicketFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['company_id', 'title', 'status'])]
class Ticket extends Model
{
    /** @use HasFactory<TicketFactory> */
    use HasFactory, SoftDeletes;

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(TicketMessage::class);
    }
}
