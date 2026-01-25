@extends('layouts.panel')

@section('title', 'Панель управления')

@section('content')
    <div class="max-w-7xl 2xl:max-w-[1600px] mx-auto space-y-6">
        <!-- Заголовок -->
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-slate-900 dark:text-white">Панель управления</h2>
                <p class="text-slate-600 dark:text-slate-400 mt-1">Добро пожаловать в панель управления</p>
            </div>
            <form action="{{ route('panel.refresh') }}" method="POST">
                @csrf
                <button type="submit" 
                        class="inline-flex items-center gap-2 px-4 py-2 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 rounded-lg text-sm font-medium hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors">
                    <i class="fa-solid fa-rotate text-xs"></i>
                    <span>Обновить</span>
                </button>
            </form>
        </div>

        <!-- Основная статистика -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            @can('panel.businesses.view')
                <div class="bg-white dark:bg-slate-900 rounded-xl p-6 border border-slate-200 dark:border-slate-800">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-slate-500 dark:text-slate-400">Бизнесов</p>
                            <p class="text-3xl font-bold text-slate-900 dark:text-white mt-2">{{ $stats['total_businesses'] }}</p>
                        </div>
                        <div class="h-14 w-14 rounded-xl bg-indigo-100 dark:bg-indigo-500/20 flex items-center justify-center">
                            <i class="fa-solid fa-building text-indigo-600 dark:text-indigo-400 text-2xl"></i>
                        </div>
                    </div>
                </div>
            @endcan

            @can('panel.users.view')
                <div class="bg-white dark:bg-slate-900 rounded-xl p-6 border border-slate-200 dark:border-slate-800">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-slate-500 dark:text-slate-400">Пользователей</p>
                            <p class="text-3xl font-bold text-slate-900 dark:text-white mt-2">{{ $stats['total_users'] }}</p>
                        </div>
                        <div class="h-14 w-14 rounded-xl bg-emerald-100 dark:bg-emerald-500/20 flex items-center justify-center">
                            <i class="fa-solid fa-users text-emerald-600 dark:text-emerald-400 text-2xl"></i>
                        </div>
                    </div>
                </div>
            @endcan

            @can('panel.clients.view')
                <div class="bg-white dark:bg-slate-900 rounded-xl p-6 border border-slate-200 dark:border-slate-800">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-slate-500 dark:text-slate-400">Клиентов</p>
                            <p class="text-3xl font-bold text-slate-900 dark:text-white mt-2">{{ $stats['total_clients'] }}</p>
                        </div>
                        <div class="h-14 w-14 rounded-xl bg-blue-100 dark:bg-blue-500/20 flex items-center justify-center">
                            <i class="fa-solid fa-user-group text-blue-600 dark:text-blue-400 text-2xl"></i>
                        </div>
                    </div>
                </div>
            @endcan

            @can('panel.appointments.view')
                <div class="bg-white dark:bg-slate-900 rounded-xl p-6 border border-slate-200 dark:border-slate-800">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-slate-500 dark:text-slate-400">Записей</p>
                            <p class="text-3xl font-bold text-slate-900 dark:text-white mt-2">{{ $stats['total_appointments'] }}</p>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                                {{ $stats['appointments_today'] }} сегодня
                            </p>
                        </div>
                        <div class="h-14 w-14 rounded-xl bg-amber-100 dark:bg-amber-500/20 flex items-center justify-center">
                            <i class="fa-solid fa-calendar-check text-amber-600 dark:text-amber-400 text-2xl"></i>
                        </div>
                    </div>
                </div>
            @endcan
        </div>

        <!-- Быстрые действия -->
        <div class="bg-white dark:bg-slate-900 rounded-xl p-6 border border-slate-200 dark:border-slate-800">
            <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">Быстрые действия</h3>
            <div class="flex flex-wrap gap-3">
                @can('panel.appointments.view')
                    <a href="{{ route('panel.appointments') }}" 
                       class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition-colors">
                        <i class="fa-solid fa-calendar-check"></i>
                        <span>Просмотр записей</span>
                    </a>
                @endcan

                @can('panel.clients.view')
                    <a href="{{ route('panel.clients') }}" 
                       class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg transition-colors">
                        <i class="fa-solid fa-users"></i>
                        <span>Просмотр клиентов</span>
                    </a>
                @endcan

                @can('panel.users.view')
                    <a href="{{ route('panel.users') }}" 
                       class="inline-flex items-center gap-2 px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-lg transition-colors">
                        <i class="fa-solid fa-users-gear"></i>
                        <span>Управление пользователями</span>
                    </a>
                @endcan

                @can('panel.roles.view')
                    <a href="{{ route('panel.roles') }}" 
                       class="inline-flex items-center gap-2 px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg transition-colors">
                        <i class="fa-solid fa-shield-halved"></i>
                        <span>Управление ролями</span>
                    </a>
                @endcan

                @can('panel.businesses.view')
                    <a href="{{ route('panel.businesses') }}" 
                       class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                        <i class="fa-solid fa-building"></i>
                        <span>Бизнесы</span>
                    </a>
                @endcan

                @can('panel.analytics.view')
                    <a href="{{ route('panel.analytics') }}" 
                       class="inline-flex items-center gap-2 px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded-lg transition-colors">
                        <i class="fa-solid fa-chart-line"></i>
                        <span>Аналитика</span>
                    </a>
                @endcan
            </div>
        </div>
    </div>
@endsection
