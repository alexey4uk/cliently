@extends('appointments.public.layout')

@section('title', 'Выбор услуги')

@section('content')
    <div class="max-w-3xl lg:max-w-3xl mx-auto sm:px-0">

        <x-breadcrumbs-public-book :business="$business" currentStep="services" :location="$location" />


        @if ($services->count() > 0)
            <!-- Список услуг в стиле локаций -->
            <div class="space-y-4 animate-in fade-in slide-in-from-bottom-4 duration-700">
                @foreach ($services as $service)
                    <div class="group relative">
                        <!-- Основная ссылка-карточка -->
                        <a href="{{ route('public.appointments.select-service', ['slug' => $business->slug, 'locationId' => $location->id, 'serviceId' => $service->id]) }}"
                            class="block bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 hover:border-indigo-500 dark:hover:border-indigo-500 shadow-sm hover:shadow-xl hover:shadow-indigo-500/10 transition-all duration-300 overflow-hidden">

                            <div class="p-5 sm:p-6">
                                <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
                                    <div class="flex-1 min-w-0">
                                        <!-- Название услуги -->
                                        <div class="flex items-center gap-3 mb-2">
                                            <h2
                                                class="text-xl font-bold text-slate-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                                                {{ $service->name }}
                                            </h2>
                                        </div>

                                        <!-- Описание услуги -->
                                        @if ($service->description)
                                            <div class="flex items-start gap-3 text-slate-500 dark:text-slate-400 mb-4">
                                                <p class="text-sm leading-6 line-clamp-2">
                                                    {{ $service->description }}
                                                </p>
                                            </div>
                                        @endif

                                        <!-- Инфо-панель (Цена и Время) — как в локациях -->
                                        <div
                                            class="flex flex-wrap items-center gap-y-2 gap-x-6 pt-4 border-t border-slate-100 dark:border-slate-800">
                                            <!-- Цена -->
                                            <div
                                                class="flex items-center gap-2 text-base font-black text-indigo-600 dark:text-indigo-400">
                                                <i class="fa-solid fa-tag text-xs opacity-70"></i>
                                                {{ number_format($service->price, 0, ',', ' ') }}
                                                <span
                                                    class="text-[10px] font-bold uppercase ml-0.5 tracking-tighter text-slate-400">Br</span>
                                            </div>

                                            <!-- Длительность -->
                                            <div
                                                class="flex items-center gap-2 text-sm font-medium text-slate-500 dark:text-slate-400">
                                                <i class="fa-solid fa-clock text-xs opacity-70"></i>
                                                <span>{{ $service->duration }} мин</span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Кнопка "Выбрать" (десктоп) -->
                                    <div class="hidden sm:flex items-center">
                                        <div
                                            class="w-10 h-10 rounded-full bg-slate-50 dark:bg-slate-800 group-hover:bg-indigo-600 flex items-center justify-center transition-all duration-300">
                                            <i
                                                class="fa-solid fa-chevron-right text-slate-400 group-hover:text-white transition-colors"></i>
                                        </div>
                                    </div>

                                    <!-- Мобильная кнопка -->
                                    <div class="sm:hidden mt-2">
                                        <div
                                            class="w-full py-3 bg-slate-50 dark:bg-slate-800 rounded-xl text-center text-sm font-bold text-indigo-600 dark:text-indigo-400 group-hover:bg-indigo-600 group-hover:text-white transition-all">
                                            Выбрать услугу
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        @else
            <!-- Empty state -->
            <div
                class="text-center py-20 bg-white dark:bg-slate-900 rounded-3xl border-2 border-dashed border-slate-200 dark:border-slate-800">
                <div
                    class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-slate-100 dark:bg-slate-800 mb-6 text-slate-400">
                    <i class="fa-solid fa-wand-magic-sparkles text-3xl"></i>
                </div>
                <h2 class="text-2xl font-bold text-slate-900 dark:text-white mb-3 text-center">Услуг пока нет</h2>
                <a href="{{ route('public.appointments.show', $business->slug) }}"
                    class="text-indigo-600 dark:text-indigo-400 font-bold hover:underline underline-offset-4">
                    Вернуться к выбору локации
                </a>
            </div>
        @endif
    </div>
@endsection
