@extends('layouts.user')

@section('title', 'Оплата через ЕРИП - Cliently')
@section('page-title', 'Оплата через ЕРИП')
@section('page-description', 'Инструкция по оплате через систему "Расчёт" (ЕРИП)')

@push('breadcrumbs')
    <x-breadcrumbs :items="[
        ['title' => 'Тарифы', 'url' => route('subscription.index')],
        ['title' => 'Оплата через ЕРИП']
    ]" />
@endpush

@section('content')

<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
    {{-- Информация о заказе --}}
    <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-6 mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-lg font-semibold text-slate-900 dark:text-white">
                    {{ $plan->name ?? 'Оплата' }}
                </h2>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                    #{{ $invoice->id }}
                </p>
            </div>
            <div class="text-right">
                <div class="text-2xl font-bold text-slate-900 dark:text-white">
                    {{ number_format($invoice->amount, 2, ',', ' ') }} {{ $invoice->currency }}
                </div>
            </div>
        </div>
    </div>

    {{-- Номер счёта и QR-код --}}
    <div class="bg-gradient-to-r from-indigo-500 to-purple-600 rounded-xl shadow-lg p-6 mb-6 text-white">
        <div class="flex flex-col md:flex-row items-center gap-6">
            {{-- QR-код --}}
            @if($invoiceUrl)
            <div class="flex-shrink-0">
                <div class="bg-white p-3 rounded-xl shadow-lg">
                    <img src="{{ route('payment.qr', ['invoice' => $invoice->id, 'size' => 150]) }}" 
                         alt="QR-код для оплаты" 
                         class="w-36 h-36"
                         loading="lazy">
                </div>
                <p class="text-xs text-center mt-2 opacity-80">Сканируйте для оплаты</p>
            </div>
            @endif

            {{-- Номер счёта --}}
            <div class="text-center md:text-left flex-1">
                <p class="text-sm opacity-80 mb-2">Номер счёта для оплаты</p>
                <div class="text-4xl font-bold tracking-wider mb-4" id="erip-number">
                    {{ $eripInvoiceNo }}
                </div>
                <div class="flex flex-wrap gap-2 justify-center md:justify-start">
                    <button onclick="copyToClipboard('{{ $eripInvoiceNo }}', event)"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-white/20 hover:bg-white/30 rounded-lg transition-colors text-sm">
                        <i class="fa-regular fa-copy"></i>
                        Скопировать номер
                    </button>
                    @if($invoiceUrl)
                    <button onclick="copyToClipboard('{{ $invoiceUrl }}', event)"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-white/20 hover:bg-white/30 rounded-lg transition-colors text-sm">
                        <i class="fa-solid fa-link"></i>
                        Скопировать ссылку
                    </button>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Инструкция --}}
    {{-- <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden mb-6">
        <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-700">
            <h3 class="text-base font-semibold text-slate-900 dark:text-white flex items-center gap-2">
                <i class="fa-solid fa-list-ol text-indigo-600 dark:text-indigo-400"></i>
                Как оплатить через ЕРИП
            </h3>
        </div>
        <div class="p-6">
            <ol class="space-y-4">
                <li class="flex gap-4">
                    <span class="flex-shrink-0 w-8 h-8 bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 rounded-full flex items-center justify-center font-semibold text-sm">1</span>
                    <div>
                        <p class="font-medium text-slate-900 dark:text-white">Войдите в интернет-банкинг</p>
                        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                            Или воспользуйтесь инфокиоском, банкоматом, кассой банка
                        </p>
                    </div>
                </li>
                <li class="flex gap-4">
                    <span class="flex-shrink-0 w-8 h-8 bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 rounded-full flex items-center justify-center font-semibold text-sm">2</span>
                    <div>
                        <p class="font-medium text-slate-900 dark:text-white">Найдите в дереве ЕРИП сервис E-POS</p>
                        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                            E-POS — раздел приёма платежей в системе «Расчёт»
                        </p>
                    </div>
                </li>
                <li class="flex gap-4">
                    <span class="flex-shrink-0 w-8 h-8 bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 rounded-full flex items-center justify-center font-semibold text-sm">3</span>
                    <div>
                        <p class="font-medium text-slate-900 dark:text-white">Введите номер счёта</p>
                        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                            Укажите номер: <span class="font-mono font-bold text-indigo-600 dark:text-indigo-400">{{ $eripInvoiceNo }}</span>
                        </p>
                    </div>
                </li>
                <li class="flex gap-4">
                    <span class="flex-shrink-0 w-8 h-8 bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 rounded-full flex items-center justify-center font-semibold text-sm">4</span>
                    <div>
                        <p class="font-medium text-slate-900 dark:text-white">Проверьте данные и оплатите</p>
                        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                            Сумма: <span class="font-bold">{{ number_format($invoice->amount, 2, ',', ' ') }} {{ $invoice->currency }}</span>
                        </p>
                    </div>
                </li>
            </ol>
        </div>
    </div> --}}

    {{-- Важная информация --}}
    {{-- <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-xl p-4 mb-6">
        <div class="flex gap-3">
            <i class="fa-solid fa-circle-info text-amber-600 dark:text-amber-400 mt-0.5"></i>
            <div class="text-sm text-amber-800 dark:text-amber-200">
                <p class="font-medium mb-1">Важно</p>
                <ul class="list-disc list-inside space-y-1 text-amber-700 dark:text-amber-300">
                    <li>После оплаты подписка активируется автоматически в течение нескольких минут</li>
                    <li>Счёт действителен 7 дней</li>
                    <li>Сохраните чек об оплате</li>
                </ul>
            </div>
        </div>
    </div> --}}

    {{-- Кнопки --}}
    <div class="flex flex-col sm:flex-row gap-3">
        @if($invoiceUrl)
            <a href="{{ $invoiceUrl }}" target="_blank"
               class="flex-1 inline-flex items-center justify-center gap-2 px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-xl transition-colors">
                <i class="fa-solid fa-external-link"></i>
                Оплатить
            </a>
        @endif
        <a href="{{ route('subscription.current') }}"
           class="flex-1 inline-flex items-center justify-center gap-2 px-6 py-3 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-medium rounded-xl transition-colors">
            Назад
        </a>
    </div>

    {{-- Проверка статуса --}}
    <div class="mt-6 text-center">
        <p class="text-sm text-slate-500 dark:text-slate-400 mb-2">
            Уже оплатили?
        </p>
        <a href="{{ route('subscription.current') }}"
           class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">
            Проверить статус подписки
        </a>
    </div>
</div>

@endsection

@push('scripts')
<script>
function copyToClipboard(text, event) {
    navigator.clipboard.writeText(text).then(() => {
        const btn = event.target.closest('button');
        const originalHtml = btn.innerHTML;
        btn.innerHTML = '<i class="fa-solid fa-check"></i> Скопировано!';
        btn.classList.add('bg-green-500/30');
        
        setTimeout(() => {
            btn.innerHTML = originalHtml;
            btn.classList.remove('bg-green-500/30');
        }, 2000);
    }).catch(() => {
        // Fallback для старых браузеров
        const textarea = document.createElement('textarea');
        textarea.value = text;
        document.body.appendChild(textarea);
        textarea.select();
        document.execCommand('copy');
        document.body.removeChild(textarea);
        alert('Скопировано: ' + text);
    });
}
</script>
@endpush
