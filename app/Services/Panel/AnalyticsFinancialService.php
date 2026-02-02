<?php

namespace App\Services\Panel;

use App\Models\Invoice;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AnalyticsFinancialService
{
    public function getFinancialData(array $filters): array
    {
        $startDate = Carbon::parse($filters["date_from"])->startOfDay();
        $endDate = Carbon::parse($filters["date_to"])->endOfDay();

        $query = Invoice::whereBetween("created_at", [$startDate, $endDate]);
        if ($filters["plan_id"]) {
            $query->where("plan_id", $filters["plan_id"]);
        }
        if ($filters["status"]) {
            $query->where("status", $filters["status"]);
        }
        $invoices = $query->get();

        $now = Carbon::now();
        $today = $now->copy()->startOfDay();
        $weekAgo = $now->copy()->subWeek();
        $monthAgo = $now->copy()->subMonth();

        $totalRevenue = Invoice::where("status", "paid")->sum("amount");
        $revenueToday = Invoice::where("status", "paid")
            ->whereBetween("paid_at", [$today, $now])
            ->sum("amount");
        $revenueWeek = Invoice::where("status", "paid")
            ->where("paid_at", ">=", $weekAgo)
            ->sum("amount");
        $revenueMonth = Invoice::where("status", "paid")
            ->where("paid_at", ">=", $monthAgo)
            ->sum("amount");

        $avgCheckData = Invoice::where("status", "paid")
            ->selectRaw("SUM(amount) as total, COUNT(*) as count")
            ->first();
        $averageCheck = $avgCheckData && $avgCheckData->count > 0
            ? round($avgCheckData->total / $avgCheckData->count, 2)
            : 0;

        $revenuePeriod = $invoices->where("status", "paid")->sum("amount");

        $revenueByPlan = Invoice::where("status", "paid")
            ->select("plan_id", DB::raw("SUM(amount) as revenue"), DB::raw("COUNT(*) as count"))
            ->groupBy("plan_id")
            ->with("plan")
            ->get()
            ->map(function ($item) {
                return [
                    "plan_id" => $item->plan_id,
                    "plan_name" => $item->plan ? $item->plan->name : "Неизвестный тариф",
                    "revenue" => $item->revenue,
                    "count" => $item->count,
                ];
            })
            ->sortByDesc("revenue")
            ->values();

        $statusStatsPeriod = [
            "paid" => $invoices->where("status", "paid")->count(),
            "pending" => $invoices->where("status", "pending")->count(),
            "failed" => $invoices->where("status", "failed")->count(),
            "cancelled" => $invoices->where("status", "cancelled")->count(),
            "refunded" => $invoices->where("status", "refunded")->count(),
        ];

        $statusStats = [
            "paid" => Invoice::where("status", "paid")->count(),
            "pending" => Invoice::where("status", "pending")->count(),
            "failed" => Invoice::where("status", "failed")->count(),
            "cancelled" => Invoice::where("status", "cancelled")->count(),
            "refunded" => Invoice::where("status", "refunded")->count(),
        ];

        $revenueByPeriod = $this->getRevenueByPeriod($startDate, $endDate, $filters);

        $recentPayments = Invoice::where("status", "paid")
            ->with(["user", "plan"])
            ->orderBy("paid_at", "desc")
            ->limit(10)
            ->get();

        return [
            "total_revenue" => $totalRevenue,
            "revenue_period" => $revenuePeriod,
            "revenue_today" => $revenueToday,
            "revenue_week" => $revenueWeek,
            "revenue_month" => $revenueMonth,
            "average_check" => $averageCheck,
            "revenue_by_plan" => $revenueByPlan,
            "status_stats" => $statusStats,
            "revenue_by_period" => $revenueByPeriod,
            "recent_payments" => $recentPayments,
        ];
    }

    public function getRevenueByPeriod(Carbon $startDate, Carbon $endDate, array $filters): array
    {
        $query = Invoice::where("status", "paid")->whereBetween("paid_at", [$startDate, $endDate]);
        if ($filters["plan_id"]) {
            $query->where("plan_id", $filters["plan_id"]);
        }
        $revenueByDate = $query
            ->selectRaw("DATE(paid_at) as date, SUM(amount) as revenue")
            ->groupBy("date")
            ->pluck("revenue", "date")
            ->toArray();

        $revenueByDay = [];
        $currentDate = $startDate->copy();
        while ($currentDate->lte($endDate)) {
            $dateStr = $currentDate->format("Y-m-d");
            $revenueByDay[] = [
                "date" => $dateStr,
                "label" => $currentDate->format("d.m"),
                "revenue" => $revenueByDate[$dateStr] ?? 0,
            ];
            $currentDate->addDay();
        }
        return $revenueByDay;
    }
}
