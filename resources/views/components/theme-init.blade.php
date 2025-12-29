{{-- Компонент для инициализации темы до загрузки DOM --}}
{{-- Предотвращает мерцание при переключении темы --}}
<script>
    // Применяем тему немедленно, до загрузки DOM, чтобы избежать мерцания
    (function() {
        const getTheme = () => {
            // Проверяем сохранённую тему в localStorage
            const savedTheme = localStorage.getItem('theme');
            if (savedTheme === 'dark' || savedTheme === 'light') {
                return savedTheme;
            }
            // Если тема не сохранена, используем системные настройки
            return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
        };

        const theme = getTheme();
        const html = document.documentElement;
        
        if (theme === 'dark') {
            html.classList.add('dark');
        } else {
            html.classList.remove('dark');
        }
    })();
</script>

