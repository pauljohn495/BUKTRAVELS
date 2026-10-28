<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Itinerary extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'name', 'start_date', 'notes'];

    protected function casts(): array
    {
        return ['start_date' => 'date'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function stops(): HasMany
    {
        return $this->hasMany(ItineraryStop::class)->orderBy('position');
    }
}
