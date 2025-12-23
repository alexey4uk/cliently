@extends('appointments.public.layout')

@section('title', 'Запись успешно создана')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xl p-8 md:p-12 text-center">
        <!-- Иконка успеха с анимацией -->
        <div class="w-20 h-20 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center mx-auto mb-6 animate-bounce">
            <i class="fa-solid fa-check text-3xl text-green-600 dark:text-green-400"></i>
        </div>
        
        <h1 class="text-3xl md:text-4xl font-bold text-slate-900 dark:text-white mb-4">
            Запись успешно создана!
        </h1>
        
        <p class="text-base text-slate-600 dark:text-slate-400 mb-8 leading-relaxed">
            Мы свяжемся с вами в ближайшее время для подтверждения записи.
        </p>
        
        @if(session('success'))
        <div class="mb-8 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-xl">
            <p class="text-sm font-medium text-green-800 dark:text-green-200 flex items-center justify-center gap-2">
                <i class="fa-solid fa-info-circle"></i>
                <span>{{ session('success') }}</span>
            </p>
        </div>
        @endif
        
        <!-- Детали записи -->
        @if(isset($appointment))
        <div class="bg-slate-50 dark:bg-slate-800 rounded-xl p-6 mt-6 mb-8 text-left">
            <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wide mb-4 flex items-center gap-2">
                <i class="fa-solid fa-calendar-check text-indigo-600 dark:text-indigo-400"></i>
                <span>Детали записи</span>
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @if(isset($appointment->service))
                <div class="flex flex-col gap-1">
                    <span class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wide">Услуга</span>
                    <span class="text-base font-semibold text-slate-900 dark:text-white">{{ $appointment->service->name }}</span>
                </div>
                @endif
                @if(isset($appointment->master))
                <div class="flex flex-col gap-1">
                    <span class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wide">Мастер</span>
                    <span class="text-base font-semibold text-slate-900 dark:text-white">{{ $appointment->master->first_name }} {{ $appointment->master->last_name }}</span>
                </div>
                @endif
                @if(isset($appointment->date))
                <div class="flex flex-col gap-1">
                    <span class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wide">Дата</span>
                    <span class="text-base font-semibold text-slate-900 dark:text-white">{{ \Carbon\Carbon::parse($appointment->date)->locale('ru')->isoFormat('D MMMM YYYY') }}</span>
                </div>
                @endif
                @if(isset($appointment->time))
                <div class="flex flex-col gap-1">
                    <span class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wide">Время</span>
                    <span class="text-base font-semibold text-slate-900 dark:text-white">{{ $appointment->time }}</span>
                </div>
                @endif
            </div>
        </div>
        @endif
        
        <!-- Кнопки действий -->
        <div class="space-y-4">
            @if($token && $appointment)
            <a href="{{ route('public.appointment.view', ['token' => $token]) }}"
               class="block w-full px-6 py-4 text-base font-semibold text-white bg-gradient-to-r from-indigo-500 to-purple-600 hover:from-indigo-600 hover:to-purple-700 rounded-lg transition-all shadow-lg hover:shadow-xl flex items-center justify-center gap-2">
                <i class="fa-solid fa-eye"></i>
                <span>Просмотреть запись</span>
            </a>
            @endif
            
            <a href="{{ route('public.appointments.show', $business->slug) }}"
               class="block w-full px-6 py-4 text-base font-semibold text-slate-700 dark:text-slate-300 bg-white dark:bg-slate-800 border-2 border-slate-300 dark:border-slate-700 hover:border-indigo-500 dark:hover:border-indigo-500 rounded-lg transition-all shadow-md hover:shadow-lg flex items-center justify-center gap-2">
                <i class="fa-solid fa-calendar-plus"></i>
                <span>Создать еще одну запись</span>
            </a>
            
            @if($business->phone)
            <div class="pt-6 border-t border-slate-200 dark:border-slate-700 mt-6">
                <p class="text-sm font-medium text-slate-600 dark:text-slate-400 mb-3">По вопросам обращайтесь:</p>
                <a href="tel:{{ $business->phone }}" 
                   class="inline-flex items-center gap-2 px-4 py-2 text-base font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 transition-colors border-2 border-indigo-200 dark:border-indigo-800 rounded-lg hover:bg-indigo-50 dark:hover:bg-indigo-900/20">
                    <i class="fa-solid fa-phone"></i>
                    <span>{{ $business->phone }}</span>
                </a>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
