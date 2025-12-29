@extends('layouts.auth')

@section('title', 'Регистрация')

@section('content')
    <!-- Форма регистрации -->
    <div class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900 animate-fade-in-up">
        <form method="POST" action="{{ route('register') }}" class="space-y-5" id="registerForm">
            @csrf

            <!-- Имя -->
            <div>
                <label for="name" class="flex items-center gap-1.5 md:gap-2 text-base md:text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                    <x-icon name="user" size="sm" class="text-[#6366F1] dark:text-[#818CF8]" />
                    <span>Имя*</span>
                </label>
                <input
                    type="text"
                    id="name" 
                    name="name"
                    value="{{ old('name') }}"
                    required
                    autocomplete="name"
                    class="w-full px-2.5 md:px-3 py-2 md:py-2.5 text-base md:text-sm rounded-md border {{ $errors->has('name') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-[#6366F1]' }} bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:border-transparent transition-colors"
                />
                @error('name')
                    <p class="mt-1.5 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                @enderror
            </div>

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
                    class="w-full px-2.5 md:px-3 py-2 md:py-2.5 text-base md:text-sm rounded-md border {{ $errors->has('email') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-[#6366F1]' }} bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:border-transparent transition-colors"
                />
                @error('email')
                    <p class="mt-1.5 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Пароль -->
            <div>
                <label for="password" class="flex items-center gap-1.5 md:gap-2 text-base md:text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                    <x-icon name="lock-closed" size="sm" class="text-[#6366F1] dark:text-[#818CF8]" />
                    <span>Пароль*</span>
                </label>
                <input
                    type="password" 
                    id="password" 
                    name="password"
                    required
                    autocomplete="new-password"
                    class="w-full px-2.5 md:px-3 py-2 md:py-2.5 text-base md:text-sm rounded-md border {{ $errors->has('password') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-[#6366F1]' }} bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:border-transparent transition-colors"
                />
                @error('password')
                    <p class="mt-1.5 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Подтверждение пароля -->
            <div>
                <label for="password_confirmation" class="flex items-center gap-1.5 md:gap-2 text-base md:text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                    <x-icon name="lock-closed" size="sm" class="text-[#6366F1] dark:text-[#818CF8]" />
                    <span>Подтверждение пароля*</span>
                </label>
                <input
                    type="password" 
                    id="password_confirmation"
                    name="password_confirmation"
                    required
                    autocomplete="new-password"
                    class="w-full px-2.5 md:px-3 py-2 md:py-2.5 text-base md:text-sm rounded-md border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-[#6366F1] focus:border-transparent transition-colors"
                />
            </div>

            <!-- Согласие с пользовательским соглашением -->
            <div>
                <label class="flex items-start gap-2 text-base md:text-sm">
                    <input
                        type="checkbox" 
                        name="terms"
                        required
                        class="mt-0.5 w-4 h-4 text-[#6366F1] border-slate-300 rounded focus:ring-[#6366F1] focus:ring-2 dark:border-slate-700 dark:bg-slate-800"
                    />
                    <span class="text-slate-600 dark:text-slate-400">
                        Я согласен с 
                        <a href="#" class="text-[#6366F1] hover:text-[#4F46E5] dark:text-[#818CF8] dark:hover:text-[#6366F1] transition-colors font-medium underline">
                            пользовательским соглашением
                        </a>
                    </span>
                </label>
                @error('terms')
                    <p class="mt-1.5 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Кнопка регистрации -->
            <div class="pt-2">
                <button 
                    type="submit"
                    class="w-full inline-flex items-center justify-center gap-2 rounded-md bg-gradient-to-r from-[#6366F1] to-[#818CF8] px-4 py-2.5 text-base md:text-sm font-medium text-white shadow-sm shadow-[#6366F1]/40 hover:from-[#4F46E5] hover:to-[#6366F1] active:from-[#4338CA] active:to-[#4F46E5] transition-all focus:outline-none focus-visible:ring-2 focus-visible:ring-[#6366F1] focus-visible:ring-offset-2 dark:focus-visible:ring-offset-slate-900"
                >
                    <span>Зарегистрироваться</span>
                    <x-icon name="user-plus" size="sm" />
                </button>
            </div>
        </form>

        <!-- Разделитель -->
        <div class="mt-6 pt-6 border-t border-slate-200 dark:border-slate-800">
            <p class="text-center text-base md:text-sm text-slate-600 dark:text-slate-400">
                Уже есть аккаунт?
                <a href="{{ route('login') }}" class="text-[#6366F1] hover:text-[#4F46E5] dark:text-[#818CF8] dark:hover:text-[#6366F1] transition-colors font-medium">
                    Войти
                </a>
            </p>
        </div>
    </div>

    @push('scripts')
        <x-auth-form-scripts formId="registerForm" submitText="Регистрация..." />
    @endpush
@endsection
