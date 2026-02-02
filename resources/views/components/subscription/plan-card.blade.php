@props([
    'plan',
    'isCurrent' => false,
    'isPopular' => false,
    'trialUsage' => [],
    'integerMetricsList',
    'booleanMetricsList',
    'hasActivePaidSubscription' => false,
])

@php
    $hasUsedTrial = isset($trialUsage[$plan->id]) && $trialUsage[$plan->id];
    $canUseTrial = $plan->trial_days > 0 && $plan->price !== null && !$hasUsedTrial;
@endphp

<div class="relative flex flex-col transition-all duration-300">
    @if($isPopular)
        <div class="absolute -top-3 sm:-top-4 left-1/2 -translate-x-1/2 z-20">
            <span class="inline-flex items-center gap-1.5 px-3 sm:px-4 py-1 sm:py-1.5 text-xs font-semibold text-white bg-indigo-600 rounded-full shadow-lg">
                <i class="fa-solid fa-star text-xs"></i>
                Популярный
            </span>
        </div>
    @endif

    <div class="flex flex-col h-full bg-white dark:bg-slate-900 rounded-xl sm:rounded-2xl border-2
        {{ $isPopular ? 'border-indigo-500 shadow-xl' : ($isCurrent ? 'border-indigo-500 shadow-lg' : 'border-slate-200 dark:border-slate-800 shadow-md') }}
        hover:shadow-xl transition-all duration-300 overflow-hidden">
        {{-- Header --}}
        <div class="px-4 sm:px-6 pt-6 sm:pt-8 pb-4 sm:pb-6
            {{ $isPopular ? 'bg-gradient-to-br from-indigo-50 to-slate-50 dark:from-indigo-900/20 dark:to-slate-800/50' : 'bg-slate-50 dark:bg-slate-800/50' }}
            border-b border-slate-200 dark:border-slate-700">
            <h3 class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-white mb-1 sm:mb-2">{{ $plan->name }}</h3>
            @if($plan->description)
                <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-400">{{ $plan->description }}</p>
            @endif
        </div>

        {{-- Price --}}
        <div class="px-4 sm:px-6 py-4 sm:py-6 border-b border-slate-200 dark:border-slate-700">
            <div class="flex items-baseline gap-2 mb-1">
                @if($plan->price)
                    <span class="text-3xl sm:text-4xl font-bold text-slate-900 dark:text-white">{{ number_format($plan->price, 0, ',', ' ') }}</span>
                    <span class="text-base sm:text-lg text-slate-600 dark:text-slate-400">BYN</span>
                @else
                    <span class="text-3xl sm:text-4xl font-bold text-slate-900 dark:text-white">Бесплатно</span>
                @endif
            </div>
            @if($plan->price)
                <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400">за {{ $plan->interval === 'monthly' ? 'месяц' : 'год' }}</p>
            @endif
            @if($plan->trial_days > 0 && $plan->price)
                @if($hasUsedTrial)
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-2">
                        <i class="fa-solid fa-info-circle mr-1"></i>
                        Пробный период уже использован
                    </p>
                @else
                    <p class="text-xs text-green-600 dark:text-green-400 mt-2 font-medium">
                        <i class="fa-solid fa-gift mr-1"></i>
                        {{ $plan->trial_days }} {{ $plan->trial_days === 1 ? 'день' : ($plan->trial_days < 5 ? 'дня' : 'дней') }} пробного периода
                    </p>
                @endif
            @endif
        </div>

        {{-- Features (метрики из БД по типу) --}}
        <div class="flex-1 px-4 sm:px-6 py-4 sm:py-6 space-y-3 sm:space-y-4">
            <div class="space-y-2 sm:space-y-3">
                @foreach($integerMetricsList as $metric)
                    @php $val = $plan->getFeatureValue($metric->key); @endphp
                    @if($val !== null)
                        <x-subscription.metric-row :metric="$metric" :value="$val" variant="row" :advanced="false" />
                    @endif
                @endforeach
            </div>
            @if($booleanMetricsList->count() > 0)
                <div class="pt-3 sm:pt-4 mt-3 sm:mt-4 border-t border-slate-200 dark:border-slate-700 space-y-2 sm:space-y-3">
                    @foreach($booleanMetricsList as $metric)
                        @php $val = $plan->getFeatureValue($metric->key); @endphp
                        @if($val !== null)
                            <x-subscription.metric-row :metric="$metric" :value="$val" variant="row" :advanced="true" />
                        @endif
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Actions --}}
        <div class="px-4 sm:px-6 pb-4 sm:pb-6 pt-3 sm:pt-4 space-y-2">
            @php $isFreePlan = !$plan->price || $plan->price == 0; $blockFreeBecausePaid = $hasActivePaidSubscription && $isFreePlan; @endphp
            @if($blockFreeBecausePaid)
                <div class="p-4 bg-amber-50 dark:bg-amber-500/10 border border-amber-200 dark:border-amber-500/20 rounded-xl">
                    <p class="text-sm text-amber-800 dark:text-amber-300 mb-3">
                        Чтобы перейти на бесплатный тариф, сначала отмените платную подписку. Она останется активной до конца оплаченного периода.
                    </p>
                    <a href="{{ route('subscription.current') }}" class="w-full min-h-[44px] py-2.5 sm:py-3 px-4 flex items-center justify-center gap-2 rounded-lg text-sm sm:text-base font-semibold text-amber-800 dark:text-amber-300 bg-amber-100 dark:bg-amber-500/20 hover:bg-amber-200 dark:hover:bg-amber-500/30 transition-colors">
                        <i class="fa-solid fa-external-link-alt"></i>
                        Перейти к подписке и отмене
                    </a>
                </div>
            @else
            <form action="{{ route('subscription.subscribe', $plan) }}" method="POST" class="subscription-form" x-ref="form-{{ $plan->id }}">
                @csrf
                @if($canUseTrial)
                    <input type="hidden" name="use_trial" value="0" x-ref="useTrial-{{ $plan->id }}">
                @endif
                @if($isCurrent)
                    <button type="button" disabled
                        class="w-full min-h-[44px] py-2.5 sm:py-3 px-4 bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 rounded-lg text-sm sm:text-base font-semibold cursor-not-allowed">
                        Текущий тариф
                    </button>
                @else
                    <button type="button"
                        @click="openConfirmModal('{{ addslashes($plan->name) }}', {{ $plan->price ? number_format($plan->price, 2, '.', '') : 0 }}, '{{ $plan->interval }}', {{ $plan->trial_days ?? 0 }}, {{ $hasUsedTrial ? 'true' : 'false' }}, $refs['form-{{ $plan->id }}'])"
                        class="subscription-submit-btn w-full min-h-[44px] py-2.5 sm:py-3 px-4 rounded-lg text-sm sm:text-base font-semibold text-white transition-all duration-200 bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 shadow-md hover:shadow-lg">
                        <span class="btn-text">
                            <i class="fa-solid fa-check-circle mr-2"></i>
                            {{ $plan->price ? 'Выбрать тариф' : 'Начать бесплатно' }}
                        </span>
                    </button>
                @endif
            </form>
            @endif
        </div>
    </div>
</div>
