<?php

namespace App\Models;

use Database\Factories\AssetDetailFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['asset_id', 'ram_type', 'ram_capacity', 'hdd_type', 'hdd_capacity', 'pro_type', 'pro_detail', 'mbr_type', 'mbr_detail', 'gra_type', 'gra_detail', 'monitor', 'mon_detail', 'keyboard', 'key_detail', 'mouse', 'mou_detail'])]
class AssetDetail extends Model
{
    /** @use HasFactory<AssetDetailFactory> */
    use HasFactory;

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }
}
