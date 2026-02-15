@extends('layouts.user')

@section('title', 'Аналитика клиентов - Cliently')
@section('page-title', 'Аналитика клиентов')
@section('page-description', 'Статистика по клиентам и их поведению')

@push('breadcrumbs')
    <x-breadcrumbs :items="[
        ['title' => 'Аналитика', 'url' => route('analytics.index')],
        ['title' => 'Клиенты']
    ]" />
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
@endpush

@section('content')

<div class="max-w-6xl 2xl:max-w-[1400px] mx-auto">
    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Аналитика клиентов</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">Статистика по клиентам и их поведению</p>
            </div>
            <div class="flex items-center gap-2">
                @if($business && !empty($hasAdvancedAnalytics))
                <a href="{{ route('analytics.clients.export', $filters) }}" 
                   class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-sm font-medium transition-colors">
                    <i class="fa-solid fa-file-csv"></i>
                    <span>Экспорт CSV</span>
                </a>
                @endif
                <a href="{{ route('analytics.index') }}" 
                   class="inline-flex items-center gap-2 px-4 py-2 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 rounded-lg text-sm font-medium hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors">
                    <i class="fa-solid fa-arrow-left text-xs"></i>
                    <span>Назад</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="mb-6">
        <x-analytics-filters
            route-name="analytics.clients"
            :filters="$filters"
            :filter-presets="$filterPresets ?? []"
            :business="$business"
        />
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-gradient-to-br from-indigo-50 to-indigo-100 dark:from-indigo-900/20 dark:to-indigo-800/10 rounded-xl p-6 border border-indigo-200/50 dark:border-indigo-800/50 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 bg-indigo-500/10 dark:bg-indigo-500/20 rounded-xl">
                    <i class="fa-solid fa-user-plus text-indigo-600 dark:text-indigo-400 text-xl"></i>
                </div>
            </div>
            <p class="text-sm font-medium text-slate-600 dark:text-slate-400 mb-1">Новые клиенты</p>
            <p class="text-3xl font-bold text-slate-900 dark:text-white mb-2">{{ $data['new_clients'] }}</p>
            <p class="text-xs text-slate-500 dark:text-slate-400">
                {{ $data['total_clients'] > 0 ? round(($data['new_clients'] / $data['total_clients']) * 100, 1) : 0 }}% от общего
            </p>
        </div>

        <div class="bg-gradient-to-br from-emerald-50 to-emerald-100 dark:from-emerald-900/20 dark:to-emerald-800/10 rounded-xl p-6 border border-emerald-200/50 dark:border-emerald-800/50 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 bg-emerald-500/10 dark:bg-emerald-500/20 rounded-xl">
                    <i class="fa-solid fa-user-check text-emerald-600 dark:text-emerald-400 text-xl"></i>
                </div>
            </div>
            <p class="text-sm font-medium text-slate-600 dark:text-slate-400 mb-1">Возвращающиеся</p>
            <p class="text-3xl font-bold text-slate-900 dark:text-white mb-2">{{ $data['returning_clients'] }}</p>
            <p class="text-xs text-slate-500 dark:text-slate-400">
                {{ $data['total_clients'] > 0 ? round(($data['returning_clients'] / $data['total_clients']) * 100, 1) : 0 }}% от общего
            </p>
        </div>

        <div class="bg-gradient-to-br from-amber-50 to-amber-100 dark:from-amber-900/20 dark:to-amber-800/10 rounded-xl p-6 border border-amber-200/50 dark:border-amber-800/50 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 bg-amber-500/10 dark:bg-amber-500/20 rounded-xl">
                    <i class="fa-solid fa-money-bill-wave text-amber-600 dark:text-amber-400 text-xl"></i>
                </div>
            </div>
            <p class="text-sm font-medium text-slate-600 dark:text-slate-400 mb-1">Средний LTV</p>
            <p class="text-3xl font-bold text-slate-900 dark:text-white mb-2">{{ number_format($data['average_ltv'], 0, ',', ' ') }} BYN</p>
            <p class="text-xs text-slate-500 dark:text-slate-400">
                Lifetime Value
            </p>
        </div>

        <div class="bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/10 rounded-xl p-6 border border-blue-200/50 dark:border-blue-800/50 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 bg-blue-500/10 dark:bg-blue-500/20 rounded-xl">
                    <i class="fa-solid fa-calendar-check text-blue-600 dark:text-blue-400 text-xl"></i>
                </div>
            </div>
            <p class="text-sm font-medium text-slate-600 dark:text-slate-400 mb-1">Частота визитов</p>
            <p class="text-3xl font-bold text-slate-900 dark:text-white mb-2">{{ number_format($data['visit_frequency'], 1) }}</p>
            <p class="text-xs text-slate-500 dark:text-slate-400">
                Записей на клиента
            </p>
        </div>
    </div>

    <!-- New vs Returning Clients Chart -->
    <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-gray-200 dark:border-slate-800 p-5 mb-6">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Новые vs Возвращающиеся клиенты</h2>
        <div class="h-80">
            <canvas id="clientsDistributionChart"></canvas>
        </div>
    </div>

    @if(empty($hasAdvancedAnalytics) && $business)
    <div class="mb-6 p-4 rounded-xl border border-amber-200 dark:border-amber-800 bg-amber-50 dark:bg-amber-950/30 text-amber-800 dark:text-amber-200">
        <p class="text-sm flex items-center gap-2 flex-wrap">
            <i class="fa-solid fa-chart-bar text-amber-600 dark:text-amber-400"></i>
            <span>Динамика новых клиентов, топ клиентов по выручке и экспорт в CSV доступны в тарифе с <strong>расширенной аналитикой</strong>.</span>
            <a href="{{ route('subscription.index') }}" class="shrink-0 font-medium underline hover:no-underline">Смотреть тарифы</a>
        </p>
    </div>
    @endif

    <!-- Динамика новых клиентов (только расширенная) -->
    @if(!empty($hasAdvancedAnalytics))
    <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-gray-200 dark:border-slate-800 p-5 mb-6 ring-1 ring-indigo-500/10 dark:ring-indigo-400/10">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-1 flex items-center gap-2">
            <i class="fa-solid fa-chart-line text-indigo-500 dark:text-indigo-400"></i>
            Динамика новых клиентов
        </h2>
        <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Как менялось привлечение новых клиентов по дням выбранного периода</p>
        <div class="h-80">
            <canvas id="newClientsChart"></canvas>
        </div>
    </div>
    @endif

    <!-- Топ-3 клиента (карточки, только расширенная) -->
    @if(!empty($hasAdvancedAnalytics) && count($data['top_clients']) > 0)
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        @foreach(array_slice($data['top_clients'], 0, 3) as $index => $client)
        <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-gray-200 dark:border-slate-800 p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-full bg-indigo-100 dark:bg-indigo-500/20 flex items-center justify-center flex-shrink-0">
                <span class="text-lg font-bold text-indigo-600 dark:text-indigo-400">{{ $index + 1 }}</span>
            </div>
            <div class="min-w-0 flex-1">
                <p class="font-semibold text-gray-900 dark:text-white truncate" title="{{ $client['client_name'] }}">{{ $client['client_name'] }}</p>
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ number_format($client['ltv'], 0, ',', ' ') }} BYN · {{ $client['appointments_count'] }} записей</p>
            </div>
        </div>
        @endforeach
    </div>
    @endif

    <!-- Top Clients by Revenue (только расширенная) -->
    @if(!empty($hasAdvancedAnalytics) && count($data['top_clients']) > 0)
    <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-gray-200 dark:border-slate-800 p-5 mb-6">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Топ клиентов по выручке</h2>
        <div class="mb-6 h-64">
            <canvas id="topClientsChart"></canvas>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-200 dark:border-slate-800">
                        <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700 dark:text-gray-300">Клиент</th>
                        <th class="text-right py-3 px-4 text-sm font-semibold text-gray-700 dark:text-gray-300">Записей</th>
                        <th class="text-right py-3 px-4 text-sm font-semibold text-gray-700 dark:text-gray-300">LTV</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data['top_clients'] as $client)
                        <tr class="border-b border-gray-100 dark:border-slate-800 hover:bg-gray-50 dark:hover:bg-slate-800/50">
                            <td class="py-3 px-4 text-sm text-gray-900 dark:text-white">{{ $client['client_name'] }}</td>
                            <td class="py-3 px-4 text-sm text-gray-600 dark:text-gray-400 text-right">{{ $client['appointments_count'] }}</td>
                            <td class="py-3 px-4 text-sm font-semibold text-gray-900 dark:text-white text-right">{{ number_format($client['ltv'], 0, ',', ' ') }} BYN</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const clientsData = @json($data);
        const newClientsByPeriod = @json($data['new_clients_by_period']);
        
        const isDarkMode = () => {
            return document.documentElement.classList.contains('dark');
        };

        const getThemeColors = () => {
            return {
                text: isDarkMode() ? '#e2e8f0' : '#1e293b',
                textSecondary: isDarkMode() ? '#94a3b8' : '#64748b',
                grid: 'rgba(148, 163, 184, 0.1)'
            };
        };

        // Clients Distribution Chart (Doughnut)
        const clientsDistributionCtx = document.getElementById('clientsDistributionChart');
        if (clientsDistributionCtx) {
            const colors = getThemeColors();
            new Chart(clientsDistributionCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Новые клиенты', 'Возвращающиеся'],
                    datasets: [{
                        data: [clientsData.new_clients, clientsData.returning_clients],
                        backgroundColor: [
                            'rgba(99, 102, 241, 0.8)',
                            'rgba(34, 197, 94, 0.8)'
                        ],
                        borderColor: [
                            'rgb(99, 102, 241)',
                            'rgb(34, 197, 94)'
                        ],
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                color: colors.text,
                                padding: 15
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = total > 0 ? ((context.parsed / total) * 100).toFixed(1) : 0;
                                    return context.label + ': ' + context.parsed + ' (' + percentage + '%)';
                                }
                            }
                        }
                    }
                }
            });
        }

        // New Clients Chart (Line)
        const newClientsCtx = document.getElementById('newClientsChart');
        if (newClientsCtx && newClientsByPeriod.length > 0) {
            const colors = getThemeColors();
            new Chart(newClientsCtx, {
                type: 'line',
                data: {
                    labels: newClientsByPeriod.map(item => item.label),
                    datasets: [{
                        label: 'Новые клиенты',
                        data: newClientsByPeriod.map(item => item.count),
                        borderColor: 'rgb(99, 102, 241)',
                        backgroundColor: 'rgba(99, 102, 241, 0.1)',
                        tension: 0.4,
                        fill: true,
                    }]
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
                                color: colors.textSecondary,
                                stepSize: 1
                            },
                            grid: {
                                color: colors.grid
                            }
                        }
                    }
                }
            });
        }

        // Top Clients Chart (Bar)
        const topClientsCtx = document.getElementById('topClientsChart');
        if (topClientsCtx && clientsData.top_clients.length > 0) {
            const colors = getThemeColors();
            new Chart(topClientsCtx, {
                type: 'bar',
                data: {
                    labels: clientsData.top_clients.map(item => item.client_name),
                    datasets: [{
                        label: 'LTV (BYN)',
                        data: clientsData.top_clients.map(item => item.ltv),
                        backgroundColor: 'rgba(34, 197, 94, 0.8)',
                        borderColor: 'rgb(34, 197, 94)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return 'LTV: ' + new Intl.NumberFormat('ru-RU').format(context.parsed.y) + ' BYN';
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            ticks: {
                                color: colors.textSecondary,
                                maxRotation: 45,
                                minRotation: 45
                            },
                            grid: {
                                color: colors.grid
                            }
                        },
                        y: {
                            beginAtZero: true,
                            ticks: {
                                color: colors.textSecondary,
                                callback: function(value) {
                                    return new Intl.NumberFormat('ru-RU').format(value) + ' BYN';
                                }
                            },
                            grid: {
                                color: colors.grid
                            }
                        }
                    }
                }
            });
        }
    });
</script>
@endpush

@endsection
