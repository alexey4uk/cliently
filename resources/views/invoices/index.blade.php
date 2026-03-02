@extends('layouts.user')

@section('title', 'Мои счета - Cliently')
@section('page-title', 'Мои счета')
@section('page-description', 'История платежей и счета к оплате')

@push('breadcrumbs')
    <x-breadcrumbs :items="[['title' => 'Мои счета']]" />
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

<div class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8 pt-2 sm:pt-4 pb-6 sm:pb-8">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Мои счета</h1>
        <p class="text-sm text-slate-600 dark:text-slate-400 mt-1">Подписка, пополнение баланса и другие платежи</p>
    </div>

    <form method="GET" action="{{ route('invoices.index') }}" class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-4 sm:p-6 mb-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Статус</label>
                <select name="status" class="w-full px-3 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                    <option value="">Все</option>
                    @foreach($statusLabels as $key => $label)
                        <option value="{{ $key }}" {{ $status === $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Тип</label>
                <select name="payment_type" class="w-full px-3 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                    <option value="">Все</option>
                    @foreach($paymentTypes as $key => $label)
                        <option value="{{ $key }}" {{ $payment_type === $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end gap-2 sm:col-span-2">
                <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-medium transition-colors">
                    <i class="fa-solid fa-filter mr-2"></i>Применить
                </button>
                <a href="{{ route('invoices.index') }}" class="px-4 py-2 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 rounded-lg text-sm font-medium hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors">
                    <i class="fa-solid fa-rotate-left"></i>
                </a>
            </div>
        </div>
    </form>

    @if($invoices->count() > 0)
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-slate-50 dark:bg-slate-800/50 border-b border-slate-200 dark:border-slate-700">
                        <tr>
                            <th class="px-4 sm:px-6 py-3 text-left text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase">№</th>
                            <th class="px-4 sm:px-6 py-3 text-left text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase">Дата</th>
                            <th class="px-4 sm:px-6 py-3 text-left text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase">Тип</th>
                            <th class="px-4 sm:px-6 py-3 text-left text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase">Назначение</th>
                            <th class="px-4 sm:px-6 py-3 text-left text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase">Сумма</th>
                            <th class="px-4 sm:px-6 py-3 text-left text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase">Статус</th>
                            <th class="px-4 sm:px-6 py-3 text-right text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase">Действия</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                        @foreach($invoices as $inv)
                            <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/30 transition-colors">
                                <td class="px-4 sm:px-6 py-3 whitespace-nowrap">
                                    <a href="{{ route('invoices.show', $inv) }}" class="text-sm font-semibold text-indigo-600 dark:text-indigo-400 hover:underline">#{{ $inv->id }}</a>
                                </td>
                                <td class="px-4 sm:px-6 py-3 text-sm text-slate-700 dark:text-slate-300">
                                    {{ $inv->created_at->format('d.m.Y H:i') }}
                                </td>
                                <td class="px-4 sm:px-6 py-3 text-sm text-slate-700 dark:text-slate-300">
                                    {{ $paymentTypes[$inv->payment_type] ?? $inv->payment_type ?? '—' }}
                                </td>
                                <td class="px-4 sm:px-6 py-3 text-sm text-slate-700 dark:text-slate-300">
                                    {{ $inv->plan?->name ?? '—' }}
                                </td>
                                <td class="px-4 sm:px-6 py-3 text-sm font-semibold text-slate-900 dark:text-white">
                                    {{ number_format($inv->amount, 2, ',', ' ') }} {{ $inv->currency }}
                                </td>
                                <td class="px-4 sm:px-6 py-3">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium {{ $statusColors[$inv->status] ?? 'bg-slate-100 text-slate-700' }}">
                                        {{ $statusLabels[$inv->status] ?? $inv->status }}
                                    </span>
                                </td>
                                <td class="px-4 sm:px-6 py-3 text-right">
                                    <a href="{{ route('invoices.show', $inv) }}" class="inline-flex items-center text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:underline">
                                        Подробнее
                                        <i class="fa-solid fa-arrow-right ml-1 text-xs"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="px-4 sm:px-6 py-3 border-t border-slate-200 dark:border-slate-700">
                {{ $invoices->links() }}
            </div>
        </div>
    @else
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-8 sm:p-12 text-center">
            <div class="inline-flex items-center justify-center w-14 h-14 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-400 mb-4">
                <i class="fa-solid fa-file-invoice text-2xl"></i>
            </div>
            <h2 class="text-lg font-semibold text-slate-900 dark:text-white mb-2">Счетов пока нет</h2>
            <p class="text-sm text-slate-600 dark:text-slate-400 max-w-sm mx-auto">
                Здесь будут отображаться счета за подписку, пополнение баланса и другие платежи.
            </p>
            <a href="{{ route('subscription.index') }}" class="inline-flex items-center gap-2 mt-4 text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:underline">
                <i class="fa-solid fa-credit-card"></i>
                Перейти к тарифам
            </a>
        </div>
    @endif
</div>
@endsection
