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
                Онлайн-запись недоступна
            </h1>
        </div>
    </div>
@endsection
