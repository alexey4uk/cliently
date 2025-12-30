@extends('appointments.public.layout')

@section('title', 'Просмотр записи')

@section('content')
<div class="max-w-3xl lg:max-w-3xl mx-auto">
    <!-- Уведомления -->
    @if(session('success'))
        <div class="mb-4 sm:mb-5 lg:mb-4 p-3 sm:p-4 lg:p-3 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 rounded-xl">
            <p class="text-sm sm:text-base lg:text-sm text-emerald-800 dark:text-emerald-200 text-center">
                {{ session('success') }}
            </p>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-4 sm:mb-5 lg:mb-4 p-3 sm:p-4 lg:p-3 bg-rose-50 dark:bg-rose-900/20 border border-rose-200 dark:border-rose-800 rounded-xl">
            <p class="text-sm sm:text-base lg:text-sm text-rose-800 dark:text-rose-200 text-center">
                {{ session('error') }}
            </p>
        </div>
    @endif

    <!-- Заголовок с номером записи -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl sm:rounded-3xl lg:rounded-2xl border border-slate-200 dark:border-slate-800 p-4 sm:p-5 lg:p-4 mb-4 sm:mb-5 lg:mb-4">
        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
            <div class="flex-1 min-w-0">
                <h1 class="text-xl sm:text-2xl lg:text-xl font-bold text-slate-900 dark:text-white mb-1.5 sm:mb-2 lg:mb-1.5">
                    Запись #{{ $appointment->id }}
                </h1>
                <p class="text-sm sm:text-base lg:text-sm text-slate-600 dark:text-slate-400">
                    {{ $appointment->date->locale('ru')->isoFormat('D MMMM') }}, {{ \Carbon\Carbon::parse($appointment->time)->format('H:i') }}
                </p>
            </div>
            <div class="flex-shrink-0">
                @if($appointment->status === 'pending')
                    <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs sm:text-sm lg:text-xs font-medium bg-amber-100 dark:bg-amber-900/30 text-amber-800 dark:text-amber-300">
                        Ожидает подтверждения
                    </span>
                @elseif($appointment->status === 'confirmed')
                    <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs sm:text-sm lg:text-xs font-medium bg-emerald-100 dark:bg-emerald-900/30 text-emerald-800 dark:text-emerald-300">
                        Подтверждена
                    </span>
                @elseif($appointment->status === 'completed')
                    <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs sm:text-sm lg:text-xs font-medium bg-indigo-100 dark:bg-indigo-900/30 text-indigo-800 dark:text-indigo-300">
                        Завершена
                    </span>
                @elseif($appointment->status === 'cancelled')
                    <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs sm:text-sm lg:text-xs font-medium bg-rose-100 dark:bg-rose-900/30 text-rose-800 dark:text-rose-300">
                        Отменена
                    </span>
                @endif
            </div>
        </div>
    </div>

    <!-- Основная информация -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl sm:rounded-3xl lg:rounded-2xl border border-slate-200 dark:border-slate-800 p-4 sm:p-5 lg:p-4 mb-4 sm:mb-5 lg:mb-4">
        <div class="flex items-center gap-3 mb-4 lg:mb-3">
            <div class="w-8 h-8 lg:w-7 lg:h-7 rounded-xl bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center">
                <i class="fa-solid fa-calendar-check text-indigo-600 dark:text-indigo-400 text-sm lg:text-xs"></i>
            </div>
            <h2 class="text-lg sm:text-xl lg:text-base font-bold text-slate-900 dark:text-white">
                Основная информация
            </h2>
        </div>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4 lg:gap-3">
            <!-- Услуга -->
            <div>
                <span class="text-xs text-slate-500 dark:text-slate-400 block mb-1">Услуга</span>
                <p class="text-sm sm:text-base lg:text-sm font-medium text-slate-900 dark:text-white mb-1">
                    {{ $appointment->service->name }}
                </p>
                <div class="flex items-center gap-2 text-xs text-slate-600 dark:text-slate-400">
                    @if($appointment->final_price)
                        <span>{{ number_format($appointment->final_price, 0, ',', ' ') }} Br</span>
                    @endif
                    @if($appointment->final_price && $appointment->final_duration)
                        <span>•</span>
                    @endif
                    @if($appointment->final_duration)
                        <span>{{ $appointment->final_duration }} мин</span>
                    @endif
                </div>
            </div>

            <!-- Мастер -->
            @if($appointment->master)
            <div>
                <span class="text-xs text-slate-500 dark:text-slate-400 block mb-1">Мастер</span>
                <p class="text-sm sm:text-base lg:text-sm font-medium text-slate-900 dark:text-white">
                    {{ $appointment->master->first_name }} {{ $appointment->master->last_name }}
                </p>
            </div>
            @endif

            <!-- Локация -->
            @if($appointment->location)
            <div class="sm:col-span-2">
                <span class="text-xs text-slate-500 dark:text-slate-400 block mb-1">Локация</span>
                <p class="text-sm sm:text-base lg:text-sm font-medium text-slate-900 dark:text-white">
                    {{ $appointment->location->name }}
                </p>
                @if($appointment->location->full_address)
                    <p class="text-xs sm:text-sm lg:text-xs text-slate-600 dark:text-slate-400 mt-1">
                        {{ $appointment->location->full_address }}
                    </p>
                @endif
            </div>
            @endif
        </div>
    </div>

    <!-- Заметки -->
    @if($appointment->notes)
    <div class="bg-white dark:bg-slate-900 rounded-2xl sm:rounded-3xl lg:rounded-2xl border border-slate-200 dark:border-slate-800 p-4 sm:p-5 lg:p-4 mb-4 sm:mb-5 lg:mb-4">
        <div class="flex items-center gap-3 mb-3 lg:mb-2.5">
            <div class="w-8 h-8 lg:w-7 lg:h-7 rounded-xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center">
                <i class="fa-solid fa-note-sticky text-slate-600 dark:text-slate-400 text-sm lg:text-xs"></i>
            </div>
            <h2 class="text-lg sm:text-xl lg:text-base font-bold text-slate-900 dark:text-white">
                Заметки
            </h2>
        </div>
        <p class="text-sm sm:text-base lg:text-sm text-slate-700 dark:text-slate-300 whitespace-pre-wrap">
            {{ $appointment->notes }}
        </p>
    </div>
    @endif

    <!-- Действия -->
    @if(!in_array($appointment->status, ['completed', 'cancelled']))
    <div class="mb-4 sm:mb-5 lg:mb-4">
        <form method="POST" action="{{ route('public.appointment.cancel', ['token' => $appointment->token]) }}" 
              onsubmit="return confirm('Вы уверены, что хотите отменить эту запись?');">
            @csrf
            <button type="submit" class="w-full px-6 py-3 text-sm sm:text-base lg:text-sm font-medium text-white bg-rose-600 hover:bg-rose-700 rounded-xl transition-colors duration-200 shadow-lg hover:shadow-xl">
                <i class="fa-solid fa-times mr-2"></i>
                Отменить запись
            </button>
        </form>
    </div>
    @endif

    <!-- Кнопка создания новой записи -->
    <div class="text-center">
        <a href="{{ route('public.appointments.show', $business->slug) }}"
           class="inline-flex items-center gap-2 px-6 py-3 text-sm sm:text-base lg:text-sm font-medium text-slate-700 dark:text-slate-300 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 hover:border-indigo-500 dark:hover:border-indigo-500 rounded-xl transition-colors duration-200">
            <i class="fa-solid fa-calendar-plus text-xs"></i>
            <span>Создать новую запись</span>
        </a>
    </div>
</div>
@endsection
