/**
 * Централизованное управление темой
 * Поддерживает переключение между светлой и тёмной темой
 * Сохраняет выбор пользователя в localStorage
 * Следит за изменениями системной темы
 */

class ThemeManager {
    constructor() {
        this.init();
    }

    /**
     * Инициализация менеджера темы
     */
    init() {
        // Применить тему при загрузке
        this.applyTheme();

        // Инициализировать обработчики после загрузки DOM
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => this.setupEventListeners());
        } else {
            this.setupEventListeners();
        }

        // Слушать изменения системной темы
        this.watchSystemTheme();
    }

    /**
     * Получить текущую тему
     * @returns {string} 'dark' или 'light'
     */
    getTheme() {
        const savedTheme = localStorage.getItem('theme');
        if (savedTheme === 'dark' || savedTheme === 'light') {
            return savedTheme;
        }
        // Если тема не сохранена, используем системные настройки
        return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
    }

    /**
     * Установить тему
     * @param {string} theme - 'dark' или 'light'
     */
    setTheme(theme) {
        if (theme !== 'dark' && theme !== 'light') {
            console.warn('Theme must be "dark" or "light"');
            return;
        }

        const html = document.documentElement;
        
        if (theme === 'dark') {
            html.classList.add('dark');
        } else {
            html.classList.remove('dark');
        }
        
        // Сохраняем выбор пользователя
        localStorage.setItem('theme', theme);

        // Вызываем событие для других компонентов
        window.dispatchEvent(new CustomEvent('themeChanged', { detail: { theme } }));
    }

    /**
     * Переключить тему между светлой и тёмной
     */
    toggleTheme() {
        const currentTheme = this.getTheme();
        const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
        this.setTheme(newTheme);
    }

    /**
     * Применить сохранённую тему или системную
     */
    applyTheme() {
        const theme = this.getTheme();
        const html = document.documentElement;
        
        if (theme === 'dark') {
            html.classList.add('dark');
        } else {
            html.classList.remove('dark');
        }
    }

    /**
     * Настроить обработчики событий для кнопок переключения темы
     */
    setupEventListeners() {
        // Обработчик для кнопок с id="theme-toggle"
        const themeToggle = document.getElementById('theme-toggle');
        if (themeToggle) {
            themeToggle.addEventListener('click', () => this.toggleTheme());
        }

        // Обработчик для кнопок с id="themeToggle" (без дефиса)
        const themeToggleAlt = document.getElementById('themeToggle');
        if (themeToggleAlt) {
            themeToggleAlt.addEventListener('click', () => this.toggleTheme());
        }

        // Обработчик для всех кнопок с data-theme-toggle атрибутом
        const themeToggleButtons = document.querySelectorAll('[data-theme-toggle]');
        themeToggleButtons.forEach(button => {
            button.addEventListener('click', () => this.toggleTheme());
        });
    }

    /**
     * Следить за изменениями системной темы
     * Применяет системную тему только если пользователь не сохранил свой выбор
     */
    watchSystemTheme() {
        const mediaQuery = window.matchMedia('(prefers-color-scheme: dark)');
        
        mediaQuery.addEventListener('change', (e) => {
            // Применяем системную тему только если пользователь не сохранил свой выбор
            if (!localStorage.getItem('theme')) {
                const html = document.documentElement;
                if (e.matches) {
                    html.classList.add('dark');
                } else {
                    html.classList.remove('dark');
                }
            }
        });
    }
}

// Создаём глобальный экземпляр
window.themeManager = new ThemeManager();

// Экспорт для использования в модулях
export default ThemeManager;

