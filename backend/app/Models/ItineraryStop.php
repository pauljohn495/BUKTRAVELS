<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ItineraryStop extends Model
{
    use HasFactory;

    protected $fillable = ['itinerary_id', 'destination_id', 'position', 'visit_date', 'notes'];

    protected function casts(): array
    {
        return ['visit_date' => 'date'];
    }

    public function itinerary(): BelongsTo
    {
        return $this->belongsTo(Itinerary::class);
    }

    public function destination(): BelongsTo
    {
        return $this->belongsTo(Destination::class);
    }
}
