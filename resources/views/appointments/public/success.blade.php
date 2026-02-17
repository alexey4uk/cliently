@extends('appointments.public.layout')

@section('title', 'Запись успешно создана')

@section('content')
<div class="max-w-3xl lg:max-w-3xl mx-auto">
    <!-- Заголовок -->
    <div class="text-center mb-5 sm:mb-6 lg:mb-5">
        <h1 class="text-2xl sm:text-3xl lg:text-2xl font-bold text-slate-900 dark:text-white mb-2 lg:mb-1.5">
            Запись успешно создана!
        </h1>
        
        <p class="text-sm sm:text-base text-slate-600 dark:text-slate-400">
            Мы свяжемся с вами в ближайшее время для подтверждения записи.
        </p>
    </div>
    
    <!-- Детали записи -->
    @if(isset($appointment))
    <div class="bg-white dark:bg-slate-900 rounded-2xl sm:rounded-3xl lg:rounded-2xl border border-slate-200 dark:border-slate-800 p-4 sm:p-5 lg:p-4 mb-4 sm:mb-5 lg:mb-4">
        <div class="flex items-center gap-3 mb-4 lg:mb-3">
            <div class="w-8 h-8 lg:w-7 lg:h-7 rounded-xl bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center">
                <i class="fa-solid fa-calendar-check text-indigo-600 dark:text-indigo-400 text-sm lg:text-xs"></i>
            </div>
            <h2 class="text-lg sm:text-xl lg:text-base font-bold text-slate-900 dark:text-white">
                Детали записи
            </h2>
        </div>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4 lg:gap-3">
            @if(isset($appointment->service))
            <div>
                <span class="text-xs text-slate-500 dark:text-slate-400 block mb-1">Услуга</span>
                <p class="text-sm sm:text-base lg:text-sm font-medium text-slate-900 dark:text-white">{{ $appointment->service?->name ?? 'Услуга удалена' }}</p>
            </div>
            @endif
            
            <div>
                <span class="text-xs text-slate-500 dark:text-slate-400 block mb-1">Мастер</span>
                @if(isset($appointment->master) && $appointment->master && !$appointment->master->trashed())
                <p class="text-sm sm:text-base lg:text-sm font-medium text-slate-900 dark:text-white">{{ $appointment->master->first_name }} {{ $appointment->master->last_name }}</p>
                @else
                <p class="text-sm sm:text-base lg:text-sm font-medium text-slate-600 dark:text-slate-400">Будет назначен</p>
                @endif
            </div>
            
            @if(isset($appointment->location))
            <div>
                <span class="text-xs text-slate-500 dark:text-slate-400 block mb-1">Локация</span>
                <p class="text-sm sm:text-base lg:text-sm font-medium text-slate-900 dark:text-white">{{ $appointment->location?->name ?? 'Локация удалена' }}</p>
            </div>
            @endif
            
            @if(isset($appointment->date) && isset($appointment->time))
            <div>
                <span class="text-xs text-slate-500 dark:text-slate-400 block mb-1">Дата и время</span>
                <p class="text-sm sm:text-base lg:text-sm font-medium text-slate-900 dark:text-white">
                    {{ \Carbon\Carbon::parse($appointment->date)->locale('ru')->isoFormat('D MMMM') }}, {{ \Carbon\Carbon::parse($appointment->time)->format('H:i') }}
                </p>
            </div>
            @elseif(isset($appointment->date))
            <div>
                <span class="text-xs text-slate-500 dark:text-slate-400 block mb-1">Дата</span>
                <p class="text-sm sm:text-base lg:text-sm font-medium text-slate-900 dark:text-white">{{ \Carbon\Carbon::parse($appointment->date)->locale('ru')->isoFormat('D MMMM') }}</p>
            </div>
            @endif
        </div>
    </div>
    @endif
    
    <!-- Кнопки действий -->
    <div class="space-y-2.5 sm:space-y-3 lg:space-y-2.5">
        @if($token && $appointment)
        <a href="{{ route('public.appointment.view', ['token' => $token]) }}"
           class="block w-full px-6 py-3 text-sm sm:text-base lg:text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl transition-colors duration-200 shadow-lg hover:shadow-xl text-center">
            <i class="fa-solid fa-eye mr-2"></i>
            Просмотреть запись
        </a>
        @endif
        
        <a href="{{ route('public.appointments.show', $business->slug) }}"
           class="block w-full px-6 py-3 text-sm sm:text-base lg:text-sm font-medium text-slate-700 dark:text-slate-300 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 hover:border-indigo-500 dark:hover:border-indigo-500 rounded-xl transition-colors duration-200 text-center">
            <i class="fa-solid fa-plus mr-2"></i>
            Создать еще одну запись
        </a>
    </div>
</div>
@endsection
