<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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
    ];

    protected $casts = [
        'date' => 'date',
    ];

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
}
