@extends('appointments.public.layout')

@section('title', 'Выбор услуги')

@section('content')
<!-- Breadcrumb навигация -->
<div class="mb-6">
    <a href="{{ route('public.appointments.show', $business->slug) }}" 
       class="inline-flex items-center gap-2 text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 transition-colors">
        <i class="fa-solid fa-arrow-left"></i>
        <span>Назад к выбору локации</span>
    </a>
</div>

<div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-lg p-6 md:p-8">
    <div class="pb-4 border-b border-slate-200 dark:border-slate-700 mb-6">
        <h2 class="text-xl md:text-2xl font-bold text-slate-900 dark:text-white flex items-center gap-3 mb-2">
            <div class="w-10 h-10 rounded-lg bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center">
                <i class="fa-solid fa-spa text-indigo-600 dark:text-indigo-400 text-lg"></i>
            </div>
            Выберите услугу
        </h2>
        <p class="text-sm text-slate-600 dark:text-slate-400 flex items-center gap-2">
            <i class="fa-solid fa-map-marker-alt text-xs"></i>
            <span>Локация: <span class="font-medium">{{ $location->name }}</span></span>
        </p>
    </div>

    @if($services->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($services as $service)
                <a href="{{ route('public.appointments.select-service', ['slug' => $business->slug, 'locationId' => $location->id, 'serviceId' => $service->id]) }}"
                   class="group bg-white dark:bg-slate-900 rounded-xl p-6 border-2 border-slate-200 dark:border-slate-700 hover:border-indigo-500 dark:hover:border-indigo-500 cursor-pointer shadow-md hover:shadow-xl transition-all duration-200 hover:-translate-y-1 flex flex-col">
                    <div class="flex-1">
                        <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-3 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors break-words">
                            {{ $service->name }}
                        </h3>
                        
                        <div class="flex items-center gap-3 mb-3">
                            <div class="text-2xl font-bold text-indigo-600 dark:text-indigo-400">
                                {{ number_format($service->price, 0, ',', ' ') }} Br
                            </div>
                            <div class="inline-flex items-center px-3 py-1 rounded-full bg-indigo-100 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300 text-sm font-medium">
                                <i class="fa-solid fa-clock text-xs mr-1.5"></i>
                                {{ $service->duration }} мин
                            </div>
                        </div>
                        
                        @if($service->description)
                            <p class="text-sm text-slate-600 dark:text-slate-400 leading-relaxed line-clamp-2 break-words">
                                {{ $service->description }}
                            </p>
                        @endif
                    </div>
                    
                    <div class="mt-4 pt-4 border-t border-slate-200 dark:border-slate-700 flex items-center justify-between">
                        <span class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wide">Выбрать</span>
                        <i class="fa-solid fa-chevron-right text-slate-400 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors"></i>
                    </div>
                </a>
            @endforeach
        </div>
    @else
        <!-- Empty state -->
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-12 text-center">
            <div class="max-w-md mx-auto">
                <div class="w-16 h-16 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center mx-auto mb-4">
                    <i class="fa-solid fa-spa text-slate-400 dark:text-slate-500 text-2xl"></i>
                </div>
                <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-2">
                    Нет доступных услуг
                </h3>
                <p class="text-sm text-slate-600 dark:text-slate-400 mb-6">
                    В данной локации пока нет доступных услуг для записи. Пожалуйста, выберите другую локацию или свяжитесь с нами.
                </p>
                <a href="{{ route('public.appointments.show', $business->slug) }}" 
                   class="inline-flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg transition-colors shadow-md hover:shadow-lg">
                    <i class="fa-solid fa-arrow-left text-xs"></i>
                    <span>Вернуться к локациям</span>
                </a>
            </div>
        </div>
    @endif
</div>
@endsection
