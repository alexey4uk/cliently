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

<div class="max-w-6xl 2xl:max-w-[1400px] mx-auto">
    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Финансовая аналитика</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">Выручка и финансовые показатели</p>
            </div>
            <div class="flex items-center gap-2">
                @if($business && !empty($hasAdvancedAnalytics))
                <a href="{{ route('analytics.financial.export', $filters) }}" 
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
            route-name="analytics.financial"
            :filters="$filters"
            :filter-presets="$filterPresets ?? []"
            :business="$business"
        />
    </div>

    @if($business && !$hasAdvancedAnalytics)
    <div class="mb-6 p-4 rounded-xl border border-amber-200 dark:border-amber-800 bg-amber-50 dark:bg-amber-950/30 text-amber-800 dark:text-amber-200">
        <p class="text-sm flex items-center gap-2">
            <i class="fa-solid fa-chart-line text-amber-600 dark:text-amber-400"></i>
            <span>Сравнение с предыдущим периодом (изменение выручки, записей, среднего чека в %) доступно на тарифах с <strong>расширенной аналитикой</strong>.</span>
            <a href="{{ route('subscription.index') }}" class="shrink-0 font-medium underline hover:no-underline">Сменить тариф</a>
        </p>
    </div>
    @endif

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-gray-200 dark:border-slate-800 p-5">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Общая выручка</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ number_format($data['total_revenue'], 0, ',', ' ') }} BYN</p>
                    @if(isset($comparison) && isset($comparison['revenue_change_percent']))
                        <div class="flex items-center mt-2">
                            @if($comparison['revenue_change_percent'] > 0)
                                <span class="text-sm font-semibold text-green-600 dark:text-green-400 flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path>
                                    </svg>
                                    +{{ abs($comparison['revenue_change_percent']) }}%
                                </span>
                            @elseif($comparison['revenue_change_percent'] < 0)
                                <span class="text-sm font-semibold text-red-600 dark:text-red-400 flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                                    </svg>
                                    {{ $comparison['revenue_change_percent'] }}%
                                </span>
                            @else
                                <span class="text-sm text-gray-500 dark:text-gray-400">Без изменений</span>
                            @endif
                            <span class="text-xs text-gray-500 dark:text-gray-400 ml-2">к предыдущему периоду</span>
                        </div>
                    @endif
                </div>
                <div class="w-12 h-12 bg-green-100 dark:bg-green-500/20 rounded-xl flex items-center justify-center">
                    <i class="fa-solid fa-money-bill-wave text-green-600 dark:text-green-400 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-gray-200 dark:border-slate-800 p-5">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Завершенных записей</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ number_format($data['completed_count'], 0, ',', ' ') }}</p>
                    @if(isset($comparison) && isset($comparison['appointments_change_percent']))
                        <div class="flex items-center mt-2">
                            @if($comparison['appointments_change_percent'] > 0)
                                <span class="text-sm font-semibold text-green-600 dark:text-green-400 flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path>
                                    </svg>
                                    +{{ abs($comparison['appointments_change_percent']) }}%
                                </span>
                            @elseif($comparison['appointments_change_percent'] < 0)
                                <span class="text-sm font-semibold text-red-600 dark:text-red-400 flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                                    </svg>
                                    {{ $comparison['appointments_change_percent'] }}%
                                </span>
                            @else
                                <span class="text-sm text-gray-500 dark:text-gray-400">Без изменений</span>
                            @endif
                            <span class="text-xs text-gray-500 dark:text-gray-400 ml-2">к предыдущему периоду</span>
                        </div>
                    @endif
                </div>
                <div class="w-12 h-12 bg-blue-100 dark:bg-blue-500/20 rounded-xl flex items-center justify-center">
                    <i class="fa-solid fa-check-circle text-blue-600 dark:text-blue-400 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-gray-200 dark:border-slate-800 p-5">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Средний чек</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ number_format($data['average_check'], 0, ',', ' ') }} BYN</p>
                    @if(isset($comparison) && isset($comparison['average_check_change_percent']))
                        <div class="flex items-center mt-2">
                            @if($comparison['average_check_change_percent'] > 0)
                                <span class="text-sm font-semibold text-green-600 dark:text-green-400 flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path>
                                    </svg>
                                    +{{ abs($comparison['average_check_change_percent']) }}%
                                </span>
                            @elseif($comparison['average_check_change_percent'] < 0)
                                <span class="text-sm font-semibold text-red-600 dark:text-red-400 flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                                    </svg>
                                    {{ $comparison['average_check_change_percent'] }}%
                                </span>
                            @else
                                <span class="text-sm text-gray-500 dark:text-gray-400">Без изменений</span>
                            @endif
                            <span class="text-xs text-gray-500 dark:text-gray-400 ml-2">к предыдущему периоду</span>
                        </div>
                    @endif
                </div>
                <div class="w-12 h-12 bg-purple-100 dark:bg-purple-500/20 rounded-xl flex items-center justify-center">
                    <i class="fa-solid fa-calculator text-purple-600 dark:text-purple-400 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Итог по периоду (только расширенная) -->
    @if(!empty($hasAdvancedAnalytics))
    <div class="mb-6 p-4 rounded-xl bg-slate-100 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700">
        <p class="text-sm text-slate-700 dark:text-slate-300">
            @if(isset($comparison) && $comparison)
                @if(isset($comparison['revenue_change_percent']))
                    @if($comparison['revenue_change_percent'] > 0)
                        <span class="font-medium text-green-600 dark:text-green-400">Выручка выросла на {{ $comparison['revenue_change_percent'] }}%</span>
                    @elseif($comparison['revenue_change_percent'] < 0)
                        <span class="font-medium text-red-600 dark:text-red-400">Выручка снизилась на {{ abs($comparison['revenue_change_percent']) }}%</span>
                    @else
                        <span class="font-medium text-slate-600 dark:text-slate-400">Выручка без изменений</span>
                    @endif
                    к предыдущему периоду.
                @endif
                Завершённых записей: {{ number_format($data['completed_count'], 0, ',', ' ') }}, средний чек {{ number_format($data['average_check'], 0, ',', ' ') }} BYN.
            @else
                <strong>За период:</strong> выручка {{ number_format($data['total_revenue'], 0, ',', ' ') }} BYN, {{ number_format($data['completed_count'], 0, ',', ' ') }} завершённых записей, средний чек {{ number_format($data['average_check'], 0, ',', ' ') }} BYN.
            @endif
        </p>
    </div>
    @endif

    <!-- Revenue Chart -->
    <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-gray-200 dark:border-slate-800 p-5 mb-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Выручка по периодам</h2>
            <div class="flex gap-2">
                <button onclick="switchChartType('revenueChart', 'line')" class="px-3 py-1 text-xs font-medium rounded-lg bg-indigo-100 dark:bg-indigo-500/20 text-indigo-700 dark:text-indigo-300 hover:bg-indigo-200 dark:hover:bg-indigo-500/30 transition-colors">
                    Линейный
                </button>
                <button onclick="switchChartType('revenueChart', 'bar')" class="px-3 py-1 text-xs font-medium rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors">
                    Столбчатый
                </button>
            </div>
        </div>
        <div class="h-80">
            <canvas id="revenueChart"></canvas>
        </div>
    </div>

    <!-- Revenue and Count Combined Chart (только расширенная) -->
    @if(!empty($hasAdvancedAnalytics))
    <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-gray-200 dark:border-slate-800 p-5 mb-6">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Выручка и количество записей</h2>
        <div class="h-80">
            <canvas id="revenueCountChart"></canvas>
        </div>
    </div>
    @endif

    @if(empty($hasAdvancedAnalytics) && $business)
    <div class="mb-6 p-4 rounded-xl border border-amber-200 dark:border-amber-800 bg-amber-50 dark:bg-amber-950/30 text-amber-800 dark:text-amber-200">
        <p class="text-sm flex items-center gap-2 flex-wrap">
            <i class="fa-solid fa-chart-bar text-amber-600 dark:text-amber-400"></i>
            <span>Разбивка по услугам, мастерам и локациям, выручка по дням недели и экспорт в CSV доступны в тарифе с <strong>расширенной аналитикой</strong>.</span>
            <a href="{{ route('subscription.index') }}" class="shrink-0 font-medium underline hover:no-underline">Смотреть тарифы</a>
        </p>
    </div>
    @endif

    <!-- Выручка по услугам / мастерам / локациям (табы, только расширенная) -->
    @if(!empty($hasAdvancedAnalytics))
    <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-gray-200 dark:border-slate-800 p-5 mb-6" x-data="{ financialTab: 'services' }">
        <div class="flex flex-wrap gap-2 border-b border-gray-200 dark:border-slate-700 mb-4 pb-3">
            <button @click="financialTab = 'services'" :class="financialTab === 'services' ? 'bg-indigo-100 dark:bg-indigo-500/20 text-indigo-700 dark:text-indigo-300' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-700'" class="px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                По услугам
            </button>
            <button @click="financialTab = 'masters'" :class="financialTab === 'masters' ? 'bg-indigo-100 dark:bg-indigo-500/20 text-indigo-700 dark:text-indigo-300' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-700'" class="px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                По мастерам
            </button>
            <button @click="financialTab = 'locations'" :class="financialTab === 'locations' ? 'bg-indigo-100 dark:bg-indigo-500/20 text-indigo-700 dark:text-indigo-300' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-700'" class="px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                По локациям
            </button>
        </div>
        <div x-show="financialTab === 'services'" x-cloak>
            @if(count($data['revenue_by_service']) > 0)
                <div class="mb-6 h-64">
                    <canvas id="revenueByServiceChart"></canvas>
                </div>
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
        <div x-show="financialTab === 'masters'" x-cloak>
            @if(count($data['revenue_by_master']) > 0)
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
            @else
                <p class="text-sm text-gray-500 dark:text-gray-400 text-center py-8">Нет данных за выбранный период</p>
            @endif
        </div>
        <div x-show="financialTab === 'locations'" x-cloak>
            @if(count($data['revenue_by_location']) > 0)
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
            @else
                <p class="text-sm text-gray-500 dark:text-gray-400 text-center py-8">Нет данных за выбранный период</p>
            @endif
        </div>
    </div>

    <!-- Revenue by Day of Week (только расширенная) -->
    @if(!empty($hasAdvancedAnalytics) && isset($data['revenue_by_day_of_week']) && count($data['revenue_by_day_of_week']) > 0)
    <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-gray-200 dark:border-slate-800 p-5 mb-6">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Выручка по дням недели</h2>
        <div class="mb-4 h-64">
            <canvas id="revenueByDayOfWeekChart"></canvas>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-200 dark:border-slate-800">
                        <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700 dark:text-gray-300">День</th>
                        <th class="text-right py-3 px-4 text-sm font-semibold text-gray-700 dark:text-gray-300">Записей</th>
                        <th class="text-right py-3 px-4 text-sm font-semibold text-gray-700 dark:text-gray-300">Выручка</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data['revenue_by_day_of_week'] as $row)
                    <tr class="border-b border-gray-100 dark:border-slate-800 hover:bg-gray-50 dark:hover:bg-slate-800/50">
                        <td class="py-3 px-4 text-sm text-gray-900 dark:text-white">{{ $row['day'] }}</td>
                        <td class="py-3 px-4 text-sm text-gray-600 dark:text-gray-400 text-right">{{ $row['count'] }}</td>
                        <td class="py-3 px-4 text-sm font-semibold text-gray-900 dark:text-white text-right">{{ number_format($row['revenue'], 0, ',', ' ') }} BYN</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
    @endif
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const revenueData = @json($data['revenue_by_period']);
        const revenueByService = @json($data['revenue_by_service']);
        const revenueByDayOfWeek = @json($data['revenue_by_day_of_week'] ?? []);
        
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

        // Revenue Chart with type switching
        let revenueChart = null;
        const ctx = document.getElementById('revenueChart');
        if (ctx && revenueData.length > 0) {
            const colors = getThemeColors();
            revenueChart = new Chart(ctx, {
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

        // Switch chart type function
        window.switchChartType = function(chartId, type) {
            if (chartId === 'revenueChart' && revenueChart) {
                revenueChart.config.type = type;
                revenueChart.update();
                
                // Update button states
                document.querySelectorAll('[onclick*="revenueChart"]').forEach(btn => {
                    btn.className = 'px-3 py-1 text-xs font-medium rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors';
                });
                event.target.className = 'px-3 py-1 text-xs font-medium rounded-lg bg-indigo-100 dark:bg-indigo-500/20 text-indigo-700 dark:text-indigo-300 hover:bg-indigo-200 dark:hover:bg-indigo-500/30 transition-colors';
            }
        };

        // Revenue and Count Combined Chart
        const revenueCountCtx = document.getElementById('revenueCountChart');
        if (revenueCountCtx && revenueData.length > 0) {
            const colors = getThemeColors();
            new Chart(revenueCountCtx, {
                type: 'line',
                data: {
                    labels: revenueData.map(item => item.label),
                    datasets: [
                        {
                            label: 'Выручка (BYN)',
                            data: revenueData.map(item => item.revenue),
                            borderColor: 'rgb(34, 197, 94)',
                            backgroundColor: 'rgba(34, 197, 94, 0.1)',
                            tension: 0.4,
                            fill: true,
                            yAxisID: 'y',
                        },
                        {
                            label: 'Количество записей',
                            data: revenueData.map(item => item.count || 0),
                            borderColor: 'rgb(59, 130, 246)',
                            backgroundColor: 'rgba(59, 130, 246, 0.1)',
                            tension: 0.4,
                            fill: true,
                            yAxisID: 'y1',
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        mode: 'index',
                        intersect: false,
                    },
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
                            type: 'linear',
                            display: true,
                            position: 'left',
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
                        },
                        y1: {
                            type: 'linear',
                            display: true,
                            position: 'right',
                            beginAtZero: true,
                            ticks: {
                                color: colors.textSecondary
                            },
                            grid: {
                                drawOnChartArea: false,
                            },
                        }
                    }
                }
            });
        }

        // Revenue by Service Bar Chart
        const revenueByServiceCtx = document.getElementById('revenueByServiceChart');
        if (revenueByServiceCtx && revenueByService.length > 0) {
            const colors = getThemeColors();
            new Chart(revenueByServiceCtx, {
                type: 'bar',
                data: {
                    labels: revenueByService.map(item => item.service_name),
                    datasets: [{
                        label: 'Выручка (BYN)',
                        data: revenueByService.map(item => item.revenue),
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
                                    return 'Выручка: ' + new Intl.NumberFormat('ru-RU').format(context.parsed.y) + ' BYN';
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

        // Revenue by Day of Week
        const revenueByDayOfWeekCtx = document.getElementById('revenueByDayOfWeekChart');
        if (revenueByDayOfWeekCtx && revenueByDayOfWeek.length > 0) {
            const colors = getThemeColors();
            new Chart(revenueByDayOfWeekCtx, {
                type: 'bar',
                data: {
                    labels: revenueByDayOfWeek.map(item => item.day),
                    datasets: [{
                        label: 'Выручка (BYN)',
                        data: revenueByDayOfWeek.map(item => item.revenue),
                        backgroundColor: 'rgba(99, 102, 241, 0.8)',
                        borderColor: 'rgb(99, 102, 241)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
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
                            beginAtZero: true,
                            ticks: {
                                color: colors.textSecondary,
                                callback: function(value) {
                                    return new Intl.NumberFormat('ru-RU').format(value) + ' BYN';
                                }
                            },
                            grid: { color: colors.grid }
                        }
                    }
                }
            });
        }
    });
</script>
@endpush

@endsection
