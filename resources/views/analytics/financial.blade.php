@extends('layouts.user')

@section('title', 'Финансовая аналитика - Cliently')
@section('page-title', 'Финансовая аналитика')
@section('page-description', 'Выручка и финансовые показатели')

@push('breadcrumbs')
    <x-breadcrumbs :items="[
        ['title' => 'Аналитика', 'url' => route('analytics.index')],
        ['title' => 'Финансовая']
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
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Финансовая аналитика</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">Выручка и финансовые показатели</p>
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
        <form method="GET" action="{{ route('analytics.financial') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4">
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
                <a href="{{ route('analytics.financial') }}" 
                   class="px-4 py-2 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 rounded-lg text-sm font-medium hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors">
                    <i class="fa-solid fa-xmark mr-2"></i>
                    Сбросить
                </a>
            </div>
        </form>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-gray-200 dark:border-slate-800 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Общая выручка</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ number_format($data['total_revenue'], 0, ',', ' ') }} BYN</p>
                </div>
                <div class="w-12 h-12 bg-green-100 dark:bg-green-500/20 rounded-xl flex items-center justify-center">
                    <i class="fa-solid fa-money-bill-wave text-green-600 dark:text-green-400 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-gray-200 dark:border-slate-800 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Завершенных записей</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ number_format($data['completed_count'], 0, ',', ' ') }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 dark:bg-blue-500/20 rounded-xl flex items-center justify-center">
                    <i class="fa-solid fa-check-circle text-blue-600 dark:text-blue-400 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-gray-200 dark:border-slate-800 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Средний чек</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ number_format($data['average_check'], 0, ',', ' ') }} BYN</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 dark:bg-purple-500/20 rounded-xl flex items-center justify-center">
                    <i class="fa-solid fa-calculator text-purple-600 dark:text-purple-400 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Revenue Chart -->
    <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-gray-200 dark:border-slate-800 p-5 mb-6">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Выручка по периодам</h2>
        <div class="h-80">
            <canvas id="revenueChart"></canvas>
        </div>
    </div>

    <!-- Revenue by Service -->
    <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-gray-200 dark:border-slate-800 p-5 mb-6">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Выручка по услугам</h2>
        @if(count($data['revenue_by_service']) > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-slate-800">
                            <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700 dark:text-gray-300">Услуга</th>
                            <th class="text-right py-3 px-4 text-sm font-semibold text-gray-700 dark:text-gray-300">Количество</th>
                            <th class="text-right py-3 px-4 text-sm font-semibold text-gray-700 dark:text-gray-300">Выручка</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data['revenue_by_service'] as $item)
                            <tr class="border-b border-gray-100 dark:border-slate-800 hover:bg-gray-50 dark:hover:bg-slate-800/50">
                                <td class="py-3 px-4 text-sm text-gray-900 dark:text-white">{{ $item['service_name'] }}</td>
                                <td class="py-3 px-4 text-sm text-gray-600 dark:text-gray-400 text-right">{{ $item['count'] }}</td>
                                <td class="py-3 px-4 text-sm font-semibold text-gray-900 dark:text-white text-right">{{ number_format($item['revenue'], 0, ',', ' ') }} BYN</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-sm text-gray-500 dark:text-gray-400 text-center py-8">Нет данных за выбранный период</p>
        @endif
    </div>

    <!-- Revenue by Master -->
    @if(count($data['revenue_by_master']) > 0)
    <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-gray-200 dark:border-slate-800 p-5 mb-6">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Выручка по мастерам</h2>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-200 dark:border-slate-800">
                        <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700 dark:text-gray-300">Мастер</th>
                        <th class="text-right py-3 px-4 text-sm font-semibold text-gray-700 dark:text-gray-300">Количество</th>
                        <th class="text-right py-3 px-4 text-sm font-semibold text-gray-700 dark:text-gray-300">Выручка</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data['revenue_by_master'] as $item)
                        <tr class="border-b border-gray-100 dark:border-slate-800 hover:bg-gray-50 dark:hover:bg-slate-800/50">
                            <td class="py-3 px-4 text-sm text-gray-900 dark:text-white">{{ $item['master_name'] }}</td>
                            <td class="py-3 px-4 text-sm text-gray-600 dark:text-gray-400 text-right">{{ $item['count'] }}</td>
                            <td class="py-3 px-4 text-sm font-semibold text-gray-900 dark:text-white text-right">{{ number_format($item['revenue'], 0, ',', ' ') }} BYN</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- Revenue by Location -->
    @if(count($data['revenue_by_location']) > 0)
    <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-gray-200 dark:border-slate-800 p-5">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Выручка по локациям</h2>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-200 dark:border-slate-800">
                        <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700 dark:text-gray-300">Локация</th>
                        <th class="text-right py-3 px-4 text-sm font-semibold text-gray-700 dark:text-gray-300">Количество</th>
                        <th class="text-right py-3 px-4 text-sm font-semibold text-gray-700 dark:text-gray-300">Выручка</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data['revenue_by_location'] as $item)
                        <tr class="border-b border-gray-100 dark:border-slate-800 hover:bg-gray-50 dark:hover:bg-slate-800/50">
                            <td class="py-3 px-4 text-sm text-gray-900 dark:text-white">{{ $item['location_name'] }}</td>
                            <td class="py-3 px-4 text-sm text-gray-600 dark:text-gray-400 text-right">{{ $item['count'] }}</td>
                            <td class="py-3 px-4 text-sm font-semibold text-gray-900 dark:text-white text-right">{{ number_format($item['revenue'], 0, ',', ' ') }} BYN</td>
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
        const revenueData = @json($data['revenue_by_period']);
        
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

        const ctx = document.getElementById('revenueChart');
        if (ctx && revenueData.length > 0) {
            const colors = getThemeColors();
            new Chart(ctx, {
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
                            display: true,
                            position: 'top',
                            labels: {
                                color: colors.text
                            }
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false,
                            callbacks: {
                                label: function(context) {
                                    return 'Выручка: ' + new Intl.NumberFormat('ru-RU').format(context.parsed.y) + ' BYN';
                                }
                            }
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
