<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - Сброс пароля</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="h-full bg-gray-50 dark:bg-gray-900 transition-colors duration-300">
<div class="min-h-full flex items-center justify-center py-8 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <!-- Заголовок -->
        <div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900 dark:text-white">
                Сброс пароля
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600 dark:text-gray-400">
                Введите ваш email для отправки ссылки сброса пароля
            </p>
        </div>

        <!-- Форма -->
        <form class="mt-8 space-y-6" method="POST" action="{{ route('password.email') }}" id="passwordResetForm">
            @csrf

            @if (session('status'))
                <div class="rounded-md bg-green-50 dark:bg-green-900/20 p-4">
                    <p class="text-sm text-green-600 dark:text-green-400">{{ session('status') }}</p>
                </div>
            @endif

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
            </div>

            <!-- Кнопка отправки -->
            <div>
                <button type="submit" class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-900 transition-colors duration-300">
                        <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                            <svg class="h-5 w-5 text-indigo-500 group-hover:text-indigo-400 transition-colors duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                        </span>
                    Отправить ссылку для сброса
                </button>
            </div>

            <!-- Ссылки навигации -->
            <div class="text-center space-y-2">
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    Вспомнили пароль?
                    <a href="{{ route('login') }}" class="font-medium text-indigo-600 hover:text-indigo-500 dark:text-indigo-400 dark:hover:text-indigo-300 transition-colors duration-300">
                        Войти
                    </a>
                </p>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    Нет аккаунта?
                    <a href="{{ route('register') }}" class="font-medium text-indigo-600 hover:text-indigo-500 dark:text-indigo-400 dark:hover:text-indigo-300 transition-colors duration-300">
                        Зарегистрироваться
                    </a>
                </p>
            </div>
        </form>
    </div>
</div>

<!-- Скрипт для темы -->
<script>
    window.matchMedia('(prefers-color-scheme: dark)');
</script>
</body>
</html>
