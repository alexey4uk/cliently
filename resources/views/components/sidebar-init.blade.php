{{-- Компонент для инициализации стилей sidebar до загрузки DOM (предотвращает мерцание) --}}
<style>
    /* Десктоп: фиксированная ширина sidebar и отступ контента */
    @media (min-width: 1024px) {
        .sidebar-container {
            width: 16rem;
        }
        .main-content {
            margin-left: 16rem;
        }
    }
    /* Мобильные: скрываем sidebar, убираем отступ */
    @media (max-width: 1023px) {
        .sidebar-container {
            display: none !important;
        }
        .main-content {
            margin-left: 0 !important;
        }
    }
</style>

