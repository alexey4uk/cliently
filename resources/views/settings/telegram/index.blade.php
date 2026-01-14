@extends('layouts.user')

@section('title', 'Telegram - Cliently')
@section('page-title', 'Telegram')
@section('page-description', 'Управление уведомлениями и онлайн-записью через Telegram')

@push('breadcrumbs')
    <x-breadcrumbs :items="[['title' => 'Настройки', 'url' => route('settings.index')], ['title' => 'Telegram', 'url' => null]]" />
@endpush

@section('content')
    <div class="w-full px-2 sm:px-0 pb-10 space-y-6">

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            @if (!$business)
                <div class="text-center py-10">
                    <p>Бизнес не найден.</p>
                </div>
                @php return; @endphp
            @endif

            @php
                $botUsername = $bot ? $bot->name : 'Bot';
            @endphp

            <!-- КАРТОЧКА: МАСТЕР (Уведомления) -->
            <div
                class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm flex flex-col transition-all">
                <div class="p-6 flex-1">
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center gap-3">
                            <div
                                class="w-10 h-10 rounded-xl bg-indigo-50 dark:bg-indigo-900/30 flex items-center justify-center text-indigo-600">
                                <i class="fa-solid fa-bell text-lg"></i>
                            </div>
                            <h3 class="font-bold text-slate-900 dark:text-white text-base">Уведомления</h3>
                        </div>

                        @if ($business->telegram_chat_id)
                            <span
                                class="flex items-center gap-1.5 px-3 py-1 bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 dark:text-emerald-400 text-[11px] font-bold rounded-full border border-emerald-100 dark:border-emerald-800/50">
                                <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-pulse"></span>
                                Связь активна
                            </span>
                        @else
                            <span
                                class="px-3 py-1 bg-slate-100 dark:bg-slate-800 text-slate-500 text-[11px] font-bold rounded-full border border-transparent">
                                Не подключен
                            </span>
                        @endif
                    </div>

                    @if (!$business->telegram_chat_id)
                        <!-- Состояние: Нужно подключить -->
                        <p class="text-slate-500 text-sm mb-6 leading-relaxed">
                            Подключите нашего бота, чтобы получать мгновенные уведомления о новых записях и других событиях.
                        </p>
                        <div
                            class="bg-slate-50 dark:bg-slate-800/50 rounded-2xl p-4 border border-slate-100 dark:border-slate-700">
                            <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">Ссылка активации
                            </div>
                            <div class="flex items-center justify-between gap-2 overflow-hidden">
                                <code
                                    class="text-indigo-600 dark:text-indigo-400 font-mono font-bold truncate text-sm">t.me/{{ $botUsername }}?start=auth_{{ $business->telegram_token }}</code>
                                <button
                                    onclick="copyText('https://t.me/{{ $botUsername }}?start=auth_{{ $business->telegram_token }}', this)"
                                    class="shrink-0 text-slate-400 hover:text-indigo-600 transition-colors p-1">
                                    <i class="fa-regular fa-copy text-base"></i>
                                </button>
                            </div>
                        </div>
                    @else
                        <!-- Состояние: Уже подключен (Доработанное) -->
                        <div class="space-y-4">
                            <div
                                class="flex items-center gap-4 p-4 bg-emerald-50/50 dark:bg-emerald-900/10 rounded-2xl border border-emerald-100/50 dark:border-emerald-800/20">
                                <div
                                    class="w-12 h-12 rounded-full bg-white dark:bg-slate-800 flex items-center justify-center text-emerald-600 shadow-sm border border-emerald-100 dark:border-emerald-700">
                                    <i class="fa-solid fa-check-double text-lg"></i>
                                </div>
                                <div class="overflow-hidden">
                                    <p class="text-sm font-bold text-slate-900 dark:text-white truncate">Вы успешно
                                        авторизованы</p>
                                    <p class="text-[11px] text-slate-500 leading-none mt-1">ID:
                                        {{ $business->telegram_chat_id }} (Ваш профиль)</p>
                                </div>
                            </div>

                            <!-- Простые настройки уведомлений для подключенного мастера -->
                            <div class="space-y-2">
                                <div
                                    class="flex items-center justify-between p-3 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors cursor-pointer">
                                    <span class="text-sm text-slate-600 dark:text-slate-400">Звуковые уведомления</span>
                                    <div class="w-8 h-4 bg-indigo-600 rounded-full relative">
                                        <div class="absolute right-0.5 top-0.5 w-3 h-3 bg-white rounded-full"></div>
                                    </div>
                                </div>
                                <div
                                    class="flex items-center justify-between p-3 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors cursor-pointer border-t border-slate-50 dark:border-slate-800">
                                    <span class="text-sm text-slate-600 dark:text-slate-400">Ежедневные отчеты</span>
                                    <div class="w-8 h-4 bg-slate-200 dark:bg-slate-700 rounded-full relative">
                                        <div class="absolute left-0.5 top-0.5 w-3 h-3 bg-white rounded-full"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="p-6 pt-0 mt-auto">
                    @if (!$business->telegram_chat_id)
                        <a href="https://t.me/{{ $botUsername }}?start=auth_{{ $business->telegram_token }}"
                            target="_blank"
                            class="w-full flex items-center justify-center gap-2 py-4 bg-indigo-600 hover:bg-indigo-700 text-white rounded-2xl font-bold transition-all active:scale-[0.98] shadow-lg shadow-indigo-100 dark:shadow-none">
                            <i class="fa-brands fa-telegram text-lg"></i>
                            <span>Подключить Telegram</span>
                        </a>
                    @else
                        <form action="{{ route('settings.telegram.disconnect') }}" method="POST"
                            onsubmit="return confirm('Вы уверены, что хотите отключить уведомления?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="w-full py-3.5 text-xs font-bold text-rose-500 hover:text-rose-600 bg-rose-50/50 dark:bg-rose-900/10 hover:bg-rose-50 dark:hover:bg-rose-900/20 rounded-2xl transition-all border border-rose-100 dark:border-rose-900/20">
                                <i class="fa-solid fa-link-slash mr-2"></i>
                                Отвязать аккаунт мастера
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            <!-- КАРТОЧКА: КЛИЕНТЫ (Запись) -->
            <div
                class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm flex flex-col transition-all group">
                <div class="p-6 flex-1">
                    <div class="flex items-center gap-3 mb-6">
                        <div
                            class="w-10 h-10 rounded-xl bg-sky-50 dark:bg-sky-900/30 flex items-center justify-center text-sky-600 group-hover:scale-110 transition-transform">
                            <i class="fa-solid fa-calendar-check text-lg"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-slate-900 dark:text-white text-base">Онлайн-запись</h3>
                            @if (!empty($bot))
                                <span
                                    class="flex items-center gap-1.5 px-3 py-1 bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 dark:text-emerald-400 text-[11px] font-bold rounded-full border border-emerald-100 dark:border-emerald-800/50">
                                    <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-pulse"></span>
                                    Бот активен
                                </span>
                            @else
                                <span
                                    class="px-3 py-1 bg-slate-100 dark:bg-slate-800 text-slate-500 text-[11px] font-bold rounded-full border border-transparent">
                                    Бот не настроен
                                </span>
                            @endif
                        </div>
                    </div>

                    <p class="text-slate-500 text-sm mb-6 leading-relaxed">
                        Поделитесь этой ссылкой с клиентами. Бот автоматически проведет их через процесс записи к вам.
                    </p>

                    <div
                        class="bg-slate-50 dark:bg-slate-800/50 rounded-2xl p-4 border border-slate-100 dark:border-slate-700 group-hover:border-sky-200 dark:group-hover:border-sky-900 transition-colors">
                        <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">Публичная ссылка
                        </div>
                        <div class="flex items-center justify-between gap-2 overflow-hidden">
                            <code
                                class="text-sky-600 dark:text-sky-400 font-mono font-bold truncate text-sm">t.me/{{ $botUsername }}?start={{ $business->slug }}</code>
                            <button
                                onclick="copyText('https://t.me/{{ $botUsername }}?start={{ $business->slug }}', this)"
                                class="shrink-0 text-slate-400 hover:text-sky-600 transition-colors p-1">
                                <i class="fa-regular fa-copy text-base"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="p-6 pt-0 mt-auto">
                    <button onclick="copyText('https://t.me/{{ $botUsername }}?start={{ $business->slug }}', this)"
                        class="w-full flex items-center justify-center gap-2 py-4 bg-white dark:bg-slate-800 text-slate-700 dark:text-white border border-slate-200 dark:border-slate-700 rounded-2xl font-bold hover:bg-slate-50 dark:hover:bg-slate-800/80 transition-all active:scale-[0.98] shadow-sm">
                        <i class="fa-solid fa-share-nodes text-sky-500"></i>
                        <span>Скопировать ссылку</span>
                    </button>
                </div>
            </div>

        </div>
    </div>

    <script>
        function copyText(text, btn) {
            navigator.clipboard.writeText(text);
            const icon = btn.querySelector('i');
            const originalClass = icon.className;
            icon.className = 'fa-solid fa-check text-emerald-500';
            setTimeout(() => {
                icon.className = originalClass;
            }, 2000);
        }
    </script>
@endsection
