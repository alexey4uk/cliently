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

<div class="max-w-4xl mx-auto">
    <!-- Информация о тарифе -->
    <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-8 mb-6">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-slate-900 dark:text-white mb-2">{{ $plan->name }}</h1>
            @if($plan->description)
                <p class="text-lg text-slate-600 dark:text-slate-400">{{ $plan->description }}</p>
            @endif
            <div class="mt-4">
                @if($plan->price)
                    <div class="flex items-baseline justify-center gap-2">
                        <span class="text-5xl font-bold text-slate-900 dark:text-white">{{ number_format($plan->price, 0, ',', ' ') }}</span>
                        <span class="text-xl text-slate-600 dark:text-slate-400">BYN/{{ $plan->interval === 'monthly' ? 'мес' : 'год' }}</span>
                    </div>
                @else
                    <span class="text-5xl font-bold text-slate-900 dark:text-white">Бесплатно</span>
                @endif
            </div>
        </div>

        <!-- Сравнение с текущим тарифом -->
        @if($currentPlan && $currentPlan->id !== $plan->id)
            <div class="bg-blue-50 dark:bg-blue-500/10 border border-blue-200 dark:border-blue-500/20 rounded-lg p-4 mb-6">
                <div class="flex items-start gap-3">
                    <i class="fa-solid fa-info-circle text-blue-600 dark:text-blue-400 mt-0.5"></i>
                    <div>
                        <h4 class="font-semibold text-blue-900 dark:text-blue-300 mb-1">Смена тарифа</h4>
                        <p class="text-sm text-blue-700 dark:text-blue-400">
                            Вы переходите с тарифа "{{ $currentPlan->name }}" на "{{ $plan->name }}".
                            @if($plan->price && $plan->price > ($currentPlan->price ?? 0))
                                Новый тариф будет активирован сразу.
                            @else
                                Обратите внимание, что при переходе на более низкий тариф некоторые функции могут стать недоступны.
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Метрики тарифа -->
        <div class="space-y-4">
            <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">Что включено в тариф:</h3>
            @foreach($metrics as $metric)
                @php
                    $value = $plan->getFeatureValue($metric->key);
                @endphp
                @if($value !== null)
                    <div class="flex items-start gap-4 p-4 bg-slate-50 dark:bg-slate-800/50 rounded-lg">
                        <div class="h-10 w-10 rounded-lg bg-indigo-100 dark:bg-indigo-500/20 flex items-center justify-center flex-shrink-0">
                            <i class="{{ $metric->icon ?? 'fa-solid fa-circle' }} text-indigo-600 dark:text-indigo-400"></i>
                        </div>
                        <div class="flex-1">
                            <div class="flex items-center justify-between mb-1">
                                <h4 class="font-semibold text-slate-900 dark:text-white">{{ $metric->label }}</h4>
                                <span class="text-sm font-semibold text-slate-700 dark:text-slate-300">
                                    @if($value === -1)
                                        Безлимит
                                    @elseif($value === true)
                                        Включено
                                    @elseif($value === false)
                                        Отключено
                                    @else
                                        {{ number_format($value, 0, ',', ' ') }}
                                    @endif
                                </span>
                            </div>
                            @if($metric->description)
                                <p class="text-sm text-slate-600 dark:text-slate-400">{{ $metric->description }}</p>
                            @endif
                        </div>
                    </div>
                @endif
            @endforeach
        </div>

        <!-- Пробный период -->
        @if($plan->trial_days > 0 && $plan->price)
            <div class="mt-6 p-4 bg-green-50 dark:bg-green-500/10 border border-green-200 dark:border-green-500/20 rounded-lg">
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-gift text-green-600 dark:text-green-400"></i>
                    <span class="text-sm font-medium text-green-900 dark:text-green-300">
                        Пробный период {{ $plan->trial_days }} {{ $plan->trial_days === 1 ? 'день' : ($plan->trial_days < 5 ? 'дня' : 'дней') }}
                    </span>
                </div>
            </div>
        @endif
    </div>

    <!-- Кнопка оформления -->
    <div class="flex items-center justify-between gap-4">
        <a href="{{ route('subscription.index') }}" 
           class="inline-flex items-center gap-2 px-4 py-2 text-slate-700 dark:text-slate-300 hover:text-slate-900 dark:hover:text-slate-100 transition-colors">
            <i class="fa-solid fa-arrow-left"></i>
            <span>Назад к тарифам</span>
        </a>

        @if($currentPlan && $currentPlan->id === $plan->id)
            <button type="button" disabled
                class="inline-flex items-center gap-2 px-6 py-3 bg-slate-200 dark:bg-slate-700 text-slate-500 dark:text-slate-400 rounded-lg font-medium cursor-not-allowed">
                <i class="fa-solid fa-check-circle"></i>
                <span>Текущий тариф</span>
            </button>
        @else
            <form action="{{ route('subscription.subscribe', $plan) }}" method="POST">
                @csrf
                <button type="submit"
                    class="inline-flex items-center gap-2 px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium transition-colors">
                    <i class="fa-solid fa-check"></i>
                    <span>Оформить подписку</span>
                </button>
            </form>
        @endif
    </div>
</div>

@endsection
