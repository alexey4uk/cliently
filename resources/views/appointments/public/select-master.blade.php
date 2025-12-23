@extends('appointments.public.layout')

@section('title', 'Выбор мастера')

@section('content')
<!-- Breadcrumb навигация -->
<div class="mb-6">
    <a href="{{ route('public.appointments.select-location', ['slug' => $business->slug, 'locationId' => $location->id]) }}" 
       class="inline-flex items-center gap-2 text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 transition-colors">
        <i class="fa-solid fa-arrow-left"></i>
        <span>Назад к выбору услуги</span>
    </a>
</div>

<div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-lg p-6 md:p-8">
    <div class="pb-4 border-b border-slate-200 dark:border-slate-700 mb-6">
        <h2 class="text-xl md:text-2xl font-bold text-slate-900 dark:text-white flex items-center gap-3 mb-2">
            <div class="w-10 h-10 rounded-lg bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center">
                <i class="fa-solid fa-user-tie text-indigo-600 dark:text-indigo-400 text-lg"></i>
            </div>
            Выберите мастера
        </h2>
        <div class="flex flex-wrap items-center gap-2 text-sm text-slate-600 dark:text-slate-400">
            <span class="flex items-center gap-1.5">
                <i class="fa-solid fa-map-marker-alt text-xs"></i>
                <span>{{ $location->name }}</span>
            </span>
            <span>•</span>
            <span class="flex items-center gap-1.5">
                <i class="fa-solid fa-spa text-xs"></i>
                <span>{{ $service->name }}</span>
            </span>
        </div>
    </div>

    @if($masters->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach($masters as $master)
                <a href="{{ route('public.appointments.select-time', ['slug' => $business->slug, 'locationId' => $location->id, 'serviceId' => $service->id, 'masterId' => $master->id]) }}"
                   class="group bg-white dark:bg-slate-900 rounded-xl p-6 border-2 border-slate-200 dark:border-slate-700 hover:border-indigo-500 dark:hover:border-indigo-500 cursor-pointer shadow-md hover:shadow-xl transition-all duration-200 hover:-translate-y-1">
                    <div class="flex items-start gap-4">
                        @if($master->photo)
                            <img src="{{ asset('storage/' . $master->photo) }}" 
                                 alt="{{ $master->first_name }} {{ $master->last_name }}"
                                 class="w-20 h-20 rounded-full object-cover ring-4 ring-indigo-100 dark:ring-indigo-900/30 group-hover:ring-indigo-200 dark:group-hover:ring-indigo-800 transition-all flex-shrink-0">
                        @else
                            <div class="w-20 h-20 rounded-full bg-gradient-to-br from-indigo-400 to-purple-500 flex items-center justify-center text-white text-2xl font-bold ring-4 ring-indigo-100 dark:ring-indigo-900/30 group-hover:ring-indigo-200 dark:group-hover:ring-indigo-800 transition-all flex-shrink-0">
                                {{ strtoupper(substr($master->first_name, 0, 1) . substr($master->last_name, 0, 1)) }}
                            </div>
                        @endif
                        <div class="flex-1 min-w-0">
                            <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-1 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors break-words">
                                {{ $master->first_name }} {{ $master->last_name }}
                            </h3>
                            @if($master->specialization)
                                <p class="text-sm text-slate-600 dark:text-slate-400 leading-relaxed break-words">
                                    {{ $master->specialization }}
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
                    <i class="fa-solid fa-user-tie text-slate-400 dark:text-slate-500 text-2xl"></i>
                </div>
                <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-2">
                    Нет доступных мастеров
                </h3>
                <p class="text-sm text-slate-600 dark:text-slate-400 mb-6">
                    Для выбранной услуги в данной локации нет доступных мастеров. Пожалуйста, выберите другую услугу или локацию.
                </p>
                <a href="{{ route('public.appointments.select-location', ['slug' => $business->slug, 'locationId' => $location->id]) }}" 
                   class="inline-flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg transition-colors shadow-md hover:shadow-lg">
                    <i class="fa-solid fa-arrow-left text-xs"></i>
                    <span>Вернуться к услугам</span>
                </a>
            </div>
        </div>
    @endif
</div>
@endsection
