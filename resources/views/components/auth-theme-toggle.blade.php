{{-- Компонент переключателя темы для страниц авторизации --}}
<div class="fixed top-4 right-4 z-10">
    <button 
        id="themeToggle"
        class="h-10 w-10 rounded-full text-sm flex items-center justify-center text-slate-700 hover:bg-white/80 hover:shadow-sm transition-colors dark:text-slate-300 dark:hover:bg-slate-800/80"
        aria-label="Переключить тему"
    >
        <x-icon name="sun" size="md" class="hidden dark:block" />
        <x-icon name="moon" size="md" class="block dark:hidden" />
    </button>
</div>

