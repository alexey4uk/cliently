@extends('layouts.user')

@section('title', 'Тарифы - Cliently')
@section('page-title', 'Выбор тарифа')
@section('page-description', 'Выберите подходящий тариф для вашего бизнеса')

@push('breadcrumbs')
    <x-breadcrumbs :items="[['title' => 'Тарифы']]" />
@endpush

@section('content')

@php
    // Группируем метрики один раз
    $basicMetrics = ['max_locations', 'max_masters', 'max_services', 'max_clients', 'max_appointments_per_month', 'max_business_users'];
    $advancedMetrics = ['telegram_bot_enabled', 'analytics_enabled', 'advanced_analytics_enabled'];
    
    $basicMetricsList = $metrics->filter(fn($m) => in_array($m->key, $basicMetrics));
    $advancedMetricsList = $metrics->filter(fn($m) => in_array($m->key, $advancedMetrics));
@endphp

<div class="max-w-[1400px] mx-auto">
    <div x-data="{
    showConfirmModal: false,
    selectedPlan: null,
    selectedForm: null,
    openConfirmModal(planName, planPrice, planInterval, trialDays, hasUsedTrial, form) {
        this.selectedPlan = {
            name: planName,
            price: planPrice ? parseFloat(planPrice) : 0,
            interval: planInterval,
            trialDays: trialDays ? parseInt(trialDays) : 0,
            hasUsedTrial: hasUsedTrial === true || hasUsedTrial === 'true'
        };
        this.selectedForm = form;
        this.showConfirmModal = true;
    },
    closeConfirmModal() {
        this.showConfirmModal = false;
        this.selectedPlan = null;
        this.selectedForm = null;
    },
    confirmSubscription() {
        if (this.selectedForm) {
            this.showConfirmModal = false;
            this.selectedForm.submit();
        }
    }
}">

<div class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-8">
    <!-- Hero секция -->
    <div class="text-center mb-8 sm:mb-12 lg:mb-16">
        <h1 class="text-2xl sm:text-3xl lg:text-4xl xl:text-5xl font-bold text-slate-900 dark:text-white mb-3 sm:mb-4">
            Выберите тариф
        </h1>
        <p class="text-base sm:text-lg text-slate-600 dark:text-slate-400 max-w-2xl mx-auto px-2">
            Все необходимые инструменты для эффективного управления салоном или студией
        </p>
    </div>

    <!-- Карточки тарифов -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6 mb-8 sm:mb-12">
        @foreach($plans as $index => $plan)
            @php
                $isCurrent = $currentPlan && $currentPlan->id === $plan->id;
                $isRecommended = $plan->slug === 'basic';
                $isPopular = $isRecommended && !$isCurrent;
            @endphp
            
            <div class="relative flex flex-col
                {{ $isPopular ? 'lg:scale-105 lg:-mt-4 lg:mb-4 z-10' : '' }}
                transition-all duration-300">
                
                <!-- Популярный badge -->
                @if($isPopular)
                    <div class="absolute -top-3 sm:-top-4 left-1/2 -translate-x-1/2 z-20">
                        <span class="inline-flex items-center gap-1.5 px-3 sm:px-4 py-1 sm:py-1.5 text-xs font-semibold text-white bg-blue-600 rounded-full shadow-lg">
                            <i class="fa-solid fa-star text-xs"></i>
                            Популярный
                        </span>
                    </div>
                @endif

                <!-- Карточка -->
                <div class="flex flex-col h-full bg-white dark:bg-slate-900 rounded-xl sm:rounded-2xl border-2 
                    {{ $isPopular ? 'border-blue-500 shadow-xl sm:shadow-2xl' : ($isCurrent ? 'border-indigo-500 shadow-lg sm:shadow-xl' : 'border-slate-200 dark:border-slate-800 shadow-md sm:shadow-lg') }}
                    hover:shadow-xl transition-all duration-300 overflow-hidden">
                    
                    <!-- Заголовок карточки -->
                    <div class="px-4 sm:px-6 pt-6 sm:pt-8 pb-4 sm:pb-6 
                        {{ $isPopular ? 'bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20' : 'bg-slate-50 dark:bg-slate-800/50' }}
                        border-b border-slate-200 dark:border-slate-700">
                        <h3 class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-white mb-1 sm:mb-2">{{ $plan->name }}</h3>
                        @if($plan->description)
                            <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-400">{{ $plan->description }}</p>
                        @endif
                    </div>

                    <!-- Цена -->
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
                            @php
                                $hasUsedTrial = isset($trialUsage[$plan->id]) && $trialUsage[$plan->id];
                            @endphp
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

                    <!-- Список возможностей -->
                    <div class="flex-1 px-4 sm:px-6 py-4 sm:py-6 space-y-3 sm:space-y-4">
                        <div class="space-y-2 sm:space-y-3">
                            @foreach($basicMetricsList as $metric)
                                @php
                                    $value = $plan->getFeatureValue($metric->key);
                                    if ($value === null) continue;
                                    
                                    $displayValue = match(true) {
                                        $value === -1 => 'Безлимит',
                                        $value === true => '✓',
                                        $value === false => '✗',
                                        is_numeric($value) => number_format($value, 0, ',', ' '),
                                        default => $value
                                    };
                                    
                                    $hasFeature = $value !== false && $value !== 0;
                                @endphp
                                <div class="flex items-center justify-between text-xs sm:text-sm">
                                    <span class="text-slate-700 dark:text-slate-300 pr-2">{{ $metric->label }}</span>
                                    <span class="font-semibold text-slate-900 dark:text-white shrink-0">
                                        {{ $displayValue }}
                                    </span>
                                </div>
                            @endforeach
                        </div>

                        @if($advancedMetricsList->count() > 0)
                            <div class="pt-3 sm:pt-4 mt-3 sm:mt-4 border-t border-slate-200 dark:border-slate-700 space-y-2 sm:space-y-3">
                                @foreach($advancedMetricsList as $metric)
                                    @php
                                        $value = $plan->getFeatureValue($metric->key);
                                        if ($value === null) continue;
                                        $hasFeature = $value === true;
                                    @endphp
                                    <div class="flex items-center gap-2 text-xs sm:text-sm">
                                        @if($hasFeature)
                                            <i class="fa-solid fa-check text-green-600 dark:text-green-400 shrink-0"></i>
                                        @else
                                            <i class="fa-solid fa-times text-slate-300 dark:text-slate-600 shrink-0"></i>
                                        @endif
                                        <span class="text-slate-700 dark:text-slate-300">{{ $metric->label }}</span>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <!-- Кнопка -->
                    <div class="px-4 sm:px-6 pb-4 sm:pb-6 pt-3 sm:pt-4">
                        <form action="{{ route('subscription.subscribe', $plan) }}" method="POST" 
                              class="subscription-form"
                              x-ref="form{{ $plan->id }}">
                            @csrf
                            @if($isCurrent)
                                <button type="button" disabled
                                    class="w-full py-2.5 sm:py-3 px-4 bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 rounded-lg text-sm sm:text-base font-semibold cursor-not-allowed">
                                    Текущий тариф
                                </button>
                            @else
                                @php
                                    $hasUsedTrial = isset($trialUsage[$plan->id]) && $trialUsage[$plan->id];
                                    $canUseTrial = $plan->trial_days > 0 && $plan->price !== null && !$hasUsedTrial;
                                @endphp
                                <button type="button"
                                    @click="openConfirmModal('{{ addslashes($plan->name) }}', {{ $plan->price ? number_format($plan->price, 2, '.', '') : 0 }}, '{{ $plan->interval }}', {{ $canUseTrial ? ($plan->trial_days ?? 0) : 0 }}, {{ $hasUsedTrial ? 'true' : 'false' }}, $refs.form{{ $plan->id }})"
                                    class="subscription-submit-btn w-full py-2.5 sm:py-3 px-4 rounded-lg text-sm sm:text-base font-semibold text-white transition-all duration-200
                                    {{ $isPopular 
                                        ? 'bg-blue-600 hover:bg-blue-700 active:bg-blue-800 shadow-lg hover:shadow-xl' 
                                        : 'bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 shadow-md hover:shadow-lg' }}">
                                    <span class="btn-text">
                                        <i class="fa-solid fa-check-circle mr-2"></i>
                                        {{ $plan->price ? 'Выбрать тариф' : 'Начать бесплатно' }}
                                    </span>
                                </button>
                            @endif
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Сравнительная таблица -->
    <div class="bg-white dark:bg-slate-900 rounded-xl sm:rounded-2xl border border-slate-200 dark:border-slate-800 shadow-lg overflow-hidden">
        <div class="px-4 sm:px-6 py-4 sm:py-5 border-b border-slate-200 dark:border-slate-800 bg-gradient-to-r from-slate-50 to-slate-100 dark:from-slate-800 dark:to-slate-900">
            <h2 class="text-lg sm:text-xl font-bold text-slate-900 dark:text-white mb-1">Сравнение тарифов</h2>
            <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-400">Детальное сравнение всех возможностей</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full min-w-[600px]">
                    <thead>
                        <tr class="bg-slate-50 dark:bg-slate-800/50 border-b-2 border-slate-200 dark:border-slate-700">
                            <th class="px-4 sm:px-6 py-4 text-left text-sm font-bold text-slate-900 dark:text-white">
                                Функция
                            </th>
                            @foreach($plans as $plan)
                                <th class="px-4 sm:px-6 py-4 text-center text-sm font-bold text-slate-900 dark:text-white min-w-[120px]">
                                    {{ $plan->name }}
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                    <!-- Основные возможности -->
                    <tr class="bg-slate-100/50 dark:bg-slate-800/30">
                        <td colspan="{{ count($plans) + 1 }}" class="px-4 sm:px-6 py-3">
                            <div class="flex items-center gap-2">
                                <i class="fa-solid fa-list-check text-slate-500 dark:text-slate-400"></i>
                                <span class="text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">Основные возможности</span>
                            </div>
                        </td>
                    </tr>
                    @foreach($basicMetricsList as $metric)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/20 transition-colors">
                            <td class="px-4 sm:px-6 py-3 text-sm text-slate-900 dark:text-white font-semibold">
                                <div class="flex items-center gap-2">
                                    @if($metric->icon)
                                        <i class="{{ $metric->icon }} text-slate-500 dark:text-slate-400"></i>
                                    @endif
                                    <span>{{ $metric->label }}</span>
                                </div>
                            </td>
                            @foreach($plans as $plan)
                                @php
                                    $value = $plan->getFeatureValue($metric->key);
                                    $isPopularPlan = $plan->slug === 'basic';
                                    $isCurrentPlan = $currentPlan && $currentPlan->id === $plan->id;
                                    
                                    $displayValue = match(true) {
                                        $value === -1 => '<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-bold bg-gradient-to-r from-indigo-100 to-purple-100 text-indigo-700 dark:from-indigo-500/20 dark:to-purple-500/20 dark:text-indigo-300">Безлимит</span>',
                                        $value === true => '<i class="fa-solid fa-check-circle text-green-600 dark:text-green-400 text-lg"></i>',
                                        $value === false => '<i class="fa-solid fa-times-circle text-slate-300 dark:text-slate-600 text-lg"></i>',
                                        is_numeric($value) => '<span class="text-sm font-bold text-slate-900 dark:text-white">' . number_format($value, 0, ',', ' ') . '</span>',
                                        default => '<span class="text-slate-400">-</span>'
                                    };
                                    
                                    $cellBg = '';
                                    if ($isPopularPlan) $cellBg = 'bg-blue-50/30 dark:bg-blue-900/10';
                                    if ($isCurrentPlan) $cellBg = 'bg-indigo-50/30 dark:bg-indigo-900/10';
                                @endphp
                                <td class="px-4 sm:px-6 py-3 text-center {{ $cellBg }}">
                                    {!! $displayValue !!}
                                </td>
                            @endforeach
                        </tr>
                    @endforeach

                    <!-- Расширенные функции -->
                    @if($advancedMetricsList->count() > 0)
                        <tr class="bg-slate-100/50 dark:bg-slate-800/30">
                            <td colspan="{{ count($plans) + 1 }}" class="px-4 sm:px-6 py-3">
                                <div class="flex items-center gap-2">
                                    <i class="fa-solid fa-star text-slate-500 dark:text-slate-400"></i>
                                    <span class="text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">Расширенные функции</span>
                                </div>
                            </td>
                        </tr>
                        @foreach($advancedMetricsList as $metric)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/20 transition-colors">
                                <td class="px-4 sm:px-6 py-3 text-sm text-slate-900 dark:text-white font-semibold">
                                    <div class="flex items-center gap-2">
                                        @if($metric->icon)
                                            <i class="{{ $metric->icon }} text-slate-500 dark:text-slate-400"></i>
                                        @endif
                                        <span>{{ $metric->label }}</span>
                                    </div>
                                </td>
                                @foreach($plans as $plan)
                                    @php
                                        $value = $plan->getFeatureValue($metric->key);
                                        $isPopularPlan = $plan->slug === 'basic';
                                        $isCurrentPlan = $currentPlan && $currentPlan->id === $plan->id;
                                        
                                        $displayValue = $value === true 
                                            ? '<i class="fa-solid fa-check-circle text-green-600 dark:text-green-400 text-lg"></i>'
                                            : '<i class="fa-solid fa-times-circle text-slate-300 dark:text-slate-600 text-lg"></i>';
                                        
                                        $cellBg = '';
                                        if ($isPopularPlan) $cellBg = 'bg-blue-50/30 dark:bg-blue-900/10';
                                        if ($isCurrentPlan) $cellBg = 'bg-indigo-50/30 dark:bg-indigo-900/10';
                                    @endphp
                                    <td class="px-4 sm:px-6 py-3 text-center {{ $cellBg }}">
                                        {!! $displayValue !!}
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>
    </div>

    <!-- Модальное окно подтверждения активации тарифа -->
    <div x-show="showConfirmModal" 
         @click.away="closeConfirmModal()"
         @keydown.escape.window="closeConfirmModal()"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
         style="display: none;">
        <div @click.stop
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="transform opacity-0 scale-95"
             x-transition:enter-end="transform opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="transform opacity-100 scale-100"
             x-transition:leave-end="transform opacity-0 scale-95"
             class="bg-white dark:bg-slate-900 rounded-2xl shadow-xl border border-slate-200 dark:border-slate-800 max-w-md w-full overflow-hidden">
            <!-- Заголовок -->
            <div class="px-6 py-5 border-b border-slate-200 dark:border-slate-800 bg-gradient-to-r from-indigo-50 to-purple-50 dark:from-indigo-900/20 dark:to-purple-900/20">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white">Подтверждение активации тарифа</h3>
                    <button @click="closeConfirmModal()"
                        class="p-1 text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 transition-colors rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
            </div>
            
            <!-- Содержимое -->
            <div class="px-6 py-6" x-show="selectedPlan">
                <div class="mb-6">
                    <div class="text-center mb-4">
                        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-indigo-100 dark:bg-indigo-500/20 mb-3">
                            <i class="fa-solid fa-credit-card text-indigo-600 dark:text-indigo-400 text-2xl"></i>
                        </div>
                        <h4 class="text-xl font-bold text-slate-900 dark:text-white mb-2" x-text="selectedPlan?.name"></h4>
                        <div class="flex items-baseline justify-center gap-2 mb-2">
                            <template x-if="selectedPlan && selectedPlan.price > 0">
                                <div>
                                    <span class="text-3xl font-bold text-slate-900 dark:text-white" x-text="Math.round(selectedPlan.price).toLocaleString('ru-RU')"></span>
                                    <span class="text-lg text-slate-600 dark:text-slate-400 ml-1">BYN</span>
                                </div>
                            </template>
                            <template x-if="!selectedPlan || !selectedPlan.price || selectedPlan.price === 0">
                                <span class="text-3xl font-bold text-slate-900 dark:text-white">Бесплатно</span>
                            </template>
                        </div>
                        <template x-if="selectedPlan && selectedPlan.price > 0">
                            <p class="text-sm text-slate-600 dark:text-slate-400" x-text="'за ' + (selectedPlan.interval === 'monthly' ? 'месяц' : 'год')"></p>
                        </template>
                        <template x-if="selectedPlan && selectedPlan.trialDays > 0 && !selectedPlan.hasUsedTrial">
                            <p class="text-sm text-green-600 dark:text-green-400 font-medium mt-2">
                                <i class="fa-solid fa-gift mr-1"></i>
                                <span x-text="selectedPlan.trialDays + ' ' + (selectedPlan.trialDays === 1 ? 'день' : (selectedPlan.trialDays < 5 ? 'дня' : 'дней')) + ' пробного периода'"></span>
                            </p>
                        </template>
                        <template x-if="selectedPlan && selectedPlan.hasUsedTrial && selectedPlan.price > 0">
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-2">
                                <i class="fa-solid fa-info-circle mr-1"></i>
                                Пробный период уже использован
                            </p>
                        </template>
                    </div>
                    
                    <div class="bg-blue-50 dark:bg-blue-500/10 border border-blue-200 dark:border-blue-500/20 rounded-xl p-4">
                        <div class="flex items-start gap-3">
                            <i class="fa-solid fa-info-circle text-blue-600 dark:text-blue-400 mt-0.5"></i>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-blue-900 dark:text-blue-300 mb-1">Вы уверены, что хотите активировать этот тариф?</p>
                                <template x-if="selectedPlan && selectedPlan.price > 0">
                                    <p class="text-xs text-blue-700 dark:text-blue-400">
                                        Тариф будет активирован сразу после подтверждения.
                                        <template x-if="selectedPlan.trialDays > 0 && !selectedPlan.hasUsedTrial">
                                            <span> Пробный период начнется сразу.</span>
                                        </template>
                                        <template x-if="selectedPlan.hasUsedTrial">
                                            <span> Пробный период для этого тарифа уже был использован ранее.</span>
                                        </template>
                                    </p>
                                </template>
                                <template x-if="!selectedPlan || !selectedPlan.price || selectedPlan.price === 0">
                                    <p class="text-xs text-blue-700 dark:text-blue-400">
                                        Бесплатный тариф будет активирован сразу после подтверждения.
                                    </p>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Действия -->
            <div class="px-6 py-5 border-t border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800 flex items-center justify-end gap-3">
                <button @click="closeConfirmModal()"
                    class="px-4 py-2 text-sm font-semibold text-slate-700 dark:text-slate-300 bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-600 transition-colors">
                    Отмена
                </button>
                <button @click="confirmSubscription()"
                    class="px-6 py-2 text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg transition-colors shadow-md hover:shadow-lg">
                    <i class="fa-solid fa-check-circle mr-2"></i>
                    Подтвердить активацию
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Обработка отправки формы выбора тарифа
    const forms = document.querySelectorAll('.subscription-form');
    
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const button = form.querySelector('.subscription-submit-btn');
            const btnText = button?.querySelector('.btn-text');
            
            if (button && btnText) {
                // Показываем состояние загрузки
                button.disabled = true;
                if (btnText) {
                    btnText.innerHTML = '<svg class="animate-spin h-4 w-4 text-white inline-block mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Обработка...';
                }
            }
        });
    });
});
</script>
@endpush
    </div>
</div>

@endsection
