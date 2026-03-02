@extends('layouts.panel')

@section('title', 'Настройки платежей - Cliently')

@push('breadcrumbs')
    <x-breadcrumbs :items="[['title' => 'Главная', 'url' => route('panel.index')], ['title' => 'Настройки платежей', 'url' => null]]" />
@endpush

@section('content')

<div x-data="paymentSettings()" class="max-w-6xl mx-auto">
    <!-- Заголовок страницы -->
    <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 p-6 mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Настройки платежей</h1>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Управление платёжными шлюзами и типами оплат</p>
            </div>
        </div>
    </div>

    <!-- Toast уведомление -->
    <div x-show="showToast"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         :class="toastType === 'success' ? 'bg-emerald-500' : 'bg-red-500'"
         class="fixed top-4 right-4 z-50 text-white px-6 py-3 rounded-lg shadow-lg flex items-center gap-3"
         style="display: none;">
        <i :class="toastType === 'success' ? 'fa-solid fa-check-circle' : 'fa-solid fa-exclamation-circle'"></i>
        <span x-text="toastMessage"></span>
    </div>

    <!-- Платёжные шлюзы -->
    <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden mb-6">
        <div class="px-6 py-4 bg-slate-50 dark:bg-slate-800/50 border-b border-slate-200 dark:border-slate-700">
            <h2 class="text-lg font-semibold text-slate-900 dark:text-white flex items-center gap-2">
                <i class="fa-solid fa-credit-card text-indigo-600 dark:text-indigo-400"></i>
                Платёжные шлюзы
            </h2>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Включите шлюзы, которые хотите использовать. Убедитесь, что API ключи настроены в .env</p>
        </div>
        <div class="divide-y divide-slate-200 dark:divide-slate-700">
            @foreach($gateways as $key => $gateway)
            <div class="p-6">
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <div class="flex items-center gap-4">
                        <div class="h-12 w-12 rounded-lg bg-slate-100 dark:bg-slate-800 flex items-center justify-center shrink-0">
                            @if($key === 'bepaid')
                                <i class="fa-solid fa-building-columns text-xl text-indigo-600 dark:text-indigo-400"></i>
                            @elseif($key === 'freekassa')
                                <i class="fa-solid fa-wallet text-xl text-emerald-600 dark:text-emerald-400"></i>
                            @else
                                <i class="fa-solid fa-credit-card text-xl text-slate-600 dark:text-slate-400"></i>
                            @endif
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <h3 class="text-base font-semibold text-slate-900 dark:text-white">{{ $gateway['display_name'] }}</h3>
                                @if(!$gateway['available'])
                                    <span class="px-2 py-0.5 text-xs font-medium bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 rounded">Недоступен</span>
                                @elseif(!$gateway['configured'])
                                    <span class="px-2 py-0.5 text-xs font-medium bg-amber-100 dark:bg-amber-500/20 text-amber-700 dark:text-amber-400 rounded">Не настроен</span>
                                @elseif($gateway['test_mode'])
                                    <span class="px-2 py-0.5 text-xs font-medium bg-blue-100 dark:bg-blue-500/20 text-blue-700 dark:text-blue-400 rounded">Тестовый режим</span>
                                @endif
                            </div>
                            <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">
                                Валюты: {{ implode(', ', $gateway['currencies']) }}
                                @if($gateway['supports_refund'])
                                    <span class="text-emerald-600 dark:text-emerald-400 ml-2">
                                        <i class="fa-solid fa-rotate-left text-xs"></i> Возвраты
                                    </span>
                                @endif
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center gap-4">
                        <!-- Тестовый режим -->
                        @if($gateway['configured'])
                        <label class="flex items-center gap-2 cursor-pointer">
                            <span class="text-sm text-slate-600 dark:text-slate-400">Тест</span>
                            <input type="checkbox"
                                   x-model="gateways['{{ $key }}'].test_mode"
                                   @change="updateGateway('{{ $key }}')"
                                   {{ !$gateway['available'] || !$gateway['configured'] ? 'disabled' : '' }}
                                   class="w-4 h-4 rounded border-slate-300 dark:border-slate-600 text-indigo-600 focus:ring-indigo-500 disabled:opacity-50">
                        </label>
                        @endif
                        <!-- Включён/Выключен -->
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox"
                                   x-model="gateways['{{ $key }}'].enabled"
                                   @change="updateGateway('{{ $key }}')"
                                   {{ !$gateway['available'] || !$gateway['configured'] ? 'disabled' : '' }}
                                   class="sr-only peer">
                            <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 dark:peer-focus:ring-indigo-800 rounded-full peer dark:bg-slate-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-slate-600 peer-checked:bg-indigo-600 peer-disabled:opacity-50 peer-disabled:cursor-not-allowed"></div>
                        </label>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Типы оплат -->
    <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
        <div class="px-6 py-4 bg-slate-50 dark:bg-slate-800/50 border-b border-slate-200 dark:border-slate-700">
            <h2 class="text-lg font-semibold text-slate-900 dark:text-white flex items-center gap-2">
                <i class="fa-solid fa-tags text-indigo-600 dark:text-indigo-400"></i>
                Типы оплат
            </h2>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Настройте типы оплат и выберите доступные шлюзы для каждого</p>
        </div>
        <div class="divide-y divide-slate-200 dark:divide-slate-700">
            @foreach($types as $key => $type)
            <div class="p-6" x-data="{ expanded: false }">
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <div class="flex items-center gap-4 flex-1 min-w-0">
                        <div class="h-12 w-12 rounded-lg bg-slate-100 dark:bg-slate-800 flex items-center justify-center shrink-0">
                            @if($key === 'subscription')
                                <i class="fa-solid fa-crown text-xl text-amber-600 dark:text-amber-400"></i>
                            @elseif($key === 'purchase')
                                <i class="fa-solid fa-shopping-cart text-xl text-emerald-600 dark:text-emerald-400"></i>
                            @elseif($key === 'donation')
                                <i class="fa-solid fa-heart text-xl text-pink-600 dark:text-pink-400"></i>
                            @elseif($key === 'balance')
                                <i class="fa-solid fa-wallet text-xl text-blue-600 dark:text-blue-400"></i>
                            @else
                                <i class="fa-solid fa-receipt text-xl text-slate-600 dark:text-slate-400"></i>
                            @endif
                        </div>
                        <div class="min-w-0">
                            <div class="flex items-center gap-2 flex-wrap">
                                <h3 class="text-base font-semibold text-slate-900 dark:text-white">{{ $type['display_name'] }}</h3>
                                @if(!$type['available'])
                                    <span class="px-2 py-0.5 text-xs font-medium bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 rounded">Не реализовано</span>
                                @elseif(empty($type['active_gateways']))
                                    <span class="px-2 py-0.5 text-xs font-medium bg-amber-100 dark:bg-amber-500/20 text-amber-700 dark:text-amber-400 rounded">Нет активных шлюзов</span>
                                @endif
                            </div>
                            <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">{{ $type['description'] }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <!-- Кнопка настроек -->
                        @if($type['available'])
                        <button @click="expanded = !expanded"
                                class="p-2 text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg transition-colors"
                                title="Настройки">
                            <i class="fa-solid fa-gear" :class="expanded && 'text-indigo-600 dark:text-indigo-400'"></i>
                        </button>
                        @endif
                        <!-- Включён/Выключен -->
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox"
                                   x-model="types['{{ $key }}'].enabled"
                                   @change="updateType('{{ $key }}')"
                                   {{ !$type['available'] ? 'disabled' : '' }}
                                   class="sr-only peer">
                            <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 dark:peer-focus:ring-indigo-800 rounded-full peer dark:bg-slate-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-slate-600 peer-checked:bg-indigo-600 peer-disabled:opacity-50 peer-disabled:cursor-not-allowed"></div>
                        </label>
                    </div>
                </div>

                <!-- Расширенные настройки -->
                <div x-show="expanded" x-collapse class="mt-4 pt-4 border-t border-slate-200 dark:border-slate-700">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Разрешённые шлюзы -->
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                                Разрешённые шлюзы
                            </label>
                            <div class="space-y-2">
                                @foreach($gateways as $gwKey => $gw)
                                <label class="flex items-center gap-3 p-3 rounded-lg border border-slate-200 dark:border-slate-700 transition-colors {{ $gw['configured'] ? 'hover:bg-slate-50 dark:hover:bg-slate-800/50 cursor-pointer' : 'opacity-60 cursor-not-allowed' }}"
                                       :class="types['{{ $key }}'].allowed_gateways.includes('{{ $gwKey }}') && 'border-indigo-300 dark:border-indigo-600 bg-indigo-50 dark:bg-indigo-900/20'">
                                    <input type="checkbox"
                                           value="{{ $gwKey }}"
                                           :checked="types['{{ $key }}'].allowed_gateways.includes('{{ $gwKey }}')"
                                           @change="toggleAllowedGateway('{{ $key }}', '{{ $gwKey }}')"
                                           {{ !$gw['configured'] ? 'disabled' : '' }}
                                           class="w-4 h-4 rounded border-slate-300 dark:border-slate-600 text-indigo-600 focus:ring-indigo-500 disabled:opacity-50">
                                    <div class="flex items-center gap-2">
                                        @if($gwKey === 'bepaid')
                                            <i class="fa-solid fa-building-columns text-indigo-600 dark:text-indigo-400"></i>
                                        @elseif($gwKey === 'freekassa')
                                            <i class="fa-solid fa-wallet text-emerald-600 dark:text-emerald-400"></i>
                                        @endif
                                        <span class="text-sm font-medium text-slate-900 dark:text-white">{{ $gw['display_name'] }}</span>
                                        @if(!$gw['configured'])
                                            <span class="text-xs text-amber-600 dark:text-amber-400">(не настроен)</span>
                                        @elseif(!$gw['enabled'])
                                            <span class="text-xs text-slate-500 dark:text-slate-400">(выключен)</span>
                                        @endif
                                    </div>
                                </label>
                                @endforeach
                            </div>
                        </div>

                        <!-- Шлюз по умолчанию -->
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                                Шлюз по умолчанию
                            </label>
                            <select x-model="types['{{ $key }}'].default_gateway"
                                    @change="updateType('{{ $key }}')"
                                    class="w-full px-4 py-2.5 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">Автоматически (первый доступный)</option>
                                @foreach($gateways as $gwKey => $gw)
                                @if($gw['configured'])
                                <option value="{{ $gwKey }}">{{ $gw['display_name'] }}</option>
                                @endif
                                @endforeach
                            </select>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                                Используется, если пользователь не выбрал шлюз
                            </p>

                            <!-- Лимиты сумм -->
                            <div class="grid grid-cols-2 gap-3 mt-4">
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">
                                        Мин. сумма
                                    </label>
                                    <input type="number"
                                           x-model="types['{{ $key }}'].min_amount"
                                           @change="updateType('{{ $key }}')"
                                           placeholder="0"
                                           min="0"
                                           step="0.01"
                                           class="w-full px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">
                                        Макс. сумма
                                    </label>
                                    <input type="number"
                                           x-model="types['{{ $key }}'].max_amount"
                                           @change="updateType('{{ $key }}')"
                                           placeholder="Без лимита"
                                           min="0"
                                           step="0.01"
                                           class="w-full px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Информация -->
    <div class="mt-6 p-4 bg-blue-50 dark:bg-blue-500/10 border border-blue-200 dark:border-blue-500/20 rounded-xl">
        <div class="flex gap-3">
            <i class="fa-solid fa-info-circle text-blue-600 dark:text-blue-400 mt-0.5"></i>
            <div class="text-sm text-blue-800 dark:text-blue-300">
                <p class="font-medium mb-1">Настройка API ключей</p>
                <p>API ключи платёжных шлюзов настраиваются в файле <code class="px-1.5 py-0.5 bg-blue-100 dark:bg-blue-500/20 rounded">.env</code>:</p>
                <ul class="mt-2 space-y-1 list-disc list-inside">
                    <li><strong>bePaid:</strong> BEPAID_SHOP_ID, BEPAID_SECRET_KEY</li>
                    <li><strong>FreeKassa:</strong> FREEKASSA_MERCHANT_ID, FREEKASSA_SECRET_1, FREEKASSA_SECRET_2, FREEKASSA_API_KEY</li>
                </ul>
            </div>
        </div>
    </div>
</div>

@endsection

@php
$gatewaysJs = collect($gateways)->mapWithKeys(fn($g, $k) => [$k => ['enabled' => $g['enabled'], 'test_mode' => $g['test_mode']]]);
$typesJs = collect($types)->mapWithKeys(fn($t, $k) => [$k => [
    'enabled' => $t['enabled'],
    'default_gateway' => $t['default_gateway'] ?? '',
    'allowed_gateways' => $t['allowed_gateways'] ?? [],
    'min_amount' => $t['min_amount'] ?? '',
    'max_amount' => $t['max_amount'] ?? '',
]]);
@endphp

@push('scripts')
<script>
function paymentSettings() {
    return {
        gateways: @json($gatewaysJs),
        types: @json($typesJs),
        showToast: false,
        toastMessage: '',
        toastType: 'success',

        async updateGateway(gateway) {
            try {
                const response = await fetch(`{{ url('panel/settings/payments/gateway') }}/${gateway}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify(this.gateways[gateway])
                });

                const data = await response.json();

                if (data.success) {
                    this.showNotification(data.message, 'success');
                } else {
                    this.showNotification(data.message || 'Ошибка сохранения', 'error');
                }
            } catch (error) {
                this.showNotification('Ошибка сохранения', 'error');
                console.error(error);
            }
        },

        async updateType(type) {
            try {
                const typeData = {
                    ...this.types[type],
                    min_amount: this.types[type].min_amount || null,
                    max_amount: this.types[type].max_amount || null,
                    default_gateway: this.types[type].default_gateway || null,
                };

                const response = await fetch(`{{ url('panel/settings/payments/type') }}/${type}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify(typeData)
                });

                const data = await response.json();

                if (data.success) {
                    this.showNotification(data.message, 'success');
                } else {
                    this.showNotification(data.message || 'Ошибка сохранения', 'error');
                }
            } catch (error) {
                this.showNotification('Ошибка сохранения', 'error');
                console.error(error);
            }
        },

        toggleAllowedGateway(type, gateway) {
            const index = this.types[type].allowed_gateways.indexOf(gateway);
            if (index > -1) {
                this.types[type].allowed_gateways.splice(index, 1);
            } else {
                this.types[type].allowed_gateways.push(gateway);
            }
            this.updateType(type);
        },

        showNotification(message, type = 'success') {
            this.toastMessage = message;
            this.toastType = type;
            this.showToast = true;
            setTimeout(() => { this.showToast = false; }, 3000);
        }
    }
}
</script>
@endpush
