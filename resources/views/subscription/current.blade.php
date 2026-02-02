@extends('layouts.user')

@section('title', 'Текущая подписка - Cliently')
@section('page-title', 'Текущая подписка')
@section('page-description', 'Информация о вашем тарифе и использовании лимитов')

@push('breadcrumbs')
    <x-breadcrumbs :items="[['title' => 'Текущая подписка']]" />
@endpush

@section('content')

<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-8">
    <!-- Информация о текущем тарифе -->
    <div class="bg-white dark:bg-slate-900 rounded-xl sm:rounded-2xl border border-slate-200 dark:border-slate-800 shadow-lg overflow-hidden mb-6 sm:mb-8">
        <!-- Заголовок карточки -->
        <div class="px-4 sm:px-6 lg:px-8 py-5 sm:py-6 bg-gradient-to-r from-indigo-50 to-slate-50 dark:from-indigo-900/20 dark:to-slate-800/50 border-b border-slate-200 dark:border-slate-700">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between sm:gap-6">
                <div class="min-w-0 flex-1">
                    <h2 class="text-xl sm:text-2xl lg:text-3xl font-bold text-slate-900 dark:text-white mb-1 sm:mb-2 break-words">{{ $plan->name }}</h2>
                    @if($plan->description)
                        <p class="text-sm sm:text-base text-slate-600 dark:text-slate-400 break-words">{{ $plan->description }}</p>
                    @endif
                </div>
                <div class="shrink-0 text-left sm:text-right">
                    @if($plan->price)
                        <div class="text-2xl sm:text-3xl lg:text-4xl font-bold text-slate-900 dark:text-white">{{ number_format($plan->price, 0, ',', ' ') }} BYN</div>
                        <div class="text-sm text-slate-600 dark:text-slate-400">{{ $plan->interval === 'monthly' ? 'в месяц' : 'в год' }}</div>
                    @else
                        <div class="text-2xl sm:text-3xl lg:text-4xl font-bold text-slate-900 dark:text-white">Бесплатно</div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Статус и информация -->
        <div class="px-4 sm:px-6 lg:px-8 py-5 sm:py-6">
            <!-- Статус подписки -->
            <div class="flex flex-wrap items-center gap-x-4 gap-y-3 mb-6">
                <div class="flex items-center gap-2 shrink-0">
                    <span class="text-sm text-slate-600 dark:text-slate-400">Статус:</span>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-full whitespace-nowrap
                        {{ $subscription->isCancelled() ? 'text-orange-700 bg-orange-100 dark:bg-orange-500/20 dark:text-orange-300' : ($subscription->status === 'active' && (!$subscription->ends_at || $subscription->ends_at->isFuture()) ? 'text-green-700 bg-green-100 dark:bg-green-500/20 dark:text-green-300' : '') }}
                        {{ $subscription->status === 'trial' ? 'text-blue-700 bg-blue-100 dark:bg-blue-500/20 dark:text-blue-300' : '' }}
                        {{ $subscription->status === 'expired' || ($subscription->ends_at && $subscription->ends_at->isPast() && $subscription->status !== 'trial') ? 'text-red-700 bg-red-100 dark:bg-red-500/20 dark:text-red-300' : '' }}">
                        @if($subscription->isCancelled())
                            <i class="fa-solid fa-exclamation-triangle"></i>
                            Будет отменена
                        @elseif($subscription->status === 'expired' || ($subscription->ends_at && $subscription->ends_at->isPast() && $subscription->status !== 'trial'))
                            <i class="fa-solid fa-clock"></i>
                            Истекла
                        @elseif($subscription->status === 'active')
                            <i class="fa-solid fa-check-circle"></i>
                            Активна
                        @elseif($subscription->status === 'trial')
                            <i class="fa-solid fa-clock"></i>
                            Пробный период
                        @endif
                    </span>
                </div>
                @if($subscription->starts_at)
                    <div class="flex items-center gap-2 shrink-0">
                        <span class="text-sm text-slate-600 dark:text-slate-400">Начало:</span>
                        <span class="text-sm font-medium text-slate-900 dark:text-white">{{ $subscription->starts_at->format('d.m.Y') }}</span>
                    </div>
                @endif
                @if($subscription->ends_at)
                    <div class="flex items-center gap-2 shrink-0">
                        <span class="text-sm text-slate-600 dark:text-slate-400">Действует до:</span>
                        <span class="text-sm font-medium text-slate-900 dark:text-white">{{ $subscription->ends_at->format('d.m.Y') }}</span>
                    </div>
                @endif
            </div>

            @if(!empty($nextPlanName) && $subscription->ends_at)
                <div class="mb-6 p-4 sm:p-5 bg-slate-50 dark:bg-slate-800/50 rounded-xl border border-slate-200 dark:border-slate-700">
                    <p class="text-sm text-slate-600 dark:text-slate-400 mb-3">
                        <i class="fa-solid fa-info-circle mr-1 text-slate-500 dark:text-slate-500"></i>
                        После {{ $subscription->ends_at->format('d.m.Y') }} будет подключён тариф «{{ $nextPlanName }}».
                    </p>
                    <a href="{{ route('subscription.index') }}" class="inline-flex items-center gap-2 text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300">
                        <i class="fa-solid fa-arrow-up-right-from-square"></i>
                        Вернуться на платный тариф сейчас
                    </a>
                </div>
            @endif

            <!-- Предупреждение об отмене -->
            @if($subscription->isCancelled())
                <div class="mb-6 p-4 sm:p-5 bg-orange-50 dark:bg-orange-500/10 rounded-xl border border-orange-200 dark:border-orange-500/20">
                    <div class="flex items-start gap-3">
                        <div class="shrink-0 w-10 h-10 rounded-full bg-orange-100 dark:bg-orange-500/20 flex items-center justify-center">
                            <i class="fa-solid fa-exclamation-triangle text-orange-600 dark:text-orange-400"></i>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-semibold text-orange-900 dark:text-orange-300 mb-1">Подписка отменена</p>
                            <p class="text-xs sm:text-sm text-orange-700 dark:text-orange-400 break-words">
                                Подписка будет активна до {{ $subscription->ends_at->format('d.m.Y') }}. После этой даты доступ к платным функциям будет ограничен.
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Предупреждение об истечении -->
            @if($subscription->ends_at && $subscription->ends_at->isPast() && $subscription->status !== 'trial' && $plan->price && $plan->price > 0)
                <div class="mb-6 p-4 sm:p-5 bg-red-50 dark:bg-red-500/10 rounded-xl border border-red-200 dark:border-red-500/20">
                    <div class="flex items-start gap-3">
                        <div class="shrink-0 w-10 h-10 rounded-full bg-red-100 dark:bg-red-500/20 flex items-center justify-center">
                            <i class="fa-solid fa-exclamation-circle text-red-600 dark:text-red-400"></i>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-semibold text-red-900 dark:text-red-300 mb-1">Подписка истекла</p>
                            <p class="text-xs sm:text-sm text-red-700 dark:text-red-400 break-words">
                                Ваша подписка истекла {{ $subscription->ends_at->format('d.m.Y') }}. Продлите подписку для восстановления доступа к платным функциям.
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Действия -->
            <div class="flex flex-col sm:flex-row flex-wrap items-stretch sm:items-center gap-3">
                @if($canManageSubscription && $subscription->plan->price && $subscription->plan->price > 0)
                    <form action="{{ route('subscription.renew') }}" method="POST" class="w-full sm:w-auto sm:flex-initial">
                        @csrf
                        <button type="submit"
                            class="w-full min-h-[44px] inline-flex items-center justify-center gap-2 px-6 py-3 bg-green-600 hover:bg-green-700 text-white rounded-xl font-semibold transition-all duration-200 shadow-md hover:shadow-lg">
                            <i class="fa-solid fa-rotate shrink-0"></i>
                            <span>Продлить подписку</span>
                        </button>
                    </form>
                @endif
                <a href="{{ route('subscription.index') }}"
                    class="w-full sm:w-auto min-h-[44px] inline-flex items-center justify-center gap-2 px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-semibold transition-all duration-200 shadow-md hover:shadow-lg">
                    <i class="fa-solid fa-arrow-right shrink-0"></i>
                    <span>Изменить тариф</span>
                </a>
                {{-- Кнопка «Отменить подписку» только если в БД реально платный тариф (не период после перехода на бесплатный) --}}
                @if($canManageSubscription && !$subscription->isCancelled() && $subscription->plan->slug !== 'free')
                    <form action="{{ route('subscription.cancel') }}" method="POST" class="w-full sm:w-auto sm:flex-initial">
                        @csrf
                        <button type="submit" onclick="return confirm('Вы уверены, что хотите отменить подписку? Она будет активна до окончания текущего периода ({{ $subscription->ends_at ? $subscription->ends_at->format('d.m.Y') : 'даты окончания' }}).');"
                            class="w-full min-h-[44px] inline-flex items-center justify-center gap-2 px-6 py-3 bg-slate-200 dark:bg-slate-700 hover:bg-red-600 hover:dark:bg-red-600 text-slate-800 dark:text-slate-200 hover:text-white rounded-xl font-semibold transition-all duration-200">
                            <i class="fa-solid fa-times-circle shrink-0"></i>
                            <span>Отменить подписку</span>
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>

    <!-- Использование лимитов -->
    <div class="bg-white dark:bg-slate-900 rounded-xl sm:rounded-2xl border border-slate-200 dark:border-slate-800 shadow-lg overflow-hidden">
        <div class="px-4 sm:px-6 lg:px-8 py-4 sm:py-5 border-b border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/50">
            <h3 class="text-lg sm:text-xl lg:text-2xl font-bold text-slate-900 dark:text-white">Использование лимитов</h3>
            <p class="text-sm text-slate-600 dark:text-slate-400 mt-1">Текущее использование ресурсов вашего тарифа</p>
        </div>
        <div class="px-4 sm:px-6 lg:px-8 py-5 sm:py-6">
            @if(count($metricsInPlan) > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                    @foreach($metricsInPlan as $item)
                        @php
                            $metric = $item['metric'];
                            $current = (int) ($item['current'] ?? 0);
                            $limit = $item['limit'];
                            $percentage = $limit !== null && $limit > 0 ? min(100, ($current / $limit) * 100) : 0;
                            $isUnlimited = $limit === -1;
                            $isWarning = !$isUnlimited && $percentage >= 80 && $percentage < 100;
                            $isDanger = !$isUnlimited && $percentage >= 100;
                            $icon = $metric->icon ?? 'fa-solid fa-circle';
                        @endphp
                        <div class="p-4 sm:p-5 bg-slate-50 dark:bg-slate-800/50 rounded-xl border border-slate-200 dark:border-slate-700 min-w-0">
                            <div class="flex items-start sm:items-center justify-between gap-3 mb-3">
                                <div class="flex items-center gap-3 min-w-0">
                                    <div class="w-10 h-10 shrink-0 rounded-lg bg-indigo-100 dark:bg-indigo-500/20 flex items-center justify-center">
                                        <i class="{{ $icon }} text-indigo-600 dark:text-indigo-400"></i>
                                    </div>
                                    <span class="text-sm font-semibold text-slate-900 dark:text-white truncate">{{ $metric->label }}</span>
                                </div>
                                <div class="shrink-0 text-right">
                                    @if($isUnlimited)
                                        <div class="text-base sm:text-lg font-bold text-slate-900 dark:text-white">{{ number_format($current, 0, ',', ' ') }}</div>
                                        <div class="text-xs text-slate-500 dark:text-slate-400">Безлимит</div>
                                    @else
                                        <div class="text-base sm:text-lg font-bold text-slate-900 dark:text-white">{{ number_format($current, 0, ',', ' ') }}</div>
                                        <div class="text-xs text-slate-500 dark:text-slate-400">из {{ number_format($limit, 0, ',', ' ') }}</div>
                                    @endif
                                </div>
                            </div>
                            @if(!$isUnlimited)
                                <div class="w-full bg-slate-200 dark:bg-slate-700 rounded-full h-2.5 mb-2">
                                    <div class="h-2.5 rounded-full transition-all duration-300
                                        {{ $isDanger ? 'bg-red-500' : ($isWarning ? 'bg-amber-500' : 'bg-indigo-600') }}"
                                        style="width: {{ $percentage }}%"></div>
                                </div>
                                <div class="flex flex-wrap items-center justify-between gap-x-2 gap-y-1">
                                    <div class="text-xs text-slate-500 dark:text-slate-400">Использовано {{ round($percentage) }}%</div>
                                    @if($isWarning)
                                        <div class="flex items-center gap-1 text-xs text-amber-600 dark:text-amber-400 font-medium shrink-0">
                                            <i class="fa-solid fa-exclamation-triangle"></i>
                                            <span>Приближаетесь к лимиту</span>
                                        </div>
                                    @elseif($isDanger)
                                        <div class="flex items-center gap-1 text-xs text-red-600 dark:text-red-400 font-medium shrink-0">
                                            <i class="fa-solid fa-exclamation-circle"></i>
                                            <span>Лимит достигнут</span>
                                        </div>
                                    @endif
                                </div>
                            @else
                                <div class="pt-2">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-indigo-100 text-indigo-700 dark:bg-indigo-500/20 dark:text-indigo-300">
                                        Безлимит
                                    </span>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-sm text-slate-600 dark:text-slate-400">В этом тарифе нет лимитируемых метрик.</p>
            @endif
        </div>
    </div>

    <!-- История платежей -->
    @if($subscription && $subscription->invoices && $subscription->invoices->count() > 0)
        <div class="bg-white dark:bg-slate-900 rounded-xl sm:rounded-2xl border border-slate-200 dark:border-slate-800 shadow-lg overflow-hidden mt-6 sm:mt-8">
            <div class="px-4 sm:px-6 lg:px-8 py-4 sm:py-5 border-b border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/50">
                <h3 class="text-lg sm:text-xl lg:text-2xl font-bold text-slate-900 dark:text-white">История платежей</h3>
                <p class="text-sm text-slate-600 dark:text-slate-400 mt-1">Все платежи по вашей подписке</p>
            </div>
            <div class="px-4 sm:px-6 lg:px-8 py-5 sm:py-6">
                <div class="space-y-3 sm:space-y-4">
                    @foreach($subscription->invoices->sortByDesc('created_at') as $invoice)
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 p-4 sm:p-5 bg-slate-50 dark:bg-slate-800/50 rounded-xl border border-slate-200 dark:border-slate-700">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="h-10 w-10 sm:h-12 sm:w-12 shrink-0 rounded-lg bg-indigo-100 dark:bg-indigo-500/20 flex items-center justify-center">
                                    <i class="fa-solid fa-receipt text-indigo-600 dark:text-indigo-400"></i>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-slate-900 dark:text-white">Инвойс #{{ $invoice->id }}</p>
                                    <p class="text-xs text-slate-600 dark:text-slate-400">{{ $invoice->created_at->format('d.m.Y H:i') }}</p>
                                </div>
                            </div>
                            <div class="shrink-0 text-left sm:text-right">
                                <p class="text-base font-bold text-slate-900 dark:text-white">{{ number_format($invoice->amount, 2) }} {{ $invoice->currency }}</p>
                                @php
                                    $statusColors = [
                                        'pending' => 'text-amber-600 dark:text-amber-400',
                                        'paid' => 'text-emerald-600 dark:text-emerald-400',
                                        'failed' => 'text-rose-600 dark:text-rose-400',
                                        'cancelled' => 'text-slate-600 dark:text-slate-400',
                                        'refunded' => 'text-blue-600 dark:text-blue-400',
                                    ];
                                    $statusLabels = [
                                        'pending' => 'Ожидает',
                                        'paid' => 'Оплачено',
                                        'failed' => 'Ошибка',
                                        'cancelled' => 'Отменено',
                                        'refunded' => 'Возврат',
                                    ];
                                @endphp
                                <p class="text-xs font-medium {{ $statusColors[$invoice->status] ?? 'text-slate-600' }}">
                                    {{ $statusLabels[$invoice->status] ?? $invoice->status }}
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
</div>

@endsection

