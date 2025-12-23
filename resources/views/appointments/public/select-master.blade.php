@extends('appointments.public.layout')

@section('title', 'Выбор мастера')

@section('content')
<div class="mb-4">
    <a href="{{ route('public.appointments.select-location', ['slug' => $business->slug, 'locationId' => $location->id]) }}" 
       class="text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 transition-colors">
        <i class="fa-solid fa-arrow-left mr-2"></i>Назад к выбору услуги
    </a>
</div>

<div class="glass-card rounded-xl p-4 md:p-6">
    <div class="pb-4 border-b border-slate-200 dark:border-slate-700 mb-5">
        <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-300 flex items-center gap-2">
            <i class="fa-solid fa-user-tie text-indigo-600 dark:text-indigo-400"></i>
            Выберите мастера
        </h3>
        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
            Локация: {{ $location->name }} • Услуга: {{ $service->name }}
        </p>
    </div>

    <div class="space-y-3">
        @foreach($masters as $master)
            <a href="{{ route('public.appointments.select-time', ['slug' => $business->slug, 'locationId' => $location->id, 'serviceId' => $service->id, 'masterId' => $master->id]) }}"
               class="flex items-start p-4 border-2 border-slate-200 dark:border-slate-700 rounded-lg hover-border cursor-pointer bg-white/50 dark:bg-slate-800/50">
                @if($master->photo)
                    <img src="{{ asset('storage/' . $master->photo) }}" 
                         alt="{{ $master->first_name }} {{ $master->last_name }}"
                         class="w-12 h-12 rounded-lg object-cover mr-3 flex-shrink-0">
                @else
                    <div class="w-12 h-12 rounded-lg bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center mr-3 flex-shrink-0">
                        <i class="fa-solid fa-user text-indigo-600 dark:text-indigo-400"></i>
                    </div>
                @endif
                <div class="flex-1">
                    <div class="font-semibold text-slate-900 dark:text-white text-base md:text-sm">
                        {{ $master->first_name }} {{ $master->last_name }}
                    </div>
                    @if($master->specialization)
                        <div class="text-sm text-slate-600 dark:text-slate-400 mt-1">{{ $master->specialization }}</div>
                    @endif
                </div>
                <i class="fa-solid fa-chevron-right text-slate-400 mt-1"></i>
            </a>
        @endforeach
    </div>
</div>
@endsection
