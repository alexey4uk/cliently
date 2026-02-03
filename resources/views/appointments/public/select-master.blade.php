@extends('appointments.public.layout')

@section('title', 'Выбор мастера')

@section('content')
    <div class="max-w-3xl lg:max-w-3xl mx-auto sm:px-0">

        <x-breadcrumbs-public-book :business="$business" currentStep="master" :location="$location" :service="$service" />

        @if ($masters->count() > 0)
            <!-- Любой мастер -->
            <div class="mb-6">
                <a href="{{ route('public.appointments.select-time-any', ['slug' => $business->slug, 'locationId' => $location->id, 'serviceId' => $service->id]) }}"
                    class="block bg-white dark:bg-slate-900 rounded-2xl border-2 border-dashed border-indigo-200 dark:border-indigo-800 hover:border-indigo-500 dark:hover:border-indigo-500 shadow-sm hover:shadow-lg transition-all duration-300 overflow-hidden group">
                    <div class="p-5 sm:p-6">
                        <div class="flex items-center gap-5">
                            <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl bg-indigo-50 dark:bg-indigo-500/20 flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">
                                <i class="fa-solid fa-users text-2xl text-indigo-600 dark:text-indigo-400"></i>
                            </div>
                            <div class="min-w-0 flex-1">
                                <h2 class="text-xl font-bold text-slate-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                                    Любой мастер
                                </h2>
                                <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">Вам назначат свободного мастера</p>
                            </div>
                            <div class="w-10 h-10 rounded-full bg-slate-50 dark:bg-slate-800 group-hover:bg-indigo-600 flex items-center justify-center transition-all shrink-0">
                                <i class="fa-solid fa-chevron-right text-slate-400 group-hover:text-white"></i>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <!-- Список мастеров -->
            <div class="space-y-4 animate-in fade-in slide-in-from-bottom-4 duration-700">
                @foreach ($masters as $master)
                    <div class="group relative">
                        <!-- Основная ссылка-карточка -->
                        <a href="{{ route('public.appointments.select-time', ['slug' => $business->slug, 'locationId' => $location->id, 'serviceId' => $service->id, 'masterId' => $master->id]) }}"
                            class="block bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 hover:border-indigo-500 dark:hover:border-indigo-500 shadow-sm hover:shadow-xl hover:shadow-indigo-500/10 transition-all duration-300 overflow-hidden">

                            <div class="p-5 sm:p-6">
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-5">
                                    <div class="flex items-center gap-5 flex-1 min-w-0">
                                        <!-- Аватар (Квадратный со скруглением как иконки в филиалах) -->
                                        <div class="relative shrink-0">
                                            @if ($master->photo)
                                                <img src="{{ asset('storage/' . $master->photo) }}"
                                                    alt="{{ $master->first_name }}"
                                                    class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl object-cover ring-1 ring-slate-100 dark:ring-slate-800 shadow-sm group-hover:scale-105 transition-transform duration-500">
                                            @else
                                                <div
                                                    class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-400 text-xl font-black group-hover:bg-indigo-50 dark:group-hover:bg-indigo-900/20 transition-colors">
                                                    {{ mb_substr($master->first_name, 0, 1) }}{{ mb_substr($master->last_name, 0, 1) }}
                                                </div>
                                            @endif

                                            <!-- Статус-точка (как в услугах/филиалах) -->
                                            <div
                                                class="absolute -bottom-1 -right-1 w-5 h-5 bg-white dark:bg-slate-900 rounded-full flex items-center justify-center">
                                                <div
                                                    class="w-3 h-3 bg-emerald-500 rounded-full ring-2 ring-emerald-500/20 animate-pulse">
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Инфо о мастере -->
                                        <div class="min-w-0">
                                            <h2
                                                class="text-xl font-bold text-slate-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors mb-1 truncate">
                                                {{ $master->first_name }} {{ $master->last_name }}
                                            </h2>

                                            @if ($master->specialization)
                                                <p
                                                    class="text-[10px] font-bold text-indigo-500 uppercase tracking-widest mb-3 leading-none">
                                                    {{ $master->specialization }}
                                                </p>
                                            @endif

                                            <div class="flex items-center gap-4">
                                                <div
                                                    class="flex items-center gap-1.5 text-sm font-black text-slate-700 dark:text-slate-300">
                                                    <i class="fa-solid fa-star text-[10px] text-amber-400"></i>
                                                    <span>5.0</span>
                                                </div>
                                                <span class="w-1 h-1 rounded-full bg-slate-200 dark:bg-slate-700"></span>
                                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                                                    Ближайшее: <span class="text-slate-900 dark:text-slate-200">14:30</span>
                                                </p>
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
                                    <div class="sm:hidden">
                                        <div
                                            class="w-full py-3 bg-slate-50 dark:bg-slate-800 rounded-xl text-center text-[11px] font-black uppercase tracking-widest text-indigo-600 dark:text-indigo-400 group-hover:bg-indigo-600 group-hover:text-white transition-all">
                                            Выбрать мастера
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
                    <i class="fa-solid fa-user-slash text-3xl"></i>
                </div>
                <h2 class="text-2xl font-bold text-slate-900 dark:text-white mb-3">Мастер не найден</h2>
                <a href="{{ url()->previous() }}"
                    class="text-indigo-600 dark:text-indigo-400 font-bold hover:underline underline-offset-4">
                    Вернуться назад
                </a>
            </div>
        @endif
    </div>
@endsection
