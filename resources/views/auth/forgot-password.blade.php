@extends('layouts.auth')

@section('title', 'Сброс пароля')

@section('content')
    <!-- Форма сброса пароля -->
    <div class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900 animate-fade-in-up">
        <form method="POST" action="{{ route('password.email') }}" class="space-y-5" id="passwordResetForm">
            @csrf

            <!-- Уведомление об успешной отправке -->
            @if (session('status'))
                <div class="rounded-lg bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 p-3">
                    <div class="flex items-center gap-2">
                        <x-icon name="check-circle" size="sm" class="text-emerald-600 dark:text-emerald-400" />
                        <p class="text-base md:text-sm text-emerald-700 dark:text-emerald-300">{{ session('status') }}</p>
                    </div>
                </div>
            @endif

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

            <!-- Кнопка отправки -->
            <div class="pt-2">
                <button 
                    type="submit"
                    class="w-full inline-flex items-center justify-center gap-2 rounded-md bg-gradient-to-r from-[#6366F1] to-[#818CF8] px-4 py-2.5 text-base md:text-sm font-medium text-white shadow-sm shadow-[#6366F1]/40 hover:from-[#4F46E5] hover:to-[#6366F1] active:from-[#4338CA] active:to-[#4F46E5] transition-all focus:outline-none focus-visible:ring-2 focus-visible:ring-[#6366F1] focus-visible:ring-offset-2 dark:focus-visible:ring-offset-slate-900"
                >
                    <span>Отправить ссылку</span>
                    <x-icon name="paper-airplane" size="sm" />
                </button>
            </div>
        </form>

        <!-- Разделитель -->
        <div class="mt-6 pt-6 border-t border-slate-200 dark:border-slate-800">
            <p class="text-center text-base md:text-sm text-slate-600 dark:text-slate-400">
                Вспомнили пароль?
                <a href="{{ route('login') }}" class="text-[#6366F1] hover:text-[#4F46E5] dark:text-[#818CF8] dark:hover:text-[#6366F1] transition-colors font-medium">
                    Войти
                </a>
            </p>
        </div>
    </div>

    @push('scripts')
        <x-auth-form-scripts formId="passwordResetForm" submitText="Отправка..." />
    @endpush
@endsection
