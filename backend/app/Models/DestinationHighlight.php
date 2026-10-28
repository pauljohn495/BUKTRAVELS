<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['destination_id', 'label', 'verification_status', 'sort_order'])]
class DestinationHighlight extends Model
{
    public function destination(): BelongsTo
    {
        return $this->belongsTo(Destination::class);
    }
}
