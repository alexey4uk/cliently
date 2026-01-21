@if($widgets['quick_actions'])
    <div class="bg-white dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden h-full flex flex-col">
        <div class="px-4 md:px-6 py-3 md:py-4 border-b border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/50">
            <div class="flex items-center gap-2 md:gap-3">
                <div class="h-8 w-8 rounded-lg bg-indigo-100 dark:bg-indigo-500/20 flex items-center justify-center flex-shrink-0">
                    <i class="fa-solid fa-bolt text-indigo-600 dark:text-indigo-400 text-xs"></i>
                </div>
                <h3 class="text-sm font-semibold text-slate-900 dark:text-white">Быстрые действия</h3>
            </div>
        </div>
        <div class="p-4 md:p-6 flex-1">
        <div class="flex flex-wrap gap-2 md:gap-3">
            <a href="{{ route('appointments.create') }}" 
               class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition-colors shadow-sm">
                <i class="fa-solid fa-plus text-xs"></i>
                <span>Новая запись</span>
            </a>
            <a href="{{ route('clients.create') }}" 
               class="inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-600 text-white rounded-lg text-sm font-medium hover:bg-emerald-700 transition-colors shadow-sm">
                <i class="fa-solid fa-user-plus text-xs"></i>
                <span>Новый клиент</span>
            </a>
            <a href="{{ route('appointments.calendar') }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 rounded-lg text-sm font-medium hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors">
                <i class="fa-solid fa-calendar text-xs"></i>
                <span>Календарь</span>
            </a>
        </div>
        </div>
    </div>
@endif
