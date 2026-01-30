@extends('layouts.panel')

@section('title', 'Панель администратора')

@section('content')
    <div class="max-w-6xl 2xl:max-w-[1400px] mx-auto space-y-6">
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

        <!-- Ключевые метрики SaaS -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Всего бизнесов -->
            <div class="bg-white dark:bg-slate-900 rounded-xl p-5 border border-slate-200 dark:border-slate-800">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wide">Всего бизнесов</p>
                        <p class="text-3xl font-bold text-slate-900 dark:text-white mt-2">{{ $stats['total_businesses'] }}</p>
                        <div class="flex items-center gap-2 mt-2">
                            @if(isset($stats['business_growth_rate']) && $stats['business_growth_rate'] > 0)
                                <span class="text-xs font-semibold text-emerald-600 dark:text-emerald-400">
                                    <i class="fa-solid fa-arrow-up mr-1"></i>{{ abs($stats['business_growth_rate']) }}%
                                </span>
                            @elseif(isset($stats['business_growth_rate']) && $stats['business_growth_rate'] < 0)
                                <span class="text-xs font-semibold text-rose-600 dark:text-rose-400">
                                    <i class="fa-solid fa-arrow-down mr-1"></i>{{ abs($stats['business_growth_rate']) }}%
                                </span>
                            @endif
                            <span class="text-xs text-slate-500 dark:text-slate-400">
                                +{{ $stats['new_businesses_month'] ?? 0 }} за месяц
                            </span>
                        </div>
                    </div>
                    <div class="h-12 w-12 rounded-xl bg-indigo-100 dark:bg-indigo-500/20 flex items-center justify-center flex-shrink-0">
                        <i class="fa-solid fa-building text-indigo-600 dark:text-indigo-400 text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- Активные бизнесы (MAU) -->
            <div class="bg-white dark:bg-slate-900 rounded-xl p-5 border border-slate-200 dark:border-slate-800">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wide">Активные бизнесы</p>
                        <p class="text-3xl font-bold text-slate-900 dark:text-white mt-2">{{ $stats['active_businesses_month'] ?? 0 }}</p>
                        <div class="flex items-center gap-2 mt-2">
                            <span class="text-xs text-slate-500 dark:text-slate-400">
                                {{ ($stats['total_businesses'] ?? 0) > 0 ? round((($stats['active_businesses_month'] ?? 0) / $stats['total_businesses']) * 100, 1) : 0 }}% от общего
                            </span>
                        </div>
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
                        <p class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wide">Пользователи</p>
                        <p class="text-3xl font-bold text-slate-900 dark:text-white mt-2">{{ $stats['total_users'] }}</p>
                        <div class="flex items-center gap-2 mt-2">
                            @if(isset($stats['user_growth_rate']) && $stats['user_growth_rate'] > 0)
                                <span class="text-xs font-semibold text-emerald-600 dark:text-emerald-400">
                                    <i class="fa-solid fa-arrow-up mr-1"></i>{{ abs($stats['user_growth_rate']) }}%
                                </span>
                            @elseif(isset($stats['user_growth_rate']) && $stats['user_growth_rate'] < 0)
                                <span class="text-xs font-semibold text-rose-600 dark:text-rose-400">
                                    <i class="fa-solid fa-arrow-down mr-1"></i>{{ abs($stats['user_growth_rate']) }}%
                                </span>
                            @endif
                            <span class="text-xs text-slate-500 dark:text-slate-400">
                                +{{ $stats['new_users_month'] ?? 0 }} за месяц
                            </span>
                        </div>
                    </div>
                    <div class="h-12 w-12 rounded-xl bg-blue-100 dark:bg-blue-500/20 flex items-center justify-center flex-shrink-0">
                        <i class="fa-solid fa-users text-blue-600 dark:text-blue-400 text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- Активные пользователи (MAU) -->
            <div class="bg-white dark:bg-slate-900 rounded-xl p-5 border border-slate-200 dark:border-slate-800">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wide">Активные пользователи</p>
                        <p class="text-3xl font-bold text-slate-900 dark:text-white mt-2">{{ $stats['active_users_month'] ?? 0 }}</p>
                        <div class="flex items-center gap-2 mt-2">
                            <span class="text-xs text-slate-500 dark:text-slate-400">
                                {{ ($stats['total_users'] ?? 0) > 0 ? round((($stats['active_users_month'] ?? 0) / $stats['total_users']) * 100, 1) : 0 }}% от общего
                            </span>
                        </div>
                    </div>
                    <div class="h-12 w-12 rounded-xl bg-purple-100 dark:bg-purple-500/20 flex items-center justify-center flex-shrink-0">
                        <i class="fa-solid fa-user-check text-purple-600 dark:text-purple-400 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Метрики вовлеченности -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-white dark:bg-slate-900 rounded-xl p-5 border border-slate-200 dark:border-slate-800">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wide mb-2">Среднее записей на бизнес</p>
                        <p class="text-2xl font-bold text-slate-900 dark:text-white">{{ $stats['avg_appointments_per_business'] ?? 0 }}</p>
                    </div>
                    <div class="h-10 w-10 rounded-lg bg-amber-100 dark:bg-amber-500/20 flex items-center justify-center">
                        <i class="fa-solid fa-calendar-check text-amber-600 dark:text-amber-400"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-slate-900 rounded-xl p-5 border border-slate-200 dark:border-slate-800">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wide mb-2">Среднее клиентов на бизнес</p>
                        <p class="text-2xl font-bold text-slate-900 dark:text-white">{{ $stats['avg_clients_per_business'] ?? 0 }}</p>
                    </div>
                    <div class="h-10 w-10 rounded-lg bg-indigo-100 dark:bg-indigo-500/20 flex items-center justify-center">
                        <i class="fa-solid fa-user-group text-indigo-600 dark:text-indigo-400"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-slate-900 rounded-xl p-5 border border-slate-200 dark:border-slate-800">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wide mb-2">Неактивные бизнесы</p>
                        <p class="text-2xl font-bold text-slate-900 dark:text-white">{{ $stats['inactive_businesses'] ?? 0 }}</p>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                            Без активности за месяц
                        </p>
                    </div>
                    <div class="h-10 w-10 rounded-lg bg-rose-100 dark:bg-rose-500/20 flex items-center justify-center">
                        <i class="fa-solid fa-exclamation-triangle text-rose-600 dark:text-rose-400"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Графики роста и активности -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- График роста бизнесов и пользователей -->
            <div class="bg-white dark:bg-slate-900 rounded-xl p-6 border border-slate-200 dark:border-slate-800">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Рост пользователей (30 дней)</h3>
                </div>
                <div class="relative h-64">
                    <canvas id="growthChart"></canvas>
                </div>
            </div>

            <!-- График активности системы -->
            <div class="bg-white dark:bg-slate-900 rounded-xl p-6 border border-slate-200 dark:border-slate-800">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Активность системы (30 дней)</h3>
                </div>
                <div class="relative h-64">
                    <canvas id="activityChart"></canvas>
                </div>
            </div>
        </div>

        <!-- График новых регистраций -->
        <div class="bg-white dark:bg-slate-900 rounded-xl p-6 border border-slate-200 dark:border-slate-800">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Новые регистрации (30 дней)</h3>
            </div>
            <div class="relative h-64">
                <canvas id="registrationsChart"></canvas>
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

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Данные для графиков
            const chartData = @json($chartData);
            const stats = @json($stats);

            // Функция для определения темы
            const isDarkMode = () => {
                return document.documentElement.classList.contains('dark');
            };

            // Функция для получения цветов в зависимости от темы
            const getThemeColors = () => {
                return {
                    text: isDarkMode() ? '#e2e8f0' : '#1e293b',
                    textSecondary: isDarkMode() ? '#94a3b8' : '#64748b',
                    grid: 'rgba(148, 163, 184, 0.1)'
                };
            };

            // Функция для обновления цветов графиков
            const updateChartColors = (chart) => {
                const colors = getThemeColors();
                chart.options.plugins.legend.labels.color = colors.text;
                if (chart.options.scales) {
                    if (chart.options.scales.x) {
                        chart.options.scales.x.ticks.color = colors.textSecondary;
                        chart.options.scales.x.grid.color = colors.grid;
                    }
                    if (chart.options.scales.y) {
                        chart.options.scales.y.ticks.color = colors.textSecondary;
                        chart.options.scales.y.grid.color = colors.grid;
                    }
                }
                chart.update('none');
            };

            // График роста бизнесов и пользователей
            const growthCtx = document.getElementById('growthChart');
            let growthChart = null;
            if (growthCtx) {
                const colors = getThemeColors();
                growthChart = new Chart(growthCtx, {
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
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: true,
                                position: 'top',
                                labels: {
                                    color: colors.text
                                }
                            },
                            tooltip: {
                                mode: 'index',
                                intersect: false,
                            }
                        },
                        scales: {
                            x: {
                                ticks: {
                                    color: colors.textSecondary
                                },
                                grid: {
                                    color: colors.grid
                                }
                            },
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    color: colors.textSecondary
                                },
                                grid: {
                                    color: colors.grid
                                }
                            }
                        }
                    }
                });
            }

            // График активности системы
            const activityCtx = document.getElementById('activityChart');
            let activityChart = null;
            if (activityCtx) {
                const colors = getThemeColors();
                activityChart = new Chart(activityCtx, {
                    type: 'line',
                    data: {
                        labels: chartData.labels,
                        datasets: [
                            {
                                label: 'Активные бизнесы',
                                data: chartData.active_businesses,
                                borderColor: 'rgb(139, 92, 246)',
                                backgroundColor: 'rgba(139, 92, 246, 0.1)',
                                tension: 0.4,
                                fill: true,
                            },
                            {
                                label: 'Созданные записи',
                                data: chartData.appointments,
                                borderColor: 'rgb(59, 130, 246)',
                                backgroundColor: 'rgba(59, 130, 246, 0.1)',
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
                                display: true,
                                position: 'top',
                                labels: {
                                    color: colors.text
                                }
                            },
                            tooltip: {
                                mode: 'index',
                                intersect: false,
                            }
                        },
                        scales: {
                            x: {
                                ticks: {
                                    color: colors.textSecondary
                                },
                                grid: {
                                    color: colors.grid
                                }
                            },
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    color: colors.textSecondary
                                },
                                grid: {
                                    color: colors.grid
                                }
                            }
                        }
                    }
                });
            }

            // График новых регистраций
            const registrationsCtx = document.getElementById('registrationsChart');
            let registrationsChart = null;
            if (registrationsCtx) {
                const colors = getThemeColors();
                registrationsChart = new Chart(registrationsCtx, {
                    type: 'line',
                    data: {
                        labels: chartData.labels,
                        datasets: [
                            {
                                label: 'Новые бизнесы',
                                data: chartData.businesses,
                                borderColor: 'rgb(99, 102, 241)',
                                backgroundColor: 'rgba(99, 102, 241, 0.1)',
                                tension: 0.4,
                                fill: true,
                            },
                            {
                                label: 'Новые пользователи',
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
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: true,
                                position: 'top',
                                labels: {
                                    color: colors.text
                                }
                            },
                            tooltip: {
                                mode: 'index',
                                intersect: false,
                            }
                        },
                        scales: {
                            x: {
                                ticks: {
                                    color: colors.textSecondary
                                },
                                grid: {
                                    color: colors.grid
                                }
                            },
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    color: colors.textSecondary
                                },
                                grid: {
                                    color: colors.grid
                                }
                            }
                        }
                    }
                });
            }

            // Обновление графиков при изменении темы
            window.addEventListener('themeChanged', function() {
                if (growthChart) updateChartColors(growthChart);
                if (activityChart) updateChartColors(activityChart);
                if (registrationsChart) updateChartColors(registrationsChart);
            });

            // Также слушаем изменения класса dark на documentElement
            const observer = new MutationObserver(function(mutations) {
                mutations.forEach(function(mutation) {
                    if (mutation.attributeName === 'class') {
                        if (growthChart) updateChartColors(growthChart);
                        if (activityChart) updateChartColors(activityChart);
                        if (registrationsChart) updateChartColors(registrationsChart);
                    }
                });
            });
            observer.observe(document.documentElement, {
                attributes: true,
                attributeFilter: ['class']
            });
        });
    </script>
    @endpush
@endsection
