@extends('appointments.public.layout')

@section('title', 'Выбор услуги')

@section('content')
<div class="max-w-3xl lg:max-w-3xl mx-auto">
    <!-- Кнопка назад -->
    <div class="mb-4 sm:mb-5 lg:mb-4">
        <a href="{{ route('public.appointments.show', $business->slug) }}"
           class="inline-flex items-center gap-2 px-4 py-2 text-sm sm:text-base lg:text-sm text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-200 border border-slate-200 dark:border-slate-700 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors duration-200">
            <i class="fa-solid fa-arrow-left text-xs lg:text-[10px]"></i>
            <span>Вернуться к локациям</span>
        </a>
    </div>

    @if($services->count() > 0)
        <!-- Заголовок -->
        <div class="mb-5 sm:mb-6 lg:mb-5">
            <h1 class="text-2xl sm:text-3xl lg:text-2xl font-bold text-slate-900 dark:text-white mb-2 lg:mb-1.5 leading-tight">
                Выберите услугу
            </h1>
            <p class="text-sm sm:text-base text-slate-600 dark:text-slate-400">
                Локация: {{ $location->name }}
            </p>
        </div>

        <!-- Вертикальный список услуг -->
        <div class="space-y-2.5 sm:space-y-3 lg:space-y-2">
            @foreach($services as $service)
                <a href="{{ route('public.appointments.select-service', ['slug' => $business->slug, 'locationId' => $location->id, 'serviceId' => $service->id]) }}"
                   class="group block w-full bg-white dark:bg-slate-900 rounded-2xl sm:rounded-3xl lg:rounded-xl border border-slate-200 dark:border-slate-800 hover:border-indigo-500 dark:hover:border-indigo-500 hover:shadow-xl transition-all duration-300 overflow-hidden">
                    <div class="p-4 sm:p-5 lg:p-4">
                        <div class="min-w-0">
                            <h2 class="text-lg sm:text-xl lg:text-base font-bold text-slate-900 dark:text-white mb-1.5 sm:mb-2 lg:mb-1.5 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors duration-200">
                                {{ $service->name }}
                            </h2>
                            
                            <!-- Цена и длительность -->
                            <div class="flex items-center gap-2.5 sm:gap-3 lg:gap-2.5 mb-2.5 sm:mb-3 lg:mb-2.5">
                                <div class="flex items-center gap-1.5 lg:gap-1">
                                    <i class="fa-solid fa-tag text-indigo-600 dark:text-indigo-400 text-xs lg:text-[10px]"></i>
                                    <span class="text-sm sm:text-base lg:text-sm font-bold text-indigo-600 dark:text-indigo-400">
                                        {{ number_format($service->price, 0, ',', ' ') }} Br
                                    </span>
                                </div>
                                <span class="text-slate-400 dark:text-slate-500">•</span>
                                <div class="flex items-center gap-1.5 lg:gap-1">
                                    <i class="fa-solid fa-clock text-slate-500 dark:text-slate-400 text-xs lg:text-[10px]"></i>
                                    <span class="text-xs sm:text-sm lg:text-xs text-slate-600 dark:text-slate-400">
                                        {{ $service->duration }} мин
                                    </span>
                                </div>
                            </div>
                            
                            @if($service->description)
                                <p class="text-xs sm:text-sm lg:text-xs text-slate-600 dark:text-slate-400 leading-relaxed mb-2.5 sm:mb-3 lg:mb-2.5">
                                    {{ $service->description }}
                                </p>
                            @endif

                            <!-- Дополнительная информация -->
                            <div class="flex flex-wrap items-center gap-3 sm:gap-4 lg:gap-2.5 pt-3 lg:pt-2.5 border-t border-slate-100 dark:border-slate-800 group-hover:border-indigo-100 dark:group-hover:border-indigo-800/50 transition-colors duration-200">
                                <div class="flex items-center gap-2 lg:gap-1.5 text-sm lg:text-xs text-slate-500 dark:text-slate-400 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors duration-200">
                                    <span>Выбрать</span>
                                    <i class="fa-solid fa-arrow-right text-xs lg:text-[10px] group-hover:translate-x-1 transition-transform duration-200"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    @else
        <!-- Empty state -->
        <div class="text-center py-12 sm:py-16">
            <div class="inline-flex items-center justify-center w-16 h-16 sm:w-20 sm:h-20 rounded-2xl bg-slate-100 dark:bg-slate-800 mb-4">
                <i class="fa-solid fa-spa text-slate-400 dark:text-slate-500 text-2xl sm:text-3xl"></i>
            </div>
            
            <h2 class="text-xl sm:text-2xl lg:text-xl font-bold text-slate-900 dark:text-white mb-2">
                Нет доступных услуг
            </h2>
            
            <p class="text-sm sm:text-base text-slate-600 dark:text-slate-400 mb-6 max-w-md mx-auto">
                К сожалению, в данной локации нет доступных услуг для записи.
            </p>

            <a href="{{ route('public.appointments.show', $business->slug) }}" 
               class="inline-flex items-center justify-center gap-2 px-6 py-3 text-sm sm:text-base font-medium text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-900/20 border border-indigo-200 dark:border-indigo-700/50 rounded-xl hover:bg-indigo-100 dark:hover:bg-indigo-500/30 transition-colors duration-200">
                <i class="fa-solid fa-arrow-left"></i>
                <span>Вернуться к локациям</span>
            </a>
        </div>
    @endif
</div>
@endsection
