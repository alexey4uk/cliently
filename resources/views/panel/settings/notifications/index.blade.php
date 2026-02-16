@extends('layouts.panel')

@section('title', 'Настройки уведомлений - Cliently')

@push('breadcrumbs')
    <x-breadcrumbs :items="[['title' => 'Главная', 'url' => route('panel.index')], ['title' => 'Настройки уведомлений', 'url' => null]]" />
@endpush

@section('content')

<div x-data="notificationSettings()" class="max-w-6xl mx-auto">
    <!-- Заголовок страницы -->
    <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 p-6 mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Настройки уведомлений</h1>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Отключите типы уведомлений, которые не нужны (в т.ч. в колокольчике), и выберите каналы — Email и Telegram</p>
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
         class="fixed top-4 right-4 z-50 bg-emerald-500 text-white px-6 py-3 rounded-lg shadow-lg flex items-center gap-3"
         style="display: none;">
        <i class="fa-solid fa-check-circle"></i>
        <span x-text="toastMessage"></span>
    </div>

    <!-- Каналы доставки -->
    <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden mb-6">
        <div class="px-6 py-4 bg-slate-50 dark:bg-slate-800/50 border-b border-slate-200 dark:border-slate-700">
            <h2 class="text-lg font-semibold text-slate-900 dark:text-white flex items-center gap-2">
                <i class="fa-solid fa-paper-plane text-indigo-600 dark:text-indigo-400"></i>
                Каналы доставки
            </h2>
        </div>
        <div class="p-6 space-y-4">
            <div class="flex flex-wrap items-center justify-between gap-4 p-4 rounded-lg bg-slate-50 dark:bg-slate-800/30 border border-slate-200 dark:border-slate-700">
                <div class="flex items-center gap-3">
                    <div class="h-10 w-10 rounded-lg bg-indigo-100 dark:bg-indigo-500/20 flex items-center justify-center shrink-0">
                        <i class="fa-brands fa-telegram text-indigo-600 dark:text-indigo-400"></i>
                    </div>
                    <div>
                        @if($user->isTelegramConnected())
                            <p class="text-sm font-medium text-slate-900 dark:text-white">Telegram привязан</p>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Уведомления приходят в Telegram</p>
                        @else
                            <p class="text-sm font-medium text-slate-900 dark:text-white">Telegram не привязан</p>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Привяжите для получения уведомлений</p>
                        @endif
                    </div>
                </div>
                @if($user->isTelegramConnected())
                    <button type="button"
                            @click="disconnectTelegram()"
                            class="px-4 py-2 text-sm font-medium text-red-700 dark:text-red-400 bg-red-50 dark:bg-red-500/10 border border-red-200 dark:border-red-500/20 rounded-lg hover:bg-red-100 dark:hover:bg-red-500/20 transition-colors">
                        Отвязать
                    </button>
                @elseif($telegramLink)
                    <a href="{{ $telegramLink }}"
                       target="_blank"
                       class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg transition-colors">
                        <i class="fa-brands fa-telegram"></i>
                        Привязать Telegram
                    </a>
                @else
                    <p class="text-sm text-slate-500 dark:text-slate-400">Ссылка для привязки недоступна</p>
                @endif
            </div>

            <div class="p-4 rounded-lg bg-slate-50 dark:bg-slate-800/30 border border-slate-200 dark:border-slate-700">
                <div class="flex items-start gap-3">
                    <div class="h-9 w-9 rounded-lg bg-slate-200 dark:bg-slate-700 flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-envelope text-slate-600 dark:text-slate-400"></i>
                    </div>
                    <p class="text-sm text-slate-600 dark:text-slate-400">
                        Уведомления на email отправляются на адрес вашего аккаунта. Включение или отключение каналов для каждого типа — ниже.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Типы уведомлений (аккордеон) -->
    <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
        <div class="px-6 py-4 bg-slate-50 dark:bg-slate-800/50 border-b border-slate-200 dark:border-slate-700">
            <h2 class="text-lg font-semibold text-slate-900 dark:text-white flex items-center gap-2">
                <i class="fa-solid fa-bell text-indigo-600 dark:text-indigo-400"></i>
                Типы уведомлений
            </h2>
        </div>
        <div class="divide-y divide-slate-200 dark:divide-slate-700">
            @foreach($typesByCategory as $categoryKey => $categoryTypes)
                @php
                    $categoryName = $categoryNames[$categoryKey] ?? ucfirst($categoryKey);
                    $categoryIcon = $categoryIcons[$categoryKey] ?? 'fa-bell';
                @endphp
                <div class="border-0">
                    <button type="button"
                            @click="openCategory['{{ $categoryKey }}'] = !openCategory['{{ $categoryKey }}']"
                            class="w-full flex items-center justify-between gap-4 px-6 py-4 text-left hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors">
                        <div class="flex items-center gap-3">
                            <div class="h-9 w-9 rounded-lg bg-indigo-100 dark:bg-indigo-500/20 flex items-center justify-center shrink-0">
                                <i class="fa-solid {{ $categoryIcon }} text-indigo-600 dark:text-indigo-400"></i>
                            </div>
                            <span class="font-medium text-slate-900 dark:text-white">{{ $categoryName }}</span>
                        </div>
                        <i class="fa-solid fa-chevron-down text-slate-400 transition-transform duration-200"
                           :class="{ 'rotate-180': openCategory['{{ $categoryKey }}'] }"></i>
                    </button>
                    <div x-show="openCategory['{{ $categoryKey }}']"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0"
                         x-transition:enter-end="opacity-100"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100"
                         x-transition:leave-end="opacity-0"
                         x-cloak
                         class="px-6 pb-6">
                        <div class="space-y-4 pt-2">
                            @foreach($categoryTypes as $type => $name)
                                <div class="rounded-lg border border-slate-200 dark:border-slate-700 p-4 transition-colors"
                                     :class="{ 'opacity-75': !getEnabledValue('{{ $type }}') }">
                                    <div class="flex flex-wrap items-start justify-between gap-4">
                                        <div class="flex-1 min-w-0">
                                            <h3 class="text-sm font-semibold text-slate-900 dark:text-white">{{ $name }}</h3>
                                        </div>
                                        <label class="relative inline-flex items-center cursor-pointer shrink-0">
                                            <input type="checkbox"
                                                   :checked="getEnabledValue('{{ $type }}')"
                                                   @change="updateEnabled('{{ $type }}', $event.target.checked)"
                                                   class="sr-only peer">
                                            <div class="w-11 h-6 bg-slate-300 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 dark:peer-focus:ring-indigo-800 rounded-full peer dark:bg-slate-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-slate-600 peer-checked:bg-indigo-600"></div>
                                            <span class="ml-2 text-sm text-slate-600 dark:text-slate-400">Получать</span>
                                        </label>
                                    </div>
                                    <div x-show="getEnabledValue('{{ $type }}')"
                                         x-cloak
                                         class="flex flex-wrap gap-4 mt-4 pt-4 border-t border-slate-200 dark:border-slate-700">
                                        @foreach($channels as $channelKey => $channelConfig)
                                            <label class="flex items-center gap-2 cursor-pointer group">
                                                <input type="checkbox"
                                                       :checked="getChannelValue('{{ $type }}', '{{ $channelKey }}')"
                                                       :disabled="!getEnabledValue('{{ $type }}')"
                                                       @change="updateSetting('{{ $type }}', '{{ $channelKey }}', $event.target.checked)"
                                                       class="w-4 h-4 text-indigo-600 bg-slate-100 border-slate-300 rounded focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:ring-offset-slate-800 focus:ring-2 dark:bg-slate-700 dark:border-slate-600 disabled:opacity-50 disabled:cursor-not-allowed">
                                                <span class="text-sm text-slate-700 dark:text-slate-300 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                                                    <i class="fa-solid fa-{{ $channelConfig['icon'] ?? 'bell' }} mr-1"></i>
                                                    {{ $channelConfig['name'] ?? ucfirst($channelKey) }}
                                                </span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

@push('scripts')
<script>
function notificationSettings() {
    return {
        settings: @json($settings),
        channels: @json($channels),
        showToast: false,
        toastMessage: '',
        saving: {},
        openCategory: @json(array_fill_keys(array_keys($typesByCategory), false)),

        init() {
            // Инициализация данных
        },

        getEnabledValue(type) {
            if (!this.settings[type]) return true;
            return this.settings[type].enabled !== false;
        },

        getChannelValue(type, channel) {
            if (!this.settings[type]) return true;
            return this.settings[type].channels[channel] ?? true;
        },

        async updateEnabled(type, enabled) {
            if (!this.settings[type]) {
                this.settings[type] = { name: '', enabled: true, channels: {} };
            }
            if (!this.settings[type].channels) this.settings[type].channels = {};
            const prev = this.settings[type].enabled;
            this.settings[type].enabled = enabled;

            const allChannels = {};
            Object.keys(this.channels).forEach(ch => {
                allChannels[ch] = this.getChannelValue(type, ch);
            });

            try {
                const res = await fetch('{{ route("panel.settings.notifications.update") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        notification_type: type,
                        enabled: enabled,
                        channels: allChannels
                    })
                });
                const data = await res.json();
                if (res.ok && data.success) {
                    this.showToastMessage('Настройки сохранены');
                } else {
                    this.settings[type].enabled = prev;
                    this.showToastMessage(data.message || 'Ошибка при сохранении', 'error');
                }
            } catch (e) {
                console.error('Error updating enabled:', e);
                this.settings[type].enabled = prev;
                this.showToastMessage('Ошибка при сохранении настроек', 'error');
            }
        },

        async updateSetting(type, channel, enabled) {
            if (!this.saving[type]) {
                this.saving[type] = {};
            }
            this.saving[type][channel] = true;

            if (!this.settings[type]) {
                this.settings[type] = { name: '', enabled: true, channels: {} };
            }
            if (!this.settings[type].channels) this.settings[type].channels = {};
            this.settings[type].channels[channel] = enabled;

            const allChannels = {};
            Object.keys(this.channels).forEach(ch => {
                allChannels[ch] = this.getChannelValue(type, ch);
            });

            try {
                const res = await fetch('{{ route("panel.settings.notifications.update") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        notification_type: type,
                        channels: allChannels
                    })
                });

                const data = await res.json();

                if (res.ok && data.success) {
                    this.showToastMessage('Настройки успешно сохранены');
                } else {
                    this.showToastMessage(data.message || 'Ошибка при сохранении', 'error');
                    this.settings[type].channels[channel] = !enabled;
                }
            } catch (error) {
                console.error('Error updating setting:', error);
                this.showToastMessage('Ошибка при сохранении настроек', 'error');
                this.settings[type].channels[channel] = !enabled;
            } finally {
                this.saving[type][channel] = false;
            }
        },

        showToastMessage(message, type = 'success') {
            this.toastMessage = message;
            this.showToast = true;
            setTimeout(() => {
                this.showToast = false;
            }, 3000);
        },

        async disconnectTelegram() {
            if (!confirm('Вы уверены, что хотите отвязать Telegram? Вы перестанете получать уведомления в Telegram.')) {
                return;
            }

            try {
                const response = await fetch('{{ route("panel.settings.notifications.telegram.disconnect") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    this.showToastMessage('Telegram успешно отвязан');
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                } else {
                    this.showToastMessage(data.message || 'Ошибка при отвязке Telegram', 'error');
                }
            } catch (error) {
                console.error('Error disconnecting telegram:', error);
                this.showToastMessage('Ошибка при отвязке Telegram', 'error');
            }
        }
    }
}
</script>
@endpush

<style>
    [x-cloak] { display: none !important; }
</style>

@endsection
