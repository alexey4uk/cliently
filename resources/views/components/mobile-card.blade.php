{{-- Единый стиль мобильной карточки на всех страницах --}}
@props(['class' => ''])

@php
    $baseClass = 'bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden';
@endphp

<div {{ $attributes->merge(['class' => $baseClass . ($class ? ' ' . $class : '')]) }}>
    {{ $slot }}
</div>
