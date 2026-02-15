{{-- Компонент переключателя темы для страниц авторизации --}}
<div class="fixed top-4 right-4 z-10">
    <button id="theme-toggle"
        class="h-9 w-9 rounded-lg flex items-center justify-center text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition-all duration-200 group"
        aria-label="Переключить тему">
        <x-icon name="sun" size="md" variant="solid"
            class="hidden dark:block group-hover:scale-110 transition-transform duration-200" />
        <x-icon name="moon" size="md" variant="solid"
            class="block dark:hidden group-hover:scale-110 transition-transform duration-200" />
    </button>
</div>
