<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

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

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($appointment) {
            if (empty($appointment->token)) {
                $appointment->token = self::generateToken();
            }
        });
    }

    /**
     * Генерация красивого читаемого токена
     * Формат: abc-123-def-456 (чередование букв и цифр, 2 группы)
     *
     * Математика безопасности:
     * - Буквы: 26 символов (a-z)
     * - Цифры: 10 символов (0-9)
     * - Комбинаций: (26^3 × 10^3) × (26^3 × 10^3) ≈ 1.2 × 10^12 (более триллиона)
     * - Вероятность коллизии при 1 млн записей: ~0.00004% (практически нулевая)
     * - Вероятность коллизии при 10 млн записей: ~0.004% (все еще крайне низкая)
     */
    protected static function generateToken(): string
    {
        $maxAttempts = 100; // Защита от бесконечного цикла
        $attempts = 0;

        do {
            // Генерируем формат: abc-123-def-456
            // Группа 1: 3 буквы + 3 цифры
            $letters1 = self::generateTokenPart(3, 'letters');
            $digits1 = self::generateTokenPart(3, 'digits');

            // Группа 2: 3 буквы + 3 цифры
            $letters2 = self::generateTokenPart(3, 'letters');
            $digits2 = self::generateTokenPart(3, 'digits');

            $token = strtolower($letters1.'-'.$digits1.'-'.$letters2.'-'.$digits2);
            $attempts++;

            // Если превысили лимит попыток, добавляем случайный суффикс
            if ($attempts >= $maxAttempts) {
                $token .= '-'.self::generateTokenPart(4, 'mixed');
                break;
            }
        } while (self::where('token', $token)->exists());

        return $token;
    }

    /**
     * Генерация части токена
     *
     * @param  int  $length  Длина части
     * @param  string  $type  Тип: 'letters' (только буквы), 'digits' (только цифры), 'mixed' (буквы и цифры)
     */
    protected static function generateTokenPart(int $length, string $type = 'mixed'): string
    {
        $characters = match ($type) {
            'letters' => 'abcdefghijklmnopqrstuvwxyz',
            'digits' => '0123456789',
            'mixed' => 'abcdefghijklmnopqrstuvwxyz0123456789',
            default => 'abcdefghijklmnopqrstuvwxyz0123456789',
        };

        $part = '';

        for ($i = 0; $i < $length; $i++) {
            $part .= $characters[random_int(0, strlen($characters) - 1)];
        }

        return $part;
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
        return Carbon::parse($this->date->format('Y-m-d').' '.$this->time);
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
        return (int) ($this->duration ?? $this->service?->duration ?? 0);
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
