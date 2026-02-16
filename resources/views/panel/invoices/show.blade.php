@extends('layouts.panel')

@section('title', 'Инвойс #'.$invoice->id)

@section('content')
    <div class="max-w-6xl mx-auto">
        <!-- Breadcrumbs -->
        <nav class="mb-4 sm:mb-6" aria-label="Breadcrumb">
            <ol class="flex items-center space-x-1 sm:space-x-2 text-xs sm:text-sm text-slate-500 dark:text-slate-400">
                <li>
                    <a href="{{ route('panel.index') }}" class="hover:text-slate-700 dark:hover:text-slate-300">Главная</a>
                </li>
                <li><i class="fa-solid fa-chevron-right text-xs"></i></li>
                <li>
                    <a href="{{ route('panel.invoices') }}" class="hover:text-slate-700 dark:hover:text-slate-300">Платежи</a>
                </li>
                <li><i class="fa-solid fa-chevron-right text-xs"></i></li>
                <li class="text-slate-900 dark:text-white font-medium">Инвойс #{{ $invoice->id }}</li>
            </ol>
        </nav>

        <!-- Заголовок -->
        <div class="mb-6 bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-slate-900 dark:text-white mb-2">Инвойс #{{ $invoice->id }}</h1>
                    <p class="text-sm text-slate-600 dark:text-slate-400">Создан {{ $invoice->created_at->format('d.m.Y H:i') }}</p>
                </div>
                @php
                    $statusColors = [
                        'pending' => 'bg-amber-100 text-amber-700 dark:bg-amber-500/20 dark:text-amber-400',
                        'paid' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-400',
                        'failed' => 'bg-rose-100 text-rose-700 dark:bg-rose-500/20 dark:text-rose-400',
                        'cancelled' => 'bg-slate-100 text-slate-700 dark:bg-slate-500/20 dark:text-slate-400',
                        'refunded' => 'bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-400',
                    ];
                    $statusLabels = [
                        'pending' => 'Ожидает оплаты',
                        'paid' => 'Оплачено',
                        'failed' => 'Ошибка',
                        'cancelled' => 'Отменено',
                        'refunded' => 'Возврат',
                    ];
                @endphp
                <span class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-medium {{ $statusColors[$invoice->status] ?? 'bg-slate-100' }}">
                    {{ $statusLabels[$invoice->status] ?? $invoice->status }}
                </span>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Основная информация -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Информация о платеже -->
                <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-6">
                    <h2 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">Информация о платеже</h2>
                    <dl class="space-y-4">
                        <div>
                            <dt class="text-sm font-medium text-slate-500 dark:text-slate-400">Сумма</dt>
                            <dd class="mt-1 text-2xl font-bold text-slate-900 dark:text-white">{{ number_format($invoice->amount, 2) }} {{ $invoice->currency }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-slate-500 dark:text-slate-400">Тариф</dt>
                            <dd class="mt-1 text-base font-semibold text-slate-900 dark:text-white">{{ $invoice->plan->name }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-slate-500 dark:text-slate-400">Способ оплаты</dt>
                            <dd class="mt-1 text-base text-slate-900 dark:text-white">
                                {{ $invoice->payment_method === 'redirect' ? 'Редирект на bePaid' : 'Виджет bePaid' }}
                            </dd>
                        </div>
                        @if($invoice->bepaid_transaction_id)
                            <div>
                                <dt class="text-sm font-medium text-slate-500 dark:text-slate-400">ID транзакции bePaid</dt>
                                <dd class="mt-1 text-base font-mono text-slate-900 dark:text-white">{{ $invoice->bepaid_transaction_id }}</dd>
                            </div>
                        @endif
                        @if($invoice->paid_at)
                            <div>
                                <dt class="text-sm font-medium text-slate-500 dark:text-slate-400">Дата оплаты</dt>
                                <dd class="mt-1 text-base text-slate-900 dark:text-white">{{ $invoice->paid_at->format('d.m.Y H:i') }}</dd>
                            </div>
                        @endif
                        @if($invoice->expires_at)
                            <div>
                                <dt class="text-sm font-medium text-slate-500 dark:text-slate-400">Срок действия</dt>
                                <dd class="mt-1 text-base text-slate-900 dark:text-white">
                                    {{ $invoice->expires_at->format('d.m.Y H:i') }}
                                    @if($invoice->isExpired())
                                        <span class="text-rose-600 dark:text-rose-400 text-sm ml-2">(Истек)</span>
                                    @endif
                                </dd>
                            </div>
                        @endif
                    </dl>
                </div>

                <!-- Пользователь -->
                <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-6">
                    <h2 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">Пользователь</h2>
                    <dl class="space-y-3">
                        <div>
                            <dt class="text-sm font-medium text-slate-500 dark:text-slate-400">Имя</dt>
                            <dd class="mt-1 text-base font-semibold text-slate-900 dark:text-white">{{ $invoice->user->name }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-slate-500 dark:text-slate-400">Email</dt>
                            <dd class="mt-1 text-base text-slate-900 dark:text-white">{{ $invoice->user->email }}</dd>
                        </div>
                    </dl>
                </div>

                @if($invoice->subscription)
                    <!-- Подписка -->
                    <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-6">
                        <h2 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">Подписка</h2>
                        <dl class="space-y-3">
                            <div>
                                <dt class="text-sm font-medium text-slate-500 dark:text-slate-400">Статус подписки</dt>
                                <dd class="mt-1">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium
                                        @if($invoice->subscription->status === 'active') bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-400
                                        @elseif($invoice->subscription->status === 'trial') bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-400
                                        @else bg-slate-100 text-slate-700 dark:bg-slate-500/20 dark:text-slate-400 @endif">
                                        {{ $invoice->subscription->status }}
                                    </span>
                                </dd>
                            </div>
                            @if($invoice->subscription->ends_at)
                                <div>
                                    <dt class="text-sm font-medium text-slate-500 dark:text-slate-400">Действует до</dt>
                                    <dd class="mt-1 text-base text-slate-900 dark:text-white">{{ $invoice->subscription->ends_at->format('d.m.Y') }}</dd>
                                </div>
                            @endif
                        </dl>
                    </div>
                @endif
            </div>

            <!-- Боковая панель -->
            <div class="space-y-6">
                <!-- Действия -->
                @can('panel.payments.manage')
                    @if($invoice->isPaid() && !$invoice->isRefunded())
                        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-6">
                            <h3 class="text-base font-semibold text-slate-900 dark:text-white mb-4">Действия</h3>
                            <form method="POST" action="{{ route('panel.invoices.refund', $invoice) }}" onsubmit="return confirm('Вы уверены, что хотите выполнить возврат средств?');">
                                @csrf
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Сумма возврата</label>
                                    <input type="number" step="0.01" name="amount" max="{{ $invoice->amount }}"
                                        placeholder="Оставьте пустым для полного возврата"
                                        class="w-full px-4 py-2 rounded-lg border border-slate-300 dark:border-slate-700 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-sm">
                                </div>
                                <button type="submit"
                                    class="w-full px-4 py-2.5 text-sm font-medium text-white bg-amber-600 hover:bg-amber-700 rounded-lg transition-colors">
                                    <i class="fa-solid fa-undo mr-2"></i>
                                    Выполнить возврат
                                </button>
                            </form>
                        </div>
                    @endif
                @endcan

                <!-- Метаданные -->
                @if($invoice->metadata)
                    <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-6">
                        <h3 class="text-base font-semibold text-slate-900 dark:text-white mb-4">Дополнительная информация</h3>
                        <pre class="text-xs text-slate-600 dark:text-slate-400 overflow-auto">{{ json_encode($invoice->metadata, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
