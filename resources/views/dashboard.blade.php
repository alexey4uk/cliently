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
        $currentBusinessRole = $pivot?->pivot->role ?? null;
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

    <!-- Stats Cards - Переработанный дизайн с градиентами -->
    @php
        $statsCards = [];
        if ($canViewAppointments) {
            $statsCards[] = 'appointments';
        }
        if ($canViewClients) {
            $statsCards[] = 'clients';
        }
        if ($canViewAppointments) {
            $statsCards[] = 'completed';
        }
        if ($canViewServices) {
            $statsCards[] = 'services';
        }
        if ($canViewMasters) {
            $statsCards[] = 'masters';
        }
        if ($canViewLocations) {
            $statsCards[] = 'locations';
        }
        $statsCount = count($statsCards);
        $gridCols = $statsCount <= 2 ? 'grid-cols-1 sm:grid-cols-2' : ($statsCount <= 4 ? 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-4' : 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6');
    @endphp
    
    @if($statsCount > 0)
    <div class="grid {{ $gridCols }} gap-6 mb-6">
        <!-- Today's Appointments - Приоритет #1: Самая важная операционная метрика -->
        @if($canViewAppointments)
        <div class="group relative bg-gradient-to-br from-indigo-50 to-indigo-100 dark:from-indigo-900/20 dark:to-indigo-800/10 rounded-2xl p-6 border border-indigo-200/50 dark:border-indigo-800/50 shadow-sm hover:shadow-md transition-all duration-300 overflow-hidden">
            <div class="absolute top-0 right-0 w-32 h-32 bg-indigo-200/30 dark:bg-indigo-800/20 rounded-full -mr-16 -mt-16 blur-2xl"></div>
            <div class="relative">
                <div class="flex items-start justify-between mb-4">
                    <div class="p-3 bg-indigo-500/10 dark:bg-indigo-500/20 rounded-xl">
                        <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                </div>
                <p class="text-sm font-medium text-slate-600 dark:text-slate-400 mb-1">Записей сегодня</p>
                <p class="text-4xl font-bold text-slate-900 dark:text-white mb-2">{{ $stats['appointments_today'] ?? 0 }}</p>
                @if(isset($stats['appointments_week']) && $stats['appointments_week'] > 0)
                    <p class="text-xs text-slate-500 dark:text-slate-400">
                        <span class="font-medium text-indigo-600 dark:text-indigo-400">{{ $stats['appointments_week'] }}</span> за неделю
                    </p>
                @else
                    <p class="text-xs text-slate-500 dark:text-slate-400">Нет записей</p>
                @endif
            </div>
        </div>
        @endif

        <!-- Total Clients - Приоритет #2: Ключевая бизнес-метрика -->
        @if($canViewClients)
        <div class="group relative bg-gradient-to-br from-emerald-50 to-emerald-100 dark:from-emerald-900/20 dark:to-emerald-800/10 rounded-2xl p-6 border border-emerald-200/50 dark:border-emerald-800/50 shadow-sm hover:shadow-md transition-all duration-300 overflow-hidden">
            <div class="absolute top-0 right-0 w-32 h-32 bg-emerald-200/30 dark:bg-emerald-800/20 rounded-full -mr-16 -mt-16 blur-2xl"></div>
            <div class="relative">
                <div class="flex items-start justify-between mb-4">
                    <div class="p-3 bg-emerald-500/10 dark:bg-emerald-500/20 rounded-xl">
                        <svg class="w-6 h-6 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    @if(isset($stats['new_clients_week']) && $stats['new_clients_week'] > 0)
                        <span class="px-2 py-1 text-xs font-semibold text-emerald-700 dark:text-emerald-400 bg-emerald-100 dark:bg-emerald-500/20 rounded-lg">
                            +{{ $stats['new_clients_week'] }}
                        </span>
                    @endif
                </div>
                <p class="text-sm font-medium text-slate-600 dark:text-slate-400 mb-1">Всего клиентов</p>
                <p class="text-4xl font-bold text-slate-900 dark:text-white mb-2">{{ number_format($stats['total_clients'] ?? 0, 0, ',', ' ') }}</p>
                @if(isset($stats['new_clients_week']) && $stats['new_clients_week'] > 0)
                    <p class="text-xs text-slate-500 dark:text-slate-400">
                        <span class="font-medium text-emerald-600 dark:text-emerald-400">+{{ $stats['new_clients_week'] }}</span> новых за неделю
                    </p>
                @else
                    <p class="text-xs text-slate-500 dark:text-slate-400">Нет новых клиентов</p>
                @endif
            </div>
        </div>
        @endif

        <!-- Completed Appointments - Приоритет #3: Операционная метрика эффективности -->
        @if($canViewAppointments)
        <div class="group relative bg-gradient-to-br from-amber-50 to-amber-100 dark:from-amber-900/20 dark:to-amber-800/10 rounded-2xl p-6 border border-amber-200/50 dark:border-amber-800/50 shadow-sm hover:shadow-md transition-all duration-300 overflow-hidden">
            <div class="absolute top-0 right-0 w-32 h-32 bg-amber-200/30 dark:bg-amber-800/20 rounded-full -mr-16 -mt-16 blur-2xl"></div>
            <div class="relative">
                <div class="flex items-start justify-between mb-4">
                    <div class="p-3 bg-amber-500/10 dark:bg-amber-500/20 rounded-xl">
                        <svg class="w-6 h-6 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <p class="text-sm font-medium text-slate-600 dark:text-slate-400 mb-1">Завершено за месяц</p>
                <p class="text-4xl font-bold text-slate-900 dark:text-white mb-2">{{ $stats['completed_month'] ?? 0 }}</p>
                @if(isset($stats['completed_week']) && $stats['completed_week'] > 0)
                    <p class="text-xs text-slate-500 dark:text-slate-400">
                        <span class="font-medium text-amber-600 dark:text-amber-400">{{ $stats['completed_week'] }}</span> за неделю
                    </p>
                @else
                    <p class="text-xs text-slate-500 dark:text-slate-400">Нет завершенных</p>
                @endif
            </div>
        </div>
        @endif

        <!-- Services - Приоритет #4: Справочная информация -->
        @if($canViewServices)
        <div class="group relative bg-gradient-to-br from-purple-50 to-purple-100 dark:from-purple-900/20 dark:to-purple-800/10 rounded-2xl p-6 border border-purple-200/50 dark:border-purple-800/50 shadow-sm hover:shadow-md transition-all duration-300 overflow-hidden">
            <div class="absolute top-0 right-0 w-32 h-32 bg-purple-200/30 dark:bg-purple-800/20 rounded-full -mr-16 -mt-16 blur-2xl"></div>
            <div class="relative">
                <div class="flex items-start justify-between mb-4">
                    <div class="p-3 bg-purple-500/10 dark:bg-purple-500/20 rounded-xl">
                        <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                    </div>
                </div>
                <p class="text-sm font-medium text-slate-600 dark:text-slate-400 mb-1">Активных услуг</p>
                <p class="text-4xl font-bold text-slate-900 dark:text-white mb-2">{{ $business->services->count() ?? 0 }}</p>
                <p class="text-xs text-slate-500 dark:text-slate-400">В системе</p>
            </div>
        </div>
        @endif

        <!-- Masters - Приоритет #5: Справочная информация -->
        @if($canViewMasters)
        <div class="group relative bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/10 rounded-2xl p-6 border border-blue-200/50 dark:border-blue-800/50 shadow-sm hover:shadow-md transition-all duration-300 overflow-hidden">
            <div class="absolute top-0 right-0 w-32 h-32 bg-blue-200/30 dark:bg-blue-800/20 rounded-full -mr-16 -mt-16 blur-2xl"></div>
            <div class="relative">
                <div class="flex items-start justify-between mb-4">
                    <div class="p-3 bg-blue-500/10 dark:bg-blue-500/20 rounded-xl">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                </div>
                <p class="text-sm font-medium text-slate-600 dark:text-slate-400 mb-1">Мастеров</p>
                <p class="text-4xl font-bold text-slate-900 dark:text-white mb-2">{{ $business->masters->count() ?? 0 }}</p>
                <p class="text-xs text-slate-500 dark:text-slate-400">В системе</p>
            </div>
        </div>
        @endif

        <!-- Locations - Приоритет #6: Справочная информация -->
        @if($canViewLocations)
        <div class="group relative bg-gradient-to-br from-rose-50 to-rose-100 dark:from-rose-900/20 dark:to-rose-800/10 rounded-2xl p-6 border border-rose-200/50 dark:border-rose-800/50 shadow-sm hover:shadow-md transition-all duration-300 overflow-hidden">
            <div class="absolute top-0 right-0 w-32 h-32 bg-rose-200/30 dark:bg-rose-800/20 rounded-full -mr-16 -mt-16 blur-2xl"></div>
            <div class="relative">
                <div class="flex items-start justify-between mb-4">
                    <div class="p-3 bg-rose-500/10 dark:bg-rose-500/20 rounded-xl">
                        <svg class="w-6 h-6 text-rose-600 dark:text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                    </div>
                </div>
                <p class="text-sm font-medium text-slate-600 dark:text-slate-400 mb-1">Локаций</p>
                <p class="text-4xl font-bold text-slate-900 dark:text-white mb-2">{{ $business->locations->count() ?? 0 }}</p>
                <p class="text-xs text-slate-500 dark:text-slate-400">В системе</p>
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

    <!-- Subscription Status and Actions - На всю ширину -->
    @if($canViewSubscription && isset($subscriptionStatus))
    <div class="mt-6 grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Subscription Status -->
        <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-gray-200 dark:border-slate-800">
            <div class="p-5 border-b border-gray-200 dark:border-slate-800">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Статус подписки</h2>
            </div>
            <div class="p-5">
                <div class="mb-4">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Тариф: {{ $subscriptionStatus['plan_name'] }}</span>
                        <span class="text-sm font-bold text-gray-900 dark:text-white">{{ number_format($subscriptionStatus['plan_price'], 2, ',', ' ') }} BYN</span>
                    </div>
                    @if($subscriptionStatus['ends_at'])
                    <p class="text-xs text-gray-500 dark:text-gray-400">Действует до: {{ \Carbon\Carbon::parse($subscriptionStatus['ends_at'])->format('d.m.Y') }}</p>
                    @endif
                </div>
                <div class="space-y-3">
                    @foreach($subscriptionStatus['usage'] as $metric => $data)
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-xs text-gray-600 dark:text-gray-400">
                                @if($metric === 'max_locations') Локации
                                @elseif($metric === 'max_masters') Мастера
                                @elseif($metric === 'max_services') Услуги
                                @elseif($metric === 'max_clients') Клиенты
                                @elseif($metric === 'max_appointments_per_month') Записи в месяц
                                @elseif($metric === 'max_business_users') Пользователи
                                @else {{ $metric }}
                                @endif
                            </span>
                            <span class="text-xs font-medium {{ $data['warning'] ? 'text-orange-600 dark:text-orange-400' : 'text-gray-600 dark:text-gray-400' }}">
                                {{ $data['current'] }} / {{ $data['limit'] === -1 ? '∞' : $data['limit'] }}
                            </span>
                        </div>
                        @if($data['limit'] > 0)
                        <div class="w-full bg-gray-200 dark:bg-slate-700 rounded-full h-2">
                            <div class="h-2 rounded-full {{ $data['warning'] ? 'bg-orange-500' : 'bg-indigo-600' }}" style="width: {{ min($data['percentage'], 100) }}%"></div>
                        </div>
                        @if($data['warning'])
                        <p class="text-xs text-orange-600 dark:text-orange-400 mt-1">Приближается к лимиту</p>
                        @endif
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Subscription Actions -->
        <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-gray-200 dark:border-slate-800">
            <div class="p-5 border-b border-gray-200 dark:border-slate-800">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Управление подпиской</h2>
            </div>
            <div class="p-5">
                <div class="space-y-3">
                    <a href="{{ route('subscription.index') }}" class="flex items-center justify-between p-4 bg-indigo-50 dark:bg-indigo-500/10 rounded-lg hover:bg-indigo-100 dark:hover:bg-indigo-500/20 transition-colors">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-indigo-600 rounded-lg flex items-center justify-center mr-3">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">Сменить тариф</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Выберите подходящий план</p>
                            </div>
                        </div>
                        <svg class="w-5 h-5 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>

                    <a href="{{ route('subscription.current') }}" class="flex items-center justify-between p-4 bg-gray-50 dark:bg-slate-800 rounded-lg hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-gray-600 rounded-lg flex items-center justify-center mr-3">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">Детали подписки</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Подробная информация</p>
                            </div>
                        </div>
                        <svg class="w-5 h-5 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>

                    @if($hasBusinessPermission('client.subscription.manage'))
                        @if(isset($subscriptionStatus['is_cancelled']) && $subscriptionStatus['is_cancelled'])
                            <div class="p-4 bg-orange-50 dark:bg-orange-500/10 rounded-lg border border-orange-200 dark:border-orange-500/20">
                                <div class="flex items-start">
                                    <svg class="w-5 h-5 text-orange-600 dark:text-orange-400 mt-0.5 mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                    </svg>
                                    <div class="flex-1">
                                        <p class="text-sm font-medium text-orange-800 dark:text-orange-300">Подписка отменена</p>
                                        @if($subscriptionStatus['ends_at'])
                                        <p class="text-xs text-orange-600 dark:text-orange-400 mt-1">Будет активна до {{ \Carbon\Carbon::parse($subscriptionStatus['ends_at'])->format('d.m.Y') }}</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @elseif(isset($subscriptionStatus['plan_slug']) && $subscriptionStatus['plan_slug'] !== 'free')
                            <form action="{{ route('subscription.cancel') }}" method="POST" class="pt-3 border-t border-gray-200 dark:border-slate-800">
                                @csrf
                                <button type="submit" onclick="return confirm('Вы уверены, что хотите отменить подписку? Она будет активна до окончания текущего периода.')" class="flex items-center justify-between w-full p-4 bg-red-50 dark:bg-red-500/10 rounded-lg hover:bg-red-100 dark:hover:bg-red-500/20 transition-colors text-left">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-red-600 rounded-lg flex items-center justify-center mr-3">
                                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-red-900 dark:text-red-300">Отменить подписку</p>
                                            <p class="text-xs text-red-600 dark:text-red-400">Действует до конца периода</p>
                                        </div>
                                    </div>
                                    <svg class="w-5 h-5 text-red-400 dark:text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </button>
                            </form>
                        @endif
                    @endif
                </div>
            </div>
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
