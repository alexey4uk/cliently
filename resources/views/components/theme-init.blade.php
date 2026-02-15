<script>
    (function() {
        // 1. Мгновенная инициализация темы
        const getTheme = () => {
            const saved = localStorage.getItem('theme');
            if (saved) return saved;
            return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
        };

        const apply = (theme) => {
            document.documentElement.classList.toggle('dark', theme === 'dark');
        };

        // Применяем сразу
        apply(getTheme());

        // 2. Обработка клика и событий
        const init = () => {
            const btn = document.getElementById('theme-toggle');
            if (!btn) return;

            // Пересоздаем кнопку, чтобы избежать дублей событий в Livewire
            const newBtn = btn.cloneNode(true);
            btn.parentNode.replaceChild(newBtn, btn);

            newBtn.addEventListener('click', () => {
                const isDark = document.documentElement.classList.contains('dark');
                const next = isDark ? 'light' : 'dark';
                
                localStorage.setItem('theme', next);
                apply(next);
            });
        };

        // Слушаем изменения системы в реальном времени
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
            if (!localStorage.getItem('theme')) {
                apply(e.matches ? 'dark' : 'light');
            }
        });

        // Инициализация при загрузке и навигации
        document.addEventListener('DOMContentLoaded', init);
        document.addEventListener('livewire:navigated', init);
    })();
</script>
