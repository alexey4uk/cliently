<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

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
        'email',
        'is_active',
        'last_name',
        'first_name',
    ];

    protected $casts = [
        'working_hours' => 'array',
    ];

    /**
     * Get the master's full name
     */
    public function getNameAttribute(): string
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }

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

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
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
     * Получить working_hours как массив
     */
    protected function getWorkingHoursArray(): ?array
    {
        $workingHours = $this->working_hours;

        if (empty($workingHours)) {
            return null;
        }

        // Если это уже массив (из-за cast)
        if (is_array($workingHours)) {
            return $workingHours;
        }

        // Если это строка JSON, декодируем
        if (is_string($workingHours)) {
            $decoded = json_decode($workingHours, true);

            return is_array($decoded) ? $decoded : null;
        }

        return null;
    }

    /**
     * Проверить, является ли день выходным для мастера
     */
    public function isDayOff(Carbon $date): bool
    {
        $workingHours = $this->getWorkingHoursArray();

        if (! $workingHours || ! is_array($workingHours)) {
            return true; // Если нет данных о рабочем времени, считаем выходным
        }

        $daysOff = $workingHours['days_off'] ?? [];
        $dayOfWeek = $date->dayOfWeek; // 0 (воскресенье) до 6 (суббота)

        return in_array($dayOfWeek, $daysOff);
    }

    /**
     * Получить время работы на конкретную дату
     *
     * @return array|null ['from' => '09:00', 'to' => '18:00'] или null если выходной
     */
    public function getWorkingTimeForDate(Carbon $date): ?array
    {
        if ($this->isDayOff($date)) {
            return null;
        }

        $workingHours = $this->getWorkingHoursArray();

        if (! $workingHours || ! is_array($workingHours)) {
            return null;
        }

        if (isset($workingHours['24_hours']) && $workingHours['24_hours']) {
            return [
                'from' => '00:00',
                'to' => '23:59',
            ];
        }

        $from = $workingHours['from'] ?? null;
        $to = $workingHours['to'] ?? null;

        if ($from && $to) {
            return [
                'from' => $from,
                'to' => $to,
            ];
        }

        return null;
    }

    /**
     * Проверить, работает ли мастер в указанное время
     */
    public function isWorkingAt(Carbon $date, string $time): bool
    {
        $workingTime = $this->getWorkingTimeForDate($date);

        if (! $workingTime) {
            return false;
        }

        $timeCarbon = Carbon::parse($time);
        $from = Carbon::parse($workingTime['from']);
        $to = Carbon::parse($workingTime['to']);

        return $timeCarbon->gte($from) && $timeCarbon->lte($to);
    }

    public function schedules()
    {
        return $this->hasMany(MasterSchedule::class);
    }

    public function dayOverrides()
    {
        return $this->hasMany(MasterDayOverride::class);
    }
}
