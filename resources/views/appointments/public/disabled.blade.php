@extends('appointments.public.layout')

@section('title', 'Онлайн-запись недоступна')

@section('content')
    <div class="max-w-2xl mx-auto">
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xl p-8 sm:p-12 text-center">
            <!-- Иконка -->
            <div class="mb-6 flex justify-center">
                <div class="h-20 w-20 rounded-full bg-rose-100 dark:bg-rose-500/20 flex items-center justify-center">
                    <i class="fa-solid fa-calendar-xmark text-rose-600 dark:text-rose-400 text-3xl"></i>
                </div>
            </div>

            <!-- Заголовок -->
            <h1 class="text-2xl sm:text-3xl font-bold text-slate-900 dark:text-white mb-3">
                Онлайн-запись временно недоступна
            </h1>

            <!-- Описание -->
            <p class="text-slate-600 dark:text-slate-400 mb-8 text-base sm:text-lg">
                К сожалению, онлайн-запись временно отключена. Пожалуйста, свяжитесь с нами напрямую для записи.
            </p>

            <!-- Контакты -->
            @if($business->phone)
                <div class="bg-slate-50 dark:bg-slate-800/50 rounded-xl p-6 mb-6">
                    <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                        <div class="flex items-center gap-3">
                            <div class="h-10 w-10 rounded-lg bg-indigo-100 dark:bg-indigo-500/20 flex items-center justify-center">
                                <i class="fa-solid fa-phone text-indigo-600 dark:text-indigo-400"></i>
                            </div>
                            <div class="text-left">
                                <p class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Телефон</p>
                                <a href="tel:{{ $business->phone }}" 
                                   class="text-lg font-semibold text-slate-900 dark:text-white hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">
                                    {{ $business->phone }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Кнопка "Попробовать позже" -->
            <div class="flex flex-col sm:flex-row gap-3 justify-center">
                <a href="{{ route('public.appointments.show', ['slug' => $business->slug]) }}"
                   class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-xl transition-colors">
                    <i class="fa-solid fa-arrow-rotate-left"></i>
                    <span>Попробовать позже</span>
                </a>
            </div>
        </div>
    </div>
@endsection
