@extends('layouts.user')

@section('title', 'Добавление клиента - Cliently')
@section('page-title', 'Новый клиент')
@section('page-description', 'Добавление нового клиента в базу')

@push('breadcrumbs')
    <x-breadcrumbs :items="[
        ['title' => 'Клиенты', 'url' => route('clients.index')],
        ['title' => 'Добавление клиента', 'url' => null],
    ]" />
@endpush

@section('content')

    <!-- Заголовок страницы -->
    <div
        class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 border-b border-slate-200 dark:border-slate-800">
        <div class="flex items-center gap-3 md:gap-4">
            <!-- Аватар -->
            <div
                class="h-16 w-16 md:h-20 md:w-20 rounded-xl bg-gradient-to-br from-indigo-500 via-indigo-600 to-purple-600 flex items-center justify-center text-xl md:text-2xl font-bold text-white shadow-lg flex-shrink-0">
                <i class="fa-solid fa-user-plus"></i>
            </div>
            <div class="min-w-0 flex-1">
                <h1 class="text-xl md:text-2xl font-semibold text-slate-900 dark:text-white mb-1">
                    Новый клиент
                </h1>
                <p class="text-sm text-slate-500 dark:text-slate-400">
                    Заполните информацию о новом клиенте
                </p>
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('clients.store') }}" class="space-y-6">
        @csrf

        <div class="space-y-6">
            <!-- Основная информация -->
            <div
                class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
                <div
                    class="px-4 md:px-6 py-4 border-b border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50">
                    <h2 class="text-sm font-semibold text-slate-900 dark:text-white flex items-center gap-2">
                        <div
                            class="w-8 h-8 rounded-lg bg-indigo-100 dark:bg-indigo-500/20 flex items-center justify-center">
                            <i class="fa-solid fa-user text-indigo-600 dark:text-indigo-400 text-sm"></i>
                        </div>
                        <span>Информация о клиенте</span>
                    </h2>
                </div>
                <div class="p-4 md:p-6 space-y-5">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div class="space-y-1.5">
                            <label for="first_name" class="block text-xs font-semibold text-slate-700 dark:text-slate-300">
                                Имя <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" id="first_name" name="first_name" required value="{{ old('first_name') }}"
                                class="w-full px-3 py-2.5 text-sm rounded-lg border {{ $errors->has('first_name') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-indigo-500' }} bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:border-transparent transition-all duration-150"
                                autofocus placeholder="Введите имя">
                            @error('first_name')
                                <p class="mt-1 text-xs text-rose-600 dark:text-rose-400 flex items-center gap-1">
                                    <i class="fa-solid fa-circle-exclamation text-xs"></i>
                                    <span>{{ $message }}</span>
                                </p>
                            @enderror
                        </div>

                        <div class="space-y-1.5">
                            <label for="last_name" class="block text-xs font-semibold text-slate-700 dark:text-slate-300">
                                Фамилия
                            </label>
                            <input type="text" id="last_name" name="last_name" value="{{ old('last_name') }}"
                                class="w-full px-3 py-2.5 text-sm rounded-lg border {{ $errors->has('last_name') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-indigo-500' }} bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:border-transparent transition-all duration-150"
                                placeholder="Введите фамилию">
                            @error('last_name')
                                <p class="mt-1 text-xs text-rose-600 dark:text-rose-400 flex items-center gap-1">
                                    <i class="fa-solid fa-circle-exclamation text-xs"></i>
                                    <span>{{ $message }}</span>
                                </p>
                            @enderror
                        </div>

                        <div class="space-y-1.5">
                            <label for="phone" class="block text-xs font-semibold text-slate-700 dark:text-slate-300">
                                Телефон <span class="text-rose-500">*</span>
                            </label>
                            <input type="tel" id="phone" name="phone" required value="{{ old('phone') }}"
                                class="w-full px-3 py-2.5 text-sm rounded-lg border {{ $errors->has('phone') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-indigo-500' }} bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:border-transparent transition-all duration-150"
                                placeholder="+375XXXXXXXXX">
                            @error('phone')
                                <p class="mt-1 text-xs text-rose-600 dark:text-rose-400 flex items-center gap-1">
                                    <i class="fa-solid fa-circle-exclamation text-xs"></i>
                                    <span>{{ $message }}</span>
                                </p>
                            @enderror
                        </div>

                        <div class="space-y-1.5">
                            <label for="email" class="block text-xs font-semibold text-slate-700 dark:text-slate-300">
                                Email
                            </label>
                            <input type="email" id="email" name="email" value="{{ old('email') }}"
                                class="w-full px-3 py-2.5 text-sm rounded-lg border {{ $errors->has('email') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-indigo-500' }} bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:border-transparent transition-all duration-150"
                                placeholder="client@example.com">
                            @error('email')
                                <p class="mt-1 text-xs text-rose-600 dark:text-rose-400 flex items-center gap-1">
                                    <i class="fa-solid fa-circle-exclamation text-xs"></i>
                                    <span>{{ $message }}</span>
                                </p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>


        </div>

        <!-- Кнопки действий -->
        <div class="flex items-center justify-between pt-4 border-t border-slate-200 dark:border-slate-800">
            <a href="{{ route('clients.index') }}"
                class="px-4 py-2 text-sm font-semibold text-slate-700 dark:text-slate-300 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-700 transition-all duration-150">
                Отмена
            </a>
            <button type="submit"
                class="px-4 py-2 text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 rounded-lg shadow-sm hover:shadow transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-slate-900">
                Создать клиента
            </button>
        </div>
    </form>

@endsection
