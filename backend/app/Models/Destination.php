<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'municipality_id', 'category_id', 'slug', 'name', 'summary', 'description',
    'address', 'latitude', 'longitude', 'access_notes', 'source_url',
    'source_label', 'status', 'verification_status', 'verified_at',
    'published_at', 'created_by', 'updated_by',
])]
class Destination extends Model
{
    use SoftDeletes;

    protected function casts(): array
    {
        return [
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'verified_at' => 'datetime',
            'published_at' => 'datetime',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function municipality(): BelongsTo
    {
        return $this->belongsTo(Municipality::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function photos(): HasMany
    {
        return $this->hasMany(DestinationPhoto::class)->orderBy('sort_order');
    }

    public function highlights(): HasMany
    {
        return $this->hasMany(DestinationHighlight::class)->orderBy('sort_order');
    }

    public function updates(): HasMany
    {
        return $this->hasMany(DestinationUpdate::class)->latest('published_at');
    }

    public function favoritedBy(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'favorites')->withTimestamps();
    }
}
