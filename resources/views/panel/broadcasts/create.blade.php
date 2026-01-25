@extends('layouts.panel')

@section('title', 'Создать рассылку')

@section('content')
    <div class="space-y-6">
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-6">
            <div class="flex items-center gap-4 mb-6">
                <div class="h-12 w-12 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center shadow-sm">
                    <i class="fa-solid fa-paper-plane text-white text-lg"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Создать рассылку</h1>
                    <p class="text-slate-600 dark:text-slate-400 mt-1">Отправка уведомлений владельцам или всем пользователям системы</p>
                </div>
            </div>

            @if ($errors->any())
                <div class="mb-6 p-4 bg-red-50 dark:bg-red-500/20 border border-red-200 dark:border-red-500/30 rounded-lg">
                    <div class="flex items-center gap-3 mb-2">
                        <i class="fa-solid fa-exclamation-circle text-red-600 dark:text-red-400"></i>
                        <span class="font-medium text-red-800 dark:text-red-300">Ошибки валидации</span>
                    </div>
                    <ul class="list-disc list-inside text-sm text-red-700 dark:text-red-400 space-y-1">
                        @foreach ($errors->all() as $e)
                            <li>{{ $e }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('panel.broadcasts.store') }}" method="POST" class="space-y-6">
                @csrf

                <div>
                    <label for="title" class="block text-sm font-semibold text-slate-900 dark:text-white mb-2">Заголовок</label>
                    <input type="text" name="title" id="title" value="{{ old('title') }}" required maxlength="255"
                           class="w-full rounded-lg border-slate-300 dark:border-slate-700 dark:bg-slate-800 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-slate-900 dark:text-white"
                           placeholder="Например: Техработы 1 февраля">
                </div>

                <div>
                    <label for="message" class="block text-sm font-semibold text-slate-900 dark:text-white mb-2">Текст</label>
                    <textarea name="message" id="message" rows="5" required maxlength="10000"
                              class="w-full rounded-lg border-slate-300 dark:border-slate-700 dark:bg-slate-800 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-slate-900 dark:text-white"
                              placeholder="Текст рассылки...">{{ old('message') }}</textarea>
                </div>

                <div>
                    <label for="target" class="block text-sm font-semibold text-slate-900 dark:text-white mb-2">Кому</label>
                    <select name="target" id="target" required
                            class="w-full rounded-lg border-slate-300 dark:border-slate-700 dark:bg-slate-800 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-slate-900 dark:text-white">
                        <option value="owners" {{ old('target') === 'owners' ? 'selected' : '' }}>Только владельцы</option>
                        <option value="all" {{ old('target') === 'all' ? 'selected' : '' }}>Все пользователи системы</option>
                    </select>
                </div>

                <div>
                    <span class="block text-sm font-semibold text-slate-900 dark:text-white mb-3">Каналы</span>
                    <div class="flex flex-wrap gap-4">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="channels[]" value="system"
                                   {{ in_array('system', old('channels', [])) ? 'checked' : '' }}
                                   class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 dark:bg-slate-800 dark:border-slate-600">
                            <span class="text-sm text-slate-700 dark:text-slate-300">В уведомлениях (колокольчик)</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="channels[]" value="email"
                                   {{ in_array('email', old('channels', [])) ? 'checked' : '' }}
                                   class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 dark:bg-slate-800 dark:border-slate-600">
                            <span class="text-sm text-slate-700 dark:text-slate-300">Email (только с подтверждённой почтой)</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="channels[]" value="telegram"
                                   {{ in_array('telegram', old('channels', [])) ? 'checked' : '' }}
                                   class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 dark:bg-slate-800 dark:border-slate-600">
                            <span class="text-sm text-slate-700 dark:text-slate-300">Telegram (только у кого привязан)</span>
                        </label>
                    </div>
                    <p class="mt-2 text-xs text-slate-500 dark:text-slate-400">Выберите минимум один канал</p>
                </div>

                <div class="flex items-center gap-4 pt-2">
                    <button type="submit"
                            class="inline-flex items-center gap-2 px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-sm font-medium shadow-sm hover:shadow-md transition-all">
                        <i class="fa-solid fa-paper-plane"></i>
                        <span>Отправить</span>
                    </button>
                    <a href="{{ route('panel.broadcasts.index') }}"
                       class="text-sm font-medium text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white transition-colors">
                        Отмена
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection
