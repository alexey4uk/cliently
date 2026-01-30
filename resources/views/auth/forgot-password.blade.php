@extends('layouts.auth')

@section('title', 'Сброс пароля')

@section('content')
    <!-- Основной контейнер с улучшенным дизайном -->
    <div class="max-w-sm w-full mx-auto">
        <!-- Заголовок страницы -->
        <div class="text-center mb-6">
            <h1 class="text-xl md:text-2xl font-bold text-slate-800 dark:text-white mb-2">Сброс пароля</h1>
            <p class="text-slate-500 dark:text-slate-400 text-sm">Введите email для восстановления доступа</p>
        </div>

        <!-- Карточка формы с улучшенным дизайном -->
        <div class="rounded-xl border border-slate-200 bg-white/80 p-5 md:p-6 shadow-md dark:border-slate-800 dark:bg-slate-900/80 animate-fade-in-up">
            <form method="POST" action="{{ route('password.email') }}" class="space-y-4" id="passwordResetForm">
                @csrf

                <!-- Уведомление об успешной отправке -->
                @if (session('status'))
                    <div class="rounded-lg bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 p-3">
                        <div class="flex items-center gap-2">
                            <x-icon name="check-circle" size="sm" class="text-emerald-600 dark:text-emerald-400" />
                            <p class="text-sm text-emerald-700 dark:text-emerald-300">{{ session('status') }}</p>
                        </div>
                    </div>
                @endif

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

                <!-- Кнопка отправки -->
                <div class="pt-1">
                    <button
                        type="submit"
                        class="w-full inline-flex items-center justify-center gap-2 rounded-lg bg-gradient-to-r from-indigo-500 to-indigo-600 px-4 py-2.5 text-sm font-medium text-white shadow-sm shadow-indigo-500/30 hover:from-indigo-600 hover:to-indigo-700 active:from-indigo-700 active:to-indigo-800 transition-all duration-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-slate-900 transform hover:scale-[1.01] active:scale-[0.99]"
                    >
                        <span>Отправить ссылку</span>
                    </button>
                </div>
            </form>

            <!-- Разделитель -->
            <div class="mt-6 pt-4 border-t border-slate-200 dark:border-slate-800">
                <p class="text-center text-sm text-slate-600 dark:text-slate-400">
                    Вспомнили пароль?
                    <a href="{{ route('login') }}" class="text-indigo-500 hover:text-indigo-600 dark:text-indigo-400 dark:hover:text-indigo-300 transition-colors font-medium underline underline-offset-2">
                        Войти
                    </a>
                </p>
            </div>
        </div>
    </div>

    @push('scripts')
        <x-auth-form-scripts formId="passwordResetForm" submitText="Отправка..." />
    @endpush
@endsection
