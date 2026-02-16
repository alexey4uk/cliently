@extends('layouts.panel')

@section('title', 'Финансовая аналитика')

@section('content')
    <div class="max-w-6xl 2xl:max-w-[1400px] mx-auto">
        <div class="space-y-6">
        <!-- Заголовок -->
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-slate-900 dark:text-white">Финансовая аналитика</h2>
                <p class="text-slate-600 dark:text-slate-400 mt-1">Выручка и финансовые показатели</p>
            </div>
            <a href="{{ route('panel.analytics') }}" 
               class="inline-flex items-center gap-2 px-4 py-2 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 rounded-lg text-sm font-medium hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors">
                <i class="fa-solid fa-arrow-left text-xs"></i>
                <span>Назад</span>
            </a>
        </div>

        <!-- Фильтры -->
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 p-5">
            <form method="GET" action="{{ route('panel.analytics.financial') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
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
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Статус платежа</label>
                    <select name="status" 
                            class="w-full px-3 py-2 border border-slate-300 dark:border-slate-700 rounded-lg bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Все статусы</option>
                        <option value="paid" {{ $filters['status'] == 'paid' ? 'selected' : '' }}>Оплачено</option>
                        <option value="pending" {{ $filters['status'] == 'pending' ? 'selected' : '' }}>Ожидает оплаты</option>
                        <option value="failed" {{ $filters['status'] == 'failed' ? 'selected' : '' }}>Ошибка</option>
                        <option value="cancelled" {{ $filters['status'] == 'cancelled' ? 'selected' : '' }}>Отменено</option>
                        <option value="refunded" {{ $filters['status'] == 'refunded' ? 'selected' : '' }}>Возвращено</option>
                    </select>
                </div>
                <div class="md:col-span-4 flex gap-2">
                    <button type="submit" 
                            class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition-colors">
                        <i class="fa-solid fa-filter mr-2"></i>
                        Применить фильтры
                    </button>
                    <a href="{{ route('panel.analytics.financial') }}" 
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
                        <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Общая выручка</p>
                        <p class="text-2xl font-bold text-slate-900 dark:text-white mt-1">{{ number_format($data['total_revenue'], 2, '.', ' ') }} BYN</p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 dark:bg-green-500/20 rounded-xl flex items-center justify-center">
                        <i class="fa-solid fa-money-bill-wave text-green-600 dark:text-green-400 text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Выручка за период</p>
                        <p class="text-2xl font-bold text-slate-900 dark:text-white mt-1">{{ number_format($data['revenue_period'], 2, '.', ' ') }} BYN</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 dark:bg-blue-500/20 rounded-xl flex items-center justify-center">
                        <i class="fa-solid fa-chart-line text-blue-600 dark:text-blue-400 text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Выручка за месяц</p>
                        <p class="text-2xl font-bold text-slate-900 dark:text-white mt-1">{{ number_format($data['revenue_month'], 2, '.', ' ') }} BYN</p>
                    </div>
                    <div class="w-12 h-12 bg-purple-100 dark:bg-purple-500/20 rounded-xl flex items-center justify-center">
                        <i class="fa-solid fa-calendar-month text-purple-600 dark:text-purple-400 text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Средний чек</p>
                        <p class="text-2xl font-bold text-slate-900 dark:text-white mt-1">{{ number_format($data['average_check'], 2, '.', ' ') }} BYN</p>
                    </div>
                    <div class="w-12 h-12 bg-amber-100 dark:bg-amber-500/20 rounded-xl flex items-center justify-center">
                        <i class="fa-solid fa-calculator text-amber-600 dark:text-amber-400 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- График динамики выручки -->
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 p-6">
            <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">Динамика выручки</h3>
            <div class="h-80">
                <canvas id="revenueChart"></canvas>
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

            <!-- Статусы платежей -->
            <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 p-6">
                <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">Статусы платежей</h3>
                <div class="h-64">
                    <canvas id="statusChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Топ тарифов по выручке -->
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-800">
                <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Топ тарифов по выручке</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-slate-50 dark:bg-slate-800/50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Тариф</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Количество</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Выручка</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                        @forelse($data['revenue_by_plan'] as $item)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                                <td class="px-6 py-4 text-sm font-medium text-slate-900 dark:text-white">{{ $item['plan_name'] }}</td>
                                <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-400 text-right">{{ number_format($item['count'], 0, '.', ' ') }}</td>
                                <td class="px-6 py-4 text-sm font-semibold text-slate-900 dark:text-white text-right">{{ number_format($item['revenue'], 2, '.', ' ') }} BYN</td>
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

        <!-- Статистика по статусам -->
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-800">
                <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Статистика по статусам платежей</h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                    <div class="text-center">
                        <p class="text-2xl font-bold text-slate-900 dark:text-white">{{ number_format($data['status_stats']['paid'], 0, '.', ' ') }}</p>
                        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Оплачено</p>
                    </div>
                    <div class="text-center">
                        <p class="text-2xl font-bold text-slate-900 dark:text-white">{{ number_format($data['status_stats']['pending'], 0, '.', ' ') }}</p>
                        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Ожидает</p>
                    </div>
                    <div class="text-center">
                        <p class="text-2xl font-bold text-slate-900 dark:text-white">{{ number_format($data['status_stats']['failed'], 0, '.', ' ') }}</p>
                        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Ошибка</p>
                    </div>
                    <div class="text-center">
                        <p class="text-2xl font-bold text-slate-900 dark:text-white">{{ number_format($data['status_stats']['cancelled'], 0, '.', ' ') }}</p>
                        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Отменено</p>
                    </div>
                    <div class="text-center">
                        <p class="text-2xl font-bold text-slate-900 dark:text-white">{{ number_format($data['status_stats']['refunded'], 0, '.', ' ') }}</p>
                        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Возвращено</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Последние платежи -->
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-800">
                <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Последние платежи</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-slate-50 dark:bg-slate-800/50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Пользователь</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Тариф</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Сумма</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Дата оплаты</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                        @forelse($data['recent_payments'] as $payment)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                                <td class="px-6 py-4 text-sm font-medium text-slate-900 dark:text-white">{{ $payment->user->name ?? 'Неизвестно' }}</td>
                                <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-400">{{ $payment->plan->name ?? 'Неизвестно' }}</td>
                                <td class="px-6 py-4 text-sm font-semibold text-slate-900 dark:text-white text-right">{{ number_format($payment->amount, 2, '.', ' ') }} BYN</td>
                                <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-400">{{ $payment->paid_at ? $payment->paid_at->format('d.m.Y H:i') : '-' }}</td>
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
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const revenueData = @json($data['revenue_by_period']);
                const plansData = @json($data['revenue_by_plan']);
                const statusData = @json($data['status_stats']);

                const isDarkMode = () => document.documentElement.classList.contains('dark');
                const getThemeColors = () => ({
                    text: isDarkMode() ? '#e2e8f0' : '#1e293b',
                    textSecondary: isDarkMode() ? '#94a3b8' : '#64748b',
                    grid: 'rgba(148, 163, 184, 0.1)'
                });

                // График динамики выручки
                const revenueCtx = document.getElementById('revenueChart');
                if (revenueCtx && revenueData.length > 0) {
                    const colors = getThemeColors();
                    new Chart(revenueCtx, {
                        type: 'line',
                        data: {
                            labels: revenueData.map(item => item.label),
                            datasets: [{
                                label: 'Выручка (BYN)',
                                data: revenueData.map(item => item.revenue),
                                borderColor: 'rgb(34, 197, 94)',
                                backgroundColor: 'rgba(34, 197, 94, 0.1)',
                                tension: 0.4,
                                fill: true,
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    labels: { color: colors.text }
                                },
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            return 'Выручка: ' + new Intl.NumberFormat('ru-RU').format(context.parsed.y) + ' BYN';
                                        }
                                    }
                                }
                            },
                            scales: {
                                x: {
                                    ticks: { color: colors.textSecondary },
                                    grid: { color: colors.grid }
                                },
                                y: {
                                    ticks: { 
                                        color: colors.textSecondary,
                                        callback: function(value) {
                                            return value.toFixed(2) + ' BYN';
                                        }
                                    },
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
                                data: plansData.map(item => item.revenue),
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
                                },
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            return context.label + ': ' + new Intl.NumberFormat('ru-RU').format(context.parsed) + ' BYN';
                                        }
                                    }
                                }
                            }
                        }
                    });
                }

                // График статусов платежей
                const statusCtx = document.getElementById('statusChart');
                if (statusCtx) {
                    const colors = getThemeColors();
                    const statusLabels = {
                        'paid': 'Оплачено',
                        'pending': 'Ожидает',
                        'failed': 'Ошибка',
                        'cancelled': 'Отменено',
                        'refunded': 'Возвращено',
                    };
                    new Chart(statusCtx, {
                        type: 'pie',
                        data: {
                            labels: Object.keys(statusData).map(key => statusLabels[key] || key),
                            datasets: [{
                                data: Object.values(statusData),
                                backgroundColor: [
                                    'rgb(34, 197, 94)',
                                    'rgb(251, 146, 60)',
                                    'rgb(239, 68, 68)',
                                    'rgb(148, 163, 184)',
                                    'rgb(139, 92, 246)',
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
