@props(['class' => ''])
<div {{ $attributes->merge(['class' => 'px-4 py-3 border-b border-slate-200 dark:border-slate-700']) }}>
    {{ $slot }}
</div>
