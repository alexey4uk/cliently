<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Phone extends Model
{
    protected $fillable = [
        'phoneable_type',
        'phoneable_id',
        'country_id',
        'phone',
        'type',
    ];

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function phoneable(): MorphTo
    {
        return $this->morphTo();
    }
}
