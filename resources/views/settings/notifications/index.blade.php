@extends('layouts.user')

@section('title', 'Настройки уведомлений - Cliently')
@section('page-title', 'Настройки уведомлений')
@section('page-description', 'Управление уведомлениями по Email и Telegram')

@push('breadcrumbs')
    <x-breadcrumbs :items="[['title' => 'Настройки', 'url' => route('profile.edit')], ['title' => 'Уведомления', 'url' => null]]" />
@endpush

@section('content')

<div x-data="notificationSettings()" class="max-w-6xl mx-auto">
    <!-- Заголовок страницы -->
    <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 p-6 mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Настройки уведомлений</h1>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Выберите, какие уведомления вы хотите получать и через какие каналы</p>
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

    <!-- Привязка Telegram -->
    <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 p-6 mb-6">
        <div class="flex items-center gap-3 mb-6">
            <div class="h-10 w-10 rounded-lg bg-indigo-100 dark:bg-indigo-500/20 flex items-center justify-center">
                <i class="fa-brands fa-telegram text-indigo-600 dark:text-indigo-400"></i>
            </div>
            <div>
                <h2 class="text-lg font-semibold text-slate-900 dark:text-white">Привязка Telegram</h2>
                <p class="text-sm text-slate-500 dark:text-slate-400">Подключите Telegram для получения уведомлений</p>
            </div>
        </div>

        <div class="space-y-4">
            @if($user->isTelegramConnected())
                <div class="flex items-center justify-between p-4 bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/20 rounded-lg">
                    <div class="flex items-center gap-3">
                        <i class="fa-solid fa-check-circle text-emerald-600 dark:text-emerald-400"></i>
                        <div>
                            <p class="text-sm font-medium text-emerald-900 dark:text-emerald-100">Telegram привязан</p>
                            <p class="text-xs text-emerald-700 dark:text-emerald-300">Вы будете получать уведомления в Telegram</p>
                        </div>
                    </div>
                    <button 
                        @click="disconnectTelegram()"
                        class="px-4 py-2 text-sm font-medium text-red-700 dark:text-red-400 bg-red-50 dark:bg-red-500/10 border border-red-200 dark:border-red-500/20 rounded-lg hover:bg-red-100 dark:hover:bg-red-500/20 transition-colors">
                        Отвязать
                    </button>
                </div>
            @else
                <div class="p-4 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg">
                    <p class="text-sm text-slate-700 dark:text-slate-300 mb-4">Для получения уведомлений в Telegram привяжите свой аккаунт:</p>
                    @if($telegramLink)
                        <a 
                            href="{{ $telegramLink }}" 
                            target="_blank"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition-colors">
                            <i class="fa-brands fa-telegram"></i>
                            <span>Привязать Telegram</span>
                        </a>
                    @else
                        <p class="text-sm text-slate-500 dark:text-slate-400">Ссылка для привязки недоступна. Обратитесь к администратору.</p>
                    @endif
                </div>
            @endif
        </div>
    </div>

    <!-- Настройки по категориям -->
    @foreach($typesByCategory as $categoryKey => $categoryTypes)
        @php
            $categoryNames = [
                'appointments' => 'Записи',
                'tickets' => 'Тикеты',
                'admin' => 'Админские',
                'subscription' => 'Подписки',
            ];
            $categoryIcons = [
                'appointments' => 'fa-calendar-check',
                'tickets' => 'fa-ticket',
                'admin' => 'fa-shield-halved',
                'subscription' => 'fa-crown',
            ];
            $categoryName = $categoryNames[$categoryKey] ?? ucfirst($categoryKey);
            $categoryIcon = $categoryIcons[$categoryKey] ?? 'fa-bell';
        @endphp

        <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 p-6 mb-6">
            <div class="flex items-center gap-3 mb-6">
                <div class="h-10 w-10 rounded-lg bg-indigo-100 dark:bg-indigo-500/20 flex items-center justify-center">
                    <i class="fa-solid {{ $categoryIcon }} text-indigo-600 dark:text-indigo-400"></i>
                </div>
                <div>
                    <h2 class="text-lg font-semibold text-slate-900 dark:text-white">{{ $categoryName }}</h2>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Управление уведомлениями для {{ strtolower($categoryName) }}</p>
                </div>
            </div>

            <div class="space-y-4">
                @foreach($categoryTypes as $type => $name)
                    <div class="border border-slate-200 dark:border-slate-700 rounded-lg p-4 hover:border-indigo-300 dark:hover:border-indigo-600 transition-colors">
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex-1">
                                <h3 class="text-base font-medium text-slate-900 dark:text-white mb-2">{{ $name }}</h3>
                                <div class="flex flex-wrap gap-4 mt-3">
                                    @foreach($channels as $channelKey => $channelConfig)
                                        <label class="flex items-center gap-2 cursor-pointer group">
                                            <input 
                                                type="checkbox" 
                                                :checked="getChannelValue('{{ $type }}', '{{ $channelKey }}')"
                                                @change="updateSetting('{{ $type }}', '{{ $channelKey }}', $event.target.checked)"
                                                class="w-4 h-4 text-indigo-600 bg-slate-100 border-slate-300 rounded focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:ring-offset-slate-800 focus:ring-2 dark:bg-slate-700 dark:border-slate-600"
                                            >
                                            <span class="text-sm text-slate-700 dark:text-slate-300 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                                                <i class="fa-solid fa-{{ $channelConfig['icon'] ?? 'bell' }} mr-1"></i>
                                                {{ $channelConfig['name'] ?? ucfirst($channelKey) }}
                                            </span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endforeach
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

        init() {
            // Инициализация данных
        },

        getChannelValue(type, channel) {
            if (!this.settings[type]) {
                return true; // По умолчанию включено
            }
            return this.settings[type].channels[channel] ?? true;
        },

        async updateSetting(type, channel, enabled) {
            // Показываем индикатор сохранения
            if (!this.saving[type]) {
                this.saving[type] = {};
            }
            this.saving[type][channel] = true;

            // Обновляем локальное состояние
            if (!this.settings[type]) {
                this.settings[type] = {
                    name: '',
                    channels: {}
                };
            }
            if (!this.settings[type].channels) {
                this.settings[type].channels = {};
            }
            this.settings[type].channels[channel] = enabled;

            // Подготавливаем все каналы для отправки
            const allChannels = {};
            Object.keys(this.channels).forEach(ch => {
                allChannels[ch] = this.getChannelValue(type, ch);
            });

            try {
                const response = await fetch('{{ route("settings.notifications.update") }}', {
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

                const data = await response.json();

                if (response.ok && data.success) {
                    this.showToastMessage('Настройки успешно сохранены');
                } else {
                    this.showToastMessage(data.message || 'Ошибка при сохранении', 'error');
                    // Откатываем изменение
                    this.settings[type].channels[channel] = !enabled;
                }
            } catch (error) {
                console.error('Error updating setting:', error);
                this.showToastMessage('Ошибка при сохранении настроек', 'error');
                // Откатываем изменение
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
                    this.showToastMessage('Telegram успешно отвязан');
                    // Перезагружаем страницу для обновления статуса
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

@endsection
