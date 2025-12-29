@extends('layouts.auth')

@section('title', 'Подтверждение email')

@section('content')
    <!-- Форма подтверждения email -->
    <div class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900 animate-fade-in-up">
        <div class="space-y-5">
            <!-- Информационное сообщение -->
            <div class="rounded-lg border border-[#6366F1]/20 bg-[#6366F1]/10 dark:border-[#6366F1]/30 dark:bg-[#6366F1]/20 p-4">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-9 h-9 rounded-full bg-[#6366F1] dark:bg-[#6366F1] flex items-center justify-center flex-shrink-0">
                        <x-icon name="envelope" size="sm" class="text-white" />
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="text-sm font-semibold text-slate-900 dark:text-white">
                            Подтвердите ваш email
                        </h3>
                        <p class="text-xs text-slate-600 dark:text-slate-400 mt-0.5">
                            Письмо отправлено на
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-2 px-3 py-2 rounded-md bg-white/60 dark:bg-slate-800/60 border border-[#6366F1]/20 dark:border-[#6366F1]/30">
                    <x-icon name="envelope" size="sm" class="text-[#6366F1] dark:text-[#818CF8] flex-shrink-0" />
                    <span class="text-xs md:text-sm font-medium text-slate-900 dark:text-white break-all">
                        {{ Auth::user()->email }}
                    </span>
                </div>
            </div>

            <!-- Подсказки -->
            <div class="rounded-lg bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 p-3.5">
                <div class="flex items-start gap-2.5">
                    <x-icon name="light-bulb" size="sm" class="text-amber-500 dark:text-amber-400 flex-shrink-0 mt-0.5" />
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-medium text-slate-700 dark:text-slate-300 mb-1.5">
                            Не получили письмо?
                        </p>
                        <ul class="text-xs text-slate-600 dark:text-slate-400 space-y-1">
                            <li class="flex items-start gap-1.5">
                                <span class="text-slate-400 dark:text-slate-500 mt-0.5">•</span>
                                <span>Проверьте папку "Спам" или "Нежелательная почта"</span>
                            </li>
                            <li class="flex items-start gap-1.5">
                                <span class="text-slate-400 dark:text-slate-500 mt-0.5">•</span>
                                <span>Убедитесь, что адрес указан правильно</span>
                            </li>
                            <li class="flex items-start gap-1.5">
                                <span class="text-slate-400 dark:text-slate-500 mt-0.5">•</span>
                                <span>Нажмите "Отправить письмо повторно" ниже</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Уведомление об отправке -->
        @if (session('status') == 'verification-link-sent')
            <div class="rounded-lg bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 p-3.5 animate-fade-in mt-5">
                <div class="flex items-start gap-2.5">
                    <x-icon name="check-circle" size="sm" class="text-emerald-600 dark:text-emerald-400 flex-shrink-0 mt-0.5" />
                    <div class="flex-1 min-w-0">
                        <p class="text-xs md:text-sm font-medium text-emerald-700 dark:text-emerald-300 mb-0.5">
                            Письмо отправлено!
                        </p>
                        <p class="text-xs text-emerald-600 dark:text-emerald-400">
                            Новая ссылка для подтверждения была отправлена на {{ Auth::user()->email }}
                        </p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Кнопки -->
        <div class="space-y-3 pt-2">
            <!-- Форма повторной отправки -->
            <form method="POST" action="{{ route('verification.send') }}" id="resendForm">
                @csrf
                <button 
                    type="submit" 
                    class="w-full inline-flex items-center justify-center gap-2 rounded-md bg-gradient-to-r from-[#6366F1] to-[#818CF8] px-4 py-2.5 text-base md:text-sm font-medium text-white shadow-sm shadow-[#6366F1]/40 hover:from-[#4F46E5] hover:to-[#6366F1] active:from-[#4338CA] active:to-[#4F46E5] transition-all focus:outline-none focus-visible:ring-2 focus-visible:ring-[#6366F1] focus-visible:ring-offset-2 dark:focus-visible:ring-offset-slate-900"
                >
                    <span>Отправить письмо повторно</span>
                    <x-icon name="paper-airplane" size="sm" />
                </button>
            </form>

            <!-- Разделитель -->
            <div class="relative">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-slate-200 dark:border-slate-800"></div>
                </div>
                <div class="relative flex justify-center text-sm">
                    <span class="px-2 bg-white dark:bg-slate-900 text-slate-500 dark:text-slate-400 text-xs">
                        Или
                    </span>
                </div>
            </div>

            <!-- Форма выхода -->
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button 
                    type="submit" 
                    class="w-full inline-flex items-center justify-center gap-2 rounded-md border border-slate-300 dark:border-slate-700 px-4 py-2.5 text-base md:text-sm font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-[#6366F1] focus-visible:ring-offset-2 dark:focus-visible:ring-offset-slate-900"
                >
                    <span>Выйти из аккаунта</span>
                    <x-icon name="arrow-right-on-rectangle" size="sm" />
                </button>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            // Улучшение UX формы
            const resendForm = document.getElementById('resendForm');
            if (resendForm) {
                const submitBtn = resendForm.querySelector('button[type="submit"]');
                resendForm.addEventListener('submit', function() {
                    submitBtn.innerHTML = '<svg class="w-3 h-3 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" /></svg><span> Отправка...</span>';
                    submitBtn.disabled = true;
                });
            }
        </script>
    @endpush
@endsection
