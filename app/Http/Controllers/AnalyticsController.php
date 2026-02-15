<?php

namespace App\Http\Controllers;

use App\Services\Analytics\AnalyticsClientsService;
use App\Services\Analytics\AnalyticsComparisonService;
use App\Services\Analytics\AnalyticsFinancialService;
use App\Services\Analytics\AnalyticsGeneralService;
use App\Services\Analytics\AnalyticsKpiService;
use App\Services\SubscriptionAccessService;
use App\Services\SubscriptionService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AnalyticsController extends Controller
{
    public function __construct(
        protected AnalyticsKpiService $kpiService,
        protected AnalyticsFinancialService $financialService,
        protected AnalyticsGeneralService $generalService,
        protected AnalyticsClientsService $clientsService,
        protected AnalyticsComparisonService $comparisonService,
    ) {}

    public function index()
    {
        $business = $this->getCurrentBusiness();

        if (! $business) {
            return view('analytics.index', [
                'business' => null,
                'kpiData' => $this->emptyKpiData(),
                'hasAdvancedAnalytics' => false,
                'chartData' => null,
            ]);
        }

        $accessService = app(SubscriptionAccessService::class);
        $redirect = $accessService->checkAccessWithRedirect(
            $business,
            'analytics_enabled',
            'client.analytics.view',
            'Аналитика',
            'subscription.index'
        );
        if ($redirect) {
            return $redirect;
        }

        $kpiData = $this->kpiService->getKpiData($business->id);
        $hasAdvancedAnalytics = $this->hasAdvancedAnalytics($business);
        $chartData = $hasAdvancedAnalytics ? $this->kpiService->getDashboardChartData($business->id) : null;

        return view('analytics.index', [
            'business' => $business,
            'kpiData' => $kpiData,
            'hasAdvancedAnalytics' => $hasAdvancedAnalytics,
            'chartData' => $chartData,
        ]);
    }

    public function financial(Request $request)
    {
        $business = $this->getCurrentBusiness();
        if (! $business) {
            return $this->financialEmptyView($request);
        }

        $accessService = app(SubscriptionAccessService::class);
        $redirect = $accessService->checkAccessWithRedirect(
            $business,
            'analytics_enabled',
            'client.analytics.view',
            'Аналитика',
            'subscription.index'
        );
        if ($redirect) {
            return $redirect;
        }

        $hasAdvancedAnalytics = $this->hasAdvancedAnalytics($business);
        $filters = $this->getFilters($request);
        $data = $this->financialService->getFinancialData($business->id, $filters);
        $comparison = $hasAdvancedAnalytics
            ? $this->comparisonService->getPeriodComparison(
                $business->id,
                $filters,
                'financial',
                $this->financialService,
                $this->generalService
            )
            : null;

        return view('analytics.financial', [
            'business' => $business,
            'data' => $data,
            'filters' => $filters,
            'hasAdvancedAnalytics' => $hasAdvancedAnalytics,
            'comparison' => $comparison,
            'filterPresets' => $this->filterPresets(),
        ]);
    }

    public function general(Request $request)
    {
        $business = $this->getCurrentBusiness();
        if (! $business) {
            return $this->generalEmptyView($request);
        }

        $accessService = app(SubscriptionAccessService::class);
        $redirect = $accessService->checkAccessWithRedirect(
            $business,
            'analytics_enabled',
            'client.analytics.view',
            'Аналитика',
            'subscription.index'
        );
        if ($redirect) {
            return $redirect;
        }

        $hasAdvancedAnalytics = $this->hasAdvancedAnalytics($business);
        $filters = $this->getFilters($request);
        $data = $this->generalService->getGeneralData($business->id, $filters);
        $comparison = $hasAdvancedAnalytics
            ? $this->comparisonService->getPeriodComparison(
                $business->id,
                $filters,
                'general',
                $this->financialService,
                $this->generalService
            )
            : null;
        $timeAnalytics = $hasAdvancedAnalytics ? $this->generalService->getTimeAnalytics($business->id, $filters) : null;

        return view('analytics.general', [
            'business' => $business,
            'data' => $data,
            'filters' => $filters,
            'hasAdvancedAnalytics' => $hasAdvancedAnalytics,
            'comparison' => $comparison,
            'timeAnalytics' => $timeAnalytics,
            'filterPresets' => $this->filterPresets(),
        ]);
    }

    public function clients(Request $request)
    {
        $business = $this->getCurrentBusiness();
        if (! $business) {
            return $this->clientsEmptyView($request);
        }

        $accessService = app(SubscriptionAccessService::class);
        $redirect = $accessService->checkAccessWithRedirect(
            $business,
            'analytics_enabled',
            'client.analytics.view',
            'Аналитика',
            'subscription.index'
        );
        if ($redirect) {
            return $redirect;
        }

        $filters = $this->getFilters($request);
        $data = $this->clientsService->getClientsAnalyticsData($business->id, $filters);
        $hasAdvancedAnalytics = $this->hasAdvancedAnalytics($business);

        return view('analytics.clients', [
            'business' => $business,
            'data' => $data,
            'filters' => $filters,
            'hasAdvancedAnalytics' => $hasAdvancedAnalytics,
            'filterPresets' => $this->filterPresets(),
        ]);
    }

    /**
     * Экспорт финансовой аналитики в CSV
     */
    public function exportFinancial(Request $request): StreamedResponse
    {
        $business = $this->getCurrentBusiness();
        if (! $business) {
            abort(404);
        }
        $accessService = app(SubscriptionAccessService::class);
        if ($accessService->checkAccessWithRedirect(
            $business,
            'analytics_enabled',
            'client.analytics.view',
            'Аналитика',
            'subscription.index'
        )) {
            abort(403);
        }

        $filters = $this->getFilters($request);
        $data = $this->financialService->getFinancialData($business->id, $filters);

        $filename = 'financial_analytics_'.Carbon::now()->format('Y-m-d_His').'.csv';

        return response()->streamDownload(function () use ($data) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Услуга', 'Количество', 'Выручка (BYN)'], ';');
            foreach ($data['revenue_by_service'] as $row) {
                fputcsv($out, [$row['service_name'], $row['count'], $row['revenue']], ';');
            }
            fputcsv($out, [], ';');
            fputcsv($out, ['Мастер', 'Количество', 'Выручка (BYN)'], ';');
            foreach ($data['revenue_by_master'] as $row) {
                fputcsv($out, [$row['master_name'], $row['count'], $row['revenue']], ';');
            }
            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }

    /**
     * Экспорт аналитики клиентов (топ по LTV) в CSV
     */
    public function exportClients(Request $request): StreamedResponse
    {
        $business = $this->getCurrentBusiness();
        if (! $business) {
            abort(404);
        }
        $accessService = app(SubscriptionAccessService::class);
        if ($accessService->checkAccessWithRedirect(
            $business,
            'analytics_enabled',
            'client.analytics.view',
            'Аналитика',
            'subscription.index'
        )) {
            abort(403);
        }

        $filters = $this->getFilters($request);
        $data = $this->clientsService->getClientsAnalyticsData($business->id, $filters);

        $filename = 'clients_analytics_'.Carbon::now()->format('Y-m-d_His').'.csv';

        return response()->streamDownload(function () use ($data) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Клиент', 'LTV (BYN)', 'Записей'], ';');
            foreach ($data['top_clients'] as $row) {
                fputcsv($out, [$row['client_name'], $row['ltv'], $row['appointments_count']], ';');
            }
            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }

    private function getFilters(Request $request): array
    {
        $preset = $request->get('preset');
        $now = Carbon::now();

        if ($preset === '7d') {
            $dateFrom = $now->copy()->subDays(6)->format('Y-m-d');
            $dateTo = $now->format('Y-m-d');
        } elseif ($preset === '30d') {
            $dateFrom = $now->copy()->subDays(29)->format('Y-m-d');
            $dateTo = $now->format('Y-m-d');
        } elseif ($preset === 'month') {
            $dateFrom = $now->copy()->startOfMonth()->format('Y-m-d');
            $dateTo = $now->format('Y-m-d');
        } elseif ($preset === 'last_month') {
            $last = $now->copy()->subMonth();
            $dateFrom = $last->copy()->startOfMonth()->format('Y-m-d');
            $dateTo = $last->copy()->endOfMonth()->format('Y-m-d');
        } else {
            $dateFrom = $request->get('date_from', $now->copy()->subDays(30)->format('Y-m-d'));
            $dateTo = $request->get('date_to', $now->format('Y-m-d'));
        }

        return [
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
            'service_id' => $request->get('service_id'),
            'master_id' => $request->get('master_id'),
            'location_id' => $request->get('location_id'),
        ];
    }

    private function filterPresets(): array
    {
        $now = Carbon::now();

        return [
            ['key' => '7d', 'label' => '7 дней', 'date_from' => $now->copy()->subDays(6)->format('Y-m-d'), 'date_to' => $now->format('Y-m-d')],
            ['key' => '30d', 'label' => '30 дней', 'date_from' => $now->copy()->subDays(29)->format('Y-m-d'), 'date_to' => $now->format('Y-m-d')],
            ['key' => 'month', 'label' => 'Этот месяц', 'date_from' => $now->copy()->startOfMonth()->format('Y-m-d'), 'date_to' => $now->format('Y-m-d')],
            ['key' => 'last_month', 'label' => 'Прошлый месяц', 'date_from' => $now->copy()->subMonth()->startOfMonth()->format('Y-m-d'), 'date_to' => $now->copy()->subMonth()->endOfMonth()->format('Y-m-d')],
        ];
    }

    private function hasAdvancedAnalytics(\App\Models\Business $business): bool
    {
        $subscriptionService = app(SubscriptionService::class);
        $owner = $this->getBusinessOwner($business);
        if (! $owner) {
            return false;
        }

        return $subscriptionService->getLimit($owner, 'advanced_analytics_enabled') === true;
    }

    private function getBusinessOwner(\App\Models\Business $business): ?\App\Models\User
    {
        $ownerRole = \App\Models\BusinessRole::where('slug', 'owner')->first();
        if (! $ownerRole) {
            return null;
        }
        $ownerPivot = \Illuminate\Support\Facades\DB::table('business_user')
            ->where('business_id', $business->id)
            ->where('role_id', $ownerRole->id)
            ->first();
        if (! $ownerPivot) {
            return null;
        }

        return \App\Models\User::find($ownerPivot->user_id);
    }

    private function emptyKpiData(): array
    {
        return [
            'retention_rate' => 0,
            'arpu' => 0,
            'revenue_forecast' => 0,
            'revenue_last_30_days' => 0,
            'total_clients' => 0,
            'returning_clients' => 0,
        ];
    }

    private function financialEmptyView(Request $request)
    {
        $filters = $this->getFilters($request);

        return view('analytics.financial', [
            'business' => null,
            'data' => [
                'total_revenue' => 0,
                'completed_count' => 0,
                'average_check' => 0,
                'revenue_by_period' => [],
                'revenue_by_service' => [],
                'revenue_by_master' => [],
                'revenue_by_location' => [],
                'revenue_by_day_of_week' => [],
            ],
            'filters' => $filters,
            'hasAdvancedAnalytics' => false,
            'comparison' => null,
            'filterPresets' => $this->filterPresets(),
        ]);
    }

    private function generalEmptyView(Request $request)
    {
        $filters = $this->getFilters($request);

        return view('analytics.general', [
            'business' => null,
            'data' => [
                'total' => 0,
                'stats_by_status' => ['pending' => 0, 'confirmed' => 0, 'completed' => 0, 'cancelled' => 0],
                'conversion_rate' => 0,
                'cancellation_rate' => 0,
                'stats_by_period' => [],
                'stats_by_service' => [],
                'stats_by_master' => [],
                'stats_by_source' => [],
            ],
            'filters' => $filters,
            'hasAdvancedAnalytics' => false,
            'comparison' => null,
            'timeAnalytics' => null,
            'filterPresets' => $this->filterPresets(),
        ]);
    }

    private function clientsEmptyView(Request $request)
    {
        $filters = $this->getFilters($request);

        return view('analytics.clients', [
            'business' => null,
            'data' => [
                'new_clients' => 0,
                'returning_clients' => 0,
                'total_clients' => 0,
                'average_ltv' => 0,
                'top_clients' => [],
                'visit_frequency' => 0,
                'new_clients_by_period' => [],
            ],
            'filters' => $filters,
            'hasAdvancedAnalytics' => false,
            'filterPresets' => $this->filterPresets(),
        ]);
    }
}
