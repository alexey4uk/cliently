@props([
    'size' => 'md', // sm, md, lg, xl, sidebar, footer
    'containerClass' => '',
    'class' => ''
])

@php
    $sizes = [
        'sm' => [
            'container' => 'h-8 w-8',
            'circle' => 'h-6 w-6',
        ],
        'md' => [
            'container' => 'h-10 w-10',
            'circle' => 'h-7 w-7',
        ],
        'lg' => [
            'container' => 'h-10 w-10 md:h-12 md:w-12',
            'circle' => 'h-8 w-8 md:h-9 md:w-9',
        ],
        'sidebar' => [
            'container' => 'h-10 w-10',
            'circle' => 'h-8 w-8',
        ],
        'footer' => [
            'container' => 'h-9 w-9 sm:h-10 sm:w-10',
            'circle' => 'h-6 w-6 sm:h-7 sm:w-7',
        ],
        'xl' => [
            'container' => 'h-12 w-12',
            'circle' => 'h-9 w-9',
        ],
    ];
    
    $sizeConfig = $sizes[$size] ?? $sizes['md'];
    $containerClasses = $containerClass ?: $sizeConfig['container'];
@endphp

<!-- Логознак: мастер + клиент -->
<div class="relative flex {{ $containerClasses }} items-center justify-center flex-shrink-0 drop-shadow-sm {{ $class }}">
    <!-- Левый круг (мастер) с градиентом -->
    <span class="absolute {{ $sizeConfig['circle'] }} rounded-full bg-gradient-to-br from-[#6366F1] via-[#7C7FF0] to-[#818CF8] border border-[#6366F1]/30 left-0 shadow-sm"></span>
    <!-- Правый круг (клиент) с градиентом -->
    <span class="absolute {{ $sizeConfig['circle'] }} rounded-full bg-gradient-to-br from-[#F472B6] via-[#F687C5] to-[#F9A8D4] border border-[#F472B6]/30 right-0 shadow-sm"></span>
    <!-- Пересечение с легким свечением -->
    <span class="absolute {{ $sizeConfig['circle'] }} rounded-full bg-gradient-to-br from-[#6366F1]/40 via-[#A78BFA]/50 to-[#F472B6]/40 blur-[2px] z-0"></span>
</div>

