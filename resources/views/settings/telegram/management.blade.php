@extends('layouts.panel')

@section('title', 'Управление Telegram ботом')

@section('content')
    <div class="space-y-6">
        <!-- Заголовок -->
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Telegram бот</h1>
            <p class="text-slate-600 dark:text-slate-400 mt-1">Настройки бота для автоматизации записи</p>
        </div>

        <!-- Информация о боте -->
        <div class="bg-white dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-700 overflow-hidden">
            @if ($bots->isEmpty())
                <!-- Пустое состояние -->
                <div class="p-8 text-center">
                    <div
                        class="w-16 h-16 bg-slate-100 dark:bg-slate-800 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fa-brands fa-telegram text-2xl text-slate-400 dark:text-slate-500"></i>
                    </div>
                    <h3 class="text-lg font-medium text-slate-900 dark:text-white mb-2">Бот не настроен</h3>
                    <p class="text-slate-600 dark:text-slate-400 mb-4">
                        Создайте Telegram бота для автоматизации записи клиентов.
                    </p>
                    <a href="{{ route('panel.telegram.management.create') }}"
                        class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg font-medium">
                        Создать бота
                    </a>
                </div>
            @else
                @php
                    $bot = $bots->first();
                @endphp

                <!-- Карточка бота -->
                <div class="p-4 md:p-6">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
                        <div class="flex items-center gap-3 md:gap-4">
                            <div
                                class="w-10 h-10 md:w-12 md:h-12 bg-indigo-100 dark:bg-indigo-900/30 rounded-lg flex items-center justify-center shrink-0">
                                <i class="fa-brands fa-telegram text-xl md:text-2xl text-indigo-600 dark:text-indigo-400"></i>
                            </div>
                            <div class="min-w-0 flex-1">
                                <h3 class="text-base md:text-lg font-semibold text-slate-900 dark:text-white truncate">{{ $bot->name }}</h3>
                                <p class="text-xs md:text-sm text-slate-500 dark:text-slate-400">ID: {{ $bot->id }}</p>
                            </div>
                        </div>
                        <a href="{{ route('panel.telegram.management.edit', $bot->id) }}"
                            class="bg-indigo-600 hover:bg-indigo-700 text-white px-3 md:px-4 py-2 rounded-lg font-medium flex items-center justify-center gap-2 text-sm md:text-base w-full sm:w-auto">
                            <i class="fa-solid fa-edit"></i>
                            <span class="hidden sm:inline">Редактировать</span>
                            <span class="sm:hidden">Изменить</span>
                        </a>
                    </div>

                    <!-- Информация о токене -->
                    <div class="bg-slate-50 dark:bg-slate-800/50 rounded-lg p-3 md:p-4 mb-3 md:mb-4">
                        <div>
                            <h4 class="text-xs md:text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Токен бота</h4>
                            <code class="text-xs md:text-sm font-mono text-slate-600 dark:text-slate-400 break-all">
                                {{ substr($bot->token, 0, 10) . str_repeat('*', max(0, strlen($bot->token) - 20)) . substr($bot->token, -10) }}
                            </code>
                        </div>
                    </div>

                    <!-- Webhook статус -->
                    <div class="bg-slate-50 dark:bg-slate-800/50 rounded-lg p-3 md:p-4 mb-3 md:mb-4">
                        <div class="flex items-center justify-between gap-4">
                            <div class="flex items-center gap-3">
                                <h4 class="text-xs md:text-sm font-medium text-slate-700 dark:text-slate-300">Webhook</h4>
                                @php
                                    $webhookStatus = $webhookStatuses[$bot->id] ?? ['status' => 'unknown', 'message' => 'Статус неизвестен'];
                                @endphp
                                @if($webhookStatus['status'] === 'connected')
                                    <span class="inline-flex items-center gap-1.5 px-2 py-1 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 text-xs font-medium rounded-full">
                                        <i class="fa-solid fa-circle-check text-[10px]"></i>
                                        {{ $webhookStatus['message'] }}
                                    </span>
                                @elseif($webhookStatus['status'] === 'not_set')
                                    <span class="inline-flex items-center gap-1.5 px-2 py-1 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 text-xs font-medium rounded-full">
                                        <i class="fa-solid fa-circle-xmark text-[10px]"></i>
                                        {{ $webhookStatus['message'] }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2 py-1 bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400 text-xs font-medium rounded-full">
                                        <i class="fa-solid fa-circle-xmark text-[10px]"></i>
                                        {{ $webhookStatus['message'] }}
                                    </span>
                                @endif
                            </div>
                            <div class="flex gap-2">
                                @if($webhookStatus['status'] === 'not_set')
                                    <form action="{{ route('panel.telegram.management.set-webhook', $bot->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit"
                                            class="bg-emerald-600 hover:bg-emerald-700 text-white px-3 py-1.5 rounded text-xs font-medium flex items-center gap-1.5 transition-colors">
                                            <i class="fa-solid fa-link text-[10px]"></i>
                                            <span class="hidden sm:inline">Установить</span>
                                            <span class="sm:hidden">Вкл</span>
                                        </button>
                                    </form>
                                @elseif($webhookStatus['status'] === 'connected')
                                    <form action="{{ route('panel.telegram.management.delete-webhook', $bot->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit"
                                            class="bg-red-600 hover:bg-red-700 text-white px-3 py-1.5 rounded text-xs font-medium flex items-center gap-1.5 transition-colors"
                                            onclick="return confirm('Вы уверены, что хотите удалить webhook?')">
                                            <i class="fa-solid fa-link-slash text-[10px]"></i>
                                            <span class="hidden sm:inline">Удалить</span>
                                            <span class="sm:hidden">Выкл</span>
                                        </button>
                                    </form>
                                @else
                                    <form action="{{ route('panel.telegram.management.set-webhook', $bot->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit"
                                            class="bg-emerald-600 hover:bg-emerald-700 text-white px-2 py-1.5 rounded text-xs font-medium flex items-center gap-1 transition-colors"
                                            title="Установить webhook">
                                            <i class="fa-solid fa-link text-[10px]"></i>
                                        </button>
                                    </form>
                                    <form action="{{ route('panel.telegram.management.delete-webhook', $bot->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit"
                                            class="bg-red-600 hover:bg-red-700 text-white px-2 py-1.5 rounded text-xs font-medium flex items-center gap-1 transition-colors"
                                            onclick="return confirm('Вы уверены, что хотите удалить webhook?')"
                                            title="Удалить webhook">
                                            <i class="fa-solid fa-link-slash text-[10px]"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Статистика -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 md:gap-4">
                        <div class="bg-slate-50 dark:bg-slate-800/50 rounded-lg p-3 md:p-4 text-center">
                            <div class="text-xl md:text-2xl font-bold text-slate-900 dark:text-white">{{ $bot->chats()->count() }}</div>
                            <div class="text-xs md:text-sm text-slate-500 dark:text-slate-400">Активных чатов</div>
                        </div>
                        <div class="bg-slate-50 dark:bg-slate-800/50 rounded-lg p-3 md:p-4 text-center">
                            <div class="text-xl md:text-2xl font-bold text-slate-900 dark:text-white">{{ $bots->count() }}</div>
                            <div class="text-xs md:text-sm text-slate-500 dark:text-slate-400">Всего ботов</div>
                        </div>
                        <div class="bg-slate-50 dark:bg-slate-800/50 rounded-lg p-3 md:p-4 text-center">
                            <div class="text-xl md:text-2xl font-bold text-slate-900 dark:text-white">{{ $bot->created_at->format('d.m.Y') }}</div>
                            <div class="text-xs md:text-sm text-slate-500 dark:text-slate-400">Дата добавления</div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

@endsection
