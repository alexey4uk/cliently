@extends('layouts.auth')

@section('title', 'Вход в аккаунт')

@section('content')
    <!-- Форма входа -->
    <div class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900 animate-fade-in-up">
        <form method="POST" action="{{ route('login') }}" class="space-y-5" id="loginForm">
            @csrf

            <!-- Email -->
            <div>
                <label for="email" class="flex items-center gap-1.5 md:gap-2 text-base md:text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                    <x-icon name="envelope" size="sm" class="text-[#6366F1] dark:text-[#818CF8]" />
                    <span>Email адрес*</span>
                </label>
                <input
                    type="email" 
                    id="email"
                    name="email"
                    value="{{ old('email') }}"
                    required
                    autocomplete="email"
                    autofocus
                    class="w-full px-2.5 md:px-3 py-2 md:py-2.5 text-base md:text-sm rounded-md border {{ $errors->has('email') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-[#6366F1]' }} bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:border-transparent transition-colors"
                />
                @error('email')
                    <p class="mt-1.5 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Пароль -->
            <div>
                <div class="flex items-center justify-between mb-2">
                    <label for="password" class="flex items-center gap-1.5 md:gap-2 text-base md:text-sm font-medium text-slate-700 dark:text-slate-300">
                        <x-icon name="lock-closed" size="sm" class="text-[#6366F1] dark:text-[#818CF8]" />
                        <span>Пароль*</span>
                    </label>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="text-xs text-[#6366F1] hover:text-[#4F46E5] dark:text-[#818CF8] dark:hover:text-[#6366F1] transition-colors">
                            Забыли?
                        </a>
                    @endif
                </div>
                <input
                    type="password" 
                    id="password"
                    name="password"
                    required
                    autocomplete="current-password"
                    class="w-full px-2.5 md:px-3 py-2 md:py-2.5 text-base md:text-sm rounded-md border {{ $errors->has('password') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-[#6366F1]' }} bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:border-transparent transition-colors"
                />
                @error('password')
                    <p class="mt-1.5 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Запомнить меня -->
            <div class="flex items-center">
                <input
                    id="remember"
                    name="remember"
                    type="checkbox"
                    class="h-4 w-4 text-[#6366F1] focus:ring-[#6366F1] border-slate-300 rounded transition-colors dark:border-slate-700 dark:bg-slate-800"
                    {{ old('remember') ? 'checked' : '' }}
                >
                <label for="remember" class="ml-2 block text-base md:text-sm text-slate-600 dark:text-slate-400">
                    Запомнить меня
                </label>
            </div>

            <!-- Кнопка входа -->
            <div class="pt-2">
                <button
                    type="submit"
                    class="w-full inline-flex items-center justify-center gap-2 rounded-md bg-linear-to-r from-[#6366F1] to-[#818CF8] px-4 py-2.5 text-base md:text-sm font-medium text-white shadow-sm shadow-[#6366F1]/40 hover:from-[#4F46E5] hover:to-[#6366F1] active:from-[#4338CA] active:to-[#4F46E5] transition-all focus:outline-none focus-visible:ring-2 focus-visible:ring-[#6366F1] focus-visible:ring-offset-2 dark:focus-visible:ring-offset-slate-900"
                >
                    <span>Войти</span>
                    <x-icon name="arrow-right-on-rectangle" size="sm" />
                </button>
            </div>
        </form>

        <!-- OAuth кнопки -->
        <x-oauth-buttons text="Войти через" />

        <!-- Разделитель -->
        <div class="mt-6 pt-6 border-t border-slate-200 dark:border-slate-800">
            <p class="text-center text-base md:text-sm text-slate-600 dark:text-slate-400">
                Нет аккаунта? 
                <a href="{{ route('register') }}" class="text-[#6366F1] hover:text-[#4F46E5] dark:text-[#818CF8] dark:hover:text-[#6366F1] transition-colors font-medium">
                    Зарегистрироваться
                </a>
            </p>
        </div>
    </div>

    @push('scripts')
        <x-auth-form-scripts formId="loginForm" submitText="Вход..." />
    @endpush
@endsection
