@extends('layouts.user')

@section('title', 'Telegram - Cliently')
@section('page-title', 'Telegram')
@section('page-description', 'Управление уведомлениями и онлайн-записью')

@push('breadcrumbs')
    <x-breadcrumbs :items="[['title' => 'Настройки', 'url' => route('settings.index')], ['title' => 'Telegram', 'url' => null]]" />
@endpush

@section('content')

@php
    $botUsername = $bot ? $bot->name : 'Bot';
    $botState = $botState ?? 'no-bot';
    $connectionUrl = $bot ? 'https://t.me/' . $botUsername . '?start=auth_' . $business->telegram_token : null;
    $bookingUrl = $bot ? 'https://t.me/' . $botUsername . '?start=' . $business->slug : null;
    $bookingQrUrl = $bot ? 'https://quickchart.io/qr?size=200&margin=10&text=' . urlencode($bookingUrl) : null;
@endphp

<div class="max-w-4xl mx-auto" 
     x-data="{
         copiedConnection: false,
         copiedBooking: false,
         showDisconnectModal: false,
         showQrModal: false,
         qrImageUrl: '',
         qrTitle: '',
         async copyText(text, type) {
             try {
                 await navigator.clipboard.writeText(text);
             } catch (err) {
                 const textArea = document.createElement('textarea');
                 textArea.value = text;
                 document.body.appendChild(textArea);
                 textArea.select();
                 document.execCommand('copy');
                 document.body.removeChild(textArea);
             }
             
             if (type === 'connection') {
                 this.copiedConnection = true;
                 setTimeout(() => this.copiedConnection = false, 2000);
             } else {
                 this.copiedBooking = true;
                 setTimeout(() => this.copiedBooking = false, 2000);
             }
         },
         openQrModal() {
             this.qrImageUrl = '{{ str_replace(['size=200', 'margin=10'], ['size=400'], $bookingQrUrl) }}';
             this.qrTitle = 'QR-код для записи через Telegram';
             this.showQrModal = true;
         },
         confirmDisconnect() {
             const form = document.getElementById('disconnect-form');
             if (form) {
                 form.submit();
             }
         }
     }">
    
    <!-- Заголовок страницы с статусом -->
    <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 p-6 mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="h-14 w-14 rounded-xl bg-gradient-to-br from-sky-500 to-teal-600 flex items-center justify-center shrink-0">
                    <i class="fa-brands fa-telegram text-white text-xl"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Telegram интеграция</h1>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Уведомления и запись через бота</p>
                </div>
            </div>
            
            @if ($botState === 'connected')
                <div class="flex items-center gap-2 px-4 py-2 bg-emerald-50 dark:bg-emerald-500/10 rounded-lg border border-emerald-200 dark:border-emerald-600">
                    <div class="h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></div>
                    <span class="text-sm font-medium text-emerald-700 dark:text-emerald-300">Подключено</span>
                </div>
            @elseif($botState === 'disconnected')
                <div class="flex items-center gap-2 px-4 py-2 bg-amber-50 dark:bg-amber-500/10 rounded-lg border border-amber-200 dark:border-amber-600">
                    <i class="fa-solid fa-circle-exclamation text-amber-600 dark:text-amber-400 text-sm"></i>
                    <span class="text-sm font-medium text-amber-700 dark:text-amber-300">Требуется подключение</span>
                </div>
            @else
                <div class="flex items-center gap-2 px-4 py-2 bg-slate-100 dark:bg-slate-800 rounded-lg">
                    <i class="fa-solid fa-robot text-slate-500 dark:text-slate-400 text-sm"></i>
                    <span class="text-sm font-medium text-slate-600 dark:text-slate-400">Недоступно</span>
                </div>
            @endif
        </div>
    </div>

    <!-- Секция 1: Статус подключения -->
    <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 p-6 mb-6">
        <h2 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">Статус подключения</h2>
        
        @if ($botState === 'no-bot')
            <!-- Состояние: бот не настроен -->
            <div class="text-center py-12">
                <div class="h-20 w-20 rounded-2xl bg-amber-100 dark:bg-amber-500/20 flex items-center justify-center mx-auto mb-4">
                    <i class="fa-solid fa-robot text-amber-600 dark:text-amber-400 text-3xl"></i>
                </div>
                <h3 class="text-xl font-semibold text-slate-900 dark:text-white mb-2">Бот не настроен</h3>
                <p class="text-sm text-slate-600 dark:text-slate-400 max-w-md mx-auto mb-6">
                    Функция временно недоступна. Работаем над этим!
                </p>
            </div>
        @elseif($botState === 'disconnected')
            <!-- Состояние: бот настроен, но не подключен -->
            <div class="space-y-6">
                <div class="flex items-start gap-4 p-5 bg-amber-50 dark:bg-amber-500/10 rounded-xl border border-amber-200 dark:border-amber-600/20">
                    <div class="h-12 w-12 rounded-lg bg-amber-100 dark:bg-amber-500/20 flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-info-circle text-amber-600 dark:text-amber-400 text-lg"></i>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-base font-semibold text-slate-900 dark:text-white mb-1">Подключите бота</h3>
                        <p class="text-sm text-slate-600 dark:text-slate-400">
                            Подключите Telegram бота, чтобы получать мгновенные уведомления о новых записях и управлять настройками.
                        </p>
                    </div>
                </div>

                <div class="bg-slate-50 dark:bg-slate-800/50 rounded-xl p-5 border border-slate-200 dark:border-slate-700">
                    <div class="flex items-center justify-between gap-3 mb-4">
                        <label class="text-sm font-semibold text-slate-700 dark:text-slate-300">Ссылка для подключения</label>
                        <button type="button"
                                @click="copyText('{{ $connectionUrl }}', 'connection')"
                                :class="copiedConnection ? 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300' : 'bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300'"
                                class="px-4 py-2 text-sm font-medium rounded-lg border border-slate-300 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
                            <i :class="copiedConnection ? 'fa-solid fa-check' : 'fa-solid fa-copy'" class="mr-2"></i>
                            <span x-text="copiedConnection ? 'Скопировано' : 'Копировать'"></span>
                        </button>
                    </div>
                    <div class="p-4 bg-white dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-700">
                        <p class="text-sm font-mono text-slate-700 dark:text-slate-300 break-all">
                            t.me/{{ $botUsername }}?start=auth_{{ $business->telegram_token }}
                        </p>
                    </div>
                </div>

                <a href="{{ $connectionUrl }}"
                   target="_blank"
                   class="w-full flex items-center justify-center gap-3 px-6 py-3.5 text-base font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl transition-colors shadow-sm hover:shadow-md">
                    <i class="fa-brands fa-telegram text-lg"></i>
                    <span>Подключить Telegram</span>
                    <i class="fa-solid fa-arrow-right text-sm"></i>
                </a>

                <div class="flex items-start gap-3 p-4 bg-blue-50 dark:bg-blue-500/10 rounded-lg border border-blue-200 dark:border-blue-600/20">
                    <i class="fa-solid fa-lightbulb text-blue-600 dark:text-blue-400 mt-0.5"></i>
                    <div>
                        <p class="text-sm font-medium text-slate-900 dark:text-white mb-1">Как подключить:</p>
                        <ol class="text-xs text-slate-600 dark:text-slate-400 space-y-1 list-decimal list-inside">
                            <li>Нажмите кнопку "Подключить Telegram" выше</li>
                            <li>Откроется чат с ботом в Telegram</li>
                            <li>Нажмите кнопку "START" в боте</li>
                            <li>Бот автоматически подключится к вашему аккаунту</li>
                        </ol>
                    </div>
                </div>
            </div>
        @else
            <!-- Состояние: подключено -->
            <div class="space-y-6">
                <div class="flex items-center gap-4 p-6 bg-emerald-50 dark:bg-emerald-500/10 rounded-xl border-2 border-emerald-200 dark:border-emerald-600/30">
                    <div class="h-16 w-16 rounded-xl bg-emerald-100 dark:bg-emerald-500/20 flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-check-circle text-emerald-600 dark:text-emerald-400 text-2xl"></i>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-1">Подключено успешно</h3>
                        <p class="text-sm text-slate-600 dark:text-slate-400 mb-2">
                            Бот подключен и готов к работе. Вы будете получать уведомления о новых записях.
                        </p>
                        <div class="flex items-center gap-2 text-xs text-slate-500 dark:text-slate-400">
                            <i class="fa-solid fa-hashtag"></i>
                            <span>ID чата: <span class="font-mono font-medium">{{ $business->telegram_chat_id }}</span></span>
                        </div>
                    </div>
                </div>

                <form id="disconnect-form" action="{{ route('settings.telegram.disconnect') }}" method="POST" class="hidden">
                    @csrf
                    @method('DELETE')
                </form>

                <button type="button"
                        @click="showDisconnectModal = true"
                        class="w-full flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-medium text-rose-600 dark:text-rose-400 bg-rose-50 dark:bg-rose-500/20 hover:bg-rose-100 dark:hover:bg-rose-500/30 rounded-lg border border-rose-200 dark:border-rose-600 transition-colors">
                    <i class="fa-solid fa-link-slash"></i>
                    <span>Отключить бота</span>
                </button>
            </div>
        @endif
    </div>

    @if ($botState === 'connected')
        <!-- Секция 2: Уведомления (только если подключен) -->
        <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 p-6 mb-6">
            <div class="flex items-center gap-3 mb-6">
                <div class="h-10 w-10 rounded-lg bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center">
                    <i class="fa-solid fa-bell text-white"></i>
                </div>
                <div>
                    <h2 class="text-lg font-semibold text-slate-900 dark:text-white">Настройки уведомлений</h2>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Управляйте уведомлениями в Telegram</p>
                </div>
            </div>

            <div class="space-y-4">
                <div class="flex items-center justify-between p-4 bg-slate-50 dark:bg-slate-800/50 rounded-lg border border-slate-200 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                    <div class="flex items-center gap-3">
                        <div class="h-10 w-10 rounded-lg bg-indigo-100 dark:bg-indigo-500/20 flex items-center justify-center">
                            <i class="fa-solid fa-bell text-indigo-600 dark:text-indigo-400"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-slate-900 dark:text-white">Звуковые уведомления</p>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Уведомления с звуком при новых записях</p>
                        </div>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" class="sr-only peer" checked>
                        <div class="w-11 h-6 bg-slate-300 dark:bg-slate-700 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-indigo-500 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                    </label>
                </div>

                <div class="flex items-center justify-between p-4 bg-slate-50 dark:bg-slate-800/50 rounded-lg border border-slate-200 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                    <div class="flex items-center gap-3">
                        <div class="h-10 w-10 rounded-lg bg-purple-100 dark:bg-purple-500/20 flex items-center justify-center">
                            <i class="fa-solid fa-chart-bar text-purple-600 dark:text-purple-400"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-slate-900 dark:text-white">Ежедневные отчеты</p>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Получайте сводку за день каждый вечер</p>
                        </div>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" class="sr-only peer">
                        <div class="w-11 h-6 bg-slate-300 dark:bg-slate-700 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-indigo-500 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                    </label>
                </div>
            </div>
        </div>
    @endif

    @if ($botState !== 'no-bot')
        <!-- Секция 3: Запись для клиентов -->
        <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 p-6 mb-6">
            <div class="flex items-center gap-3 mb-6">
                <div class="h-10 w-10 rounded-lg bg-gradient-to-br from-sky-500 to-teal-600 flex items-center justify-center">
                    <i class="fa-solid fa-calendar-check text-white"></i>
                </div>
                <div>
                    <h2 class="text-lg font-semibold text-slate-900 dark:text-white">Запись для клиентов</h2>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Клиенты могут записываться через Telegram бота</p>
                </div>
            </div>

            <div class="space-y-6">
                <p class="text-sm text-slate-600 dark:text-slate-400">
                    Поделитесь ссылкой с клиентами. Бот проведет их через процесс записи и поможет выбрать удобное время.
                </p>

                <!-- QR-код -->
                @if ($bookingQrUrl)
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-3">QR-код для быстрого доступа</label>
                        <div class="bg-slate-50 dark:bg-slate-800/50 rounded-xl p-6 border border-slate-200 dark:border-slate-700">
                            <div class="flex flex-col items-center gap-4">
                                <div class="cursor-pointer hover:scale-105 transition-transform duration-200"
                                     @click="openQrModal()">
                                    <img src="{{ $bookingQrUrl }}" 
                                         alt="QR-код для записи"
                                         class="w-48 h-48 rounded-lg shadow-sm border-2 border-slate-200 dark:border-slate-700">
                                </div>
                                <button type="button"
                                        @click="openQrModal()"
                                        class="px-4 py-2 text-sm font-medium text-slate-700 dark:text-slate-300 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700 rounded-lg transition-colors">
                                    <i class="fa-solid fa-expand mr-2"></i>
                                    Увеличить QR-код
                                </button>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Ссылка -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-3">Ссылка для записи</label>
                    <div class="bg-slate-50 dark:bg-slate-800/50 rounded-xl p-5 border border-slate-200 dark:border-slate-700">
                        <div class="flex items-center justify-between gap-3 mb-3">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-mono text-slate-700 dark:text-slate-300 break-all">
                                    t.me/{{ $botUsername }}?start={{ $business->slug }}
                                </p>
                            </div>
                            <button type="button"
                                    @click="copyText('{{ $bookingUrl }}', 'booking')"
                                    :class="copiedBooking ? 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300' : 'bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300'"
                                    class="px-4 py-2 text-sm font-medium rounded-lg border border-slate-300 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors whitespace-nowrap">
                                <i :class="copiedBooking ? 'fa-solid fa-check' : 'fa-solid fa-copy'" class="mr-2"></i>
                                <span x-text="copiedBooking ? 'Скопировано' : 'Копировать'"></span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Кнопки действий -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <button type="button"
                            @click="copyText('{{ $bookingUrl }}', 'booking')"
                            class="flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-medium text-sky-700 dark:text-sky-300 bg-sky-50 dark:bg-sky-500/20 hover:bg-sky-100 dark:hover:bg-sky-500/30 rounded-lg border border-sky-200 dark:border-sky-600 transition-colors">
                        <i class="fa-solid fa-copy"></i>
                        <span>Копировать ссылку</span>
                    </button>

                    <a href="{{ $bookingUrl }}" 
                       target="_blank"
                       class="flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-300 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700 rounded-lg transition-colors">
                        <i class="fa-solid fa-external-link"></i>
                        <span>Открыть бота</span>
                    </a>
                </div>

                <!-- Инструкция для клиентов -->
                <div class="flex items-start gap-3 p-4 bg-blue-50 dark:bg-blue-500/10 rounded-lg border border-blue-200 dark:border-blue-600/20">
                    <i class="fa-solid fa-info-circle text-blue-600 dark:text-blue-400 mt-0.5"></i>
                    <div>
                        <p class="text-sm font-medium text-slate-900 dark:text-white mb-1">Для клиентов:</p>
                        <p class="text-xs text-slate-600 dark:text-slate-400">
                            Клиенты должны нажать кнопку "START" в боте для начала процесса записи. Бот проведет их через выбор услуги, мастера и времени.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Преимущества Telegram -->
    <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 p-6">
        <div class="flex items-center gap-3 mb-6">
            <div class="h-10 w-10 rounded-lg bg-amber-100 dark:bg-amber-500/20 flex items-center justify-center">
                <i class="fa-solid fa-star text-amber-600 dark:text-amber-400"></i>
            </div>
            <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Преимущества Telegram</h3>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="p-4 bg-slate-50 dark:bg-slate-800/50 rounded-lg border border-slate-200 dark:border-slate-700">
                <div class="h-10 w-10 rounded-lg bg-sky-100 dark:bg-sky-500/20 flex items-center justify-center mb-3">
                    <i class="fa-solid fa-bolt text-sky-600 dark:text-sky-400"></i>
                </div>
                <h4 class="text-sm font-semibold text-slate-900 dark:text-white mb-1">Мгновенные уведомления</h4>
                <p class="text-xs text-slate-600 dark:text-slate-400">
                    Узнавайте о новых записях сразу
                </p>
            </div>

            <div class="p-4 bg-slate-50 dark:bg-slate-800/50 rounded-lg border border-slate-200 dark:border-slate-700">
                <div class="h-10 w-10 rounded-lg bg-sky-100 dark:bg-sky-500/20 flex items-center justify-center mb-3">
                    <i class="fa-solid fa-mobile-screen text-sky-600 dark:text-sky-400"></i>
                </div>
                <h4 class="text-sm font-semibold text-slate-900 dark:text-white mb-1">Удобство для клиентов</h4>
                <p class="text-xs text-slate-600 dark:text-slate-400">
                    Запись прямо в мессенджере
                </p>
            </div>

            <div class="p-4 bg-slate-50 dark:bg-slate-800/50 rounded-lg border border-slate-200 dark:border-slate-700">
                <div class="h-10 w-10 rounded-lg bg-sky-100 dark:bg-sky-500/20 flex items-center justify-center mb-3">
                    <i class="fa-solid fa-robot text-sky-600 dark:text-sky-400"></i>
                </div>
                <h4 class="text-sm font-semibold text-slate-900 dark:text-white mb-1">Автоматизация</h4>
                <p class="text-xs text-slate-600 dark:text-slate-400">
                    Бот отвечает на вопросы клиентов
                </p>
            </div>

            <div class="p-4 bg-slate-50 dark:bg-slate-800/50 rounded-lg border border-slate-200 dark:border-slate-700">
                <div class="h-10 w-10 rounded-lg bg-sky-100 dark:bg-sky-500/20 flex items-center justify-center mb-3">
                    <i class="fa-solid fa-shield-halved text-sky-600 dark:text-sky-400"></i>
                </div>
                <h4 class="text-sm font-semibold text-slate-900 dark:text-white mb-1">Надежность</h4>
                <p class="text-xs text-slate-600 dark:text-slate-400">
                    Стабильная работа и безопасность
                </p>
            </div>
        </div>
    </div>

    <!-- Модальное окно подтверждения отключения -->
    <div x-show="showDisconnectModal" 
         x-cloak
         @click.away="showDisconnectModal = false"
         @keydown.escape.window="showDisconnectModal = false"
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
                <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Подтверждение отключения</h3>
                <button @click="showDisconnectModal = false"
                        class="p-1.5 text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg transition-colors">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>
            <p class="text-sm text-slate-700 dark:text-slate-300 mb-6">
                Вы уверены, что хотите отключить Telegram бота? Вы перестанете получать уведомления о новых записях.
            </p>
            <div class="flex gap-3">
                <button @click="showDisconnectModal = false"
                        class="flex-1 px-4 py-2.5 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-medium hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
                    Отмена
                </button>
                <button @click="confirmDisconnect()"
                        class="flex-1 px-4 py-2.5 rounded-lg bg-rose-600 hover:bg-rose-700 text-white font-medium transition-colors">
                    Отключить
                </button>
            </div>
        </div>
    </div>

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
                <div class="w-full bg-slate-50 dark:bg-slate-800/50 rounded-lg p-3">
                    <p class="text-xs text-slate-500 dark:text-slate-400 mb-1">Ссылка:</p>
                    <p class="text-xs text-slate-700 dark:text-slate-300 font-mono truncate" x-text="'{{ $bookingUrl }}'"></p>
                </div>
                <button @click="showQrModal = false"
                        class="w-full px-4 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-300 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700 rounded-lg transition-colors">
                    Закрыть
                </button>
            </div>
        </div>
    </div>
</div>

@endsection
