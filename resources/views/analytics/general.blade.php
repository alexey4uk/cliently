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
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Всего записей</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ number_format($data['total'], 0, ',', ' ') }}</p>
                    @if(isset($comparison) && isset($comparison['total_change_percent']))
                        <div class="flex items-center mt-2">
                            @if($comparison['total_change_percent'] > 0)
                                <span class="text-sm font-semibold text-green-600 dark:text-green-400 flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path>
                                    </svg>
                                    +{{ abs($comparison['total_change_percent']) }}%
                                </span>
                            @elseif($comparison['total_change_percent'] < 0)
                                <span class="text-sm font-semibold text-red-600 dark:text-red-400 flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                                    </svg>
                                    {{ $comparison['total_change_percent'] }}%
                                </span>
                            @else
                                <span class="text-sm text-gray-500 dark:text-gray-400">Без изменений</span>
                            @endif
                            <span class="text-xs text-gray-500 dark:text-gray-400 ml-2">к предыдущему периоду</span>
                        </div>
                    @endif
                </div>
                <div class="w-12 h-12 bg-indigo-100 dark:bg-indigo-500/20 rounded-xl flex items-center justify-center">
                    <i class="fa-solid fa-calendar-check text-indigo-600 dark:text-indigo-400 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-gray-200 dark:border-slate-800 p-5">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Завершено</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ number_format($data['stats_by_status']['completed'], 0, ',', ' ') }}</p>
                    @if(isset($comparison) && isset($comparison['completed_change_percent']))
                        <div class="flex items-center mt-2">
                            @if($comparison['completed_change_percent'] > 0)
                                <span class="text-sm font-semibold text-green-600 dark:text-green-400 flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path>
                                    </svg>
                                    +{{ abs($comparison['completed_change_percent']) }}%
                                </span>
                            @elseif($comparison['completed_change_percent'] < 0)
                                <span class="text-sm font-semibold text-red-600 dark:text-red-400 flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                                    </svg>
                                    {{ $comparison['completed_change_percent'] }}%
                                </span>
                            @else
                                <span class="text-sm text-gray-500 dark:text-gray-400">Без изменений</span>
                            @endif
                            <span class="text-xs text-gray-500 dark:text-gray-400 ml-2">к предыдущему периоду</span>
                        </div>
                    @endif
                </div>
                <div class="w-12 h-12 bg-green-100 dark:bg-green-500/20 rounded-xl flex items-center justify-center">
                    <i class="fa-solid fa-check-circle text-green-600 dark:text-green-400 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-gray-200 dark:border-slate-800 p-5">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Конверсия</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $data['conversion_rate'] }}%</p>
                    @if(isset($comparison) && isset($comparison['conversion_change']))
                        <div class="flex items-center mt-2">
                            @if($comparison['conversion_change'] > 0)
                                <span class="text-sm font-semibold text-green-600 dark:text-green-400 flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path>
                                    </svg>
                                    +{{ number_format(abs($comparison['conversion_change']), 1) }}%
                                </span>
                            @elseif($comparison['conversion_change'] < 0)
                                <span class="text-sm font-semibold text-red-600 dark:text-red-400 flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                                    </svg>
                                    {{ number_format($comparison['conversion_change'], 1) }}%
                                </span>
                            @else
                                <span class="text-sm text-gray-500 dark:text-gray-400">Без изменений</span>
                            @endif
                            <span class="text-xs text-gray-500 dark:text-gray-400 ml-2">к предыдущему периоду</span>
                        </div>
                    @endif
                </div>
                <div class="w-12 h-12 bg-blue-100 dark:bg-blue-500/20 rounded-xl flex items-center justify-center">
                    <i class="fa-solid fa-chart-line text-blue-600 dark:text-blue-400 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-gray-200 dark:border-slate-800 p-5">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Отменено</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ number_format($data['stats_by_status']['cancelled'], 0, ',', ' ') }}</p>
                    <p class="text-sm text-red-600 dark:text-red-400 mt-1">{{ $data['cancellation_rate'] }}%</p>
                    @if(isset($comparison) && isset($comparison['cancellation_change']))
                        <div class="flex items-center mt-1">
                            @if($comparison['cancellation_change'] < 0)
                                <span class="text-xs font-semibold text-green-600 dark:text-green-400 flex items-center">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path>
                                    </svg>
                                    {{ number_format($comparison['cancellation_change'], 1) }}%
                                </span>
                            @elseif($comparison['cancellation_change'] > 0)
                                <span class="text-xs font-semibold text-red-600 dark:text-red-400 flex items-center">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                                    </svg>
                                    +{{ number_format($comparison['cancellation_change'], 1) }}%
                                </span>
                            @endif
                        </div>
                    @endif
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

    <!-- Stats by Master with Efficiency -->
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

    <!-- Time Analytics -->
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
