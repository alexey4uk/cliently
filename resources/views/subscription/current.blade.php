@extends('layouts.user')

@section('title', 'Текущая подписка - Cliently')
@section('page-title', 'Текущая подписка')
@section('page-description', 'Информация о вашем тарифе и использовании лимитов')

@push('breadcrumbs')
    <x-breadcrumbs :items="[['title' => 'Текущая подписка']]" />
@endpush

@section('content')

<div class="max-w-6xl mx-auto">
    <!-- Информация о текущем тарифе -->
    <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-6 mb-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-2xl font-bold text-slate-900 dark:text-white mb-2">{{ $plan->name }}</h2>
                <p class="text-slate-600 dark:text-slate-400">{{ $plan->description }}</p>
            </div>
            <div class="text-right">
                @if($plan->price)
                    <div class="text-3xl font-bold text-slate-900 dark:text-white">{{ number_format($plan->price, 0, ',', ' ') }} BYN</div>
                    <div class="text-sm text-slate-600 dark:text-slate-400">{{ $plan->interval === 'monthly' ? 'в месяц' : 'в год' }}</div>
                @else
                    <div class="text-3xl font-bold text-slate-900 dark:text-white">Бесплатно</div>
                @endif
            </div>
        </div>

        <!-- Статус подписки -->
        <div class="flex items-center gap-4 mb-6">
            <div>
                <span class="text-sm text-slate-600 dark:text-slate-400">Статус:</span>
                <span class="ml-2 inline-flex items-center gap-1.5 px-3 py-1 text-xs font-semibold rounded-full
                    {{ $subscription->status === 'active' ? 'text-green-700 bg-green-100 dark:bg-green-500/20 dark:text-green-300' : '' }}
                    {{ $subscription->status === 'trial' ? 'text-blue-700 bg-blue-100 dark:bg-blue-500/20 dark:text-blue-300' : '' }}">
                    @if($subscription->status === 'active')
                        <i class="fa-solid fa-check-circle"></i>
                        Активна
                    @elseif($subscription->status === 'trial')
                        <i class="fa-solid fa-clock"></i>
                        Пробный период
                    @endif
                </span>
            </div>
            @if($subscription->ends_at)
                <div>
                    <span class="text-sm text-slate-600 dark:text-slate-400">Действует до:</span>
                    <span class="ml-2 text-sm font-medium text-slate-900 dark:text-white">
                        {{ $subscription->ends_at->format('d.m.Y') }}
                    </span>
                </div>
            @endif
        </div>

        <a href="{{ route('subscription.index') }}" 
           class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium transition-colors">
            <i class="fa-solid fa-arrow-right"></i>
            <span>Изменить тариф</span>
        </a>
    </div>

    <!-- Использование лимитов -->
    <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-6">
        <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-6">Использование лимитов</h3>
        
        <div class="space-y-6">
            @foreach([
                'locations' => ['icon' => 'fa-location-dot', 'label' => 'Локации', 'key' => 'max_locations'],
                'masters' => ['icon' => 'fa-user-tie', 'label' => 'Мастера', 'key' => 'max_masters'],
                'services' => ['icon' => 'fa-scissors', 'label' => 'Услуги', 'key' => 'max_services'],
                'clients' => ['icon' => 'fa-users', 'label' => 'Клиенты', 'key' => 'max_clients'],
                'appointments_per_month' => ['icon' => 'fa-calendar-check', 'label' => 'Записей в месяц', 'key' => 'max_appointments_per_month'],
                'business_users' => ['icon' => 'fa-users', 'label' => 'Пользователи бизнеса', 'key' => 'max_business_users'],
            ] as $usageKey => $info)
                @php
                    $current = $usage[$usageKey]['current'] ?? 0;
                    $limit = $usage[$usageKey]['limit'] ?? 0;
                    $percentage = $limit > 0 ? min(100, ($current / $limit) * 100) : 0;
                    $isUnlimited = $limit === -1;
                    $isWarning = $percentage >= 80 && $percentage < 100;
                    $isDanger = $percentage >= 100;
                @endphp
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center gap-3">
                            <i class="fa-solid {{ $info['icon'] }} text-slate-500 dark:text-slate-400"></i>
                            <span class="text-sm font-medium text-slate-900 dark:text-white">{{ $info['label'] }}</span>
                        </div>
                        <div class="text-sm text-slate-600 dark:text-slate-400">
                            @if($isUnlimited)
                                <span class="font-semibold text-slate-900 dark:text-white">{{ number_format($current, 0, ',', ' ') }}</span>
                                <span class="ml-1">/ Безлимит</span>
                            @else
                                <span class="font-semibold text-slate-900 dark:text-white">{{ number_format($current, 0, ',', ' ') }}</span>
                                <span class="ml-1">/ {{ number_format($limit, 0, ',', ' ') }}</span>
                            @endif
                        </div>
                    </div>
                    @if(!$isUnlimited)
                        <div class="w-full bg-slate-200 dark:bg-slate-700 rounded-full h-2.5">
                            <div class="h-2.5 rounded-full transition-all duration-300
                                {{ $isDanger ? 'bg-red-500' : ($isWarning ? 'bg-amber-500' : 'bg-indigo-600') }}"
                                style="width: {{ $percentage }}%"></div>
                        </div>
                        @if($isWarning)
                            <p class="text-xs text-amber-600 dark:text-amber-400 mt-1">
                                <i class="fa-solid fa-exclamation-triangle"></i>
                                Приближаетесь к лимиту
                            </p>
                        @elseif($isDanger)
                            <p class="text-xs text-red-600 dark:text-red-400 mt-1">
                                <i class="fa-solid fa-exclamation-circle"></i>
                                Лимит достигнут. Обновите тариф для увеличения лимита.
                            </p>
                        @endif
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</div>

@endsection
