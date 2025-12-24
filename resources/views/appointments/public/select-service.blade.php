@extends('appointments.public.layout')

@section('title', 'Выбор услуги')

@section('content')
<!-- Breadcrumb навигация -->
<div class="mb-4">
    <a href="{{ route('public.appointments.show', $business->slug) }}" 
       class="inline-flex items-center gap-1.5 text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300">
        <i class="fa-solid fa-arrow-left text-xs"></i>
        <span>Назад</span>
    </a>
</div>

<div class="bg-white dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-800 p-4">
    <div class="pb-3 border-b border-slate-200 dark:border-slate-700 mb-4">
        <h2 class="text-lg font-semibold text-slate-900 dark:text-white mb-1">
            Выберите услугу
        </h2>
        <p class="text-sm text-slate-600 dark:text-slate-400">
            Локация: {{ $location->name }}
        </p>
    </div>

    @if($services->count() > 0)
        <div class="space-y-1">
            @foreach($services as $service)
                <a href="{{ route('public.appointments.select-service', ['slug' => $business->slug, 'locationId' => $location->id, 'serviceId' => $service->id]) }}"
                   class="flex items-center justify-between p-3 rounded-lg border border-slate-200 dark:border-slate-700 hover:border-indigo-500 dark:hover:border-indigo-500 hover:bg-indigo-50 dark:hover:bg-indigo-900/10 transition-colors">
                    <div class="flex-1 min-w-0">
                        <h3 class="text-sm font-semibold text-slate-900 dark:text-white mb-1 truncate">
                            {{ $service->name }}
                        </h3>
                        <div class="flex items-center gap-2 text-xs text-slate-600 dark:text-slate-400">
                            <span class="font-semibold text-indigo-600 dark:text-indigo-400">
                                {{ number_format($service->price, 0, ',', ' ') }} Br
                            </span>
                            <span>•</span>
                            <span>{{ $service->duration }} мин</span>
                        </div>
                        @if($service->description)
                            <p class="text-xs text-slate-600 dark:text-slate-400 mt-1 line-clamp-1 truncate">
                                {{ $service->description }}
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
                Нет доступных услуг в данной локации.
            </p>
            <a href="{{ route('public.appointments.show', $business->slug) }}" 
               class="inline-flex items-center gap-1.5 text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300">
                <i class="fa-solid fa-arrow-left text-xs"></i>
                <span>Вернуться к локациям</span>
            </a>
        </div>
    @endif
</div>
@endsection
