<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Appointment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'business_id',
        'client_id',
        'service_id',
        'master_id',
        'location_id',
        'date',
        'time',
        'status',
        'source',
        'notes',
        'duration',
        'price',
        'token',
        'reminder_sent_at',
    ];

    protected $casts = [
        'date' => 'date',
        'reminder_sent_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::creating(function ($appointment) {
            if (empty($appointment->token)) {
                $appointment->token = Str::random(16);
            }
        });
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class)->withTrashed();
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class)->withTrashed();
    }

    public function master(): BelongsTo
    {
        return $this->belongsTo(Master::class)->withTrashed();
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class)->withTrashed();
    }

    public function getDateTimeAttribute(): Carbon
    {
        return Carbon::parse($this->date->format('Y-m-d').' '.$this->time);
    }

    public function getFinalPriceAttribute(): float
    {
        return $this->price ?? $this->service?->price ?? 0.0;
    }

    public function getFinalDurationAttribute(): int
    {
        return (int) ($this->duration ?? $this->service?->duration ?? 0);
    }

    public function isToday(): bool
    {
        return $this->date->isToday();
    }

    public function isPast(): bool
    {
        return $this->dateTime->isPast();
    }

    /**
     * Проверить, пересекается ли запись с другим временным интервалом
     *
     * @param  Carbon  $startTime  Время начала
     * @param  int  $duration  Длительность в минутах
     * @param  int|null  $excludeAppointmentId  ID записи для исключения (при обновлении)
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
     * @param  int  $masterId  ID мастера
     * @param  Carbon  $date  Дата
     * @param  string  $time  Время в формате H:i
     * @param  int  $duration  Длительность в минутах
     * @param  int|null  $excludeAppointmentId  ID записи для исключения
     */
    public static function hasConflictForMaster(int $masterId, Carbon $date, string $time, int $duration, ?int $excludeAppointmentId = null): bool
    {
        // Нормализуем время до H:i (из БД может прийти 14:30:00)
        $timeNormalized = Carbon::parse($time)->format('H:i');
        $startTime = Carbon::parse($date->format('Y-m-d').' '.$timeNormalized);
        $dateStr = $date->format('Y-m-d');

        $appointments = self::where('master_id', $masterId)
            ->where('date', $dateStr)
            ->whereNotIn('status', ['cancelled'])
            ->with('service')
            ->get();

        foreach ($appointments as $appointment) {
            if ($appointment->overlapsWith($startTime, $duration, $excludeAppointmentId)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Проверить, свободен ли слот хотя бы у одного мастера (для записи «к любому мастеру»)
     */
    public static function isSlotFreeForAnyMaster(
        int $businessId,
        int $locationId,
        int $serviceId,
        Carbon $date,
        string $time,
        int $duration,
    ): bool {
        $masters = Master::where('business_id', $businessId)
            ->where('is_active', true)
            ->whereHas('services', fn ($q) => $q->where('services.id', $serviceId))
            ->whereHas('locations', fn ($q) => $q->where('locations.id', $locationId))
            ->get();

        $scheduleService = app(\App\Services\MasterScheduleService::class);

        foreach ($masters as $master) {
            $workingTime = $scheduleService->getWorkingTimeForDate($master, $date);
            if (! $workingTime) {
                continue;
            }
            $startTime = Carbon::parse($time);
            $endTime = $startTime->copy()->addMinutes($duration);
            $workStart = Carbon::parse($workingTime['from']);
            $workEnd = Carbon::parse($workingTime['to']);
            if ($startTime->lt($workStart) || $endTime->gt($workEnd)) {
                continue;
            }
            if (self::hasConflictForMaster($master->id, $date, $time, $duration)) {
                continue;
            }

            return true;
        }

        return false;
    }
}
