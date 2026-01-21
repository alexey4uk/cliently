@extends('layouts.panel')

@section('title', 'Панель администратора')

@section('content')
    <div class="space-y-6">
        <!-- Заголовок -->
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-slate-900 dark:text-white">Панель администратора</h2>
                <p class="text-slate-600 dark:text-slate-400 mt-1">Обзор всей системы</p>
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
            <div class="bg-white dark:bg-slate-900 rounded-xl p-6 border border-slate-200 dark:border-slate-800">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-slate-500 dark:text-slate-400">Всего бизнесов</p>
                        <p class="text-3xl font-bold text-slate-900 dark:text-white mt-2">{{ $stats['total_businesses'] }}</p>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                            +{{ $stats['new_businesses_week'] }} за неделю
                        </p>
                    </div>
                    <div class="h-14 w-14 rounded-xl bg-indigo-100 dark:bg-indigo-500/20 flex items-center justify-center">
                        <i class="fa-solid fa-building text-indigo-600 dark:text-indigo-400 text-2xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-slate-900 rounded-xl p-6 border border-slate-200 dark:border-slate-800">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-slate-500 dark:text-slate-400">Всего пользователей</p>
                        <p class="text-3xl font-bold text-slate-900 dark:text-white mt-2">{{ $stats['total_users'] }}</p>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                            +{{ $stats['new_users_week'] }} за неделю
                        </p>
                    </div>
                    <div class="h-14 w-14 rounded-xl bg-emerald-100 dark:bg-emerald-500/20 flex items-center justify-center">
                        <i class="fa-solid fa-users text-emerald-600 dark:text-emerald-400 text-2xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-slate-900 rounded-xl p-6 border border-slate-200 dark:border-slate-800">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-slate-500 dark:text-slate-400">Всего клиентов</p>
                        <p class="text-3xl font-bold text-slate-900 dark:text-white mt-2">{{ $stats['total_clients'] }}</p>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                            +{{ $stats['new_clients_week'] }} за неделю
                        </p>
                    </div>
                    <div class="h-14 w-14 rounded-xl bg-blue-100 dark:bg-blue-500/20 flex items-center justify-center">
                        <i class="fa-solid fa-user-group text-blue-600 dark:text-blue-400 text-2xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-slate-900 rounded-xl p-6 border border-slate-200 dark:border-slate-800">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-slate-500 dark:text-slate-400">Всего записей</p>
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
        </div>

        <!-- Статистика записей -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white dark:bg-slate-900 rounded-xl p-6 border border-slate-200 dark:border-slate-800">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-slate-500 dark:text-slate-400">Ожидают</p>
                        <p class="text-2xl font-bold text-slate-900 dark:text-white mt-1">{{ $stats['appointments_pending'] }}</p>
                    </div>
                    <div class="h-12 w-12 rounded-xl bg-yellow-100 dark:bg-yellow-500/20 flex items-center justify-center">
                        <i class="fa-solid fa-clock text-yellow-600 dark:text-yellow-400 text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-slate-900 rounded-xl p-6 border border-slate-200 dark:border-slate-800">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-slate-500 dark:text-slate-400">Подтверждены</p>
                        <p class="text-2xl font-bold text-slate-900 dark:text-white mt-1">{{ $stats['appointments_confirmed'] }}</p>
                    </div>
                    <div class="h-12 w-12 rounded-xl bg-blue-100 dark:bg-blue-500/20 flex items-center justify-center">
                        <i class="fa-solid fa-check text-blue-600 dark:text-blue-400 text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-slate-900 rounded-xl p-6 border border-slate-200 dark:border-slate-800">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-slate-500 dark:text-slate-400">Завершены</p>
                        <p class="text-2xl font-bold text-slate-900 dark:text-white mt-1">{{ $stats['appointments_completed'] }}</p>
                    </div>
                    <div class="h-12 w-12 rounded-xl bg-emerald-100 dark:bg-emerald-500/20 flex items-center justify-center">
                        <i class="fa-solid fa-check-circle text-emerald-600 dark:text-emerald-400 text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-slate-900 rounded-xl p-6 border border-slate-200 dark:border-slate-800">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-slate-500 dark:text-slate-400">Отменены</p>
                        <p class="text-2xl font-bold text-slate-900 dark:text-white mt-1">{{ $stats['appointments_cancelled'] }}</p>
                    </div>
                    <div class="h-12 w-12 rounded-xl bg-rose-100 dark:bg-rose-500/20 flex items-center justify-center">
                        <i class="fa-solid fa-xmark text-rose-600 dark:text-rose-400 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Последние бизнесы -->
            <div class="bg-white dark:bg-slate-900 rounded-xl p-6 border border-slate-200 dark:border-slate-800">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Последние бизнесы</h3>
                    <a href="{{ route('panel.businesses') }}" 
                       class="text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300">
                        Все бизнесы →
                    </a>
                </div>
                <div class="space-y-3">
                    @forelse($recentBusinesses as $business)
                        <div class="flex items-center justify-between p-3 rounded-lg bg-slate-50 dark:bg-slate-800">
                            <div class="flex-1">
                                <p class="font-medium text-slate-900 dark:text-white">{{ $business->name }}</p>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                                    {{ $business->appointments_count }} записей • {{ $business->clients_count }} клиентов
                                </p>
                            </div>
                            <a href="{{ route('panel.businesses.show', $business) }}" 
                               class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300">
                                <i class="fa-solid fa-arrow-right"></i>
                            </a>
                        </div>
                    @empty
                        <p class="text-sm text-slate-500 dark:text-slate-400 text-center py-4">Нет бизнесов</p>
                    @endforelse
                </div>
            </div>

            <!-- Последние пользователи -->
            <div class="bg-white dark:bg-slate-900 rounded-xl p-6 border border-slate-200 dark:border-slate-800">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Последние пользователи</h3>
                    <a href="{{ route('panel.users') }}" 
                       class="text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300">
                        Все пользователи →
                    </a>
                </div>
                <div class="space-y-3">
                    @forelse($recentUsers as $user)
                        <div class="flex items-center justify-between p-3 rounded-lg bg-slate-50 dark:bg-slate-800">
                            <div class="flex items-center gap-3 flex-1">
                                <div class="h-10 w-10 rounded-full bg-indigo-100 dark:bg-indigo-500/20 flex items-center justify-center">
                                    <i class="fa-solid fa-user text-indigo-600 dark:text-indigo-400"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="font-medium text-slate-900 dark:text-white">
                                        {{ $user->first_name }} {{ $user->last_name }}
                                    </p>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                                        {{ $user->email }}
                                    </p>
                                </div>
                            </div>
                            <a href="{{ route('panel.users.edit', $user) }}" 
                               class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300">
                                <i class="fa-solid fa-arrow-right"></i>
                            </a>
                        </div>
                    @empty
                        <p class="text-sm text-slate-500 dark:text-slate-400 text-center py-4">Нет пользователей</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Топ бизнесов -->
        <div class="bg-white dark:bg-slate-900 rounded-xl p-6 border border-slate-200 dark:border-slate-800">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Топ бизнесов по активности</h3>
                <a href="{{ route('panel.businesses') }}" 
                   class="text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300">
                    Все бизнесы →
                </a>
            </div>
            <div class="space-y-3">
                @forelse($topBusinesses as $index => $business)
                    <div class="flex items-center justify-between p-4 rounded-lg bg-slate-50 dark:bg-slate-800">
                        <div class="flex items-center gap-4 flex-1">
                            <div class="h-10 w-10 rounded-full bg-indigo-100 dark:bg-indigo-500/20 flex items-center justify-center font-bold text-indigo-600 dark:text-indigo-400">
                                {{ $index + 1 }}
                            </div>
                            <div class="flex-1">
                                <p class="font-medium text-slate-900 dark:text-white">{{ $business->name }}</p>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                                    {{ $business->appointments_count }} записей • {{ $business->clients_count }} клиентов
                                </p>
                            </div>
                        </div>
                        <a href="{{ route('panel.businesses.show', $business) }}" 
                           class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300">
                            <i class="fa-solid fa-arrow-right"></i>
                        </a>
                    </div>
                @empty
                    <p class="text-sm text-slate-500 dark:text-slate-400 text-center py-4">Нет данных</p>
                @endforelse
            </div>
        </div>

        <!-- Быстрые действия -->
        <div class="bg-white dark:bg-slate-900 rounded-xl p-6 border border-slate-200 dark:border-slate-800">
            <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">Быстрые действия</h3>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('panel.users') }}" 
                   class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition-colors">
                    <i class="fa-solid fa-users-gear"></i>
                    <span>Управление пользователями</span>
                </a>
                <a href="{{ route('panel.roles') }}" 
                   class="inline-flex items-center gap-2 px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg transition-colors">
                    <i class="fa-solid fa-shield-halved"></i>
                    <span>Управление ролями</span>
                </a>
                <a href="{{ route('panel.businesses') }}" 
                   class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg transition-colors">
                    <i class="fa-solid fa-building"></i>
                    <span>Управление бизнесами</span>
                </a>
                <a href="{{ route('panel.appointments') }}" 
                   class="inline-flex items-center gap-2 px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-lg transition-colors">
                    <i class="fa-solid fa-calendar-check"></i>
                    <span>Все записи</span>
                </a>
            </div>
        </div>
    </div>
@endsection
