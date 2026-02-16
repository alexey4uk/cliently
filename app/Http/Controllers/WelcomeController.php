<?php

namespace App\Http\Controllers;

use App\Models\BusinessUserInvitation;
use App\Models\Plan;
use App\Models\SubscriptionMetric;
use App\Traits\HasCurrentBusiness;
use Illuminate\Support\Facades\Auth;

class WelcomeController extends Controller
{
    use HasCurrentBusiness;

    /**
     * Display the welcome/onboarding page.
     */
    public function index()
    {
        $user = Auth::user();
        $business = $this->getCurrentBusiness();

        // Если у пользователя уже есть бизнес, перенаправляем на dashboard
        if ($business) {
            return redirect()->route('dashboard');
        }

        // Получаем активные приглашения для пользователя
        $invitations = BusinessUserInvitation::where('email', $user->email)
            ->whereNull('accepted_at')
            ->where('expires_at', '>', now())
            ->with(['business', 'creator', 'businessRole'])
            ->get();

        $userBusinesses = $user ? $user->businesses : collect();

        return view('onboarding', [
            'invitations' => $invitations,
            'userBusinesses' => $userBusinesses,
        ]);
    }

    /**
     * Лендинг: планы и метрики для секции Pricing (те же данные, что и subscription/index).
     */
    public function landing()
    {
        $plans = Plan::getActiveCached();
        $metrics = SubscriptionMetric::getActiveCached();

        $basicKeys = ['max_locations', 'max_masters', 'max_services', 'max_clients', 'max_appointments_per_month', 'max_business_users'];
        $advancedKeys = ['telegram_bot_enabled', 'analytics_enabled', 'advanced_analytics_enabled'];

        $basicMetricsList = $metrics->filter(fn ($m) => in_array($m->key, $basicKeys));
        $advancedMetricsList = $metrics->filter(fn ($m) => in_array($m->key, $advancedKeys));

        return view('welcome', [
            'plans' => $plans,
            'metrics' => $metrics,
            'basicMetricsList' => $basicMetricsList,
            'advancedMetricsList' => $advancedMetricsList,
        ]);
    }

    /**
     * Display the privacy policy page.
     */
    public function privacyPolicy()
    {
        return view('privacy-policy');
    }

    /**
     * Display the public offer page.
     */
    public function publicOffer()
    {
        return view('public-offer');
    }
}
