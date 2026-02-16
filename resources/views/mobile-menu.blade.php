<!-- Кнопка открытия мобильного меню (только мобильные) -->
<div x-data="{
    toggleMenu() {
        // Отправляем событие для внешнего меню
        document.dispatchEvent(new CustomEvent('mobile-menu-toggle', { 
            detail: { open: true } 
        }));
        document.body.style.overflow = 'hidden';
    }
}" 
class="lg:hidden relative">
    <button @click="toggleMenu()"
        class="h-9 w-9 rounded-lg flex items-center justify-center text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition-all duration-200 flex-shrink-0 group relative z-50"
        aria-label="Открыть меню"
        type="button">
        <i class="fa-solid fa-bars text-base group-hover:scale-110 transition-transform duration-200"></i>
    </button>
</div>
