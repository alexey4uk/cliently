@extends('layouts.panel')

@section('title', 'Управление Telegram ботами')

@section('content')
    <div class="space-y-6">
        <!-- Заголовок и кнопка -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Telegram боты</h1>
                <p class="text-slate-600 dark:text-slate-400 mt-1">Управление ботами для автоматизации записи</p>
            </div>
            <a href="{{ route('panel.telegram.management.create') }}"
                class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg font-medium flex items-center gap-2">
                <i class="fa-solid fa-plus"></i>
                <span>Добавить бота</span>
            </a>
        </div>

        <!-- Список ботов -->
        <div class="bg-white dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-700 overflow-hidden">
            @if ($bots->isEmpty())
                <!-- Пустое состояние -->
                <div class="p-8 text-center">
                    <div
                        class="w-16 h-16 bg-slate-100 dark:bg-slate-800 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fa-brands fa-telegram text-2xl text-slate-400 dark:text-slate-500"></i>
                    </div>
                    <h3 class="text-lg font-medium text-slate-900 dark:text-white mb-2">Нет ботов</h3>
                    <p class="text-slate-600 dark:text-slate-400 mb-4">
                        Создайте первого Telegram бота для автоматизации записи клиентов.
                    </p>
                    <a href="{{ route('panel.telegram.management.create') }}"
                        class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg font-medium">
                        Создать бота
                    </a>
                </div>
            @else
                <!-- Таблица ботов -->
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-slate-50 dark:bg-slate-800 border-b border-slate-200 dark:border-slate-700">
                            <tr>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                    Бот</th>
                                <th
                                    class="px-6 py-3 text-right text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                    Действия</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                            @foreach ($bots as $currentBot)
                                <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50"
                                    data-bot-id="{{ $currentBot->id }}">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="w-8 h-8 bg-indigo-100 dark:bg-indigo-900/30 rounded-lg flex items-center justify-center">
                                                <i class="fa-brands fa-telegram text-indigo-600 dark:text-indigo-400"></i>
                                            </div>
                                            <div>
                                                <div class="font-medium text-slate-900 dark:text-white">
                                                    {{ $currentBot->name }}</div>
                                                <div class="text-sm text-slate-500 dark:text-slate-400">ID:
                                                    {{ $currentBot->id }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <a href="{{ route('panel.telegram.management.edit', $currentBot->id) }}"
                                                class="text-slate-600 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-300 p-1"
                                                title="Редактировать">
                                                <i class="fa-solid fa-edit"></i>
                                            </a>
                                            <form action="{{ route('panel.telegram.management.destroy', $currentBot->id) }}"
                                                method="POST"
                                                onsubmit="return confirm('Вы уверены, что хотите удалить этого бота?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300 p-1"
                                                    title="Удалить">
                                                    <i class="fa-solid fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
@endsection
