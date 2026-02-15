@props([
    'availableFeatures' => [],
    'existingFeatures' => []
])

<script>
    (function() {
        const availableFeatures = @json($availableFeatures);
        const existingFeatures = @json($existingFeatures);
        
        function initPlanFeaturesManager() {
            // Проверяем доступность класса
            if (typeof PlanFeaturesManager === 'undefined') {
                return false;
            }
            
            // Инициализируем менеджер свойств тарифа
            try {
                new PlanFeaturesManager(
                    'features-container',
                    availableFeatures,
                    existingFeatures
                );
                return true;
            } catch (error) {
                console.error('Ошибка при инициализации PlanFeaturesManager:', error);
                return false;
            }
        }
        
        // Функция попытки инициализации с повторными попытками
        function tryInitWithRetry() {
            if (initPlanFeaturesManager()) {
                return; // Успешно инициализировано
            }
            
            // Пробуем еще раз через небольшие интервалы
            let attempts = 0;
            const maxAttempts = 200; // 10 секунд (200 * 50ms)
            
            const interval = setInterval(function() {
                attempts++;
                if (initPlanFeaturesManager()) {
                    clearInterval(interval);
                } else if (attempts >= maxAttempts) {
                    clearInterval(interval);
                    console.error('PlanFeaturesManager не загружен после ' + maxAttempts + ' попыток. Убедитесь, что Vite bundle собран (npm run dev или npm run build).');
                }
            }, 50);
        }
        
        // Запускаем инициализацию при загрузке DOM
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', tryInitWithRetry);
        } else {
            // DOM уже загружен, запускаем сразу
            tryInitWithRetry();
        }
        
        // Также пробуем при полной загрузке страницы (на случай, если Vite загружается асинхронно)
        window.addEventListener('load', function() {
            if (typeof PlanFeaturesManager === 'undefined') {
                tryInitWithRetry();
            }
        });
    })();
</script>
