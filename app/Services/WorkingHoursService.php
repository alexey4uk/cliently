<?php

namespace App\Services;

class WorkingHoursService
{
    /**
     * Формирует структуру working_hours из валидированных данных
     */
    public static function format(array $workingHours): array
    {
        $daysOff = $workingHours['days_off'] ?? [];
        $is24Hours = !empty($workingHours['24_hours']);

        return [
            'from' => $is24Hours ? '00:00' : ($workingHours['from'] ?? null),
            'to' => $is24Hours ? '00:00' : ($workingHours['to'] ?? null),
            '24_hours' => $is24Hours,
            'days_off' => $daysOff,
        ];
    }

    /**
     * Преобразует working_hours в JSON строку для сохранения в БД
     */
    public static function toJson(array $workingHours): string
    {
        return json_encode(self::format($workingHours));
    }
}

