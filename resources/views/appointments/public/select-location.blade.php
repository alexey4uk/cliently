@extends('appointments.public.layout')

@section('title', 'Выбор локации')

@section('content')
<div class="glass-card rounded-xl p-4 md:p-6">
    <div class="pb-4 border-b border-slate-200 dark:border-slate-700 mb-5">
        <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-300 flex items-center gap-2">
            <i class="fa-solid fa-map-marker-alt text-indigo-600 dark:text-indigo-400"></i>
            Выберите локацию
        </h3>
    </div>

    <div class="space-y-3">
        @foreach($locations as $location)
            <a href="{{ route('public.appointments.select-location', ['slug' => $business->slug, 'locationId' => $location->id]) }}"
               class="flex items-start p-4 border-2 border-slate-200 dark:border-slate-700 rounded-lg hover-border cursor-pointer bg-white/50 dark:bg-slate-800/50">
                <div class="flex-1">
                    <div class="font-semibold text-slate-900 dark:text-white text-base md:text-sm">{{ $location->name }}</div>
                    @if($location->full_address)
                        <div class="text-sm text-slate-600 dark:text-slate-400 mt-1">{{ $location->full_address }}</div>
                    @endif
                </div>
                <i class="fa-solid fa-chevron-right text-slate-400 mt-1"></i>
            </a>
        @endforeach
    </div>
</div>
@endsection
