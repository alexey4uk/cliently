@props([
    'size' => 'md', // sm, md, lg
    'class' => '',
    'buttonClass' => '',
])

@php
    $sizes = [
        'sm' => 'w-4 h-4',
        'md' => 'w-5 h-5',
        'lg' => 'w-6 h-6',
    ];
    
    $iconSize = $sizes[$size] ?? $sizes['md'];
    $defaultButtonClass = 'p-2 rounded-lg text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors';
    $buttonClasses = $buttonClass ?: $defaultButtonClass;
@endphp

<!-- Theme Toggle Button -->
<button 
    id="theme-toggle" 
    class="{{ $buttonClasses }} {{ $class }}" 
    aria-label="Переключить тему"
    type="button"
>
    <!-- Солнце (показывается в тёмной теме) -->
    <x-icon name="sun" variant="outline" :size="$size" class="hidden dark:block" />
    <!-- Луна (показывается в светлой теме) -->
    <x-icon name="moon" variant="outline" :size="$size" class="block dark:hidden" />
</button>

