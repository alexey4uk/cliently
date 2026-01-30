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
        
        // Если тема не сохранена, используем системные настройки
        if (!savedTheme) {
            return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
        }
        
        // Если явно установлена тема, используем её
        if (savedTheme === 'dark' || savedTheme === 'light') {
            return savedTheme;
        }
        
        // По умолчанию используем системные настройки
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
        const savedTheme = localStorage.getItem('theme');
        // Определяем текущую активную тему
        const currentTheme = document.documentElement.classList.contains('dark') ? 'dark' : 'light';
        // Переключаем на противоположную
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
     * При изменении системной темы очищаем сохранённую тему, чтобы применить системную
     */
    watchSystemTheme() {
        const mediaQuery = window.matchMedia('(prefers-color-scheme: dark)');
        
        mediaQuery.addEventListener('change', (e) => {
            const savedTheme = localStorage.getItem('theme');
            
            // Если тема не сохранена, применяем системную тему
            if (!savedTheme) {
                const html = document.documentElement;
                if (e.matches) {
                    html.classList.add('dark');
                } else {
                    html.classList.remove('dark');
                }
                // Вызываем событие для других компонентов
                window.dispatchEvent(new CustomEvent('themeChanged', { 
                    detail: { theme: e.matches ? 'dark' : 'light' } 
                }));
            } else {
                // Если пользователь явно выбрал тему, но изменил системную - очищаем сохранённую
                // Это позволяет системной теме применяться автоматически
                localStorage.removeItem('theme');
                const html = document.documentElement;
                if (e.matches) {
                    html.classList.add('dark');
                } else {
                    html.classList.remove('dark');
                }
                // Вызываем событие для других компонентов
                window.dispatchEvent(new CustomEvent('themeChanged', { 
                    detail: { theme: e.matches ? 'dark' : 'light' } 
                }));
            }
        });
    }
}

// Создаём глобальный экземпляр
window.themeManager = new ThemeManager();

// Экспорт для использования в модулях
export default ThemeManager;

