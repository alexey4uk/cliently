<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Plan;
use App\Models\SubscriptionMetric;
use App\Services\BepaidService;
use App\Services\SubscriptionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

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

        // Проверяем для каждого тарифа, использовал ли пользователь пробный период
        $subscriptionService = app(SubscriptionService::class);
        $trialUsage = [];
        foreach ($plans as $plan) {
            if ($plan->trial_days > 0 && $plan->price !== null) {
                $trialUsage[$plan->id] = $subscriptionService->hasUsedTrialForPlan($user, $plan);
            }
        }

        return view('subscription.index', [
            'plans' => $plans,
            'user' => $user,
            'currentPlan' => $currentPlan,
            'currentSubscription' => $currentSubscription,
            'metrics' => $metrics,
            'trialUsage' => $trialUsage,
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
        $bepaidService = app(BepaidService::class);

        // Проверяем, может ли пользователь использовать пробный период
        $canUseTrial = $plan->trial_days > 0 && $plan->price !== null;
        $hasUsedTrial = false;
        $useTrial = $request->input('use_trial', false);

        if ($canUseTrial) {
            $hasUsedTrial = $subscriptionService->hasUsedTrialForPlan($user, $plan);
        }

        // Если тариф бесплатный или выбран пробный период - активируем сразу
        $isTrial = $canUseTrial && ! $hasUsedTrial && $useTrial;
        $isFree = $plan->price === null || $plan->price == 0;

        if ($isFree || $isTrial) {
            // Создаем или обновляем подписку без оплаты
            $subscription = $subscriptionService->createSubscription($user, $plan, $isTrial);

            $message = "Тариф «{$plan->name}» успешно активирован!";

            if ($isTrial) {
                $trialDays = $plan->trial_days;
                $trialText = $trialDays === 1 ? 'день' : ($trialDays < 5 ? 'дня' : 'дней');
                $message .= " У вас {$trialDays} {$trialText} пробного периода.";
            } elseif ($hasUsedTrial && $canUseTrial) {
                $message .= ' Пробный период для этого тарифа уже был использован ранее.';
            }

            return redirect()->route('subscription.current')
                ->with('success', $message);
        }

        // Для платных тарифов создаем Invoice и перенаправляем на оплату
        try {
            // Проверяем, включен ли bePaid
            $bepaidSettings = \App\Models\BepaidSettings::getSettings();
            if (! $bepaidSettings->enabled) {
                return redirect()->back()
                    ->with('error', 'Платежная система временно недоступна. Обратитесь в поддержку.');
            }

            // Создаем Invoice
            $invoice = Invoice::create([
                'user_id' => $user->id,
                'plan_id' => $plan->id,
                'amount' => $plan->price,
                'currency' => config('bepaid.currency', 'BYN'),
                'status' => 'pending',
                'payment_method' => $request->input('payment_method', config('bepaid.default_payment_method', 'redirect')),
                'expires_at' => now()->addDays(7), // Срок действия инвойса - 7 дней
                'metadata' => [
                    'plan_name' => $plan->name,
                    'plan_interval' => $plan->interval,
                ],
            ]);

            // Создаем платежный токен
            $paymentData = $bepaidService->createPaymentToken($invoice, $invoice->payment_method);

            // Перенаправляем на страницу оплаты или сразу на bePaid
            if ($invoice->payment_method === 'redirect') {
                // Редирект на страницу bePaid
                return redirect($paymentData['redirect_url']);
            } else {
                // Виджет - перенаправляем на страницу с виджетом
                return redirect()->route('subscription.payment', $invoice)
                    ->with('payment_token', $paymentData['token']);
            }

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Ошибка при создании платежа: '.$e->getMessage());
        }
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

        // Загружаем инвойсы для подписки
        $subscription->load('invoices');

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
        if (! $subscription) {
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
                ->with('success', 'Подписка успешно отменена. Она будет активна до '.$subscription->fresh()->ends_at->format('d.m.Y').'.');
        }

        return redirect()->route('subscription.current')
            ->with('error', 'Не удалось отменить подписку. Попробуйте позже.');
    }

    /**
     * Страница оплаты (для виджета)
     */
    public function payment(Invoice $invoice)
    {
        $user = Auth::user();

        // Проверяем, что инвойс принадлежит пользователю
        if ($invoice->user_id !== $user->id) {
            abort(403, 'Доступ запрещен');
        }

        // Проверяем, что инвойс еще не оплачен
        if ($invoice->isPaid()) {
            return redirect()->route('subscription.current')
                ->with('info', 'Этот платеж уже оплачен.');
        }

        // Проверяем, не истек ли срок
        if ($invoice->isExpired()) {
            return redirect()->route('subscription.index')
                ->with('error', 'Срок действия платежа истек. Создайте новый.');
        }

        $paymentToken = session('payment_token') ?? $invoice->bepaid_payment_token;

        return view('subscription.payment', [
            'invoice' => $invoice,
            'plan' => $invoice->plan,
            'payment_token' => $paymentToken,
        ]);
    }

    /**
     * Callback успешной оплаты
     */
    public function paymentSuccess(Request $request)
    {
        $invoiceId = $request->input('invoice');

        if (! $invoiceId) {
            return redirect()->route('subscription.index')
                ->with('error', 'Неверные параметры запроса.');
        }

        $invoice = Invoice::find($invoiceId);

        if (! $invoice) {
            return redirect()->route('subscription.index')
                ->with('error', 'Инвойс не найден.');
        }

        // Проверяем статус инвойса
        if ($invoice->isPaid()) {
            // Проверяем, не пытаемся ли мы повторно активировать подписку
            if ($invoice->subscription_id && $invoice->subscription->status === 'active') {
                if (config('bepaid.logging.enabled')) {
                    Log::info('Payment success callback: subscription already active', [
                        'invoice_id' => $invoice->id,
                        'subscription_id' => $invoice->subscription_id,
                    ]);
                }
            }

            return redirect()->route('subscription.current')
                ->with('success', 'Платеж успешно обработан! Подписка активирована.');
        }

        // Если еще не оплачен, проверяем статус через API
        if ($invoice->bepaid_transaction_id) {
            try {
                $bepaidService = app(BepaidService::class);
                $status = $bepaidService->checkPaymentStatus($invoice->bepaid_transaction_id);

                if ($status['paid']) {
                    $invoice->update([
                        'status' => 'paid',
                        'paid_at' => now(),
                        'bepaid_transaction_id' => $status['uid'] ?? $invoice->bepaid_transaction_id,
                    ]);

                    // Активируем подписку
                    $subscriptionService = app(SubscriptionService::class);
                    if ($invoice->subscription_id) {
                        $subscription = $invoice->subscription;

                        // Идемпотентность: проверяем, не активирована ли уже подписка
                        if ($subscription->status === 'active' && $subscription->invoice_id === $invoice->id) {
                            if (config('bepaid.logging.enabled')) {
                                Log::info('Payment success callback: subscription already activated for this invoice', [
                                    'invoice_id' => $invoice->id,
                                    'subscription_id' => $subscription->id,
                                ]);
                            }
                        } else {
                            $subscription->update([
                                'status' => 'active',
                                'payment_status' => 'paid',
                                'invoice_id' => $invoice->id,
                            ]);

                            if (config('bepaid.logging.enabled')) {
                                Log::info('Payment success callback: subscription activated', [
                                    'invoice_id' => $invoice->id,
                                    'subscription_id' => $subscription->id,
                                ]);
                            }
                        }
                    } else {
                        // Создаем подписку, если её еще нет
                        $subscription = $subscriptionService->createSubscription($invoice->user, $invoice->plan, false, $invoice);

                        if (config('bepaid.logging.enabled')) {
                            Log::info('Payment success callback: subscription created', [
                                'invoice_id' => $invoice->id,
                                'subscription_id' => $subscription->id,
                            ]);
                        }
                    }

                    return redirect()->route('subscription.current')
                        ->with('success', 'Платеж успешно обработан! Подписка активирована.');
                }
            } catch (\Exception $e) {
                // Логируем все ошибки при проверке статуса платежа
                Log::error('Payment success callback: error checking payment status', [
                    'invoice_id' => $invoice->id,
                    'transaction_id' => $invoice->bepaid_transaction_id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
            }
        }

        return redirect()->route('subscription.current')
            ->with('info', 'Платеж обрабатывается. Подписка будет активирована после подтверждения платежа.');
    }

    /**
     * Callback отклоненной оплаты
     */
    public function paymentDecline(Request $request)
    {
        $invoiceId = $request->input('invoice');

        if ($invoiceId) {
            $invoice = Invoice::find($invoiceId);
            if ($invoice && $invoice->isPending()) {
                $invoice->update(['status' => 'failed']);
            }
        }

        return redirect()->route('subscription.index')
            ->with('error', 'Платеж был отклонен. Попробуйте еще раз или выберите другой способ оплаты.');
    }

    /**
     * Callback неудачной оплаты
     */
    public function paymentFail(Request $request)
    {
        $invoiceId = $request->input('invoice');

        if ($invoiceId) {
            $invoice = Invoice::find($invoiceId);
            if ($invoice && $invoice->isPending()) {
                $invoice->update(['status' => 'failed']);
            }
        }

        return redirect()->route('subscription.index')
            ->with('error', 'Произошла ошибка при обработке платежа. Попробуйте еще раз.');
    }

    /**
     * Callback отмены оплаты
     */
    public function paymentCancel(Request $request)
    {
        $invoiceId = $request->input('invoice');

        if ($invoiceId) {
            $invoice = Invoice::find($invoiceId);
            if ($invoice && $invoice->isPending()) {
                $invoice->update(['status' => 'cancelled']);
            }
        }

        return redirect()->route('subscription.index')
            ->with('info', 'Оплата отменена. Вы можете попробовать снова позже.');
    }
}
