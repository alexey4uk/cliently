@extends('appointments.public.layout')

@section('title', 'Выбор локации')

@section('content')
<div class="bg-white dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-800 p-4">
    <div class="pb-3 border-b border-slate-200 dark:border-slate-700 mb-4">
        <h2 class="text-lg font-semibold text-slate-900 dark:text-white">
            Выберите локацию
        </h2>
    </div>

    @if($locations->count() > 0)
        <div class="space-y-1">
            @foreach($locations as $location)
                <a href="{{ route('public.appointments.select-location', ['slug' => $business->slug, 'locationId' => $location->id]) }}"
                   class="flex items-center justify-between p-3 rounded-lg border border-slate-200 dark:border-slate-700 hover:border-indigo-500 dark:hover:border-indigo-500 hover:bg-indigo-50 dark:hover:bg-indigo-900/10 transition-colors">
                    <div class="flex-1 min-w-0">
                        <h3 class="text-sm font-semibold text-slate-900 dark:text-white truncate">
                            {{ $location->name }}
                        </h3>
                        @if($location->full_address)
                            <p class="text-xs text-slate-600 dark:text-slate-400 truncate">
                                {{ $location->full_address }}
                            </p>
                        @endif
                    </div>
                    <div class="flex-shrink-0 ml-3">
                        <i class="fa-solid fa-chevron-right text-slate-400 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 text-sm transition-colors"></i>
                    </div>
                </a>
            @endforeach
        </div>
    @else
        <!-- Empty state -->
        <div class="text-center py-8">
            <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">
                Нет доступных локаций для записи.
            </p>
            @if($business->phone)
                <a href="tel:{{ $business->phone }}" 
                   class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300">
                    <i class="fa-solid fa-phone text-xs"></i>
                    <span>Позвонить</span>
                </a>
            @endif
        </div>
    @endif
</div>
@endsection
