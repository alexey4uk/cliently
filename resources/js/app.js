import './bootstrap';
import PlanFeaturesManager from './plan-features';
import './phone-input';
import './address-autocomplete';

// Явно устанавливаем в window для использования в blade шаблонах
// Делаем это синхронно при загрузке модуля
if (typeof window !== 'undefined') {
    window.PlanFeaturesManager = PlanFeaturesManager;
}

// import Alpine from 'alpinejs';
// import './theme';

// window.Alpine = Alpine;

// Alpine.start();
