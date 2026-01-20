@extends('layouts.user')

@section('title', 'Telegram - Cliently')
@section('page-title', 'Telegram')
@section('page-description', 'Управление уведомлениями и онлайн-записью')

@push('breadcrumbs')
    <x-breadcrumbs :items="[['title' => 'Настройки', 'url' => route('settings.index')], ['title' => 'Telegram', 'url' => null]]" />
@endpush

@section('content')
    <div class="space-y-6">
        @php
            $botUsername = $bot ? $bot->name : 'Bot';
            $botState = $botState ?? 'no-bot';
        @endphp

        <!-- Шапка страницы -->
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm p-5">
            <div class="flex items-center gap-3">
                <div
                    class="h-10 w-10 rounded-lg bg-gradient-to-br from-sky-500 to-teal-600 flex items-center justify-center">
                    <i class="fa-brands fa-telegram text-white"></i>
                </div>
                <div>
                    <h2 class="text-lg font-semibold text-slate-900 dark:text-white">Telegram интеграция</h2>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Уведомления и запись через бота</p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Карточка: Уведомления -->
            <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm p-5">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-3">
                        <div
                            class="h-10 w-10 rounded-lg bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center">
                            <i class="fa-solid fa-bell text-white"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold text-slate-900 dark:text-white">Уведомления</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Получайте уведомления в Telegram</p>
                        </div>
                    </div>

                    @if ($botState === 'connected')
                        <span
                            class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 text-xs font-medium rounded-full">
                            <i class="fa-solid fa-circle-check text-[10px]"></i>
                            Подключено
                        </span>
                    @elseif($botState === 'disconnected')
                        <span
                            class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400 text-xs font-medium rounded-full">
                            <i class="fa-solid fa-circle-exclamation text-[10px]"></i>
                            Не подключено
                        </span>
                    @else
                        <span
                            class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 text-xs font-medium rounded-full">
                            Временно недоступно
                        </span>
                    @endif
                </div>

                @if ($botState === 'no-bot')
                    <!-- Состояние: бот не настроен -->
                    <div class="text-center py-8">
                        <div
                            class="h-14 w-14 rounded-full bg-amber-50 dark:bg-amber-900/20 flex items-center justify-center mx-auto mb-4">
                            <i class="fa-solid fa-robot text-amber-500 dark:text-amber-400 text-lg"></i>
                        </div>
                        <h4 class="text-sm font-medium text-slate-800 dark:text-slate-200 mb-2">Упс. Уже чиним.</h4>
                        <p class="text-xs text-slate-600 dark:text-slate-400">
                            Функция временно недоступна. Работаем над этим!
                        </p>
                    </div>
                @elseif($botState === 'disconnected')
                    <!-- Состояние: бот настроен, но не подключен -->
                    <div class="space-y-4">
                        <p class="text-sm text-slate-600 dark:text-slate-400">
                            Подключите бота, чтобы получать мгновенные уведомления.
                        </p>

                        <div
                            class="bg-slate-50 dark:bg-slate-800/50 rounded-lg p-4 border border-slate-200 dark:border-slate-700">
                            <div class="flex items-center justify-between gap-2 mb-2">
                                <span class="text-xs font-medium text-slate-600 dark:text-slate-400">Ссылка
                                    подключения</span>
                                <button
                                    onclick="copyText('https://t.me/{{ $botUsername }}?start=auth_{{ $business->telegram_token }}')"
                                    class="text-xs text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300">
                                    <i class="fa-solid fa-copy mr-1"></i>
                                    Копировать
                                </button>
                            </div>
                            <code class="text-sm font-mono text-slate-700 dark:text-slate-300 break-all">
                                t.me/{{ $botUsername }}?start=auth_{{ $business->telegram_token }}
                            </code>
                        </div>

                        <a href="https://t.me/{{ $botUsername }}?start=auth_{{ $business->telegram_token }}"
                            target="_blank"
                            class="w-full flex items-center justify-center gap-2 py-2.5 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-medium rounded-lg transition-colors">
                            <i class="fa-brands fa-telegram"></i>
                            Подключить Telegram
                        </a>

                        <div class="text-xs text-slate-500 dark:text-slate-400 mt-3">
                            <i class="fa-solid fa-info-circle mr-1"></i>
                            Нажмите "START" в боте для подключения
                        </div>
                    </div>
                @else
                    <!-- Состояние: подключено -->
                    <div class="space-y-4">
                        <div
                            class="flex items-center gap-3 p-3 bg-emerald-50 dark:bg-emerald-900/10 rounded-lg border border-emerald-100 dark:border-emerald-800/20">
                            <div
                                class="h-8 w-8 rounded-full bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center">
                                <i class="fa-solid fa-check text-emerald-600 dark:text-emerald-400 text-sm"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-slate-800 dark:text-slate-200">Подключено успешно</p>
                                <p class="text-xs text-slate-500 dark:text-slate-400">ID чата:
                                    {{ $business->telegram_chat_id }}</p>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <div
                                class="flex items-center justify-between p-3 hover:bg-slate-50 dark:hover:bg-slate-800/50 rounded-lg transition-colors">
                                <div class="flex items-center gap-2">
                                    <i class="fa-solid fa-bell text-slate-400 dark:text-slate-500 text-sm"></i>
                                    <span class="text-sm text-slate-600 dark:text-slate-400">Звуковые уведомления</span>
                                </div>
                                <div class="relative inline-block w-10 h-5">
                                    <input type="checkbox" id="sound-notifications" class="sr-only" checked>
                                    <div class="block w-10 h-5 bg-indigo-600 rounded-full transition-colors"></div>
                                    <div class="absolute right-1 top-1 bg-white w-3 h-3 rounded-full transition-transform">
                                    </div>
                                </div>
                            </div>

                            <div
                                class="flex items-center justify-between p-3 hover:bg-slate-50 dark:hover:bg-slate-800/50 rounded-lg transition-colors">
                                <div class="flex items-center gap-2">
                                    <i class="fa-solid fa-chart-bar text-slate-400 dark:text-slate-500 text-sm"></i>
                                    <span class="text-sm text-slate-600 dark:text-slate-400">Ежедневные отчеты</span>
                                </div>
                                <div class="relative inline-block w-10 h-5">
                                    <input type="checkbox" id="daily-reports" class="sr-only">
                                    <div
                                        class="block w-10 h-5 bg-slate-300 dark:bg-slate-700 rounded-full transition-colors">
                                    </div>
                                    <div class="absolute left-1 top-1 bg-white w-3 h-3 rounded-full transition-transform">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <form action="{{ route('settings.telegram.disconnect') }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" onclick="return confirm('Вы уверены, что хотите отключить бота?')"
                                class="w-full flex items-center justify-center gap-2 py-2.5 text-sm font-medium text-rose-600 dark:text-rose-400 bg-rose-50 dark:bg-rose-900/20 hover:bg-rose-100 dark:hover:bg-rose-900/30 rounded-lg border border-rose-200 dark:border-rose-800 transition-colors">
                                <i class="fa-solid fa-link-slash"></i>
                                Отключить
                            </button>
                        </form>
                    </div>
                @endif
            </div>

            <!-- Карточка: Онлайн-запись для клиентов -->
            <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm p-5">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-3">
                        <div
                            class="h-10 w-10 rounded-lg bg-gradient-to-br from-sky-500 to-teal-600 flex items-center justify-center">
                            <i class="fa-solid fa-calendar-check text-white"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold text-slate-900 dark:text-white">Запись для клиентов</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Клиенты записываются через бота</p>
                        </div>
                    </div>

                    @if ($botState !== 'no-bot')
                        <span
                            class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 text-xs font-medium rounded-full">
                            <i class="fa-solid fa-circle-check text-[10px]"></i>
                            Доступно
                        </span>
                    @else
                        <span
                            class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 text-xs font-medium rounded-full">
                            Временно недоступно
                        </span>
                    @endif
                </div>

                @if ($botState !== 'no-bot')
                    <div class="space-y-4">
                        <p class="text-sm text-slate-600 dark:text-slate-400">
                            Поделитесь ссылкой с клиентами. Бот проведет их через процесс записи.
                        </p>

                        <div
                            class="bg-slate-50 dark:bg-slate-800/50 rounded-lg p-4 border border-slate-200 dark:border-slate-700">
                            <div class="flex items-center justify-between gap-2 mb-2">
                                <span class="text-xs font-medium text-slate-600 dark:text-slate-400">Ссылка для
                                    записи</span>
                                <button onclick="copyText('https://t.me/{{ $botUsername }}?start={{ $business->slug }}')"
                                    class="text-xs text-sky-600 dark:text-sky-400 hover:text-sky-700 dark:hover:text-sky-300">
                                    <i class="fa-solid fa-copy mr-1"></i>
                                    Копировать
                                </button>
                            </div>
                            <code class="text-sm font-mono text-slate-700 dark:text-slate-300 break-all">
                                t.me/{{ $botUsername }}?start={{ $business->slug }}
                            </code>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <button onclick="copyText('https://t.me/{{ $botUsername }}?start={{ $business->slug }}')"
                                class="flex items-center justify-center gap-2 py-2.5 text-sm font-medium text-sky-700 dark:text-sky-300 bg-sky-50 dark:bg-sky-900/30 hover:bg-sky-100 dark:hover:bg-sky-900/50 rounded-lg border border-sky-200 dark:border-sky-800 transition-colors">
                                <i class="fa-solid fa-copy text-sky-600 dark:text-sky-400"></i>
                                Копировать ссылку
                            </button>

                            <a href="https://t.me/{{ $botUsername }}?start={{ $business->slug }}" target="_blank"
                                class="flex items-center justify-center gap-2 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-300 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 rounded-lg border border-slate-200 dark:border-slate-700 transition-colors">
                                <i class="fa-solid fa-external-link text-slate-500 dark:text-slate-400"></i>
                                Открыть бота
                            </a>
                        </div>

                        <div class="text-xs text-slate-500 dark:text-slate-400 mt-3">
                            <i class="fa-solid fa-info-circle mr-1"></i>
                            Клиенты нажимают "START" для начала записи
                        </div>
                    </div>
                @else
                    <div class="text-center py-8">
                        <div
                            class="h-14 w-14 rounded-full bg-amber-50 dark:bg-amber-900/20 flex items-center justify-center mx-auto mb-4">
                            <i class="fa-solid fa-robot text-amber-500 dark:text-amber-400 text-lg"></i>
                        </div>
                        <h4 class="text-sm font-medium text-slate-800 dark:text-slate-200 mb-2">Упс. Уже чиним.</h4>
                        <p class="text-xs text-slate-600 dark:text-slate-400">
                            Функция временно недоступна. Работаем над этим!
                        </p>
                    </div>
                @endif
            </div>
        </div>


        <!-- Преимущества Telegram -->
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm p-5">
            <div class="flex items-center gap-3 mb-4">
                <i class="fa-solid fa-star text-amber-500 dark:text-amber-400 text-lg"></i>
                <h3 class="font-semibold text-slate-900 dark:text-white">Преимущества Telegram</h3>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                <div class="flex items-start gap-2 p-3 bg-slate-50 dark:bg-slate-800/50 rounded-lg">
                    <i class="fa-solid fa-bolt text-sky-500 dark:text-sky-400 mt-0.5"></i>
                    <div>
                        <h4 class="text-sm font-medium text-slate-800 dark:text-slate-200 mb-1">Мгновенные уведомления</h4>
                        <p class="text-xs text-slate-600 dark:text-slate-400">
                            Узнавайте о новых записях сразу
                        </p>
                    </div>
                </div>

                <div class="flex items-start gap-2 p-3 bg-slate-50 dark:bg-slate-800/50 rounded-lg">
                    <i class="fa-solid fa-mobile-screen text-sky-500 dark:text-sky-400 mt-0.5"></i>
                    <div>
                        <h4 class="text-sm font-medium text-slate-800 dark:text-slate-200 mb-1">Удобство для клиентов</h4>
                        <p class="text-xs text-slate-600 dark:text-slate-400">
                            Запись прямо в мессенджере
                        </p>
                    </div>
                </div>

                <div class="flex items-start gap-2 p-3 bg-slate-50 dark:bg-slate-800/50 rounded-lg">
                    <i class="fa-solid fa-robot text-sky-500 dark:text-sky-400 mt-0.5"></i>
                    <div>
                        <h4 class="text-sm font-medium text-slate-800 dark:text-slate-200 mb-1">Автоматизация</h4>
                        <p class="text-xs text-slate-600 dark:text-slate-400">
                            Бот отвечает на вопросы клиентов
                        </p>
                    </div>
                </div>

                <div class="flex items-start gap-2 p-3 bg-slate-50 dark:bg-slate-800/50 rounded-lg">
                    <i class="fa-solid fa-shield-halved text-sky-500 dark:text-sky-400 mt-0.5"></i>
                    <div>
                        <h4 class="text-sm font-medium text-slate-800 dark:text-slate-200 mb-1">Надежность</h4>
                        <p class="text-xs text-slate-600 dark:text-slate-400">
                            Стабильная работа и безопасность
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function copyText(text) {
            navigator.clipboard.writeText(text).then(() => {
                const button = event.target.closest('button');
                if (button) {
                    const originalHTML = button.innerHTML;
                    button.innerHTML = '<i class="fa-solid fa-check mr-1"></i>Скопировано';
                    button.classList.remove('text-indigo-600', 'dark:text-indigo-400', 'text-sky-600',
                        'dark:text-sky-400');
                    button.classList.add('text-green-600', 'dark:text-green-400');

                    setTimeout(() => {
                        button.innerHTML = originalHTML;
                        button.classList.remove('text-green-600', 'dark:text-green-400');

                        // Восстанавливаем оригинальный цвет
                        if (originalHTML.includes('indigo') || button.classList.contains(
                                'text-indigo-600')) {
                            button.classList.add('text-indigo-600', 'dark:text-indigo-400');
                        } else {
                            button.classList.add('text-sky-600', 'dark:text-sky-400');
                        }
                    }, 2000);
                }
            });
        }

        // Обработка переключателей
        document.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const container = this.parentElement;
                const track = container.querySelector('div:first-child');
                const thumb = container.querySelector('div:last-child');

                if (this.checked) {
                    track.classList.remove('bg-slate-300', 'dark:bg-slate-700');
                    track.classList.add('bg-indigo-600');
                    thumb.classList.remove('left-1');
                    thumb.classList.add('right-1');
                } else {
                    track.classList.add('bg-slate-300', 'dark:bg-slate-700');
                    track.classList.remove('bg-indigo-600');
                    thumb.classList.remove('right-1');
                    thumb.classList.add('left-1');
                }

                // Здесь можно добавить отправку AJAX запроса для сохранения настроек
                console.log(`Setting ${this.id} changed to: ${this.checked}`);
            });
        });
    </script>
@endsection
