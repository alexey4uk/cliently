@extends('appointments.public.layout')

@section('title', 'Выбор мастера')

@section('content')
<!-- Breadcrumb навигация -->
<div class="mb-4">
    <a href="{{ route('public.appointments.select-location', ['slug' => $business->slug, 'locationId' => $location->id]) }}" 
       class="inline-flex items-center gap-1.5 text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300">
        <i class="fa-solid fa-arrow-left text-xs"></i>
        <span>Назад</span>
    </a>
</div>

<div class="bg-white dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-800 p-4">
    <div class="pb-3 border-b border-slate-200 dark:border-slate-700 mb-4">
        <h2 class="text-lg font-semibold text-slate-900 dark:text-white mb-1">
            Выберите мастера
        </h2>
        <p class="text-sm text-slate-600 dark:text-slate-400">
            {{ $location->name }} • {{ $service->name }}
        </p>
    </div>

    @if($masters->count() > 0)
        <div class="space-y-1">
            @foreach($masters as $master)
                <a href="{{ route('public.appointments.select-time', ['slug' => $business->slug, 'locationId' => $location->id, 'serviceId' => $service->id, 'masterId' => $master->id]) }}"
                   class="flex items-center justify-between p-3 rounded-lg border border-slate-200 dark:border-slate-700 hover:border-indigo-500 dark:hover:border-indigo-500 hover:bg-indigo-50 dark:hover:bg-indigo-900/10 transition-colors group">
                    <div class="flex items-center gap-3 flex-1 min-w-0">
                        @if($master->photo)
                            <img src="{{ asset('storage/' . $master->photo) }}"
                                 alt="{{ $master->first_name }} {{ $master->last_name }}"
                                 class="w-10 h-10 rounded-full object-cover flex-shrink-0">
                        @else
                            <div class="w-10 h-10 rounded-full bg-amber-500 flex items-center justify-center text-white text-xs font-semibold flex-shrink-0">
                                {{ strtoupper(substr($master->first_name, 0, 1) . substr($master->last_name, 0, 1)) }}
                            </div>
                        @endif
                        <div class="flex-1 min-w-0">
                            <h3 class="text-sm font-semibold text-slate-900 dark:text-white truncate">
                                {{ $master->first_name }} {{ $master->last_name }}
                            </h3>
                            @if($master->specialization)
                                <p class="text-xs text-slate-600 dark:text-slate-400 truncate">
                                    {{ $master->specialization }}
                                </p>
                            @endif
                        </div>
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
                Нет доступных мастеров для выбранной услуги.
            </p>
            <a href="{{ route('public.appointments.select-location', ['slug' => $business->slug, 'locationId' => $location->id]) }}" 
               class="inline-flex items-center gap-1.5 text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300">
                <i class="fa-solid fa-arrow-left text-xs"></i>
                <span>Вернуться к услугам</span>
            </a>
        </div>
    @endif
</div>
@endsection
