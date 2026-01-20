@extends('layouts.panel')

@section('title', 'Редактировать Telegram бота')

@section('content')
    <div class="space-y-6">
        <!-- Заголовок -->
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Редактировать Telegram бота</h1>
            <p class="text-slate-600 dark:text-slate-400 mt-1">Обновление настроек Telegram бота</p>
        </div>

        <!-- Форма редактирования бота -->
        <div class="bg-white dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-700 overflow-hidden">
            <form action="{{ route('panel.telegram.management.update', $bot->id) }}" method="POST" class="p-6 space-y-6">
                @csrf
                @method('PATCH')

                <!-- Имя бота -->
                <div>
                    <label for="name" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                        Имя бота <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fa-solid fa-robot text-slate-400"></i>
                        </div>
                        <input type="text" id="name" name="name" required value="{{ old('name', $bot->name) }}"
                            class="w-full pl-10 pr-3 py-3 border border-slate-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-800 text-slate-900 dark:text-white placeholder-slate-500 dark:placeholder-slate-400 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                            placeholder="Мой салон красоты">
                    </div>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-2">
                        Уникальное имя для идентификации бота в системе
                    </p>
                    @error('name')
                        <div class="mt-2 text-sm text-red-600 dark:text-red-400 flex items-center gap-1">
                            <i class="fa-solid fa-exclamation-circle"></i>
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <!-- Токен бота -->
                <div>
                    <label for="token" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                        Токен бота <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fa-solid fa-key text-slate-400"></i>
                        </div>
                        <input type="text" id="token" name="token" required value="{{ old('token', $bot->token) }}"
                            class="w-full pl-10 pr-3 py-3 border border-slate-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-800 text-slate-900 dark:text-white placeholder-slate-500 dark:placeholder-slate-400 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors font-mono text-sm"
                            placeholder="123456:ABC-DEF1234ghIkl-zyx57W2v1u123ew11">
                    </div>
                    <div class="flex items-start gap-2 mt-2">
                        <i class="fa-solid fa-info-circle text-blue-500 mt-0.5 flex-shrink-0"></i>
                        <p class="text-xs text-slate-500 dark:text-slate-400">
                            Получите токен у <a href="https://t.me/botfather" target="_blank"
                                class="text-indigo-600 dark:text-indigo-400 hover:underline">@BotFather</a> в Telegram
                        </p>
                    </div>
                    @error('token')
                        <div class="mt-2 text-sm text-red-600 dark:text-red-400 flex items-center gap-1">
                            <i class="fa-solid fa-exclamation-circle"></i>
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <!-- Кнопки -->
                <div class="flex gap-3 pt-6 border-t border-slate-200 dark:border-slate-700">
                    <a href="{{ route('panel.telegram.management') }}"
                        class="flex-1 px-4 py-3 text-slate-600 dark:text-slate-400 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 font-medium rounded-lg transition-colors text-center">
                        Отмена
                    </a>
                    <button type="submit"
                        class="flex-1 px-4 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-medium rounded-lg transition-colors">
                        <i class="fa-solid fa-save mr-2"></i>
                        Сохранить изменения
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
