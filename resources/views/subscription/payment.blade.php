@extends('layouts.user')

@section('title', 'Оплата подписки - Cliently')
@section('page-title', 'Оплата подписки')
@section('page-description', 'Оплата тарифа '.$plan->name)

@push('breadcrumbs')
    <x-breadcrumbs :items="[
        ['title' => 'Тарифы', 'url' => route('subscription.index')],
        ['title' => 'Оплата']
    ]" />
@endpush

@section('content')

<div class="max-w-4xl mx-auto">
    <!-- Информация о платеже -->
    <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-8 mb-6">
        <div class="text-center mb-6">
            <div class="h-16 w-16 rounded-full bg-indigo-100 dark:bg-indigo-500/20 flex items-center justify-center mx-auto mb-4">
                <i class="fa-solid fa-credit-card text-indigo-600 dark:text-indigo-400 text-2xl"></i>
            </div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white mb-2">Оплата подписки</h1>
            <p class="text-slate-600 dark:text-slate-400">Тариф: {{ $plan->name }}</p>
        </div>

        <div class="bg-slate-50 dark:bg-slate-800/50 rounded-lg p-6 mb-6">
            <dl class="space-y-4">
                <div class="flex items-center justify-between">
                    <dt class="text-sm font-medium text-slate-600 dark:text-slate-400">Сумма к оплате:</dt>
                    <dd class="text-2xl font-bold text-slate-900 dark:text-white">{{ number_format($invoice->amount, 2) }} {{ $invoice->currency }}</dd>
                </div>
                <div class="flex items-center justify-between">
                    <dt class="text-sm font-medium text-slate-600 dark:text-slate-400">Период:</dt>
                    <dd class="text-base font-semibold text-slate-900 dark:text-white">{{ $plan->interval === 'monthly' ? '1 месяц' : '1 год' }}</dd>
                </div>
                @if($invoice->expires_at)
                    <div class="flex items-center justify-between">
                        <dt class="text-sm font-medium text-slate-600 dark:text-slate-400">Срок действия инвойса:</dt>
                        <dd class="text-base text-slate-900 dark:text-white">{{ $invoice->expires_at->format('d.m.Y H:i') }}</dd>
                    </div>
                @endif
            </dl>
        </div>

        @if($invoice->payment_method === 'widget' && $payment_token)
            <!-- Виджет bePaid -->
            <div id="bepaid-widget-container" class="mb-6">
                <p class="text-sm text-slate-600 dark:text-slate-400 mb-4 text-center">Заполните форму оплаты ниже</p>
                <!-- Здесь будет встроен виджет bePaid -->
                <div id="checkout" class="min-h-[400px]"></div>
            </div>

            <script src="https://checkout.begateway.com/v1/checkout.js"></script>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    if (typeof BeGateway !== 'undefined') {
                        new BeGateway.Checkout({
                            token: '{{ $payment_token }}',
                            checkoutContainer: 'checkout',
                            onSuccess: function(data) {
                                window.location.href = '{{ route('subscription.payment.success', ['invoice' => $invoice->id]) }}';
                            },
                            onError: function(data) {
                                window.location.href = '{{ route('subscription.payment.fail', ['invoice' => $invoice->id]) }}';
                            },
                            onCancel: function(data) {
                                window.location.href = '{{ route('subscription.payment.cancel', ['invoice' => $invoice->id]) }}';
                            }
                        });
                    }
                });
            </script>
        @else
            <!-- Кнопка редиректа на bePaid -->
            <div class="text-center">
                <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">
                    Вы будете перенаправлены на страницу оплаты bePaid
                </p>
                @if($invoice->metadata && isset($invoice->metadata['bepaid_response']['redirect_url']))
                    <a href="{{ $invoice->metadata['bepaid_response']['redirect_url'] }}"
                       class="inline-flex items-center gap-2 px-8 py-4 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-semibold text-lg transition-colors shadow-lg hover:shadow-xl">
                        <i class="fa-solid fa-arrow-right"></i>
                        <span>Перейти к оплате</span>
                    </a>
                @else
                    <p class="text-sm text-rose-600 dark:text-rose-400">
                        Ошибка: не удалось получить ссылку на оплату. Обратитесь в поддержку.
                    </p>
                @endif
            </div>
        @endif
    </div>

    <!-- Информация о безопасности -->
    <div class="bg-blue-50 dark:bg-blue-500/10 border border-blue-200 dark:border-blue-500/20 rounded-lg p-4">
        <div class="flex items-start gap-3">
            <i class="fa-solid fa-shield-halved text-blue-600 dark:text-blue-400 mt-0.5"></i>
            <div>
                <p class="text-sm font-medium text-blue-900 dark:text-blue-300 mb-1">Безопасная оплата</p>
                <p class="text-xs text-blue-700 dark:text-blue-400">
                    Оплата обрабатывается через защищенный платежный шлюз bePaid. Ваши данные защищены.
                </p>
            </div>
        </div>
    </div>

    <!-- Кнопка отмены -->
    <div class="text-center mt-6">
        <a href="{{ route('subscription.index') }}"
           class="inline-flex items-center gap-2 px-4 py-2 text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-300 transition-colors">
            <i class="fa-solid fa-arrow-left"></i>
            <span>Отменить и вернуться к тарифам</span>
        </a>
    </div>
</div>

@endsection
