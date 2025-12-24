@extends('appointments.public.layout')

@section('title', 'Запись успешно создана')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-800 p-6 text-center">
        <!-- Иконка успеха -->
        <div class="w-16 h-16 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center mx-auto mb-4">
            <i class="fa-solid fa-check text-2xl text-green-600 dark:text-green-400"></i>
        </div>
        
        <h1 class="text-2xl font-semibold text-slate-900 dark:text-white mb-3">
            Запись успешно создана!
        </h1>
        
        <p class="text-sm text-slate-600 dark:text-slate-400 mb-6">
            Мы свяжемся с вами в ближайшее время для подтверждения записи.
        </p>
        
        @if(session('success'))
        <div class="mb-6 p-3 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg">
            <p class="text-sm text-green-800 dark:text-green-200">
                {{ session('success') }}
            </p>
        </div>
        @endif
        
        <!-- Детали записи -->
        @if(isset($appointment))
        <div class="bg-slate-50 dark:bg-slate-800 rounded-lg p-4 mb-6 text-left">
            <h3 class="text-sm font-medium text-slate-700 dark:text-slate-300 mb-3">
                Детали записи
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                @if(isset($appointment->service))
                <div>
                    <span class="text-xs text-slate-500 dark:text-slate-400">Услуга</span>
                    <p class="text-sm font-medium text-slate-900 dark:text-white">{{ $appointment->service->name }}</p>
                </div>
                @endif
                @if(isset($appointment->master))
                <div>
                    <span class="text-xs text-slate-500 dark:text-slate-400">Мастер</span>
                    <p class="text-sm font-medium text-slate-900 dark:text-white">{{ $appointment->master->first_name }} {{ $appointment->master->last_name }}</p>
                </div>
                @endif
                @if(isset($appointment->date))
                <div>
                    <span class="text-xs text-slate-500 dark:text-slate-400">Дата</span>
                    <p class="text-sm font-medium text-slate-900 dark:text-white">{{ \Carbon\Carbon::parse($appointment->date)->locale('ru')->isoFormat('D MMMM YYYY') }}</p>
                </div>
                @endif
                @if(isset($appointment->time))
                <div>
                    <span class="text-xs text-slate-500 dark:text-slate-400">Время</span>
                    <p class="text-sm font-medium text-slate-900 dark:text-white">{{ $appointment->time }}</p>
                </div>
                @endif
            </div>
        </div>
        @endif
        
        <!-- Кнопки действий -->
        <div class="space-y-3">
            @if($token && $appointment)
            <a href="{{ route('public.appointment.view', ['token' => $token]) }}"
               class="block w-full px-4 py-2.5 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg transition-colors">
                Просмотреть запись
            </a>
            @endif
            
            <a href="{{ route('public.appointments.show', $business->slug) }}"
               class="block w-full px-4 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-300 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 hover:border-indigo-500 dark:hover:border-indigo-500 rounded-lg transition-colors">
                Создать еще одну запись
            </a>
            
            @if($business->phone)
            <div class="pt-4 border-t border-slate-200 dark:border-slate-700 mt-4">
                <p class="text-sm text-slate-600 dark:text-slate-400 mb-2">По вопросам обращайтесь:</p>
                <a href="tel:{{ $business->phone }}" 
                   class="inline-flex items-center gap-1.5 text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300">
                    <i class="fa-solid fa-phone text-xs"></i>
                    <span>{{ $business->phone }}</span>
                </a>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
