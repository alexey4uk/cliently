@extends('layouts.auth')

@section('title', 'Подтверждение пароля')

@section('content')
    <!-- Основной контейнер с улучшенным дизайном -->
    <div class="max-w-sm w-full mx-auto">
        <!-- Заголовок страницы -->
        <div class="text-center mb-6">
            <h1 class="text-xl md:text-2xl font-bold text-slate-800 dark:text-white mb-2">Подтверждение пароля</h1>
            <p class="text-slate-500 dark:text-slate-400 text-sm">Пожалуйста, подтвердите свой пароль</p>
        </div>

        <!-- Карточка формы с улучшенным дизайном -->
        <div class="rounded-xl border border-slate-200 bg-white/80 p-5 md:p-6 shadow-md dark:border-slate-800 dark:bg-slate-900/80 animate-fade-in-up">
            <form method="POST" action="{{ route('password.confirm') }}" class="space-y-4" id="confirmPasswordForm">
                @csrf

                <!-- Password -->
                <div class="space-y-2">
                    <label for="password" class="flex items-center gap-2 text-sm font-medium text-slate-700 dark:text-slate-300">
                        <x-icon name="lock-closed" size="sm" class="text-indigo-500 dark:text-indigo-400" />
                        <span>Пароль</span>
                        <span class="text-rose-500 dark:text-rose-400 ml-1">*</span>
                    </label>
                    <div class="relative">
                        <input
                            type="password"
                            id="password"
                            name="password"
                            required
                            autocomplete="current-password"
                            autofocus
                            class="w-full px-3 py-2 text-sm rounded-lg border-2 {{ $errors->has('password') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-200 dark:border-slate-700 focus:ring-indigo-500 focus:border-indigo-500' }} bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:border-transparent transition-all duration-200"
                            placeholder="••••••••"
                        />
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

                <!-- Кнопка подтверждения -->
                <div class="pt-1">
                    <button
                        type="submit"
                        class="w-full inline-flex items-center justify-center gap-2 rounded-lg bg-gradient-to-r from-indigo-500 to-indigo-600 px-4 py-2.5 text-sm font-medium text-white shadow-sm shadow-indigo-500/30 hover:from-indigo-600 hover:to-indigo-700 active:from-indigo-700 active:to-indigo-800 transition-all duration-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-slate-900 transform hover:scale-[1.01] active:scale-[0.99]"
                    >
                        <span>Подтвердить</span>
                        <x-icon name="check" size="sm" />
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <x-auth-form-scripts formId="confirmPasswordForm" submitText="Проверка..." />
    @endpush
@endsection
