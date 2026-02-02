<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Business;
use App\Models\Client;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PanelController extends Controller
{
    /**
     * Display the main panel page.
     * Универсальный дашборд, который показывает виджеты в зависимости от прав пользователя.
     */
    public function index()
    {
        $user = Auth::user();
        $dashboardData = $this->collectAllDashboardData($user);

        return view('panel.dashboard', $dashboardData);
    }

    /**
     * Собрать все возможные данные для dashboard
     */
    private function collectAllDashboardData($user)
    {
        $now = Carbon::now();
        $today = $now->toDateString();
        $weekAgo = $now->copy()->subWeek();
        $monthAgo = $now->copy()->subMonth();
        $twoMonthsAgo = $now->copy()->subMonths(2);

        $data = [
            'stats' => [],
            'recentBusinesses' => collect(),
            'recentUsers' => collect(),
            'topBusinesses' => collect(),
            'recentAppointments' => collect(),
            'inactiveBusinesses' => collect(),
            'activeBusinesses' => collect(),
        ];

        // 1. Общие счетчики
        $totalBusinesses = Business::count();
        $totalAppointments = Appointment::count();
        $totalClients = Client::count();
        $totalUsers = User::count();

        // 2. БИЗНЕСЫ (Активность и Рост)
        $data['stats']['total_businesses'] = $totalBusinesses;

        // Получаем ID активных бизнесов (быстро по индексам appointments)
        $activeBizIdsMonth = DB::table('appointments')->where('created_at', '>=', $monthAgo)->distinct()->pluck('business_id');
        $activeBizIdsWeek = DB::table('appointments')->where('created_at', '>=', $weekAgo)->distinct()->pluck('business_id');

        $data['stats']['active_businesses_month'] = $activeBizIdsMonth->count();
        $data['stats']['active_businesses_week'] = $activeBizIdsWeek->count();
        $data['stats']['inactive_businesses'] = max(0, $totalBusinesses - $activeBizIdsMonth->count());

        // Рост бизнесов
        $newBizLastMonth = Business::whereBetween('created_at', [$twoMonthsAgo, $monthAgo])->count();
        $newBizThisMonth = Business::where('created_at', '>=', $monthAgo)->count();
        $data['stats']['new_businesses_month'] = $newBizThisMonth;
        $data['stats']['new_businesses_week'] = Business::where('created_at', '>=', $weekAgo)->count();
        $data['stats']['business_growth_rate'] = $newBizLastMonth > 0
            ? round((($newBizThisMonth - $newBizLastMonth) / $newBizLastMonth) * 100, 1)
            : ($newBizThisMonth > 0 ? 100 : 0);

        // Топ-бизнесы по записям за всё время (для блока «Топ бизнесов по активности»)
        $topStats = DB::table('appointments')
            ->select('business_id', DB::raw('count(*) as total'))
            ->groupBy('business_id')->orderByDesc('total')->limit(5)->get();

        $data['topBusinesses'] = Business::whereIn('id', $topStats->pluck('business_id'))->get()
            ->map(function ($b) use ($topStats) {
                $b->appointments_count = $topStats->firstWhere('business_id', $b->id)->total ?? 0;
                return $b;
            })->sortByDesc('appointments_count');

        // Активные бизнесы за неделю: топ по записям за последние 7 дней (для блока «Активные бизнесы за неделю»)
        $activeWeekStats = DB::table('appointments')
            ->where('created_at', '>=', $weekAgo)
            ->select('business_id', DB::raw('count(*) as total'))
            ->groupBy('business_id')->orderByDesc('total')->limit(5)->get();

        $data['activeBusinesses'] = $activeWeekStats->isEmpty()
            ? collect()
            : Business::whereIn('id', $activeWeekStats->pluck('business_id'))->get()
                ->map(function ($b) use ($activeWeekStats) {
                    $b->appointments_count = $activeWeekStats->firstWhere('business_id', $b->id)->total ?? 0;
                    return $b;
                })->sortByDesc('appointments_count')->values();

        // 3. ПОЛЬЗОВАТЕЛИ (Регистрации и Активность)
        $data['stats']['total_users'] = $totalUsers;
        $data['stats']['active_users_month'] = DB::table('business_user')->whereIn('business_id', $activeBizIdsMonth)->distinct()->count('user_id');
        $data['stats']['active_users_week'] = DB::table('business_user')->whereIn('business_id', $activeBizIdsWeek)->distinct()->count('user_id');

        // Блок новых регистраций пользователей
        $newUsersLastMonth = User::whereBetween('created_at', [$twoMonthsAgo, $monthAgo])->count();
        $newUsersThisMonth = User::where('created_at', '>=', $monthAgo)->count();
        $data['stats']['new_users_month'] = $newUsersThisMonth;
        $data['stats']['new_users_week'] = User::where('created_at', '>=', $weekAgo)->count();
        $data['stats']['user_growth_rate'] = $newUsersLastMonth > 0
            ? round((($newUsersThisMonth - $newUsersLastMonth) / $newUsersLastMonth) * 100, 1)
            : ($newUsersThisMonth > 0 ? 100 : 0);

        $data['recentUsers'] = User::with('roles')->latest()->limit(5)->get();

        // 4. КЛИЕНТЫ
        $data['stats']['total_clients'] = $totalClients;
        $data['stats']['new_clients_week'] = Client::where('created_at', '>=', $weekAgo)->count();
        $newClientsMonth = Client::where('created_at', '>=', $monthAgo)->count();
        $newClientsLastMonth = Client::whereBetween('created_at', [$twoMonthsAgo, $monthAgo])->count();
        $data['stats']['new_clients_month'] = $newClientsMonth;
        $data['stats']['client_growth_rate'] = $newClientsLastMonth > 0
            ? round((($newClientsMonth - $newClientsLastMonth) / $newClientsLastMonth) * 100, 1)
            : ($newClientsMonth > 0 ? 100 : 0);

        // 5. ЗАПИСИ (схлопываем в один запрос; «неделя» = даты от week_ago до сегодня)
        $appStats = Appointment::selectRaw("
            COUNT(CASE WHEN date = ? AND status != 'cancelled' THEN 1 END) as today,
            COUNT(CASE WHEN date >= ? AND date <= ? AND status != 'cancelled' THEN 1 END) as week,
            COUNT(CASE WHEN status = 'pending' THEN 1 END) as pending,
            COUNT(CASE WHEN status = 'cancelled' AND updated_at >= ? THEN 1 END) as cancelled_week
        ", [$today, $weekAgo->toDateString(), $today, $weekAgo])->first();

        $data['stats']['total_appointments'] = $totalAppointments;
        $data['stats']['appointments_today'] = $appStats->today ?? 0;
        $data['stats']['appointments_week'] = $appStats->week ?? 0;
        $data['stats']['appointments_pending'] = $appStats->pending ?? 0;
        $data['stats']['cancelled_week'] = $appStats->cancelled_week ?? 0;
        $data['stats']['appointments_month'] = Appointment::where('created_at', '>=', $monthAgo)->count();

        $data['recentAppointments'] = Appointment::with(['business', 'client', 'service'])->latest()->limit(10)->get();

        // 6. ДОПОЛНИТЕЛЬНЫЕ СПИСКИ
        $data['inactiveBusinesses'] = Business::whereNotIn('id', $activeBizIdsMonth)
            ->withCount(['appointments', 'clients'])
            ->latest()->limit(5)->get();

        $data['recentBusinesses'] = Business::withCount(['appointments', 'clients', 'users'])->latest()->limit(5)->get();

        // Средние метрики
        $data['stats']['avg_appointments_per_business'] = $totalBusinesses > 0 ? round($totalAppointments / $totalBusinesses, 1) : 0;
        $data['stats']['avg_clients_per_business'] = $totalBusinesses > 0 ? round($totalClients / $totalBusinesses, 1) : 0;

        return $data;
    }

    /**
     * Обновление данных дашборда
     */
    public function refresh()
    {
        return redirect()->back()->with('success', 'Данные обновлены');
    }
}