@extends('layouts.user')

@section('title', 'Общая аналитика - Cliently')
@section('page-title', 'Общая аналитика')
@section('page-description', 'Статистика записей и показатели эффективности')

@push('breadcrumbs')
    <x-breadcrumbs :items="[
        ['title' => 'Аналитика', 'url' => route('analytics.index')],
        ['title' => 'Общая']
    ]" />
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
@endpush

@section('content')

<div>
    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Общая аналитика</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">Статистика записей и показатели эффективности</p>
            </div>
            <a href="{{ route('analytics.index') }}" 
               class="inline-flex items-center gap-2 px-4 py-2 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 rounded-lg text-sm font-medium hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors">
                <i class="fa-solid fa-arrow-left text-xs"></i>
                <span>Назад</span>
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-gray-200 dark:border-slate-800 p-5 mb-6">
        <form method="GET" action="{{ route('analytics.general') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Дата начала</label>
                <input type="date" name="date_from" value="{{ $filters['date_from'] }}" 
                       class="w-full px-3 py-2 border border-gray-300 dark:border-slate-700 rounded-lg bg-white dark:bg-slate-800 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Дата конца</label>
                <input type="date" name="date_to" value="{{ $filters['date_to'] }}" 
                       class="w-full px-3 py-2 border border-gray-300 dark:border-slate-700 rounded-lg bg-white dark:bg-slate-800 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Услуга</label>
                <select name="service_id" 
                        class="w-full px-3 py-2 border border-gray-300 dark:border-slate-700 rounded-lg bg-white dark:bg-slate-800 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">Все услуги</option>
                    @foreach($business->services as $service)
                        <option value="{{ $service->id }}" {{ $filters['service_id'] == $service->id ? 'selected' : '' }}>
                            {{ $service->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Мастер</label>
                <select name="master_id" 
                        class="w-full px-3 py-2 border border-gray-300 dark:border-slate-700 rounded-lg bg-white dark:bg-slate-800 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">Все мастера</option>
                    @foreach($business->masters as $master)
                        <option value="{{ $master->id }}" {{ $filters['master_id'] == $master->id ? 'selected' : '' }}>
                            {{ $master->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Локация</label>
                <select name="location_id" 
                        class="w-full px-3 py-2 border border-gray-300 dark:border-slate-700 rounded-lg bg-white dark:bg-slate-800 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">Все локации</option>
                    @foreach($business->locations as $location)
                        <option value="{{ $location->id }}" {{ $filters['location_id'] == $location->id ? 'selected' : '' }}>
                            {{ $location->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="md:col-span-5 flex gap-2">
                <button type="submit" 
                        class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition-colors">
                    <i class="fa-solid fa-filter mr-2"></i>
                    Применить фильтры
                </button>
                <a href="{{ route('analytics.general') }}" 
                   class="px-4 py-2 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 rounded-lg text-sm font-medium hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors">
                    <i class="fa-solid fa-xmark mr-2"></i>
                    Сбросить
                </a>
            </div>
        </form>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-gray-200 dark:border-slate-800 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Всего записей</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ number_format($data['total'], 0, ',', ' ') }}</p>
                </div>
                <div class="w-12 h-12 bg-indigo-100 dark:bg-indigo-500/20 rounded-xl flex items-center justify-center">
                    <i class="fa-solid fa-calendar-check text-indigo-600 dark:text-indigo-400 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-gray-200 dark:border-slate-800 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Завершено</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ number_format($data['stats_by_status']['completed'], 0, ',', ' ') }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 dark:bg-green-500/20 rounded-xl flex items-center justify-center">
                    <i class="fa-solid fa-check-circle text-green-600 dark:text-green-400 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-gray-200 dark:border-slate-800 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Конверсия</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $data['conversion_rate'] }}%</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 dark:bg-blue-500/20 rounded-xl flex items-center justify-center">
                    <i class="fa-solid fa-chart-line text-blue-600 dark:text-blue-400 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-gray-200 dark:border-slate-800 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Отменено</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ number_format($data['stats_by_status']['cancelled'], 0, ',', ' ') }}</p>
                    <p class="text-sm text-red-600 dark:text-red-400 mt-1">{{ $data['cancellation_rate'] }}%</p>
                </div>
                <div class="w-12 h-12 bg-red-100 dark:bg-red-500/20 rounded-xl flex items-center justify-center">
                    <i class="fa-solid fa-xmark-circle text-red-600 dark:text-red-400 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Status Distribution Chart -->
    <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-gray-200 dark:border-slate-800 p-5 mb-6">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Распределение по статусам</h2>
        <div class="h-80">
            <canvas id="statusChart"></canvas>
        </div>
    </div>

    <!-- Appointments Chart -->
    <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-gray-200 dark:border-slate-800 p-5 mb-6">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Записи по периодам</h2>
        <div class="h-80">
            <canvas id="appointmentsChart"></canvas>
        </div>
    </div>

    <!-- Stats by Service -->
    <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-gray-200 dark:border-slate-800 p-5 mb-6">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Статистика по услугам</h2>
        @if(count($data['stats_by_service']) > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-slate-800">
                            <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700 dark:text-gray-300">Услуга</th>
                            <th class="text-right py-3 px-4 text-sm font-semibold text-gray-700 dark:text-gray-300">Всего</th>
                            <th class="text-right py-3 px-4 text-sm font-semibold text-gray-700 dark:text-gray-300">Завершено</th>
                            <th class="text-right py-3 px-4 text-sm font-semibold text-gray-700 dark:text-gray-300">Отменено</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data['stats_by_service'] as $item)
                            <tr class="border-b border-gray-100 dark:border-slate-800 hover:bg-gray-50 dark:hover:bg-slate-800/50">
                                <td class="py-3 px-4 text-sm text-gray-900 dark:text-white">{{ $item['service_name'] }}</td>
                                <td class="py-3 px-4 text-sm text-gray-600 dark:text-gray-400 text-right">{{ $item['total'] }}</td>
                                <td class="py-3 px-4 text-sm text-green-600 dark:text-green-400 text-right">{{ $item['completed'] }}</td>
                                <td class="py-3 px-4 text-sm text-red-600 dark:text-red-400 text-right">{{ $item['cancelled'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-sm text-gray-500 dark:text-gray-400 text-center py-8">Нет данных за выбранный период</p>
        @endif
    </div>

    <!-- Stats by Master -->
    @if(count($data['stats_by_master']) > 0)
    <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-gray-200 dark:border-slate-800 p-5">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Статистика по мастерам</h2>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-200 dark:border-slate-800">
                        <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700 dark:text-gray-300">Мастер</th>
                        <th class="text-right py-3 px-4 text-sm font-semibold text-gray-700 dark:text-gray-300">Всего</th>
                        <th class="text-right py-3 px-4 text-sm font-semibold text-gray-700 dark:text-gray-300">Завершено</th>
                        <th class="text-right py-3 px-4 text-sm font-semibold text-gray-700 dark:text-gray-300">Отменено</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data['stats_by_master'] as $item)
                        <tr class="border-b border-gray-100 dark:border-slate-800 hover:bg-gray-50 dark:hover:bg-slate-800/50">
                            <td class="py-3 px-4 text-sm text-gray-900 dark:text-white">{{ $item['master_name'] }}</td>
                            <td class="py-3 px-4 text-sm text-gray-600 dark:text-gray-400 text-right">{{ $item['total'] }}</td>
                            <td class="py-3 px-4 text-sm text-green-600 dark:text-green-400 text-right">{{ $item['completed'] }}</td>
                            <td class="py-3 px-4 text-sm text-red-600 dark:text-red-400 text-right">{{ $item['cancelled'] }}</td>
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
        const statsData = @json($data['stats_by_period']);
        const statusData = @json($data['stats_by_status']);
        
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

        // Status Chart (Pie)
        const statusCtx = document.getElementById('statusChart');
        if (statusCtx) {
            const colors = getThemeColors();
            new Chart(statusCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Ожидают', 'Подтверждено', 'Завершено', 'Отменено'],
                    datasets: [{
                        data: [
                            statusData.pending,
                            statusData.confirmed,
                            statusData.completed,
                            statusData.cancelled
                        ],
                        backgroundColor: [
                            'rgba(251, 191, 36, 0.8)',
                            'rgba(59, 130, 246, 0.8)',
                            'rgba(34, 197, 94, 0.8)',
                            'rgba(239, 68, 68, 0.8)'
                        ],
                        borderColor: [
                            'rgb(251, 191, 36)',
                            'rgb(59, 130, 246)',
                            'rgb(34, 197, 94)',
                            'rgb(239, 68, 68)'
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

        // Appointments Chart (Line)
        const appointmentsCtx = document.getElementById('appointmentsChart');
        if (appointmentsCtx && statsData.length > 0) {
            const colors = getThemeColors();
            new Chart(appointmentsCtx, {
                type: 'line',
                data: {
                    labels: statsData.map(item => item.label),
                    datasets: [
                        {
                            label: 'Всего',
                            data: statsData.map(item => item.total),
                            borderColor: 'rgb(99, 102, 241)',
                            backgroundColor: 'rgba(99, 102, 241, 0.1)',
                            tension: 0.4,
                            fill: true,
                        },
                        {
                            label: 'Завершено',
                            data: statsData.map(item => item.completed),
                            borderColor: 'rgb(34, 197, 94)',
                            backgroundColor: 'rgba(34, 197, 94, 0.1)',
                            tension: 0.4,
                            fill: true,
                        },
                        {
                            label: 'Отменено',
                            data: statsData.map(item => item.cancelled),
                            borderColor: 'rgb(239, 68, 68)',
                            backgroundColor: 'rgba(239, 68, 68, 0.1)',
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
    });
</script>
@endpush

@endsection
