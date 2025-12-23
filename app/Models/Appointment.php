<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;
use Carbon\Carbon;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'client_id',
        'service_id',
        'master_id',
        'location_id',
        'date',
        'time',
        'status',
        'notes',
        'duration',
        'price',
        'token',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($appointment) {
            if (empty($appointment->token)) {
                $appointment->token = Str::random(64);
            }
        });
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function master(): BelongsTo
    {
        return $this->belongsTo(Master::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    /**
     * Получить дату и время записи
     */
    public function getDateTimeAttribute(): Carbon
    {
        return Carbon::parse($this->date->format('Y-m-d') . ' ' . $this->time);
    }


    /**
     * Получить финальную цену (переопределенная или из услуги)
     */
    public function getFinalPriceAttribute(): float
    {
        return $this->price ?? $this->service->price;
    }

    /**
     * Получить финальную длительность (переопределенная или из услуги)
     */
    public function getFinalDurationAttribute(): int
    {
        return $this->duration ?? $this->service->duration;
    }

    /**
     * Проверить, является ли запись на сегодня
     */
    public function isToday(): bool
    {
        return $this->date->isToday();
    }

    /**
     * Проверить, является ли запись в прошлом
     */
    public function isPast(): bool
    {
        return $this->dateTime->isPast();
    }

    /**
     * Проверить, пересекается ли запись с другим временным интервалом
     *
     * @param Carbon $startTime Время начала
     * @param int $duration Длительность в минутах
     * @param int|null $excludeAppointmentId ID записи для исключения (при обновлении)
     * @return bool
     */
    public function overlapsWith(Carbon $startTime, int $duration, ?int $excludeAppointmentId = null): bool
    {
        // Исключаем отмененные записи
        if ($this->status === 'cancelled') {
            return false;
        }

        // Исключаем текущую запись при обновлении
        if ($excludeAppointmentId && $this->id === $excludeAppointmentId) {
            return false;
        }

        $appointmentStart = $this->dateTime;
        $appointmentDuration = $this->final_duration;
        $appointmentEnd = $appointmentStart->copy()->addMinutes($appointmentDuration);

        $checkEnd = $startTime->copy()->addMinutes($duration);

        // Проверяем пересечение временных интервалов
        return $startTime->lt($appointmentEnd) && $checkEnd->gt($appointmentStart);
    }

    /**
     * Проверить, есть ли конфликтующие записи для мастера
     *
     * @param int $masterId ID мастера
     * @param Carbon $date Дата
     * @param string $time Время в формате H:i
     * @param int $duration Длительность в минутах
     * @param int|null $excludeAppointmentId ID записи для исключения
     * @return bool
     */
    public static function hasConflictForMaster(int $masterId, Carbon $date, string $time, int $duration, ?int $excludeAppointmentId = null): bool
    {
        $startTime = Carbon::parse($date->format('Y-m-d') . ' ' . $time);

        $appointments = self::where('master_id', $masterId)
            ->where('date', $date->format('Y-m-d'))
            ->where('status', '!=', 'cancelled')
            ->get();

        foreach ($appointments as $appointment) {
            if ($appointment->overlapsWith($startTime, $duration, $excludeAppointmentId)) {
                return true;
            }
        }

        return false;
    }
}
