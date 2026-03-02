@extends('layouts.user')

@section('title', 'Счёт #' . $invoice->id . ' - Cliently')
@section('page-title', 'Счёт #' . $invoice->id)
@section('page-description', null)

@push('breadcrumbs')
    <x-breadcrumbs :items="[
        ['title' => 'Мои счета', 'url' => route('invoices.index')],
        ['title' => '#' . $invoice->id]
    ]" />
@endpush

@section('content')

@php
    $statusLabels = [
        'pending' => 'Ожидает оплаты',
        'paid' => 'Оплачено',
        'failed' => 'Ошибка',
        'cancelled' => 'Отменено',
        'refunded' => 'Возврат',
    ];
    $statusColors = [
        'pending' => 'bg-amber-100 text-amber-700 dark:bg-amber-500/20 dark:text-amber-400',
        'paid' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-400',
        'failed' => 'bg-rose-100 text-rose-700 dark:bg-rose-500/20 dark:text-rose-400',
        'cancelled' => 'bg-slate-100 text-slate-700 dark:bg-slate-500/20 dark:text-slate-400',
        'refunded' => 'bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-400',
    ];
@endphp

<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 pt-2 sm:pt-4 pb-6 sm:pb-8">
    <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
        <div class="p-4 sm:p-6 border-b border-slate-200 dark:border-slate-700">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-xl font-bold text-slate-900 dark:text-white">Счёт #{{ $invoice->id }}</h1>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Создан {{ $invoice->created_at->format('d.m.Y H:i') }}</p>
                </div>
                <span class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-medium {{ $statusColors[$invoice->status] ?? 'bg-slate-100 text-slate-700' }}">
                    {{ $statusLabels[$invoice->status] ?? $invoice->status }}
                </span>
            </div>
        </div>

        <div class="p-4 sm:p-6 space-y-6">
            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <dt class="text-sm font-medium text-slate-500 dark:text-slate-400">Тип оплаты</dt>
                    <dd class="mt-1 text-base font-semibold text-slate-900 dark:text-white">{{ $paymentTypeLabel }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-slate-500 dark:text-slate-400">Сумма</dt>
                    <dd class="mt-1 text-2xl font-bold text-slate-900 dark:text-white">{{ number_format($invoice->amount, 2, ',', ' ') }} {{ $invoice->currency }}</dd>
                </div>
                @if($invoice->plan)
                    <div>
                        <dt class="text-sm font-medium text-slate-500 dark:text-slate-400">Тариф</dt>
                        <dd class="mt-1 text-base font-semibold text-slate-900 dark:text-white">{{ $invoice->plan->name }}</dd>
                    </div>
                @endif
                <div>
                    <dt class="text-sm font-medium text-slate-500 dark:text-slate-400">Способ оплаты</dt>
                    <dd class="mt-1 text-base text-slate-900 dark:text-white">{{ $invoice->getGatewayDisplayName() }}</dd>
                </div>
                @if($invoice->expires_at)
                    <div>
                        <dt class="text-sm font-medium text-slate-500 dark:text-slate-400">Срок оплаты</dt>
                        <dd class="mt-1 text-base text-slate-900 dark:text-white">
                            {{ $invoice->expires_at->format('d.m.Y H:i') }}
                            @if($invoice->isExpired())
                                <span class="text-rose-600 dark:text-rose-400 text-sm ml-1">(истёк)</span>
                            @endif
                        </dd>
                    </div>
                @endif
                @if($invoice->paid_at)
                    <div>
                        <dt class="text-sm font-medium text-slate-500 dark:text-slate-400">Дата оплаты</dt>
                        <dd class="mt-1 text-base text-slate-900 dark:text-white">{{ $invoice->paid_at->format('d.m.Y H:i') }}</dd>
                    </div>
                @endif
                @if($invoice->getTransactionId())
                    <div class="sm:col-span-2">
                        <dt class="text-sm font-medium text-slate-500 dark:text-slate-400">Номер для поддержки</dt>
                        <dd class="mt-1 text-base font-mono text-slate-700 dark:text-slate-300 break-all">{{ $invoice->getTransactionId() }}</dd>
                    </div>
                @endif
            </dl>

            {{-- Действия --}}
            <div class="flex flex-wrap items-center gap-3 pt-4 border-t border-slate-200 dark:border-slate-700">
                @if($canPay)
                    <a href="{{ route('payment.select', $invoice) }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-medium transition-colors">
                        <i class="fa-solid fa-credit-card"></i>
                        Оплатить
                    </a>
                @elseif($invoice->isPending() && $invoice->isExpired() && $retryUrl)
                    <p class="text-sm text-slate-600 dark:text-slate-400">Срок оплаты истёк.</p>
                    <a href="{{ $retryUrl }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700 rounded-lg text-sm font-medium transition-colors">
                        <i class="fa-solid fa-arrow-right"></i>
                        Оформить заново
                    </a>
                @elseif(in_array($invoice->status, ['failed', 'cancelled']) && $retryUrl)
                    <a href="{{ $retryUrl }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700 rounded-lg text-sm font-medium transition-colors">
                        <i class="fa-solid fa-arrow-right"></i>
                        Попробовать снова
                    </a>
                @endif
                <a href="{{ route('invoices.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white text-sm font-medium transition-colors">
                    <i class="fa-solid fa-arrow-left"></i>
                    К списку счетов
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
