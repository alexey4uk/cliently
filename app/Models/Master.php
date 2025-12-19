<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Master extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'user_id',
        'name',
        'description',
        'photo',
        'specialization',
        'phone',
        'email',
        'working_hours',
        'is_active',
        'last_name',
        'first_name',
    ];

    public function locations(): BelongsToMany
    {
        return $this->belongsToMany(Location::class, 'master_location')
            ->withTimestamps();
    }

    public function services(): BelongsToMany
    {
        return $this->belongsToMany(Service::class, 'service_master')
        ->withPivot('price')
        ->withTimestamps();
    }
}
