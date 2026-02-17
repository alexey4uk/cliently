@extends('layouts.user')

@section('title', 'Главная - Cliently')
@section('page-title', 'Главная')
@section('page-description', 'Обзор вашего бизнеса')

@push('breadcrumbs')
    <x-breadcrumbs :items="[]" />
@endpush

@section('content')
@php
    $user = Auth::user();
    $currentBusiness = $business;
    $currentBusinessRole = null;
    $currentBusinessRoleId = null;
    $permissionService = null;
    if ($user && $currentBusiness) {
        $pivot = $user->businesses()->where('business_id', $currentBusiness->id)->first();
        $currentBusinessRole = $pivot?->pivot->role_id ? \App\Models\BusinessRole::find($pivot->pivot->role_id)?->slug : null;
        $currentBusinessRoleId = $pivot?->pivot->role_id;
        if ($currentBusinessRoleId) {
            $permissionService = app(\App\Services\BusinessRolePermissionService::class);
        }
    }

    $hasBusinessPermission = function($permission) use ($currentBusinessRoleId, $permissionService) {
        if (!$currentBusinessRoleId || !$permissionService) {
            return false;
        }
        return $permissionService->hasPermission($currentBusinessRoleId, $permission);
    };

    $hasOwnAppointmentsOnly = $hasBusinessPermission('client.appointments.view.own') 
        && !$hasBusinessPermission('client.appointments.view');
    $hasOwnClientsOnly = $hasBusinessPermission('client.clients.view.own') 
        && !$hasBusinessPermission('client.clients.view');
    
    $appointmentsTitle = $hasOwnAppointmentsOnly ? 'Мои записи' : 'Записи';
    $clientsTitle = $hasOwnClientsOnly ? 'Мои клиенты' : 'Клиенты';

    $hasAnalyticsAccess = false;
    if ($hasBusinessPermission('client.analytics.view') && $currentBusiness) {
        $accessService = app(\App\Services\SubscriptionAccessService::class);
        $hasAnalyticsAccess = $accessService->hasAccess($currentBusiness, 'analytics_enabled', 'client.analytics.view');
    }

    $canViewAppointments = $hasBusinessPermission('client.appointments.view') || $hasBusinessPermission('client.appointments.view.own');
    $canViewClients = $hasBusinessPermission('client.clients.view') || $hasBusinessPermission('client.clients.view.own');
    $canViewSubscription = $hasBusinessPermission('client.subscription.view');
@endphp

<div class="max-w-6xl 2xl:max-w-[1400px] mx-auto space-y-6">
    @if(!$currentBusiness)
    <div class="p-4 rounded-xl bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800/50">
        <p class="text-amber-800 dark:text-amber-200 font-medium">Добавьте бизнес в настройках, чтобы начать работу.</p>
        <p class="text-sm text-amber-700 dark:text-amber-300 mt-1">После создания бизнеса здесь появится обзор: записи, клиенты и статистика.</p>
        <div class="mt-3">
            <a href="{{ route('settings.businesses.index') }}" class="inline-flex items-center gap-2 px-3 py-2 text-sm font-medium text-white bg-amber-600 hover:bg-amber-700 rounded-lg transition-colors">
                <i class="fa-solid fa-briefcase"></i>
                <span>Управление бизнесами</span>
            </a>
        </div>
    </div>
    @endif

    {{-- Заголовок --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Главная</h1>
            <p class="text-slate-600 dark:text-slate-400 mt-1 text-sm">Обзор вашего бизнеса</p>
        </div>
        <form action="{{ route('dashboard.refresh') }}" method="POST" class="shrink-0">
            @csrf
            <button type="submit" class="inline-flex items-center gap-2 px-3 py-2 text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg text-sm transition-colors">
                <i class="fa-solid fa-rotate text-xs"></i>
                Обновить
            </button>
        </form>
    </div>

    {{-- Быстрые действия --}}
    @php
        $subscriptionService = app(\App\Services\SubscriptionService::class);
        $canCreateAppointment = $hasBusinessPermission('client.appointments.create') && $subscriptionService->canCreateAppointment($user);
        $canCreateClient = $hasBusinessPermission('client.clients.create') && $subscriptionService->canCreateClient($user);
        $canCreateService = $hasBusinessPermission('client.services.create') && $subscriptionService->canCreateService($user);
    @endphp
    <section>
        <h2 class="text-sm font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-3">Быстрые действия</h2>
        <div class="flex flex-wrap gap-3">
            @if($hasBusinessPermission('client.appointments.create'))
                @if($canCreateAppointment)
                    <a href="{{ route('appointments.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-sm font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                        <i class="fa-solid fa-calendar-plus text-indigo-500"></i>
                        Новая запись
                    </a>
                @else
                    <button disabled class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-800 rounded-xl text-sm font-medium text-slate-400 dark:text-slate-500 cursor-not-allowed" title="Достигнут месячный лимит записей для вашего тарифа.">
                        <i class="fa-solid fa-calendar-plus"></i>
                        Новая запись
                    </button>
                @endif
            @endif
            @if($hasBusinessPermission('client.clients.create'))
                @if($canCreateClient)
                    <a href="{{ route('clients.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-sm font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                        <i class="fa-solid fa-user-plus text-emerald-500"></i>
                        Новый клиент
                    </a>
                @else
                    <button disabled class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-800 rounded-xl text-sm font-medium text-slate-400 dark:text-slate-500 cursor-not-allowed" title="Достигнут лимит клиентов для вашего тарифа.">
                        <i class="fa-solid fa-user-plus"></i>
                        Новый клиент
                    </button>
                @endif
            @endif
            @if($hasBusinessPermission('client.services.create'))
                @if($canCreateService)
                    <a href="{{ route('services.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-sm font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                        <i class="fa-solid fa-briefcase text-amber-500"></i>
                        Новая услуга
                    </a>
                @else
                    <button disabled class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-800 rounded-xl text-sm font-medium text-slate-400 dark:text-slate-500 cursor-not-allowed" title="Достигнут лимит услуг для вашего тарифа.">
                        <i class="fa-solid fa-briefcase"></i>
                        Новая услуга
                    </button>
                @endif
            @endif
            @if($hasBusinessPermission('client.appointments.view'))
                <a href="{{ route('appointments.calendar') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-sm font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                    <i class="fa-solid fa-calendar-days text-violet-500"></i>
                    Календарь
                </a>
            @endif
        </div>
    </section>

    {{-- Ключевые показатели --}}
    @php
        $statsCount = ($canViewAppointments ? 2 : 0) + ($canViewClients ? 1 : 0) + ($canViewSubscription && isset($subscriptionStatus) ? 1 : 0);
        $gridCols = $statsCount <= 2 ? 'grid-cols-2' : ($statsCount === 3 ? 'grid-cols-3' : 'grid-cols-2 lg:grid-cols-4');
    @endphp

    @if($statsCount > 0)
    <section>
        <h2 class="sr-only">Ключевые показатели</h2>
        <div class="grid {{ $gridCols }} gap-4">
            @if($canViewAppointments)
            <a href="{{ route('appointments.index') }}" class="block p-4 bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 hover:border-indigo-300 dark:hover:border-indigo-700 transition-colors">
                <div class="flex items-center gap-3">
                    <div class="p-2 rounded-lg bg-indigo-100 dark:bg-indigo-500/20 text-indigo-600 dark:text-indigo-400">
                        <i class="fa-solid fa-calendar-day text-lg"></i>
                    </div>
                    <div class="min-w-0">
                        <p class="text-2xl font-bold text-slate-900 dark:text-white tabular-nums">{{ $stats['appointments_today'] ?? 0 }}</p>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Записей сегодня</p>
                    </div>
                </div>
                @if(isset($stats['appointments_week']) && $stats['appointments_week'] > 0)
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-2">{{ $stats['appointments_week'] }} за неделю</p>
                @endif
            </a>
            @endif

            @if($canViewClients)
            <a href="{{ route('clients.index') }}" class="block p-4 bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 hover:border-emerald-300 dark:hover:border-emerald-700 transition-colors">
                <div class="flex items-center gap-3">
                    <div class="p-2 rounded-lg bg-emerald-100 dark:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400">
                        <i class="fa-solid fa-users text-lg"></i>
                    </div>
                    <div class="min-w-0">
                        <p class="text-2xl font-bold text-slate-900 dark:text-white tabular-nums">{{ number_format($stats['total_clients'] ?? 0, 0, ',', ' ') }}</p>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Клиентов</p>
                    </div>
                </div>
                @if(isset($stats['new_clients_week']) && $stats['new_clients_week'] > 0)
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-2">+{{ $stats['new_clients_week'] }} за неделю</p>
                @endif
            </a>
            @endif

            @if($canViewAppointments)
            <div class="p-4 bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800">
                <div class="flex items-center gap-3">
                    <div class="p-2 rounded-lg bg-amber-100 dark:bg-amber-500/20 text-amber-600 dark:text-amber-400">
                        <i class="fa-solid fa-check-circle text-lg"></i>
                    </div>
                    <div class="min-w-0">
                        <p class="text-2xl font-bold text-slate-900 dark:text-white tabular-nums">{{ $stats['completed_month'] ?? 0 }}</p>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Завершено за месяц</p>
                    </div>
                </div>
                @if(isset($stats['completed_week']) && $stats['completed_week'] > 0)
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-2">{{ $stats['completed_week'] }} за неделю</p>
                @endif
            </div>
            @endif

            @if($canViewSubscription && isset($subscriptionStatus))
            <a href="{{ route('subscription.current') }}" class="block p-4 bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 hover:border-violet-300 dark:hover:border-violet-700 transition-colors">
                <div class="flex items-center gap-3">
                    <div class="p-2 rounded-lg bg-violet-100 dark:bg-violet-500/20 text-violet-600 dark:text-violet-400">
                        <i class="fa-solid fa-credit-card text-lg"></i>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-base font-bold text-slate-900 dark:text-white truncate">{{ $subscriptionStatus['plan_name'] }}</p>
                        <p class="text-xs text-slate-500 dark:text-slate-400 truncate">
                            @if(($subscriptionStatus['plan_price'] ?? 0) > 0)
                                {{ number_format($subscriptionStatus['plan_price'], 0, ',', ' ') }} BYN
                                @if(!empty($subscriptionStatus['ends_at']))
                                    · до {{ \Carbon\Carbon::parse($subscriptionStatus['ends_at'])->format('d.m.Y') }}
                                @endif
                            @else
                                @if(!empty($subscriptionStatus['ends_at']))
                                    до {{ \Carbon\Carbon::parse($subscriptionStatus['ends_at'])->format('d.m.Y') }}
                                @else
                                    Бессрочно
                                @endif
                            @endif
                        </p>
                        @if($hasBusinessPermission('client.subscription.manage') && isset($subscriptionStatus['is_cancelled']) && $subscriptionStatus['is_cancelled'])
                            <span class="inline-block text-xs font-medium text-amber-600 dark:text-amber-400 mt-1">Отменена</span>
                        @endif
                    </div>
                    <i class="fa-solid fa-chevron-right text-slate-400 text-xs"></i>
                </div>
            </a>
            @endif
        </div>
    </section>
    @endif

    {{-- Основной контент: записи и клиенты --}}
    <section class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Записи на сегодня --}}
        @if($canViewAppointments)
        <div class="lg:col-span-2 bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 overflow-hidden">
            <div class="px-4 py-3 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between">
                <h2 class="text-sm font-semibold text-slate-900 dark:text-white">{{ $appointmentsTitle }} на сегодня</h2>
                @if($hasBusinessPermission('client.appointments.view') || $hasBusinessPermission('client.appointments.view.own'))
                <a href="{{ route('appointments.index') }}" class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline">Все →</a>
                @endif
            </div>
            <div class="p-4">
                @if(isset($appointments['upcoming']) && $appointments['upcoming']->isNotEmpty())
                    <ul class="space-y-3">
                        @foreach($appointments['upcoming']->take(5) as $appointment)
                            <li>
                                <a href="{{ route('appointments.show', $appointment->id) }}" class="flex items-center gap-3 p-3 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors group">
                                    <div class="shrink-0 w-14 text-center">
                                        <p class="text-base font-bold text-slate-900 dark:text-white">{{ \Carbon\Carbon::parse($appointment->time)->format('H:i') }}</p>
                                        @if($appointment->service->duration)
                                            <p class="text-xs text-slate-500 dark:text-slate-400">
                                                {{ \Carbon\Carbon::parse($appointment->time)->addMinutes($appointment->service->duration)->format('H:i') }}
                                            </p>
                                        @endif
                                    </div>
                                    <div class="shrink-0 w-px h-12 bg-indigo-200 dark:bg-indigo-800"></div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-slate-900 dark:text-white truncate">{{ $appointment->client->full_name }}</p>
                                        <p class="text-xs text-slate-500 dark:text-slate-400 truncate">{{ $appointment->service->name }}</p>
                                    </div>
                                    <span class="shrink-0 px-2 py-1 text-xs font-medium text-emerald-700 dark:text-emerald-400 bg-emerald-100 dark:bg-emerald-500/20 rounded-full">Подтверждено</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @elseif(isset($appointments['pending']) && $appointments['pending']->isNotEmpty())
                    <ul class="space-y-3">
                        @foreach($appointments['pending']->take(5) as $appointment)
                            <li>
                                <a href="{{ route('appointments.show', $appointment->id) }}" class="flex items-center gap-3 p-3 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors group">
                                    <div class="shrink-0 w-14 text-center">
                                        <p class="text-sm font-bold text-slate-900 dark:text-white">{{ \Carbon\Carbon::parse($appointment->date)->format('d.m') }}</p>
                                        <p class="text-xs text-slate-500 dark:text-slate-400">{{ \Carbon\Carbon::parse($appointment->time)->format('H:i') }}</p>
                                    </div>
                                    <div class="shrink-0 w-px h-12 bg-amber-200 dark:bg-amber-800"></div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-slate-900 dark:text-white truncate">{{ $appointment->client->full_name }}</p>
                                        <p class="text-xs text-slate-500 dark:text-slate-400 truncate">{{ $appointment->service->name }}</p>
                                    </div>
                                    <span class="shrink-0 px-2 py-1 text-xs font-medium text-amber-700 dark:text-amber-400 bg-amber-100 dark:bg-amber-500/20 rounded-full">Ожидает</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <div class="text-center py-8">
                        <div class="h-16 w-16 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center mx-auto mb-3">
                            <i class="fa-solid fa-calendar-day text-2xl text-slate-400 dark:text-slate-500"></i>
                        </div>
                        <h4 class="text-sm font-semibold text-slate-900 dark:text-white mb-1">Нет записей на сегодня</h4>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mb-3">Создайте новую запись для начала работы</p>
                        @if($hasBusinessPermission('client.appointments.create') && $canCreateAppointment)
                        <a href="{{ route('appointments.create') }}" class="inline-flex items-center gap-2 px-3 py-1.5 bg-indigo-600 text-white rounded-lg text-xs font-medium hover:bg-indigo-700 transition-colors">
                            <i class="fa-solid fa-plus text-xs"></i>
                            <span>Новая запись</span>
                        </a>
                        @endif
                    </div>
                @endif
            </div>
        </div>
        @endif

        {{-- Последние клиенты --}}
        @if($canViewClients)
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 overflow-hidden">
            <div class="px-4 py-3 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between">
                <h2 class="text-sm font-semibold text-slate-900 dark:text-white">{{ $clientsTitle }}</h2>
                @if($hasBusinessPermission('client.clients.view') || $hasBusinessPermission('client.clients.view.own'))
                <a href="{{ route('clients.index') }}" class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline">Все →</a>
                @endif
            </div>
            <div class="p-4">
                @if(($clients ?? collect())->isNotEmpty())
                    <ul class="space-y-3">
                        @foreach($clients as $client)
                            <li>
                                <a href="{{ route('clients.show', $client->id) }}" class="flex items-center gap-3 p-2 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($client->full_name) }}&background=6366f1&color=fff&size=40" alt="" class="w-10 h-10 rounded-full shrink-0">
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-slate-900 dark:text-white truncate">{{ $client->full_name }}</p>
                                        <p class="text-xs text-slate-500 dark:text-slate-400 truncate">{{ $client->phone }}</p>
                                    </div>
                                    <span class="text-xs text-slate-400 dark:text-slate-500 shrink-0">{{ $client->created_at->locale('ru')->diffForHumans() }}</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <div class="text-center py-8">
                        <div class="h-16 w-16 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center mx-auto mb-3">
                            <i class="fa-solid fa-users text-2xl text-slate-400 dark:text-slate-500"></i>
                        </div>
                        <h4 class="text-sm font-semibold text-slate-900 dark:text-white mb-1">Нет клиентов</h4>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mb-3">Добавьте первого клиента</p>
                        @if($hasBusinessPermission('client.clients.create') && $canCreateClient)
                            <a href="{{ route('clients.create') }}" class="inline-flex items-center gap-2 px-3 py-1.5 bg-indigo-600 text-white rounded-lg text-xs font-medium hover:bg-indigo-700 transition-colors">
                                <i class="fa-solid fa-plus text-xs"></i>
                                <span>Новый клиент</span>
                            </a>
                        @endif
                    </div>
                @endif
            </div>
        </div>
        @endif
    </section>

    {{-- Ссылка на аналитику --}}
    @if($hasAnalyticsAccess)
    <section>
        <a href="{{ route('analytics.index') }}" class="flex items-center justify-between p-4 bg-slate-100 dark:bg-slate-800/50 rounded-xl border border-slate-200 dark:border-slate-700 hover:bg-slate-200/50 dark:hover:bg-slate-800 transition-colors group">
            <div class="flex items-center gap-3">
                <div class="p-2 rounded-lg bg-indigo-100 dark:bg-indigo-500/20 text-indigo-600 dark:text-indigo-400">
                    <i class="fa-solid fa-chart-line"></i>
                </div>
                <div>
                    <span class="font-medium text-slate-900 dark:text-white">Детальная аналитика</span>
                    <span class="text-sm text-slate-500 dark:text-slate-400 block">Графики, финансовые метрики и топ услуги/мастера</span>
                </div>
            </div>
            <i class="fa-solid fa-arrow-right text-slate-400 group-hover:text-indigo-500 transition-colors"></i>
        </a>
    </section>
    @endif
</div>

@endsection
