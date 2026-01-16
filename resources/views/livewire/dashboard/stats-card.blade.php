<div class="bg-white dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-800 shadow-sm p-3 md:p-4 lg:p-5">
    <div class="h-8 w-8 md:h-10 md:w-10 rounded-lg bg-{{ $color }}-100 dark:bg-{{ $color }}-500/20 flex items-center justify-center mb-2 md:mb-3">
        <i class="fa-solid {{ $icon }} text-{{ $color }}-600 dark:text-{{ $color }}-400 text-sm md:text-base"></i>
    </div>
    <div>
        <p class="text-[10px] md:text-xs text-slate-500 dark:text-slate-400 mb-0.5 md:mb-1">{{ $label }}</p>
        <p class="text-xl md:text-2xl font-bold text-slate-900 dark:text-white">{{ $value }}</p>
    </div>
</div>
