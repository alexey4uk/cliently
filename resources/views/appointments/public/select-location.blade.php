@extends('appointments.public.layout')

@section('title', 'Выбор филиала')

@section('content')
    <div class="w-full">

        <x-breadcrumbs-public-book :business="$business" currentStep="locations" />

        <div class="mb-4">
            <h2 class="text-xl font-bold text-slate-900 dark:text-white tracking-tight">Выберите филиал</h2>
        </div>

        @if ($locations->count() > 0)
            <div class="space-y-2 -mx-1 px-1 sm:mx-0 sm:px-0">
                @foreach ($locations as $location)
                    <a href="{{ route('public.appointments.select-location', ['slug' => $business->slug, 'locationId' => $location->id]) }}"
                       class="group block rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 overflow-hidden transition-all duration-200 hover:border-indigo-400 dark:hover:border-indigo-500 hover:shadow-md active:scale-[0.99] touch-manipulation min-h-[56px] sm:min-h-0 flex items-stretch">

                        <div class="px-3 py-3 sm:px-4 sm:py-3 flex items-center gap-3 w-full min-w-0">
                            <div class="shrink-0 w-10 h-10 sm:w-9 sm:h-9 rounded-lg bg-indigo-100 dark:bg-indigo-500/20 flex items-center justify-center text-indigo-600 dark:text-indigo-400 group-hover:bg-indigo-500 group-hover:text-white transition-colors">
                                <i class="fa-solid fa-location-dot text-base sm:text-sm"></i>
                            </div>
                            <div class="min-w-0 flex-1">
                                <h3 class="text-base font-semibold text-slate-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors truncate">
                                    {{ $location->name }}
                                </h3>
                                {{-- На смартфоне: адрес отдельной строкой, телефон и время — второй строкой --}}
                                @if ($location->full_address)
                                    <p class="mt-0.5 text-xs text-slate-500 dark:text-slate-400 line-clamp-1 sm:line-clamp-none sm:truncate max-w-full">
                                        {{ $location->full_address }}
                                    </p>
                                @endif
                                <div class="mt-1 flex flex-wrap items-center gap-x-3 gap-y-0.5 text-xs text-slate-500 dark:text-slate-400">
                                    @if ($location->phone)
                                        <span class="inline-flex items-center gap-1 shrink-0 touch-manipulation"><i class="fa-solid fa-phone text-indigo-500"></i>{{ $location->phone }}</span>
                                    @endif
                                    @if ($location->working_hours_display)
                                        <span class="inline-flex items-center gap-1 shrink-0"><i class="fa-solid fa-clock"></i>{{ $location->working_hours_display }}</span>
                                    @endif
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
                    <i class="fa-solid fa-map-location-dot text-xl"></i>
                </div>
                <h3 class="text-base font-semibold text-slate-900 dark:text-white mb-1">Нет доступных филиалов</h3>
                <p class="text-sm text-slate-500 dark:text-slate-400 mb-4">Свяжитесь с нами для записи</p>
                @if ($business->phone ?? null)
                    <a href="tel:{{ $business->phone }}" class="inline-flex items-center justify-center gap-1.5 min-h-[44px] px-5 py-3 rounded-xl text-sm font-medium bg-indigo-600 text-white hover:bg-indigo-700 active:bg-indigo-800 transition-colors touch-manipulation">
                        <i class="fa-solid fa-phone text-xs"></i>
                        Позвонить
                    </a>
                @endif
            </div>
        @endif
    </div>
@endsection
