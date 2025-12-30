{{-- Компонент для инициализации темы до загрузки DOM --}}
{{-- Предотвращает мерцание при переключении темы --}}
<script>
    // Применяем тему немедленно, до загрузки DOM, чтобы избежать мерцания
    (function() {
        try {
            const getTheme = () => {
                const savedTheme = localStorage.getItem('theme');
                
                // Если тема не сохранена, используем системные настройки
                if (!savedTheme) {
                    if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
                        return 'dark';
                    }
                    return 'light';
                }
                
                // Если явно установлена тема, используем её
                if (savedTheme === 'dark' || savedTheme === 'light') {
                    return savedTheme;
                }
                
                // По умолчанию используем системные настройки
                if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
                    return 'dark';
                }
                return 'light';
            };

            const theme = getTheme();
            const html = document.documentElement;
            
            if (theme === 'dark') {
                html.classList.add('dark');
            } else {
                html.classList.remove('dark');
            }
        } catch (e) {
            // В случае ошибки используем светлую тему по умолчанию
            console.warn('Theme initialization error:', e);
        }
    })();
</script>

