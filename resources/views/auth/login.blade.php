<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }} - Вход</title>
    <!-- Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

</head>
<body class="h-full bg-gray-50 dark:bg-gray-900 transition-colors duration-300">
<div class="min-h-full flex items-center justify-center py-8 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <!-- Заголовок -->
        <div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900 dark:text-white">
                Вход в аккаунт
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600 dark:text-gray-400">
                Введите свои данные для входа
            </p>
        </div>

        <!-- Форма -->
        <form class="mt-8 space-y-6" method="POST" action="{{ route('login') }}" id="loginForm">
            @csrf

            <div class="space-y-4">
                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Email адрес *
                    </label>
                    <input
                        id="email"
                        name="email"
                        type="email"
                        autocomplete="email"
                        required
                        class="appearance-none relative block w-full px-3 py-3 border border-gray-300 dark:border-gray-700 placeholder-gray-500 dark:placeholder-gray-400 text-gray-900 dark:text-white dark:bg-gray-800 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm transition-colors duration-300 @error('email') border-red-500 @enderror"
                        placeholder="example@mail.com"
                        value="{{ old('email') }}"
                    >
                    @error('email')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Пароль -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Пароль *
                    </label>
                    <input
                        id="password"
                        name="password"
                        type="password"
                        autocomplete="current-password"
                        required
                        class="appearance-none relative block w-full px-3 py-3 border border-gray-300 dark:border-gray-700 placeholder-gray-500 dark:placeholder-gray-400 text-gray-900 dark:text-white dark:bg-gray-800 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm transition-colors duration-300 @error('password') border-red-500 @enderror"
                        placeholder="Введите ваш пароль"
                    >
                    @error('password')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Запомнить меня -->
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input
                            id="remember"
                            name="remember"
                            type="checkbox"
                            class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 dark:border-gray-700 rounded transition-colors duration-300"
                            {{ old('remember') ? 'checked' : '' }}
                        >
                        <label for="remember" class="ml-2 block text-sm text-gray-900 dark:text-gray-300">
                            Запомнить меня
                        </label>
                    </div>

                    @if (Route::has('password.request'))
                        <div class="text-sm">
                            <a href="{{ route('password.request') }}" class="font-medium text-indigo-600 hover:text-indigo-500 dark:text-indigo-400 dark:hover:text-indigo-300 transition-colors duration-300">
                                Забыли пароль?
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Общие ошибки -->
            @error('login')
            <div class="rounded-md bg-red-50 dark:bg-red-900/20 p-4">
                <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            </div>
            @enderror

            <!-- Кнопка входа -->
            <div>
                <button type="submit" class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-900 transition-colors duration-300">
                        <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                            <svg class="h-5 w-5 text-indigo-500 group-hover:text-indigo-400 transition-colors duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                            </svg>
                        </span>
                    Войти
                </button>
            </div>

            <!-- Ссылка на регистрацию -->
            <div class="text-center">
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    Еще нет аккаунта?
                    <a href="{{ route('register') }}" class="font-medium text-indigo-600 hover:text-indigo-500 dark:text-indigo-400 dark:hover:text-indigo-300 transition-colors duration-300">
                        Зарегистрироваться
                    </a>
                </p>
            </div>
        </form>
    </div>
</div>
</body>
</html>
