@extends('layouts.user')

@section('title', 'Текущая подписка - Cliently')
@section('page-title', 'Текущая подписка')
@section('page-description', 'Информация о вашем тарифе и использовании лимитов')

@push('breadcrumbs')
    <x-breadcrumbs :items="[['title' => 'Текущая подписка']]" />
@endpush

@section('content')

@php
    // Получаем бизнес и роль для проверки прав доступа
    $currentBusiness = null;
    $currentBusinessRole = null;
    $currentBusinessRoleId = null;
    $permissionService = null;
    if ($user) {
        $user->load('businesses');
        $currentBusiness = $user->businesses->first();
        if ($currentBusiness) {
            $pivot = $user->businesses()->where('business_id', $currentBusiness->id)->first();
            $currentBusinessRole = $pivot?->pivot->role ?? null;
            $currentBusinessRoleId = $pivot?->pivot->role_id;
            if ($currentBusinessRoleId) {
                $permissionService = app(\App\Services\BusinessRolePermissionService::class);
            }
        }
    }

    // Функция для проверки бизнес-прав
    $hasBusinessPermission = function($permission) use ($currentBusinessRoleId, $permissionService) {
        if (!$currentBusinessRoleId || !$permissionService) {
            return false;
        }
        return $permissionService->hasPermission($currentBusinessRoleId, $permission);
    };
@endphp

<div class="max-w-6xl mx-auto">
    <!-- Информация о текущем тарифе -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-lg overflow-hidden mb-6 sm:mb-8">
        <!-- Заголовок карточки -->
        <div class="px-6 sm:px-8 py-6 bg-gradient-to-r from-indigo-50 to-purple-50 dark:from-indigo-900/20 dark:to-purple-900/20 border-b border-slate-200 dark:border-slate-700">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h2 class="text-2xl sm:text-3xl font-bold text-slate-900 dark:text-white mb-2">{{ $plan->name }}</h2>
                    @if($plan->description)
                        <p class="text-sm sm:text-base text-slate-600 dark:text-slate-400">{{ $plan->description }}</p>
                    @endif
                </div>
                <div class="text-left sm:text-right">
                    @if($plan->price)
                        <div class="text-3xl sm:text-4xl font-bold text-slate-900 dark:text-white">{{ number_format($plan->price, 0, ',', ' ') }} BYN</div>
                        <div class="text-sm text-slate-600 dark:text-slate-400">{{ $plan->interval === 'monthly' ? 'в месяц' : 'в год' }}</div>
                    @else
                        <div class="text-3xl sm:text-4xl font-bold text-slate-900 dark:text-white">Бесплатно</div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Статус и информация -->
        <div class="px-6 sm:px-8 py-6">
            <!-- Статус подписки -->
            <div class="flex flex-wrap items-center gap-4 mb-6">
                <div class="flex items-center gap-2">
                    <span class="text-sm text-slate-600 dark:text-slate-400">Статус:</span>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-full
                        {{ $subscription->isCancelled() ? 'text-orange-700 bg-orange-100 dark:bg-orange-500/20 dark:text-orange-300' : ($subscription->status === 'active' ? 'text-green-700 bg-green-100 dark:bg-green-500/20 dark:text-green-300' : '') }}
                        {{ $subscription->status === 'trial' ? 'text-blue-700 bg-blue-100 dark:bg-blue-500/20 dark:text-blue-300' : '' }}">
                        @if($subscription->isCancelled())
                            <i class="fa-solid fa-exclamation-triangle"></i>
                            Будет отменена
                        @elseif($subscription->status === 'active')
                            <i class="fa-solid fa-check-circle"></i>
                            Активна
                        @elseif($subscription->status === 'trial')
                            <i class="fa-solid fa-clock"></i>
                            Пробный период
                        @endif
                    </span>
                </div>
                @if($subscription->starts_at)
                    <div class="flex items-center gap-2">
                        <span class="text-sm text-slate-600 dark:text-slate-400">Начало:</span>
                        <span class="text-sm font-medium text-slate-900 dark:text-white">
                            {{ $subscription->starts_at->format('d.m.Y') }}
                        </span>
                    </div>
                @endif
                @if($subscription->ends_at)
                    <div class="flex items-center gap-2">
                        <span class="text-sm text-slate-600 dark:text-slate-400">Действует до:</span>
                        <span class="text-sm font-medium text-slate-900 dark:text-white">
                            {{ $subscription->ends_at->format('d.m.Y') }}
                        </span>
                    </div>
                @endif
            </div>

            <!-- Предупреждение об отмене -->
            @if($subscription->isCancelled())
                <div class="mb-6 p-4 bg-orange-50 dark:bg-orange-500/10 rounded-xl border border-orange-200 dark:border-orange-500/20">
                    <div class="flex items-start gap-3">
                        <div class="shrink-0 w-10 h-10 rounded-full bg-orange-100 dark:bg-orange-500/20 flex items-center justify-center">
                            <i class="fa-solid fa-exclamation-triangle text-orange-600 dark:text-orange-400"></i>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-semibold text-orange-900 dark:text-orange-300 mb-1">Подписка отменена</p>
                            <p class="text-xs text-orange-700 dark:text-orange-400">
                                Подписка будет активна до {{ $subscription->ends_at->format('d.m.Y') }}. После этой даты доступ к платным функциям будет ограничен.
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Действия -->
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                <a href="{{ route('subscription.index') }}" 
                   class="flex-1 sm:flex-initial inline-flex items-center justify-center gap-2 px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-semibold transition-all duration-200 shadow-md hover:shadow-lg">
                    <i class="fa-solid fa-arrow-right"></i>
                    <span>Изменить тариф</span>
                </a>

                @if($hasBusinessPermission('client.subscription.manage') && !$subscription->isCancelled() && $plan->slug !== 'free')
                    <form action="{{ route('subscription.cancel') }}" method="POST" class="flex-1 sm:flex-initial">
                        @csrf
                        <button type="submit" onclick="return confirm(&quot;Вы уверены, что хотите отменить подписку? Она будет активна до окончания текущего периода ({{ $subscription->ends_at ? $subscription->ends_at->format('d.m.Y') : 'даты окончания' }}).&quot;)" 
                                class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3 bg-red-600 hover:bg-red-700 text-white rounded-xl font-semibold transition-all duration-200 shadow-md hover:shadow-lg">
                            <i class="fa-solid fa-times-circle"></i>
                            <span>Отменить подписку</span>
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>

    <!-- Использование лимитов -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-lg overflow-hidden">
        <div class="px-6 sm:px-8 py-5 sm:py-6 border-b border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800">
            <h3 class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-white">Использование лимитов</h3>
            <p class="text-sm text-slate-600 dark:text-slate-400 mt-1">Текущее использование ресурсов вашего тарифа</p>
        </div>
        
        <div class="px-6 sm:px-8 py-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                @foreach([
                    'locations' => ['icon' => 'fa-location-dot', 'label' => 'Локации', 'key' => 'max_locations'],
                    'masters' => ['icon' => 'fa-user-tie', 'label' => 'Мастера', 'key' => 'max_masters'],
                    'services' => ['icon' => 'fa-scissors', 'label' => 'Услуги', 'key' => 'max_services'],
                    'clients' => ['icon' => 'fa-users', 'label' => 'Клиенты', 'key' => 'max_clients'],
                    'appointments_per_month' => ['icon' => 'fa-calendar-check', 'label' => 'Записей в месяц', 'key' => 'max_appointments_per_month'],
                    'business_users' => ['icon' => 'fa-user-group', 'label' => 'Пользователи бизнеса', 'key' => 'max_business_users'],
                ] as $usageKey => $info)
                    @php
                        $current = $usage[$usageKey]['current'] ?? 0;
                        $limit = $usage[$usageKey]['limit'] ?? 0;
                        $percentage = $limit > 0 ? min(100, ($current / $limit) * 100) : 0;
                        $isUnlimited = $limit === -1;
                        $isWarning = $percentage >= 80 && $percentage < 100;
                        $isDanger = $percentage >= 100;
                    @endphp
                    <div class="p-4 bg-slate-50 dark:bg-slate-800/50 rounded-xl border border-slate-200 dark:border-slate-700">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-lg bg-indigo-100 dark:bg-indigo-500/20 flex items-center justify-center">
                                    <i class="fa-solid {{ $info['icon'] }} text-indigo-600 dark:text-indigo-400"></i>
                                </div>
                                <span class="text-sm font-semibold text-slate-900 dark:text-white">{{ $info['label'] }}</span>
                            </div>
                            <div class="text-right">
                                @if($isUnlimited)
                                    <div class="text-lg font-bold text-slate-900 dark:text-white">{{ number_format($current, 0, ',', ' ') }}</div>
                                    <div class="text-xs text-slate-500 dark:text-slate-400">Безлимит</div>
                                @else
                                    <div class="text-lg font-bold text-slate-900 dark:text-white">{{ number_format($current, 0, ',', ' ') }}</div>
                                    <div class="text-xs text-slate-500 dark:text-slate-400">из {{ number_format($limit, 0, ',', ' ') }}</div>
                                @endif
                            </div>
                        </div>
                        @if(!$isUnlimited)
                            <div class="w-full bg-slate-200 dark:bg-slate-700 rounded-full h-2.5 mb-2">
                                <div class="h-2.5 rounded-full transition-all duration-300
                                    {{ $isDanger ? 'bg-red-500' : ($isWarning ? 'bg-amber-500' : 'bg-indigo-600') }}"
                                    style="width: {{ $percentage }}%"></div>
                            </div>
                            <div class="flex items-center justify-between">
                                <div class="text-xs text-slate-500 dark:text-slate-400">
                                    Использовано {{ round($percentage) }}%
                                </div>
                                @if($isWarning)
                                    <div class="flex items-center gap-1 text-xs text-amber-600 dark:text-amber-400 font-medium">
                                        <i class="fa-solid fa-exclamation-triangle"></i>
                                        <span>Приближаетесь к лимиту</span>
                                    </div>
                                @elseif($isDanger)
                                    <div class="flex items-center gap-1 text-xs text-red-600 dark:text-red-400 font-medium">
                                        <i class="fa-solid fa-exclamation-circle"></i>
                                        <span>Лимит достигнут</span>
                                    </div>
                                @endif
                            </div>
                        @else
                            <div class="pt-2">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-indigo-100 text-indigo-700 dark:bg-indigo-500/20 dark:text-indigo-300">
                                    Безлимит
                                </span>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- История платежей -->
    @if($subscription && $subscription->invoices && $subscription->invoices->count() > 0)
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-lg overflow-hidden mt-6 sm:mt-8">
            <div class="px-6 sm:px-8 py-5 sm:py-6 border-b border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800">
                <h3 class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-white">История платежей</h3>
                <p class="text-sm text-slate-600 dark:text-slate-400 mt-1">Все платежи по вашей подписке</p>
            </div>
            
            <div class="px-6 sm:px-8 py-6">
                <div class="space-y-4">
                    @foreach($subscription->invoices->sortByDesc('created_at') as $invoice)
                        <div class="flex items-center justify-between p-4 bg-slate-50 dark:bg-slate-800/50 rounded-lg border border-slate-200 dark:border-slate-700">
                            <div class="flex items-center gap-4">
                                <div class="h-12 w-12 rounded-lg bg-indigo-100 dark:bg-indigo-500/20 flex items-center justify-center">
                                    <i class="fa-solid fa-receipt text-indigo-600 dark:text-indigo-400"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-slate-900 dark:text-white">Инвойс #{{ $invoice->id }}</p>
                                    <p class="text-xs text-slate-600 dark:text-slate-400">{{ $invoice->created_at->format('d.m.Y H:i') }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-base font-bold text-slate-900 dark:text-white">{{ number_format($invoice->amount, 2) }} {{ $invoice->currency }}</p>
                                @php
                                    $statusColors = [
                                        'pending' => 'text-amber-600 dark:text-amber-400',
                                        'paid' => 'text-emerald-600 dark:text-emerald-400',
                                        'failed' => 'text-rose-600 dark:text-rose-400',
                                        'cancelled' => 'text-slate-600 dark:text-slate-400',
                                        'refunded' => 'text-blue-600 dark:text-blue-400',
                                    ];
                                    $statusLabels = [
                                        'pending' => 'Ожидает',
                                        'paid' => 'Оплачено',
                                        'failed' => 'Ошибка',
                                        'cancelled' => 'Отменено',
                                        'refunded' => 'Возврат',
                                    ];
                                @endphp
                                <p class="text-xs font-medium {{ $statusColors[$invoice->status] ?? 'text-slate-600' }}">
                                    {{ $statusLabels[$invoice->status] ?? $invoice->status }}
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
</div>

@endsection

