<?php

namespace App\Services\Analytics;

use App\Models\Appointment;
use Carbon\Carbon;

class AnalyticsKpiService
{
    public function getKpiData(int $businessId): array
    {
        $now = Carbon::now();
        $last30Days = $now->copy()->subDays(30);
        $last90Days = $now->copy()->subDays(90);

        $completedAppointments = Appointment::where('business_id', $businessId)
            ->where('status', 'completed')
            ->where('date', '>=', $last90Days->format('Y-m-d'))
            ->with(['client', 'service'])
            ->get();

        $uniqueClients = $completedAppointments->pluck('client_id')->unique();
        $totalClients = $uniqueClients->count();

        $totalRevenue = $completedAppointments->sum(fn ($a) => $a->price ?? $a->service?->price ?? 0);
        $arpu = $totalClients > 0 ? round($totalRevenue / $totalClients, 2) : 0;

        $returningClients = $uniqueClients->filter(
            fn ($clientId) => $completedAppointments->where('client_id', $clientId)->count() > 1
        )->count();
        $retentionRate = $totalClients > 0 ? round(($returningClients / $totalClients) * 100, 1) : 0;

        $threeMonthsAgo = $now->copy()->subMonths(3)->startOfMonth();
        $allMonthlyAppointments = Appointment::where('business_id', $businessId)
            ->where('status', 'completed')
            ->where('date', '>=', $threeMonthsAgo->format('Y-m-d'))
            ->where('date', '<', $now->copy()->startOfMonth()->format('Y-m-d'))
            ->with('service')
            ->get();

        $monthlyRevenues = [];
        $groupedByMonth = $allMonthlyAppointments->groupBy(fn ($a) => $a->date->format('Y-m'));
        for ($i = 0; $i < 3; $i++) {
            $monthKey = $now->copy()->subMonths($i + 1)->format('Y-m');
            $monthAppointments = $groupedByMonth->get($monthKey, collect());
            $monthRevenue = $monthAppointments->sum(fn ($a) => $a->price ?? $a->service?->price ?? 0);
            if ($monthRevenue > 0) {
                $monthlyRevenues[] = $monthRevenue;
            }
        }
        $revenueForecast = count($monthlyRevenues) > 0 ? round(array_sum($monthlyRevenues) / count($monthlyRevenues), 0) : 0;

        $revenueLast30Days = $completedAppointments
            ->filter(fn ($a) => $a->date->format('Y-m-d') >= $last30Days->format('Y-m-d'))
            ->sum(fn ($a) => $a->price ?? $a->service?->price ?? 0);

        return [
            'retention_rate' => $retentionRate,
            'arpu' => $arpu,
            'revenue_forecast' => $revenueForecast,
            'revenue_last_30_days' => $revenueLast30Days,
            'total_clients' => $totalClients,
            'returning_clients' => $returningClients,
        ];
    }

    /**
     * Данные для мини-графиков на главной аналитики
     */
    public function getDashboardChartData(int $businessId): array
    {
        $now = Carbon::now();
        $last7Days = $now->copy()->subDays(6);
        $last30Days = $now->copy()->subDays(30);

        $completedLast7 = Appointment::where('business_id', $businessId)
            ->where('status', 'completed')
            ->where('date', '>=', $last7Days->format('Y-m-d'))
            ->with('service')
            ->get();

        $revenueByDay = [];
        for ($i = 6; $i >= 0; $i--) {
            $day = $now->copy()->subDays($i);
            $dateStr = $day->format('Y-m-d');
            $dayRevenue = $completedLast7
                ->filter(fn ($a) => $a->date->format('Y-m-d') === $dateStr)
                ->sum(fn ($a) => $a->price ?? $a->service?->price ?? 0);
            $revenueByDay[] = [
                'label' => $day->locale('ru')->translatedFormat('D'),
                'revenue' => $dayRevenue,
            ];
        }

        $appointments30 = Appointment::where('business_id', $businessId)
            ->where('date', '>=', $last30Days->format('Y-m-d'))
            ->get();

        $statusCounts = [
            'pending' => $appointments30->where('status', 'pending')->count(),
            'confirmed' => $appointments30->where('status', 'confirmed')->count(),
            'completed' => $appointments30->where('status', 'completed')->count(),
            'cancelled' => $appointments30->where('status', 'cancelled')->count(),
        ];

        return [
            'revenue_last_7_days' => $revenueByDay,
            'status_counts_30_days' => $statusCounts,
        ];
    }
}
