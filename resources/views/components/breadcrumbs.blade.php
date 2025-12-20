@props(['items' => []])

<nav class="flex items-center space-x-2 text-sm mb-2">
    <a href="{{ route('dashboard') }}" class="text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200 transition-colors">
        Главная
    </a>
    @foreach($items as $item)
        <span class="text-slate-400">/</span>
        @if(isset($item['url']))
            <a href="{{ $item['url'] }}" class="text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200 transition-colors">
                {{ $item['title'] }}
            </a>
        @else
            <span class="text-slate-900 dark:text-slate-100 font-medium">
                {{ $item['title'] }}
            </span>
        @endif
    @endforeach
</nav>

