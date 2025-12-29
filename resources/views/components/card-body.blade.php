@props([
    'class' => '',
    'padding' => 'default', // 'none', 'sm', 'default', 'lg'
])

@php
    $paddingClasses = [
        'none' => '',
        'sm' => 'px-3 py-2',
        'default' => 'px-5 py-4',
        'lg' => 'px-6 py-5',
    ];
    $paddingClass = $paddingClasses[$padding] ?? $paddingClasses['default'];
@endphp

<div class="{{ $paddingClass }} {{ $class }}">
    {{ $slot }}
</div>

