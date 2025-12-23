@extends('appointments.public.layout')

@section('title', 'Запись успешно создана')

@section('content')
<div class="max-w-md mx-auto">
    <div class="glass-card rounded-xl p-6 md:p-8 text-center">
        <div class="inline-flex items-center justify-center w-16 h-16 bg-green-100 dark:bg-green-900/30 rounded-full mb-4">
            <i class="fa-solid fa-check text-2xl text-green-600 dark:text-green-400"></i>
        </div>
        
        <h1 class="text-2xl font-semibold text-slate-900 dark:text-white mb-2">
            Запись успешно создана!
        </h1>
        
        <p class="text-slate-600 dark:text-slate-400 mb-6">
            Мы свяжемся с вами в ближайшее время для подтверждения записи.
        </p>
        
        @if(session('success'))
        <div class="mb-6 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-md">
            <p class="text-sm text-green-800 dark:text-green-200">
                {{ session('success') }}
            </p>
        </div>
        @endif
        
        <div class="space-y-3">
            <a href="{{ route('public.appointments.show', $business->slug) }}"
               class="block w-full px-4 py-2 text-sm font-medium text-white bg-gradient-to-r from-indigo-500 to-purple-600 hover:from-indigo-600 hover:to-purple-700 rounded-md transition-colors shadow-sm">
                <i class="fa-solid fa-calendar-plus mr-2"></i>
                Создать еще одну запись
            </a>
            @if($business->phone)
            <div class="pt-4 border-t border-slate-200 dark:border-slate-700">
                <p class="text-sm text-slate-600 dark:text-slate-400 mb-2">По вопросам обращайтесь:</p>
                <a href="tel:{{ $business->phone }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 transition-colors">
                    <i class="fa-solid fa-phone mr-2"></i>{{ $business->phone }}
                </a>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
