@extends('appointments.public.layout')

@section('title', 'Выбор услуги')

@section('content')
<div class="mb-4">
    <a href="{{ route('public.appointments.show', $business->slug) }}" 
       class="text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 transition-colors">
        <i class="fa-solid fa-arrow-left mr-2"></i>Назад к выбору локации
    </a>
</div>

<div class="glass-card rounded-xl p-4 md:p-6">
    <div class="pb-4 border-b border-slate-200 dark:border-slate-700 mb-5">
        <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-300 flex items-center gap-2">
            <i class="fa-solid fa-spa text-indigo-600 dark:text-indigo-400"></i>
            Выберите услугу
        </h3>
        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Локация: {{ $location->name }}</p>
    </div>

    <div class="space-y-3">
        @foreach($services as $service)
            <a href="{{ route('public.appointments.select-service', ['slug' => $business->slug, 'locationId' => $location->id, 'serviceId' => $service->id]) }}"
               class="flex items-start p-4 border-2 border-slate-200 dark:border-slate-700 rounded-lg hover-border cursor-pointer bg-white/50 dark:bg-slate-800/50">
                <div class="flex-1">
                    <div class="font-semibold text-slate-900 dark:text-white text-base md:text-sm">{{ $service->name }}</div>
                    <div class="text-sm text-slate-600 dark:text-slate-400 mt-1">
                        <span class="font-medium">{{ number_format($service->price, 0, ',', ' ') }} Br</span>
                        <span class="mx-2">•</span>
                        <span>{{ $service->duration }} мин</span>
                    </div>
                    @if($service->description)
                        <div class="text-sm text-slate-500 dark:text-slate-500 mt-2">{{ $service->description }}</div>
                    @endif
                </div>
                <i class="fa-solid fa-chevron-right text-slate-400 mt-1"></i>
            </a>
        @endforeach
    </div>
</div>
@endsection
