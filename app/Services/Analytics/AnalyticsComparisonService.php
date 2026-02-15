<?php

namespace App\Services\Analytics;

use Carbon\Carbon;

class AnalyticsComparisonService
{
    public function getPeriodComparison(
        int $businessId,
        array $filters,
        string $type,
        AnalyticsFinancialService $financialService,
        AnalyticsGeneralService $generalService
    ): array {
        $startDate = Carbon::parse($filters['date_from']);
        $endDate = Carbon::parse($filters['date_to']);
        $daysDiff = $startDate->diffInDays($endDate);

        $previousEndDate = $startDate->copy()->subDay();
        $previousStartDate = $previousEndDate->copy()->subDays($daysDiff);
        $previousFilters = [
            'date_from' => $previousStartDate->format('Y-m-d'),
            'date_to' => $previousEndDate->format('Y-m-d'),
            'service_id' => $filters['service_id'] ?? null,
            'master_id' => $filters['master_id'] ?? null,
            'location_id' => $filters['location_id'] ?? null,
        ];

        if ($type === 'financial') {
            $current = $financialService->getFinancialData($businessId, $filters);
            $previous = $financialService->getFinancialData($businessId, $previousFilters);

            return [
                'revenue_change' => $current['total_revenue'] - $previous['total_revenue'],
                'revenue_change_percent' => $this->percentChange($previous['total_revenue'], $current['total_revenue']),
                'appointments_change' => $current['completed_count'] - $previous['completed_count'],
                'appointments_change_percent' => $this->percentChange($previous['completed_count'], $current['completed_count']),
                'average_check_change' => $current['average_check'] - $previous['average_check'],
                'average_check_change_percent' => $this->percentChange($previous['average_check'], $current['average_check']),
                'previous_period' => [
                    'total_revenue' => $previous['total_revenue'],
                    'completed_count' => $previous['completed_count'],
                    'average_check' => $previous['average_check'],
                ],
            ];
        }

        $current = $generalService->getGeneralData($businessId, $filters);
        $previous = $generalService->getGeneralData($businessId, $previousFilters);

        return [
            'total_change' => $current['total'] - $previous['total'],
            'total_change_percent' => $this->percentChange($previous['total'], $current['total']),
            'completed_change' => $current['stats_by_status']['completed'] - $previous['stats_by_status']['completed'],
            'completed_change_percent' => $this->percentChange(
                $previous['stats_by_status']['completed'],
                $current['stats_by_status']['completed']
            ),
            'conversion_change' => $current['conversion_rate'] - $previous['conversion_rate'],
            'cancellation_change' => $current['cancellation_rate'] - $previous['cancellation_rate'],
            'previous_period' => [
                'total' => $previous['total'],
                'completed' => $previous['stats_by_status']['completed'],
                'conversion_rate' => $previous['conversion_rate'],
                'cancellation_rate' => $previous['cancellation_rate'],
            ],
        ];
    }

    private function percentChange(float $previous, float $current): float
    {
        if ($previous == 0) {
            return $current > 0 ? 100.0 : 0.0;
        }

        return round((($current - $previous) / $previous) * 100, 1);
    }
}
