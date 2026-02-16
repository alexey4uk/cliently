@extends('layouts.user')

@section('title', 'Текущая подписка - Cliently')
@section('page-title', 'Текущая подписка')
@section('page-description', 'Тариф, лимиты и платежи')

@push('breadcrumbs')
    <x-breadcrumbs :items="[['title' => 'Текущая подписка']]" />
@endpush

@section('content')

<div class="max-w-4xl mx-auto px-3 sm:px-6 lg:px-8 py-3 sm:py-8 pb-6 sm:pb-8" x-data="{ showCancelModal: false, showRenewModal: false }">
    {{-- Карточка текущего тарифа --}}
    <div class="relative rounded-xl sm:rounded-2xl border border-teal-200/50 dark:border-teal-800/50 bg-gradient-to-br from-teal-50 to-cyan-100 dark:from-teal-900/20 dark:to-cyan-800/10 shadow-sm overflow-hidden mb-4 sm:mb-8">
        <div class="absolute top-0 right-0 w-32 h-32 sm:w-40 sm:h-40 bg-teal-200/30 dark:bg-teal-800/20 rounded-full -mr-16 -mt-16 sm:-mr-20 sm:-mt-20 blur-2xl"></div>
        <div class="relative px-4 sm:px-6 py-4 sm:py-6">
            <div class="flex flex-col gap-3 sm:gap-4 sm:flex-row sm:items-start sm:justify-between">
                <div class="min-w-0 flex-1">
                    <div class="flex flex-wrap items-center gap-2 sm:gap-3 mb-1.5 sm:mb-2">
                        <h1 class="text-lg sm:text-2xl font-bold text-slate-900 dark:text-white break-words">{{ $plan->name }}</h1>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-semibold rounded-full shrink-0
                            {{ $subscription->isCancelled() ? 'text-amber-700 bg-amber-100 dark:bg-amber-500/20 dark:text-amber-300' : '' }}
                            {{ $subscription->status === 'active' && !$subscription->isCancelled() && (!$subscription->ends_at || $subscription->ends_at->isFuture()) ? 'text-emerald-700 bg-emerald-100 dark:bg-emerald-500/20 dark:text-emerald-300' : '' }}
                            {{ $subscription->status === 'trial' ? 'text-sky-700 bg-sky-100 dark:bg-sky-500/20 dark:text-sky-300' : '' }}
                            {{ $subscription->status === 'expired' || ($subscription->ends_at && $subscription->ends_at->isPast() && $subscription->status !== 'trial') ? 'text-red-700 bg-red-100 dark:bg-red-500/20 dark:text-red-300' : '' }}">
                            @if($subscription->isCancelled())
                                <i class="fa-solid fa-pause-circle"></i>
                                Будет отменена
                            @elseif($subscription->status === 'expired' || ($subscription->ends_at && $subscription->ends_at->isPast() && $subscription->status !== 'trial'))
                                <i class="fa-solid fa-clock"></i>
                                Истекла
                            @elseif($subscription->status === 'active')
                                <i class="fa-solid fa-check-circle"></i>
                                Активна
                            @elseif($subscription->status === 'trial')
                                <i class="fa-solid fa-gift"></i>
                                Пробный период
                            @endif
                        </span>
                    </div>
                    @if($plan->description)
                        <p class="text-sm text-slate-600 dark:text-slate-400 mb-2 sm:mb-3 line-clamp-2 sm:line-clamp-none">{{ $plan->description }}</p>
                    @endif
                    <div class="flex flex-wrap gap-x-3 sm:gap-x-4 gap-y-1 text-xs sm:text-sm text-slate-500 dark:text-slate-400">
                        @if($subscription->starts_at)
                            <span>Начало: <strong class="text-slate-700 dark:text-slate-300">{{ $subscription->starts_at->format('d.m.Y') }}</strong></span>
                        @endif
                        @if($subscription->ends_at)
                            <span>До: <strong class="text-slate-700 dark:text-slate-300">{{ $subscription->ends_at->format('d.m.Y') }}</strong></span>
                        @endif
                    </div>
                </div>
                <div class="shrink-0 text-left sm:text-right pt-1 sm:pt-0">
                    @if($plan->price && $plan->price > 0)
                        <div class="text-xl sm:text-3xl font-bold text-slate-900 dark:text-white">{{ number_format($plan->price, 0, ',', ' ') }} BYN</div>
                        <div class="text-xs sm:text-sm text-slate-600 dark:text-slate-400">{{ $plan->interval === 'monthly' ? 'в месяц' : 'в год' }}</div>
                    @else
                        <div class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-white">Бесплатно</div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Уведомления: следующий тариф / отмена / истечение --}}
    @if(!empty($nextPlanName) && $subscription->ends_at)
        <div class="mb-4 sm:mb-6 p-3 sm:p-4 rounded-xl bg-slate-100 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700">
            <p class="text-xs sm:text-sm text-slate-700 dark:text-slate-300 break-words">
                <i class="fa-solid fa-arrow-right text-teal-600 dark:text-teal-400 mr-1.5"></i>
                После {{ $subscription->ends_at->format('d.m.Y') }} будет подключён тариф «{{ $nextPlanName }}».
            </p>
            <a href="{{ route('subscription.index') }}" class="inline-flex items-center gap-1.5 mt-2 text-xs sm:text-sm font-medium text-teal-600 dark:text-teal-400 hover:text-teal-700 dark:hover:text-teal-300 touch-manipulation">
                Вернуться на платный тариф сейчас
                <i class="fa-solid fa-arrow-up-right-from-square text-xs"></i>
            </a>
        </div>
    @endif

    @if($subscription->isCancelled())
        <div class="mb-4 sm:mb-6 p-3 sm:p-4 rounded-xl bg-amber-50 dark:bg-amber-500/10 border border-amber-200 dark:border-amber-500/20">
            <p class="text-sm font-medium text-amber-800 dark:text-amber-200">Подписка отменена</p>
            <p class="text-xs sm:text-sm text-amber-700 dark:text-amber-300 mt-0.5 break-words">
                @if($subscription->ends_at)
                    Активна до {{ $subscription->ends_at->format('d.m.Y') }}. После этой даты доступ к платным функциям будет ограничен.
                @else
                    Доступ к платным функциям будет ограничен после окончания текущего периода.
                @endif
            </p>
        </div>
    @endif

    @if($subscription->ends_at && $subscription->ends_at->isPast() && $subscription->status !== 'trial' && $plan->price && $plan->price > 0)
        <div class="mb-4 sm:mb-6 p-3 sm:p-4 rounded-xl bg-red-50 dark:bg-red-500/10 border border-red-200 dark:border-red-500/20">
            <p class="text-sm font-medium text-red-800 dark:text-red-200">Подписка истекла</p>
            <p class="text-xs sm:text-sm text-red-700 dark:text-red-300 mt-0.5 break-words">
                Истекла {{ $subscription->ends_at->format('d.m.Y') }}. Продлите подписку для восстановления доступа.
            </p>
        </div>
    @endif

    {{-- Действия: на мобиле кнопки во всю ширину, удобный тап --}}
    <div class="flex flex-col sm:flex-row sm:flex-wrap gap-2 sm:gap-3 mb-6 sm:mb-8">
        @if($canManageSubscription && $subscription->plan->price && $subscription->plan->price > 0)
            <form id="subscription-renew-form" action="{{ route('subscription.renew') }}" method="POST" class="w-full sm:w-auto sm:flex-initial">
                @csrf
                <button type="button" @click="showRenewModal = true"
                    class="w-full min-h-[48px] sm:min-h-[44px] inline-flex items-center justify-center gap-2 px-5 py-3 sm:py-2.5 bg-teal-600 hover:bg-teal-700 active:bg-teal-800 text-white rounded-xl font-semibold text-sm transition-colors shadow-sm touch-manipulation">
                    <i class="fa-solid fa-rotate"></i>
                    Продлить подписку
                </button>
            </form>
        @endif
        <a href="{{ route('subscription.index') }}"
            class="w-full sm:w-auto min-h-[48px] sm:min-h-[44px] inline-flex items-center justify-center gap-2 px-5 py-3 sm:py-2.5 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 text-slate-700 dark:text-slate-200 rounded-xl font-semibold text-sm hover:bg-slate-50 dark:hover:bg-slate-700 active:bg-slate-100 dark:active:bg-slate-600 transition-colors touch-manipulation">
            <i class="fa-solid fa-arrow-right"></i>
            Изменить тариф
        </a>
        @if($canManageSubscription && !$subscription->isCancelled() && $subscription->plan->slug !== 'free')
            <form id="subscription-cancel-form" action="{{ route('subscription.cancel') }}" method="POST" class="w-full sm:w-auto sm:flex-initial">
                @csrf
                <button type="button" @click="showCancelModal = true"
                    class="w-full min-h-[48px] sm:min-h-[44px] inline-flex items-center justify-center gap-2 px-5 py-3 sm:py-2.5 bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-xl font-semibold text-sm hover:bg-red-100 hover:text-red-700 dark:hover:bg-red-500/20 dark:hover:text-red-300 active:bg-red-100 dark:active:bg-red-500/20 transition-colors touch-manipulation">
                    <i class="fa-solid fa-times-circle"></i>
                    Отменить подписку
                </button>
            </form>
        @endif
    </div>

    {{-- Использование лимитов --}}
    <div class="rounded-xl sm:rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-sm overflow-hidden mb-6 sm:mb-8">
        <div class="px-4 sm:px-6 py-3 sm:py-4 border-b border-slate-200 dark:border-slate-800">
            <h2 class="text-base sm:text-lg font-bold text-slate-900 dark:text-white">Использование лимитов</h2>
            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-0.5">Текущее использование ресурсов по тарифу</p>
        </div>
        <div class="px-4 sm:px-6 py-4 sm:py-5">
            @if(count($metricsInPlan) > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
                    @foreach($metricsInPlan as $item)
                        @php
                            $metric = $item['metric'];
                            $current = (int) ($item['current'] ?? 0);
                            $limit = $item['limit'];
                            $percentage = $limit > 0 ? min(100, ($current / $limit) * 100) : 0;
                            $isUnlimited = $limit === -1;
                            $isWarning = !$isUnlimited && $percentage >= 80 && $percentage < 100;
                            $isDanger = !$isUnlimited && $percentage >= 100;
                            $icon = $metric->icon ?? 'fa-solid fa-chart-simple';
                        @endphp
                        <div class="p-3 sm:p-4 rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 min-w-0">
                            <div class="flex items-center justify-between gap-2 sm:gap-3 mb-2">
                                <div class="flex items-center gap-2 sm:gap-2.5 min-w-0">
                                    <div class="w-8 h-8 sm:w-9 sm:h-9 shrink-0 rounded-lg bg-teal-100 dark:bg-teal-500/20 flex items-center justify-center">
                                        <i class="{{ $icon }} text-teal-600 dark:text-teal-400 text-xs sm:text-sm"></i>
                                    </div>
                                    <span class="text-xs sm:text-sm font-semibold text-slate-900 dark:text-white truncate">{{ $metric->label }}</span>
                                </div>
                                <div class="shrink-0 text-right">
                                    @if($isUnlimited)
                                        <span class="text-sm sm:text-base font-bold text-slate-900 dark:text-white">{{ number_format($current, 0, ',', ' ') }}</span>
                                        <span class="text-xs text-slate-500 dark:text-slate-400 block">Безлимит</span>
                                    @else
                                        <span class="text-sm sm:text-base font-bold text-slate-900 dark:text-white">{{ number_format($current, 0, ',', ' ') }}</span>
                                        <span class="text-xs text-slate-500 dark:text-slate-400">из {{ number_format($limit, 0, ',', ' ') }}</span>
                                    @endif
                                </div>
                            </div>
                            @if(!$isUnlimited)
                                <div class="w-full bg-slate-200 dark:bg-slate-700 rounded-full h-1.5 sm:h-2 mt-1.5 sm:mt-2">
                                    <div class="h-1.5 sm:h-2 rounded-full transition-all duration-300
                                        {{ $isDanger ? 'bg-slate-500 dark:bg-slate-400' : ($isWarning ? 'bg-teal-500' : 'bg-teal-500') }}"
                                        style="width: {{ $percentage }}%"></div>
                                </div>
                                <div class="flex items-center justify-between gap-1 sm:gap-2 mt-1 sm:mt-1.5 flex-wrap">
                                    <span class="text-xs text-slate-500 dark:text-slate-400">Использовано {{ round($percentage) }}%</span>
                                    @if($isWarning)
                                        <span class="text-xs text-slate-600 dark:text-slate-400 flex items-center gap-1 shrink-0">
                                            <i class="fa-solid fa-info-circle"></i>
                                            Почти весь лимит использован
                                        </span>
                                    @elseif($isDanger)
                                        <span class="text-xs text-slate-600 dark:text-slate-400 flex items-center gap-1 shrink-0">
                                            <i class="fa-solid fa-minus-circle"></i>
                                            Лимит по тарифу использован
                                        </span>
                                    @endif
                                </div>
                            @else
                                <span class="inline-flex items-center mt-2 px-2 py-0.5 rounded-full text-xs font-semibold bg-teal-100 text-teal-700 dark:bg-teal-500/20 dark:text-teal-300">Безлимит</span>
                            @endif
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-sm text-slate-500 dark:text-slate-400">В этом тарифе нет лимитируемых метрик.</p>
            @endif
        </div>
    </div>

    {{-- История платежей --}}
    @if($subscription && $subscription->invoices && $subscription->invoices->count() > 0)
        <div class="rounded-xl sm:rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-sm overflow-hidden">
            <div class="px-4 sm:px-6 py-3 sm:py-4 border-b border-slate-200 dark:border-slate-800">
                <h2 class="text-base sm:text-lg font-bold text-slate-900 dark:text-white">История платежей</h2>
                <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-0.5">Платежи по подписке</p>
            </div>
            <div class="px-4 sm:px-6 py-4 sm:py-5">
                <ul class="space-y-2 sm:space-y-3">
                    @foreach($subscription->invoices->sortByDesc('created_at') as $invoice)
                        @php
                            $statusColors = [
                                'pending' => 'text-amber-600 dark:text-amber-400',
                                'paid' => 'text-emerald-600 dark:text-emerald-400',
                                'failed' => 'text-red-600 dark:text-red-400',
                                'cancelled' => 'text-slate-500 dark:text-slate-400',
                                'refunded' => 'text-sky-600 dark:text-sky-400',
                            ];
                            $statusLabels = [
                                'pending' => 'Ожидает',
                                'paid' => 'Оплачено',
                                'failed' => 'Ошибка',
                                'cancelled' => 'Отменено',
                                'refunded' => 'Возврат',
                            ];
                        @endphp
                        <li class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 p-3 sm:p-4 rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 min-w-0">
                            <div class="flex items-center gap-2 sm:gap-3 min-w-0">
                                <div class="w-9 h-9 sm:w-10 sm:h-10 shrink-0 rounded-lg bg-teal-100 dark:bg-teal-500/20 flex items-center justify-center">
                                    <i class="fa-solid fa-receipt text-teal-600 dark:text-teal-400 text-sm"></i>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-xs sm:text-sm font-semibold text-slate-900 dark:text-white truncate">Инвойс #{{ $invoice->id }}</p>
                                    <p class="text-xs text-slate-500 dark:text-slate-400">{{ $invoice->created_at ? $invoice->created_at->format('d.m.Y H:i') : '—' }}</p>
                                </div>
                            </div>
                            <div class="flex items-center justify-between sm:block sm:text-right gap-2">
                                <p class="text-sm sm:text-base font-bold text-slate-900 dark:text-white">{{ number_format($invoice->amount, 2) }} {{ $invoice->currency }}</p>
                                <p class="text-xs font-medium {{ $statusColors[$invoice->status] ?? 'text-slate-500' }}">{{ $statusLabels[$invoice->status] ?? $invoice->status }}</p>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    {{-- Модалка: отмена подписки (адаптив + safe area) --}}
    @if($canManageSubscription && !$subscription->isCancelled() && $subscription->plan->slug !== 'free')
        <div x-show="showCancelModal" x-cloak
             x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-3 sm:p-4 pb-[env(safe-area-inset-bottom)] sm:pb-4 bg-black/50" @keydown.escape.window="showCancelModal = false"
             role="dialog" aria-modal="true" aria-labelledby="cancel-modal-title">
            <div x-show="showCancelModal" @click.stop
                 x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                 x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0 scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="bg-white dark:bg-slate-900 rounded-2xl sm:rounded-2xl shadow-xl border border-slate-200 dark:border-slate-800 max-w-md w-full max-h-[90vh] overflow-y-auto">
                <div class="px-4 sm:px-6 py-4 sm:py-5 border-b border-slate-200 dark:border-slate-800">
                    <div class="flex items-center gap-3">
                        <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-full bg-amber-100 dark:bg-amber-500/20 flex items-center justify-center shrink-0">
                            <i class="fa-solid fa-exclamation-triangle text-amber-600 dark:text-amber-400 text-lg sm:text-xl"></i>
                        </div>
                        <h2 id="cancel-modal-title" class="text-base sm:text-lg font-bold text-slate-900 dark:text-white">Отменить подписку?</h2>
                    </div>
                </div>
                <div class="px-4 sm:px-6 py-4 sm:py-5">
                    <p class="text-sm text-slate-600 dark:text-slate-400">
                        Подписка останется активной до {{ $subscription->ends_at ? $subscription->ends_at->format('d.m.Y') : 'даты окончания' }}. После этого доступ к платным функциям будет ограничен.
                    </p>
                </div>
                <div class="px-4 sm:px-6 py-4 bg-slate-50 dark:bg-slate-800/50 flex flex-col-reverse sm:flex-row gap-2 sm:gap-3 sm:justify-end pb-[env(safe-area-inset-bottom)] sm:pb-4">
                    <button type="button" @click="showCancelModal = false"
                        class="w-full sm:w-auto min-h-[48px] sm:min-h-0 px-4 py-3 sm:py-2.5 text-sm font-semibold text-slate-700 dark:text-slate-300 bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-xl sm:rounded-lg hover:bg-slate-50 dark:hover:bg-slate-600 transition-colors touch-manipulation">
                        Нет, оставить
                    </button>
                    <button type="button" @click="document.getElementById('subscription-cancel-form').submit(); showCancelModal = false"
                        class="w-full sm:w-auto min-h-[48px] sm:min-h-0 px-4 py-3 sm:py-2.5 text-sm font-semibold text-white bg-red-600 hover:bg-red-700 rounded-xl sm:rounded-lg transition-colors touch-manipulation">
                        Да, отменить
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- Модалка: продление подписки (адаптив + safe area) --}}
    @if($canManageSubscription && $subscription->plan->price && $subscription->plan->price > 0)
        <div x-show="showRenewModal" x-cloak
             x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-3 sm:p-4 pb-[env(safe-area-inset-bottom)] sm:pb-4 bg-black/50" @keydown.escape.window="showRenewModal = false"
             role="dialog" aria-modal="true" aria-labelledby="renew-modal-title">
            <div x-show="showRenewModal" @click.stop
                 x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                 x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0 scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="bg-white dark:bg-slate-900 rounded-2xl shadow-xl border border-slate-200 dark:border-slate-800 max-w-md w-full max-h-[90vh] overflow-y-auto">
                <div class="px-4 sm:px-6 py-4 sm:py-5 border-b border-slate-200 dark:border-slate-800">
                    <div class="flex items-center gap-3">
                        <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-full bg-teal-100 dark:bg-teal-500/20 flex items-center justify-center shrink-0">
                            <i class="fa-solid fa-rotate text-teal-600 dark:text-teal-400 text-lg sm:text-xl"></i>
                        </div>
                        <h2 id="renew-modal-title" class="text-base sm:text-lg font-bold text-slate-900 dark:text-white">Продлить подписку?</h2>
                    </div>
                </div>
                <div class="px-4 sm:px-6 py-4 sm:py-5">
                    <p class="text-sm text-slate-600 dark:text-slate-400 break-words">
                        Будет создан счёт на оплату тарифа «{{ $plan->name }}» — {{ number_format($plan->price, 0, ',', ' ') }} BYN ({{ $plan->interval === 'monthly' ? 'в месяц' : 'в год' }}). После оплаты подписка будет продлена.
                    </p>
                </div>
                <div class="px-4 sm:px-6 py-4 bg-slate-50 dark:bg-slate-800/50 flex flex-col-reverse sm:flex-row gap-2 sm:gap-3 sm:justify-end pb-[env(safe-area-inset-bottom)] sm:pb-4">
                    <button type="button" @click="showRenewModal = false"
                        class="w-full sm:w-auto min-h-[48px] sm:min-h-0 px-4 py-3 sm:py-2.5 text-sm font-semibold text-slate-700 dark:text-slate-300 bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-xl sm:rounded-lg hover:bg-slate-50 dark:hover:bg-slate-600 transition-colors touch-manipulation">
                        Отмена
                    </button>
                    <button type="button" @click="document.getElementById('subscription-renew-form').submit(); showRenewModal = false"
                        class="w-full sm:w-auto min-h-[48px] sm:min-h-0 px-4 py-3 sm:py-2.5 text-sm font-semibold text-white bg-teal-600 hover:bg-teal-700 rounded-xl sm:rounded-lg transition-colors touch-manipulation">
                        Да, продлить
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>

@endsection
