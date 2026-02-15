<?php

namespace App\Services\Analytics;

use App\Models\Appointment;
use Carbon\Carbon;

class AnalyticsGeneralService
{
    public function getGeneralData(int $businessId, array $filters): array
    {
        $startDate = Carbon::parse($filters['date_from'])->startOfDay();
        $endDate = Carbon::parse($filters['date_to'])->endOfDay();

        $query = Appointment::where('business_id', $businessId)
            ->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')]);
        $this->applyFilters($query, $filters);
        $appointments = $query->get();

        $statsByStatus = [
            'pending' => $appointments->where('status', 'pending')->count(),
            'confirmed' => $appointments->where('status', 'confirmed')->count(),
            'completed' => $appointments->where('status', 'completed')->count(),
            'cancelled' => $appointments->where('status', 'cancelled')->count(),
        ];
        $total = $appointments->count();
        $completed = $statsByStatus['completed'];
        $cancelled = $statsByStatus['cancelled'];
        $conversionRate = $total > 0 ? round(($completed / $total) * 100, 1) : 0;
        $cancellationRate = $total > 0 ? round(($cancelled / $total) * 100, 1) : 0;

        $financialService = app(AnalyticsFinancialService::class);

        return [
            'total' => $total,
            'stats_by_status' => $statsByStatus,
            'conversion_rate' => $conversionRate,
            'cancellation_rate' => $cancellationRate,
            'stats_by_period' => $this->getStatsByPeriod($businessId, $startDate, $endDate, $filters),
            'stats_by_service' => $this->getStatsByService($businessId, $filters),
            'stats_by_master' => $this->getStatsByMaster($businessId, $filters),
            'stats_by_source' => $this->getStatsBySource($businessId, $filters),
        ];
    }

    public function getTimeAnalytics(int $businessId, array $filters): array
    {
        $startDate = Carbon::parse($filters['date_from'])->startOfDay();
        $endDate = Carbon::parse($filters['date_to'])->endOfDay();

        $query = Appointment::where('business_id', $businessId)
            ->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')]);
        $this->applyFilters($query, $filters);
        $appointments = $query->get();

        $daysOfWeek = ['Понедельник', 'Вторник', 'Среда', 'Четверг', 'Пятница', 'Суббота', 'Воскресенье'];
        $heatmap = [];
        for ($day = 0; $day < 7; $day++) {
            for ($hour = 0; $hour < 24; $hour++) {
                $heatmap[$day][$hour] = 0;
            }
        }

        foreach ($appointments as $a) {
            if ($a->time) {
                $hour = (int) Carbon::parse($a->time)->format('H');
                $dow = $a->date->dayOfWeek;
                $dayIndex = $dow === 0 ? 6 : $dow - 1;
                if (isset($heatmap[$dayIndex][$hour])) {
                    $heatmap[$dayIndex][$hour]++;
                }
            }
        }

        $maxValue = 0;
        foreach ($heatmap as $day) {
            foreach ($day as $c) {
                if ($c > $maxValue) {
                    $maxValue = $c;
                }
            }
        }

        $byDayOfWeek = [];
        foreach ($daysOfWeek as $i => $name) {
            $byDayOfWeek[] = [
                'day' => $name,
                'count' => array_sum($heatmap[$i]),
            ];
        }

        $byHour = [];
        for ($hour = 0; $hour < 24; $hour++) {
            $total = 0;
            for ($day = 0; $day < 7; $day++) {
                $total += $heatmap[$day][$hour] ?? 0;
            }
            $byHour[] = ['hour' => $hour, 'count' => $total];
        }

        $byMonth = [];
        foreach ($appointments as $a) {
            $key = $a->date->format('Y-m');
            if (! isset($byMonth[$key])) {
                $byMonth[$key] = [
                    'month' => $a->date->format('F Y'),
                    'label' => $a->date->locale('ru')->translatedFormat('M Y'),
                    'count' => 0,
                ];
            }
            $byMonth[$key]['count']++;
        }
        ksort($byMonth);

        return [
            'heatmap' => $heatmap,
            'max_value' => $maxValue,
            'by_day_of_week' => $byDayOfWeek,
            'by_hour' => $byHour,
            'by_month' => array_values($byMonth),
            'days_of_week' => $daysOfWeek,
        ];
    }

    /**
     * Статистика по источникам записей (source: online, manual и т.д.)
     */
    public function getStatsBySource(int $businessId, array $filters): array
    {
        $query = Appointment::where('business_id', $businessId)
            ->whereBetween('date', [
                Carbon::parse($filters['date_from'])->format('Y-m-d'),
                Carbon::parse($filters['date_to'])->format('Y-m-d'),
            ]);
        $this->applyFilters($query, $filters);
        $appointments = $query->get();

        $labels = [
            'online' => 'Онлайн-запись',
            'manual' => 'Вручную',
            'telegram' => 'Telegram',
            'widget' => 'Виджет',
            '' => 'Не указан',
        ];

        $bySource = $appointments->groupBy(fn ($a) => $a->source ?? '')->map(function ($group, $sourceKey) use ($labels) {
            return [
                'source' => $sourceKey,
                'label' => $labels[$sourceKey] ?? $sourceKey ?: 'Не указан',
                'count' => $group->count(),
                'completed' => $group->where('status', 'completed')->count(),
            ];
        })->values()->sortByDesc('count')->values();

        return $bySource->toArray();
    }

    private function getStatsByPeriod(int $businessId, Carbon $startDate, Carbon $endDate, array $filters): array
    {
        $query = Appointment::where('business_id', $businessId)
            ->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')]);
        $this->applyFilters($query, $filters);
        $appointments = $query->get();

        $result = [];
        $current = $startDate->copy();
        while ($current->lte($endDate)) {
            $dateStr = $current->format('Y-m-d');
            $day = $appointments->filter(fn ($a) => $a->date->format('Y-m-d') === $dateStr);
            $result[] = [
                'date' => $dateStr,
                'label' => $current->format('d.m'),
                'total' => $day->count(),
                'completed' => $day->where('status', 'completed')->count(),
                'cancelled' => $day->where('status', 'cancelled')->count(),
            ];
            $current->addDay();
        }

        return $result;
    }

    private function getStatsByService(int $businessId, array $filters): array
    {
        $query = Appointment::where('business_id', $businessId)
            ->whereBetween('date', [
                Carbon::parse($filters['date_from'])->format('Y-m-d'),
                Carbon::parse($filters['date_to'])->format('Y-m-d'),
            ]);
        $this->applyFilters($query, $filters);
        $appointments = $query->with('service')->get();

        return $appointments->groupBy('service_id')->map(function ($group, $serviceId) {
            $service = $group->first()->service;

            return [
                'service_id' => $serviceId,
                'service_name' => $service ? $service->name : 'Неизвестная услуга',
                'total' => $group->count(),
                'completed' => $group->where('status', 'completed')->count(),
                'cancelled' => $group->where('status', 'cancelled')->count(),
            ];
        })->values()->sortByDesc('total')->take(10)->values()->toArray();
    }

    private function getStatsByMaster(int $businessId, array $filters): array
    {
        $query = Appointment::where('business_id', $businessId)
            ->whereBetween('date', [
                Carbon::parse($filters['date_from'])->format('Y-m-d'),
                Carbon::parse($filters['date_to'])->format('Y-m-d'),
            ]);
        $this->applyFilters($query, $filters);
        $appointments = $query->with('master')->get();

        return $appointments->groupBy('master_id')->map(function ($group, $masterId) {
            $master = $group->first()->master;
            $name = $master ? trim($master->first_name.' '.($master->last_name ?? '')) : 'Неизвестный мастер';

            return [
                'master_id' => $masterId,
                'master_name' => $name,
                'total' => $group->count(),
                'completed' => $group->where('status', 'completed')->count(),
                'cancelled' => $group->where('status', 'cancelled')->count(),
            ];
        })->values()->sortByDesc('total')->take(10)->values()->toArray();
    }

    private function applyFilters($query, array $filters): void
    {
        if (! empty($filters['service_id'])) {
            $query->where('service_id', $filters['service_id']);
        }
        if (! empty($filters['master_id'])) {
            $query->where('master_id', $filters['master_id']);
        }
        if (! empty($filters['location_id'])) {
            $query->where('location_id', $filters['location_id']);
        }
    }
}
