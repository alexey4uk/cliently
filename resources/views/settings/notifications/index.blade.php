@extends('layouts.user')

@section('title', 'Настройки уведомлений - Cliently')
@section('page-title', 'Настройки уведомлений')
@section('page-description', 'Управление уведомлениями по Email и Telegram')

@push('breadcrumbs')
    <x-breadcrumbs :items="[['title' => 'Настройки', 'url' => route('profile.edit')], ['title' => 'Уведомления', 'url' => null]]" />
@endpush

@section('content')

@php
    $categoryNames = [
        'appointments' => 'Записи',
        'tickets' => 'Тикеты',
        'subscription' => 'Подписки',
        'business' => 'Команда и приглашения',
        'telegram' => 'Telegram',
    ];
    $categoryIcons = [
        'appointments' => 'fa-calendar-check',
        'tickets' => 'fa-ticket',
        'subscription' => 'fa-crown',
        'business' => 'fa-users',
        'telegram' => 'fa-brands fa-telegram',
    ];
    $categoriesOrder = ['appointments', 'tickets', 'subscription', 'business', 'telegram'];
@endphp

<script type="application/json" id="notification-settings-data">
{!! json_encode(compact('settings', 'channels', 'typesByCategory', 'categoryNames', 'categoryIcons', 'categoriesOrder')) !!}
</script>
<div x-data="notificationSettings(JSON.parse(document.getElementById('notification-settings-data').textContent))" class="max-w-4xl mx-auto">
    <!-- Заголовок -->
    <div class="mb-6">
        <h1 class="text-xl font-bold text-slate-900 dark:text-white">Уведомления</h1>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">Каналы доставки и типы событий. Уведомления всегда отображаются в колокольчике; здесь можно отключить типы и выбрать Email или Telegram.</p>
    </div>

    <!-- Toast -->
    <div x-show="showToast"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 translate-y-1"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         class="fixed bottom-4 right-4 z-50 bg-emerald-600 dark:bg-emerald-700 text-white text-sm px-4 py-2 rounded-lg shadow-lg flex items-center gap-2"
         style="display: none;">
        <i class="fa-solid fa-check"></i>
        <span x-text="toastMessage"></span>
    </div>

    <!-- Каналы доставки: компактно в одну карточку -->
    <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-5 mb-6">
        <h2 class="text-sm font-semibold text-slate-700 dark:text-slate-300 mb-4 flex items-center gap-2">
            <i class="fa-solid fa-paper-plane text-indigo-500"></i>
            Каналы доставки
        </h2>
        <div class="flex flex-col sm:flex-row sm:items-center gap-4 sm:gap-6">
            <div class="flex items-center gap-3 flex-1">
                <div class="h-9 w-9 rounded-lg bg-indigo-100 dark:bg-indigo-500/20 flex items-center justify-center shrink-0">
                    <i class="fa-brands fa-telegram text-indigo-600 dark:text-indigo-400"></i>
                </div>
                <div>
                    @if($user->isTelegramConnected())
                        <p class="text-sm font-medium text-slate-900 dark:text-white">Telegram привязан</p>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Уведомления приходят в мессенджер</p>
                    @else
                        <p class="text-sm font-medium text-slate-900 dark:text-white">Telegram не привязан</p>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Привяжите для получения в Telegram</p>
                    @endif
                </div>
            </div>
            <div class="flex items-center gap-3 shrink-0">
                @if($user->isTelegramConnected())
                    <button type="button"
                            @click="disconnectTelegram()"
                            class="px-3 py-1.5 text-sm font-medium text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-500/10 rounded-lg transition-colors">
                        Отвязать
                    </button>
                @elseif($telegramLink)
                    <a href="{{ $telegramLink }}"
                       target="_blank"
                       class="inline-flex items-center gap-2 px-3 py-1.5 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg transition-colors">
                        <i class="fa-brands fa-telegram"></i>
                        Привязать
                    </a>
                @else
                    <span class="text-sm text-slate-400">Ссылка недоступна</span>
                @endif
            </div>
            <div class="hidden sm:block w-px h-8 bg-slate-200 dark:bg-slate-700" aria-hidden="true"></div>
            <div class="flex items-center gap-3 flex-1 sm:flex-initial">
                <div class="h-9 w-9 rounded-lg bg-slate-100 dark:bg-slate-800 flex items-center justify-center shrink-0">
                    <i class="fa-solid fa-envelope text-slate-500 dark:text-slate-400"></i>
                </div>
                <p class="text-xs text-slate-500 dark:text-slate-400">Email — на адрес вашего аккаунта. Каналы для каждого типа ниже.</p>
            </div>
        </div>
    </div>

    <!-- Типы уведомлений: вкладки + таблица -->
    <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
        <div class="border-b border-slate-200 dark:border-slate-700 overflow-x-auto">
            <div class="flex gap-0 min-w-max px-2">
                <template x-for="catKey in categoriesOrder" :key="catKey">
                    <button type="button"
                            @click="activeTab = catKey"
                            :class="activeTab === catKey
                                ? 'text-indigo-600 dark:text-indigo-400 border-b-2 border-indigo-600 dark:border-indigo-400 bg-indigo-50/50 dark:bg-indigo-500/10'
                                : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-50 dark:hover:bg-slate-800/50 border-b-2 border-transparent'"
                            class="px-4 py-3 text-sm font-medium whitespace-nowrap transition-colors">
                        <i class="fa-solid mr-1.5 w-4 inline-block text-center" :class="categoryIcons[catKey] || 'fa-bell'"></i>
                        <span x-text="categoryNames[catKey] || catKey"></span>
                    </button>
                </template>
            </div>
        </div>

        <div class="p-4 sm:p-5">
            <template x-for="catKey in categoriesOrder" :key="'panel-'+catKey">
                <div x-show="activeTab === catKey"
                     x-cloak
                     class="space-y-1">
                    <template x-for="(name, type) in (typesByCategory[catKey] || {})" :key="type">
                        <div class="flex flex-wrap items-center gap-3 py-3 px-3 rounded-lg -mx-1 hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors"
                             :class="{ 'opacity-75': !getEnabledValue(type) }">
                            <div class="flex-1 min-w-0 text-sm font-medium text-slate-900 dark:text-white" x-text="name"></div>
                            <div class="flex items-center gap-4 flex-wrap">
                                <label class="inline-flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox"
                                           :checked="getEnabledValue(type)"
                                           @change="updateEnabled(type, $event.target.checked)"
                                           class="w-4 h-4 text-indigo-600 rounded border-slate-300 focus:ring-indigo-500 dark:bg-slate-700 dark:border-slate-600">
                                    <span class="text-xs text-slate-500 dark:text-slate-400">Получать</span>
                                </label>
                                <template x-for="(channelConfig, channelKey) in channels" :key="channelKey">
                                    <label x-show="!(type === 'telegram.disconnected' && channelKey === 'telegram')"
                                           class="inline-flex items-center gap-2 cursor-pointer">
                                        <input type="checkbox"
                                               :checked="getChannelValue(type, channelKey)"
                                               :disabled="!getEnabledValue(type)"
                                               @change="updateSetting(type, channelKey, $event.target.checked)"
                                               class="w-4 h-4 text-indigo-600 rounded border-slate-300 focus:ring-indigo-500 dark:bg-slate-700 dark:border-slate-600 disabled:opacity-50">
                                        <span class="text-xs text-slate-600 dark:text-slate-400" x-text="channelConfig.name || channelKey"></span>
                                    </label>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>
            </template>
        </div>
    </div>
</div>

@push('scripts')
<script>
function notificationSettings(initial) {
    const {
        settings: initialSettings,
        channels: initialChannels,
        typesByCategory,
        categoryNames,
        categoryIcons,
        categoriesOrder,
    } = initial;

    return {
        settings: initialSettings,
        channels: initialChannels,
        typesByCategory: typesByCategory || {},
        categoryNames: categoryNames || {},
        categoryIcons: categoryIcons || {},
        categoriesOrder: categoriesOrder || [],
        activeTab: (categoriesOrder && categoriesOrder[0]) || 'appointments',
        showToast: false,
        toastMessage: '',
        saving: {},

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
                const res = await fetch('{{ route("settings.notifications.update") }}', {
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
                    this.showToastMessage('Сохранено');
                } else {
                    this.settings[type].enabled = prev;
                    this.showToastMessage(data.message || 'Ошибка', 'error');
                }
            } catch (e) {
                console.error(e);
                this.settings[type].enabled = prev;
                this.showToastMessage('Ошибка сохранения', 'error');
            }
        },

        async updateSetting(type, channel, enabled) {
            if (!this.saving[type]) this.saving[type] = {};
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
                const res = await fetch('{{ route("settings.notifications.update") }}', {
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
                    this.showToastMessage('Сохранено');
                } else {
                    this.settings[type].channels[channel] = !enabled;
                    this.showToastMessage(data.message || 'Ошибка', 'error');
                }
            } catch (err) {
                console.error(err);
                this.settings[type].channels[channel] = !enabled;
                this.showToastMessage('Ошибка сохранения', 'error');
            } finally {
                this.saving[type][channel] = false;
            }
        },

        showToastMessage(message) {
            this.toastMessage = message;
            this.showToast = true;
            setTimeout(() => { this.showToast = false; }, 2500);
        },

        async disconnectTelegram() {
            if (!confirm('Отвязать Telegram? Уведомления в Telegram перестанут приходить.')) return;
            try {
                const response = await fetch('{{ route("settings.notifications.telegram.disconnect") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });
                const data = await response.json();
                if (response.ok && data.success) {
                    this.showToastMessage('Telegram отвязан');
                    setTimeout(() => window.location.reload(), 800);
                } else {
                    this.showToastMessage(data.message || 'Ошибка отвязки', 'error');
                }
            } catch (e) {
                console.error(e);
                this.showToastMessage('Ошибка отвязки', 'error');
            }
        }
    };
}
</script>
@endpush

@endsection
