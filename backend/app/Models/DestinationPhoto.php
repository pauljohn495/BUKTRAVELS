<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

#[Fillable(['destination_id', 'url', 'disk', 'path', 'original_name', 'alt_text', 'attribution', 'source_url', 'license', 'is_primary', 'verification_status', 'sort_order'])]
class DestinationPhoto extends Model
{
    protected function casts(): array
    {
        return ['is_primary' => 'boolean'];
    }

    public function destination(): BelongsTo
    {
        return $this->belongsTo(Destination::class);
    }

    public function publicUrl(): string
    {
        if (! $this->path || ! $this->disk) {
            return $this->url;
        }

        $storageUrl = Storage::disk($this->disk)->url($this->path);

        return str_starts_with($storageUrl, 'http') ? $storageUrl : rtrim(config('app.url'), '/').'/'.ltrim($storageUrl, '/');
    }
}
