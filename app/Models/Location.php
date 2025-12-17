<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Location extends Model
{
    //
    protected $fillable = [
        'business_id',
        'name',
        'address',
        'description',
        'phone',
        'email',
        'working_hours',
    ];

    public function businesses(): BelongsToMany
    {
        return $this->belongsToMany(Business::class);
    }

    public function services(): BelongsToMany
    {
        return $this->belongsToMany(Service::class);
    }
}
