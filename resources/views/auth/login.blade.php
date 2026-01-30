@extends('layouts.auth')

@section('title', 'Вход в аккаунт')

@section('content')
    <!-- Основной контейнер с улучшенным дизайном -->
    <div class="max-w-sm w-full mx-auto">
        <!-- Заголовок страницы -->
        <div class="text-center mb-6">
            <h1 class="text-xl md:text-2xl font-bold text-slate-800 dark:text-white mb-2">Добро пожаловать</h1>
        </div>

        <!-- Карточка формы с улучшенным дизайном -->
        <div class="rounded-xl border border-slate-200 bg-white/80 p-5 md:p-6 shadow-md dark:border-slate-800 dark:bg-slate-900/80 animate-fade-in-up">
            <form method="POST" action="{{ route('login') }}" class="space-y-4" id="loginForm">
                @csrf

                <!-- Email -->
                <div class="space-y-2">
                    <label for="email" class="flex items-center gap-2 text-sm font-medium text-slate-700 dark:text-slate-300">
                        <x-icon name="envelope" size="sm" class="text-indigo-500 dark:text-indigo-400" />
                        <span>Email</span>
                    </label>
                    <div class="relative">
                        <input
                            type="email"
                            id="email"
                            name="email"
                            value="{{ old('email') }}"
                            required
                            autocomplete="email"
                            autofocus
                            class="w-full px-3 py-2 text-sm rounded-lg border-2 {{ $errors->has('email') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-200 dark:border-slate-700 focus:ring-indigo-500 focus:border-indigo-500' }} bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:border-transparent transition-all duration-200"/>
                        @error('email')
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                <x-icon name="exclamation-circle" size="sm" class="text-rose-500" />
                            </div>
                        @enderror
                    </div>
                    @error('email')
                        <p class="mt-1 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Пароль -->
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <label for="password" class="flex items-center gap-2 text-sm font-medium text-slate-700 dark:text-slate-300">
                            <x-icon name="lock-closed" size="sm" class="text-indigo-500 dark:text-indigo-400" />
                            <span>Пароль</span>
                        </label>
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="text-xs text-indigo-500 hover:text-indigo-600 dark:text-indigo-400 dark:hover:text-indigo-300 transition-colors font-medium">
                                Забыли пароль?
                            </a>
                        @endif
                    </div>
                    <div class="relative">
                        <input
                            type="password"
                            id="password"
                            name="password"
                            required
                            autocomplete="current-password"
                            class="w-full px-3 py-2 text-sm rounded-lg border-2 {{ $errors->has('password') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-200 dark:border-slate-700 focus:ring-indigo-500 focus:border-indigo-500' }} bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:border-transparent transition-all duration-200"/>
                        @error('password')
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                <x-icon name="exclamation-circle" size="sm" class="text-rose-500" />
                            </div>
                        @enderror
                    </div>
                    @error('password')
                        <p class="mt-1 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Запомнить меня и кнопка входа -->
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <input
                                id="remember"
                                name="remember"
                                type="checkbox"
                                class="h-4 w-4 text-indigo-500 focus:ring-indigo-500 border-slate-300 rounded transition-colors dark:border-slate-700 dark:bg-slate-800"
                                {{ old('remember') ? 'checked' : '' }}
                            >
                            <label for="remember" class="ml-2 block text-sm text-slate-600 dark:text-slate-400">
                                Запомнить меня
                            </label>
                        </div>
                    </div>

                    <!-- Кнопка входа с улучшенным дизайном -->
                    <button
                        type="submit"
                        class="w-full inline-flex items-center justify-center gap-2 rounded-lg bg-gradient-to-r from-indigo-500 to-indigo-600 px-4 py-2.5 text-sm font-medium text-white shadow-sm shadow-indigo-500/30 hover:from-indigo-600 hover:to-indigo-700 active:from-indigo-700 active:to-indigo-800 transition-all duration-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-slate-900 transform hover:scale-[1.01] active:scale-[0.99]"
                    >
                        <span>Войти в аккаунт</span>
                        <x-icon name="arrow-right-on-rectangle" size="sm" />
                    </button>
                </div>
            </form>

            <!-- OAuth кнопки с улучшенным дизайном -->
            <x-oauth-buttons text="Войти через" />

            <!-- Нижний разделитель и ссылка на регистрацию -->
            <div class="mt-6 pt-4 border-t border-slate-200 dark:border-slate-800">
                <p class="text-center text-sm text-slate-600 dark:text-slate-400">
                    Еще нет аккаунта?
                    <a href="{{ route('register') }}" class="text-indigo-500 hover:text-indigo-600 dark:text-indigo-400 dark:hover:text-indigo-300 transition-colors font-medium underline underline-offset-2">
                        Зарегистрироваться
                    </a>
                </p>
            </div>
        </div>
    </div>

    @push('scripts')
        <x-auth-form-scripts formId="loginForm" submitText="Вход..." />
    @endpush
@endsection
