@extends('appointments.public.layout')

@section('title', 'Выбор локации')

@section('content')
    <div class="max-w-3xl lg:max-w-3xl mx-auto sm:px-0">

        <x-breadcrumbs-public-book :business="$business" currentStep="locations" />


        @if ($locations->count() > 0)
            <!-- Список локаций -->
            <div class="space-y-4 animate-in fade-in slide-in-from-bottom-4 duration-700">
                @foreach ($locations as $location)
                    <div class="group relative">
                        <!-- Основная ссылка-карточка -->
                        <a href="{{ route('public.appointments.select-location', ['slug' => $business->slug, 'locationId' => $location->id]) }}"
                            class="block bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 hover:border-indigo-500 dark:hover:border-indigo-500 shadow-sm hover:shadow-xl hover:shadow-indigo-500/10 transition-all duration-300 overflow-hidden">

                            <div class="p-5 sm:p-6">
                                <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
                                    <div class="flex-1 min-w-0">
                                        <!-- Статус и Название -->
                                        <div class="flex items-center gap-3 mb-2">
                                            <h2
                                                class="text-xl font-bold text-slate-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                                                {{ $location->name }}
                                            </h2>
                                            <span
                                                class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-emerald-100 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400">
                                                Открыто
                                            </span>
                                        </div>

                                        <!-- Адрес -->
                                        @if ($location->full_address)
                                            <div class="flex items-start gap-3 text-slate-500 dark:text-slate-400 mb-4">
                                                <p class="text-sm leading-6 line-clamp-2">
                                                    {{ $location->full_address }}
                                                </p>
                                            </div>
                                        @endif

                                        <!-- Инфо-панель -->
                                        <div
                                            class="flex flex-wrap items-center gap-y-2 gap-x-6 pt-4 border-t border-slate-100 dark:border-slate-800">
                                            @if ($location->phone)
                                                <div
                                                    class="flex items-center gap-2 text-sm font-medium text-slate-600 dark:text-slate-400 hover:text-indigo-500 transition-colors">
                                                    <i class="fa-solid fa-phone text-xs opacity-70 text-indigo-500"></i>
                                                    {{ $location->phone }}
                                                </div>
                                            @endif

                                            <div
                                                class="flex items-center gap-2 text-sm font-medium text-slate-500 dark:text-slate-400">
                                                <i class="fa-solid fa-clock text-xs opacity-70"></i>
                                                <span>09:00 – 21:00</span>
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
                                            Выбрать филиал
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
                class="text-center py-20 bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800">
                <div
                    class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-slate-100 dark:bg-slate-800 mb-6 text-slate-400">
                    <i class="fa-solid fa-map-location-dot text-3xl"></i>
                </div>
                <h2 class="text-2xl font-bold text-slate-900 dark:text-white mb-3">Локации не найдены</h2>
                @if ($business->phone)
                    <a href="tel:{{ $business->phone }}" class="text-indigo-600 font-bold">Позвонить нам</a>
                @endif
            </div>
        @endif
    </div>
@endsection
