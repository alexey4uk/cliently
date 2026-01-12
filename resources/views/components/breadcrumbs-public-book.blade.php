@props([
    'business' => null,
    'currentStep' => 'time',
    'location' => null,
    'service' => null,
])

@php
    $steps = [
        'locations' => ['name' => 'Локация', 'route' => $business ? route('public.appointments.show', $business->slug) : null],
        'services' => ['name' => 'Услуга', 'route' => $location ? route('public.appointments.select-location', ['slug' => $business->slug, 'locationId' => $location->id]) : null],
        'master' => ['name' => 'Мастер', 'route' => $service ? route('public.appointments.select-service', ['slug' => $business->slug, 'serviceId' => $service->id, 'locationId' => $location->id]) : null],
        'time' => ['name' => 'Время', 'route' => null],
    ];
    
    $stepKeys = array_keys($steps);
    $currentIndex = array_search($currentStep, $stepKeys);
@endphp

<div class="mb-6 pt-1">
    <div class="overflow-x-auto pb-1 -mx-3 px-3 sm:mx-0 sm:px-0 sm:pb-0">
        <nav class="flex items-center gap-1.5 text-[9px] sm:text-[10px] uppercase tracking-[0.15em] font-black whitespace-nowrap">
            @foreach($steps as $key => $step)
                @if(!$loop->first)
                    <i class="fa-solid fa-chevron-right text-[7px] text-slate-300 mx-0.5"></i>
                @endif
                
                @php
                    $stepIndex = array_search($key, $stepKeys);
                    $isCurrent = $key === $currentStep;
                    $isPast = $stepIndex < $currentIndex;
                @endphp
                
                @if($isCurrent)
                    <span class="px-2 py-0.5 rounded bg-indigo-600 text-white">
                        {{ $step['name'] }}
                    </span>
                @elseif($isPast && $step['route'])
                    <a href="{{ $step['route'] }}"
                       class="px-2 py-0.5 rounded text-slate-600 dark:text-slate-300 hover:text-indigo-600 transition-colors">
                        {{ $step['name'] }}
                    </a>
                @else
                    <span class="px-2 py-0.5 rounded text-slate-400 dark:text-slate-500">
                        {{ $step['name'] }}
                    </span>
                @endif
            @endforeach
        </nav>
    </div>
</div>