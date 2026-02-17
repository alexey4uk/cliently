@extends('layouts.panel')

@section('title', 'Подписка #'.$subscription->id)

@php
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
    <div class="max-w-4xl mx-auto">
        <nav class="mb-6" aria-label="Breadcrumb">
            <ol class="flex items-center flex-wrap gap-x-1 gap-y-1 text-xs sm:text-sm text-slate-500 dark:text-slate-400">
                <li><a href="{{ route('panel.index') }}" class="hover:text-slate-700 dark:hover:text-slate-300 transition-colors">Главная</a></li>
                <li><i class="fa-solid fa-chevron-right text-[10px] opacity-60"></i></li>
                <li><a href="{{ route('panel.subscriptions.index') }}" class="hover:text-slate-700 dark:hover:text-slate-300 transition-colors">Подписки</a></li>
                <li><i class="fa-solid fa-chevron-right text-[10px] opacity-60"></i></li>
                <li class="text-slate-900 dark:text-white font-medium">#{{ $subscription->id }}</li>
            </ol>
        </nav>

        <!-- Заголовок -->
        <div class="mb-6 bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
            <div class="p-6 sm:p-8">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div class="flex items-start gap-4">
                        <div class="h-14 w-14 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center shadow-lg flex-shrink-0">
                            <i class="fa-solid fa-credit-card text-white text-xl"></i>
                        </div>
                        <div>
                            <h1 class="text-2xl sm:text-3xl font-bold text-slate-900 dark:text-white">Подписка #{{ $subscription->id }}</h1>
                            <p class="text-sm text-slate-600 dark:text-slate-400 mt-1">
                                {{ $subscription->plan?->name ?? '—' }}
                                · с {{ $subscription->starts_at?->format('d.m.Y') ?? '—' }}
                                @if($subscription->status === 'trial' && $subscription->trial_ends_at)
                                    по {{ $subscription->trial_ends_at->format('d.m.Y') }} (пробный)
                                @elseif($subscription->ends_at)
                                    по {{ $subscription->ends_at->format('d.m.Y') }}
                                @else
                                    · бессрочно
                                @endif
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <a href="{{ route('panel.subscriptions.index') }}"
                            class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 rounded-xl text-sm font-medium hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors">
                            <i class="fa-solid fa-arrow-left text-xs"></i>
                            К списку
                        </a>
                        <span class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-medium {{ $statusColors[$subscription->status] ?? 'bg-slate-100 text-slate-700 dark:bg-slate-500/20 dark:text-slate-400' }}">
                            <span class="w-2 h-2 rounded-full
                                @if($subscription->status === 'active') bg-emerald-500
                                @elseif($subscription->status === 'trial') bg-blue-500
                                @elseif($subscription->status === 'expired') bg-rose-500
                                @else bg-slate-400
                                @endif"></span>
                            {{ $statusLabels[$subscription->status] ?? $subscription->status }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Данные подписки (только просмотр) --}}
        <section class="mb-8">
            <h2 class="text-sm font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-4">Данные подписки</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
                    <div class="px-5 py-3 border-b border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/30">
                        <span class="text-sm font-semibold text-slate-900 dark:text-white flex items-center gap-2">
                            <i class="fa-solid fa-user text-slate-500 dark:text-slate-400"></i>
                            Пользователь
                        </span>
                    </div>
                    <div class="p-5">
                        <p class="text-base font-semibold text-slate-900 dark:text-white">{{ $subscription->user?->name ?? '—' }}</p>
                        <p class="text-sm text-slate-600 dark:text-slate-400 mt-0.5 truncate">{{ $subscription->user?->email ?? '—' }}</p>
                    </div>
                </div>
                <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
                    <div class="px-5 py-3 border-b border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/30">
                        <span class="text-sm font-semibold text-slate-900 dark:text-white flex items-center gap-2">
                            <i class="fa-solid fa-calendar-days text-slate-500 dark:text-slate-400"></i>
                            Тариф и период
                        </span>
                    </div>
                    <div class="p-5 space-y-3">
                        <div>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Тариф</p>
                            <p class="text-sm font-medium text-slate-900 dark:text-white">{{ $subscription->plan?->name ?? '—' }}</p>
                            @if($subscription->plan?->price !== null && (float) $subscription->plan->price > 0)
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                                    {{ number_format((float) $subscription->plan->price, 0, ',', ' ') }} BYN {{ $subscription->plan->interval === 'yearly' ? '/ год' : '/ мес' }}
                                </p>
                            @endif
                        </div>
                        <div>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Период</p>
                            <p class="text-sm text-slate-900 dark:text-white">
                                {{ $subscription->starts_at?->format('d.m.Y') ?? '—' }} —
                                @if($subscription->status === 'trial' && $subscription->trial_ends_at)
                                    {{ $subscription->trial_ends_at->format('d.m.Y') }} <span class="text-slate-500 dark:text-slate-400">(пробный)</span>
                                @elseif($subscription->ends_at)
                                    {{ $subscription->ends_at->format('d.m.Y') }}
                                @else
                                    бессрочно
                                @endif
                            </p>
                        </div>
                        @if($subscription->cancelled_at)
                            <p class="text-xs text-slate-500 dark:text-slate-400">Отменена {{ $subscription->cancelled_at->format('d.m.Y H:i') }}</p>
                        @endif
                    </div>
                </div>
            </div>
        </section>

        @can('panel.subscriptions.manage')
        @php
            $currentEndsAt = $subscription->status === 'trial' && $subscription->trial_ends_at
                ? $subscription->trial_ends_at
                : $subscription->ends_at;
            $defaultEndsAt = $currentEndsAt?->format('Y-m-d') ?? now()->addMonth()->format('Y-m-d');
        @endphp

        {{-- Выдать или изменить подписку: продление = тот же тариф + новая дата --}}
        <section class="mb-8">
            <h2 class="text-sm font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-4">Выдать или изменить подписку</h2>
            <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">Любое действие с подпиской делается через эту форму: <strong>продление</strong> — тот же тариф и новая дата окончания, <strong>смена тарифа</strong> — другой тариф и срок. Текущая подписка будет заменена.</p>
            <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
                <div class="p-6">
                    <form method="POST" action="{{ route('panel.subscriptions.grant') }}">
                        @csrf
                        <input type="hidden" name="user_id" value="{{ $subscription->user_id }}">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-xs font-medium text-slate-600 dark:text-slate-400 mb-2">Тариф</label>
                                <select name="plan_id" required class="w-full px-4 py-2.5 border border-slate-300 dark:border-slate-700 dark:bg-slate-800 rounded-xl text-sm text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
                                    @foreach($plans as $p)
                                        <option value="{{ $p->id }}" {{ $subscription->plan_id === $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-600 dark:text-slate-400 mb-2">Окончание (дата)</label>
                                <input type="date" name="ends_at" value="{{ $defaultEndsAt }}"
                                    class="w-full px-4 py-2.5 border border-slate-300 dark:border-slate-700 dark:bg-slate-800 rounded-xl text-sm text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500"
                                    min="{{ now()->format('Y-m-d') }}">
                            </div>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-xs font-medium text-slate-600 dark:text-slate-400 mb-2">Или кол-во дней</label>
                                <input type="number" name="days" min="1" max="3650" placeholder="Напр. 30"
                                    class="w-full px-4 py-2.5 border border-slate-300 dark:border-slate-700 dark:bg-slate-800 rounded-xl text-sm text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
                            </div>
                            <div class="flex items-end pb-2.5">
                                {{-- Скрытое поле: без него при снятой галочке as_trial не попадает в запрос --}}
                                <input type="hidden" name="as_trial" value="0">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" name="as_trial" value="1" {{ $subscription->status === 'trial' ? 'checked' : '' }} class="rounded border-slate-300 dark:border-slate-600 text-indigo-600 focus:ring-indigo-500">
                                    <span class="text-sm text-slate-700 dark:text-slate-300">Оформить как пробный период</span>
                                </label>
                            </div>
                        </div>
                        <button type="submit" class="inline-flex items-center justify-center gap-2 px-6 py-2.5 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl transition-colors shadow-sm">
                            <i class="fa-solid fa-check text-xs"></i>
                            Применить
                        </button>
                    </form>
                </div>
            </div>
        </section>

        {{-- Отменить в конце периода --}}
        @if(in_array($subscription->status, ['active', 'trial'], true) && !$subscription->cancelled_at)
        <section>
            <h2 class="text-sm font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-4">Отмена</h2>
            <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
                <div class="p-6 flex flex-col sm:flex-row sm:items-center gap-4">
                    <p class="text-sm text-slate-600 dark:text-slate-400 sm:mr-4">Подписка останется активной до конца периода, но не будет продлеваться.</p>
                    <form method="POST" action="{{ route('panel.subscriptions.cancel', $subscription) }}" onsubmit="return confirm('Отменить подписку в конце периода?');">
                        @csrf
                        <button type="submit" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-300 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 rounded-xl transition-colors border border-slate-200 dark:border-slate-700">
                            <i class="fa-solid fa-ban text-xs"></i>
                            Отменить в конце периода
                        </button>
                    </form>
                </div>
            </div>
        </section>
        @endif
        @endcan
    </div>
@endsection
