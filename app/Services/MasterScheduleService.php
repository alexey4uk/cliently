<?php

namespace App\Services;

use App\Models\Master;
use App\Models\MasterSchedule;
use Carbon\Carbon;

class MasterScheduleService
{
    /**
     * Сохранить расписание для мастера
     */
    public function saveScheduleForMaster(array $data, Master $master): void
    {
        // Регулярное расписание по дням
        if (isset($data['schedules'])) {
            $this->saveSchedules($data['schedules'], $master);
        }

        // Переопределения на даты
        if (isset($data['overrides'])) {
            $this->saveOverrides($data['overrides'], $master);
        }
    }

    /**
     * Получить расписание для мастера
     */
    public function getScheduleForMaster(Master $master): array
    {
        $schedules = $master
            ->schedules()
            ->with('breaks')
            ->get()
            ->keyBy('day_of_week');
        $overrides = $master->dayOverrides()->get()->keyBy('date');

        return [
            'schedules' => $schedules->toArray(),
            'overrides' => $overrides->toArray(),
        ];
    }

    /**
     * Проверить, работает ли мастер в указанное время
     */
    public function isWorkingAt(
        Master $master,
        Carbon $date,
        string $time,
    ): bool {
        $workingTime = $this->getWorkingTimeForDate($master, $date);

        if (! $workingTime) {
            return false;
        }

        $timeCarbon = Carbon::parse($time);
        $from = Carbon::parse($workingTime['from']);
        $to = Carbon::parse($workingTime['to']);

        // Проверить перерывы
        if ($this->isInBreak($master, $date, $timeCarbon)) {
            return false;
        }

        return $timeCarbon->gte($from) && $timeCarbon->lte($to);
    }

    /**
     * Получить время работы на дату
     */
    public function getWorkingTimeForDate(Master $master, Carbon $date): ?array
    {
        // Сначала проверить переопределения
        $override = $master
            ->dayOverrides()
            ->where('date', $date->format('Y-m-d'))
            ->first();
        if ($override) {
            if (! $override->is_working) {
                return null;
            }

            return $this->normalizeWorkingWindow($override->start_time, $override->end_time);
        }

        // Иначе регулярное расписание
        $dayOfWeek = $date->dayOfWeek; // 0 - воскресенье, 1 - понедельник, ...
        $schedule = $master
            ->schedules()
            ->where('day_of_week', $dayOfWeek)
            ->first();

        if (! $schedule || ! $schedule->is_working) {
            return null;
        }

        return $this->normalizeWorkingWindow($schedule->start_time, $schedule->end_time);
    }

    /**
     * Нормализовать окно работы.
     * 00:00–00:00 трактуется как круглосуточно (весь день 00:00–24:00).
     * Начало >= конца в остальных случаях — выходной (null).
     */
    private function normalizeWorkingWindow($from, $to): ?array
    {
        $fromTime = $from ? Carbon::parse($from) : null;
        $toTime = $to ? Carbon::parse($to) : null;

        if (! $fromTime || ! $toTime) {
            return null;
        }

        // 00:00–00:00 = круглосуточно (весь день); конец 24:00 чтобы слот 23:00+1ч помещался
        if ($fromTime->format('H:i') === '00:00' && $toTime->format('H:i') === '00:00') {
            return [
                'from' => '00:00',
                'to' => '24:00',
            ];
        }

        // Начало >= конца в остальных случаях — выходной
        if ($fromTime->gte($toTime)) {
            return null;
        }

        return [
            'from' => $fromTime->format('H:i'),
            'to' => $toTime->format('H:i'),
        ];
    }

    /**
     * Проверить, находится ли время в перерыве
     */
    private function isInBreak(Master $master, Carbon $date, Carbon $time): bool
    {
        $dayOfWeek = $date->dayOfWeek;
        $schedule = $master
            ->schedules()
            ->where('day_of_week', $dayOfWeek)
            ->first();

        if (! $schedule) {
            return false;
        }

        $breaks = $schedule->breaks;
        foreach ($breaks as $break) {
            $breakFrom = Carbon::parse($break->start_time);
            $breakTo = Carbon::parse($break->end_time);
            if ($time->gte($breakFrom) && $time->lte($breakTo)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Сохранить регулярное расписание
     */
    private function saveSchedules(array $schedules, Master $master): void
    {
        // Все дни недели
        $allDays = [0, 1, 2, 3, 4, 5, 6];
        $submittedDays = array_keys($schedules);

        // Удаляем все существующие расписания мастера
        $master->schedules()->delete();

        // Создаем записи только для рабочих дней
        foreach ($schedules as $day => $data) {
            $isWorking = isset($data['is_working']) && $data['is_working'] == 1;

            if ($isWorking) {
                $master->schedules()->create([
                    'day_of_week' => $day,
                    'start_time' => $data['start_time'] ?? null,
                    'end_time' => $data['end_time'] ?? null,
                    'is_working' => true,
                ]);
            }
        }
    }

    /**
     * Сохранить перерывы
     */
    private function saveBreaks(array $breaks, MasterSchedule $schedule): void
    {
        $schedule->breaks()->delete(); // Очистить старые

        foreach ($breaks as $breakData) {
            $schedule->breaks()->create($breakData);
        }
    }

    /**
     * Сохранить переопределения
     */
    private function saveOverrides(array $overrides, Master $master): void
    {
        foreach ($overrides as $data) {
            $date = $data['date'] ?? null;
            // Пропускаем записи без даты или с плейсхолдером (например new_1 от формы)
            if (! $date || ! preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
                continue;
            }

            $master->dayOverrides()->updateOrCreate(
                ['date' => $date],
                [
                    'is_working' => $data['is_working'] ?? true,
                    'start_time' => $data['start_time'] ?? null,
                    'end_time' => $data['end_time'] ?? null,
                ],
            );
        }
    }
}
