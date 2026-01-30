@extends('layouts.panel')

@section('title', 'Аналитика подписок')

@section('content')
    <div class="max-w-6xl 2xl:max-w-[1400px] mx-auto">
        <div class="space-y-6">
        <!-- Заголовок -->
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-slate-900 dark:text-white">Аналитика подписок</h2>
                <p class="text-slate-600 dark:text-slate-400 mt-1">Статистика подписок и тарифов</p>
            </div>
            <a href="{{ route('panel.analytics') }}" 
               class="inline-flex items-center gap-2 px-4 py-2 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 rounded-lg text-sm font-medium hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors">
                <i class="fa-solid fa-arrow-left text-xs"></i>
                <span>Назад</span>
            </a>
        </div>

        <!-- Фильтры -->
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 p-5">
            <form method="GET" action="{{ route('panel.analytics.subscriptions') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Дата начала</label>
                    <input type="date" name="date_from" value="{{ $filters['date_from'] }}" 
                           class="w-full px-3 py-2 border border-slate-300 dark:border-slate-700 rounded-lg bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Дата конца</label>
                    <input type="date" name="date_to" value="{{ $filters['date_to'] }}" 
                           class="w-full px-3 py-2 border border-slate-300 dark:border-slate-700 rounded-lg bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Тариф</label>
                    <select name="plan_id" 
                            class="w-full px-3 py-2 border border-slate-300 dark:border-slate-700 rounded-lg bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Все тарифы</option>
                        @foreach($plans as $plan)
                            <option value="{{ $plan->id }}" {{ $filters['plan_id'] == $plan->id ? 'selected' : '' }}>
                                {{ $plan->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Статус подписки</label>
                    <select name="status" 
                            class="w-full px-3 py-2 border border-slate-300 dark:border-slate-700 rounded-lg bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Все статусы</option>
                        <option value="active" {{ $filters['status'] == 'active' ? 'selected' : '' }}>Активная</option>
                        <option value="trial" {{ $filters['status'] == 'trial' ? 'selected' : '' }}>Пробная</option>
                        <option value="cancelled" {{ $filters['status'] == 'cancelled' ? 'selected' : '' }}>Отменена</option>
                        <option value="expired" {{ $filters['status'] == 'expired' ? 'selected' : '' }}>Истекла</option>
                    </select>
                </div>
                <div class="md:col-span-4 flex gap-2">
                    <button type="submit" 
                            class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition-colors">
                        <i class="fa-solid fa-filter mr-2"></i>
                        Применить фильтры
                    </button>
                    <a href="{{ route('panel.analytics.subscriptions') }}" 
                       class="px-4 py-2 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 rounded-lg text-sm font-medium hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors">
                        <i class="fa-solid fa-xmark mr-2"></i>
                        Сбросить
                    </a>
                </div>
            </form>
        </div>

        <!-- Метрики -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Активные подписки</p>
                        <p class="text-2xl font-bold text-slate-900 dark:text-white mt-1">{{ number_format($data['active_subscriptions'], 0, '.', ' ') }}</p>
                    </div>
                    <div class="w-12 h-12 bg-emerald-100 dark:bg-emerald-500/20 rounded-xl flex items-center justify-center">
                        <i class="fa-solid fa-check-circle text-emerald-600 dark:text-emerald-400 text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Пробные подписки</p>
                        <p class="text-2xl font-bold text-slate-900 dark:text-white mt-1">{{ number_format($data['trial_subscriptions'], 0, '.', ' ') }}</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 dark:bg-blue-500/20 rounded-xl flex items-center justify-center">
                        <i class="fa-solid fa-clock text-blue-600 dark:text-blue-400 text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Отмененные подписки</p>
                        <p class="text-2xl font-bold text-slate-900 dark:text-white mt-1">{{ number_format($data['cancelled_subscriptions'], 0, '.', ' ') }}</p>
                    </div>
                    <div class="w-12 h-12 bg-rose-100 dark:bg-rose-500/20 rounded-xl flex items-center justify-center">
                        <i class="fa-solid fa-xmark-circle text-rose-600 dark:text-rose-400 text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Конверсия</p>
                        <p class="text-2xl font-bold text-slate-900 dark:text-white mt-1">{{ number_format($data['conversion_rate'], 1, '.', ' ') }}%</p>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Пробные → Платные</p>
                    </div>
                    <div class="w-12 h-12 bg-purple-100 dark:bg-purple-500/20 rounded-xl flex items-center justify-center">
                        <i class="fa-solid fa-chart-line text-purple-600 dark:text-purple-400 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- График динамики подписок -->
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 p-6">
            <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">Динамика подписок</h3>
            <div class="h-80">
                <canvas id="subscriptionsChart"></canvas>
            </div>
        </div>

        <!-- Графики распределения -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Распределение по тарифам -->
            <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 p-6">
                <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">Распределение по тарифам</h3>
                <div class="h-64">
                    <canvas id="plansChart"></canvas>
                </div>
            </div>

            <!-- Статусы подписок -->
            <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 p-6">
                <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">Статусы подписок</h3>
                <div class="h-64">
                    <canvas id="statusChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Статистика по тарифам -->
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-800">
                <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Статистика по тарифам</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-slate-50 dark:bg-slate-800/50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Тариф</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Количество</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                        @forelse($data['distribution_by_plan'] as $item)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                                <td class="px-6 py-4 text-sm font-medium text-slate-900 dark:text-white">{{ $item['plan_name'] }}</td>
                                <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-400 text-right">{{ number_format($item['count'], 0, '.', ' ') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="px-6 py-8 text-center text-sm text-slate-500 dark:text-slate-400">Нет данных</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Новые подписки за период -->
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-800">
                <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Новые подписки за период</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-slate-50 dark:bg-slate-800/50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Пользователь</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Тариф</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Статус</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Дата создания</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                        @forelse($data['new_subscriptions'] as $subscription)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                                <td class="px-6 py-4 text-sm font-medium text-slate-900 dark:text-white">{{ $subscription->user->name ?? 'Неизвестно' }}</td>
                                <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-400">{{ $subscription->plan->name ?? 'Неизвестно' }}</td>
                                <td class="px-6 py-4">
                                    @if($subscription->status === 'active')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800 dark:bg-emerald-500/20 dark:text-emerald-400">
                                            Активная
                                        </span>
                                    @elseif($subscription->status === 'trial')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-500/20 dark:text-blue-400">
                                            Пробная
                                        </span>
                                    @elseif($subscription->status === 'cancelled')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-rose-100 text-rose-800 dark:bg-rose-500/20 dark:text-rose-400">
                                            Отменена
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-800 dark:bg-slate-500/20 dark:text-slate-400">
                                            {{ $subscription->status }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-400">{{ $subscription->created_at->format('d.m.Y H:i') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-sm text-slate-500 dark:text-slate-400">Нет данных</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Отмененные подписки -->
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-800">
                <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Отмененные подписки</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-slate-50 dark:bg-slate-800/50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Пользователь</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Тариф</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Дата отмены</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                        @forelse($data['cancelled_subscriptions_list'] as $subscription)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                                <td class="px-6 py-4 text-sm font-medium text-slate-900 dark:text-white">{{ $subscription->user->name ?? 'Неизвестно' }}</td>
                                <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-400">{{ $subscription->plan->name ?? 'Неизвестно' }}</td>
                                <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-400">{{ $subscription->cancelled_at ? $subscription->cancelled_at->format('d.m.Y H:i') : '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-6 py-8 text-center text-sm text-slate-500 dark:text-slate-400">Нет данных</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const subscriptionsData = @json($data['subscriptions_by_period']);
                const plansData = @json($data['distribution_by_plan']);
                const statusData = @json($data['status_stats']);

                const isDarkMode = () => document.documentElement.classList.contains('dark');
                const getThemeColors = () => ({
                    text: isDarkMode() ? '#e2e8f0' : '#1e293b',
                    textSecondary: isDarkMode() ? '#94a3b8' : '#64748b',
                    grid: 'rgba(148, 163, 184, 0.1)'
                });

                // График динамики подписок
                const subscriptionsCtx = document.getElementById('subscriptionsChart');
                if (subscriptionsCtx && subscriptionsData.length > 0) {
                    const colors = getThemeColors();
                    new Chart(subscriptionsCtx, {
                        type: 'line',
                        data: {
                            labels: subscriptionsData.map(item => item.label),
                            datasets: [
                                {
                                    label: 'Всего',
                                    data: subscriptionsData.map(item => item.total),
                                    borderColor: 'rgb(59, 130, 246)',
                                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                                    tension: 0.4,
                                    fill: true,
                                },
                                {
                                    label: 'Активные',
                                    data: subscriptionsData.map(item => item.active),
                                    borderColor: 'rgb(34, 197, 94)',
                                    backgroundColor: 'rgba(34, 197, 94, 0.1)',
                                    tension: 0.4,
                                    fill: true,
                                },
                                {
                                    label: 'Пробные',
                                    data: subscriptionsData.map(item => item.trial),
                                    borderColor: 'rgb(251, 146, 60)',
                                    backgroundColor: 'rgba(251, 146, 60, 0.1)',
                                    tension: 0.4,
                                    fill: true,
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    labels: { color: colors.text }
                                }
                            },
                            scales: {
                                x: {
                                    ticks: { color: colors.textSecondary },
                                    grid: { color: colors.grid }
                                },
                                y: {
                                    ticks: { color: colors.textSecondary },
                                    grid: { color: colors.grid }
                                }
                            }
                        }
                    });
                }

                // График распределения по тарифам
                const plansCtx = document.getElementById('plansChart');
                if (plansCtx && plansData.length > 0) {
                    const colors = getThemeColors();
                    const chartColors = [
                        'rgb(99, 102, 241)',
                        'rgb(16, 185, 129)',
                        'rgb(59, 130, 246)',
                        'rgb(236, 72, 153)',
                        'rgb(251, 146, 60)',
                        'rgb(139, 92, 246)',
                    ];
                    new Chart(plansCtx, {
                        type: 'pie',
                        data: {
                            labels: plansData.map(item => item.plan_name),
                            datasets: [{
                                data: plansData.map(item => item.count),
                                backgroundColor: chartColors.slice(0, plansData.length),
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                    labels: { color: colors.text }
                                }
                            }
                        }
                    });
                }

                // График статусов подписок
                const statusCtx = document.getElementById('statusChart');
                if (statusCtx) {
                    const colors = getThemeColors();
                    const statusLabels = {
                        'active': 'Активные',
                        'trial': 'Пробные',
                        'cancelled': 'Отмененные',
                        'expired': 'Истекшие',
                    };
                    new Chart(statusCtx, {
                        type: 'pie',
                        data: {
                            labels: Object.keys(statusData).map(key => statusLabels[key] || key),
                            datasets: [{
                                data: Object.values(statusData),
                                backgroundColor: [
                                    'rgb(34, 197, 94)',
                                    'rgb(59, 130, 246)',
                                    'rgb(239, 68, 68)',
                                    'rgb(148, 163, 184)',
                                ],
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                    labels: { color: colors.text }
                                }
                            }
                        }
                    });
                }
            });
        </script>
    @endpush
@endsection
