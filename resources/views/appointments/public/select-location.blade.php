@extends('appointments.public.layout')

@section('title', 'Выбор локации')

@section('content')
<div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-lg p-6 md:p-8">
    <div class="pb-4 border-b border-slate-200 dark:border-slate-700 mb-6">
        <h2 class="text-xl md:text-2xl font-bold text-slate-900 dark:text-white flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center">
                <i class="fa-solid fa-map-marker-alt text-indigo-600 dark:text-indigo-400 text-lg"></i>
            </div>
            Выберите локацию
        </h2>
    </div>

    @if($locations->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach($locations as $location)
                <a href="{{ route('public.appointments.select-location', ['slug' => $business->slug, 'locationId' => $location->id]) }}"
                   class="group bg-white dark:bg-slate-900 rounded-xl p-6 border-2 border-slate-200 dark:border-slate-700 hover:border-indigo-500 dark:hover:border-indigo-500 cursor-pointer shadow-md hover:shadow-lg transition-all duration-200 hover:-translate-y-1">
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 rounded-lg bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center flex-shrink-0 group-hover:bg-indigo-200 dark:group-hover:bg-indigo-900/50 transition-colors">
                            <i class="fa-solid fa-map-marker-alt text-indigo-600 dark:text-indigo-400 text-xl"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-2 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                                {{ $location->name }}
                            </h3>
                            @if($location->full_address)
                                <p class="text-sm text-slate-600 dark:text-slate-400 leading-relaxed break-words">
                                    {{ $location->full_address }}
                                </p>
                            @endif
                        </div>
                        <div class="flex-shrink-0">
                            <i class="fa-solid fa-chevron-right text-slate-400 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors"></i>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    @else
        <!-- Empty state -->
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-12 text-center">
            <div class="max-w-md mx-auto">
                <div class="w-16 h-16 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center mx-auto mb-4">
                    <i class="fa-solid fa-map-marker-alt text-slate-400 dark:text-slate-500 text-2xl"></i>
                </div>
                <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-2">
                    Нет доступных локаций
                </h3>
                <p class="text-sm text-slate-600 dark:text-slate-400 mb-6">
                    К сожалению, в данный момент нет доступных локаций для записи. Пожалуйста, свяжитесь с нами для уточнения информации.
                </p>
                @if($business->phone)
                    <a href="tel:{{ $business->phone }}" 
                       class="inline-flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg transition-colors shadow-md hover:shadow-lg">
                        <i class="fa-solid fa-phone text-xs"></i>
                        <span>Позвонить</span>
                    </a>
                @endif
            </div>
        </div>
    @endif
</div>
@endsection
