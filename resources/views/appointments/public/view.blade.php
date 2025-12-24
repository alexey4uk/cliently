@extends('appointments.public.layout')

@section('title', 'Просмотр записи')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <!-- Уведомления -->
    @if(session('success'))
        <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-3">
            <p class="text-sm text-green-800 dark:text-green-200">
                {{ session('success') }}
            </p>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-rose-50 dark:bg-rose-900/20 border border-rose-200 dark:border-rose-800 rounded-lg p-3">
            <p class="text-sm text-rose-800 dark:text-rose-200">
                {{ session('error') }}
            </p>
        </div>
    @endif

    <!-- Заголовок с номером записи -->
    <div class="bg-white dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-800 p-4">
        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-2">
            <div class="flex-1 min-w-0">
                <h1 class="text-lg font-semibold text-slate-900 dark:text-white mb-1">
                    Запись #{{ $appointment->id }}
                </h1>
                <p class="text-sm text-slate-600 dark:text-slate-400">
                    {{ $appointment->date->locale('ru')->isoFormat('D MMMM YYYY') }} в {{ $appointment->time }}
                </p>
            </div>
            <div class="flex-shrink-0">
                @if($appointment->status === 'pending')
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300">
                        Ожидает подтверждения
                    </span>
                @elseif($appointment->status === 'confirmed')
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300">
                        Подтверждена
                    </span>
                @elseif($appointment->status === 'completed')
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300">
                        Завершена
                    </span>
                @elseif($appointment->status === 'cancelled')
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-rose-100 dark:bg-rose-900/30 text-rose-800 dark:text-rose-300">
                        Отменена
                    </span>
                @endif
            </div>
        </div>
    </div>

    <!-- Основная информация -->
    <div class="bg-white dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-800">
        <div class="px-4 py-3 border-b border-slate-200 dark:border-slate-700">
            <h2 class="text-sm font-medium text-slate-900 dark:text-white">Основная информация</h2>
        </div>
        <div class="p-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Услуга -->
                <div>
                    <span class="text-xs text-slate-500 dark:text-slate-400 block mb-1">
                        Услуга
                    </span>
                    <p class="text-sm font-medium text-slate-900 dark:text-white">
                        {{ $appointment->service->name }}
                    </p>
                    <div class="flex items-center gap-2 mt-1 text-xs text-slate-600 dark:text-slate-400">
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
                    <span class="text-xs text-slate-500 dark:text-slate-400 block mb-1">
                        Мастер
                    </span>
                    <p class="text-sm font-medium text-slate-900 dark:text-white">
                        {{ $appointment->master->first_name }} {{ $appointment->master->last_name }}
                    </p>
                </div>
                @endif

                <!-- Локация -->
                @if($appointment->location)
                <div class="md:col-span-2">
                    <span class="text-xs text-slate-500 dark:text-slate-400 block mb-1">
                        Локация
                    </span>
                    <p class="text-sm font-medium text-slate-900 dark:text-white">
                        {{ $appointment->location->name }}
                    </p>
                    @if($appointment->location->full_address)
                        <p class="text-xs text-slate-600 dark:text-slate-400 mt-1">
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
    <div class="bg-white dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-800">
        <div class="px-4 py-3 border-b border-slate-200 dark:border-slate-700">
            <h2 class="text-sm font-medium text-slate-900 dark:text-white">Заметки</h2>
        </div>
        <div class="p-4">
            <p class="text-sm text-slate-700 dark:text-slate-300 whitespace-pre-wrap">
                {{ $appointment->notes }}
            </p>
        </div>
    </div>
    @endif

    <!-- Действия -->
    @if(!in_array($appointment->status, ['completed', 'cancelled']))
    <div class="bg-white dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-800 p-4">
        <form method="POST" action="{{ route('public.appointment.cancel', ['token' => $appointment->token]) }}" 
              onsubmit="return confirm('Вы уверены, что хотите отменить эту запись?');">
            @csrf
            <button type="submit" class="w-full px-4 py-2 text-sm font-medium text-white bg-rose-600 hover:bg-rose-700 rounded-lg transition-colors">
                Отменить запись
            </button>
        </form>
    </div>
    @endif

    <!-- Контакты -->
    @if($business->phone)
    <div class="bg-white dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-800 p-4">
        <div class="text-center">
            <p class="text-sm text-slate-600 dark:text-slate-400 mb-2">По вопросам обращайтесь:</p>
            <a href="tel:{{ $business->phone }}" 
               class="inline-flex items-center gap-1.5 text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300">
                <i class="fa-solid fa-phone text-xs"></i>
                <span>{{ $business->phone }}</span>
            </a>
        </div>
    </div>
    @endif

    <!-- Кнопка создания новой записи -->
    <div class="text-center">
        <a href="{{ route('public.appointments.show', $business->slug) }}"
           class="inline-flex items-center gap-1.5 text-sm text-slate-700 dark:text-slate-300 hover:text-indigo-600 dark:hover:text-indigo-400">
            <i class="fa-solid fa-calendar-plus text-xs"></i>
            <span>Создать новую запись</span>
        </a>
    </div>
</div>
@endsection
