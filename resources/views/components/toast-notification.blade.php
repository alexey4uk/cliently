@props(['type' => 'info', 'message' => '', 'autoClose' => true, 'duration' => 5000])

@php
    $config = [
        'success' => [
            'icon' => 'fa-check-circle',
            'iconColor' => 'text-emerald-500',
            'glow' => 'shadow-emerald-500/20',
            'borderColor' => 'border-emerald-500/30',
        ],
        'error' => [
            'icon' => 'fa-exclamation-circle',
            'iconColor' => 'text-rose-500',
            'glow' => 'shadow-rose-500/20',
            'borderColor' => 'border-rose-500/30',
        ],
        'info' => [
            'icon' => 'fa-info-circle',
            'iconColor' => 'text-blue-500',
            'glow' => 'shadow-blue-500/20',
            'borderColor' => 'border-blue-500/30',
        ],
        'warning' => [
            'icon' => 'fa-triangle-exclamation',
            'iconColor' => 'text-amber-500',
            'glow' => 'shadow-amber-500/20',
            'borderColor' => 'border-amber-500/30',
        ],
    ];
    
    $style = $config[$type] ?? $config['info'];
@endphp

<div 
    x-data="{ show: false }"
    x-init="
        $nextTick(() => {
            show = true;
            @if($autoClose)
            setTimeout(() => {
                show = false;
                setTimeout(() => $el.remove(), 300);
            }, {{ $duration }});
            @endif
        })
    "
    x-show="show"
    x-transition:enter="transition ease-out duration-400"
    x-transition:enter-start="opacity-0 translate-y-4 translate-x-full scale-95"
    x-transition:enter-end="opacity-100 translate-y-0 translate-x-0 scale-100"
    x-transition:leave="transition ease-in duration-250"
    x-transition:leave-start="opacity-100 translate-y-0 translate-x-0 scale-100"
    x-transition:leave-end="opacity-0 translate-y-4 translate-x-full scale-95"
    class="toast-notification relative flex items-center gap-3 rounded-xl backdrop-blur-xl bg-white/80 dark:bg-slate-900/80 border {{ $style['borderColor'] }} p-4 min-w-[280px] max-w-md"
    role="alert"
>
    <!-- Иконка -->
    <div class="flex-shrink-0">
        <div class="flex h-8 w-8 items-center justify-center">
            <i class="fa-solid {{ $style['icon'] }} text-lg {{ $style['iconColor'] }}"></i>
        </div>
    </div>

    <!-- Сообщение -->
    <div class="flex-1 min-w-0">
        <p class="text-sm font-medium text-slate-900 dark:text-slate-100 leading-relaxed">{{ $message }}</p>
    </div>
</div>
