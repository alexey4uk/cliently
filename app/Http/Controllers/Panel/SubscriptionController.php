<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use App\Services\SubscriptionService;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function __construct(
        protected SubscriptionService $subscriptionService
    ) {}

    /**
     * Список подписок с фильтрами
     */
    public function index()
    {
        $search = request('search', '');
        $status = request('status', '');
        $planId = request('plan_id', '');
        $sort = request('sort', 'created_at');
        $direction = request('direction', 'desc');
        $perPage = request('per_page', 20);

        $query = Subscription::with(['user', 'plan']);

        if ($search !== '') {
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }
        if ($status !== '') {
            $query->where('status', $status);
        }
        if ($planId !== '') {
            $query->where('plan_id', $planId);
        }

        $allowedSorts = ['created_at', 'starts_at', 'ends_at', 'status'];
        if (in_array($sort, $allowedSorts)) {
            $query->orderBy($sort, $direction);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $subscriptions = $query->paginate($perPage)->withQueryString();
        $plans = Plan::orderBy('sort_order')->get();

        $stats = [
            'total' => Subscription::count(),
            'active' => Subscription::where('status', 'active')->count(),
            'trial' => Subscription::where('status', 'trial')->count(),
            'expired' => Subscription::where('status', 'expired')->count(),
        ];

        return view('panel.subscriptions.index', compact(
            'subscriptions',
            'plans',
            'search',
            'status',
            'planId',
            'sort',
            'direction',
            'perPage',
            'stats'
        ));
    }

    /**
     * Карточка подписки: данные и действия (статус, продление, выдача)
     */
    public function show(Subscription $subscription)
    {
        $subscription->load(['user', 'plan']);
        $plans = Plan::where('is_active', true)->orderBy('sort_order')->get();

        return view('panel.subscriptions.show', compact('subscription', 'plans'));
    }

    /**
     * Отменить подписку в конце периода (без продления).
     */
    public function cancel(Subscription $subscription)
    {
        $this->subscriptionService->adminCancelAtEnd($subscription);

        return redirect()
            ->route('panel.subscriptions.show', $subscription)
            ->with('success', 'Подписка отменена в конце периода.');
    }

    /**
     * Выдать или изменить подписку (продление = тот же тариф + новая дата, смена тарифа = другой тариф и срок).
     */
    public function grant(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'plan_id' => 'required|exists:plans,id',
            'ends_at' => 'nullable|date',
            'days' => 'nullable|integer|min:1|max:3650',
            'as_trial' => 'boolean',
        ]);

        $user = User::findOrFail($request->input('user_id'));
        $plan = Plan::findOrFail($request->input('plan_id'));
        // Чекбокс: при отметке приходит as_trial=1, при снятой галочке (есть hidden 0) — 0
        $asTrial = $request->boolean('as_trial');

        // Если указано кол-во дней — используем их, иначе дату окончания
        $days = $request->filled('days') ? (int) $request->input('days') : null;
        $endsAt = null;
        if ($days === null && $request->filled('ends_at')) {
            $endsAt = \Carbon\Carbon::parse($request->input('ends_at'));
        }

        $this->subscriptionService->adminGrant($user, $plan, $endsAt, $asTrial, $days);

        $subscription = $user->subscription()->with('plan')->first();

        return redirect()
            ->route('panel.subscriptions.show', $subscription)
            ->with('success', 'Подписка выдана.');
    }
}
