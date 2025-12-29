@props([
    'class' => '',
    'hover' => false,
])

<div class="bg-white/80 dark:bg-slate-900/80 backdrop-blur-sm rounded-xl border border-slate-200/60 dark:border-slate-800/60 shadow-sm {{ $hover ? 'hover:shadow-md transition-shadow' : '' }} {{ $class }}">
    {{ $slot }}
</div>

