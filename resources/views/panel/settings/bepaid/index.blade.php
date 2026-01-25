@extends('layouts.panel')

@section('title', 'Настройки bePaid')

@section('content')
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 pb-20 sm:pb-8">
        <!-- Breadcrumbs -->
        <nav class="mb-4 sm:mb-6" aria-label="Breadcrumb">
            <ol class="flex items-center space-x-1 sm:space-x-2 text-xs sm:text-sm text-slate-500 dark:text-slate-400 overflow-x-auto">
                <li class="flex-shrink-0">
                    <a href="{{ route('panel.index') }}" class="hover:text-slate-700 dark:hover:text-slate-300 transition-colors">
                        <i class="fa-solid fa-home sm:hidden"></i>
                        <span class="hidden sm:inline">Главная</span>
                    </a>
                </li>
                <li class="flex-shrink-0"><i class="fa-solid fa-chevron-right text-xs"></i></li>
                <li class="flex-shrink-0 text-slate-900 dark:text-white font-medium">Настройки bePaid</li>
            </ol>
        </nav>

        <!-- Page Header -->
        <div class="mb-6 sm:mb-8">
            <div class="flex items-start sm:items-center gap-3 sm:gap-4">
                <div class="h-10 w-10 sm:h-12 sm:w-12 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center shadow-sm flex-shrink-0">
                    <i class="fa-solid fa-credit-card text-white text-base sm:text-lg"></i>
                </div>
                <div class="min-w-0 flex-1">
                    <h1 class="text-xl sm:text-2xl lg:text-3xl font-bold text-slate-900 dark:text-white mb-1">Настройки bePaid</h1>
                    <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-400">Управление настройками платежного шлюза bePaid</p>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="mb-6 p-4 bg-emerald-50 dark:bg-emerald-500/20 border border-emerald-200 dark:border-emerald-500/30 rounded-lg flex items-center gap-3">
                <i class="fa-solid fa-check-circle text-emerald-600 dark:text-emerald-400"></i>
                <span class="text-emerald-800 dark:text-emerald-300">{{ session('success') }}</span>
            </div>
        @endif

        @if($errors->any())
            <div class="mb-6 p-4 bg-red-50 dark:bg-red-500/20 border border-red-200 dark:border-red-500/30 rounded-lg">
                <div class="flex items-center gap-3 mb-2">
                    <i class="fa-solid fa-exclamation-circle text-red-600 dark:text-red-400"></i>
                    <span class="font-medium text-red-800 dark:text-red-300">Ошибки валидации</span>
                </div>
                <ul class="list-disc list-inside text-sm text-red-700 dark:text-red-400 space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('panel.settings.bepaid.update') }}" id="bepaidSettingsForm">
            @csrf
            @method('PATCH')

            <div class="space-y-6">
                <!-- Основные настройки -->
                <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 bg-slate-50 dark:bg-slate-800/50 border-b border-slate-200 dark:border-slate-700">
                        <h2 class="text-lg font-semibold text-slate-900 dark:text-white flex items-center gap-2">
                            <i class="fa-solid fa-sliders-h text-indigo-600 dark:text-indigo-400"></i>
                            Основные настройки
                        </h2>
                    </div>
                    <div class="p-6 space-y-6">
                        <!-- Включить bePaid -->
                        <div class="flex items-start justify-between gap-4 p-4 rounded-lg bg-slate-50 dark:bg-slate-800/30 border border-slate-200 dark:border-slate-700">
                            <div class="flex-1">
                                <label class="block text-sm font-semibold text-slate-900 dark:text-white mb-1">
                                    Включить bePaid
                                </label>
                                <p class="text-sm text-slate-600 dark:text-slate-400">
                                    Активировать обработку платежей через bePaid
                                </p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="hidden" name="enabled" value="0">
                                <input type="checkbox" name="enabled" value="1" {{ old('enabled', $settings->enabled) ? 'checked' : '' }}
                                    class="sr-only peer">
                                <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 dark:peer-focus:ring-indigo-800 rounded-full peer dark:bg-slate-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-slate-600 peer-checked:bg-indigo-600"></div>
                            </label>
                        </div>

                        <!-- Режим работы -->
                        <div class="flex items-start justify-between gap-4 p-4 rounded-lg bg-slate-50 dark:bg-slate-800/30 border border-slate-200 dark:border-slate-700">
                            <div class="flex-1">
                                <label class="block text-sm font-semibold text-slate-900 dark:text-white mb-1">
                                    Тестовый режим
                                </label>
                                <p class="text-sm text-slate-600 dark:text-slate-400">
                                    Использовать тестовые данные для проверки интеграции
                                </p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="hidden" name="test_mode" value="0">
                                <input type="checkbox" name="test_mode" value="1" {{ old('test_mode', $settings->test_mode) ? 'checked' : '' }}
                                    class="sr-only peer">
                                <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 dark:peer-focus:ring-indigo-800 rounded-full peer dark:bg-slate-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-slate-600 peer-checked:bg-indigo-600"></div>
                            </label>
                        </div>

                        <!-- Webhook URL -->
                        <div>
                            <label for="webhook_url" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">
                                <i class="fa-solid fa-link text-xs text-slate-400 mr-1.5"></i>Webhook URL
                            </label>
                            <input
                                type="text"
                                id="webhook_url"
                                name="webhook_url"
                                value="{{ old('webhook_url', $settings->webhook_url) }}"
                                placeholder="/webhooks/bepaid"
                                class="w-full px-4 py-3 rounded-lg border border-slate-300 dark:border-slate-700 focus:ring-indigo-500 focus:border-indigo-500 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 transition-all text-sm sm:text-base"
                            />
                            <p class="mt-2 text-xs text-slate-500 dark:text-slate-400">
                                URL для получения уведомлений от bePaid (без домена)
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Тестовые настройки -->
                <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 bg-slate-50 dark:bg-slate-800/50 border-b border-slate-200 dark:border-slate-700">
                        <h2 class="text-lg font-semibold text-slate-900 dark:text-white flex items-center gap-2">
                            <i class="fa-solid fa-vial text-amber-600 dark:text-amber-400"></i>
                            Тестовые настройки
                        </h2>
                    </div>
                    <div class="p-6 space-y-5">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div>
                                <label for="test_shop_id" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">
                                    <i class="fa-solid fa-store text-xs text-slate-400 mr-1.5"></i>Shop ID
                                </label>
                                <input
                                    type="text"
                                    id="test_shop_id"
                                    name="test_shop_id"
                                    value="{{ old('test_shop_id', $settings->test_shop_id) }}"
                                    class="w-full px-4 py-3 rounded-lg border border-slate-300 dark:border-slate-700 focus:ring-indigo-500 focus:border-indigo-500 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 transition-all text-sm sm:text-base"
                                />
                            </div>

                            <div>
                                <label for="test_secret_key" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">
                                    <i class="fa-solid fa-key text-xs text-slate-400 mr-1.5"></i>Secret Key
                                </label>
                                <input
                                    type="password"
                                    id="test_secret_key"
                                    name="test_secret_key"
                                    value="{{ old('test_secret_key', $settings->test_secret_key) }}"
                                    class="w-full px-4 py-3 rounded-lg border border-slate-300 dark:border-slate-700 focus:ring-indigo-500 focus:border-indigo-500 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 transition-all text-sm sm:text-base"
                                />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div>
                                <label for="test_gateway_base" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">
                                    <i class="fa-solid fa-server text-xs text-slate-400 mr-1.5"></i>Gateway Base URL
                                </label>
                                <input
                                    type="url"
                                    id="test_gateway_base"
                                    name="test_gateway_base"
                                    value="{{ old('test_gateway_base', $settings->test_gateway_base) }}"
                                    placeholder="https://demo-gateway.begateway.com"
                                    class="w-full px-4 py-3 rounded-lg border border-slate-300 dark:border-slate-700 focus:ring-indigo-500 focus:border-indigo-500 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 transition-all text-sm sm:text-base"
                                />
                            </div>

                            <div>
                                <label for="test_checkout_base" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">
                                    <i class="fa-solid fa-cash-register text-xs text-slate-400 mr-1.5"></i>Checkout Base URL
                                </label>
                                <input
                                    type="url"
                                    id="test_checkout_base"
                                    name="test_checkout_base"
                                    value="{{ old('test_checkout_base', $settings->test_checkout_base) }}"
                                    placeholder="https://checkout.begateway.com"
                                    class="w-full px-4 py-3 rounded-lg border border-slate-300 dark:border-slate-700 focus:ring-indigo-500 focus:border-indigo-500 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 transition-all text-sm sm:text-base"
                                />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Продакшн настройки -->
                <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 bg-slate-50 dark:bg-slate-800/50 border-b border-slate-200 dark:border-slate-700">
                        <h2 class="text-lg font-semibold text-slate-900 dark:text-white flex items-center gap-2">
                            <i class="fa-solid fa-rocket text-emerald-600 dark:text-emerald-400"></i>
                            Продакшн настройки
                        </h2>
                    </div>
                    <div class="p-6 space-y-5">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div>
                                <label for="production_shop_id" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">
                                    <i class="fa-solid fa-store text-xs text-slate-400 mr-1.5"></i>Shop ID
                                </label>
                                <input
                                    type="text"
                                    id="production_shop_id"
                                    name="production_shop_id"
                                    value="{{ old('production_shop_id', $settings->production_shop_id) }}"
                                    class="w-full px-4 py-3 rounded-lg border border-slate-300 dark:border-slate-700 focus:ring-indigo-500 focus:border-indigo-500 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 transition-all text-sm sm:text-base"
                                />
                            </div>

                            <div>
                                <label for="production_secret_key" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">
                                    <i class="fa-solid fa-key text-xs text-slate-400 mr-1.5"></i>Secret Key
                                </label>
                                <input
                                    type="password"
                                    id="production_secret_key"
                                    name="production_secret_key"
                                    value="{{ old('production_secret_key', $settings->production_secret_key) }}"
                                    class="w-full px-4 py-3 rounded-lg border border-slate-300 dark:border-slate-700 focus:ring-indigo-500 focus:border-indigo-500 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 transition-all text-sm sm:text-base"
                                />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div>
                                <label for="production_gateway_base" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">
                                    <i class="fa-solid fa-server text-xs text-slate-400 mr-1.5"></i>Gateway Base URL
                                </label>
                                <input
                                    type="url"
                                    id="production_gateway_base"
                                    name="production_gateway_base"
                                    value="{{ old('production_gateway_base', $settings->production_gateway_base) }}"
                                    placeholder="https://gateway.begateway.com"
                                    class="w-full px-4 py-3 rounded-lg border border-slate-300 dark:border-slate-700 focus:ring-indigo-500 focus:border-indigo-500 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 transition-all text-sm sm:text-base"
                                />
                            </div>

                            <div>
                                <label for="production_checkout_base" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">
                                    <i class="fa-solid fa-cash-register text-xs text-slate-400 mr-1.5"></i>Checkout Base URL
                                </label>
                                <input
                                    type="url"
                                    id="production_checkout_base"
                                    name="production_checkout_base"
                                    value="{{ old('production_checkout_base', $settings->production_checkout_base) }}"
                                    placeholder="https://checkout.begateway.com"
                                    class="w-full px-4 py-3 rounded-lg border border-slate-300 dark:border-slate-700 focus:ring-indigo-500 focus:border-indigo-500 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 transition-all text-sm sm:text-base"
                                />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Кнопки действий -->
                <div class="flex items-center justify-end gap-4">
                    <button
                        type="button"
                        id="testConnectionBtn"
                        class="px-4 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-300 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors"
                    >
                        <i class="fa-solid fa-plug mr-2"></i>
                        Проверить подключение
                    </button>
                    <button
                        type="submit"
                        class="px-6 py-2.5 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg transition-colors shadow-sm"
                    >
                        <i class="fa-solid fa-save mr-2"></i>
                        Сохранить настройки
                    </button>
                </div>
            </div>
        </form>
    </div>

    <script>
        document.getElementById('testConnectionBtn')?.addEventListener('click', async function() {
            const btn = this;
            const originalText = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-2"></i>Проверка...';

            try {
                const response = await fetch('{{ route('panel.settings.bepaid.test-connection') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                    }
                });

                const data = await response.json();

                if (data.success) {
                    alert('Подключение успешно! Настройки корректны.');
                } else {
                    alert('Ошибка: ' + (data.message || 'Не удалось проверить подключение'));
                }
            } catch (error) {
                alert('Ошибка при проверке подключения: ' + error.message);
            } finally {
                btn.disabled = false;
                btn.innerHTML = originalText;
            }
        });
    </script>
@endsection
