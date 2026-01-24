<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\SubscriptionMetric;
use App\Services\SubscriptionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubscriptionController extends Controller
{
    /**
     * Список доступных тарифов (страница выбора)
     */
    public function index()
    {
        $user = Auth::user();

        $plans = Plan::where('is_active', true)
            ->orderBy('sort_order')
            ->with('features')
            ->get();

        $currentSubscription = $user->activeSubscription();
        $currentPlan = $currentSubscription ? $currentSubscription->plan : null;

        // Получаем активные метрики с сортировкой
        $metrics = SubscriptionMetric::where('is_active', true)
            ->ordered()
            ->get();

        return view('subscription.index', [
            'plans' => $plans,
            'user' => $user,
            'currentPlan' => $currentPlan,
            'currentSubscription' => $currentSubscription,
            'metrics' => $metrics,
        ]);
    }

    /**
     * Детали тарифа
     */
    public function show(Plan $plan)
    {
        $user = Auth::user();

        if (! $plan->is_active) {
            return redirect()->route('subscription.index')
                ->with('error', 'Этот тариф недоступен.');
        }

        $currentSubscription = $user->activeSubscription();
        $currentPlan = $currentSubscription ? $currentSubscription->plan : null;

        $plan->load('features');

        // Получаем активные метрики с сортировкой
        $metrics = SubscriptionMetric::where('is_active', true)
            ->ordered()
            ->get();

        return view('subscription.show', [
            'plan' => $plan,
            'user' => $user,
            'currentPlan' => $currentPlan,
            'currentSubscription' => $currentSubscription,
            'metrics' => $metrics,
        ]);
    }

    /**
     * Оформление подписки (POST)
     */
    public function subscribe(Request $request, Plan $plan)
    {
        $user = Auth::user();

        if (! $plan->is_active) {
            return redirect()->route('subscription.index')
                ->with('error', 'Этот тариф недоступен.');
        }

        $subscriptionService = app(SubscriptionService::class);

        // Создаем или обновляем подписку
        // Для бесплатных тарифов сразу активируем
        $isTrial = $plan->trial_days > 0 && $plan->price !== null;
        $subscriptionService->createSubscription($user, $plan, $isTrial);

        $message = "Тариф «{$plan->name}» успешно активирован!";
        
        if ($isTrial) {
            $trialDays = $plan->trial_days;
            $trialText = $trialDays === 1 ? 'день' : ($trialDays < 5 ? 'дня' : 'дней');
            $message .= " У вас {$trialDays} {$trialText} пробного периода.";
        }

        return redirect()->route('subscription.current')
            ->with('success', $message);
    }

    /**
     * Текущая подписка
     */
    public function current()
    {
        $user = Auth::user();

        $subscription = $user->activeSubscription();

        if (! $subscription) {
            return redirect()->route('subscription.index')
                ->with('info', 'У вас нет активной подписки. Выберите тариф.');
        }

        $subscriptionService = app(SubscriptionService::class);
        $plan = $subscription->plan;

        // Получаем использование лимитов (суммарно по всем бизнесам пользователя)
        $usage = [
            'locations' => [
                'current' => $subscriptionService->getCurrentUsage($user, 'max_locations'),
                'limit' => $subscriptionService->getLimit($user, 'max_locations'),
            ],
            'masters' => [
                'current' => $subscriptionService->getCurrentUsage($user, 'max_masters'),
                'limit' => $subscriptionService->getLimit($user, 'max_masters'),
            ],
            'services' => [
                'current' => $subscriptionService->getCurrentUsage($user, 'max_services'),
                'limit' => $subscriptionService->getLimit($user, 'max_services'),
            ],
            'clients' => [
                'current' => $subscriptionService->getCurrentUsage($user, 'max_clients'),
                'limit' => $subscriptionService->getLimit($user, 'max_clients'),
            ],
            'appointments_per_month' => [
                'current' => $subscriptionService->getCurrentUsage($user, 'max_appointments_per_month'),
                'limit' => $subscriptionService->getLimit($user, 'max_appointments_per_month'),
            ],
            'business_users' => [
                'current' => $subscriptionService->getCurrentUsage($user, 'max_business_users'),
                'limit' => $subscriptionService->getLimit($user, 'max_business_users'),
            ],
        ];

        return view('subscription.current', [
            'user' => $user,
            'subscription' => $subscription,
            'plan' => $plan,
            'usage' => $usage,
        ]);
    }

    /**
     * Отменить подписку
     */
    public function cancel(Request $request)
    {
        $this->authorizeBusinessPermission('client.subscription.manage');

        $user = Auth::user();
        $subscriptionService = app(SubscriptionService::class);

        $subscription = $user->activeSubscription();

        // Проверяем наличие активной подписки
        if (!$subscription) {
            return redirect()->route('subscription.current')
                ->with('error', 'У вас нет активной подписки.');
        }

        // Проверяем, что тариф не бесплатный
        if ($subscription->plan->slug === 'free') {
            return redirect()->route('subscription.current')
                ->with('error', 'Бесплатный тариф нельзя отменять.');
        }

        // Проверяем, что подписка еще не отменена
        if ($subscription->isCancelled()) {
            return redirect()->route('subscription.current')
                ->with('error', 'Подписка уже отменена.');
        }

        // Отменяем подписку
        $result = $subscriptionService->cancelSubscription($user);

        if ($result) {
            return redirect()->route('subscription.current')
                ->with('success', 'Подписка успешно отменена. Она будет активна до ' . $subscription->fresh()->ends_at->format('d.m.Y') . '.');
        }

        return redirect()->route('subscription.current')
            ->with('error', 'Не удалось отменить подписку. Попробуйте позже.');
    }
}
