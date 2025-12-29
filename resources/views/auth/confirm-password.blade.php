@extends('layouts.auth')

@section('title', 'Подтверждение пароля')

@section('content')
    <!-- Форма подтверждения пароля -->
    <div class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900 animate-fade-in-up">
        <form method="POST" action="{{ route('password.confirm') }}" class="space-y-5" id="confirmPasswordForm">
            @csrf

            <!-- Password -->
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
                    autocomplete="current-password"
                    autofocus
                    class="w-full px-2.5 md:px-3 py-2 md:py-2.5 text-base md:text-sm rounded-md border {{ $errors->has('password') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-[#6366F1]' }} bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:border-transparent transition-colors"
                />
                @error('password')
                    <p class="mt-1.5 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Кнопка подтверждения -->
            <div class="pt-2">
                <button 
                    type="submit"
                    class="w-full inline-flex items-center justify-center gap-2 rounded-md bg-gradient-to-r from-[#6366F1] to-[#818CF8] px-4 py-2.5 text-base md:text-sm font-medium text-white shadow-sm shadow-[#6366F1]/40 hover:from-[#4F46E5] hover:to-[#6366F1] active:from-[#4338CA] active:to-[#4F46E5] transition-all focus:outline-none focus-visible:ring-2 focus-visible:ring-[#6366F1] focus-visible:ring-offset-2 dark:focus-visible:ring-offset-slate-900"
                >
                    <span>Подтвердить</span>
                    <x-icon name="check" size="sm" />
                </button>
            </div>
        </form>
    </div>

    @push('scripts')
        <x-auth-form-scripts formId="confirmPasswordForm" submitText="Проверка..." />
    @endpush
@endsection
