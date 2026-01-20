@extends('layouts.panel')

@section('title', 'Поддержка')

@section('content')
    <div class="space-y-6">
        <!-- Заголовок -->
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-slate-900 dark:text-white">Поддержка</h2>
                <p class="text-slate-600 dark:text-slate-400 mt-1">Раздел поддержки и технической помощи</p>
            </div>
        </div>

        <!-- Информация -->
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 p-6">
            <div class="space-y-4">
                <div class="flex items-start gap-4">
                    <div class="h-10 w-10 rounded-xl bg-indigo-100 dark:bg-indigo-500/20 flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-headset text-indigo-600 dark:text-indigo-400"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Связаться с поддержкой</h3>
                        <p class="text-slate-600 dark:text-slate-400 mt-1">Если у вас возникли вопросы или проблемы, свяжитесь с нашей командой поддержки.</p>
                    </div>
                </div>

                <div class="flex items-start gap-4">
                    <div class="h-10 w-10 rounded-xl bg-emerald-100 dark:bg-emerald-500/20 flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-envelope text-emerald-600 dark:text-emerald-400"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Email</h3>
                        <p class="text-slate-600 dark:text-slate-400 mt-1">support@cliently.ru</p>
                    </div>
                </div>

                <div class="flex items-start gap-4">
                    <div class="h-10 w-10 rounded-xl bg-amber-100 dark:bg-amber-500/20 flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-clock text-amber-600 dark:text-amber-400"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Рабочее время</h3>
                        <p class="text-slate-600 dark:text-slate-400 mt-1">Пн-Пт: 9:00 - 18:00 (МСК)</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
