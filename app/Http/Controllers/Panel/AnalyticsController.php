<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Services\Panel\AnalyticsFinancialService;
use App\Services\Panel\AnalyticsGeneralService;
use App\Services\Panel\AnalyticsOverviewService;
use App\Services\Panel\AnalyticsSubscriptionsService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AnalyticsController extends Controller
{
    public function __construct(
        protected AnalyticsOverviewService $overviewService,
        protected AnalyticsFinancialService $financialService,
        protected AnalyticsGeneralService $generalService,
        protected AnalyticsSubscriptionsService $subscriptionsService,
    ) {}

    public function index()
    {
        $this->authorize('panel.analytics.view');
        $data = $this->overviewService->getOverviewData();

        return view('panel.analytics.index', compact('data'));
    }

    public function financial(Request $request)
    {
        $this->authorize('panel.analytics.financial');
        $filters = $this->getFilters($request);
        $data = $this->financialService->getFinancialData($filters);
        $plans = Plan::getActiveCached();

        return view('panel.analytics.financial', compact('data', 'filters', 'plans'));
    }

    public function general(Request $request)
    {
        $this->authorize('panel.analytics.general');
        $filters = $this->getFilters($request);
        $data = $this->generalService->getGeneralData($filters);

        return view('panel.analytics.general', compact('data', 'filters'));
    }

    public function subscriptions(Request $request)
    {
        $this->authorize('panel.analytics.subscriptions');
        $filters = $this->getFilters($request);
        $data = $this->subscriptionsService->getSubscriptionsData($filters);
        $plans = Plan::getActiveCached();

        return view('panel.analytics.subscriptions', compact('data', 'filters', 'plans'));
    }

    private function getFilters(Request $request): array
    {
        return [
            'date_from' => $request->get('date_from', Carbon::now()->subDays(30)->format('Y-m-d')),
            'date_to' => $request->get('date_to', Carbon::now()->format('Y-m-d')),
            'plan_id' => $request->get('plan_id'),
            'status' => $request->get('status'),
        ];
    }
}
