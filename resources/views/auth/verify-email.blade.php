@extends('layouts.auth')

@section('title', 'Подтверждение email')

@section('content')
    <!-- Основной контейнер с улучшенным дизайном -->
    <div class="max-w-sm w-full mx-auto">
        <!-- Заголовок страницы -->
        <div class="text-center mb-6">
            <h1 class="text-xl md:text-2xl font-bold text-slate-800 dark:text-white mb-2">Подтверждение email</h1>
            <p class="text-slate-500 dark:text-slate-400 text-sm">Пожалуйста, подтвердите свой email адрес</p>
        </div>

        <!-- Карточка формы с улучшенным дизайном -->
        <div class="rounded-xl border border-slate-200 bg-white/80 p-5 md:p-6 shadow-md dark:border-slate-800 dark:bg-slate-900/80 animate-fade-in-up">
            <div class="space-y-4">
                <!-- Информационное сообщение -->
                <div class="rounded-lg border border-indigo-500/20 bg-indigo-50 dark:border-indigo-500/30 dark:bg-indigo-900/20 p-4">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-9 h-9 rounded-full bg-indigo-500 dark:bg-indigo-500 flex items-center justify-center flex-shrink-0">
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
                    <div class="flex items-center gap-2 px-3 py-2 rounded-md bg-white/60 dark:bg-slate-800/60 border border-indigo-500/20 dark:border-indigo-500/30">
                        <x-icon name="envelope" size="sm" class="text-indigo-500 dark:text-indigo-400 flex-shrink-0" />
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

                <!-- Уведомление об отправке -->
                @if (session('status') == 'verification-link-sent')
            <div class="rounded-lg bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 p-3.5 animate-fade-in mt-4">
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
                <div class="space-y-3 pt-1">
                    <!-- Форма повторной отправки -->
                    <form method="POST" action="{{ route('verification.send') }}" id="resendForm">
                    @csrf
                        <button
                            type="submit"
                            class="w-full inline-flex items-center justify-center gap-2 rounded-lg bg-gradient-to-r from-indigo-500 to-indigo-600 px-4 py-2.5 text-sm font-medium text-white shadow-sm shadow-indigo-500/30 hover:from-indigo-600 hover:to-indigo-700 active:from-indigo-700 active:to-indigo-800 transition-all duration-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-slate-900 transform hover:scale-[1.01] active:scale-[0.99]"
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
                            <span class="px-2 bg-white/80 dark:bg-slate-900/80 text-slate-500 dark:text-slate-400 text-xs">
                            Или
                        </span>
                    </div>
                </div>

                <!-- Форма выхода -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                        <button
                            type="submit"
                            class="w-full inline-flex items-center justify-center gap-2 rounded-lg border border-slate-300 dark:border-slate-700 px-4 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-slate-900"
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
