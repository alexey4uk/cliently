@extends('layouts.user')

@section('title', 'Выбор способа оплаты - Cliently')
@section('page-title', 'Выбор способа оплаты')
@section('page-description', 'Выберите удобный способ оплаты')

@push('breadcrumbs')
    <x-breadcrumbs :items="[
        ['title' => 'Тарифы', 'url' => route('subscription.index')],
        ['title' => 'Оплата']
    ]" />
@endpush

@section('content')

<style>
.payment-submit-btn__spinner { align-items: center; gap: 0.5rem; }
.payment-submit-btn__spinner-dot {
    width: 1rem;
    height: 1rem;
    border: 2px solid rgba(255,255,255,0.3);
    border-top-color: #fff;
    border-radius: 50%;
    animation: payment-btn-spin 0.7s linear infinite;
}
@keyframes payment-btn-spin { to { transform: rotate(360deg); } }
</style>

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
                @if($plan && $plan->interval)
                    <p class="text-sm text-slate-500 dark:text-slate-400">
                        {{ $plan->interval === 'monthly' ? 'за месяц' : 'за год' }}
                    </p>
                @endif
            </div>
        </div>
    </div>

    {{-- Выбор платёжной системы (шлюза) --}}
    @if($showGatewaySelector && count($availableGateways) > 1)
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden mb-6">
            <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-700">
                <h3 class="text-base font-semibold text-slate-900 dark:text-white">
                    Платёжная система
                </h3>
            </div>
            <div class="p-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    @foreach($availableGateways as $gatewayKey => $gateway)
                        <a 
                            href="{{ route('payment.select', ['invoice' => $invoice, 'gateway' => $gatewayKey]) }}"
                            class="flex items-center gap-3 p-4 rounded-xl border-2 transition-all duration-200 {{ $selectedGateway === $gatewayKey 
                                ? 'border-indigo-500 bg-indigo-50 dark:bg-indigo-900/20' 
                                : 'border-slate-200 dark:border-slate-700 hover:border-slate-300 dark:hover:border-slate-600 hover:bg-slate-50 dark:hover:bg-slate-800/50' }}"
                        >
                            <div class="w-12 h-12 rounded-lg flex items-center justify-center flex-shrink-0 {{ $selectedGateway === $gatewayKey 
                                ? 'bg-indigo-100 dark:bg-indigo-900/40' 
                                : 'bg-slate-100 dark:bg-slate-800' }}">
                                <i class="fa-solid {{ $gateway['icon'] }} text-xl {{ $selectedGateway === $gatewayKey 
                                    ? 'text-indigo-600 dark:text-indigo-400' 
                                    : 'text-slate-500 dark:text-slate-400' }}"></i>
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="font-semibold {{ $selectedGateway === $gatewayKey 
                                    ? 'text-indigo-900 dark:text-indigo-100' 
                                    : 'text-slate-900 dark:text-white' }}">
                                    {{ $gateway['display_name'] }}
                                </div>
                                <div class="text-sm {{ $selectedGateway === $gatewayKey 
                                    ? 'text-indigo-600 dark:text-indigo-300' 
                                    : 'text-slate-500 dark:text-slate-400' }}">
                                    {{ $gateway['description'] }}
                                </div>
                            </div>
                            @if($selectedGateway === $gatewayKey)
                                <i class="fa-solid fa-check-circle text-indigo-500 text-xl flex-shrink-0"></i>
                            @endif
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    {{-- Выбор способа оплаты --}}
    <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-700">
            <h3 class="text-base font-semibold text-slate-900 dark:text-white">
                @if($showGatewaySelector && count($availableGateways) > 1)
                    Способ оплаты
                    <span class="text-sm font-normal text-slate-500 dark:text-slate-400">
                        ({{ $availableGateways[$selectedGateway]['display_name'] ?? $selectedGateway }})
                    </span>
                @else
                    Cпособ оплаты
                @endif
            </h3>
        </div>

        @if(count($paymentMethods) > 0)
            <form action="{{ route('payment.process', $invoice) }}" method="POST" id="payment-form">
                @csrf
                <input type="hidden" name="gateway" value="{{ $selectedGateway }}">
                
                @php
                    $enabledMethods = collect($paymentMethods)->filter(fn($m) => empty($m['disabled']));
                    $firstEnabledIndex = $enabledMethods->keys()->first();
                @endphp
                <div class="divide-y divide-slate-200 dark:divide-slate-700">
                    @foreach($paymentMethods as $index => $method)
                        @php
                            $isDisabled = !empty($method['disabled']);
                            $isFirstEnabled = $index === $firstEnabledIndex;
                        @endphp
                        <label class="flex items-center gap-4 px-6 py-4 transition-colors group {{ $isDisabled ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-800/50' }}">
                            <input 
                                type="radio" 
                                name="payment_system_id" 
                                value="{{ $method['id'] }}"
                                class="w-5 h-5 text-indigo-600 border-slate-300 dark:border-slate-600 focus:ring-indigo-500 dark:bg-slate-800"
                                {{ $isFirstEnabled ? 'checked' : '' }}
                                {{ $isDisabled ? 'disabled' : '' }}
                                required
                            >
                            <div class="flex items-center gap-3 flex-1 min-w-0">
                                <div class="w-10 h-10 rounded-lg bg-slate-100 dark:bg-slate-800 flex items-center justify-center flex-shrink-0 {{ $isDisabled ? '' : 'group-hover:bg-indigo-100 dark:group-hover:bg-indigo-900/30' }} transition-colors">
                                    <i class="fa-solid {{ $method['icon'] ?? 'fa-credit-card' }} text-lg text-slate-600 dark:text-slate-400 {{ $isDisabled ? '' : 'group-hover:text-indigo-600 dark:group-hover:text-indigo-400' }} transition-colors"></i>
                                </div>
                                <div class="min-w-0">
                                    <div class="font-medium text-slate-900 dark:text-white truncate">
                                        {{ $method['name'] }}
                                        @if($isDisabled)
                                            <span class="text-xs text-amber-600 dark:text-amber-400 ml-1">(скоро)</span>
                                        @endif
                                    </div>
                                    @if(!empty($method['description']))
                                        <div class="text-sm text-slate-500 dark:text-slate-400 truncate">
                                            {{ $method['description'] }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </label>
                    @endforeach
                </div>

                <div class="px-6 py-4 bg-slate-50 dark:bg-slate-800/50 border-t border-slate-200 dark:border-slate-700">
                    <button
                        type="submit"
                        id="payment-submit-btn"
                        class="payment-submit-btn w-full inline-flex items-center justify-center gap-2 px-6 py-3 bg-indigo-600 hover:bg-indigo-700 disabled:opacity-70 disabled:pointer-events-none text-white font-semibold rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-slate-900"
                    >
                        <span class="payment-submit-btn__label">
                            <i class="fa-solid fa-arrow-right mr-2"></i>
                            Перейти к оплате
                        </span>
                        <span class="payment-submit-btn__spinner" style="display: none;">
                            <span class="payment-submit-btn__spinner-dot"></span>
                            Подождите...
                        </span>
                    </button>
                </div>
            </form>
        @else
            <div class="px-6 py-12 text-center">
                <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center">
                    <i class="fa-solid fa-credit-card-alt text-2xl text-slate-400"></i>
                </div>
                <h4 class="text-lg font-medium text-slate-900 dark:text-white mb-2">
                    Способы оплаты недоступны
                </h4>
                <p class="text-slate-500 dark:text-slate-400 mb-4">
                    @if($showGatewaySelector && count($availableGateways) > 1)
                        Попробуйте выбрать другую платёжную систему.
                    @else
                        К сожалению, в данный момент нет доступных способов оплаты. Попробуйте позже.
                    @endif
                </p>
                <a href="{{ route('subscription.index') }}" class="inline-flex items-center text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 dark:hover:text-indigo-300 font-medium">
                    <i class="fa-solid fa-arrow-left mr-2"></i>
                    Вернуться к тарифам
                </a>
            </div>
        @endif
    </div>

    {{-- Безопасность --}}
    <div class="mt-6 flex items-center justify-center gap-6 text-sm text-slate-500 dark:text-slate-400">
        <div class="flex items-center gap-2">
            <i class="fa-solid fa-lock text-green-500"></i>
            <span>Безопасная оплата</span>
        </div>
        <div class="flex items-center gap-2">
            <i class="fa-solid fa-shield-check text-green-500"></i>
            <span>Защита данных</span>
        </div>
    </div>

    {{-- Ссылка назад --}}
    <div class="mt-6 text-center">
        <a href="{{ route('subscription.index') }}" class="inline-flex items-center text-slate-600 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white transition-colors">
            <i class="fa-solid fa-arrow-left mr-2"></i>
            Вернуться к выбору тарифа
        </a>
    </div>
</div>

@endsection

@push('scripts')
<script>
(function() {
    var form = document.getElementById('payment-form');
    var btn = document.getElementById('payment-submit-btn');
    if (!form || !btn) return;

    var label = btn.querySelector('.payment-submit-btn__label');
    var spinner = btn.querySelector('.payment-submit-btn__spinner');

    form.addEventListener('submit', function() {
        btn.disabled = true;
        if (label) label.style.display = 'none';
        if (spinner) spinner.style.display = 'inline-flex';
    });

    function resetBtn() {
        btn.disabled = false;
        if (label) label.style.display = '';
        if (spinner) spinner.style.display = 'none';
    }
    window.addEventListener('pageshow', function(e) { if (e.persisted) resetBtn(); });
})();
</script>
@endpush
