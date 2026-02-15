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

<div class="max-w-6xl 2xl:max-w-[1400px] mx-auto">
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
    <div class="mb-6">
        <x-analytics-filters
            route-name="analytics.general"
            :filters="$filters"
            :filter-presets="$filterPresets ?? []"
            :business="$business"
        />
    </div>

    @if($business && !$hasAdvancedAnalytics)
    <div class="mb-6 p-4 rounded-xl border border-amber-200 dark:border-amber-800 bg-amber-50 dark:bg-amber-950/30 text-amber-800 dark:text-amber-200">
        <p class="text-sm flex items-center gap-2 flex-wrap">
            <i class="fa-solid fa-chart-bar text-amber-600 dark:text-amber-400"></i>
            <span>Сравнение с предыдущим периодом и блок «Популярные часы и дни недели» доступны на тарифах с <strong>расширенной аналитикой</strong>.</span>
            <a href="{{ route('subscription.index') }}" class="shrink-0 font-medium underline hover:no-underline">Сменить тариф</a>
        </p>
    </div>
    @endif

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        {{-- Всего записей --}}
        <div class="rounded-xl border border-gray-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-5 shadow-sm">
            <div class="flex items-start justify-between gap-3">
                <div class="min-w-0 flex-1">
                    <p class="text-xs font-medium uppercase tracking-wide text-gray-400 dark:text-gray-500">Всего записей</p>
                    <p class="mt-1 text-2xl font-bold tabular-nums text-gray-900 dark:text-white">{{ number_format($data['total'], 0, ',', ' ') }}</p>
                    @if(isset($comparison) && isset($comparison['total_change_percent']))
                        <p class="mt-2 flex items-center gap-1.5 text-xs">
                            @if($comparison['total_change_percent'] > 0)
                                <span class="inline-flex items-center font-medium text-green-600 dark:text-green-400"><i class="fa-solid fa-arrow-up text-[10px]"></i> +{{ abs($comparison['total_change_percent']) }}%</span>
                            @elseif($comparison['total_change_percent'] < 0)
                                <span class="inline-flex items-center font-medium text-red-600 dark:text-red-400"><i class="fa-solid fa-arrow-down text-[10px]"></i> {{ $comparison['total_change_percent'] }}%</span>
                            @else
                                <span class="text-gray-500 dark:text-gray-400">Без изменений</span>
                            @endif
                            <span class="text-gray-400 dark:text-gray-500">к пред. периоду</span>
                        </p>
                    @endif
                </div>
                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-lg bg-indigo-100 dark:bg-indigo-500/20">
                    <i class="fa-solid fa-calendar-check text-indigo-600 dark:text-indigo-400 text-lg"></i>
                </div>
            </div>
        </div>

        {{-- Завершено --}}
        <div class="rounded-xl border border-gray-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-5 shadow-sm">
            <div class="flex items-start justify-between gap-3">
                <div class="min-w-0 flex-1">
                    <p class="text-xs font-medium uppercase tracking-wide text-gray-400 dark:text-gray-500">Завершено</p>
                    <p class="mt-1 text-2xl font-bold tabular-nums text-gray-900 dark:text-white">{{ number_format($data['stats_by_status']['completed'], 0, ',', ' ') }}</p>
                    @if(isset($comparison) && isset($comparison['completed_change_percent']))
                        <p class="mt-2 flex items-center gap-1.5 text-xs">
                            @if($comparison['completed_change_percent'] > 0)
                                <span class="inline-flex items-center font-medium text-green-600 dark:text-green-400"><i class="fa-solid fa-arrow-up text-[10px]"></i> +{{ abs($comparison['completed_change_percent']) }}%</span>
                            @elseif($comparison['completed_change_percent'] < 0)
                                <span class="inline-flex items-center font-medium text-red-600 dark:text-red-400"><i class="fa-solid fa-arrow-down text-[10px]"></i> {{ $comparison['completed_change_percent'] }}%</span>
                            @else
                                <span class="text-gray-500 dark:text-gray-400">Без изменений</span>
                            @endif
                            <span class="text-gray-400 dark:text-gray-500">к пред. периоду</span>
                        </p>
                    @endif
                </div>
                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-lg bg-green-100 dark:bg-green-500/20">
                    <i class="fa-solid fa-check-circle text-green-600 dark:text-green-400 text-lg"></i>
                </div>
            </div>
        </div>

        {{-- Конверсия --}}
        <div class="rounded-xl border border-gray-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-5 shadow-sm">
            <div class="flex items-start justify-between gap-3">
                <div class="min-w-0 flex-1">
                    <p class="text-xs font-medium uppercase tracking-wide text-gray-400 dark:text-gray-500">Конверсия</p>
                    <p class="mt-1 text-2xl font-bold tabular-nums text-gray-900 dark:text-white">{{ $data['conversion_rate'] }}%</p>
                    @if(isset($comparison) && isset($comparison['conversion_change']))
                        <p class="mt-2 flex items-center gap-1.5 text-xs">
                            @if($comparison['conversion_change'] > 0)
                                <span class="inline-flex items-center font-medium text-green-600 dark:text-green-400"><i class="fa-solid fa-arrow-up text-[10px]"></i> +{{ number_format(abs($comparison['conversion_change']), 1) }}%</span>
                            @elseif($comparison['conversion_change'] < 0)
                                <span class="inline-flex items-center font-medium text-red-600 dark:text-red-400"><i class="fa-solid fa-arrow-down text-[10px]"></i> {{ number_format($comparison['conversion_change'], 1) }}%</span>
                            @else
                                <span class="text-gray-500 dark:text-gray-400">Без изменений</span>
                            @endif
                            <span class="text-gray-400 dark:text-gray-500">к пред. периоду</span>
                        </p>
                    @endif
                </div>
                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-lg bg-blue-100 dark:bg-blue-500/20">
                    <i class="fa-solid fa-chart-line text-blue-600 dark:text-blue-400 text-lg"></i>
                </div>
            </div>
        </div>

        {{-- Отменено --}}
        <div class="rounded-xl border border-gray-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-5 shadow-sm">
            <div class="flex items-start justify-between gap-3">
                <div class="min-w-0 flex-1">
                    <p class="text-xs font-medium uppercase tracking-wide text-gray-400 dark:text-gray-500">Отменено</p>
                    <p class="mt-1 text-2xl font-bold tabular-nums text-gray-900 dark:text-white">{{ number_format($data['stats_by_status']['cancelled'], 0, ',', ' ') }}</p>
                    <p class="mt-0.5 text-sm font-medium text-red-600 dark:text-red-400">{{ $data['cancellation_rate'] }}% от записей</p>
                    @if(isset($comparison) && isset($comparison['cancellation_change']))
                        <p class="mt-2 flex items-center gap-1.5 text-xs">
                            @if($comparison['cancellation_change'] < 0)
                                <span class="inline-flex items-center font-medium text-green-600 dark:text-green-400"><i class="fa-solid fa-arrow-down text-[10px]"></i> {{ number_format($comparison['cancellation_change'], 1) }}%</span>
                            @elseif($comparison['cancellation_change'] > 0)
                                <span class="inline-flex items-center font-medium text-red-600 dark:text-red-400"><i class="fa-solid fa-arrow-up text-[10px]"></i> +{{ number_format($comparison['cancellation_change'], 1) }}%</span>
                            @else
                                <span class="text-gray-500 dark:text-gray-400">Без изменений</span>
                            @endif
                            <span class="text-gray-400 dark:text-gray-500">к пред. периоду</span>
                        </p>
                    @endif
                </div>
                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-lg bg-red-100 dark:bg-red-500/20">
                    <i class="fa-solid fa-xmark-circle text-red-600 dark:text-red-400 text-lg"></i>
                </div>
            </div>
        </div>
    </div>

    <div x-data="{ generalTab: 'funnel' }" class="mb-6">
        <div class="flex flex-wrap gap-2 border-b border-gray-200 dark:border-slate-700 mb-4 pb-3">
            <button @click="generalTab = 'funnel'" :class="generalTab === 'funnel' ? 'bg-indigo-100 dark:bg-indigo-500/20 text-indigo-700 dark:text-indigo-300' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-700'" class="px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                Воронка и статусы
            </button>
            @if(!empty($hasAdvancedAnalytics))
            <button @click="generalTab = 'services_masters'" :class="generalTab === 'services_masters' ? 'bg-indigo-100 dark:bg-indigo-500/20 text-indigo-700 dark:text-indigo-300' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-700'" class="px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                По услугам и мастерам
            </button>
            <button @click="generalTab = 'time'" :class="generalTab === 'time' ? 'bg-indigo-100 dark:bg-indigo-500/20 text-indigo-700 dark:text-indigo-300' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-700'" class="px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                По времени
            </button>
            <button @click="generalTab = 'sources'" :class="generalTab === 'sources' ? 'bg-indigo-100 dark:bg-indigo-500/20 text-indigo-700 dark:text-indigo-300' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-700'" class="px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                Источники
            </button>
            @endif
        </div>

        <div x-show="generalTab === 'funnel'" x-cloak>
            <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-gray-200 dark:border-slate-800 p-5 mb-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Распределение по статусам</h2>
                <div class="h-80">
                    <canvas id="statusChart"></canvas>
                </div>
            </div>
            <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-gray-200 dark:border-slate-800 p-5 mb-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Записи по периодам</h2>
                <div class="h-80">
                    <canvas id="appointmentsChart"></canvas>
                </div>
            </div>
        </div>

        @if(!empty($hasAdvancedAnalytics))
        <div x-show="generalTab === 'services_masters'" x-cloak>
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
            @if(count($data['stats_by_master']) > 0)
            <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-gray-200 dark:border-slate-800 p-5 mb-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Эффективность мастеров</h2>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-gray-200 dark:border-slate-800">
                                <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700 dark:text-gray-300">Мастер</th>
                                <th class="text-right py-3 px-4 text-sm font-semibold text-gray-700 dark:text-gray-300">Всего</th>
                                <th class="text-right py-3 px-4 text-sm font-semibold text-gray-700 dark:text-gray-300">Завершено</th>
                                <th class="text-right py-3 px-4 text-sm font-semibold text-gray-700 dark:text-gray-300">Конверсия</th>
                                <th class="text-right py-3 px-4 text-sm font-semibold text-gray-700 dark:text-gray-300">Отменено</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($data['stats_by_master'] as $item)
                                @php
                                    $conversionRate = $item['total'] > 0 ? round(($item['completed'] / $item['total']) * 100, 1) : 0;
                                @endphp
                                <tr class="border-b border-gray-100 dark:border-slate-800 hover:bg-gray-50 dark:hover:bg-slate-800/50">
                                    <td class="py-3 px-4 text-sm text-gray-900 dark:text-white">{{ $item['master_name'] }}</td>
                                    <td class="py-3 px-4 text-sm text-gray-600 dark:text-gray-400 text-right">{{ $item['total'] }}</td>
                                    <td class="py-3 px-4 text-sm text-green-600 dark:text-green-400 text-right">{{ $item['completed'] }}</td>
                                    <td class="py-3 px-4 text-sm font-semibold text-right">
                                        <span class="{{ $conversionRate >= 80 ? 'text-green-600 dark:text-green-400' : ($conversionRate >= 60 ? 'text-yellow-600 dark:text-yellow-400' : 'text-red-600 dark:text-red-400') }}">
                                            {{ $conversionRate }}%
                                        </span>
                                    </td>
                                    <td class="py-3 px-4 text-sm text-red-600 dark:text-red-400 text-right">{{ $item['cancelled'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif
        </div>
        @endif

        @if(!empty($hasAdvancedAnalytics))
        <div x-show="generalTab === 'time'" x-cloak>
            @if(isset($timeAnalytics))
    <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-gray-200 dark:border-slate-800 p-5 mb-6">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Популярные часы и дни недели</h2>
        <div class="overflow-x-auto mb-6">
            <table class="w-full text-sm">
                <thead>
                    <tr>
                        <th class="text-left py-2 px-2 text-xs font-semibold text-gray-700 dark:text-gray-300">День / Час</th>
                        @for($hour = 0; $hour < 24; $hour++)
                            <th class="text-center py-2 px-1 text-xs font-semibold text-gray-700 dark:text-gray-300">{{ str_pad($hour, 2, '0', STR_PAD_LEFT) }}:00</th>
                        @endfor
                    </tr>
                </thead>
                <tbody>
                    @foreach($timeAnalytics['days_of_week'] as $dayIndex => $dayName)
                        <tr>
                            <td class="py-2 px-2 text-xs font-medium text-gray-700 dark:text-gray-300">{{ $dayName }}</td>
                            @for($hour = 0; $hour < 24; $hour++)
                                @php
                                    $count = $timeAnalytics['heatmap'][$dayIndex][$hour] ?? 0;
                                    $maxValue = $timeAnalytics['max_value'];
                                    $intensity = $maxValue > 0 ? round(($count / $maxValue) * 100) : 0;
                                    $bgColor = $intensity > 70 ? 'bg-green-500' : ($intensity > 40 ? 'bg-green-300' : ($intensity > 10 ? 'bg-green-100' : 'bg-gray-50'));
                                    $darkBgColor = $intensity > 70 ? 'dark:bg-green-600' : ($intensity > 40 ? 'dark:bg-green-700' : ($intensity > 10 ? 'dark:bg-green-800' : 'dark:bg-slate-800'));
                                @endphp
                                <td class="text-center py-2 px-1">
                                    <div class="w-full h-8 {{ $bgColor }} {{ $darkBgColor }} rounded flex items-center justify-center" title="{{ $dayName }}, {{ str_pad($hour, 2, '0', STR_PAD_LEFT) }}:00 - {{ $count }} записей">
                                        <span class="text-xs font-medium {{ $count > 0 ? 'text-gray-900 dark:text-white' : 'text-gray-400 dark:text-gray-500' }}">{{ $count > 0 ? $count : '' }}</span>
                                    </div>
                                </td>
                            @endfor
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Charts for Time Analytics -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- By Day of Week -->
        <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-gray-200 dark:border-slate-800 p-5">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Записи по дням недели</h2>
            <div class="h-64">
                <canvas id="byDayOfWeekChart"></canvas>
            </div>
        </div>

        <!-- By Hour -->
        <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-gray-200 dark:border-slate-800 p-5">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Записи по часам</h2>
            <div class="h-64">
                <canvas id="byHourChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Seasonality by Month -->
    @if(count($timeAnalytics['by_month']) > 0)
    <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-gray-200 dark:border-slate-800 p-5">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Сезонность (по месяцам)</h2>
        <div class="h-64">
            <canvas id="byMonthChart"></canvas>
        </div>
    </div>
    @endif
            @else
                <p class="text-sm text-gray-500 dark:text-gray-400 py-6">Аналитика по времени доступна на тарифах с расширенной аналитикой.</p>
            @endif
        </div>
        @endif

        @if(!empty($hasAdvancedAnalytics))
        <div x-show="generalTab === 'sources'" x-cloak>
            @if(isset($data['stats_by_source']) && count($data['stats_by_source']) > 0)
            <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-gray-200 dark:border-slate-800 p-5 mb-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Записи по источникам</h2>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-gray-200 dark:border-slate-800">
                                <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700 dark:text-gray-300">Источник</th>
                                <th class="text-right py-3 px-4 text-sm font-semibold text-gray-700 dark:text-gray-300">Всего</th>
                                <th class="text-right py-3 px-4 text-sm font-semibold text-gray-700 dark:text-gray-300">Завершено</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($data['stats_by_source'] as $row)
                            <tr class="border-b border-gray-100 dark:border-slate-800 hover:bg-gray-50 dark:hover:bg-slate-800/50">
                                <td class="py-3 px-4 text-sm text-gray-900 dark:text-white">{{ $row['label'] }}</td>
                                <td class="py-3 px-4 text-sm text-gray-600 dark:text-gray-400 text-right">{{ $row['count'] }}</td>
                                <td class="py-3 px-4 text-sm text-green-600 dark:text-green-400 text-right">{{ $row['completed'] }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @else
                <p class="text-sm text-gray-500 dark:text-gray-400 py-6">Нет данных по источникам за выбранный период.</p>
            @endif
        </div>
        @endif
    </div>
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

        // Time Analytics Charts
        @if(isset($timeAnalytics))
        // By Day of Week Chart
        const byDayOfWeekCtx = document.getElementById('byDayOfWeekChart');
        if (byDayOfWeekCtx) {
            const colors = getThemeColors();
            const byDayData = @json($timeAnalytics['by_day_of_week']);
            new Chart(byDayOfWeekCtx, {
                type: 'bar',
                data: {
                    labels: byDayData.map(item => item.day),
                    datasets: [{
                        label: 'Записей',
                        data: byDayData.map(item => item.count),
                        backgroundColor: 'rgba(99, 102, 241, 0.8)',
                        borderColor: 'rgb(99, 102, 241)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
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

        // By Hour Chart
        const byHourCtx = document.getElementById('byHourChart');
        if (byHourCtx) {
            const colors = getThemeColors();
            const byHourData = @json($timeAnalytics['by_hour']);
            new Chart(byHourCtx, {
                type: 'line',
                data: {
                    labels: byHourData.map(item => item.hour + ':00'),
                    datasets: [{
                        label: 'Записей',
                        data: byHourData.map(item => item.count),
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
                            display: false
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

        // By Month Chart
        const byMonthCtx = document.getElementById('byMonthChart');
        if (byMonthCtx) {
            const colors = getThemeColors();
            const byMonthData = @json($timeAnalytics['by_month']);
            if (byMonthData.length > 0) {
                new Chart(byMonthCtx, {
                    type: 'bar',
                    data: {
                        labels: byMonthData.map(item => item.label),
                        datasets: [{
                            label: 'Записей',
                            data: byMonthData.map(item => item.count),
                            backgroundColor: 'rgba(139, 92, 246, 0.8)',
                            borderColor: 'rgb(139, 92, 246)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
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
        }
        @endif
    });
</script>
@endpush

@endsection
