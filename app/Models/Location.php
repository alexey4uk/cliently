<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Location extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'name',
        'city',
        'street',
        'house',
        'building',
        'apartment',
        'description',
        'phone',
        'working_hours',
    ];

    /**
     * Get the full address attribute.
     */
    public function getFullAddressAttribute(): string
    {
        $parts = array_filter([
            $this->city,
            $this->street ? "ул. {$this->street}" : null,
            $this->house ? "д. {$this->house}" : null,
            $this->building ? "корп. {$this->building}" : null,
            $this->apartment ? "кв. {$this->apartment}" : null,
        ]);

        return implode(', ', $parts);
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function services(): BelongsToMany
    {
        return $this->belongsToMany(Service::class, 'location_service')
            ->withTimestamps();
    }

    public function masters(): BelongsToMany
    {
        return $this->belongsToMany(Master::class, 'master_location')
            ->withTimestamps();
    }
}
