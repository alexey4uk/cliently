@if(isset($widgets['next_appointment']) && $widgets['next_appointment'])
    <div class="bg-white dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden h-full flex flex-col">
        <div class="px-4 md:px-6 py-3 md:py-4 border-b border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/50">
            <div class="flex items-center gap-2 md:gap-3">
                <div class="h-8 w-8 rounded-lg bg-indigo-100 dark:bg-indigo-500/20 flex items-center justify-center flex-shrink-0">
                    <i class="fa-solid fa-clock text-indigo-600 dark:text-indigo-400 text-xs"></i>
                </div>
                <h3 class="text-sm font-semibold text-slate-900 dark:text-white">Следующая запись</h3>
            </div>
        </div>
        <div class="p-4 md:p-6 flex-1 flex flex-col">
        @if($appointments['next'] ?? null)
            <div class="space-y-2 flex-1 flex flex-col justify-center">
                <p class="text-2xl md:text-3xl font-bold text-slate-900 dark:text-white">
                    {{ \Carbon\Carbon::parse($appointments['next']->time)->format('H:i') }}
                </p>
                <p class="text-sm md:text-base font-medium text-slate-700 dark:text-slate-300 truncate">
                    {{ $appointments['next']->service->name }}
                </p>
                <p class="text-xs md:text-sm text-slate-600 dark:text-slate-400 truncate">
                    {{ $appointments['next']->client->full_name }}
                </p>
                @if($appointments['next']->master)
                    <p class="text-xs text-slate-500 dark:text-slate-500 truncate">
                        {{ trim($appointments['next']->master->first_name . ' ' . $appointments['next']->master->last_name) }}
                    </p>
                @endif
            </div>
        @else
            <div class="flex-1 flex flex-col items-center justify-center py-12 px-4">
                <div class="h-20 w-20 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center mb-5">
                    <i class="fa-solid fa-calendar-xmark text-slate-400 dark:text-slate-500 text-3xl"></i>
                </div>
                <h4 class="text-base font-semibold text-slate-900 dark:text-white mb-6">Нет предстоящих записей</h4>
                <a href="{{ route('appointments.create') }}" 
                   class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition-colors shadow-sm">
                    <i class="fa-solid fa-plus text-xs"></i>
                    <span>Создать запись</span>
                </a>
            </div>
        @endif
        </div>
    </div>
@endif
