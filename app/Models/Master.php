<?php

namespace App\Models;

use App\Services\MasterScheduleService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Master extends Model
{
    use HasFactory, SoftDeletes;

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
        return trim($this->first_name.' '.$this->last_name);
    }

    public function locations(): BelongsToMany
    {
        return $this->belongsToMany(
            Location::class,
            'master_location',
        )->withTimestamps();
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
        return $this->morphOne(Phone::class, 'phoneable')->where(
            'type',
            'primary',
        );
    }

    public function getPhoneAttribute(): ?string
    {
        return $this->primaryPhone?->phone;
    }

    /**
     * Получить working_hours как массив (для обратной совместимости)
     * Теперь данные из новых таблиц, конвертируем в старый формат
     */
    protected function getWorkingHoursArray(): ?array
    {
        $service = app(MasterScheduleService::class);
        $schedule = $service->getScheduleForMaster($this);

        // Конвертировать в старый формат
        $workingHours = [];
        $daysOff = [];

        foreach ($schedule['schedules'] as $day => $data) {
            if (! $data['is_working']) {
                $daysOff[] = $day;
            } else {
                $workingHours['from'] = $data['start_time'];
                $workingHours['to'] = $data['end_time'];
            }
        }

        $workingHours['days_off'] = $daysOff;
        $workingHours['24_hours'] = false; // Пока не поддерживаем

        return $workingHours;
    }

    /**
     * Проверить, является ли день выходным для мастера
     */
    public function isDayOff(Carbon $date): bool
    {
        $service = app(MasterScheduleService::class);
        $workingTime = $service->getWorkingTimeForDate($this, $date);

        return $workingTime === null;
    }

    /**
     * Получить время работы на конкретную дату
     *
     * @return array|null ['from' => '09:00', 'to' => '18:00'] или null если выходной
     */
    public function getWorkingTimeForDate(Carbon $date): ?array
    {
        $service = app(MasterScheduleService::class);

        return $service->getWorkingTimeForDate($this, $date);
    }

    /**
     * Проверить, работает ли мастер в указанное время
     */
    public function isWorkingAt(Carbon $date, string $time): bool
    {
        $service = app(MasterScheduleService::class);

        return $service->isWorkingAt($this, $date, $time);
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
