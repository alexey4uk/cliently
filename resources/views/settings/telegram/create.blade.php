@extends('layouts.panel')

@section('title', 'Создать Telegram бота')

@section('content')
    <div class="space-y-6">
        <!-- Заголовок -->
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Создать Telegram бота</h1>
        </div>

        <!-- Форма создания бота -->
        <div class="bg-white dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-700 overflow-hidden">
            <div class="p-4 md:p-6">
                <form action="{{ route('panel.telegram.management.store') }}" method="POST" class="space-y-4">
                    @csrf

                    <!-- Имя бота -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                            Имя бота <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="name" name="name" required
                            class="w-full px-3 py-2 border border-slate-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-800 text-slate-900 dark:text-white placeholder-slate-500 dark:placeholder-slate-400 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                            placeholder="Мой салон красоты">
                        @error('name')
                            <div class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Токен бота -->
                    <div>
                        <label for="token" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                            Токен бота <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="token" name="token" required
                            class="w-full px-3 py-2 border border-slate-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-800 text-slate-900 dark:text-white placeholder-slate-500 dark:placeholder-slate-400 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors font-mono text-sm"
                            placeholder="123456:ABC-DEF1234ghIkl-zyx57W2v1u123ew11">
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                            Получите токен у <a href="https://t.me/botfather" target="_blank" class="text-indigo-600 dark:text-indigo-400 hover:underline">@BotFather</a>
                        </p>
                        @error('token')
                            <div class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Кнопки -->
                    <div class="flex gap-3 pt-4 border-t border-slate-200 dark:border-slate-700">
                        <a href="{{ route('panel.telegram.management') }}"
                            class="flex-1 px-4 py-2 text-slate-600 dark:text-slate-400 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 font-medium rounded-lg transition-colors text-center">
                            Отмена
                        </a>
                        <button type="submit"
                            class="flex-1 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg transition-colors">
                            Создать бота
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
