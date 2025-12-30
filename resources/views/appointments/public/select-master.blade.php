@extends('appointments.public.layout')

@section('title', 'Выбор мастера')

@section('content')
<div class="max-w-3xl lg:max-w-3xl mx-auto">
    <!-- Кнопка назад -->
    <div class="mb-4 sm:mb-5 lg:mb-4">
        <a href="{{ route('public.appointments.select-location', ['slug' => $business->slug, 'locationId' => $location->id]) }}"
           class="inline-flex items-center gap-2 px-4 py-2 text-sm sm:text-base lg:text-sm text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-200 border border-slate-200 dark:border-slate-700 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors duration-200">
            <i class="fa-solid fa-arrow-left text-xs lg:text-[10px]"></i>
            <span>Вернуться к услугам</span>
        </a>
    </div>

    @if($masters->count() > 0)
        <!-- Заголовок -->
        <div class="mb-5 sm:mb-6 lg:mb-5">
            <h1 class="text-2xl sm:text-3xl lg:text-2xl font-bold text-slate-900 dark:text-white mb-2 lg:mb-1.5 leading-tight">
                Выберите мастера
            </h1>
            <p class="text-sm sm:text-base text-slate-600 dark:text-slate-400">
                {{ $location->name }} • {{ $service->name }}
            </p>
        </div>

        <!-- Вертикальный список мастеров -->
        <div class="space-y-2.5 sm:space-y-3 lg:space-y-2">
            @foreach($masters as $master)
                <a href="{{ route('public.appointments.select-time', ['slug' => $business->slug, 'locationId' => $location->id, 'serviceId' => $service->id, 'masterId' => $master->id]) }}"
                   class="group block w-full bg-white dark:bg-slate-900 rounded-2xl sm:rounded-3xl lg:rounded-xl border border-slate-200 dark:border-slate-800 hover:border-indigo-500 dark:hover:border-indigo-500 hover:shadow-xl transition-all duration-300 overflow-hidden">
                    <div class="p-4 sm:p-5 lg:p-4">
                        <div class="flex items-start gap-3 sm:gap-4 lg:gap-3">
                            <!-- Фото или инициалы -->
                            <div class="flex-shrink-0">
                                @if($master->photo)
                                    <img src="{{ asset('storage/' . $master->photo) }}"
                                         alt="{{ $master->first_name }} {{ $master->last_name }}"
                                         class="w-10 h-10 sm:w-12 sm:h-12 lg:w-10 lg:h-10 rounded-xl object-cover">
                                @else
                                    <div class="w-10 h-10 sm:w-12 sm:h-12 lg:w-10 lg:h-10 rounded-xl bg-gradient-to-br from-amber-400 to-amber-600 flex items-center justify-center text-white font-bold text-xs sm:text-sm lg:text-xs">
                                        {{ strtoupper(substr($master->first_name, 0, 1) . substr($master->last_name, 0, 1)) }}
                                    </div>
                                @endif
                            </div>

                            <!-- Контент -->
                            <div class="flex-1 min-w-0">
                                <h2 class="text-lg sm:text-xl lg:text-base font-bold text-slate-900 dark:text-white mb-1 sm:mb-1.5 lg:mb-1 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors duration-200">
                                    {{ $master->first_name }} {{ $master->last_name }}
                                </h2>
                                
                                @if($master->specialization)
                                    <div class="flex items-center gap-1.5 mb-2 sm:mb-2.5 lg:mb-2">
                                        <i class="fa-solid fa-star text-amber-500 text-xs lg:text-[10px]"></i>
                                        <p class="text-xs sm:text-sm lg:text-xs text-slate-600 dark:text-slate-400">
                                            {{ $master->specialization }}
                                        </p>
                                    </div>
                                @endif

                                @if($master->description)
                                    <p class="text-xs sm:text-sm lg:text-xs text-slate-600 dark:text-slate-400 leading-relaxed mb-2.5 sm:mb-3 lg:mb-2.5">
                                        {{ $master->description }}
                                    </p>
                                @endif

                                <!-- Дополнительная информация -->
                                <div class="flex flex-wrap items-center gap-3 sm:gap-4 lg:gap-2.5 pt-3 lg:pt-2.5 border-t border-slate-100 dark:border-slate-800 group-hover:border-indigo-100 dark:group-hover:border-indigo-800/50 transition-colors duration-200">
                                    @if($master->phone)
                                        <div class="flex items-center gap-2 lg:gap-1.5 text-sm lg:text-xs text-slate-500 dark:text-slate-400">
                                            <i class="fa-solid fa-phone text-xs lg:text-[10px]"></i>
                                            <span>{{ $master->phone }}</span>
                                        </div>
                                    @endif
                                    
                                    <div class="flex items-center gap-2 lg:gap-1.5 text-sm lg:text-xs text-slate-500 dark:text-slate-400 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors duration-200">
                                        <span>Выбрать</span>
                                        <i class="fa-solid fa-arrow-right text-xs lg:text-[10px] group-hover:translate-x-1 transition-transform duration-200"></i>
                                    </div>
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
                <i class="fa-solid fa-user-tie text-slate-400 dark:text-slate-500 text-2xl sm:text-3xl"></i>
            </div>
            
            <h2 class="text-xl sm:text-2xl lg:text-xl font-bold text-slate-900 dark:text-white mb-2">
                Нет доступных мастеров
            </h2>
            
            <p class="text-sm sm:text-base text-slate-600 dark:text-slate-400 mb-6 max-w-md mx-auto">
                К сожалению, для выбранной услуги и локации нет доступных мастеров.
            </p>

            <a href="{{ route('public.appointments.select-location', ['slug' => $business->slug, 'locationId' => $location->id]) }}" 
               class="inline-flex items-center justify-center gap-2 px-6 py-3 text-sm sm:text-base font-medium text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-900/20 border border-indigo-200 dark:border-indigo-700/50 rounded-xl hover:bg-indigo-100 dark:hover:bg-indigo-500/30 transition-colors duration-200">
                <i class="fa-solid fa-arrow-left"></i>
                <span>Вернуться к услугам</span>
            </a>
        </div>
    @endif
</div>
@endsection
