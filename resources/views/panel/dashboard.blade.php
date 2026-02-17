@extends('layouts.panel')

@section('title', 'Панель управления')

@section('content')
    @php
        $canViewBusinesses = auth()->user()->can('panel.businesses.view');
        $canViewUsers = auth()->user()->can('panel.users.view');
        $canViewAppointments = auth()->user()->can('panel.appointments.view');
        $canViewAnalytics = auth()->user()->can('panel.analytics.view');
        $canViewSupport = auth()->user()->can('panel.support.view');
        $canViewSubscriptions = auth()->user()->can('panel.subscriptions.view');
    @endphp

    <div class="max-w-6xl 2xl:max-w-[1400px] mx-auto space-y-8">
        {{-- Заголовок --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Панель управления</h1>
                <p class="text-slate-600 dark:text-slate-400 mt-1 text-sm">Сводка и быстрый доступ к разделам</p>
            </div>
            <form action="{{ route('panel.refresh') }}" method="POST" class="shrink-0">
                @csrf
                <button type="submit" class="inline-flex items-center gap-2 px-3 py-2 text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg text-sm transition-colors">
                    <i class="fa-solid fa-rotate text-xs"></i>
                    Обновить
                </button>
            </form>
        </div>

        {{-- Ключевые показатели: компактная сетка --}}
        @if($canViewBusinesses || $canViewUsers || $canViewAppointments || $canViewSubscriptions)
        <section>
            <h2 class="sr-only">Ключевые показатели</h2>
            <div class="grid grid-cols-2 lg:grid-cols-4 xl:grid-cols-5 gap-4">
                @if($canViewBusinesses && isset($stats['total_businesses']))
                <a href="{{ route('panel.businesses') }}" class="block p-4 bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 hover:border-indigo-300 dark:hover:border-indigo-700 transition-colors group">
                    <div class="flex items-center gap-3">
                        <div class="p-2 rounded-lg bg-indigo-100 dark:bg-indigo-500/20 text-indigo-600 dark:text-indigo-400">
                            <i class="fa-solid fa-building text-lg"></i>
                        </div>
                        <div class="min-w-0">
                            <p class="text-2xl font-bold text-slate-900 dark:text-white tabular-nums">{{ $stats['total_businesses'] }}</p>
                            <p class="text-xs text-slate-500 dark:text-slate-400 truncate">Бизнесов</p>
                        </div>
                    </div>
                    @if(isset($stats['new_businesses_month']))
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-2">+{{ $stats['new_businesses_month'] }} за месяц</p>
                    @endif
                </a>
                @endif

                @if($canViewUsers && isset($stats['total_users']))
                <a href="{{ route('panel.users') }}" class="block p-4 bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 hover:border-blue-300 dark:hover:border-blue-700 transition-colors group">
                    <div class="flex items-center gap-3">
                        <div class="p-2 rounded-lg bg-blue-100 dark:bg-blue-500/20 text-blue-600 dark:text-blue-400">
                            <i class="fa-solid fa-users text-lg"></i>
                        </div>
                        <div class="min-w-0">
                            <p class="text-2xl font-bold text-slate-900 dark:text-white tabular-nums">{{ number_format($stats['total_users'], 0, ',', ' ') }}</p>
                            <p class="text-xs text-slate-500 dark:text-slate-400 truncate">Пользователей</p>
                        </div>
                    </div>
                    @if(isset($stats['new_users_month']))
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-2">+{{ $stats['new_users_month'] }} за месяц</p>
                    @endif
                </a>
                @endif

                @if($canViewAppointments && isset($stats['appointments_today']))
                <a href="{{ route('panel.appointments') }}" class="block p-4 bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 hover:border-emerald-300 dark:hover:border-emerald-700 transition-colors group">
                    <div class="flex items-center gap-3">
                        <div class="p-2 rounded-lg bg-emerald-100 dark:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400">
                            <i class="fa-solid fa-calendar-day text-lg"></i>
                        </div>
                        <div class="min-w-0">
                            <p class="text-2xl font-bold text-slate-900 dark:text-white tabular-nums">{{ $stats['appointments_today'] ?? 0 }}</p>
                            <p class="text-xs text-slate-500 dark:text-slate-400 truncate">Записей сегодня</p>
                        </div>
                    </div>
                    @if(isset($stats['appointments_week']))
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-2">{{ $stats['appointments_week'] }} за неделю</p>
                    @endif
                </a>
                @endif

                @if($canViewSubscriptions && isset($stats['subscriptions_active']))
                <a href="{{ route('panel.subscriptions.index') }}" class="block p-4 bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 hover:border-violet-300 dark:hover:border-violet-700 transition-colors group">
                    <div class="flex items-center gap-3">
                        <div class="p-2 rounded-lg bg-violet-100 dark:bg-violet-500/20 text-violet-600 dark:text-violet-400">
                            <i class="fa-solid fa-credit-card text-lg"></i>
                        </div>
                        <div class="min-w-0">
                            <p class="text-2xl font-bold text-slate-900 dark:text-white tabular-nums">{{ $stats['subscriptions_active'] ?? 0 }}</p>
                            <p class="text-xs text-slate-500 dark:text-slate-400 truncate">Активных подписок</p>
                        </div>
                    </div>
                    @if(isset($stats['subscriptions_trial']) && ($stats['subscriptions_trial'] ?? 0) > 0)
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-2">{{ $stats['subscriptions_trial'] }} пробных</p>
                    @endif
                </a>
                @endif

                @if($canViewSupport && isset($stats['appointments_pending']))
                <div class="p-4 bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800">
                    <div class="flex items-center gap-3">
                        <div class="p-2 rounded-lg bg-amber-100 dark:bg-amber-500/20 text-amber-600 dark:text-amber-400">
                            <i class="fa-solid fa-clock text-lg"></i>
                        </div>
                        <div class="min-w-0">
                            <p class="text-2xl font-bold text-slate-900 dark:text-white tabular-nums">{{ $stats['appointments_pending'] ?? 0 }}</p>
                            <p class="text-xs text-slate-500 dark:text-slate-400 truncate">Ожидают подтверждения</p>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </section>
        @endif

        {{-- Быстрые действия --}}
        <section>
            <h2 class="text-sm font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-3">Быстрые действия</h2>
            <div class="flex flex-wrap gap-3">
                @can('panel.users.view')
                <a href="{{ route('panel.users') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-sm font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                    <i class="fa-solid fa-users text-slate-500 dark:text-slate-400"></i>
                    Пользователи
                </a>
                @endcan
                @can('panel.roles.view')
                <a href="{{ route('panel.roles') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-sm font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                    <i class="fa-solid fa-shield-halved text-slate-500 dark:text-slate-400"></i>
                    Роли
                </a>
                @endcan
                @can('panel.businesses.view')
                <a href="{{ route('panel.businesses') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-sm font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                    <i class="fa-solid fa-building text-slate-500 dark:text-slate-400"></i>
                    Бизнесы
                </a>
                @endcan
                @can('panel.appointments.view')
                <a href="{{ route('panel.appointments') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-sm font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                    <i class="fa-solid fa-calendar-check text-slate-500 dark:text-slate-400"></i>
                    Записи
                </a>
                @endcan
                @can('panel.subscriptions.view')
                <a href="{{ route('panel.subscriptions.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-sm font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                    <i class="fa-solid fa-credit-card text-slate-500 dark:text-slate-400"></i>
                    Подписки
                </a>
                @endcan
                @can('panel.analytics.view')
                <a href="{{ route('panel.analytics') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-sm font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                    <i class="fa-solid fa-chart-bar text-slate-500 dark:text-slate-400"></i>
                    Аналитика
                </a>
                @endcan
                @if(auth()->user()->can('panel.support.view') || auth()->user()->can('panel.tickets.view'))
                <a href="{{ route('panel.tickets') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-sm font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                    <i class="fa-solid fa-headset text-slate-500 dark:text-slate-400"></i>
                    Тикеты
                </a>
                @endif
            </div>
        </section>

        {{-- Активность и списки --}}
        <section class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Активные бизнесы за неделю (для поддержки) --}}
            @if($canViewSupport && isset($activeBusinesses) && $activeBusinesses->isNotEmpty())
            <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 overflow-hidden">
                <div class="px-4 py-3 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between">
                    <h3 class="text-sm font-semibold text-slate-900 dark:text-white">Активность за неделю</h3>
                    @can('panel.businesses.view')
                    <a href="{{ route('panel.businesses') }}" class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline">Все →</a>
                    @endcan
                </div>
                <ul class="divide-y divide-slate-200 dark:divide-slate-800">
                    @foreach($activeBusinesses as $business)
                    <li>
                        <a href="{{ $canViewBusinesses ? route('panel.businesses.show', $business) : '#' }}" class="block px-4 py-3 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                            <span class="font-medium text-slate-900 dark:text-white">{{ $business->name }}</span>
                            <span class="text-xs text-slate-500 dark:text-slate-400 ml-2">{{ $business->appointments_count }} записей</span>
                        </a>
                    </li>
                    @endforeach
                </ul>
            </div>
            @endif

            {{-- Бизнесы без активности (для поддержки) --}}
            @if($canViewSupport && isset($inactiveBusinesses) && $inactiveBusinesses->isNotEmpty())
            <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 overflow-hidden">
                <div class="px-4 py-3 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between bg-amber-50/50 dark:bg-amber-500/5">
                    <h3 class="text-sm font-semibold text-slate-900 dark:text-white">Без активности за месяц</h3>
                    @can('panel.businesses.view')
                    <a href="{{ route('panel.businesses') }}" class="text-xs text-amber-600 dark:text-amber-400 hover:underline">Все →</a>
                    @endcan
                </div>
                <ul class="divide-y divide-slate-200 dark:divide-slate-800">
                    @foreach($inactiveBusinesses as $business)
                    <li>
                        <a href="{{ $canViewBusinesses ? route('panel.businesses.show', $business) : '#' }}" class="block px-4 py-3 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                            <span class="font-medium text-slate-900 dark:text-white">{{ $business->name }}</span>
                            <span class="text-xs text-slate-500 dark:text-slate-400 ml-2">{{ $business->appointments_count }} записей, {{ $business->clients_count }} клиентов</span>
                        </a>
                    </li>
                    @endforeach
                </ul>
            </div>
            @endif
        </section>

        {{-- Последние: бизнесы, пользователи, записи --}}
        <section>
            <h2 class="text-sm font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-3">Последняя активность</h2>
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                @if($canViewBusinesses && $recentBusinesses->isNotEmpty())
                <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 overflow-hidden">
                    <div class="px-4 py-3 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between">
                        <h3 class="text-sm font-semibold text-slate-900 dark:text-white">Бизнесы</h3>
                        <a href="{{ route('panel.businesses') }}" class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline">Все →</a>
                    </div>
                    <ul class="divide-y divide-slate-200 dark:divide-slate-800">
                        @foreach($recentBusinesses as $business)
                        <li>
                            <a href="{{ route('panel.businesses.show', $business) }}" class="block px-4 py-3 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                                <span class="font-medium text-slate-900 dark:text-white block truncate">{{ $business->name }}</span>
                                <span class="text-xs text-slate-500 dark:text-slate-400">{{ $business->appointments_count }} записей · {{ $business->clients_count }} клиентов</span>
                            </a>
                        </li>
                        @endforeach
                    </ul>
                </div>
                @endif

                @if($canViewUsers && $recentUsers->isNotEmpty())
                <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 overflow-hidden">
                    <div class="px-4 py-3 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between">
                        <h3 class="text-sm font-semibold text-slate-900 dark:text-white">Пользователи</h3>
                        <a href="{{ route('panel.users') }}" class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline">Все →</a>
                    </div>
                    <ul class="divide-y divide-slate-200 dark:divide-slate-800">
                        @foreach($recentUsers as $u)
                        <li>
                            <a href="{{ auth()->user()->can('panel.users.update') ? route('panel.users.edit', $u) : '#' }}" class="block px-4 py-3 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                                <span class="font-medium text-slate-900 dark:text-white block truncate">{{ $u->first_name }} {{ $u->last_name }}</span>
                                <span class="text-xs text-slate-500 dark:text-slate-400 truncate block">{{ $u->email }}</span>
                            </a>
                        </li>
                        @endforeach
                    </ul>
                </div>
                @endif

                @if($canViewAppointments && $recentAppointments->isNotEmpty())
                <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 overflow-hidden {{ ($canViewBusinesses && $canViewUsers) ? '' : 'lg:col-span-2' }}">
                    <div class="px-4 py-3 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between">
                        <h3 class="text-sm font-semibold text-slate-900 dark:text-white">Записи</h3>
                        <a href="{{ route('panel.appointments') }}" class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline">Все →</a>
                    </div>
                    <ul class="divide-y divide-slate-200 dark:divide-slate-800">
                        @foreach($recentAppointments->take(5) as $apt)
                        <li class="px-4 py-3 hover:bg-slate-50 dark:hover:bg-slate-800/50">
                            <p class="text-sm font-medium text-slate-900 dark:text-white truncate">{{ $apt->client?->full_name ?? 'Клиент удалён' }}</p>
                            <p class="text-xs text-slate-500 dark:text-slate-400 truncate">{{ $apt->business->name ?? '—' }} · {{ $apt->service->name ?? '—' }} · {{ \Carbon\Carbon::parse($apt->date)->format('d.m.Y') }}</p>
                        </li>
                        @endforeach
                    </ul>
                </div>
                @endif
            </div>
        </section>

        {{-- Ссылка на аналитику --}}
        @if($canViewAnalytics)
        <section>
            <a href="{{ route('panel.analytics') }}" class="flex items-center justify-between p-4 bg-slate-100 dark:bg-slate-800/50 rounded-xl border border-slate-200 dark:border-slate-700 hover:bg-slate-200/50 dark:hover:bg-slate-800 transition-colors group">
                <div class="flex items-center gap-3">
                    <div class="p-2 rounded-lg bg-indigo-100 dark:bg-indigo-500/20 text-indigo-600 dark:text-indigo-400">
                        <i class="fa-solid fa-chart-line"></i>
                    </div>
                    <div>
                        <span class="font-medium text-slate-900 dark:text-white">Детальная аналитика</span>
                        <span class="text-sm text-slate-500 dark:text-slate-400 block">Графики, рост, подписки</span>
                    </div>
                </div>
                <i class="fa-solid fa-arrow-right text-slate-400 group-hover:text-indigo-500 transition-colors"></i>
            </a>
        </section>
        @endif
    </div>
@endsection
