@extends('layouts.user')

@section('title', 'Аналитика - Cliently')
@section('page-title', 'Аналитика')
@section('page-description', 'Финансовая и общая аналитика вашего бизнеса')

@push('breadcrumbs')
    <x-breadcrumbs :items="[['title' => 'Аналитика']]" />
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
@endpush

@section('content')

<div class="max-w-6xl 2xl:max-w-[1400px] mx-auto">
    <!-- Page Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Аналитика</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-1">Финансовая и общая аналитика вашего бизнеса</p>
    </div>

    <!-- KPI Dashboard -->
    @if(isset($kpiData))
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-gradient-to-br from-indigo-50 to-indigo-100 dark:from-indigo-900/20 dark:to-indigo-800/10 rounded-xl p-6 border border-indigo-200/50 dark:border-indigo-800/50 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 bg-indigo-500/10 dark:bg-indigo-500/20 rounded-xl">
                    <i class="fa-solid fa-users text-indigo-600 dark:text-indigo-400 text-xl"></i>
                </div>
            </div>
            <p class="text-sm font-medium text-slate-600 dark:text-slate-400 mb-1">Retention Rate</p>
            <p class="text-3xl font-bold text-slate-900 dark:text-white mb-2">{{ $kpiData['retention_rate'] }}%</p>
            <p class="text-xs text-slate-500 dark:text-slate-400">
                {{ $kpiData['returning_clients'] }} из {{ $kpiData['total_clients'] }} клиентов возвращаются
            </p>
        </div>

        <div class="bg-gradient-to-br from-emerald-50 to-emerald-100 dark:from-emerald-900/20 dark:to-emerald-800/10 rounded-xl p-6 border border-emerald-200/50 dark:border-emerald-800/50 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 bg-emerald-500/10 dark:bg-emerald-500/20 rounded-xl">
                    <i class="fa-solid fa-money-bill-wave text-emerald-600 dark:text-emerald-400 text-xl"></i>
                </div>
            </div>
            <p class="text-sm font-medium text-slate-600 dark:text-slate-400 mb-1">ARPU</p>
            <p class="text-3xl font-bold text-slate-900 dark:text-white mb-2">{{ number_format($kpiData['arpu'], 0, ',', ' ') }} BYN</p>
            <p class="text-xs text-slate-500 dark:text-slate-400">
                Средний доход на клиента
            </p>
        </div>

        <div class="bg-gradient-to-br from-amber-50 to-amber-100 dark:from-amber-900/20 dark:to-amber-800/10 rounded-xl p-6 border border-amber-200/50 dark:border-amber-800/50 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 bg-amber-500/10 dark:bg-amber-500/20 rounded-xl">
                    <i class="fa-solid fa-chart-line text-amber-600 dark:text-amber-400 text-xl"></i>
                </div>
            </div>
            <p class="text-sm font-medium text-slate-600 dark:text-slate-400 mb-1">Прогноз выручки</p>
            <p class="text-3xl font-bold text-slate-900 dark:text-white mb-2">{{ number_format($kpiData['revenue_forecast'], 0, ',', ' ') }} BYN</p>
            <p class="text-xs text-slate-500 dark:text-slate-400">
                На следующий месяц
            </p>
        </div>

        <div class="bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/10 rounded-xl p-6 border border-blue-200/50 dark:border-blue-800/50 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 bg-blue-500/10 dark:bg-blue-500/20 rounded-xl">
                    <i class="fa-solid fa-calendar-check text-blue-600 dark:text-blue-400 text-xl"></i>
                </div>
            </div>
            <p class="text-sm font-medium text-slate-600 dark:text-slate-400 mb-1">Выручка за 30 дней</p>
            <p class="text-3xl font-bold text-slate-900 dark:text-white mb-2">{{ number_format($kpiData['revenue_last_30_days'], 0, ',', ' ') }} BYN</p>
            <p class="text-xs text-slate-500 dark:text-slate-400">
                Текущий период
            </p>
        </div>
    </div>
    @endif

    <!-- Quick Links -->
    <div class="space-y-6">
        <!-- Quick Links -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <a href="{{ route('analytics.financial') }}" 
               class="group bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-gray-200 dark:border-slate-800 p-6 hover:shadow-md hover:border-indigo-400 dark:hover:border-indigo-600 transition-all">
                <div class="flex items-center justify-between mb-4">
                    <div class="h-12 w-12 rounded-lg bg-green-100 dark:bg-green-500/20 flex items-center justify-center group-hover:bg-green-200 dark:group-hover:bg-green-500/30 transition-colors">
                        <i class="fa-solid fa-money-bill-wave text-green-600 dark:text-green-400 text-xl"></i>
                    </div>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                    Финансовая аналитика
                </h3>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                    Выручка, средний чек, доходы по услугам, мастерам и локациям
                </p>
                <div class="flex items-center gap-2 text-sm text-indigo-600 dark:text-indigo-400 font-medium">
                    <span>Открыть</span>
                    <i class="fa-solid fa-arrow-right text-xs group-hover:translate-x-1 transition-transform"></i>
                </div>
            </a>

            <a href="{{ route('analytics.general') }}" 
               class="group bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-gray-200 dark:border-slate-800 p-6 hover:shadow-md hover:border-indigo-400 dark:hover:border-indigo-600 transition-all">
                <div class="flex items-center justify-between mb-4">
                    <div class="h-12 w-12 rounded-lg bg-blue-100 dark:bg-blue-500/20 flex items-center justify-center group-hover:bg-blue-200 dark:group-hover:bg-blue-500/30 transition-colors">
                        <i class="fa-solid fa-chart-bar text-blue-600 dark:text-blue-400 text-xl"></i>
                    </div>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                    Общая аналитика
                </h3>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                    Статистика записей, конверсия, анализ по услугам и мастерам
                </p>
                <div class="flex items-center gap-2 text-sm text-indigo-600 dark:text-indigo-400 font-medium">
                    <span>Открыть</span>
                    <i class="fa-solid fa-arrow-right text-xs group-hover:translate-x-1 transition-transform"></i>
                </div>
            </a>

            <a href="{{ route('analytics.clients') }}" 
               class="group bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-gray-200 dark:border-slate-800 p-6 hover:shadow-md hover:border-indigo-400 dark:hover:border-indigo-600 transition-all">
                <div class="flex items-center justify-between mb-4">
                    <div class="h-12 w-12 rounded-lg bg-purple-100 dark:bg-purple-500/20 flex items-center justify-center group-hover:bg-purple-200 dark:group-hover:bg-purple-500/30 transition-colors">
                        <i class="fa-solid fa-users text-purple-600 dark:text-purple-400 text-xl"></i>
                    </div>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                    Аналитика клиентов
                </h3>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                    Новые vs возвращающиеся клиенты, LTV, топ клиентов, частота визитов
                </p>
                <div class="flex items-center gap-2 text-sm text-indigo-600 dark:text-indigo-400 font-medium">
                    <span>Открыть</span>
                    <i class="fa-solid fa-arrow-right text-xs group-hover:translate-x-1 transition-transform"></i>
                </div>
            </a>
        </div>
    </div>
</div>

@endsection
