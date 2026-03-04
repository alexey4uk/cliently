@extends('appointments.public.layout')

@section('title', 'Нет мастеров в филиале')

@section('content')
    <div class="w-full">
        <div class="rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 p-6 sm:p-8 text-center">
            <div class="w-12 h-12 rounded-xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center mx-auto mb-3 text-slate-400">
                <i class="fa-solid fa-user-slash text-xl"></i>
            </div>
            <h3 class="text-base font-semibold text-slate-900 dark:text-white mb-1">В этом филиале пока нет мастеров</h3>
            <p class="text-sm text-slate-500 dark:text-slate-400 mb-4">Запись в выбранный филиал временно недоступна. Выберите другой филиал или свяжитесь с нами.</p>
            <a href="{{ $backUrl }}"
               class="inline-flex items-center justify-center min-h-[44px] px-4 py-2.5 rounded-xl text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 transition-colors touch-manipulation">
                {{ $backText }}
            </a>
        </div>
    </div>
@endsection
