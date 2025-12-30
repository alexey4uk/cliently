@extends('appointments.public.layout')

@section('title', 'Выбор локации')

@section('content')
<div class="max-w-3xl lg:max-w-3xl mx-auto">
    @if($locations->count() > 0)
        <!-- Заголовок -->
        <div class="mb-5 sm:mb-6 lg:mb-5">
            <h1 class="text-2xl sm:text-3xl lg:text-2xl font-bold text-slate-900 dark:text-white mb-2 lg:mb-1.5 leading-tight">
                Где вам удобно?
            </h1>
            <p class="text-sm sm:text-base text-slate-600 dark:text-slate-400">
                Выберите локацию для записи
            </p>
        </div>

        <!-- Вертикальный список локаций -->
        <div class="space-y-2.5 sm:space-y-3 lg:space-y-2">
            @foreach($locations as $location)
                <a href="{{ route('public.appointments.select-location', ['slug' => $business->slug, 'locationId' => $location->id]) }}"
                   class="group block w-full bg-white dark:bg-slate-900 rounded-2xl sm:rounded-3xl lg:rounded-xl border border-slate-200 dark:border-slate-800 hover:border-indigo-500 dark:hover:border-indigo-500 hover:shadow-xl transition-all duration-300 overflow-hidden">
                    <div class="p-4 sm:p-5 lg:p-4">
                        <div class="min-w-0">
                                <h2 class="text-lg sm:text-xl lg:text-base font-bold text-slate-900 dark:text-white mb-1.5 sm:mb-2 lg:mb-1.5 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors duration-200">
                                    {{ $location->name }}
                                </h2>
                                
                                @if($location->full_address)
                                    <div class="flex items-start gap-2 mb-3 sm:mb-4 lg:mb-2.5">
                                        <i class="fa-solid fa-location-dot text-slate-400 dark:text-slate-500 mt-0.5 lg:mt-0 flex-shrink-0 text-xs"></i>
                                        <p class="text-xs sm:text-sm lg:text-xs text-slate-600 dark:text-slate-400 leading-relaxed">
                                            {{ $location->full_address }}
                                        </p>
                                    </div>
                                @endif

                                <!-- Дополнительная информация -->
                                <div class="flex flex-wrap items-center gap-3 sm:gap-4 lg:gap-2.5 pt-3 lg:pt-2.5 border-t border-slate-100 dark:border-slate-800 group-hover:border-indigo-100 dark:group-hover:border-indigo-800/50 transition-colors duration-200">
                                    @if($location->phone)
                                        <div class="flex items-center gap-2 lg:gap-1.5 text-sm lg:text-xs text-slate-500 dark:text-slate-400">
                                            <i class="fa-solid fa-phone text-xs lg:text-[10px]"></i>
                                            <span>{{ $location->phone }}</span>
                                        </div>
                                    @endif
                                    
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
                <i class="fa-solid fa-map-marker-alt text-slate-400 dark:text-slate-500 text-2xl sm:text-3xl"></i>
            </div>
            
            <h2 class="text-xl sm:text-2xl lg:text-xl font-bold text-slate-900 dark:text-white mb-2">
                Нет доступных локаций
            </h2>
            
            <p class="text-sm sm:text-base text-slate-600 dark:text-slate-400 mb-6 max-w-md mx-auto">
                К сожалению, в данный момент нет доступных локаций для записи.
            </p>

            @if($business->phone)
                <a href="tel:{{ $business->phone }}" 
                   class="inline-flex items-center justify-center gap-2 px-6 py-3 text-sm sm:text-base font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl transition-colors duration-200 shadow-lg hover:shadow-xl">
                    <i class="fa-solid fa-phone"></i>
                    <span>Позвонить: {{ $business->phone }}</span>
                </a>
            @endif
        </div>
    @endif
</div>
@endsection
