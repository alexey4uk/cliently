@extends('layouts.panel')

@section('title', 'Панель поддержки')

@section('content')
    <div class="space-y-6">
        <!-- Заголовок -->
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-slate-900 dark:text-white">Панель поддержки</h2>
                <p class="text-slate-600 dark:text-slate-400 mt-1">Аналитика и поддержка пользователей</p>
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
                        <p class="text-sm text-slate-500 dark:text-slate-400">Активных пользователей</p>
                        <p class="text-3xl font-bold text-slate-900 dark:text-white mt-2">{{ $stats['active_users_week'] }}</p>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                            за неделю
                        </p>
                    </div>
                    <div class="h-14 w-14 rounded-xl bg-indigo-100 dark:bg-indigo-500/20 flex items-center justify-center">
                        <i class="fa-solid fa-users text-indigo-600 dark:text-indigo-400 text-2xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-slate-900 rounded-xl p-6 border border-slate-200 dark:border-slate-800">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-slate-500 dark:text-slate-400">Записей сегодня</p>
                        <p class="text-3xl font-bold text-slate-900 dark:text-white mt-2">{{ $stats['appointments_today'] }}</p>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                            {{ $stats['appointments_week'] }} за неделю
                        </p>
                    </div>
                    <div class="h-14 w-14 rounded-xl bg-emerald-100 dark:bg-emerald-500/20 flex items-center justify-center">
                        <i class="fa-solid fa-calendar-day text-emerald-600 dark:text-emerald-400 text-2xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-slate-900 rounded-xl p-6 border border-slate-200 dark:border-slate-800">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-slate-500 dark:text-slate-400">Ожидают подтверждения</p>
                        <p class="text-3xl font-bold text-slate-900 dark:text-white mt-2">{{ $stats['appointments_pending'] }}</p>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                            требуют внимания
                        </p>
                    </div>
                    <div class="h-14 w-14 rounded-xl bg-yellow-100 dark:bg-yellow-500/20 flex items-center justify-center">
                        <i class="fa-solid fa-clock text-yellow-600 dark:text-yellow-400 text-2xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-slate-900 rounded-xl p-6 border border-slate-200 dark:border-slate-800">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-slate-500 dark:text-slate-400">Новых регистраций</p>
                        <p class="text-3xl font-bold text-slate-900 dark:text-white mt-2">{{ $stats['new_users_week'] }}</p>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                            {{ $stats['new_users_month'] }} за месяц
                        </p>
                    </div>
                    <div class="h-14 w-14 rounded-xl bg-blue-100 dark:bg-blue-500/20 flex items-center justify-center">
                        <i class="fa-solid fa-user-plus text-blue-600 dark:text-blue-400 text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Активные бизнесы -->
            <div class="bg-white dark:bg-slate-900 rounded-xl p-6 border border-slate-200 dark:border-slate-800">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Активные бизнесы</h3>
                    <a href="{{ route('panel.businesses') }}" 
                       class="text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300">
                        Все бизнесы →
                    </a>
                </div>
                <div class="space-y-3">
                    @forelse($activeBusinesses as $business)
                        <div class="flex items-center justify-between p-3 rounded-lg bg-slate-50 dark:bg-slate-800">
                            <div class="flex-1">
                                <p class="font-medium text-slate-900 dark:text-white">{{ $business->name }}</p>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                                    {{ $business->appointments_count }} записей за неделю
                                </p>
                            </div>
                            <a href="{{ route('panel.businesses.show', $business) }}" 
                               class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300">
                                <i class="fa-solid fa-arrow-right"></i>
                            </a>
                        </div>
                    @empty
                        <p class="text-sm text-slate-500 dark:text-slate-400 text-center py-4">Нет активных бизнесов</p>
                    @endforelse
                </div>
            </div>

            <!-- Бизнесы без активности -->
            <div class="bg-white dark:bg-slate-900 rounded-xl p-6 border border-slate-200 dark:border-slate-800">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Бизнесы без активности</h3>
                    <span class="text-xs text-slate-500 dark:text-slate-400">за месяц</span>
                </div>
                <div class="space-y-3">
                    @forelse($inactiveBusinesses as $business)
                        <div class="flex items-center justify-between p-3 rounded-lg bg-rose-50 dark:bg-rose-500/10 border border-rose-200 dark:border-rose-500/20">
                            <div class="flex-1">
                                <p class="font-medium text-slate-900 dark:text-white">{{ $business->name }}</p>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                                    {{ $business->appointments_count }} записей всего • {{ $business->clients_count }} клиентов
                                </p>
                            </div>
                            <a href="{{ route('panel.businesses.show', $business) }}" 
                               class="text-rose-600 dark:text-rose-400 hover:text-rose-700 dark:hover:text-rose-300">
                                <i class="fa-solid fa-arrow-right"></i>
                            </a>
                        </div>
                    @empty
                        <p class="text-sm text-slate-500 dark:text-slate-400 text-center py-4">Все бизнесы активны</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Последние записи -->
        <div class="bg-white dark:bg-slate-900 rounded-xl p-6 border border-slate-200 dark:border-slate-800">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Последние записи</h3>
                <a href="{{ route('panel.appointments') }}" 
                   class="text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300">
                    Все записи →
                </a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-slate-200 dark:border-slate-800">
                            <th class="text-left py-3 px-4 text-sm font-semibold text-slate-500 dark:text-slate-400">Бизнес</th>
                            <th class="text-left py-3 px-4 text-sm font-semibold text-slate-500 dark:text-slate-400">Клиент</th>
                            <th class="text-left py-3 px-4 text-sm font-semibold text-slate-500 dark:text-slate-400">Услуга</th>
                            <th class="text-left py-3 px-4 text-sm font-semibold text-slate-500 dark:text-slate-400">Дата</th>
                            <th class="text-left py-3 px-4 text-sm font-semibold text-slate-500 dark:text-slate-400">Статус</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentAppointments as $appointment)
                            <tr class="border-b border-slate-100 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800/50">
                                <td class="py-3 px-4">
                                    <p class="text-sm font-medium text-slate-900 dark:text-white">{{ $appointment->business->name }}</p>
                                </td>
                                <td class="py-3 px-4">
                                    <p class="text-sm text-slate-900 dark:text-white">{{ $appointment->client->full_name }}</p>
                                </td>
                                <td class="py-3 px-4">
                                    <p class="text-sm text-slate-600 dark:text-slate-400">{{ $appointment->service->name ?? '-' }}</p>
                                </td>
                                <td class="py-3 px-4">
                                    <p class="text-sm text-slate-600 dark:text-slate-400">
                                        {{ $appointment->date->format('d.m.Y') }} {{ $appointment->time }}
                                    </p>
                                </td>
                                <td class="py-3 px-4">
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
                        @empty
                            <tr>
                                <td colspan="5" class="py-8 text-center text-sm text-slate-500 dark:text-slate-400">
                                    Нет записей
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Быстрые действия -->
        <div class="bg-white dark:bg-slate-900 rounded-xl p-6 border border-slate-200 dark:border-slate-800">
            <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">Быстрые действия</h3>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('panel.analytics') }}" 
                   class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition-colors">
                    <i class="fa-solid fa-chart-line"></i>
                    <span>Аналитика</span>
                </a>
                <a href="{{ route('panel.tickets') }}" 
                   class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg transition-colors">
                    <i class="fa-solid fa-headset"></i>
                    <span>Поддержка</span>
                </a>
                <a href="{{ route('panel.appointments') }}" 
                   class="inline-flex items-center gap-2 px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-lg transition-colors">
                    <i class="fa-solid fa-calendar-check"></i>
                    <span>Все записи</span>
                </a>
                <a href="{{ route('panel.businesses') }}" 
                   class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                    <i class="fa-solid fa-building"></i>
                    <span>Бизнесы</span>
                </a>
            </div>
        </div>
    </div>
@endsection
