@extends('appointments.public.layout')

@section('title', 'Просмотр записи')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <!-- Уведомления -->
    @if(session('success'))
        <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4">
            <p class="text-sm font-medium text-green-800 dark:text-green-200 flex items-center gap-2">
                <i class="fa-solid fa-check-circle"></i>
                <span>{{ session('success') }}</span>
            </p>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-rose-50 dark:bg-rose-900/20 border border-rose-200 dark:border-rose-800 rounded-lg p-4">
            <p class="text-sm font-medium text-rose-800 dark:text-rose-200 flex items-center gap-2">
                <i class="fa-solid fa-exclamation-circle"></i>
                <span>{{ session('error') }}</span>
            </p>
        </div>
    @endif

    <!-- Заголовок с номером записи -->
    <div class="bg-white dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-800 shadow-sm p-4 sm:p-6">
        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
            <div class="flex-1 min-w-0">
                <h1 class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-white mb-1">
                    Запись #{{ $appointment->id }}
                </h1>
                <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-400">
                    {{ $appointment->date->locale('ru')->isoFormat('D MMMM YYYY') }} в {{ $appointment->time }}
                </p>
            </div>
            <div class="flex-shrink-0">
                @if($appointment->status === 'pending')
                    <span class="inline-flex items-center px-2 sm:px-3 py-1.5 rounded-full text-[10px] sm:text-xs font-semibold bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300">
                        <i class="fa-solid fa-clock mr-1.5 sm:mr-2 text-xs"></i>
                        <span class="hidden sm:inline">Ожидает подтверждения</span>
                        <span class="sm:hidden">Ожидает</span>
                    </span>
                @elseif($appointment->status === 'confirmed')
                    <span class="inline-flex items-center px-2 sm:px-3 py-1.5 rounded-full text-[10px] sm:text-xs font-semibold bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300">
                        <i class="fa-solid fa-check-circle mr-1.5 sm:mr-2 text-xs"></i>
                        <span class="hidden sm:inline">Подтверждена</span>
                        <span class="sm:hidden">ОК</span>
                    </span>
                @elseif($appointment->status === 'completed')
                    <span class="inline-flex items-center px-2 sm:px-3 py-1.5 rounded-full text-[10px] sm:text-xs font-semibold bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300">
                        <i class="fa-solid fa-check-double mr-1.5 sm:mr-2 text-xs"></i>
                        <span class="hidden sm:inline">Завершена</span>
                        <span class="sm:hidden">Готово</span>
                    </span>
                @elseif($appointment->status === 'cancelled')
                    <span class="inline-flex items-center px-2 sm:px-3 py-1.5 rounded-full text-[10px] sm:text-xs font-semibold bg-rose-100 dark:bg-rose-900/30 text-rose-800 dark:text-rose-300">
                        <i class="fa-solid fa-times-circle mr-1.5 sm:mr-2 text-xs"></i>
                        <span class="hidden sm:inline">Отменена</span>
                        <span class="sm:hidden">Отмена</span>
                    </span>
                @endif
            </div>
        </div>
    </div>

    <!-- Основная информация -->
    <div class="bg-white dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-800 shadow-sm">
        <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-700">
            <h2 class="text-base font-semibold text-slate-900 dark:text-white">Основная информация</h2>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Услуга -->
                <div>
                    <label class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wide block mb-2">
                        Услуга
                    </label>
                    <p class="text-base font-semibold text-slate-900 dark:text-white">
                        {{ $appointment->service->name }}
                    </p>
                    <div class="flex items-center gap-3 mt-1 text-sm text-slate-600 dark:text-slate-400">
                        @if($appointment->final_price)
                            <span>{{ number_format($appointment->final_price, 0, ',', ' ') }} Br</span>
                        @endif
                        <span>•</span>
                        <span>{{ $appointment->final_duration }} мин</span>
                    </div>
                </div>

                <!-- Мастер -->
                @if($appointment->master)
                <div>
                    <label class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wide block mb-2">
                        Мастер
                    </label>
                    <p class="text-base font-semibold text-slate-900 dark:text-white">
                        {{ $appointment->master->first_name }} {{ $appointment->master->last_name }}
                    </p>
                </div>
                @endif

                <!-- Локация -->
                @if($appointment->location)
                <div class="md:col-span-2">
                    <label class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wide block mb-2">
                        Локация
                    </label>
                    <p class="text-base font-semibold text-slate-900 dark:text-white">
                        {{ $appointment->location->name }}
                    </p>
                    @if($appointment->location->full_address)
                        <p class="text-sm text-slate-600 dark:text-slate-400 mt-1">
                            {{ $appointment->location->full_address }}
                        </p>
                    @endif
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Заметки -->
    @if($appointment->notes)
    <div class="bg-white dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-800 shadow-sm">
        <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-700">
            <h2 class="text-base font-semibold text-slate-900 dark:text-white">Заметки</h2>
        </div>
        <div class="p-6">
            <p class="text-sm text-slate-700 dark:text-slate-300 leading-relaxed whitespace-pre-wrap">
                {{ $appointment->notes }}
            </p>
        </div>
    </div>
    @endif

    <!-- Действия -->
    @if(!in_array($appointment->status, ['completed', 'cancelled']))
    <div class="bg-white dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-800 shadow-sm p-6">
        <form method="POST" action="{{ route('public.appointments.cancel-appointment', ['slug' => $business->slug, 'token' => $appointment->token]) }}" 
              onsubmit="return confirm('Вы уверены, что хотите отменить эту запись?');">
            @csrf
            <button type="submit" class="w-full px-4 py-2.5 text-sm font-medium text-white bg-rose-600 hover:bg-rose-700 rounded-lg transition-colors flex items-center justify-center gap-2">
                <i class="fa-solid fa-times-circle"></i>
                <span>Отменить запись</span>
            </button>
        </form>
    </div>
    @endif

    <!-- Контакты -->
    @if($business->phone)
    <div class="bg-white dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-800 shadow-sm p-6">
        <div class="text-center">
            <p class="text-sm font-medium text-slate-600 dark:text-slate-400 mb-3">По вопросам обращайтесь:</p>
            <a href="tel:{{ $business->phone }}" 
               class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 transition-colors">
                <i class="fa-solid fa-phone"></i>
                <span>{{ $business->phone }}</span>
            </a>
        </div>
    </div>
    @endif

    <!-- Кнопка создания новой записи -->
    <div class="text-center">
        <a href="{{ route('public.appointments.show', $business->slug) }}"
           class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-slate-700 dark:text-slate-300 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">
            <i class="fa-solid fa-calendar-plus"></i>
            <span>Создать новую запись</span>
        </a>
    </div>
</div>
@endsection
