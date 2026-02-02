@extends('layouts.user')

@section('title', 'Детали тарифа - Cliently')
@section('page-title', 'Детали тарифа')
@section('page-description', 'Подробная информация о тарифе')

@push('breadcrumbs')
    <x-breadcrumbs :items="[
        ['title' => 'Тарифы', 'url' => route('subscription.index')],
        ['title' => $plan->name]
    ]" />
@endpush

@section('content')

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="bg-white dark:bg-slate-900 rounded-xl sm:rounded-2xl border-2 border-slate-200 dark:border-slate-800 shadow-md hover:shadow-lg transition-shadow p-6 sm:p-8 mb-6">
        <div class="text-center mb-8">
            <h1 class="text-2xl sm:text-3xl font-bold text-slate-900 dark:text-white mb-2">{{ $plan->name }}</h1>
            @if($plan->description)
                <p class="text-base sm:text-lg text-slate-600 dark:text-slate-400">{{ $plan->description }}</p>
            @endif
            <div class="mt-4">
                @if($plan->price)
                    <div class="flex items-baseline justify-center gap-2">
                        <span class="text-4xl sm:text-5xl font-bold text-slate-900 dark:text-white">{{ number_format($plan->price, 0, ',', ' ') }}</span>
                        <span class="text-lg sm:text-xl text-slate-600 dark:text-slate-400">BYN/{{ $plan->interval === 'monthly' ? 'мес' : 'год' }}</span>
                    </div>
                @else
                    <span class="text-4xl sm:text-5xl font-bold text-slate-900 dark:text-white">Бесплатно</span>
                @endif
            </div>
        </div>

        @if($currentPlan && $currentPlan->id !== $plan->id)
            <div class="bg-indigo-50 dark:bg-indigo-500/10 border border-indigo-200 dark:border-indigo-500/20 rounded-xl p-4 mb-6">
                <div class="flex items-start gap-3">
                    <i class="fa-solid fa-info-circle text-indigo-600 dark:text-indigo-400 mt-0.5 shrink-0"></i>
                    <div>
                        <h4 class="font-semibold text-indigo-900 dark:text-indigo-300 mb-1">Смена тарифа</h4>
                        <p class="text-sm text-indigo-700 dark:text-indigo-400">
                            Вы переходите с тарифа "{{ $currentPlan->name }}" на "{{ $plan->name }}".
                            @php
                                $currentSubscription = $user->activeSubscription();
                                $hasActiveTime = $currentSubscription && $currentSubscription->ends_at && $currentSubscription->ends_at->isFuture();
                            @endphp
                            @if($plan->price && $plan->price > ($currentPlan->price ?? 0))
                                Новый тариф будет активирован сразу.
                                @if($hasActiveTime)
                                    <br><span class="text-green-600 dark:text-green-400 font-medium">✓ Оплаченное время будет сохранено до {{ $currentSubscription->ends_at->format('d.m.Y') }}.</span>
                                @endif
                            @elseif($plan->price && $plan->price < ($currentPlan->price ?? 0))
                                @if($hasActiveTime)
                                    <br><span class="text-green-600 dark:text-green-400 font-medium">✓ Оплаченное время будет сохранено до {{ $currentSubscription->ends_at->format('d.m.Y') }}.</span>
                                    <br>Обратите внимание, что при переходе на более низкий тариф некоторые функции могут стать недоступны.
                                @else
                                    Обратите внимание, что при переходе на более низкий тариф некоторые функции могут стать недоступны.
                                @endif
                            @elseif($hasActiveTime)
                                <br><span class="text-green-600 dark:text-green-400 font-medium">✓ Оплаченное время будет сохранено до {{ $currentSubscription->ends_at->format('d.m.Y') }}.</span>
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        @endif

        <div class="space-y-4">
            <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">Что включено в тариф:</h3>
            @foreach($metrics as $metric)
                @php $value = $plan->getFeatureValue($metric->key); @endphp
                @if($value !== null)
                    <x-subscription.metric-row :metric="$metric" :value="$value" variant="block" />
                @endif
            @endforeach
        </div>

        @if($plan->trial_days > 0 && $plan->price)
            <div class="mt-6 p-4 bg-green-50 dark:bg-green-500/10 border border-green-200 dark:border-green-500/20 rounded-xl">
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-gift text-green-600 dark:text-green-400"></i>
                    <span class="text-sm font-medium text-green-900 dark:text-green-300">
                        Пробный период {{ $plan->trial_days }} {{ $plan->trial_days === 1 ? 'день' : ($plan->trial_days < 5 ? 'дня' : 'дней') }}
                    </span>
                </div>
            </div>
        @endif
    </div>

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <a href="{{ route('subscription.index') }}"
           class="inline-flex items-center justify-center gap-2 min-h-[44px] px-4 py-2.5 text-slate-700 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white transition-colors rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800">
            <i class="fa-solid fa-arrow-left"></i>
            <span>Назад к тарифам</span>
        </a>

        @if($currentPlan && $currentPlan->id === $plan->id)
            <button type="button" disabled
                class="inline-flex items-center justify-center gap-2 min-h-[44px] px-6 py-3 bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 rounded-lg font-medium cursor-not-allowed">
                <i class="fa-solid fa-check-circle"></i>
                <span>Текущий тариф</span>
            </button>
        @else
            @php
                $subscriptionService = app(\App\Services\SubscriptionService::class);
                $canUseTrial = $plan->trial_days > 0 && $plan->price !== null;
                $hasUsedTrial = $canUseTrial ? $subscriptionService->hasUsedTrialForPlan($user, $plan) : false;
            @endphp

            <form action="{{ route('subscription.subscribe', $plan) }}" method="POST" class="subscription-form">
                @csrf
                @if($plan->price && $plan->price > 0)
                    @if($canUseTrial && !$hasUsedTrial)
                        <div class="mb-4 p-4 bg-green-50 dark:bg-green-500/10 border border-green-200 dark:border-green-500/20 rounded-xl">
                            <div class="flex items-center gap-2 mb-2">
                                <i class="fa-solid fa-gift text-green-600 dark:text-green-400"></i>
                                <span class="text-sm font-medium text-green-900 dark:text-green-300">
                                    Доступен пробный период {{ $plan->trial_days }} {{ $plan->trial_days === 1 ? 'день' : ($plan->trial_days < 5 ? 'дня' : 'дней') }}
                                </span>
                            </div>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="use_trial" value="1" checked class="rounded">
                                <span class="text-sm text-green-800 dark:text-green-300">Использовать пробный период</span>
                            </label>
                            <p class="text-xs text-green-700 dark:text-green-300 mt-2">Пробный период начнется сразу после активации</p>
                        </div>
                    @endif
                    <button type="submit"
                        class="subscription-submit-btn inline-flex items-center justify-center gap-2 min-h-[44px] px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium transition-colors shadow-md hover:shadow-lg">
                        <i class="fa-solid fa-credit-card"></i>
                        <span class="btn-text">Оплатить подписку</span>
                    </button>
                @else
                    <button type="submit"
                        class="subscription-submit-btn inline-flex items-center justify-center gap-2 min-h-[44px] px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium transition-colors shadow-md hover:shadow-lg">
                        <i class="fa-solid fa-check"></i>
                        <span class="btn-text">Оформить подписку</span>
                    </button>
                @endif
            </form>
        @endif
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.subscription-form').forEach(form => {
        form.addEventListener('submit', function() {
            const btn = form.querySelector('.subscription-submit-btn');
            const btnText = btn?.querySelector('.btn-text');
            if (btn && btnText) {
                btn.disabled = true;
                btnText.innerHTML = '<svg class="animate-spin h-4 w-4 text-white inline-block mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Обработка...';
            }
        });
    });
});
</script>
@endpush
@endsection
