@extends('layouts.panel')

@section('title', 'Подписки')

@php
    use Illuminate\Support\Str;
    $statusLabels = [
        'trial' => 'Пробный',
        'active' => 'Активна',
        'past_due' => 'Просрочена',
        'cancelled' => 'Отменена',
        'expired' => 'Истекла',
    ];
    $statusColors = [
        'trial' => 'bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-400',
        'active' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-400',
        'past_due' => 'bg-amber-100 text-amber-700 dark:bg-amber-500/20 dark:text-amber-400',
        'cancelled' => 'bg-slate-100 text-slate-700 dark:bg-slate-500/20 dark:text-slate-400',
        'expired' => 'bg-rose-100 text-rose-700 dark:bg-rose-500/20 dark:text-rose-400',
    ];
@endphp

@section('content')
    <div class="max-w-[1400px] mx-auto">
        <div class="space-y-6">
            <!-- Заголовок -->
            <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-4 sm:p-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div class="flex items-center gap-3 sm:gap-4">
                        <div class="h-10 w-10 sm:h-12 sm:w-12 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center shadow-sm flex-shrink-0">
                            <i class="fa-solid fa-credit-card text-white text-base sm:text-lg"></i>
                        </div>
                        <div class="min-w-0 flex-1">
                            <h1 class="text-xl sm:text-2xl lg:text-3xl font-bold text-slate-900 dark:text-white">Подписки</h1>
                            <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-400 mt-0.5 sm:mt-1">Управление подписками пользователей</p>
                        </div>
                    </div>
                </div>
                <!-- Статистика в цветных карточках -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-6">
                    <div class="bg-slate-50 dark:bg-slate-800/50 rounded-xl p-4 border border-slate-200 dark:border-slate-700">
                        <p class="text-2xl font-bold text-slate-900 dark:text-white">{{ $stats['total'] }}</p>
                        <p class="text-xs font-medium text-slate-600 dark:text-slate-400 mt-1">Всего подписок</p>
                    </div>
                    <div class="bg-emerald-50 dark:bg-emerald-500/20 rounded-xl p-4 border border-emerald-200 dark:border-emerald-700">
                        <p class="text-2xl font-bold text-emerald-700 dark:text-emerald-400">{{ $stats['active'] }}</p>
                        <p class="text-xs font-medium text-emerald-600 dark:text-emerald-400 mt-1">Активные</p>
                    </div>
                    <div class="bg-blue-50 dark:bg-blue-500/20 rounded-xl p-4 border border-blue-200 dark:border-blue-700">
                        <p class="text-2xl font-bold text-blue-700 dark:text-blue-400">{{ $stats['trial'] }}</p>
                        <p class="text-xs font-medium text-blue-600 dark:text-blue-400 mt-1">Пробные</p>
                    </div>
                    <div class="bg-rose-50 dark:bg-rose-500/20 rounded-xl p-4 border border-rose-200 dark:border-rose-700">
                        <p class="text-2xl font-bold text-rose-700 dark:text-rose-400">{{ $stats['expired'] }}</p>
                        <p class="text-xs font-medium text-rose-600 dark:text-rose-400 mt-1">Истекшие</p>
                    </div>
                </div>
            </div>

            <!-- Поиск и фильтры -->
            <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-4 sm:p-6">
                <form method="GET" action="{{ route('panel.subscriptions.index') }}" x-data="{ filtersOpen: false }">
                    <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-4">
                        <div class="flex-1 w-full sm:min-w-[200px]">
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fa-solid fa-search text-slate-400 text-sm"></i>
                                </div>
                                <input type="text" name="search" value="{{ $search }}"
                                    placeholder="Имя или email пользователя..."
                                    class="block w-full pl-10 pr-3 py-2.5 border border-slate-300 dark:border-slate-700 dark:bg-slate-800 rounded-xl text-sm text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all">
                            </div>
                        </div>
                        <div class="flex items-center gap-2 flex-wrap">
                            <div class="relative">
                                <button type="button" @click="filtersOpen = !filtersOpen"
                                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-50 dark:bg-slate-800 text-slate-700 dark:text-slate-300 rounded-xl font-medium transition-all hover:bg-slate-100 dark:hover:bg-slate-700 border border-slate-200 dark:border-slate-700 text-sm whitespace-nowrap">
                                    <i class="fa-solid fa-sliders-h text-xs"></i>
                                    <span>Фильтры</span>
                                    @if($status || $planId)
                                        <span class="inline-flex items-center justify-center min-w-[20px] h-5 px-1.5 text-xs font-semibold text-white bg-indigo-600 rounded-full">
                                            {{ ($status ? 1 : 0) + ($planId ? 1 : 0) }}
                                        </span>
                                    @endif
                                    <i class="fa-solid fa-chevron-down text-xs transition-transform duration-200" :class="{ 'rotate-180': filtersOpen }"></i>
                                </button>
                                <div x-show="filtersOpen" @click.away="filtersOpen = false"
                                    x-transition:enter="transition ease-out duration-200"
                                    x-transition:enter-start="opacity-0 scale-95"
                                    x-transition:enter-end="opacity-100 scale-100"
                                    class="absolute right-0 mt-2 w-64 bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-xl z-50 p-4 space-y-4"
                                    style="display: none;">
                                    <div class="flex items-center justify-between pb-2 border-b border-slate-200 dark:border-slate-700">
                                        <span class="text-sm font-semibold text-slate-900 dark:text-white">Фильтры</span>
                                        @if($status || $planId)
                                            <a href="{{ route('panel.subscriptions.index', array_merge(request()->except(['status', 'plan_id']))) }}"
                                                class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline">Сбросить</a>
                                        @endif
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-2 uppercase tracking-wide">Статус</label>
                                        <select name="status" class="w-full px-3 py-2.5 rounded-lg border border-slate-300 dark:border-slate-700 dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500">
                                            <option value="">Все</option>
                                            @foreach($statusLabels as $val => $label)
                                                <option value="{{ $val }}" {{ $status === $val ? 'selected' : '' }}>{{ $label }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-2 uppercase tracking-wide">Тариф</label>
                                        <select name="plan_id" class="w-full px-3 py-2.5 rounded-lg border border-slate-300 dark:border-slate-700 dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500">
                                            <option value="">Все</option>
                                            @foreach($plans as $p)
                                                <option value="{{ $p->id }}" {{ $planId == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-medium">
                                        <i class="fa-solid fa-check text-xs"></i> Применить
                                    </button>
                                </div>
                            </div>
                            <button type="submit" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-sm font-medium shadow-sm hover:shadow-md transition-all whitespace-nowrap">
                                <i class="fa-solid fa-search text-xs"></i>
                                <span class="hidden sm:inline">Найти</span>
                            </button>
                            @if($search || $status || $planId)
                                <a href="{{ route('panel.subscriptions.index') }}"
                                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 rounded-xl text-sm font-medium hover:bg-slate-200 dark:hover:bg-slate-700 transition-all whitespace-nowrap">
                                    <i class="fa-solid fa-xmark text-xs"></i> Сбросить
                                </a>
                            @endif
                        </div>
                    </div>
                    <input type="hidden" name="sort" value="{{ $sort }}">
                    <input type="hidden" name="direction" value="{{ $direction }}">
                    <input type="hidden" name="per_page" value="{{ $perPage }}">
                    <!-- Активные фильтры (теги) -->
                    @if($search || $status || $planId)
                        <div class="mt-4 pt-4 border-t border-slate-200 dark:border-slate-700">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="text-xs font-medium text-slate-500 dark:text-slate-400">Активные фильтры:</span>
                                @if($search)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-indigo-50 dark:bg-indigo-500/10 text-indigo-700 dark:text-indigo-300 rounded-lg text-xs font-medium border border-indigo-200 dark:border-indigo-500/20">
                                        Поиск: "{{ Str::limit($search, 30) }}"
                                        <a href="{{ route('panel.subscriptions.index', array_merge(request()->except(['search']))) }}" class="hover:text-indigo-900 dark:hover:text-indigo-200">
                                            <i class="fa-solid fa-xmark text-[10px]"></i>
                                        </a>
                                    </span>
                                @endif
                                @if($status)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-indigo-50 dark:bg-indigo-500/10 text-indigo-700 dark:text-indigo-300 rounded-lg text-xs font-medium border border-indigo-200 dark:border-indigo-500/20">
                                        Статус: {{ $statusLabels[$status] ?? $status }}
                                        <a href="{{ route('panel.subscriptions.index', array_merge(request()->except(['status']))) }}" class="hover:text-indigo-900 dark:hover:text-indigo-200">
                                            <i class="fa-solid fa-xmark text-[10px]"></i>
                                        </a>
                                    </span>
                                @endif
                                @if($planId && $plans->firstWhere('id', $planId))
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-indigo-50 dark:bg-indigo-500/10 text-indigo-700 dark:text-indigo-300 rounded-lg text-xs font-medium border border-indigo-200 dark:border-indigo-500/20">
                                        Тариф: {{ $plans->firstWhere('id', $planId)->name }}
                                        <a href="{{ route('panel.subscriptions.index', array_merge(request()->except(['plan_id']))) }}" class="hover:text-indigo-900 dark:hover:text-indigo-200">
                                            <i class="fa-solid fa-xmark text-[10px]"></i>
                                        </a>
                                    </span>
                                @endif
                            </div>
                        </div>
                    @endif
                </form>
            </div>

            <!-- Таблица -->
            <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-slate-50 dark:bg-slate-800/80 border-b border-slate-200 dark:border-slate-700">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">Пользователь</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">Тариф</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">Статус</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">Период</th>
                                <th class="px-6 py-4 text-right text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">Действия</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                            @forelse($subscriptions as $sub)
                                <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="h-9 w-9 rounded-lg bg-slate-200 dark:bg-slate-700 flex items-center justify-center flex-shrink-0">
                                                <i class="fa-solid fa-user text-slate-500 dark:text-slate-400 text-sm"></i>
                                            </div>
                                            <div>
                                                <p class="text-sm font-semibold text-slate-900 dark:text-white">{{ $sub->user?->name ?? '—' }}</p>
                                                <p class="text-xs text-slate-500 dark:text-slate-400 truncate max-w-[200px]">{{ $sub->user?->email ?? '—' }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-sm font-medium bg-slate-100 dark:bg-slate-800 text-slate-800 dark:text-slate-200 border border-slate-200 dark:border-slate-700">
                                            {{ $sub->plan?->name ?? '—' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium {{ $statusColors[$sub->status] ?? 'bg-slate-100 text-slate-700 dark:bg-slate-500/20 dark:text-slate-400' }}">
                                            <span class="w-1.5 h-1.5 rounded-full
                                                @if($sub->status === 'active') bg-emerald-500
                                                @elseif($sub->status === 'trial') bg-blue-500
                                                @elseif($sub->status === 'expired') bg-rose-500
                                                @else bg-slate-400
                                                @endif"></span>
                                            {{ $statusLabels[$sub->status] ?? $sub->status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-slate-600 dark:text-slate-400">
                                            <div class="flex items-center gap-1.5">
                                                <i class="fa-solid fa-play text-slate-400 text-[10px]"></i>
                                                <span>{{ $sub->starts_at?->format('d.m.Y') ?? '—' }}</span>
                                            </div>
                                            <div class="flex items-center gap-1.5 mt-0.5">
                                                <i class="fa-solid fa-flag-checkered text-slate-400 text-[10px]"></i>
                                                <span>{{ $sub->ends_at ? $sub->ends_at->format('d.m.Y') : 'Бессрочно' }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <a href="{{ route('panel.subscriptions.show', $sub) }}"
                                            class="inline-flex items-center gap-2 px-3 py-2 text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-500/20 rounded-lg transition-colors border border-transparent hover:border-indigo-200 dark:hover:border-indigo-500/30">
                                            <i class="fa-solid fa-gear text-xs"></i>
                                            <span class="hidden sm:inline">Управление</span>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-16 text-center">
                                        <div class="flex flex-col items-center justify-center max-w-sm mx-auto">
                                            <div class="h-16 w-16 rounded-2xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center mb-4">
                                                <i class="fa-solid fa-credit-card text-slate-400 text-2xl"></i>
                                            </div>
                                            <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-2">Подписок не найдено</h3>
                                            <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">Измените параметры поиска или фильтры</p>
                                            <a href="{{ route('panel.subscriptions.index') }}"
                                                class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 rounded-xl text-sm font-medium hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors">
                                                <i class="fa-solid fa-xmark"></i> Сбросить фильтры
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($subscriptions->hasPages())
                    <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/30">
                        {{ $subscriptions->withQueryString()->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
