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
@endphp

<div>
    <!-- Page Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Dashboard</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-1">Обзор вашего бизнеса</p>
    </div>

    <!-- Stats Cards -->
    @if($hasBusinessPermission('client.appointments.view') || $hasBusinessPermission('client.appointments.view.own'))
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <!-- Today's Appointments -->
        <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-gray-200 dark:border-slate-800 p-5 card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Записей сегодня</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $stats['appointments_today'] ?? 0 }}</p>
                    @if(isset($stats['appointments_week']) && $stats['appointments_week'] > 0)
                        <p class="text-sm text-green-600 dark:text-green-400 mt-1 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path>
                            </svg>
                            {{ $stats['appointments_week'] }} за неделю
                        </p>
                    @else
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Нет записей</p>
                    @endif
                </div>
                <div class="w-12 h-12 bg-indigo-100 dark:bg-indigo-500/20 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Total Clients -->
        <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-gray-200 dark:border-slate-800 p-5 card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Всего клиентов</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ number_format($stats['total_clients'] ?? 0, 0, ',', ' ') }}</p>
                    @if(isset($stats['new_clients_week']) && $stats['new_clients_week'] > 0)
                        <p class="text-sm text-green-600 dark:text-green-400 mt-1 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path>
                            </svg>
                            +{{ $stats['new_clients_week'] }} новых за неделю
                        </p>
                    @else
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Нет новых клиентов</p>
                    @endif
                </div>
                <div class="w-12 h-12 bg-green-100 dark:bg-green-500/20 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Revenue / Completed Appointments -->
        <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-gray-200 dark:border-slate-800 p-5 card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Завершено за месяц</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $stats['completed_month'] ?? 0 }}</p>
                    @if(isset($stats['completed_week']) && $stats['completed_week'] > 0)
                        <p class="text-sm text-green-600 dark:text-green-400 mt-1 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path>
                            </svg>
                            {{ $stats['completed_week'] }} за неделю
                        </p>
                    @else
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Нет завершенных</p>
                    @endif
                </div>
                <div class="w-12 h-12 bg-yellow-100 dark:bg-yellow-500/20 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Services -->
        @if($hasBusinessPermission('client.services.view'))
        <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-gray-200 dark:border-slate-800 p-5 card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Активных услуг</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $business->services->count() ?? 0 }}</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">В системе</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 dark:bg-purple-500/20 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                </div>
            </div>
        </div>
        @endif
    </div>
    @endif

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Today's Schedule -->
        @if($hasBusinessPermission('client.appointments.view') || $hasBusinessPermission('client.appointments.view.own'))
        <div class="lg:col-span-2 bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-gray-200 dark:border-slate-800">
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
        @if($hasBusinessPermission('client.clients.view') || $hasBusinessPermission('client.clients.view.own'))
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

    <!-- Financial Metrics -->
    @if($hasAnalyticsAccess && isset($financialStats))
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mt-6 mb-6">
        <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-gray-200 dark:border-slate-800 p-5 card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Выручка за месяц</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ number_format($financialStats['revenue_month'] ?? 0, 0, ',', ' ') }} BYN</p>
                    @if(isset($financialStats['revenue_growth']) && $financialStats['revenue_growth'] > 0)
                        <p class="text-sm text-green-600 dark:text-green-400 mt-1 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path>
                            </svg>
                            +{{ $financialStats['revenue_growth'] }}%
                        </p>
                    @elseif(isset($financialStats['revenue_growth']) && $financialStats['revenue_growth'] < 0)
                        <p class="text-sm text-red-600 dark:text-red-400 mt-1 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                            </svg>
                            {{ $financialStats['revenue_growth'] }}%
                        </p>
                    @endif
                </div>
                <div class="w-12 h-12 bg-green-100 dark:bg-green-500/20 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-gray-200 dark:border-slate-800 p-5 card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Выручка за неделю</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ number_format($financialStats['revenue_week'] ?? 0, 0, ',', ' ') }} BYN</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 dark:bg-blue-500/20 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-gray-200 dark:border-slate-800 p-5 card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Средний чек</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ number_format($financialStats['average_check'] ?? 0, 2, ',', ' ') }} BYN</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 dark:bg-purple-500/20 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-5m-6 5h.01M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-gray-200 dark:border-slate-800 p-5 card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Рост выручки</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $financialStats['revenue_growth'] ?? 0 }}%</p>
                </div>
                <div class="w-12 h-12 bg-indigo-100 dark:bg-indigo-500/20 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Top Services and Masters -->
    @if($hasAnalyticsAccess && (($hasBusinessPermission('client.services.view') && isset($topServices) && count($topServices) > 0) || ($hasBusinessPermission('client.masters.view') && isset($topMasters) && count($topMasters) > 0)))
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6 mb-6">
        @if($hasBusinessPermission('client.services.view') && isset($topServices) && count($topServices) > 0)
        <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-gray-200 dark:border-slate-800">
            <div class="p-5 border-b border-gray-200 dark:border-slate-800">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Топ услуги</h2>
            </div>
            <div class="p-5">
                <div class="space-y-4">
                    @foreach($topServices as $service)
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $service['service_name'] }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $service['count'] }} записей</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-bold text-gray-900 dark:text-white">{{ number_format($service['revenue'], 0, ',', ' ') }} BYN</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        @if($hasBusinessPermission('client.masters.view') && isset($topMasters) && count($topMasters) > 0)
        <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-gray-200 dark:border-slate-800">
            <div class="p-5 border-b border-gray-200 dark:border-slate-800">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Топ мастера</h2>
            </div>
            <div class="p-5">
                <div class="space-y-4">
                    @foreach($topMasters as $master)
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $master['master_name'] }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $master['count'] }} записей</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-bold text-gray-900 dark:text-white">{{ number_format($master['revenue'], 0, ',', ' ') }} BYN</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif
    </div>
    @endif

    <!-- Subscription Status and Actions -->
    @if($hasBusinessPermission('client.subscription.view') && isset($subscriptionStatus))
    <div class="mt-6 mb-6 grid grid-cols-1 lg:grid-cols-2 gap-6">
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
                    <div class="pt-3 border-t border-gray-200 dark:border-slate-800">
                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">Дополнительные действия</p>
                        <a href="{{ route('subscription.index') }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 font-medium">
                            Управление подпиской →
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Quick Actions -->
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
