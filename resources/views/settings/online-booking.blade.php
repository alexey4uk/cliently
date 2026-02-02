@extends('layouts.user')

@section('title', 'Онлайн-запись - Cliently')
@section('page-title', 'Онлайн-запись')
@section('page-description', 'Управление доступом клиентов к записи')

@push('breadcrumbs')
    <x-breadcrumbs :items="[
        ['title' => 'Настройки', 'url' => route('settings.index')],
        ['title' => 'Онлайн-запись', 'url' => null],
    ]" />
@endpush

@section('content')

<div class="max-w-4xl mx-auto px-4 sm:px-6 pb-6">
@php
    $hasSlug = !empty($business->slug);
    $fullUrl = $hasSlug ? route('public.appointments.show', ['slug' => $business->slug]) : '';
    $displayUrl = $fullUrl ? str_replace(['http://', 'https://', 'www.'], '', $fullUrl) : '';
    $telegramUrl = $bot && $hasSlug ? 'https://t.me/' . $bot->name . '?start=' . $business->slug : null;
    $hasTelegramQr = $bot && $telegramUrl && ($telegramBotEnabled ?? false);

    $webQrUrl = $hasSlug ? route('settings.online-booking.qr', ['type' => 'web', 'size' => 200]) : '';
    $webQrModalUrl = $hasSlug ? route('settings.online-booking.qr', ['type' => 'web', 'size' => 400]) : '';
    $webQrDownloadUrl = $hasSlug ? route('settings.online-booking.qr', ['type' => 'web', 'size' => 200, 'download' => 1]) : '';
    $telegramQrUrl = $hasTelegramQr ? route('settings.online-booking.qr', ['type' => 'telegram', 'size' => 200]) : '';
    $telegramQrModalUrl = $hasTelegramQr ? route('settings.online-booking.qr', ['type' => 'telegram', 'size' => 400]) : '';
    $telegramQrDownloadUrl = $hasTelegramQr ? route('settings.online-booking.qr', ['type' => 'telegram', 'size' => 200, 'download' => 1]) : '';

    $isEnabled = $business->online_booking_enabled ?? true;
@endphp

<div x-data="{
    isEnabled: {{ $isEnabled ? 'true' : 'false' }},
    isLoading: false,
    toggleError: '',
    copiedWeb: false,
    copiedTelegram: false,
    showQrModal: false,
    qrImageUrl: '',
    qrTitle: '',
    isDownloading: false,
    webQrModalUrl: '{{ $webQrModalUrl }}',
    telegramQrModalUrl: '{{ $telegramQrModalUrl }}',
    webQrDownloadUrl: '{{ $webQrDownloadUrl }}',
    telegramQrDownloadUrl: '{{ $telegramQrDownloadUrl }}',

    async toggleBooking() {
        if (this.isLoading) return;
        this.toggleError = '';
        this.isLoading = true;
        const newValue = !this.isEnabled;
        try {
            const csrfToken = document.querySelector('meta[name=csrf-token]');
            if (!csrfToken) {
                throw new Error('CSRF токен не найден');
            }
            const formData = new FormData();
            formData.append('online_booking_enabled', newValue ? '1' : '0');
            formData.append('_token', csrfToken.content);
            formData.append('_method', 'PATCH');
            const response = await fetch('{{ route('settings.online-booking.update') }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken.content,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: formData
            });
            if (!response.ok) {
                let errorMessage = 'Ошибка при сохранении';
                try {
                    const errorData = await response.json();
                    errorMessage = errorData.message || errorMessage;
                } catch (e) {
                    const errorText = await response.text();
                    console.error('Error response:', errorText);
                }
                throw new Error(errorMessage);
            }
            const data = await response.json();
            if (data.success) {
                this.isEnabled = data.online_booking_enabled;
            } else {
                throw new Error(data.message || 'Ошибка при сохранении');
            }
        } catch (error) {
            console.error('Ошибка при изменении состояния:', error);
            this.toggleError = error.message || 'Не удалось изменить состояние онлайн-записи. Попробуйте ещё раз.';
        } finally {
            this.isLoading = false;
        }
    },

    async copyUrl(type) {
        let url = type === 'web' ? '{{ $fullUrl }}' : '{{ $telegramUrl }}';
        if (!url) return;
        try {
            await navigator.clipboard.writeText(url);
        } catch (err) {
            const textArea = document.createElement('textarea');
            textArea.value = url;
            document.body.appendChild(textArea);
            textArea.select();
            document.execCommand('copy');
            document.body.removeChild(textArea);
        }
        if (type === 'web') {
            this.copiedWeb = true;
            setTimeout(() => this.copiedWeb = false, 2000);
        } else {
            this.copiedTelegram = true;
            setTimeout(() => this.copiedTelegram = false, 2000);
        }
    },

    openQrModal(type) {
        const url = type === 'web' ? this.webQrModalUrl : this.telegramQrModalUrl;
        if (!url) return;
        this.qrImageUrl = url;
        this.qrTitle = type === 'web' ? 'QR-код веб-записи' : 'QR-код Telegram';
        this.showQrModal = true;
    },

    async downloadQr(url, filename) {
        if (this.isDownloading || !url) return;
        this.isDownloading = true;
        try {
            const response = await fetch(url);
            const blob = await response.blob();
            const downloadUrl = window.URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.href = downloadUrl;
            link.download = filename;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            window.URL.revokeObjectURL(downloadUrl);
        } catch (error) {
            console.error('Ошибка скачивания:', error);
            window.open(url, '_blank');
        } finally {
            this.isDownloading = false;
        }
    },

    downloadWebQr() {
        this.downloadQr(this.webQrDownloadUrl, 'qr-zapisi-{{ $business->slug ?? 'business' }}.png');
    },

    downloadTelegramQr() {
        this.downloadQr(this.telegramQrDownloadUrl, 'qr-telegram-{{ $business->slug ?? 'business' }}.png');
    },

    downloadModalQr() {
        const filename = 'qr-{{ $business->slug ?? 'business' }}-' + new Date().getTime() + '.png';
        this.downloadQr(this.qrImageUrl, filename);
    }
}">

    <!-- Заголовок страницы -->
    <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 p-4 sm:p-6 mb-4 sm:mb-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="min-w-0">
                <h1 class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-white">Онлайн-запись</h1>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Управление доступом к записи для клиентов</p>
            </div>
            <div class="flex flex-col gap-2 w-full sm:w-auto sm:items-end">
                <div class="flex items-center justify-between sm:justify-end gap-3">
                    <div class="text-left sm:text-right min-w-0">
                        <div class="text-sm font-medium text-slate-900 dark:text-white" x-text="isEnabled ? 'Включена' : 'Выключена'"></div>
                        <div class="text-xs text-slate-500 dark:text-slate-400" x-text="isEnabled ? 'Клиенты могут записываться' : 'Запись временно недоступна'"></div>
                    </div>
                    <div class="min-h-[44px] min-w-[44px] flex items-center justify-center shrink-0">
                        <button type="button"
                                role="switch"
                                :aria-checked="isEnabled"
                                aria-label="Включить или выключить онлайн-запись"
                                @click="toggleBooking()"
                                :disabled="isLoading"
                                :class="isEnabled ? 'bg-indigo-600' : 'bg-slate-300 dark:bg-slate-700'"
                                class="relative inline-flex h-7 w-12 items-center rounded-full transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed">
                            <span :class="isEnabled ? 'translate-x-6' : 'translate-x-1'"
                                  class="inline-block h-5 w-5 transform rounded-full bg-white transition-transform shadow-sm"></span>
                        </button>
                    </div>
                </div>
                <p x-show="toggleError" x-text="toggleError" x-cloak
                   class="text-sm text-rose-600 dark:text-rose-400"
                   style="display: none;"></p>
            </div>
        </div>
    </div>

    <!-- Контент (скрывается при выключенной записи) -->
    <template x-if="isEnabled && {{ $hasSlug ? 'true' : 'false' }}">
        <div class="space-y-6">
            <!-- Веб-запись -->
            <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 p-4 sm:p-6">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between mb-4 sm:mb-6">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="h-12 w-12 shrink-0 rounded-lg bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center">
                            <i class="fa-solid fa-globe text-white text-lg"></i>
                        </div>
                        <div class="min-w-0">
                            <h2 class="text-lg font-semibold text-slate-900 dark:text-white">Веб-запись</h2>
                            <p class="text-sm text-slate-500 dark:text-slate-400">Запись через веб-страницу</p>
                        </div>
                    </div>
                    <a href="{{ $fullUrl }}"
                       target="_blank"
                       rel="noopener"
                       class="w-full sm:w-auto min-h-[44px] inline-flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-medium text-indigo-700 dark:text-indigo-300 bg-indigo-50 dark:bg-indigo-900/30 hover:bg-indigo-100 dark:hover:bg-indigo-900/50 rounded-lg transition-colors">
                        <i class="fa-solid fa-external-link text-sm"></i>
                        <span>Открыть</span>
                    </a>
                </div>

                <div class="space-y-6">
                    <!-- QR-код -->
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <label class="text-sm font-medium text-slate-700 dark:text-slate-300">QR-код</label>
                            <span class="text-xs text-slate-500 dark:text-slate-400">Наведите камеру</span>
                        </div>

                        <div class="bg-slate-50 dark:bg-slate-800/50 rounded-xl p-4 sm:p-6">
                            <div class="flex flex-col items-center gap-4">
                                <div class="cursor-pointer active:scale-105 sm:hover:scale-105 transition-transform duration-200 touch-manipulation"
                                     @click="openQrModal('web')">
                                    <img src="{{ $webQrUrl }}"
                                         alt="QR-код веб-записи"
                                         class="w-40 h-40 sm:w-48 sm:h-48 rounded-lg shadow-sm border-2 border-slate-200 dark:border-slate-700">
                                </div>

                                <div class="flex gap-3 w-full sm:max-w-xs">
                                    <button @click="downloadWebQr()"
                                            :disabled="isDownloading"
                                            class="flex-1 min-h-[44px] inline-flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed touch-manipulation">
                                        <i class="fa-solid fa-download text-sm"></i>
                                        <span x-text="isDownloading ? 'Скачивается...' : 'Скачать'"></span>
                                    </button>
                                    <button @click="openQrModal('web')"
                                            class="flex-1 min-h-[44px] inline-flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-300 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700 rounded-lg transition-colors touch-manipulation">
                                        <i class="fa-solid fa-expand text-sm"></i>
                                        <span>Увеличить</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Ссылка -->
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-3">Ссылка для записи</label>
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center p-4 bg-slate-50 dark:bg-slate-800/50 rounded-lg border border-slate-200 dark:border-slate-700">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm text-slate-700 dark:text-slate-300 font-mono truncate break-all">
                                    {{ $displayUrl }}
                                </p>
                            </div>
                            <button type="button"
                                    @click="copyUrl('web')"
                                    :class="copiedWeb ? 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300' : 'bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300'"
                                    class="w-full sm:w-auto min-h-[44px] px-4 py-2.5 text-sm font-medium rounded-lg border border-slate-300 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors touch-manipulation shrink-0">
                                <i :class="copiedWeb ? 'fa-solid fa-check' : 'fa-solid fa-copy'" class="mr-2"></i>
                                <span x-text="copiedWeb ? 'Скопировано' : 'Копировать'"></span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Telegram запись -->
            @if ($hasTelegramQr)
                <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 p-4 sm:p-6">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between mb-4 sm:mb-6">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="h-12 w-12 shrink-0 rounded-lg bg-gradient-to-br from-sky-500 to-teal-600 flex items-center justify-center">
                                <i class="fa-brands fa-telegram text-white text-lg"></i>
                            </div>
                            <div class="min-w-0">
                                <h2 class="text-lg font-semibold text-slate-900 dark:text-white">Telegram бот</h2>
                                <p class="text-sm text-slate-500 dark:text-slate-400">Запись через мессенджер</p>
                            </div>
                        </div>
                        <a href="{{ $telegramUrl }}"
                           target="_blank"
                           rel="noopener"
                           class="w-full sm:w-auto min-h-[44px] inline-flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-medium text-sky-700 dark:text-sky-300 bg-sky-50 dark:bg-sky-900/30 hover:bg-sky-100 dark:hover:bg-sky-900/50 rounded-lg transition-colors">
                            <i class="fa-brands fa-telegram text-sm"></i>
                            <span>Открыть</span>
                        </a>
                    </div>

                    <div class="space-y-6">
                        <!-- QR-код -->
                        <div>
                            <div class="flex items-center justify-between mb-3">
                                <label class="text-sm font-medium text-slate-700 dark:text-slate-300">QR-код бота</label>
                                <span class="text-xs text-slate-500 dark:text-slate-400">Наведите камеру</span>
                            </div>

                            <div class="bg-slate-50 dark:bg-slate-800/50 rounded-xl p-4 sm:p-6">
                                <div class="flex flex-col items-center gap-4">
                                    <div class="cursor-pointer active:scale-105 sm:hover:scale-105 transition-transform duration-200 touch-manipulation"
                                         @click="openQrModal('telegram')">
                                        <img src="{{ $telegramQrUrl }}"
                                             alt="QR-код Telegram"
                                             class="w-40 h-40 sm:w-48 sm:h-48 rounded-lg shadow-sm border-2 border-slate-200 dark:border-slate-700">
                                    </div>

                                    <div class="flex gap-3 w-full sm:max-w-xs">
                                        <button @click="downloadTelegramQr()"
                                                :disabled="isDownloading"
                                                class="flex-1 min-h-[44px] inline-flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-medium text-white bg-sky-600 hover:bg-sky-700 rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed touch-manipulation">
                                            <i class="fa-solid fa-download text-sm"></i>
                                            <span x-text="isDownloading ? 'Скачивается...' : 'Скачать'"></span>
                                        </button>
                                        <button @click="openQrModal('telegram')"
                                                class="flex-1 min-h-[44px] inline-flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-300 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700 rounded-lg transition-colors touch-manipulation">
                                            <i class="fa-solid fa-expand text-sm"></i>
                                            <span>Увеличить</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Ссылка -->
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-3">Ссылка на бота</label>
                            <div class="flex flex-col gap-3 sm:flex-row sm:items-center p-4 bg-slate-50 dark:bg-slate-800/50 rounded-lg border border-slate-200 dark:border-slate-700">
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm text-slate-700 dark:text-slate-300 font-mono truncate break-all">
                                        t.me/{{ $bot->name }}?start={{ $business->slug }}
                                    </p>
                                </div>
                                <button type="button"
                                        @click="copyUrl('telegram')"
                                        :class="copiedTelegram ? 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300' : 'bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300'"
                                        class="w-full sm:w-auto min-h-[44px] px-4 py-2.5 text-sm font-medium rounded-lg border border-slate-300 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors touch-manipulation shrink-0">
                                    <i :class="copiedTelegram ? 'fa-solid fa-check' : 'fa-solid fa-copy'" class="mr-2"></i>
                                    <span x-text="copiedTelegram ? 'Скопировано' : 'Копировать'"></span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Советы по использованию -->
            <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 p-4 sm:p-6">
                <div class="flex items-center gap-3 mb-4">
                    <div class="h-10 w-10 rounded-lg bg-amber-100 dark:bg-amber-500/20 flex items-center justify-center">
                        <i class="fa-solid fa-lightbulb text-amber-600 dark:text-amber-400"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Идеи для размещения QR-кодов</h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="flex items-start gap-3">
                        <i class="fa-solid fa-circle-check text-emerald-500 text-sm mt-0.5"></i>
                        <span class="text-sm text-slate-700 dark:text-slate-300">На визитках, чеках и печатных материалах</span>
                    </div>
                    <div class="flex items-start gap-3">
                        <i class="fa-solid fa-circle-check text-emerald-500 text-sm mt-0.5"></i>
                        <span class="text-sm text-slate-700 dark:text-slate-300">На сайте, в соцсетях и email-подписи</span>
                    </div>
                    <div class="flex items-start gap-3">
                        <i class="fa-solid fa-circle-check text-emerald-500 text-sm mt-0.5"></i>
                        <span class="text-sm text-slate-700 dark:text-slate-300">В офисе, на ресепшене, в зоне ожидания</span>
                    </div>
                    <div class="flex items-start gap-3">
                        <i class="fa-solid fa-circle-check text-emerald-500 text-sm mt-0.5"></i>
                        <span class="text-sm text-slate-700 dark:text-slate-300">В мессенджерах при общении с клиентами</span>
                    </div>
                </div>
            </div>
        </div>
    </template>

    <!-- Сообщение если запись отключена -->
    <template x-if="!isEnabled">
        <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 p-6 sm:p-12 text-center">
            <div class="max-w-md mx-auto">
                <div class="h-20 w-20 rounded-xl bg-amber-100 dark:bg-amber-500/20 flex items-center justify-center mx-auto mb-4">
                    <i class="fa-solid fa-toggle-off text-amber-600 dark:text-amber-400 text-2xl"></i>
                </div>
                <h3 class="text-xl font-semibold text-slate-900 dark:text-white mb-2">
                    Онлайн-запись отключена
                </h3>
                <p class="text-sm text-slate-600 dark:text-slate-400 mb-6">
                    Клиенты не смогут записываться через веб-форму или Telegram бота. Включите онлайн-запись, чтобы возобновить прием записей.
                </p>
            </div>
        </div>
    </template>

    <!-- Сообщение если slug не настроен -->
    <template x-if="isEnabled && !{{ $hasSlug ? 'true' : 'false' }}">
        <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 p-12 text-center">
            <div class="max-w-md mx-auto">
                <div class="h-20 w-20 rounded-xl bg-rose-100 dark:bg-rose-500/20 flex items-center justify-center mx-auto mb-4">
                    <i class="fa-solid fa-link-slash text-rose-600 dark:text-rose-400 text-2xl"></i>
                </div>
                <h3 class="text-xl font-semibold text-slate-900 dark:text-white mb-2">
                    Онлайн-запись не настроена
                </h3>
                <p class="text-sm text-slate-600 dark:text-slate-400 mb-6">
                    Настройте идентификатор бизнеса, чтобы получить ссылки для записи.
                </p>
                <a href="{{ route('settings.business.edit') }}"
                   class="inline-flex items-center gap-2 px-6 py-3 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg transition-colors">
                    <i class="fa-solid fa-gear text-sm"></i>
                    <span>Настроить бизнес</span>
                </a>
            </div>
        </div>
    </template>

    <!-- Модальное окно QR-кода -->
    <div x-show="showQrModal"
         x-cloak
         role="dialog"
         aria-modal="true"
         aria-labelledby="qr-modal-title"
         @click.away="showQrModal = false"
         @keydown.escape.window="showQrModal = false"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6 bg-black/50 backdrop-blur-sm overflow-y-auto"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         style="display: none;">
        <div @click.stop
             class="relative bg-white dark:bg-slate-900 rounded-xl shadow-xl w-full max-w-md max-h-[90vh] overflow-y-auto p-4 sm:p-6 my-auto"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="transform opacity-0 scale-95"
             x-transition:enter-end="transform opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="transform opacity-100 scale-100"
             x-transition:leave-end="transform opacity-0 scale-95">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 id="qr-modal-title" class="text-lg font-semibold text-slate-900 dark:text-white" x-text="qrTitle"></h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Отсканируйте камерой телефона</p>
                </div>
                <button type="button"
                        @click="showQrModal = false"
                        aria-label="Закрыть"
                        class="min-h-[44px] min-w-[44px] flex items-center justify-center p-1.5 text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg transition-colors">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <div class="flex flex-col items-center gap-4">
                <div class="bg-white p-4 rounded-xl border-2 border-slate-200 dark:border-slate-700 shadow-lg">
                    <img :src="qrImageUrl" :alt="qrTitle" class="w-64 h-64">
                </div>

                <div class="w-full space-y-3">
                    <div class="bg-slate-50 dark:bg-slate-800/50 rounded-lg p-3">
                        <p class="text-xs text-slate-500 dark:text-slate-400 mb-1">Ссылка:</p>
                        <p class="text-xs text-slate-700 dark:text-slate-300 font-mono truncate"
                           x-text="qrTitle === 'QR-код веб-записи' ? '{{ $fullUrl }}' : '{{ $telegramUrl }}'">
                        </p>
                    </div>

                    <div class="flex gap-3">
                        <button type="button"
                                @click="downloadModalQr()"
                                :disabled="isDownloading"
                                class="flex-1 min-h-[44px] inline-flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                            <i class="fa-solid fa-download text-sm"></i>
                            <span x-text="isDownloading ? 'Скачивается...' : 'Скачать'"></span>
                        </button>
                        <button type="button"
                                @click="showQrModal = false"
                                class="min-h-[44px] px-4 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-300 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700 rounded-lg transition-colors">
                            Закрыть
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>

@endsection
