<?php

namespace App\Services\Panel;

use App\Models\Subscription;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AnalyticsSubscriptionsService
{
    public function getSubscriptionsData(array $filters): array
    {
        $startDate = Carbon::parse($filters['date_from'])->startOfDay();
        $endDate = Carbon::parse($filters['date_to'])->endOfDay();

        $query = Subscription::whereBetween('created_at', [$startDate, $endDate]);
        if ($filters['plan_id']) {
            $query->where('plan_id', $filters['plan_id']);
        }
        if ($filters['status']) {
            $query->where('status', $filters['status']);
        }

        $activeSubscriptions = Subscription::where('status', 'active')->count();
        $trialSubscriptions = Subscription::where('status', 'trial')->count();

        $cancelledSubscriptions = Subscription::whereNotNull('cancelled_at')->count();

        $distributionByPlan = Subscription::select('plan_id', DB::raw('COUNT(*) as count'))
            ->groupBy('plan_id')
            ->with('plan')
            ->get()
            ->map(function ($item) {
                return [
                    'plan_id' => $item->plan_id,
                    'plan_name' => $item->plan ? $item->plan->name : 'Неизвестный тариф',
                    'count' => $item->count,
                ];
            })
            ->sortByDesc('count')
            ->values();

        $statusStats = [
            'active' => Subscription::where('status', 'active')->count(),
            'trial' => Subscription::where('status', 'trial')->count(),
            'cancelled' => Subscription::where('status', 'cancelled')->count(),
            'expired' => Subscription::where('status', 'expired')->count(),
        ];

        $trialToPaid = Subscription::where('status', 'active')
            ->whereNotNull('trial_ends_at')
            ->where('trial_ends_at', '<', now())
            ->count();
        $totalTrials = Subscription::where('status', 'trial')->count() + $trialToPaid;
        $conversionRate = $totalTrials > 0 ? round(($trialToPaid / $totalTrials) * 100, 1) : 0;

        $subscriptionsByPeriod = $this->getSubscriptionsByPeriod($startDate, $endDate, $filters);

        $newSubscriptions = Subscription::whereBetween('created_at', [$startDate, $endDate])
            ->with(['user', 'plan'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $cancelledSubscriptionsList = Subscription::whereNotNull('cancelled_at')
            ->whereBetween('cancelled_at', [$startDate, $endDate])
            ->with(['user', 'plan'])
            ->orderBy('cancelled_at', 'desc')
            ->limit(10)
            ->get();

        return [
            'active_subscriptions' => $activeSubscriptions,
            'trial_subscriptions' => $trialSubscriptions,
            'cancelled_subscriptions' => $cancelledSubscriptions,
            'distribution_by_plan' => $distributionByPlan,
            'status_stats' => $statusStats,
            'conversion_rate' => $conversionRate,
            'subscriptions_by_period' => $subscriptionsByPeriod,
            'new_subscriptions' => $newSubscriptions,
            'cancelled_subscriptions_list' => $cancelledSubscriptionsList,
        ];
    }

    public function getSubscriptionsByPeriod(Carbon $startDate, Carbon $endDate, array $filters): array
    {
        $query = Subscription::whereBetween('created_at', [$startDate, $endDate]);
        if ($filters['plan_id']) {
            $query->where('plan_id', $filters['plan_id']);
        }
        if ($filters['status']) {
            $query->where('status', $filters['status']);
        }
        $subscriptions = $query->get();
        $groupedByDate = $subscriptions->groupBy(fn ($s) => $s->created_at->format('Y-m-d'));

        $subscriptionsByDay = [];
        $currentDate = $startDate->copy();
        while ($currentDate->lte($endDate)) {
            $dateStr = $currentDate->format('Y-m-d');
            $daySubscriptions = $groupedByDate->get($dateStr, collect());
            $subscriptionsByDay[] = [
                'date' => $dateStr,
                'label' => $currentDate->format('d.m'),
                'total' => $daySubscriptions->count(),
                'active' => $daySubscriptions->where('status', 'active')->count(),
                'trial' => $daySubscriptions->where('status', 'trial')->count(),
            ];
            $currentDate->addDay();
        }

        return $subscriptionsByDay;
    }
}
