@extends('layouts.panel')

@section('title', 'Панель управления')

@section('content')
    <div class="max-w-6xl 2xl:max-w-[1400px] mx-auto space-y-8">
        <!-- Заголовок с улучшенным дизайном -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 border-b border-slate-200 dark:border-slate-800">
            <div>
                <h1 class="text-3xl font-bold text-slate-900 dark:text-white">Панель управления</h1>
                <p class="text-slate-600 dark:text-slate-400 mt-2">Обзор системы и ключевые метрики</p>
            </div>
            <form action="{{ route('panel.refresh') }}" method="POST">
                @csrf
                <button type="submit" 
                        class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-lg text-sm font-medium transition-all duration-200 shadow-sm hover:shadow">
                    <i class="fa-solid fa-rotate text-xs"></i>
                    <span>Обновить данные</span>
                </button>
            </form>
        </div>

        @php
            // Определяем доступные виджеты на основе прав
            $canViewBusinesses = auth()->user()->can('panel.businesses.view');
            $canViewUsers = auth()->user()->can('panel.users.view');
            $canViewClients = auth()->user()->can('panel.clients.view');
            $canViewAppointments = auth()->user()->can('panel.appointments.view');
            $canViewAnalytics = auth()->user()->can('panel.analytics.view');
            $canViewSupport = auth()->user()->can('panel.support.view');
        @endphp

        <!-- Основные метрики - Улучшенный дизайн с акцентами -->
        @if($canViewBusinesses || $canViewUsers || $canViewSupport || $canViewAppointments)
        <div>
            <h2 class="text-xl font-semibold text-slate-900 dark:text-white mb-4">Ключевые показатели</h2>
            @php
                $mainStatsCount = 0;
                if ($canViewBusinesses && isset($stats['total_businesses'])) $mainStatsCount++;
                if ($canViewUsers && isset($stats['total_users'])) $mainStatsCount++;
                if ($canViewAppointments && isset($stats['appointments_today'])) $mainStatsCount++;
                if ($canViewUsers && isset($stats['new_users_week'])) $mainStatsCount++;
                $mainGridCols = $mainStatsCount <= 2 ? 'grid-cols-1 sm:grid-cols-2' : ($mainStatsCount <= 4 ? 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-4' : 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4');
            @endphp
            <div class="grid {{ $mainGridCols }} gap-6">
                <!-- Всего бизнесов -->
                @if($canViewBusinesses && isset($stats['total_businesses']))
                <div class="group relative bg-gradient-to-br from-indigo-50 to-indigo-100 dark:from-indigo-900/20 dark:to-indigo-800/10 rounded-2xl p-6 border border-indigo-200/50 dark:border-indigo-800/50 shadow-sm hover:shadow-md transition-all duration-300 overflow-hidden">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-indigo-200/30 dark:bg-indigo-800/20 rounded-full -mr-16 -mt-16 blur-2xl"></div>
                    <div class="relative">
                        <div class="flex items-start justify-between mb-4">
                            <div class="p-3 bg-indigo-500/10 dark:bg-indigo-500/20 rounded-xl">
                                <i class="fa-solid fa-building text-indigo-600 dark:text-indigo-400 text-xl"></i>
                            </div>
                            @if(isset($stats['business_growth_rate']) && $stats['business_growth_rate'] > 0)
                                <span class="px-2 py-1 text-xs font-semibold text-emerald-700 dark:text-emerald-400 bg-emerald-100 dark:bg-emerald-500/20 rounded-lg">
                                    <i class="fa-solid fa-arrow-up mr-1"></i>{{ abs($stats['business_growth_rate']) }}%
                                </span>
                            @elseif(isset($stats['business_growth_rate']) && $stats['business_growth_rate'] < 0)
                                <span class="px-2 py-1 text-xs font-semibold text-rose-700 dark:text-rose-400 bg-rose-100 dark:bg-rose-500/20 rounded-lg">
                                    <i class="fa-solid fa-arrow-down mr-1"></i>{{ abs($stats['business_growth_rate']) }}%
                                </span>
                            @endif
                        </div>
                        <p class="text-sm font-medium text-slate-600 dark:text-slate-400 mb-1">Всего бизнесов</p>
                        <p class="text-4xl font-bold text-slate-900 dark:text-white mb-2">{{ $stats['total_businesses'] }}</p>
                        <p class="text-xs text-slate-500 dark:text-slate-400">
                            <span class="font-medium text-indigo-600 dark:text-indigo-400">+{{ $stats['new_businesses_month'] ?? 0 }}</span> за месяц
                        </p>
                    </div>
                </div>
                @endif

                <!-- Всего пользователей -->
                @if($canViewUsers && isset($stats['total_users']))
                <div class="group relative bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/10 rounded-2xl p-6 border border-blue-200/50 dark:border-blue-800/50 shadow-sm hover:shadow-md transition-all duration-300 overflow-hidden">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-blue-200/30 dark:bg-blue-800/20 rounded-full -mr-16 -mt-16 blur-2xl"></div>
                    <div class="relative">
                        <div class="flex items-start justify-between mb-4">
                            <div class="p-3 bg-blue-500/10 dark:bg-blue-500/20 rounded-xl">
                                <i class="fa-solid fa-users text-blue-600 dark:text-blue-400 text-xl"></i>
                            </div>
                            @if(isset($stats['user_growth_rate']) && $stats['user_growth_rate'] > 0)
                                <span class="px-2 py-1 text-xs font-semibold text-emerald-700 dark:text-emerald-400 bg-emerald-100 dark:bg-emerald-500/20 rounded-lg">
                                    <i class="fa-solid fa-arrow-up mr-1"></i>{{ abs($stats['user_growth_rate']) }}%
                                </span>
                            @elseif(isset($stats['user_growth_rate']) && $stats['user_growth_rate'] < 0)
                                <span class="px-2 py-1 text-xs font-semibold text-rose-700 dark:text-rose-400 bg-rose-100 dark:bg-rose-500/20 rounded-lg">
                                    <i class="fa-solid fa-arrow-down mr-1"></i>{{ abs($stats['user_growth_rate']) }}%
                                </span>
                            @endif
                        </div>
                        <p class="text-sm font-medium text-slate-600 dark:text-slate-400 mb-1">Пользователи</p>
                        <p class="text-4xl font-bold text-slate-900 dark:text-white mb-2">{{ number_format($stats['total_users'], 0, ',', ' ') }}</p>
                        <p class="text-xs text-slate-500 dark:text-slate-400">
                            <span class="font-medium text-blue-600 dark:text-blue-400">+{{ $stats['new_users_month'] ?? 0 }}</span> за месяц
                        </p>
                    </div>
                </div>
                @endif

                <!-- Записи сегодня (для поддержки) -->
                @if($canViewAppointments && isset($stats['appointments_today']))
                <div class="group relative bg-gradient-to-br from-emerald-50 to-emerald-100 dark:from-emerald-900/20 dark:to-emerald-800/10 rounded-2xl p-6 border border-emerald-200/50 dark:border-emerald-800/50 shadow-sm hover:shadow-md transition-all duration-300 overflow-hidden">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-emerald-200/30 dark:bg-emerald-800/20 rounded-full -mr-16 -mt-16 blur-2xl"></div>
                    <div class="relative">
                        <div class="flex items-start justify-between mb-4">
                            <div class="p-3 bg-emerald-500/10 dark:bg-emerald-500/20 rounded-xl">
                                <i class="fa-solid fa-calendar-day text-emerald-600 dark:text-emerald-400 text-xl"></i>
                            </div>
                        </div>
                        <p class="text-sm font-medium text-slate-600 dark:text-slate-400 mb-1">Записей сегодня</p>
                        <p class="text-4xl font-bold text-slate-900 dark:text-white mb-2">{{ $stats['appointments_today'] ?? 0 }}</p>
                        <p class="text-xs text-slate-500 dark:text-slate-400">
                            {{ $stats['appointments_week'] ?? 0 }} за неделю
                        </p>
                    </div>
                </div>
                @endif

                <!-- Новые регистрации за неделю (для поддержки) -->
                @if($canViewUsers && isset($stats['new_users_week']))
                <div class="group relative bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/10 rounded-2xl p-6 border border-blue-200/50 dark:border-blue-800/50 shadow-sm hover:shadow-md transition-all duration-300 overflow-hidden">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-blue-200/30 dark:bg-blue-800/20 rounded-full -mr-16 -mt-16 blur-2xl"></div>
                    <div class="relative">
                        <div class="flex items-start justify-between mb-4">
                            <div class="p-3 bg-blue-500/10 dark:bg-blue-500/20 rounded-xl">
                                <i class="fa-solid fa-user-plus text-blue-600 dark:text-blue-400 text-xl"></i>
                            </div>
                        </div>
                        <p class="text-sm font-medium text-slate-600 dark:text-slate-400 mb-1">Новых регистраций</p>
                        <p class="text-4xl font-bold text-slate-900 dark:text-white mb-2">{{ $stats['new_users_week'] ?? 0 }}</p>
                        <p class="text-xs text-slate-500 dark:text-slate-400">
                            {{ $stats['new_users_month'] ?? 0 }} за месяц
                        </p>
                    </div>
                </div>
                @endif

            </div>
        </div>
        @endif

        <!-- Виджеты для поддержки -->
        @if($canViewSupport || ($canViewAppointments && isset($activeBusinesses)))
        <div>
            <h2 class="text-xl font-semibold text-slate-900 dark:text-white mb-4">Активность и мониторинг</h2>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Активные бизнесы (для поддержки) -->
                @if($canViewSupport && isset($activeBusinesses) && $activeBusinesses && $activeBusinesses->isNotEmpty())
                <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between bg-slate-50 dark:bg-slate-800/50">
                        <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Активные бизнесы</h3>
                        <span class="text-xs text-slate-500 dark:text-slate-400">за неделю</span>
                    </div>
                    <div class="divide-y divide-slate-200 dark:divide-slate-800">
                        @foreach($activeBusinesses as $business)
                            <a href="{{ auth()->user()->can('panel.businesses.view') ? route('panel.businesses.show', $business) : '#' }}" 
                               class="block px-6 py-4 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors group">
                                <div class="flex items-center justify-between">
                                    <div class="flex-1">
                                        <p class="font-semibold text-slate-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">{{ $business->name }}</p>
                                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1.5">
                                            <i class="fa-solid fa-calendar-check mr-1"></i>{{ $business->appointments_count }} записей за неделю
                                        </p>
                                    </div>
                                    <i class="fa-solid fa-chevron-right text-slate-400 dark:text-slate-500 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors"></i>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Бизнесы без активности (для поддержки) -->
                @if($canViewSupport && isset($inactiveBusinesses) && $inactiveBusinesses && $inactiveBusinesses->isNotEmpty())
                <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between bg-rose-50 dark:bg-rose-500/10">
                        <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Бизнесы без активности</h3>
                        <span class="text-xs text-slate-500 dark:text-slate-400">за месяц</span>
                    </div>
                    <div class="divide-y divide-slate-200 dark:divide-slate-800">
                        @foreach($inactiveBusinesses as $business)
                            <a href="{{ auth()->user()->can('panel.businesses.view') ? route('panel.businesses.show', $business) : '#' }}" 
                               class="block px-6 py-4 hover:bg-rose-50 dark:hover:bg-rose-500/10 transition-colors group border-l-4 border-rose-500">
                                <div class="flex items-center justify-between">
                                    <div class="flex-1">
                                        <p class="font-semibold text-slate-900 dark:text-white group-hover:text-rose-600 dark:group-hover:text-rose-400 transition-colors">{{ $business->name }}</p>
                                        <div class="flex items-center gap-4 mt-1.5">
                                            <span class="text-xs text-slate-500 dark:text-slate-400">
                                                <i class="fa-solid fa-calendar-check mr-1"></i>{{ $business->appointments_count }} записей всего
                                            </span>
                                            <span class="text-xs text-slate-500 dark:text-slate-400">
                                                <i class="fa-solid fa-users mr-1"></i>{{ $business->clients_count }} клиентов
                                            </span>
                                        </div>
                                    </div>
                                    <i class="fa-solid fa-chevron-right text-slate-400 dark:text-slate-500 group-hover:text-rose-600 dark:group-hover:text-rose-400 transition-colors"></i>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>
        @endif

        <!-- Analytics Link - Ссылка на детальную аналитику -->
        @if($canViewAnalytics)
        <div class="bg-gradient-to-r from-indigo-50 to-purple-50 dark:from-indigo-900/20 dark:to-purple-900/20 rounded-xl border border-indigo-200/50 dark:border-indigo-800/50 p-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="p-3 bg-indigo-500/10 dark:bg-indigo-500/20 rounded-xl">
                        <i class="fa-solid fa-chart-bar text-indigo-600 dark:text-indigo-400 text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Детальная аналитика</h3>
                        <p class="text-sm text-slate-600 dark:text-slate-400 mt-1">Просмотрите графики роста, активности системы и детальную статистику</p>
                    </div>
                </div>
                <a href="{{ route('panel.analytics') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-medium transition-colors">
                    <i class="fa-solid fa-arrow-right text-sm"></i>
                    <span>Открыть аналитику</span>
                </a>
            </div>
        </div>
        @endif

        <!-- Списки данных - Улучшенный дизайн -->
        <div>
            <h2 class="text-xl font-semibold text-slate-900 dark:text-white mb-4">Последняя активность</h2>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Последние бизнесы -->
                @if($canViewBusinesses && $recentBusinesses && $recentBusinesses->isNotEmpty())
                <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between bg-slate-50 dark:bg-slate-800/50">
                        <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Последние бизнесы</h3>
                        @if(auth()->user()->can('panel.businesses.view'))
                        <a href="{{ route('panel.businesses') }}" 
                           class="text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 font-medium transition-colors">
                            Все →
                        </a>
                        @endif
                    </div>
                    <div class="divide-y divide-slate-200 dark:divide-slate-800">
                        @foreach($recentBusinesses as $business)
                            <a href="{{ auth()->user()->can('panel.businesses.view') ? route('panel.businesses.show', $business) : '#' }}" 
                               class="block px-6 py-4 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors group">
                                <div class="flex items-center justify-between">
                                    <div class="flex-1">
                                        <p class="font-semibold text-slate-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">{{ $business->name }}</p>
                                        <div class="flex items-center gap-4 mt-1.5">
                                            <span class="text-xs text-slate-500 dark:text-slate-400">
                                                <i class="fa-solid fa-calendar-check mr-1"></i>{{ $business->appointments_count }} записей
                                            </span>
                                            <span class="text-xs text-slate-500 dark:text-slate-400">
                                                <i class="fa-solid fa-users mr-1"></i>{{ $business->clients_count }} клиентов
                                            </span>
                                        </div>
                                    </div>
                                    <i class="fa-solid fa-chevron-right text-slate-400 dark:text-slate-500 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors"></i>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Последние пользователи -->
                @if($canViewUsers && $recentUsers && $recentUsers->isNotEmpty())
                <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between bg-slate-50 dark:bg-slate-800/50">
                        <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Последние пользователи</h3>
                        @if(auth()->user()->can('panel.users.view'))
                        <a href="{{ route('panel.users') }}" 
                           class="text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 font-medium transition-colors">
                            Все →
                        </a>
                        @endif
                    </div>
                    <div class="divide-y divide-slate-200 dark:divide-slate-800">
                        @foreach($recentUsers as $user)
                            <a href="{{ auth()->user()->can('panel.users.update') ? route('panel.users.edit', $user) : '#' }}" 
                               class="block px-6 py-4 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors group">
                                <div class="flex items-center gap-4">
                                    <div class="h-10 w-10 rounded-full bg-gradient-to-br from-indigo-400 to-indigo-600 dark:from-indigo-500 dark:to-indigo-700 flex items-center justify-center text-white font-semibold text-sm shadow-sm">
                                        {{ strtoupper(substr($user->first_name ?? 'U', 0, 1) . substr($user->last_name ?? '', 0, 1)) }}
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="font-semibold text-slate-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors truncate">
                                            {{ $user->first_name }} {{ $user->last_name }}
                                        </p>
                                        <p class="text-xs text-slate-500 dark:text-slate-400 truncate mt-0.5">
                                            {{ $user->email }}
                                        </p>
                                    </div>
                                    <i class="fa-solid fa-chevron-right text-slate-400 dark:text-slate-500 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors"></i>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Последние записи -->
                @if($canViewAppointments && $recentAppointments && $recentAppointments->isNotEmpty())
                <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden lg:col-span-2">
                    <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between bg-slate-50 dark:bg-slate-800/50">
                        <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Последние записи</h3>
                        @if(auth()->user()->can('panel.appointments.view'))
                        <a href="{{ route('panel.appointments') }}" 
                           class="text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 font-medium transition-colors">
                            Все →
                        </a>
                        @endif
                    </div>
                    @if($canViewSupport)
                    <!-- Таблица для поддержки -->
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="border-b border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/50">
                                    <th class="text-left py-3 px-6 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wide">Бизнес</th>
                                    <th class="text-left py-3 px-6 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wide">Клиент</th>
                                    <th class="text-left py-3 px-6 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wide">Услуга</th>
                                    <th class="text-left py-3 px-6 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wide">Дата</th>
                                    <th class="text-left py-3 px-6 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wide">Статус</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentAppointments->take(10) as $appointment)
                                    <tr class="border-b border-slate-100 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                                        <td class="py-3 px-6">
                                            <p class="text-sm font-medium text-slate-900 dark:text-white">{{ $appointment->business->name ?? 'Неизвестный бизнес' }}</p>
                                        </td>
                                        <td class="py-3 px-6">
                                            <p class="text-sm text-slate-900 dark:text-white">{{ $appointment->client->full_name ?? 'Неизвестный клиент' }}</p>
                                        </td>
                                        <td class="py-3 px-6">
                                            <p class="text-sm text-slate-600 dark:text-slate-400">{{ $appointment->service->name ?? '-' }}</p>
                                        </td>
                                        <td class="py-3 px-6">
                                            <p class="text-sm text-slate-600 dark:text-slate-400">
                                                {{ \Carbon\Carbon::parse($appointment->date)->format('d.m.Y') }} {{ $appointment->time ?? '' }}
                                            </p>
                                        </td>
                                        <td class="py-3 px-6">
                                            @php
                                                $statusColors = [
                                                    'pending' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-500/20 dark:text-yellow-400',
                                                    'confirmed' => 'bg-blue-100 text-blue-800 dark:bg-blue-500/20 dark:text-blue-400',
                                                    'completed' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-500/20 dark:text-emerald-400',
                                                    'cancelled' => 'bg-rose-100 text-rose-800 dark:bg-rose-500/20 dark:text-rose-400',
                                                ];
                                                $statusLabels = [
                                                    'pending' => 'Ожидает',
                                                    'confirmed' => 'Подтверждена',
                                                    'completed' => 'Завершена',
                                                    'cancelled' => 'Отменена',
                                                ];
                                            @endphp
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$appointment->status] ?? 'bg-slate-100 text-slate-800 dark:bg-slate-800 dark:text-slate-300' }}">
                                                {{ $statusLabels[$appointment->status] ?? $appointment->status }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <!-- Список для других ролей -->
                    <div class="divide-y divide-slate-200 dark:divide-slate-800">
                        @foreach($recentAppointments->take(5) as $appointment)
                            <div class="px-6 py-4 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                                <div class="flex items-center justify-between">
                                    <div class="flex-1">
                                        <p class="font-semibold text-slate-900 dark:text-white">{{ $appointment->client->full_name ?? 'Неизвестный клиент' }}</p>
                                        <div class="flex items-center gap-4 mt-1.5">
                                            <span class="text-xs text-slate-500 dark:text-slate-400">
                                                <i class="fa-solid fa-building mr-1"></i>{{ $appointment->business->name ?? 'Неизвестный бизнес' }}
                                            </span>
                                            <span class="text-xs text-slate-500 dark:text-slate-400">
                                                <i class="fa-solid fa-briefcase mr-1"></i>{{ $appointment->service->name ?? 'Неизвестная услуга' }}
                                            </span>
                                        </div>
                                    </div>
                                    <span class="text-sm font-medium text-slate-600 dark:text-slate-400">
                                        {{ \Carbon\Carbon::parse($appointment->date)->format('d.m.Y') }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    @endif
                </div>
                @endif
            </div>
        </div>

        <!-- Топ бизнесов -->
        @if($canViewBusinesses && $topBusinesses && $topBusinesses->isNotEmpty())
        <div>
            <h2 class="text-xl font-semibold text-slate-900 dark:text-white mb-4">Топ бизнесов по активности</h2>
            <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/50 flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Рейтинг</h3>
                    @if(auth()->user()->can('panel.businesses.view'))
                    <a href="{{ route('panel.businesses') }}" 
                       class="text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 font-medium transition-colors">
                        Все бизнесы →
                    </a>
                    @endif
                </div>
                <div class="divide-y divide-slate-200 dark:divide-slate-800">
                    @foreach($topBusinesses as $index => $business)
                        <a href="{{ auth()->user()->can('panel.businesses.view') ? route('panel.businesses.show', $business) : '#' }}" 
                           class="block px-6 py-5 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors group">
                            <div class="flex items-center gap-4">
                                <div class="shrink-0 w-12 h-12 rounded-xl bg-gradient-to-br from-indigo-400 to-indigo-600 dark:from-indigo-500 dark:to-indigo-700 flex items-center justify-center text-white font-bold text-lg shadow-sm">
                                    {{ $index + 1 }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="font-semibold text-slate-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">{{ $business->name }}</p>
                                    <div class="flex items-center gap-4 mt-1.5">
                                        <span class="text-xs text-slate-500 dark:text-slate-400">
                                            <i class="fa-solid fa-calendar-check mr-1"></i>{{ $business->appointments_count }} записей
                                        </span>
                                        <span class="text-xs text-slate-500 dark:text-slate-400">
                                            <i class="fa-solid fa-users mr-1"></i>{{ $business->clients_count }} клиентов
                                        </span>
                                    </div>
                                </div>
                                <i class="fa-solid fa-chevron-right text-slate-400 dark:text-slate-500 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors"></i>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        <!-- Быстрые действия - Улучшенный дизайн -->
        <div>
            <h2 class="text-xl font-semibold text-slate-900 dark:text-white mb-4">Быстрые действия</h2>
            <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-6">
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
                    @if(auth()->user()->can('panel.users.view'))
                    <a href="{{ route('panel.users') }}" 
                       class="flex flex-col items-center justify-center p-4 bg-indigo-50 dark:bg-indigo-500/10 hover:bg-indigo-100 dark:hover:bg-indigo-500/20 rounded-xl border border-indigo-200 dark:border-indigo-800 transition-all duration-200 group">
                        <div class="w-12 h-12 bg-indigo-600 dark:bg-indigo-500 rounded-xl flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                            <i class="fa-solid fa-users-gear text-white text-lg"></i>
                        </div>
                        <span class="text-sm font-medium text-slate-900 dark:text-white text-center">Пользователи</span>
                    </a>
                    @endif
                    @if(auth()->user()->can('panel.roles.view'))
                    <a href="{{ route('panel.roles') }}" 
                       class="flex flex-col items-center justify-center p-4 bg-purple-50 dark:bg-purple-500/10 hover:bg-purple-100 dark:hover:bg-purple-500/20 rounded-xl border border-purple-200 dark:border-purple-800 transition-all duration-200 group">
                        <div class="w-12 h-12 bg-purple-600 dark:bg-purple-500 rounded-xl flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                            <i class="fa-solid fa-shield-halved text-white text-lg"></i>
                        </div>
                        <span class="text-sm font-medium text-slate-900 dark:text-white text-center">Роли</span>
                    </a>
                    @endif
                    @if(auth()->user()->can('panel.businesses.view'))
                    <a href="{{ route('panel.businesses') }}" 
                       class="flex flex-col items-center justify-center p-4 bg-emerald-50 dark:bg-emerald-500/10 hover:bg-emerald-100 dark:hover:bg-emerald-500/20 rounded-xl border border-emerald-200 dark:border-emerald-800 transition-all duration-200 group">
                        <div class="w-12 h-12 bg-emerald-600 dark:bg-emerald-500 rounded-xl flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                            <i class="fa-solid fa-building text-white text-lg"></i>
                        </div>
                        <span class="text-sm font-medium text-slate-900 dark:text-white text-center">Бизнесы</span>
                    </a>
                    @endif
                    @if(auth()->user()->can('panel.appointments.view'))
                    <a href="{{ route('panel.appointments') }}" 
                       class="flex flex-col items-center justify-center p-4 bg-amber-50 dark:bg-amber-500/10 hover:bg-amber-100 dark:hover:bg-amber-500/20 rounded-xl border border-amber-200 dark:border-amber-800 transition-all duration-200 group">
                        <div class="w-12 h-12 bg-amber-600 dark:bg-amber-500 rounded-xl flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                            <i class="fa-solid fa-calendar-check text-white text-lg"></i>
                        </div>
                        <span class="text-sm font-medium text-slate-900 dark:text-white text-center">Записи</span>
                    </a>
                    @endif
                    @if(auth()->user()->can('panel.analytics.view'))
                    <a href="{{ route('panel.analytics') }}" 
                       class="flex flex-col items-center justify-center p-4 bg-blue-50 dark:bg-blue-500/10 hover:bg-blue-100 dark:hover:bg-blue-500/20 rounded-xl border border-blue-200 dark:border-blue-800 transition-all duration-200 group">
                        <div class="w-12 h-12 bg-blue-600 dark:bg-blue-500 rounded-xl flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                            <i class="fa-solid fa-chart-bar text-white text-lg"></i>
                        </div>
                        <span class="text-sm font-medium text-slate-900 dark:text-white text-center">Аналитика</span>
                    </a>
                    @endif
                    @if(auth()->user()->can('panel.support.view') || auth()->user()->can('panel.tickets.view'))
                    <a href="{{ route('panel.tickets') }}" 
                       class="flex flex-col items-center justify-center p-4 bg-teal-50 dark:bg-teal-500/10 hover:bg-teal-100 dark:hover:bg-teal-500/20 rounded-xl border border-teal-200 dark:border-teal-800 transition-all duration-200 group">
                        <div class="w-12 h-12 bg-teal-600 dark:bg-teal-500 rounded-xl flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                            <i class="fa-solid fa-headset text-white text-lg"></i>
                        </div>
                        <span class="text-sm font-medium text-slate-900 dark:text-white text-center">Поддержка / Тикеты</span>
                    </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

@endsection
