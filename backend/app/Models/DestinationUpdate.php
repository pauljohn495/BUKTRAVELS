<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['destination_id', 'title', 'body', 'status', 'published_at', 'created_by'])]
class DestinationUpdate extends Model
{
    protected function casts(): array
    {
        return ['published_at' => 'datetime'];
    }

    public function destination(): BelongsTo
    {
        return $this->belongsTo(Destination::class);
    }
}
