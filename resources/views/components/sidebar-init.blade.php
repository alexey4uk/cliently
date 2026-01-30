{{-- Компонент для инициализации состояния sidebar до загрузки DOM --}}
{{-- Предотвращает мерцание при загрузке страницы --}}
<script>
    // Применяем состояние sidebar немедленно, до загрузки DOM
    // Скрипт выполняется синхронно в <head>, поэтому document.documentElement уже доступен
    (function() {
        try {
            const sidebarCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
            // Устанавливаем data-атрибут на html для применения через CSS (явно конвертируем в строку)
            document.documentElement.setAttribute('data-sidebar-collapsed', sidebarCollapsed ? 'true' : 'false');
        } catch (e) {
            // Если localStorage недоступен, используем значение по умолчанию
            document.documentElement.setAttribute('data-sidebar-collapsed', 'false');
        }
    })();
</script>

<style>
    /* Применяем стили для sidebar и контента до инициализации Alpine.js (только для десктопа) */
    @media (min-width: 1024px) {
        /* Начальные стили для предотвращения FOUC - применяются до загрузки Alpine.js */
        .sidebar-container {
            width: 16rem; /* Значение по умолчанию (развернуто) */
        }
        
        .main-content {
            margin-left: 16rem; /* Значение по умолчанию (развернуто) */
        }
        
        /* Переопределяем для свернутого состояния */
        html[data-sidebar-collapsed="true"] .sidebar-container {
            width: 4rem !important;
        }
        
        html[data-sidebar-collapsed="true"] .main-content {
            margin-left: 4rem !important;
        }
        
        /* Явно задаем для развернутого состояния */
        html[data-sidebar-collapsed="false"] .sidebar-container {
            width: 16rem !important;
        }
        
        html[data-sidebar-collapsed="false"] .main-content {
            margin-left: 16rem !important;
        }
    }
    
    /* Мобильные устройства - убираем все отступы и скрываем sidebar */
    @media (max-width: 1023px) {
        .sidebar-container {
            display: none !important;
        }
        
        .main-content {
            margin-left: 0 !important;
        }
        
        html[data-sidebar-collapsed="true"] .main-content,
        html[data-sidebar-collapsed="false"] .main-content {
            margin-left: 0 !important;
        }
    }
    
    /* Скрываем текст при свернутом sidebar (только для десктопа) */
    @media (min-width: 1024px) {
        html[data-sidebar-collapsed="true"] .sidebar-text {
            display: none !important;
            visibility: hidden !important;
            opacity: 0 !important;
            width: 0 !important;
            overflow: hidden !important;
            max-width: 0 !important;
        }
        
        /* Скрываем заголовки секций при свернутом sidebar */
        html[data-sidebar-collapsed="true"] .sidebar-section-title {
            display: none !important;
            visibility: hidden !important;
        }
    }
</style>

