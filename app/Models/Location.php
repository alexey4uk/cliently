<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

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

    public function masters(): BelongsToMany
    {
        return $this->belongsToMany(Master::class, 'master_location')
            ->withTimestamps();
    }

    public function phones(): MorphMany
    {
        return $this->morphMany(Phone::class, 'phoneable');
    }

    public function primaryPhone(): MorphOne
    {
        return $this->morphOne(Phone::class, 'phoneable')->where('type', 'primary');
    }

    public function getPhoneAttribute(): ?string
    {
        return $this->primaryPhone?->phone;
    }

    /**
     * Время работы для отображения: "09:00 – 18:00" или "Круглосуточно"
     */
    public function getWorkingHoursDisplayAttribute(): ?string
    {
        $data = is_string($this->working_hours)
            ? json_decode($this->working_hours, true)
            : $this->working_hours;

        if (empty($data)) {
            return null;
        }

        if (! empty($data['24_hours'])) {
            return 'Круглосуточно';
        }

        $from = $data['from'] ?? null;
        $to = $data['to'] ?? null;
        if ($from && $to) {
            $fromNorm = \Carbon\Carbon::parse($from)->format('H:i');
            $toNorm = \Carbon\Carbon::parse($to)->format('H:i');
            if ($fromNorm === '00:00' && $toNorm === '00:00') {
                return 'Круглосуточно';
            }

            return $fromNorm.' – '.$toNorm;
        }

        return null;
    }
}
