@extends('layouts.user')

@section('title', 'Главная - Cliently')
@section('page-title', 'Главная')
@section('page-description', 'Обзор вашего бизнеса')

@push('breadcrumbs')
    <x-breadcrumbs :items="[]" />
@endpush

@section('content')

@php
    // Получаем бизнес и роль для проверки прав доступа
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

    // Функция для проверки бизнес-прав
    $hasBusinessPermission = function($permission) use ($currentBusinessRoleId, $permissionService) {
        if (!$currentBusinessRoleId || !$permissionService) {
            return false;
        }
        return $permissionService->hasPermission($currentBusinessRoleId, $permission);
    };

    // Определяем, есть ли право только на свои данные
    $hasOwnAppointmentsOnly = $hasBusinessPermission('client.appointments.view.own') 
        && !$hasBusinessPermission('client.appointments.view');
    $hasOwnClientsOnly = $hasBusinessPermission('client.clients.view.own') 
        && !$hasBusinessPermission('client.clients.view');
    
    // Определяем заголовки
    $appointmentsTitle = $hasOwnAppointmentsOnly ? 'Мои записи' : 'Записи';
    $clientsTitle = $hasOwnClientsOnly ? 'Мои клиенты' : 'Клиенты';

    // Проверяем доступ к аналитике
    $hasAnalyticsAccess = false;
    if ($hasBusinessPermission('client.analytics.view') && $currentBusiness) {
        $accessService = app(\App\Services\SubscriptionAccessService::class);
        $hasAnalyticsAccess = $accessService->hasAccess($currentBusiness, 'analytics_enabled', 'client.analytics.view');
    }

    // Определяем доступные виджеты
    $canViewAppointments = $hasBusinessPermission('client.appointments.view') || $hasBusinessPermission('client.appointments.view.own');
    $canViewClients = $hasBusinessPermission('client.clients.view') || $hasBusinessPermission('client.clients.view.own');
    $canViewServices = $hasBusinessPermission('client.services.view');
    $canViewMasters = $hasBusinessPermission('client.masters.view');
    $canViewLocations = $hasBusinessPermission('client.locations.view');
    $canViewSubscription = $hasBusinessPermission('client.subscription.view');
@endphp

<div class="max-w-6xl 2xl:max-w-[1400px] mx-auto">
    <!-- Page Header -->
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Dashboard</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">Обзор вашего бизнеса</p>
        </div>
        <form action="{{ route('dashboard.refresh') }}" method="POST">
            @csrf
            <button type="submit" 
                    class="inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-slate-900 border border-gray-200 dark:border-slate-800 text-gray-700 dark:text-gray-300 rounded-lg text-sm font-medium hover:bg-gray-50 dark:hover:bg-slate-800 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                <span>Обновить</span>
            </button>
        </form>
    </div>

    <!-- Stats Cards: записи сегодня, клиенты, завершено за месяц -->
    @php
        $statsCount = ($canViewAppointments ? 2 : 0) + ($canViewClients ? 1 : 0);
        $gridCols = $statsCount <= 2 ? 'grid-cols-1 sm:grid-cols-2' : 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-3';
    @endphp

    @if($statsCount > 0)
    <div class="grid {{ $gridCols }} gap-4 sm:gap-6 mb-6">
        @if($canViewAppointments)
        <div class="group relative bg-gradient-to-br from-indigo-50 to-indigo-100 dark:from-indigo-900/20 dark:to-indigo-800/10 rounded-2xl p-5 border border-indigo-200/50 dark:border-indigo-800/50 shadow-sm hover:shadow-md transition-all duration-300 overflow-hidden">
            <div class="absolute top-0 right-0 w-28 h-28 bg-indigo-200/30 dark:bg-indigo-800/20 rounded-full -mr-14 -mt-14 blur-2xl"></div>
            <div class="relative flex items-start gap-4">
                <div class="p-2.5 bg-indigo-500/10 dark:bg-indigo-500/20 rounded-xl shrink-0">
                    <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-xs font-medium text-slate-500 dark:text-slate-400">Записей сегодня</p>
                    <p class="text-2xl sm:text-3xl font-bold text-slate-900 dark:text-white mt-0.5">{{ $stats['appointments_today'] ?? 0 }}</p>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                        @if(isset($stats['appointments_week']) && $stats['appointments_week'] > 0)
                            <span class="font-medium text-indigo-600 dark:text-indigo-400">{{ $stats['appointments_week'] }}</span> за неделю
                        @else
                            Нет записей
                        @endif
                    </p>
                </div>
            </div>
        </div>
        @endif

        @if($canViewClients)
        <div class="group relative bg-gradient-to-br from-emerald-50 to-emerald-100 dark:from-emerald-900/20 dark:to-emerald-800/10 rounded-2xl p-5 border border-emerald-200/50 dark:border-emerald-800/50 shadow-sm hover:shadow-md transition-all duration-300 overflow-hidden">
            <div class="absolute top-0 right-0 w-28 h-28 bg-emerald-200/30 dark:bg-emerald-800/20 rounded-full -mr-14 -mt-14 blur-2xl"></div>
            <div class="relative flex items-start gap-4">
                <div class="p-2.5 bg-emerald-500/10 dark:bg-emerald-500/20 rounded-xl shrink-0">
                    <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-xs font-medium text-slate-500 dark:text-slate-400">Клиентов</p>
                    <p class="text-2xl sm:text-3xl font-bold text-slate-900 dark:text-white mt-0.5">{{ number_format($stats['total_clients'] ?? 0, 0, ',', ' ') }}</p>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                        @if(isset($stats['new_clients_week']) && $stats['new_clients_week'] > 0)
                            <span class="font-medium text-emerald-600 dark:text-emerald-400">+{{ $stats['new_clients_week'] }}</span> за неделю
                        @else
                            Нет новых за неделю
                        @endif
                    </p>
                </div>
            </div>
        </div>
        @endif

        @if($canViewAppointments)
        <div class="group relative bg-gradient-to-br from-amber-50 to-amber-100 dark:from-amber-900/20 dark:to-amber-800/10 rounded-2xl p-5 border border-amber-200/50 dark:border-amber-800/50 shadow-sm hover:shadow-md transition-all duration-300 overflow-hidden">
            <div class="absolute top-0 right-0 w-28 h-28 bg-amber-200/30 dark:bg-amber-800/20 rounded-full -mr-14 -mt-14 blur-2xl"></div>
            <div class="relative flex items-start gap-4">
                <div class="p-2.5 bg-amber-500/10 dark:bg-amber-500/20 rounded-xl shrink-0">
                    <svg class="w-5 h-5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-xs font-medium text-slate-500 dark:text-slate-400">Завершено за месяц</p>
                    <p class="text-2xl sm:text-3xl font-bold text-slate-900 dark:text-white mt-0.5">{{ $stats['completed_month'] ?? 0 }}</p>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                        @if(isset($stats['completed_week']) && $stats['completed_week'] > 0)
                            <span class="font-medium text-amber-600 dark:text-amber-400">{{ $stats['completed_week'] }}</span> за неделю
                        @else
                            Нет завершенных
                        @endif
                    </p>
                </div>
            </div>
        </div>
        @endif
    </div>
    @endif

    <!-- Main Content Grid - На всю ширину -->
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <!-- Today's Schedule -->
        @if($canViewAppointments)
        <div class="xl:col-span-2 bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-gray-200 dark:border-slate-800">
            <div class="p-5 border-b border-gray-200 dark:border-slate-800 flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $appointmentsTitle }} на сегодня</h2>
                @if($hasBusinessPermission('client.appointments.view') || $hasBusinessPermission('client.appointments.view.own'))
                <a href="{{ route('appointments.index') }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 font-medium">Все записи →</a>
                @endif
            </div>
            <div class="p-5">
                @if(isset($appointments['upcoming']) && $appointments['upcoming']->isNotEmpty())
                    <div class="space-y-4">
                        @foreach($appointments['upcoming']->take(4) as $appointment)
                            <div class="flex items-center p-4 bg-gray-50 dark:bg-slate-800 rounded-lg">
                                <div class="shrink-0 w-16 text-center">
                                    <p class="text-lg font-bold text-gray-900 dark:text-white">{{ \Carbon\Carbon::parse($appointment->time)->format('H:i') }}</p>
                                    @if($appointment->service->duration)
                                        <p class="text-xs text-gray-500 dark:text-gray-400">
                                            {{ \Carbon\Carbon::parse($appointment->time)->addMinutes($appointment->service->duration)->format('H:i') }}
                                        </p>
                                    @endif
                                </div>
                                <div class="shrink-0 w-px h-12 bg-indigo-500 mx-4"></div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $appointment->client->full_name }}</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $appointment->service->name }}</p>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <span class="px-2.5 py-1 text-xs font-medium text-green-700 dark:text-green-300 bg-green-100 dark:bg-green-500/20 rounded-full">Подтверждено</span>
                                    <a href="{{ route('appointments.show', $appointment->id) }}" class="p-1.5 text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-slate-700">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path>
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @elseif(isset($appointments['pending']) && $appointments['pending']->isNotEmpty())
                    <div class="space-y-4">
                        @foreach($appointments['pending']->take(4) as $appointment)
                            <div class="flex items-center p-4 bg-gray-50 dark:bg-slate-800 rounded-lg">
                                <div class="shrink-0 w-16 text-center">
                                    <p class="text-lg font-bold text-gray-900 dark:text-white">{{ \Carbon\Carbon::parse($appointment->date)->format('d.m') }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ \Carbon\Carbon::parse($appointment->time)->format('H:i') }}</p>
                                </div>
                                <div class="shrink-0 w-px h-12 bg-yellow-500 mx-4"></div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $appointment->client->full_name }}</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $appointment->service->name }}</p>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <span class="px-2.5 py-1 text-xs font-medium text-yellow-700 dark:text-yellow-300 bg-yellow-100 dark:bg-yellow-500/20 rounded-full">Ожидает</span>
                                    <a href="{{ route('appointments.show', $appointment->id) }}" class="p-1.5 text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-slate-700">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path>
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12">
                        <div class="h-20 w-20 rounded-full bg-gray-100 dark:bg-slate-800 flex items-center justify-center mx-auto mb-4">
                            <svg class="w-10 h-10 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <h4 class="text-base font-semibold text-gray-900 dark:text-white mb-2">Нет записей на сегодня</h4>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Создайте новую запись для начала работы</p>
                        <a href="{{ route('appointments.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            <span>Новая запись</span>
                        </a>
                    </div>
                @endif
            </div>
        </div>
        @endif

        <!-- Recent Clients -->
        @if($canViewClients)
        <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-gray-200 dark:border-slate-800">
            <div class="p-5 border-b border-gray-200 dark:border-slate-800 flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $clientsTitle }}</h2>
                @if($hasBusinessPermission('client.clients.view') || $hasBusinessPermission('client.clients.view.own'))
                <a href="{{ route('clients.index') }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 font-medium">Все →</a>
                @endif
            </div>
            <div class="p-5">
                @if(($clients ?? collect())->isNotEmpty())
                    <div class="space-y-4">
                        @foreach($clients as $client)
                            <div class="flex items-center">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($client->full_name) }}&background=6366f1&color=fff" alt="" class="w-10 h-10 rounded-full">
                                <div class="ml-3 flex-1">
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $client->full_name }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $client->phone }}</p>
                                </div>
                                <span class="text-xs text-gray-400 dark:text-gray-500">{{ $client->created_at->locale('ru')->diffForHumans() }}</span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12">
                        <div class="h-20 w-20 rounded-full bg-gray-100 dark:bg-slate-800 flex items-center justify-center mx-auto mb-4">
                            <svg class="w-10 h-10 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                        <h4 class="text-base font-semibold text-gray-900 dark:text-white mb-2">Нет клиентов</h4>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Добавьте первого клиента</p>
                        @if($hasBusinessPermission('client.clients.create'))
                            <a href="{{ route('clients.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                <span>Новый клиент</span>
                            </a>
                        @endif
                    </div>
                @endif
            </div>
        </div>
        @endif
    </div>

    <!-- Analytics Link - Ссылка на детальную аналитику -->
    @if($hasAnalyticsAccess)
    <div class="mt-6 bg-gradient-to-r from-indigo-50 to-purple-50 dark:from-indigo-900/20 dark:to-purple-900/20 rounded-xl border border-indigo-200/50 dark:border-indigo-800/50 p-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="p-3 bg-indigo-500/10 dark:bg-indigo-500/20 rounded-xl">
                    <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Детальная аналитика</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Просмотрите графики, финансовые метрики и топ услуги/мастера</p>
                </div>
            </div>
            <a href="{{ route('analytics.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-medium transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
                <span>Открыть аналитику</span>
            </a>
        </div>
    </div>
    @endif

    <!-- Тариф и подписка — единый виджет -->
    @if($canViewSubscription && isset($subscriptionStatus))
    @php
        $metricLabels = [
            'max_locations' => 'Локации',
            'max_masters' => 'Мастера',
            'max_services' => 'Услуги',
            'max_clients' => 'Клиенты',
            'max_appointments_per_month' => 'Записи в месяц',
            'max_business_users' => 'Пользователи',
        ];
    @endphp
    <div class="mt-6 group relative bg-gradient-to-br from-teal-50 to-cyan-100 dark:from-teal-900/20 dark:to-cyan-800/10 rounded-2xl border border-teal-200/50 dark:border-teal-800/50 shadow-sm hover:shadow-md transition-all duration-300 overflow-hidden">
        <div class="absolute top-0 right-0 w-40 h-40 bg-teal-200/30 dark:bg-teal-800/20 rounded-full -mr-20 -mt-20 blur-2xl"></div>
        <div class="relative p-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
                <div class="flex items-start gap-4">
                    <div class="p-3 bg-teal-500/10 dark:bg-teal-500/20 rounded-xl shrink-0">
                        <svg class="w-6 h-6 text-teal-600 dark:text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-sm font-medium text-slate-600 dark:text-slate-400">Ваш тариф</h2>
                        <p class="text-xl font-bold text-slate-900 dark:text-white mt-0.5">{{ $subscriptionStatus['plan_name'] }}</p>
                        <div class="flex flex-wrap items-center gap-x-4 gap-y-1 mt-2">
                            <span class="text-sm font-semibold text-teal-600 dark:text-teal-400">{{ number_format($subscriptionStatus['plan_price'] ?? 0, 0, ',', ' ') }} BYN</span>
                            @if(!empty($subscriptionStatus['ends_at']))
                                <span class="text-xs text-slate-500 dark:text-slate-400">Действует до {{ \Carbon\Carbon::parse($subscriptionStatus['ends_at'])->format('d.m.Y') }}</span>
                            @endif
                        </div>
                        @if(!empty($subscriptionStatus['is_preserved_period']) && !empty($subscriptionStatus['ends_at']) && !empty($subscriptionStatus['next_plan_name']))
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                                После этой даты будет подключён тариф «{{ $subscriptionStatus['next_plan_name'] }}»
                            </p>
                        @endif
                    </div>
                </div>
                <div class="flex flex-wrap gap-3 shrink-0">
                    <a href="{{ route('subscription.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-teal-600 hover:bg-teal-700 text-white rounded-xl text-sm font-medium transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                        </svg>
                        Сменить тариф
                    </a>
                    <a href="{{ route('subscription.current') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white/80 dark:bg-slate-800/80 hover:bg-white dark:hover:bg-slate-800 border border-teal-200 dark:border-teal-700 text-slate-700 dark:text-slate-300 rounded-xl text-sm font-medium transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Детали подписки
                    </a>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
                @foreach($subscriptionStatus['usage'] ?? [] as $metric => $data)
                @php
                    $limit = $data['limit'] ?? null;
                    $current = (int) ($data['current'] ?? 0);
                    $hasLimit = $limit !== null && $limit !== -1 && $limit > 0;
                    $limitLabel = ($limit === -1 || $limit === null) ? '∞' : (int) $limit;
                    $limitInt = $hasLimit ? (int) $limit : 0;
                    $percentage = $hasLimit ? min((float) ($data['percentage'] ?? 0), 100) : 0;
                    $limitReached = $hasLimit && $current >= $limitInt;
                    $warning = !empty($data['warning']);
                @endphp
                <div class="bg-white/60 dark:bg-slate-800/40 rounded-xl p-4 border border-teal-100/50 dark:border-teal-800/30">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs font-medium text-slate-600 dark:text-slate-400">{{ $metricLabels[$metric] ?? $metric }}</span>
                        <span class="text-xs font-semibold {{ $limitReached ? 'text-amber-600 dark:text-amber-400' : ($warning ? 'text-amber-600 dark:text-amber-400' : 'text-slate-700 dark:text-slate-300') }}">
                            {{ $current }} / {{ $limitLabel }}
                        </span>
                    </div>
                    @if($hasLimit)
                    <div class="w-full bg-slate-200 dark:bg-slate-700 rounded-full h-1.5">
                        <div class="h-1.5 rounded-full {{ $limitReached ? 'bg-amber-500' : ($warning ? 'bg-amber-500' : 'bg-teal-500') }}" style="width: {{ $percentage }}%"></div>
                    </div>
                    @if($limitReached)
                    <p class="text-xs text-amber-600 dark:text-amber-400 mt-1">Лимит достигнут</p>
                    @elseif($warning)
                    <p class="text-xs text-amber-600 dark:text-amber-400 mt-1">Близко к лимиту</p>
                    @endif
                    @else
                    <p class="text-xs text-slate-500 dark:text-slate-400">Без ограничений</p>
                    @endif
                </div>
                @endforeach
            </div>

            @if($hasBusinessPermission('client.subscription.manage'))
                @if(isset($subscriptionStatus['is_cancelled']) && $subscriptionStatus['is_cancelled'])
                <div class="flex items-start gap-3 p-4 bg-amber-50/80 dark:bg-amber-500/10 rounded-xl border border-amber-200/50 dark:border-amber-500/20">
                    <svg class="w-5 h-5 text-amber-600 dark:text-amber-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                    <div>
                        <p class="text-sm font-medium text-amber-800 dark:text-amber-300">Подписка отменена</p>
                        @if(!empty($subscriptionStatus['ends_at']))
                        <p class="text-xs text-amber-600 dark:text-amber-400 mt-1">Доступ сохранится до {{ \Carbon\Carbon::parse($subscriptionStatus['ends_at'])->format('d.m.Y') }}</p>
                        @endif
                    </div>
                </div>
                @endif
            @endif
        </div>
    </div>
    @endif

    <!-- Quick Actions - На всю ширину -->
    <div class="mt-6 bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-gray-200 dark:border-slate-800 p-5">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Быстрые действия</h2>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            @php
                $subscriptionService = app(\App\Services\SubscriptionService::class);
                $canCreateAppointment = $hasBusinessPermission('client.appointments.create') && $subscriptionService->canCreateAppointment($user);
                $canCreateClient = $hasBusinessPermission('client.clients.create') && $subscriptionService->canCreateClient($user);
                $canCreateService = $hasBusinessPermission('client.services.create') && $subscriptionService->canCreateService($user);
            @endphp
            @if($hasBusinessPermission('client.appointments.create'))
                @if($canCreateAppointment)
                    <a href="{{ route('appointments.create') }}" class="flex flex-col items-center p-4 bg-indigo-50 dark:bg-indigo-500/10 rounded-lg hover:bg-indigo-100 dark:hover:bg-indigo-500/20 transition-colors">
                        <div class="w-12 h-12 bg-indigo-600 rounded-lg flex items-center justify-center mb-3">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                        </div>
                        <span class="text-sm font-medium text-gray-900 dark:text-white">Новая запись</span>
                    </a>
                @else
                    <button disabled class="flex flex-col items-center p-4 bg-slate-100 dark:bg-slate-800 rounded-lg cursor-not-allowed opacity-50" title="Достигнут месячный лимит записей для вашего тарифа.">
                        <div class="w-12 h-12 bg-slate-400 rounded-lg flex items-center justify-center mb-3">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                        </div>
                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Новая запись</span>
                    </button>
                @endif
            @endif
            @if($hasBusinessPermission('client.clients.create'))
                @if($canCreateClient)
                    <a href="{{ route('clients.create') }}" class="flex flex-col items-center p-4 bg-green-50 dark:bg-green-500/10 rounded-lg hover:bg-green-100 dark:hover:bg-green-500/20 transition-colors">
                        <div class="w-12 h-12 bg-green-600 rounded-lg flex items-center justify-center mb-3">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                            </svg>
                        </div>
                        <span class="text-sm font-medium text-gray-900 dark:text-white">Новый клиент</span>
                    </a>
                @else
                    <button disabled class="flex flex-col items-center p-4 bg-slate-100 dark:bg-slate-800 rounded-lg cursor-not-allowed opacity-50" title="Достигнут лимит клиентов для вашего тарифа.">
                        <div class="w-12 h-12 bg-slate-400 rounded-lg flex items-center justify-center mb-3">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                            </svg>
                        </div>
                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Новый клиент</span>
                    </button>
                @endif
            @endif
            @if($hasBusinessPermission('client.services.create'))
                @if($canCreateService)
                    <a href="{{ route('services.create') }}" class="flex flex-col items-center p-4 bg-yellow-50 dark:bg-yellow-500/10 rounded-lg hover:bg-yellow-100 dark:hover:bg-yellow-500/20 transition-colors">
                        <div class="w-12 h-12 bg-yellow-600 rounded-lg flex items-center justify-center mb-3">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                            </svg>
                        </div>
                        <span class="text-sm font-medium text-gray-900 dark:text-white">Новая услуга</span>
                    </a>
                @else
                    <button disabled class="flex flex-col items-center p-4 bg-slate-100 dark:bg-slate-800 rounded-lg cursor-not-allowed opacity-50" title="Достигнут лимит услуг для вашего тарифа.">
                        <div class="w-12 h-12 bg-slate-400 rounded-lg flex items-center justify-center mb-3">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                            </svg>
                        </div>
                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Новая услуга</span>
                    </button>
                @endif
            @endif
            @if($hasBusinessPermission('client.appointments.view'))
                <a href="{{ route('appointments.calendar') }}" class="flex flex-col items-center p-4 bg-purple-50 dark:bg-purple-500/10 rounded-lg hover:bg-purple-100 dark:hover:bg-purple-500/20 transition-colors">
                    <div class="w-12 h-12 bg-purple-600 rounded-lg flex items-center justify-center mb-3">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                    <span class="text-sm font-medium text-gray-900 dark:text-white">Календарь</span>
                </a>
            @endif
        </div>
    </div>
</div>

@endsection
