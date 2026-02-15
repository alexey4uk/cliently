/**
 * Plan Features Manager
 * Управление динамическими полями свойств тарифов
 */
class PlanFeaturesManager {
    constructor(containerId, availableFeatures, existingFeatures = []) {
        this.containerId = containerId;
        this.availableFeatures = availableFeatures || { integer: {}, boolean: {} };
        this.existingFeatures = existingFeatures || [];
        this.featureIndex = 0;
        this.container = null;
        
        this.init();
    }
    
    init() {
        // Ждем загрузки DOM
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => this.setup());
        } else {
            // Если DOM уже загружен, вызываем setup сразу, но с небольшой задержкой
            // чтобы убедиться, что все элементы отрендерены
            setTimeout(() => this.setup(), 0);
        }
    }
    
    setup() {
        this.container = document.getElementById(this.containerId);
        if (!this.container) {
            console.error(`Container with id "${this.containerId}" not found, повторная попытка через 100ms...`);
            // Повторная попытка найти контейнер через небольшую задержку
            setTimeout(() => {
                this.container = document.getElementById(this.containerId);
                if (this.container) {
                    this.continueSetup();
                } else {
                    console.error(`Container with id "${this.containerId}" still not found after retry`);
                }
            }, 100);
            return;
        }
        
        this.continueSetup();
    }
    
    continueSetup() {
        // Загружаем существующие свойства
        if (this.existingFeatures.length > 0) {
            this.existingFeatures.forEach(feature => {
                this.addFeature(feature.key, feature.value, feature.type);
            });
        }
        
        // Находим кнопку добавления свойства и привязываем обработчик
        // Сначала пытаемся найти по id
        let addButton = document.getElementById('add-feature-btn');
        
        // Если не найдена по id, ищем в родительском элементе
        if (!addButton) {
            const parentElement = this.container.parentElement;
            if (parentElement) {
                // Ищем кнопку с текстом "Добавить" или просто кнопку после контейнера
                addButton = parentElement.querySelector('button:not(.remove-feature-btn)');
                if (addButton && !addButton.textContent.includes('Добавить')) {
                    addButton = null;
                }
            }
        }
        
        if (addButton) {
            // Удаляем старый обработчик onclick, если есть
            addButton.removeAttribute('onclick');
            // Удаляем все предыдущие обработчики событий, создавая новую кнопку
            const newButton = addButton.cloneNode(true);
            addButton.parentNode.replaceChild(newButton, addButton);
            addButton = newButton;
            
            addButton.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                this.addFeature();
            });
        } else {
            console.warn('Кнопка добавления свойства не найдена');
        }
        
        // Находим форму и добавляем валидацию
        const form = document.getElementById('plan-form');
        if (form) {
            form.addEventListener('submit', (e) => {
                if (!this.validateForm()) {
                    e.preventDefault();
                    e.stopPropagation();
                    return false;
                }
            });
        }
    }
    
    addFeature(key = '', value = '', type = 'integer') {
        if (!this.container) {
            console.error('Container not initialized, пытаемся найти снова...');
            this.container = document.getElementById(this.containerId);
            if (!this.container) {
                console.error(`Container with id "${this.containerId}" still not found`);
                return;
            }
        }
        
        
        const currentFeatureIndex = this.featureIndex;
        const featureDiv = document.createElement('div');
        featureDiv.className = 'feature-item';
        featureDiv.setAttribute('data-feature-index', currentFeatureIndex);
        
        // Определяем, есть ли метрика в списке доступных
        let isCustomFeature = true;
        let featureType = type || 'integer';
        let featureInfo = null;
        
        if (key) {
            // Проверяем integer метрики
            if (this.availableFeatures.integer && this.availableFeatures.integer[key]) {
                isCustomFeature = false;
                // В режиме редактирования используем тип из БД, не переопределяем
                if (!type) {
                    featureType = 'integer';
                }
                featureInfo = this.availableFeatures.integer[key];
            }
            // Проверяем boolean метрики
            else if (this.availableFeatures.boolean && this.availableFeatures.boolean[key]) {
                isCustomFeature = false;
                // В режиме редактирования используем тип из БД, не переопределяем
                if (!type) {
                    featureType = 'boolean';
                }
                featureInfo = this.availableFeatures.boolean[key];
            }
        }
        
        // Если тип не определен, используем переданный или по умолчанию integer
        if (!featureType) {
            featureType = type || 'integer';
        }
        
        const isBoolean = featureType === 'boolean';
        
        // Правильно определяем boolean значение
        const booleanValue = isBoolean && (value === 'true' || value === true || value === 1 || value === '1');
        
        // Для integer полей убираем boolean значения
        let integerValue = '';
        if (!isBoolean) {
            const valueStr = String(value).toLowerCase().trim();
            if (value !== null && value !== undefined && value !== '' && 
                valueStr !== 'true' && valueStr !== 'false' && 
                value !== true && value !== false) {
                const numValue = Number(value);
                if (!isNaN(numValue) && isFinite(numValue)) {
                    integerValue = String(numValue);
                }
            }
        }
        
        // Генерируем HTML
        featureDiv.innerHTML = this.generateFeatureHTML(
            currentFeatureIndex,
            key,
            isCustomFeature,
            featureType,
            featureInfo,
            isBoolean,
            booleanValue,
            integerValue
        );
        
        this.container.appendChild(featureDiv);
        
        // Настраиваем обработчики событий
        this.setupFeatureEventHandlers(featureDiv, currentFeatureIndex, isCustomFeature, key, featureType);
        
        // Если метрика из списка и уже выбрана при загрузке, создаем скрытые поля сразу
        if (!isCustomFeature && key) {
            const select = featureDiv.querySelector('.feature-key-select');
            if (select) {
                select.value = key;
                
                // Создаем скрытые поля для key и type
                const hiddenKeyInput = document.createElement('input');
                hiddenKeyInput.type = 'hidden';
                hiddenKeyInput.name = `features[${currentFeatureIndex}][key]`;
                hiddenKeyInput.value = key;
                featureDiv.appendChild(hiddenKeyInput);
                
                const hiddenTypeInput = document.createElement('input');
                hiddenTypeInput.type = 'hidden';
                hiddenTypeInput.name = `features[${currentFeatureIndex}][type]`;
                hiddenTypeInput.value = featureType;
                featureDiv.appendChild(hiddenTypeInput);
                
                // Обновляем описание
                const descriptionField = featureDiv.querySelector('.feature-description');
                if (descriptionField && featureInfo) {
                    descriptionField.querySelector('.description-text').textContent = featureInfo.description || '';
                    descriptionField.style.display = 'block';
                }
            }
        }
        
        // Очищаем boolean значения из integer полей при загрузке
        if (!isBoolean) {
            const integerValueInput = featureDiv.querySelector('.integer-value-input');
            if (integerValueInput) {
                const currentValue = String(integerValueInput.value).toLowerCase().trim();
                if (currentValue === 'false' || currentValue === 'true') {
                    integerValueInput.value = '';
                }
            }
        }
        
        // Устанавливаем правильный name в зависимости от типа
        setTimeout(() => {
            this.updateValueFieldName(featureDiv, currentFeatureIndex, isBoolean);
        }, 0);
        
            // Триггерим change для синхронизации полей, если метрика из списка
            if (!isCustomFeature && key) {
                const select = featureDiv.querySelector('.feature-key-select');
                if (select) {
                    setTimeout(() => {
                        select.dispatchEvent(new Event('change', { bubbles: true }));
                    }, 10);
                }
            }
            
            // Обновляем индексы в заголовках свойств
            this.updateFeatureNumbers();
            
            this.featureIndex++;
    }
    
    generateFeatureHTML(index, key, isCustomFeature, featureType, featureInfo, isBoolean, booleanValue, integerValue) {
        const integerOptions = this.availableFeatures.integer ? 
            Object.keys(this.availableFeatures.integer).map(k => 
                `<option value="${k}" ${key === k ? 'selected' : ''} data-type="integer">${this.availableFeatures.integer[k].label}</option>`
            ).join('') : '';
        
        const booleanOptions = this.availableFeatures.boolean ? 
            Object.keys(this.availableFeatures.boolean).map(k => 
                `<option value="${k}" ${key === k ? 'selected' : ''} data-type="boolean">${this.availableFeatures.boolean[k].label}</option>`
            ).join('') : '';
        
        return `
            <div class="p-4 sm:p-5 bg-gradient-to-br from-slate-50 to-slate-100/50 dark:from-slate-800/50 dark:to-slate-800/30 rounded-xl border-2 border-slate-200 dark:border-slate-700 hover:border-indigo-300 dark:hover:border-indigo-600 transition-all space-y-4">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center gap-2">
                        <div class="h-7 w-7 rounded-lg bg-indigo-100 dark:bg-indigo-500/20 flex items-center justify-center flex-shrink-0">
                            <i class="fa-solid fa-cog text-indigo-600 dark:text-indigo-400 text-xs"></i>
                        </div>
                        <span class="text-sm font-semibold text-slate-700 dark:text-slate-300">Свойство #${index + 1}</span>
                    </div>
                    <button type="button" 
                            class="remove-feature-btn inline-flex items-center justify-center gap-1.5 px-3 py-1.5 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-all text-xs font-medium">
                        <i class="fa-solid fa-trash"></i>
                        <span class="hidden sm:inline">Удалить</span>
                    </button>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div class="sm:col-span-2 lg:col-span-1">
                        <label class="block text-xs sm:text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">
                            <i class="fa-solid fa-tag text-xs text-slate-400 mr-1.5"></i>Свойство
                            <span class="text-rose-500 ml-1">*</span>
                        </label>
                        <select class="feature-key-select w-full px-3 sm:px-4 py-2.5 sm:py-3 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all text-sm" required>
                            <option value="">-- Выберите свойство --</option>
                            ${integerOptions}
                            ${booleanOptions}
                            <option value="__custom__" ${isCustomFeature && key ? 'selected' : ''}>-- Другое свойство --</option>
                        </select>
                    </div>
                    <div class="custom-key-field" style="display: ${isCustomFeature && key ? 'block' : 'none'};">
                        <label class="block text-xs sm:text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">
                            <i class="fa-solid fa-key text-xs text-slate-400 mr-1.5"></i>Ключ
                            <span class="text-rose-500 ml-1">*</span>
                        </label>
                        <input type="text" 
                               name="features[${index}][key]" 
                               value="${isCustomFeature ? (key || '') : ''}"
                               class="custom-key-input w-full px-3 sm:px-4 py-2.5 sm:py-3 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all text-sm"
                               placeholder="max_custom_feature"
                               ${isCustomFeature && key ? 'required' : ''}>
                    </div>
                    <div class="custom-type-field" style="display: ${isCustomFeature && key ? 'block' : 'none'};">
                        <label class="block text-xs sm:text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">
                            <i class="fa-solid fa-list text-xs text-slate-400 mr-1.5"></i>Тип
                            <span class="text-rose-500 ml-1">*</span>
                        </label>
                        <select name="features[${index}][type]"
                                class="custom-type-select w-full px-3 sm:px-4 py-2.5 sm:py-3 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all text-sm"
                                ${isCustomFeature && key ? 'required' : ''}>
                            <option value="integer" ${featureType === 'integer' ? 'selected' : ''}>Число</option>
                            <option value="boolean" ${featureType === 'boolean' ? 'selected' : ''}>Да/Нет</option>
                        </select>
                    </div>
                </div>
                
                <div class="feature-description" style="display: ${featureInfo ? 'block' : 'none'};">
                    <div class="flex items-start gap-2.5 p-3 bg-blue-50 dark:bg-blue-500/10 rounded-lg border border-blue-200 dark:border-blue-500/20">
                        <i class="fa-solid fa-info-circle text-blue-600 dark:text-blue-400 text-sm mt-0.5 flex-shrink-0"></i>
                        <p class="text-xs sm:text-sm text-blue-700 dark:text-blue-300 leading-relaxed">
                            <span class="description-text">${featureInfo ? (featureInfo.description || '') : ''}</span>
                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="integer-value-field" style="display: ${isBoolean ? 'none' : 'block'};">
                        <label class="block text-xs sm:text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">
                            <i class="fa-solid fa-hashtag text-xs text-slate-400 mr-1.5"></i>Числовое значение
                        </label>
                        <input type="number" 
                               name="features[${index}][value]"
                               value="${integerValue}"
                               inputmode="numeric"
                               class="integer-value-input w-full px-3 sm:px-4 py-2.5 sm:py-3 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all text-sm"
                               placeholder="Введите число">
                        <p class="mt-2 text-xs text-slate-500 dark:text-slate-400">
                            <i class="fa-solid fa-lightbulb mr-1"></i>
                            Используйте <strong>-1</strong> для безлимита, оставьте пустым если не требуется
                        </p>
                    </div>
                    <div class="boolean-value-field" style="display: ${isBoolean ? 'block' : 'none'};">
                        <label class="block text-xs sm:text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">
                            <i class="fa-solid fa-toggle-on text-xs text-slate-400 mr-1.5"></i>Включено
                            <span class="text-rose-500 ml-1">*</span>
                        </label>
                        <label class="flex items-center gap-3 p-4 rounded-lg border-2 ${booleanValue ? 'border-indigo-300 dark:border-indigo-600 bg-indigo-50 dark:bg-indigo-500/10' : 'border-slate-200 dark:border-slate-700'} bg-white dark:bg-slate-900 cursor-pointer transition-all group">
                            <input type="checkbox" 
                                   class="boolean-checkbox w-5 h-5 text-indigo-600 rounded border-slate-300 focus:ring-indigo-500 focus:ring-2 flex-shrink-0"
                                   ${booleanValue ? 'checked' : ''}>
                            <div class="flex-1">
                                <span class="text-sm font-medium text-slate-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                                    ${booleanValue ? 'Включено' : 'Выключено'}
                                </span>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                                    ${booleanValue ? 'Свойство активно для этого тарифа' : 'Свойство неактивно'}
                                </p>
                            </div>
                        </label>
                        <input type="hidden" 
                               name="features[${index}][value]"
                               class="boolean-value-input"
                               value="${booleanValue ? 'true' : 'false'}"
                               ${isBoolean ? 'required' : ''}>
                    </div>
                </div>
            </div>
        `;
    }
    
    setupFeatureEventHandlers(featureDiv, currentFeatureIndex, isCustomFeature, key, featureType) {
        const select = featureDiv.querySelector('.feature-key-select');
        const customKeyField = featureDiv.querySelector('.custom-key-field');
        const customTypeField = featureDiv.querySelector('.custom-type-field');
        const descriptionField = featureDiv.querySelector('.feature-description');
        const integerValueField = featureDiv.querySelector('.integer-value-field');
        const booleanValueField = featureDiv.querySelector('.boolean-value-field');
        const booleanCheckbox = featureDiv.querySelector('.boolean-checkbox');
        const booleanValueInput = featureDiv.querySelector('.boolean-value-input');
        const customKeyInput = featureDiv.querySelector('.custom-key-input');
        const customTypeSelect = featureDiv.querySelector('.custom-type-select');
        const removeBtn = featureDiv.querySelector('.remove-feature-btn');
        
        // Обработчик удаления
        if (removeBtn) {
            removeBtn.addEventListener('click', () => {
                featureDiv.remove();
                this.updateFeatureNumbers();
            });
        }
        
        // Обработчик изменения select метрики
        if (select) {
            select.addEventListener('change', () => {
                const selectedOption = select.options[select.selectedIndex];
                const selectedKey = select.value;
                const isCustom = selectedKey === '__custom__';
                
                if (isCustom) {
                    // Показываем поля для кастомной метрики
                    if (customKeyField) customKeyField.style.display = 'block';
                    if (customTypeField) customTypeField.style.display = 'block';
                    if (descriptionField) descriptionField.style.display = 'none';
                    
                    // Удаляем скрытые поля key и type, если есть
                    const hiddenKeyInput = featureDiv.querySelector('input[type="hidden"][name*="key"]');
                    if (hiddenKeyInput) hiddenKeyInput.remove();
                    const hiddenTypeInput = featureDiv.querySelector('input[type="hidden"][name*="type"]');
                    if (hiddenTypeInput) hiddenTypeInput.remove();
                    
                    // Обновляем тип из select
                    if (customTypeSelect) {
                        this.updateValueField(featureDiv, currentFeatureIndex, customTypeSelect.value);
                    }
                } else {
                    // Скрываем поля для кастомной метрики
                    if (customKeyField) customKeyField.style.display = 'none';
                    if (customTypeField) customTypeField.style.display = 'none';
                    
                    // Создаем или обновляем скрытое поле для ключа
                    let keyInput = featureDiv.querySelector('input[type="hidden"][name*="key"]');
                    if (!keyInput) {
                        keyInput = document.createElement('input');
                        keyInput.type = 'hidden';
                        keyInput.name = `features[${currentFeatureIndex}][key]`;
                        featureDiv.appendChild(keyInput);
                    }
                    keyInput.value = selectedKey;
                    
                    // Создаем или обновляем скрытое поле для типа
                    let typeInput = featureDiv.querySelector('input[type="hidden"][name*="type"]');
                    if (!typeInput) {
                        typeInput = document.createElement('input');
                        typeInput.type = 'hidden';
                        typeInput.name = `features[${currentFeatureIndex}][type]`;
                        featureDiv.appendChild(typeInput);
                    }
                    
                    // Получаем тип и информацию о метрике
                    const metricType = selectedOption.getAttribute('data-type');
                    typeInput.value = metricType;
                    
                    // Обновляем описание
                    if (descriptionField) {
                        if (metricType === 'integer' && this.availableFeatures.integer && this.availableFeatures.integer[selectedKey]) {
                            descriptionField.querySelector('.description-text').textContent = this.availableFeatures.integer[selectedKey].description || '';
                            descriptionField.style.display = 'block';
                        } else if (metricType === 'boolean' && this.availableFeatures.boolean && this.availableFeatures.boolean[selectedKey]) {
                            descriptionField.querySelector('.description-text').textContent = this.availableFeatures.boolean[selectedKey].description || '';
                            descriptionField.style.display = 'block';
                        } else {
                            descriptionField.style.display = 'none';
                        }
                    }
                    
                    // Обновляем поле значения
                    this.updateValueField(featureDiv, currentFeatureIndex, metricType);
                }
            });
        }
        
        // Обработчик изменения типа для кастомной метрики
        if (customTypeSelect) {
            customTypeSelect.addEventListener('change', () => {
                this.updateValueField(featureDiv, currentFeatureIndex, customTypeSelect.value);
            });
        }
        
        // Обработчик чекбокса для boolean
        if (booleanCheckbox && booleanValueInput) {
            const updateBooleanVisualState = () => {
                booleanValueInput.value = booleanCheckbox.checked ? 'true' : 'false';
                
                // Обновляем визуальное состояние карточки
                const booleanField = featureDiv.querySelector('.boolean-value-field label');
                if (booleanField) {
                    if (booleanCheckbox.checked) {
                        booleanField.classList.remove('border-slate-200', 'dark:border-slate-700');
                        booleanField.classList.add('border-indigo-300', 'dark:border-indigo-600', 'bg-indigo-50', 'dark:bg-indigo-500/10');
                        const statusText = booleanField.querySelector('span.text-sm.font-medium');
                        const descText = booleanField.querySelector('p.text-xs');
                        if (statusText) statusText.textContent = 'Включено';
                        if (descText) descText.textContent = 'Свойство активно для этого тарифа';
                    } else {
                        booleanField.classList.remove('border-indigo-300', 'dark:border-indigo-600', 'bg-indigo-50', 'dark:bg-indigo-500/10');
                        booleanField.classList.add('border-slate-200', 'dark:border-slate-700');
                        const statusText = booleanField.querySelector('span.text-sm.font-medium');
                        const descText = booleanField.querySelector('p.text-xs');
                        if (statusText) statusText.textContent = 'Выключено';
                        if (descText) descText.textContent = 'Свойство неактивно';
                    }
                }
            };
            
            booleanCheckbox.addEventListener('change', updateBooleanVisualState);
            
            // Устанавливаем начальное состояние
            setTimeout(updateBooleanVisualState, 0);
        }
    }
    
    updateValueField(featureDiv, currentFeatureIndex, type) {
        const valueFieldName = `features[${currentFeatureIndex}][value]`;
        const integerValueField = featureDiv.querySelector('.integer-value-field');
        const booleanValueField = featureDiv.querySelector('.boolean-value-field');
        const integerValueInput = integerValueField?.querySelector('.integer-value-input');
        const booleanValueInput = booleanValueField?.querySelector('.boolean-value-input');
        
        if (type === 'boolean') {
            if (integerValueField) integerValueField.style.display = 'none';
            if (booleanValueField) booleanValueField.style.display = 'block';
            if (integerValueInput) {
                integerValueInput.removeAttribute('required');
                integerValueInput.removeAttribute('name');
            }
            if (booleanValueInput) {
                booleanValueInput.setAttribute('required', 'required');
                booleanValueInput.setAttribute('name', valueFieldName);
            }
        } else {
            if (integerValueField) integerValueField.style.display = 'block';
            if (booleanValueField) booleanValueField.style.display = 'none';
            if (booleanValueInput) {
                booleanValueInput.removeAttribute('required');
                booleanValueInput.removeAttribute('name');
            }
            if (integerValueInput) {
                integerValueInput.removeAttribute('required');
                integerValueInput.setAttribute('name', valueFieldName);
            }
        }
    }
    
    updateValueFieldName(featureDiv, currentFeatureIndex, isBoolean) {
        const integerValueField = featureDiv.querySelector('.integer-value-field');
        const booleanValueField = featureDiv.querySelector('.boolean-value-field');
        
        if (isBoolean) {
            const integerInput = integerValueField?.querySelector('input');
            if (integerInput) {
                integerInput.removeAttribute('name');
            }
        } else {
            const booleanInput = booleanValueField?.querySelector('input.boolean-value-input');
            if (booleanInput) {
                booleanInput.removeAttribute('name');
            }
        }
    }
    
    removeFeature(index) {
        const featureDiv = this.container.querySelector(`[data-feature-index="${index}"]`);
        if (featureDiv) {
            featureDiv.remove();
            this.updateFeatureNumbers();
        }
    }
    
    updateFeatureNumbers() {
        const features = this.container.querySelectorAll('.feature-item');
        features.forEach((featureDiv, index) => {
            // Ищем элемент с текстом "Свойство #"
            const headerDiv = featureDiv.querySelector('.flex.items-center.justify-between');
            if (headerDiv) {
                const numberElement = headerDiv.querySelector('span.text-sm.font-semibold');
                if (numberElement && numberElement.textContent.includes('Свойство #')) {
                    numberElement.textContent = `Свойство #${index + 1}`;
                }
            }
        });
        
        // Обновляем счетчик свойств в информационной панели
        const featuresCountElement = document.getElementById('features-count');
        if (featuresCountElement) {
            featuresCountElement.textContent = features.length;
        }
    }
    
    validateForm() {
        const form = document.getElementById('plan-form');
        if (!form) {
            return true;
        }
        
        const features = form.querySelectorAll('[data-feature-index]');
        if (features.length === 0) {
            // Если нет свойств, это нормально - можно сохранить тариф без свойств
            return true;
        }
        
        let isValid = true;
        const errors = [];
        
        features.forEach((featureDiv, index) => {
            // Ищем все input и select с name содержащим features
            const allInputs = featureDiv.querySelectorAll('input, select');
            let keyInput = null;
            let typeInput = null;
            let valueInput = null;
            
            allInputs.forEach(input => {
                const name = input.name || '';
                if (name.includes('features') && name.includes('key')) {
                    keyInput = input;
                }
                if (name.includes('features') && name.includes('type')) {
                    typeInput = input;
                }
                if (name.includes('features') && name.includes('value')) {
                    valueInput = input;
                }
            });
            
            // Проверяем key
            if (!keyInput) {
                errors.push(`Свойство ${index + 1}: не найден ключ`);
                isValid = false;
            } else if (!keyInput.value || keyInput.value.trim() === '') {
                errors.push(`Свойство ${index + 1}: не указан ключ`);
                isValid = false;
            }
            
            // Проверяем type
            if (!typeInput) {
                errors.push(`Свойство ${index + 1}: не найден тип`);
                isValid = false;
            } else if (!typeInput.value || typeInput.value.trim() === '') {
                errors.push(`Свойство ${index + 1}: не указан тип`);
                isValid = false;
            }
            
            // Проверяем value (только для boolean типа, для integer значение может быть пустым)
            if (!valueInput) {
                errors.push(`Свойство ${index + 1}: не найдено поле значения`);
                isValid = false;
            } else {
                const value = valueInput.value;
                const isBooleanType = typeInput && typeInput.value === 'boolean';
                // Для boolean типа значение обязательно, для integer может быть пустым
                if (isBooleanType && (value === '' || value === null || value === undefined || (typeof value === 'string' && value.trim() === ''))) {
                    errors.push(`Свойство ${index + 1}: не указано значение (boolean тип требует значение)`);
                    isValid = false;
                }
            }
        });
        
        if (!isValid) {
            alert('Пожалуйста, заполните все поля свойств тарифа:\n' + errors.join('\n'));
            return false;
        }
        
        return true;
    }
}

// Экспортируем для использования в других модулях
// Убеждаемся, что класс доступен глобально сразу
if (typeof window !== 'undefined') {
    window.PlanFeaturesManager = PlanFeaturesManager;
}

// Также экспортируем для ES6 модулей
if (typeof module !== 'undefined' && module.exports) {
    module.exports = PlanFeaturesManager;
}

// Для Vite/ES modules - экспортируем по умолчанию
export default PlanFeaturesManager;
