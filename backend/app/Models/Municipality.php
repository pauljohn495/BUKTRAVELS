<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'slug', 'type', 'verification_status'])]
class Municipality extends Model
{
    public function destinations(): HasMany
    {
        return $this->hasMany(Destination::class);
    }
}
