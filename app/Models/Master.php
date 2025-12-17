<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Master extends Model
{
    protected $fillable = [
        'business_id',
        'name',
        'description',
        'specialization',
        'email',
        'phone',
    ];

    public function locations(): BelongsToMany
    {
        return $this->belongsToMany(Location::class, 'master_location')
            ->withTimestamps();
    }

    public function services(): belongsToMany
    {
        return $this->belongsToMany(Service::class, 'service_master')
            ->withTimestamps();
    }
}
