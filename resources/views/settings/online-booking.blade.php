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

<div class="max-w-4xl mx-auto">
@php
    $fullUrl = route('public.appointments.show', ['slug' => $business->slug]);
    $displayUrl = str_replace(['http://', 'https://', 'www.'], '', $fullUrl);
    $telegramUrl = $bot ? 'https://t.me/' . $bot->name . '?start=' . $business->slug : null;

    // QR-коды
    $webQrUrl = 'https://quickchart.io/qr?size=200&margin=10&text=' . urlencode($fullUrl);
    $telegramQrUrl = $bot
        ? 'https://quickchart.io/qr?size=200&margin=10&text=' . urlencode($telegramUrl)
        : null;

    // Состояние онлайн-записи
    $isEnabled = $business->online_booking_enabled ?? true;
@endphp

<div x-data="{
    isEnabled: {{ $isEnabled ? 'true' : 'false' }},
    copiedWeb: false,
    copiedTelegram: false,
    showQrModal: false,
    qrImageUrl: '',
    qrTitle: '',
    isDownloading: false,

    async toggleBooking() {
        try {
            const form = document.getElementById('booking-form');
            const formData = new FormData(form);
            formData.set('online_booking_enabled', !this.isEnabled);
            
            const response = await fetch('{{ route('settings.online-booking.update') }}', {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                    'Accept': 'application/json',
                },
                body: formData
            });

            if (response.ok) {
                this.isEnabled = !this.isEnabled;
            } else {
                throw new Error('Ошибка при сохранении');
            }
        } catch (error) {
            console.error('Ошибка при изменении состояния:', error);
            alert('Не удалось изменить состояние онлайн-записи');
        }
    },

    async copyUrl(type) {
        let url = type === 'web' ? '{{ $fullUrl }}' : '{{ $telegramUrl }}';
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
        this.qrImageUrl = type === 'web' ?
            '{{ str_replace(['size=200', 'margin=10'], ['size=400'], $webQrUrl) }}' :
            '{{ str_replace(['size=200', 'margin=10'], ['size=400'], $telegramQrUrl) }}';
        this.qrTitle = type === 'web' ? 'QR-код веб-записи' : 'QR-код Telegram';
        this.showQrModal = true;
    },

    async downloadQr(url, filename) {
        if (this.isDownloading) return;

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
        const filename = 'qr-запись-{{ $business->slug }}.png';
        this.downloadQr('{{ $webQrUrl }}', filename);
    },

    downloadTelegramQr() {
        const filename = 'qr-telegram-{{ $business->slug }}.png';
        this.downloadQr('{{ $telegramQrUrl }}', filename);
    },

    downloadModalQr() {
        const filename = 'qr-{{ $business->slug }}-' + new Date().getTime() + '.png';
        this.downloadQr(this.qrImageUrl, filename);
    }
}">
    <form id="booking-form" method="POST" action="{{ route('settings.online-booking.update') }}" class="hidden">
        @csrf
        @method('PATCH')
        <input type="hidden" name="online_booking_enabled" :value="isEnabled ? '1' : '0'">
    </form>

    <!-- Заголовок страницы -->
    <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 p-6 mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Онлайн-запись</h1>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Управление доступом к записи для клиентов</p>
            </div>
            
            <!-- Переключатель -->
            <div class="flex items-center gap-4">
                <div class="text-right">
                    <div class="text-sm font-medium text-slate-900 dark:text-white" x-text="isEnabled ? 'Включена' : 'Выключена'"></div>
                    <div class="text-xs text-slate-500 dark:text-slate-400" x-text="isEnabled ? 'Клиенты могут записываться' : 'Запись временно недоступна'"></div>
                </div>
                <button type="button" 
                        @click="toggleBooking()"
                        :class="isEnabled ? 'bg-indigo-600' : 'bg-slate-300 dark:bg-slate-700'"
                        class="relative inline-flex h-7 w-12 items-center rounded-full transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                    <span class="sr-only">Включить онлайн-запись</span>
                    <span :class="isEnabled ? 'translate-x-6' : 'translate-x-1'"
                          class="inline-block h-5 w-5 transform rounded-full bg-white transition-transform shadow-sm"></span>
                </button>
            </div>
        </div>
    </div>

    <!-- Контент (скрывается при выключенной записи) -->
    <template x-if="isEnabled && {{ $business->slug ? 'true' : 'false' }}">
        <div class="space-y-6">
            <!-- Веб-запись -->
            <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 p-6">
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center gap-3">
                        <div class="h-12 w-12 rounded-lg bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center">
                            <i class="fa-solid fa-globe text-white text-lg"></i>
                        </div>
                        <div>
                            <h2 class="text-lg font-semibold text-slate-900 dark:text-white">Веб-запись</h2>
                            <p class="text-sm text-slate-500 dark:text-slate-400">Запись через веб-страницу</p>
                        </div>
                    </div>
                    <a href="{{ $fullUrl }}" 
                       target="_blank" 
                       rel="noopener"
                       class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-indigo-700 dark:text-indigo-300 bg-indigo-50 dark:bg-indigo-900/30 hover:bg-indigo-100 dark:hover:bg-indigo-900/50 rounded-lg transition-colors">
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

                        <div class="bg-slate-50 dark:bg-slate-800/50 rounded-xl p-6">
                            <div class="flex flex-col items-center gap-4">
                                <div class="cursor-pointer hover:scale-105 transition-transform duration-200"
                                     @click="openQrModal('web')">
                                    <img src="{{ $webQrUrl }}" 
                                         alt="QR-код веб-записи"
                                         class="w-48 h-48 rounded-lg shadow-sm border-2 border-slate-200 dark:border-slate-700">
                                </div>

                                <div class="flex gap-3 w-full max-w-xs">
                                    <button @click="downloadWebQr()" 
                                            :disabled="isDownloading"
                                            class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                                        <i class="fa-solid fa-download text-sm"></i>
                                        <span x-text="isDownloading ? 'Скачивается...' : 'Скачать'"></span>
                                    </button>
                                    <button @click="openQrModal('web')"
                                            class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-300 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700 rounded-lg transition-colors">
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
                        <div class="flex items-center gap-3 p-4 bg-slate-50 dark:bg-slate-800/50 rounded-lg border border-slate-200 dark:border-slate-700">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm text-slate-700 dark:text-slate-300 font-mono truncate">
                                    {{ $displayUrl }}
                                </p>
                            </div>
                            <button type="button"
                                    @click="copyUrl('web')"
                                    :class="copiedWeb ? 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300' : 'bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300'"
                                    class="px-4 py-2 text-sm font-medium rounded-lg border border-slate-300 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors whitespace-nowrap">
                                <i :class="copiedWeb ? 'fa-solid fa-check' : 'fa-solid fa-copy'" class="mr-2"></i>
                                <span x-text="copiedWeb ? 'Скопировано' : 'Копировать'"></span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Telegram запись -->
            @if ($bot && $telegramUrl)
                <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 p-6">
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center gap-3">
                            <div class="h-12 w-12 rounded-lg bg-gradient-to-br from-sky-500 to-teal-600 flex items-center justify-center">
                                <i class="fa-brands fa-telegram text-white text-lg"></i>
                            </div>
                            <div>
                                <h2 class="text-lg font-semibold text-slate-900 dark:text-white">Telegram бот</h2>
                                <p class="text-sm text-slate-500 dark:text-slate-400">Запись через мессенджер</p>
                            </div>
                        </div>
                        <a href="{{ $telegramUrl }}" 
                           target="_blank" 
                           rel="noopener"
                           class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-sky-700 dark:text-sky-300 bg-sky-50 dark:bg-sky-900/30 hover:bg-sky-100 dark:hover:bg-sky-900/50 rounded-lg transition-colors">
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

                            <div class="bg-slate-50 dark:bg-slate-800/50 rounded-xl p-6">
                                <div class="flex flex-col items-center gap-4">
                                    <div class="cursor-pointer hover:scale-105 transition-transform duration-200"
                                         @click="openQrModal('telegram')">
                                        <img src="{{ $telegramQrUrl }}" 
                                             alt="QR-код Telegram"
                                             class="w-48 h-48 rounded-lg shadow-sm border-2 border-slate-200 dark:border-slate-700">
                                    </div>

                                    <div class="flex gap-3 w-full max-w-xs">
                                        <button @click="downloadTelegramQr()" 
                                                :disabled="isDownloading"
                                                class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-medium text-white bg-sky-600 hover:bg-sky-700 rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                                            <i class="fa-solid fa-download text-sm"></i>
                                            <span x-text="isDownloading ? 'Скачивается...' : 'Скачать'"></span>
                                        </button>
                                        <button @click="openQrModal('telegram')"
                                                class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-300 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700 rounded-lg transition-colors">
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
                            <div class="flex items-center gap-3 p-4 bg-slate-50 dark:bg-slate-800/50 rounded-lg border border-slate-200 dark:border-slate-700">
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm text-slate-700 dark:text-slate-300 font-mono truncate">
                                        t.me/{{ $bot->name }}?start={{ $business->slug }}
                                    </p>
                                </div>
                                <button type="button"
                                        @click="copyUrl('telegram')"
                                        :class="copiedTelegram ? 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300' : 'bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300'"
                                        class="px-4 py-2 text-sm font-medium rounded-lg border border-slate-300 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors whitespace-nowrap">
                                    <i :class="copiedTelegram ? 'fa-solid fa-check' : 'fa-solid fa-copy'" class="mr-2"></i>
                                    <span x-text="copiedTelegram ? 'Скопировано' : 'Копировать'"></span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Советы по использованию -->
            <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 p-6">
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

    <!-- Сообщение если slug не настроен -->
    <template x-if="isEnabled && !{{ $business->slug ? 'true' : 'false' }}">
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
         @click.away="showQrModal = false"
         @keydown.escape.window="showQrModal = false"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         style="display: none;">
        <div @click.stop
             class="relative bg-white dark:bg-slate-900 rounded-xl shadow-xl w-full max-w-md p-6"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="transform opacity-0 scale-95"
             x-transition:enter-end="transform opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="transform opacity-100 scale-100"
             x-transition:leave-end="transform opacity-0 scale-95">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-lg font-semibold text-slate-900 dark:text-white" x-text="qrTitle"></h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Отсканируйте камерой телефона</p>
                </div>
                <button @click="showQrModal = false"
                        class="p-1.5 text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg transition-colors">
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
                        <button @click="downloadModalQr()" 
                                :disabled="isDownloading"
                                class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                            <i class="fa-solid fa-download text-sm"></i>
                            <span x-text="isDownloading ? 'Скачивается...' : 'Скачать'"></span>
                        </button>
                        <button @click="showQrModal = false"
                                class="px-4 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-300 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700 rounded-lg transition-colors">
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
