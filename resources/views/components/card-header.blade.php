@props([
    'class' => '',
])

<div class="flex items-center justify-between px-5 py-4 border-b border-slate-200/60 dark:border-slate-700/60 {{ $class }}">
    {{ $slot }}
</div>

