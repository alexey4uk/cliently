@extends('layouts.panel')

@section('title', 'Аналитика')

@section('content')
    <div class="space-y-6">
        <!-- Заголовок -->
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-slate-900 dark:text-white">Аналитика</h2>
                <p class="text-slate-600 dark:text-slate-400 mt-1">Статистика и показатели системы</p>
            </div>
        </div>

        <!-- Быстрые ссылки на разделы -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @can('panel.analytics.financial')
                <a href="{{ route('panel.analytics.financial') }}" 
                   class="bg-white dark:bg-slate-900 rounded-xl p-5 border border-slate-200 dark:border-slate-800 hover:border-indigo-500 dark:hover:border-indigo-500 transition-colors group">
                    <div class="flex items-center gap-4">
                        <div class="h-12 w-12 rounded-xl bg-indigo-100 dark:bg-indigo-500/20 flex items-center justify-center flex-shrink-0 group-hover:bg-indigo-200 dark:group-hover:bg-indigo-500/30 transition-colors">
                            <i class="fa-solid fa-money-bill-wave text-indigo-600 dark:text-indigo-400 text-xl"></i>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Финансовая аналитика</h3>
                            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Выручка и финансовые показатели</p>
                        </div>
                        <i class="fa-solid fa-arrow-right text-slate-400 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors"></i>
                    </div>
                </a>
            @endcan

            @can('panel.analytics.general')
                <a href="{{ route('panel.analytics.general') }}" 
                   class="bg-white dark:bg-slate-900 rounded-xl p-5 border border-slate-200 dark:border-slate-800 hover:border-emerald-500 dark:hover:border-emerald-500 transition-colors group">
                    <div class="flex items-center gap-4">
                        <div class="h-12 w-12 rounded-xl bg-emerald-100 dark:bg-emerald-500/20 flex items-center justify-center flex-shrink-0 group-hover:bg-emerald-200 dark:group-hover:bg-emerald-500/30 transition-colors">
                            <i class="fa-solid fa-chart-line text-emerald-600 dark:text-emerald-400 text-xl"></i>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Общая аналитика</h3>
                            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Статистика записей и бизнесов</p>
                        </div>
                        <i class="fa-solid fa-arrow-right text-slate-400 group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors"></i>
                    </div>
                </a>
            @endcan

            @can('panel.analytics.subscriptions')
                <a href="{{ route('panel.analytics.subscriptions') }}" 
                   class="bg-white dark:bg-slate-900 rounded-xl p-5 border border-slate-200 dark:border-slate-800 hover:border-amber-500 dark:hover:border-amber-500 transition-colors group">
                    <div class="flex items-center gap-4">
                        <div class="h-12 w-12 rounded-xl bg-amber-100 dark:bg-amber-500/20 flex items-center justify-center flex-shrink-0 group-hover:bg-amber-200 dark:group-hover:bg-amber-500/30 transition-colors">
                            <i class="fa-solid fa-credit-card text-amber-600 dark:text-amber-400 text-xl"></i>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Аналитика подписок</h3>
                            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Статистика подписок и тарифов</p>
                        </div>
                        <i class="fa-solid fa-arrow-right text-slate-400 group-hover:text-amber-600 dark:group-hover:text-amber-400 transition-colors"></i>
                    </div>
                </a>
            @endcan
        </div>

        <!-- Карточки метрик -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Всего бизнесов -->
            <div class="bg-white dark:bg-slate-900 rounded-xl p-5 border border-slate-200 dark:border-slate-800">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wide">Всего бизнесов</p>
                        <p class="text-3xl font-bold text-slate-900 dark:text-white mt-2">{{ number_format($data['total_businesses'], 0, '.', ' ') }}</p>
                        @if($data['business_growth_rate'] > 0)
                            <div class="flex items-center gap-2 mt-2">
                                <span class="text-xs font-semibold text-emerald-600 dark:text-emerald-400">
                                    <i class="fa-solid fa-arrow-up mr-1"></i>{{ abs($data['business_growth_rate']) }}%
                                </span>
                                <span class="text-xs text-slate-500 dark:text-slate-400">за месяц</span>
                            </div>
                        @endif
                    </div>
                    <div class="h-12 w-12 rounded-xl bg-indigo-100 dark:bg-indigo-500/20 flex items-center justify-center flex-shrink-0">
                        <i class="fa-solid fa-building text-indigo-600 dark:text-indigo-400 text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- Активные бизнесы -->
            <div class="bg-white dark:bg-slate-900 rounded-xl p-5 border border-slate-200 dark:border-slate-800">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wide">Активные бизнесы</p>
                        <p class="text-3xl font-bold text-slate-900 dark:text-white mt-2">{{ number_format($data['active_businesses'], 0, '.', ' ') }}</p>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-2">MAU (за месяц)</p>
                    </div>
                    <div class="h-12 w-12 rounded-xl bg-emerald-100 dark:bg-emerald-500/20 flex items-center justify-center flex-shrink-0">
                        <i class="fa-solid fa-chart-line text-emerald-600 dark:text-emerald-400 text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- Всего пользователей -->
            <div class="bg-white dark:bg-slate-900 rounded-xl p-5 border border-slate-200 dark:border-slate-800">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wide">Всего пользователей</p>
                        <p class="text-3xl font-bold text-slate-900 dark:text-white mt-2">{{ number_format($data['total_users'], 0, '.', ' ') }}</p>
                        @if($data['user_growth_rate'] > 0)
                            <div class="flex items-center gap-2 mt-2">
                                <span class="text-xs font-semibold text-emerald-600 dark:text-emerald-400">
                                    <i class="fa-solid fa-arrow-up mr-1"></i>{{ abs($data['user_growth_rate']) }}%
                                </span>
                                <span class="text-xs text-slate-500 dark:text-slate-400">за месяц</span>
                            </div>
                        @endif
                    </div>
                    <div class="h-12 w-12 rounded-xl bg-blue-100 dark:bg-blue-500/20 flex items-center justify-center flex-shrink-0">
                        <i class="fa-solid fa-users text-blue-600 dark:text-blue-400 text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- Активные пользователи -->
            <div class="bg-white dark:bg-slate-900 rounded-xl p-5 border border-slate-200 dark:border-slate-800">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wide">Активные пользователи</p>
                        <p class="text-3xl font-bold text-slate-900 dark:text-white mt-2">{{ number_format($data['active_users'], 0, '.', ' ') }}</p>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-2">MAU (за месяц)</p>
                    </div>
                    <div class="h-12 w-12 rounded-xl bg-purple-100 dark:bg-purple-500/20 flex items-center justify-center flex-shrink-0">
                        <i class="fa-solid fa-users-gear text-purple-600 dark:text-purple-400 text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- Всего записей -->
            <div class="bg-white dark:bg-slate-900 rounded-xl p-5 border border-slate-200 dark:border-slate-800">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wide">Всего записей</p>
                        <p class="text-3xl font-bold text-slate-900 dark:text-white mt-2">{{ number_format($data['total_appointments'], 0, '.', ' ') }}</p>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-2">
                            {{ number_format($data['avg_appointments_per_business'], 1, '.', ' ') }} на бизнес
                        </p>
                    </div>
                    <div class="h-12 w-12 rounded-xl bg-amber-100 dark:bg-amber-500/20 flex items-center justify-center flex-shrink-0">
                        <i class="fa-solid fa-calendar-check text-amber-600 dark:text-amber-400 text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- Всего клиентов -->
            <div class="bg-white dark:bg-slate-900 rounded-xl p-5 border border-slate-200 dark:border-slate-800">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wide">Всего клиентов</p>
                        <p class="text-3xl font-bold text-slate-900 dark:text-white mt-2">{{ number_format($data['total_clients'], 0, '.', ' ') }}</p>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-2">
                            {{ number_format($data['avg_clients_per_business'], 1, '.', ' ') }} на бизнес
                        </p>
                    </div>
                    <div class="h-12 w-12 rounded-xl bg-rose-100 dark:bg-rose-500/20 flex items-center justify-center flex-shrink-0">
                        <i class="fa-solid fa-user-group text-rose-600 dark:text-rose-400 text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- Выручка от подписок -->
            @can('panel.analytics.financial')
                <div class="bg-white dark:bg-slate-900 rounded-xl p-5 border border-slate-200 dark:border-slate-800">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <p class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wide">Выручка (месяц)</p>
                            <p class="text-3xl font-bold text-slate-900 dark:text-white mt-2">{{ number_format($data['revenue_month'], 2, '.', ' ') }} BYN</p>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-2">
                                Всего: {{ number_format($data['revenue_total'], 2, '.', ' ') }} BYN
                            </p>
                        </div>
                        <div class="h-12 w-12 rounded-xl bg-green-100 dark:bg-green-500/20 flex items-center justify-center flex-shrink-0">
                            <i class="fa-solid fa-money-bill-wave text-green-600 dark:text-green-400 text-xl"></i>
                        </div>
                    </div>
                </div>
            @endcan

            <!-- Активные подписки -->
            @can('panel.analytics.subscriptions')
                <div class="bg-white dark:bg-slate-900 rounded-xl p-5 border border-slate-200 dark:border-slate-800">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <p class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wide">Активные подписки</p>
                            <p class="text-3xl font-bold text-slate-900 dark:text-white mt-2">{{ number_format($data['active_subscriptions'], 0, '.', ' ') }}</p>
                        </div>
                        <div class="h-12 w-12 rounded-xl bg-cyan-100 dark:bg-cyan-500/20 flex items-center justify-center flex-shrink-0">
                            <i class="fa-solid fa-credit-card text-cyan-600 dark:text-cyan-400 text-xl"></i>
                        </div>
                    </div>
                </div>
            @endcan
        </div>

        <!-- Графики -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Рост бизнесов и пользователей -->
            <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 p-6">
                <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">Рост бизнесов и пользователей (30 дней)</h3>
                <canvas id="growthChart"></canvas>
            </div>

            <!-- Активность системы -->
            <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 p-6">
                <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">Активность системы (30 дней)</h3>
                <canvas id="activityChart"></canvas>
            </div>
        </div>

        @can('panel.analytics.financial')
            <!-- Выручка от подписок -->
            <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 p-6">
                <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">Выручка от подписок (30 дней)</h3>
                <canvas id="revenueChart"></canvas>
            </div>
        @endcan

        <!-- Топ списки -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Топ-5 бизнесов по записям -->
            <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-800">
                    <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Топ-5 бизнесов по записям</h3>
                </div>
                <div class="p-6">
                    @if($data['top_businesses_by_appointments']->count() > 0)
                        <div class="space-y-4">
                            @foreach($data['top_businesses_by_appointments'] as $business)
                                <div class="flex items-center justify-between">
                                    <div class="flex-1">
                                        <p class="text-sm font-medium text-slate-900 dark:text-white">{{ $business['name'] }}</p>
                                    </div>
                                    <span class="text-sm font-semibold text-slate-600 dark:text-slate-400">{{ number_format($business['count'], 0, '.', ' ') }}</span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-slate-500 dark:text-slate-400">Нет данных</p>
                    @endif
                </div>
            </div>

            <!-- Топ-5 бизнесов по клиентам -->
            <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-800">
                    <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Топ-5 бизнесов по клиентам</h3>
                </div>
                <div class="p-6">
                    @if($data['top_businesses_by_clients']->count() > 0)
                        <div class="space-y-4">
                            @foreach($data['top_businesses_by_clients'] as $business)
                                <div class="flex items-center justify-between">
                                    <div class="flex-1">
                                        <p class="text-sm font-medium text-slate-900 dark:text-white">{{ $business['name'] }}</p>
                                    </div>
                                    <span class="text-sm font-semibold text-slate-600 dark:text-slate-400">{{ number_format($business['count'], 0, '.', ' ') }}</span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-slate-500 dark:text-slate-400">Нет данных</p>
                    @endif
                </div>
            </div>

            <!-- Последние регистрации -->
            <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-800">
                    <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Последние регистрации</h3>
                </div>
                <div class="p-6">
                    @if($data['recent_registrations']->count() > 0)
                        <div class="space-y-4">
                            @foreach($data['recent_registrations'] as $user)
                                <div>
                                    <p class="text-sm font-medium text-slate-900 dark:text-white">{{ $user->name }}</p>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">{{ $user->email }}</p>
                                    <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">{{ $user->created_at->format('d.m.Y H:i') }}</p>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-slate-500 dark:text-slate-400">Нет данных</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const chartData = @json($data['chart_data']);
                const isDarkMode = () => document.documentElement.classList.contains('dark');
                const getThemeColors = () => ({
                    text: isDarkMode() ? '#e2e8f0' : '#1e293b',
                    textSecondary: isDarkMode() ? '#94a3b8' : '#64748b',
                    grid: 'rgba(148, 163, 184, 0.1)'
                });

                // График роста бизнесов и пользователей
                const growthCtx = document.getElementById('growthChart');
                if (growthCtx) {
                    const colors = getThemeColors();
                    new Chart(growthCtx, {
                        type: 'line',
                        data: {
                            labels: chartData.labels,
                            datasets: [
                                {
                                    label: 'Бизнесы',
                                    data: chartData.businesses,
                                    borderColor: 'rgb(99, 102, 241)',
                                    backgroundColor: 'rgba(99, 102, 241, 0.1)',
                                    tension: 0.4,
                                    fill: true,
                                },
                                {
                                    label: 'Пользователи',
                                    data: chartData.users,
                                    borderColor: 'rgb(16, 185, 129)',
                                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                                    tension: 0.4,
                                    fill: true,
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: true,
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

                // График активности системы
                const activityCtx = document.getElementById('activityChart');
                if (activityCtx) {
                    const colors = getThemeColors();
                    new Chart(activityCtx, {
                        type: 'line',
                        data: {
                            labels: chartData.labels,
                            datasets: [
                                {
                                    label: 'Записи',
                                    data: chartData.appointments,
                                    borderColor: 'rgb(59, 130, 246)',
                                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                                    tension: 0.4,
                                    fill: true,
                                },
                                {
                                    label: 'Клиенты',
                                    data: chartData.clients,
                                    borderColor: 'rgb(236, 72, 153)',
                                    backgroundColor: 'rgba(236, 72, 153, 0.1)',
                                    tension: 0.4,
                                    fill: true,
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: true,
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

                @can('panel.analytics.financial')
                // График выручки
                const revenueCtx = document.getElementById('revenueChart');
                if (revenueCtx) {
                    const colors = getThemeColors();
                    new Chart(revenueCtx, {
                        type: 'line',
                        data: {
                            labels: chartData.labels,
                            datasets: [
                                {
                                    label: 'Выручка (BYN)',
                                    data: chartData.revenue,
                                    borderColor: 'rgb(34, 197, 94)',
                                    backgroundColor: 'rgba(34, 197, 94, 0.1)',
                                    tension: 0.4,
                                    fill: true,
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: true,
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
                @endcan
            });
        </script>
    @endpush
@endsection
