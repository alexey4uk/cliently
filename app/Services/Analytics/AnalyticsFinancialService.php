<?php

namespace App\Services\Analytics;

use App\Models\Appointment;
use Carbon\Carbon;

class AnalyticsFinancialService
{
    public function getFinancialData(int $businessId, array $filters): array
    {
        $startDate = Carbon::parse($filters['date_from'])->startOfDay();
        $endDate = Carbon::parse($filters['date_to'])->endOfDay();

        $query = Appointment::where('business_id', $businessId)
            ->where('status', 'completed')
            ->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')]);

        $this->applyFilters($query, $filters);
        $appointments = $query->with('service')->get();

        $totalRevenue = $appointments->sum(fn ($a) => $a->price ?? $a->service?->price ?? 0);
        $completedCount = $appointments->count();
        $averageCheck = $completedCount > 0 ? round($totalRevenue / $completedCount, 2) : 0;

        return [
            'total_revenue' => $totalRevenue,
            'completed_count' => $completedCount,
            'average_check' => $averageCheck,
            'revenue_by_period' => $this->getRevenueByPeriod($businessId, $startDate, $endDate, $filters),
            'revenue_by_service' => $this->getRevenueByService($businessId, $filters),
            'revenue_by_master' => $this->getRevenueByMaster($businessId, $filters),
            'revenue_by_location' => $this->getRevenueByLocation($businessId, $filters),
            'revenue_by_day_of_week' => $this->getRevenueByDayOfWeek($businessId, $filters),
        ];
    }

    public function getRevenueByPeriod(int $businessId, Carbon $startDate, Carbon $endDate, array $filters): array
    {
        $query = Appointment::where('business_id', $businessId)
            ->where('status', 'completed')
            ->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')]);
        $this->applyFilters($query, $filters);
        $appointments = $query->with('service')->get();

        $groupedByDate = $appointments->groupBy(fn ($a) => $a->date->format('Y-m-d'));
        $revenueByDay = [];
        $currentDate = $startDate->copy();

        while ($currentDate->lte($endDate)) {
            $dateStr = $currentDate->format('Y-m-d');
            $dayAppointments = $groupedByDate->get($dateStr, collect());
            $revenueByDay[] = [
                'date' => $dateStr,
                'label' => $currentDate->format('d.m'),
                'revenue' => $dayAppointments->sum(fn ($a) => $a->price ?? $a->service?->price ?? 0),
                'count' => $dayAppointments->count(),
            ];
            $currentDate->addDay();
        }

        return $revenueByDay;
    }

    public function getRevenueByService(int $businessId, array $filters): array
    {
        $query = Appointment::where('business_id', $businessId)
            ->where('status', 'completed')
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
                'revenue' => $group->sum(fn ($a) => $a->price ?? $a->service?->price ?? 0),
                'count' => $group->count(),
            ];
        })->values()->sortByDesc('revenue')->take(10)->values()->toArray();
    }

    public function getRevenueByMaster(int $businessId, array $filters): array
    {
        $query = Appointment::where('business_id', $businessId)
            ->where('status', 'completed')
            ->whereBetween('date', [
                Carbon::parse($filters['date_from'])->format('Y-m-d'),
                Carbon::parse($filters['date_to'])->format('Y-m-d'),
            ]);
        $this->applyFilters($query, $filters);
        $appointments = $query->with(['master', 'service'])->get();

        return $appointments->groupBy('master_id')->map(function ($group, $masterId) {
            $master = $group->first()->master;
            $name = $master ? trim($master->first_name.' '.($master->last_name ?? '')) : 'Неизвестный мастер';

            return [
                'master_id' => $masterId,
                'master_name' => $name,
                'revenue' => $group->sum(fn ($a) => $a->price ?? $a->service?->price ?? 0),
                'count' => $group->count(),
            ];
        })->values()->sortByDesc('revenue')->take(10)->values()->toArray();
    }

    public function getRevenueByLocation(int $businessId, array $filters): array
    {
        $query = Appointment::where('business_id', $businessId)
            ->where('status', 'completed')
            ->whereBetween('date', [
                Carbon::parse($filters['date_from'])->format('Y-m-d'),
                Carbon::parse($filters['date_to'])->format('Y-m-d'),
            ]);
        $this->applyFilters($query, $filters);
        $appointments = $query->with(['location', 'service'])->get();

        return $appointments->groupBy('location_id')->map(function ($group, $locationId) {
            $location = $group->first()->location;

            return [
                'location_id' => $locationId,
                'location_name' => $location ? $location->name : 'Неизвестная локация',
                'revenue' => $group->sum(fn ($a) => $a->price ?? $a->service?->price ?? 0),
                'count' => $group->count(),
            ];
        })->values()->sortByDesc('revenue')->take(10)->values()->toArray();
    }

    /**
     * Выручка по дням недели (Пн–Вс)
     */
    public function getRevenueByDayOfWeek(int $businessId, array $filters): array
    {
        $query = Appointment::where('business_id', $businessId)
            ->where('status', 'completed')
            ->whereBetween('date', [
                Carbon::parse($filters['date_from'])->format('Y-m-d'),
                Carbon::parse($filters['date_to'])->format('Y-m-d'),
            ]);
        $this->applyFilters($query, $filters);
        $appointments = $query->with('service')->get();

        $days = ['Понедельник', 'Вторник', 'Среда', 'Четверг', 'Пятница', 'Суббота', 'Воскресенье'];
        $byDay = array_fill(0, 7, ['day' => '', 'revenue' => 0, 'count' => 0]);
        foreach ($days as $i => $label) {
            $byDay[$i]['day'] = $label;
        }

        foreach ($appointments as $a) {
            $dow = $a->date->dayOfWeek;
            $idx = $dow === 0 ? 6 : $dow - 1;
            $byDay[$idx]['revenue'] += $a->price ?? $a->service?->price ?? 0;
            $byDay[$idx]['count']++;
        }

        return $byDay;
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
