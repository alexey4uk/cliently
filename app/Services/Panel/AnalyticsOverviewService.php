<?php

namespace App\Services\Panel;

use App\Models\Appointment;
use App\Models\Business;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AnalyticsOverviewService
{
    public function getOverviewData(): array
    {
        $today = Carbon::today();
        $monthAgo = Carbon::now()->subMonth();
        $twoMonthsAgo = Carbon::now()->subMonths(2);

        $totalBusinesses = Business::count();
        $totalUsers = User::count();

        try {
            $clientsInfo = DB::selectOne(
                "SELECT table_rows FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = 'clients'",
            );
            $totalClients =
                $clientsInfo && isset($clientsInfo->table_rows)
                    ? (int) $clientsInfo->table_rows
                    : Client::count();
        } catch (\Exception $e) {
            $totalClients = Client::count();
        }

        try {
            $appointmentsInfo = DB::selectOne(
                "SELECT table_rows FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = 'appointments'",
            );
            $totalAppointments =
                $appointmentsInfo && isset($appointmentsInfo->table_rows)
                    ? (int) $appointmentsInfo->table_rows
                    : Appointment::count();
        } catch (\Exception $e) {
            $totalAppointments = Appointment::count();
        }

        $activeBusinesses = DB::table('businesses')
            ->join('appointments', 'businesses.id', '=', 'appointments.business_id')
            ->where('appointments.created_at', '>=', $monthAgo)
            ->count(DB::raw('DISTINCT businesses.id'));

        $activeUsers = DB::table('users')
            ->join('business_user', 'users.id', '=', 'business_user.user_id')
            ->join('appointments', 'business_user.business_id', '=', 'appointments.business_id')
            ->where('appointments.created_at', '>=', $monthAgo)
            ->count(DB::raw('DISTINCT users.id'));

        $revenueTotal = Invoice::where('status', 'paid')->sum('amount');
        $revenueMonth = Invoice::where('status', 'paid')
            ->where('paid_at', '>=', $monthAgo)
            ->sum('amount');

        $activeSubscriptions = \App\Models\Subscription::where('status', 'active')
            ->where(function ($query) {
                $query->whereNull('ends_at')->orWhere('ends_at', '>', now());
            })
            ->count();

        $newBusinessesLastMonth = Business::whereBetween('created_at', [$twoMonthsAgo, $monthAgo])->count();
        $newBusinessesThisMonth = Business::where('created_at', '>=', $monthAgo)->count();
        $businessGrowthRate = $newBusinessesLastMonth > 0
            ? round((($newBusinessesThisMonth - $newBusinessesLastMonth) / $newBusinessesLastMonth) * 100, 1)
            : ($newBusinessesThisMonth > 0 ? 100 : 0);

        $newUsersLastMonth = User::whereBetween('created_at', [$twoMonthsAgo, $monthAgo])->count();
        $newUsersThisMonth = User::where('created_at', '>=', $monthAgo)->count();
        $userGrowthRate = $newUsersLastMonth > 0
            ? round((($newUsersThisMonth - $newUsersLastMonth) / $newUsersLastMonth) * 100, 1)
            : ($newUsersThisMonth > 0 ? 100 : 0);

        $avgAppointmentsPerBusiness = $totalBusinesses > 0
            ? round($totalAppointments / $totalBusinesses, 1)
            : 0;
        $avgClientsPerBusiness = $totalBusinesses > 0
            ? round($totalClients / $totalBusinesses, 1)
            : 0;

        $chartData = $this->getChartData(30);

        $topBusinessesByAppointments = Business::query()
            ->selectRaw(
                'businesses.*,
                (SELECT COUNT(*) FROM appointments WHERE appointments.business_id = businesses.id) as appointments_count',
            )
            ->orderByRaw('appointments_count DESC')
            ->limit(5)
            ->get()
            ->map(function ($business) {
                return [
                    'id' => $business->id,
                    'name' => $business->name,
                    'count' => $business->appointments_count,
                ];
            });

        $topBusinessesByClients = Business::query()
            ->selectRaw(
                'businesses.*,
                (SELECT COUNT(*) FROM clients WHERE clients.business_id = businesses.id) as clients_count',
            )
            ->orderByRaw('clients_count DESC')
            ->limit(5)
            ->get()
            ->map(function ($business) {
                return [
                    'id' => $business->id,
                    'name' => $business->name,
                    'count' => $business->clients_count,
                ];
            });

        $recentRegistrations = User::orderBy('created_at', 'desc')
            ->limit(10)
            ->get(['id', 'name', 'email', 'created_at']);

        return [
            'total_businesses' => $totalBusinesses,
            'active_businesses' => $activeBusinesses,
            'total_users' => $totalUsers,
            'active_users' => $activeUsers,
            'total_clients' => $totalClients,
            'total_appointments' => $totalAppointments,
            'revenue_total' => $revenueTotal,
            'revenue_month' => $revenueMonth,
            'active_subscriptions' => $activeSubscriptions,
            'business_growth_rate' => $businessGrowthRate,
            'user_growth_rate' => $userGrowthRate,
            'avg_appointments_per_business' => $avgAppointmentsPerBusiness,
            'avg_clients_per_business' => $avgClientsPerBusiness,
            'chart_data' => $chartData,
            'top_businesses_by_appointments' => $topBusinessesByAppointments,
            'top_businesses_by_clients' => $topBusinessesByClients,
            'recent_registrations' => $recentRegistrations,
        ];
    }

    public function getChartData(int $days = 30): array
    {
        $startDate = Carbon::now()->subDays($days)->startOfDay();
        $endDate = Carbon::now()->endOfDay();

        $businessesByDate = Business::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy(DB::raw('DATE(created_at)'))
            ->pluck('count', 'date')
            ->toArray();

        $usersByDate = User::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy(DB::raw('DATE(created_at)'))
            ->pluck('count', 'date')
            ->toArray();

        $appointmentsByDate = Appointment::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy(DB::raw('DATE(created_at)'))
            ->pluck('count', 'date')
            ->toArray();

        $clientsByDate = Client::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy(DB::raw('DATE(created_at)'))
            ->pluck('count', 'date')
            ->toArray();

        $revenueByDate = Invoice::where('status', 'paid')
            ->whereBetween('paid_at', [$startDate, $endDate])
            ->selectRaw('DATE(paid_at) as date, SUM(amount) as total')
            ->groupBy(DB::raw('DATE(paid_at)'))
            ->pluck('total', 'date')
            ->toArray();

        $businessesData = [];
        $usersData = [];
        $appointmentsData = [];
        $clientsData = [];
        $revenueData = [];
        $labels = [];

        for ($i = $days; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $dateStr = $date->format('Y-m-d');
            $labels[] = $date->format('d.m');
            $businessesData[] = $businessesByDate[$dateStr] ?? 0;
            $usersData[] = $usersByDate[$dateStr] ?? 0;
            $appointmentsData[] = $appointmentsByDate[$dateStr] ?? 0;
            $clientsData[] = $clientsByDate[$dateStr] ?? 0;
            $revenueData[] = $revenueByDate[$dateStr] ?? 0;
        }

        return [
            'labels' => $labels,
            'businesses' => $businessesData,
            'users' => $usersData,
            'appointments' => $appointmentsData,
            'clients' => $clientsData,
            'revenue' => $revenueData,
        ];
    }
}
