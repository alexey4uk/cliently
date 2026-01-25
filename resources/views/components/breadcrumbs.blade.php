@props(['items' => [], 'base' => null])

@if(!empty($items) || !empty($base))
<nav aria-label="Breadcrumb" class="mb-4 md:mb-6">
    <ol class="flex items-center flex-wrap gap-1.5 md:gap-2 text-sm">
        <li>
            @if($base && isset($base['url']) && $base['url'])
                <a href="{{ $base['url'] }}" 
                   class="text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 transition-colors flex items-center gap-1.5">
                    <i class="fa-solid fa-home text-xs"></i>
                    <span class="hidden lg:block">{{ $base['title'] ?? 'Главная' }}</span>
                </a>
            @else
                <a href="{{ route('dashboard') }}" 
                   class="text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 transition-colors flex items-center gap-1.5">
                    <i class="fa-solid fa-home text-xs"></i>
                    <span class="hidden lg:block">Главная</span>
                </a>
            @endif
        </li>
        @foreach($items as $item)
            <li class="flex items-center gap-1.5 md:gap-2">
                <i class="fa-solid fa-chevron-right text-xs text-slate-400 dark:text-slate-500"></i>
                @if(isset($item['url']) && $item['url'])
                    <a href="{{ $item['url'] }}" 
                       class="text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 transition-colors">
                        {{ $item['title'] }}
                    </a>
                @else
                    <span class="text-slate-900 dark:text-slate-100 font-medium">
                        {{ $item['title'] }}
                    </span>
                @endif
            </li>
        @endforeach
    </ol>
</nav>
@endif
