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

<div>
    <!-- Page Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Аналитика</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-1">Финансовая и общая аналитика вашего бизнеса</p>
    </div>

    <!-- Quick Links -->
    <div class="space-y-6">
        <!-- Quick Links -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
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
        </div>
    </div>
</div>

@endsection
