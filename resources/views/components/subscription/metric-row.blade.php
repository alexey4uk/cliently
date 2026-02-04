@props([
    'metric',
    'value',
    'variant' => 'row', // 'row' for card list, 'block' for show page
    'advanced' => false,  // for row: true = check/times + label only
])

@php
    if ($value === null) {
        return;
    }
    $displayValue = match (true) {
        $value === -1 => 'Безлимит',
        $value === 0 => '—',
        $value === true => '✓',
        $value === false => '✗',
        is_numeric($value) => number_format($value, 0, ',', ' '),
        default => $value,
    };
    $hasFeature = $value === true;
    $icon = $metric->icon ?? 'fa-solid fa-circle';
@endphp

@if($variant === 'block')
    <div class="flex items-start gap-4 p-4 bg-slate-50 dark:bg-slate-800/50 rounded-lg">
        <div class="h-10 w-10 rounded-lg bg-indigo-100 dark:bg-indigo-500/20 flex items-center justify-center shrink-0">
            <i class="{{ $icon }} text-indigo-600 dark:text-indigo-400"></i>
        </div>
        <div class="flex-1">
            <div class="flex items-center justify-between mb-1">
                <h4 class="font-semibold text-slate-900 dark:text-white">{{ $metric->label }}</h4>
                <span class="text-sm font-semibold text-slate-700 dark:text-slate-300">
                    @if($value === -1)
                        Безлимит
                    @elseif($value === 0)
                        <span class="text-slate-400 dark:text-slate-500" aria-label="Не доступно">—</span>
                    @elseif($value === true)
                        Включено
                    @elseif($value === false)
                        Отключено
                    @else
                        {{ number_format($value, 0, ',', ' ') }}
                    @endif
                </span>
            </div>
            @if(!empty($metric->description))
                <p class="text-sm text-slate-600 dark:text-slate-400">{{ $metric->description }}</p>
            @endif
        </div>
    </div>
@elseif($advanced)
    <div class="flex items-center gap-2 text-xs sm:text-sm">
        @if($hasFeature)
            <i class="fa-solid fa-check text-green-600 dark:text-green-400 shrink-0"></i>
        @else
            <i class="fa-solid fa-times text-slate-300 dark:text-slate-600 shrink-0"></i>
        @endif
        <span class="text-slate-700 dark:text-slate-300">{{ $metric->label }}</span>
    </div>
@else
    <div class="flex items-center justify-between text-xs sm:text-sm">
        <span class="text-slate-700 dark:text-slate-300 pr-2">{{ $metric->label }}</span>
        <span class="font-semibold text-slate-900 dark:text-white shrink-0" @if($value === 0) aria-label="Не доступно" @endif>{{ $displayValue }}</span>
    </div>
@endif
