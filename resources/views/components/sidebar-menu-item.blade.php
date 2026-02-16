@props([
    'href',
    'route' => null,
    'icon' => 'fa-circle',
    'title' => '',
    'active' => false,
    'permission' => null,
    'collapsed' => false,
])

@if(!$permission || (auth()->check() && auth()->user()->can($permission)))
    <a href="{{ $href }}"
        class="group flex items-center py-2.5 text-sm font-medium rounded-lg transition-all duration-200 relative {{ $active
            ? 'bg-gradient-to-r from-indigo-50 to-indigo-50/50 dark:from-indigo-500/20 dark:to-indigo-500/10 text-indigo-700 dark:text-indigo-300 shadow-sm ring-1 ring-indigo-100 dark:ring-indigo-500/20'
            : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100/80 dark:hover:bg-slate-800/60 hover:text-slate-900 dark:hover:text-slate-100' }}"
        :class="collapsed ? 'justify-center mx-2' : 'px-3'"
        :title="collapsed ? '{{ $title }}' : ''"
        x-data="{ tooltip: false }"
        @mouseenter="if (collapsed) tooltip = true"
        @mouseleave="tooltip = false">
        <div class="flex items-center justify-center flex-shrink-0"
            :class="collapsed ? 'mx-auto w-7 h-7' : 'w-5 h-5 mr-3'">
            <i class="fa-solid {{ $icon }} transition-transform duration-200 {{ $active ? 'scale-110' : 'group-hover:scale-110' }}" 
               :class="collapsed ? 'text-lg' : 'text-base'"></i>
        </div>
        <span x-show="!collapsed" x-cloak
            class="sidebar-text whitespace-nowrap font-medium">{{ $title }}</span>
        <div x-show="tooltip && collapsed" 
             x-transition
             class="absolute left-full ml-2 px-2 py-1 bg-slate-900 dark:bg-slate-700 text-white text-xs rounded shadow-lg z-50 whitespace-nowrap">
            {{ $title }}
        </div>
    </a>
@endif
