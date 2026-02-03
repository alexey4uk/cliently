@extends('appointments.public.layout')

@section('title', 'Выбор услуги')

@section('content')
    <div class="w-full">

        <x-breadcrumbs-public-book :business="$business" currentStep="services" :location="$location" />

        <div class="mb-4">
            <h2 class="text-xl font-bold text-slate-900 dark:text-white tracking-tight">Выберите услугу</h2>
        </div>

        @if ($services->count() > 0)
            <div class="space-y-2 -mx-1 px-1 sm:mx-0 sm:px-0">
                @foreach ($services as $service)
                    <a href="{{ route('public.appointments.select-service', ['slug' => $business->slug, 'locationId' => $location->id, 'serviceId' => $service->id]) }}"
                       class="group block rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 overflow-hidden transition-all duration-200 hover:border-indigo-400 dark:hover:border-indigo-500 hover:shadow-md active:scale-[0.99] touch-manipulation min-h-[56px] sm:min-h-0 flex items-stretch">

                        <div class="px-3 py-3 sm:px-4 sm:py-3 flex items-center gap-3 w-full min-w-0">
                            <div class="shrink-0 w-10 h-10 sm:w-9 sm:h-9 rounded-lg bg-emerald-100 dark:bg-emerald-500/20 flex items-center justify-center text-emerald-600 dark:text-emerald-400 group-hover:bg-indigo-500 group-hover:text-white transition-colors">
                                <i class="fa-solid fa-scissors text-base sm:text-sm"></i>
                            </div>
                            <div class="min-w-0 flex-1">
                                <h3 class="text-base font-semibold text-slate-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors truncate">
                                    {{ $service->name }}
                                </h3>
                                @if ($service->description)
                                    <p class="mt-0.5 text-xs text-slate-500 dark:text-slate-400 line-clamp-1 sm:line-clamp-none sm:truncate max-w-full">
                                        {{ $service->description }}
                                    </p>
                                @endif
                                <div class="mt-1 flex flex-wrap items-center gap-x-3 gap-y-0.5 text-xs text-slate-500 dark:text-slate-400">
                                    <span class="inline-flex items-center gap-1 font-semibold text-indigo-600 dark:text-indigo-400">
                                        {{ number_format($service->price, 0, ',', ' ') }} <span class="font-normal text-slate-400">BYN</span>
                                    </span>
                                    <span class="inline-flex items-center gap-1 shrink-0">
                                        <i class="fa-solid fa-clock"></i>{{ $service->duration }} мин
                                    </span>
                                </div>
                            </div>
                            <span class="shrink-0 w-9 h-9 sm:w-8 sm:h-8 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-400 group-hover:bg-indigo-500 group-hover:text-white transition-colors">
                                <i class="fa-solid fa-chevron-right text-xs"></i>
                            </span>
                        </div>
                    </a>
                @endforeach
            </div>
        @else
            <div class="rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 p-6 sm:p-8 text-center">
                <div class="w-12 h-12 rounded-xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center mx-auto mb-3 text-slate-400">
                    <i class="fa-solid fa-scissors text-xl"></i>
                </div>
                <h3 class="text-base font-semibold text-slate-900 dark:text-white mb-1">Услуг пока нет</h3>
                <p class="text-sm text-slate-500 dark:text-slate-400 mb-4">В выбранном филиале нет доступных услуг</p>
                <a href="{{ route('public.appointments.show', $business->slug) }}"
                   class="inline-flex items-center justify-center min-h-[44px] px-4 py-2.5 rounded-xl text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 transition-colors touch-manipulation">
                    Вернуться к выбору филиала
                </a>
            </div>
        @endif
    </div>
@endsection
