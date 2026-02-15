<?php

namespace App\Services;

use Carbon\Carbon;

class WorkingHoursService
{
    /**
     * Формирует структуру working_hours из валидированных данных
     */
    public static function format(array $workingHours): array
    {
        $daysOff = $workingHours['days_off'] ?? [];
        $is24Hours = ! empty($workingHours['24_hours']);

        // 00:00 — 00:00 считаем аналогом круглосуточно
        $from = $workingHours['from'] ?? null;
        $to = $workingHours['to'] ?? null;
        if (! $is24Hours && $from && $to) {
            $fromNorm = Carbon::parse($from)->format('H:i');
            $toNorm = Carbon::parse($to)->format('H:i');
            if ($fromNorm === '00:00' && $toNorm === '00:00') {
                $is24Hours = true;
            }
        }

        return [
            'from' => $is24Hours ? '00:00' : $from,
            'to' => $is24Hours ? '00:00' : $to,
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
