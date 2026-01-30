@extends('layouts.panel')

@section('title', 'Редактирование свойства')

@section('content')
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 pb-20 sm:pb-8">
        <!-- Breadcrumbs -->
        <nav class="mb-4 sm:mb-6" aria-label="Breadcrumb">
            <ol class="flex items-center space-x-1 sm:space-x-2 text-xs sm:text-sm text-slate-500 dark:text-slate-400 overflow-x-auto">
                <li class="flex-shrink-0">
                    <a href="{{ route('panel.index') }}" class="hover:text-slate-700 dark:hover:text-slate-300 transition-colors">
                        <i class="fa-solid fa-home sm:hidden"></i>
                        <span class="hidden sm:inline">Главная</span>
                    </a>
                </li>
                <li class="flex-shrink-0"><i class="fa-solid fa-chevron-right text-xs"></i></li>
                <li class="flex-shrink-0">
                    <a href="{{ route('panel.plans.index') }}" class="hover:text-slate-700 dark:hover:text-slate-300 transition-colors">
                        Тарифы
                    </a>
                </li>
                <li class="flex-shrink-0"><i class="fa-solid fa-chevron-right text-xs"></i></li>
                <li class="flex-shrink-0">
                    <a href="{{ route('panel.plans.properties.index') }}" class="hover:text-slate-700 dark:hover:text-slate-300 transition-colors">
                        Свойства
                    </a>
                </li>
                <li class="flex-shrink-0"><i class="fa-solid fa-chevron-right text-xs"></i></li>
                <li class="flex-shrink-0 text-slate-900 dark:text-white font-medium">Редактирование</li>
            </ol>
        </nav>

        <!-- Page Header -->
        <div class="mb-6 sm:mb-8">
            <div class="flex items-start sm:items-center gap-3 sm:gap-4">
                <div class="h-10 w-10 sm:h-12 sm:w-12 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center shadow-sm flex-shrink-0">
                    <i class="fa-solid fa-edit text-white text-base sm:text-lg"></i>
                </div>
                <div class="min-w-0 flex-1">
                    <h1 class="text-xl sm:text-2xl lg:text-3xl font-bold text-slate-900 dark:text-white mb-1">Редактирование свойства</h1>
                    <div class="flex items-center gap-3 flex-wrap">
                        <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-400">Измените параметры свойства</p>
                        <div class="flex items-center gap-2 px-2.5 py-1 rounded-lg bg-slate-100 dark:bg-slate-800 text-xs font-medium text-slate-700 dark:text-slate-300">
                            <i class="fa-solid fa-code text-slate-500"></i>
                            <code class="font-mono">{{ $metric->key }}</code>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form -->
        <form method="POST" action="{{ route('panel.plans.properties.update', $metric) }}" id="property-form" class="space-y-6">
            @csrf
            @method('PATCH')

            <!-- Основная информация -->
            <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
                <div class="px-4 sm:px-6 py-4 border-b border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/50">
                    <div class="flex items-center gap-3">
                        <div class="h-9 w-9 rounded-lg bg-indigo-100 dark:bg-indigo-500/20 flex items-center justify-center flex-shrink-0">
                            <span class="text-sm font-bold text-indigo-600 dark:text-indigo-400">1</span>
                        </div>
                        <div>
                            <h2 class="text-base sm:text-lg font-semibold text-slate-900 dark:text-white">Основная информация</h2>
                            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-0.5">Ключ, название и описание свойства</p>
                        </div>
                    </div>
                </div>
                
                <div class="p-4 sm:p-6 space-y-5">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label for="key" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">
                                <i class="fa-solid fa-key text-xs text-slate-400 mr-1.5"></i>Ключ свойства
                                <span class="text-rose-500 ml-1" aria-label="обязательное поле">*</span>
                            </label>
                            <input
                                type="text"
                                id="key"
                                name="key"
                                value="{{ old('key', $metric->key) }}"
                                required
                                pattern="^[a-z][a-z0-9_]*$"
                                autocomplete="off"
                                aria-required="true"
                                aria-invalid="{{ $errors->has('key') ? 'true' : 'false' }}"
                                aria-describedby="{{ $errors->has('key') ? 'key-error' : 'key-help' }}"
                                class="w-full px-4 py-3 rounded-lg border {{ $errors->has('key') ? 'border-rose-500 focus:ring-rose-500 focus:border-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-indigo-500 focus:border-indigo-500' }} bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 transition-all text-sm sm:text-base font-mono"
                                placeholder="max_locations"
                            />
                            @if(!$errors->has('key'))
                                <p id="key-help" class="mt-2 text-xs text-slate-500 dark:text-slate-400">
                                    <i class="fa-solid fa-info-circle mr-1"></i>
                                    Только строчные буквы, цифры и подчеркивания (snake_case)
                                </p>
                            @endif
                            @error('key')
                                <p id="key-error" class="mt-2 text-xs text-rose-600 dark:text-rose-400 flex items-center gap-1" role="alert">
                                    <i class="fa-solid fa-circle-exclamation"></i>
                                    <span>{{ $message }}</span>
                                </p>
                            @enderror
                        </div>

                        <div>
                            <label for="type" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">
                                <i class="fa-solid fa-list text-xs text-slate-400 mr-1.5"></i>Тип свойства
                                <span class="text-rose-500 ml-1" aria-label="обязательное поле">*</span>
                            </label>
                            <select
                                id="type"
                                name="type"
                                required
                                aria-required="true"
                                aria-invalid="{{ $errors->has('type') ? 'true' : 'false' }}"
                                class="w-full px-4 py-3 rounded-lg border {{ $errors->has('type') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-indigo-500' }} bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 transition-all text-sm sm:text-base">
                                <option value="">-- Выберите тип --</option>
                                <option value="integer" {{ old('type', $metric->type) === 'integer' ? 'selected' : '' }}>Число (integer)</option>
                                <option value="boolean" {{ old('type', $metric->type) === 'boolean' ? 'selected' : '' }}>Да/Нет (boolean)</option>
                            </select>
                            @error('type')
                                <p class="mt-2 text-xs text-rose-600 dark:text-rose-400 flex items-center gap-1" role="alert">
                                    <i class="fa-solid fa-circle-exclamation"></i>
                                    <span>{{ $message }}</span>
                                </p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label for="label" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">
                            <i class="fa-solid fa-tag text-xs text-slate-400 mr-1.5"></i>Название свойства
                            <span class="text-rose-500 ml-1" aria-label="обязательное поле">*</span>
                        </label>
                        <input
                            type="text"
                            id="label"
                            name="label"
                            value="{{ old('label', $metric->label) }}"
                            required
                            autocomplete="off"
                            aria-required="true"
                            aria-invalid="{{ $errors->has('label') ? 'true' : 'false' }}"
                            aria-describedby="{{ $errors->has('label') ? 'label-error' : 'label-help' }}"
                            class="w-full px-4 py-3 rounded-lg border {{ $errors->has('label') ? 'border-rose-500 focus:ring-rose-500 focus:border-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-indigo-500 focus:border-indigo-500' }} bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 transition-all text-sm sm:text-base"
                            placeholder="Максимальное количество локаций"
                        />
                        @if(!$errors->has('label'))
                            <p id="label-help" class="mt-2 text-xs text-slate-500 dark:text-slate-400">
                                <i class="fa-solid fa-lightbulb mr-1"></i>
                                Понятное название, которое будет отображаться в интерфейсе
                            </p>
                        @endif
                        @error('label')
                            <p id="label-error" class="mt-2 text-xs text-rose-600 dark:text-rose-400 flex items-center gap-1" role="alert">
                                <i class="fa-solid fa-circle-exclamation"></i>
                                <span>{{ $message }}</span>
                            </p>
                        @enderror
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">
                            <i class="fa-solid fa-align-left text-xs text-slate-400 mr-1.5"></i>Описание
                        </label>
                        <textarea
                            id="description"
                            name="description"
                            rows="3"
                            maxlength="500"
                            aria-describedby="description-help description-count"
                            class="w-full px-4 py-3 rounded-lg border border-slate-300 dark:border-slate-700 focus:ring-indigo-500 focus:border-indigo-500 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 transition-all resize-none text-sm sm:text-base"
                            placeholder="Краткое описание назначения свойства..."
                        >{{ old('description', $metric->description) }}</textarea>
                        <div class="mt-2 flex items-center justify-between">
                            <p id="description-help" class="text-xs text-slate-500 dark:text-slate-400">
                                Описание поможет понять назначение свойства
                            </p>
                            <span id="description-count" class="text-xs text-slate-400 dark:text-slate-500">
                                <span id="description-length">{{ mb_strlen(old('description', $metric->description)) }}</span>/500
                            </span>
                        </div>
                        @error('description')
                            <p class="mt-2 text-xs text-rose-600 dark:text-rose-400 flex items-center gap-1" role="alert">
                                <i class="fa-solid fa-circle-exclamation"></i>
                                <span>{{ $message }}</span>
                            </p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Дополнительные настройки -->
            <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
                <div class="px-4 sm:px-6 py-4 border-b border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/50">
                    <div class="flex items-center gap-3">
                        <div class="h-9 w-9 rounded-lg bg-purple-100 dark:bg-purple-500/20 flex items-center justify-center flex-shrink-0">
                            <span class="text-sm font-bold text-purple-600 dark:text-purple-400">2</span>
                        </div>
                        <div>
                            <h2 class="text-base sm:text-lg font-semibold text-slate-900 dark:text-white">Дополнительные настройки</h2>
                            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-0.5">Иконка и статус</p>
                        </div>
                    </div>
                </div>
                
                <div class="p-4 sm:p-6 space-y-5">
                    <div>
                        <label for="icon" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">
                            <i class="fa-solid fa-icons text-xs text-slate-400 mr-1.5"></i>Иконка FontAwesome
                        </label>
                        <div class="relative">
                            <input
                                type="text"
                                id="icon"
                                name="icon"
                                value="{{ old('icon', $metric->icon) }}"
                                autocomplete="off"
                                aria-describedby="icon-help"
                                class="w-full px-4 py-3 rounded-lg border border-slate-300 dark:border-slate-700 focus:ring-indigo-500 focus:border-indigo-500 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 transition-all text-sm sm:text-base"
                                placeholder="fa-location-dot"
                            />
                            @if(old('icon', $metric->icon))
                                <div class="absolute right-3 top-1/2 -translate-y-1/2">
                                    <i class="{{ old('icon', $metric->icon) }} text-slate-400 text-lg"></i>
                                </div>
                            @endif
                        </div>
                        <p id="icon-help" class="mt-2 text-xs text-slate-500 dark:text-slate-400">
                            <i class="fa-solid fa-lightbulb mr-1"></i>
                            Например: fa-location-dot, fa-user-tie, fa-chart-line, fa-bolt
                        </p>
                        @error('icon')
                            <p class="mt-2 text-xs text-rose-600 dark:text-rose-400 flex items-center gap-1" role="alert">
                                <i class="fa-solid fa-circle-exclamation"></i>
                                <span>{{ $message }}</span>
                            </p>
                        @enderror
                    </div>

                    <div>
                        <label class="flex items-start gap-3 p-4 rounded-lg border-2 {{ old('is_active', $metric->is_active) ? 'border-indigo-300 dark:border-indigo-600 bg-indigo-50 dark:bg-indigo-500/10' : 'border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50' }} hover:border-indigo-300 dark:hover:border-indigo-600 cursor-pointer transition-all group">
                            <input
                                type="checkbox"
                                id="is_active"
                                name="is_active"
                                value="1"
                                {{ old('is_active', $metric->is_active) ? 'checked' : '' }}
                                class="mt-0.5 w-5 h-5 text-indigo-600 rounded border-slate-300 focus:ring-indigo-500 focus:ring-2 flex-shrink-0"
                            />
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="text-sm font-semibold text-slate-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">Активна</span>
                                    @if(old('is_active', $metric->is_active))
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-emerald-100 text-emerald-800 dark:bg-emerald-500/20 dark:text-emerald-300">
                                            <i class="fa-solid fa-check-circle mr-1"></i>
                                            Включено
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-slate-200 text-slate-600 dark:bg-slate-700 dark:text-slate-400">
                                            <i class="fa-solid fa-pause-circle mr-1"></i>
                                            Выключено
                                        </span>
                                    @endif
                                </div>
                                <p class="text-xs text-slate-600 dark:text-slate-400">Только активные свойства будут отображаться при создании и редактировании тарифов</p>
                            </div>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Sticky Actions Bar -->
            <div class="sticky bottom-0 left-0 right-0 z-50 bg-white/95 dark:bg-slate-900/95 backdrop-blur-sm border-t-2 border-slate-200 dark:border-slate-800 shadow-2xl sm:shadow-none sm:border-t-0 sm:static sm:bg-transparent sm:backdrop-blur-none">
                <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="py-4 sm:py-6">
                        <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 sm:gap-4">
                            <div class="flex items-center gap-3 order-2 sm:order-1">
                                <a
                                    href="{{ route('panel.plans.properties.index') }}"
                                    class="inline-flex items-center justify-center gap-2 px-4 sm:px-5 py-3 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-xl font-medium transition-all shadow-sm hover:shadow-md text-sm sm:text-base">
                                    <i class="fa-solid fa-arrow-left"></i>
                                    <span class="hidden sm:inline">Вернуться к списку</span>
                                    <span class="sm:hidden">Отмена</span>
                                </a>
                                <button
                                    type="button"
                                    onclick="window.history.back()"
                                    class="hidden sm:inline-flex items-center justify-center gap-2 px-4 py-3 bg-slate-50 hover:bg-slate-100 dark:bg-slate-800/50 dark:hover:bg-slate-800 text-slate-600 dark:text-slate-400 rounded-xl font-medium transition-all text-sm">
                                    <i class="fa-solid fa-undo"></i>
                                    <span>Назад</span>
                                </button>
                            </div>
                            
                            <button
                                type="submit"
                                id="submit-btn"
                                class="order-1 sm:order-2 flex-1 sm:flex-none inline-flex items-center justify-center gap-2.5 px-5 sm:px-6 py-3 bg-gradient-to-r from-indigo-600 via-indigo-600 to-indigo-700 hover:from-indigo-700 hover:via-indigo-700 hover:to-indigo-800 text-white rounded-xl font-semibold transition-all shadow-lg hover:shadow-xl text-sm sm:text-base group">
                                <i class="fa-solid fa-save group-hover:scale-110 transition-transform"></i>
                                <span>Сохранить изменения</span>
                                <i class="fa-solid fa-arrow-right ml-1 opacity-0 group-hover:opacity-100 group-hover:translate-x-1 transition-all"></i>
                            </button>
                        </div>

                        <p class="mt-3 sm:hidden text-center text-xs text-slate-500 dark:text-slate-400">
                            <i class="fa-solid fa-info-circle mr-1"></i>
                            Проверьте все поля перед сохранением
                        </p>
                    </div>
                </div>
            </div>
        </form>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Счетчик символов для описания
            const descriptionTextarea = document.getElementById('description');
            const descriptionCount = document.getElementById('description-length');
            if (descriptionTextarea && descriptionCount) {
                descriptionTextarea.addEventListener('input', function() {
                    descriptionCount.textContent = this.value.length;
                });
            }

            // Предпросмотр иконки
            const iconInput = document.getElementById('icon');
            if (iconInput) {
                let iconPreview = iconInput.parentElement.querySelector('.absolute');
                iconInput.addEventListener('input', function() {
                    const iconValue = this.value.trim();
                    if (iconValue) {
                        if (!iconPreview) {
                            iconPreview = document.createElement('div');
                            iconPreview.className = 'absolute right-3 top-1/2 -translate-y-1/2';
                            iconInput.parentElement.classList.add('relative');
                            iconInput.parentElement.appendChild(iconPreview);
                        }
                        iconPreview.innerHTML = `<i class="${iconValue} text-slate-400 text-lg"></i>`;
                    } else if (iconPreview) {
                        iconPreview.remove();
                        iconPreview = null;
                    }
                });
            }

            // Обновление визуального состояния чекбокса is_active
            const isActiveCheckbox = document.getElementById('is_active');
            const isActiveLabel = isActiveCheckbox?.closest('label');
            if (isActiveCheckbox && isActiveLabel) {
                const updateCheckboxState = () => {
                    if (isActiveCheckbox.checked) {
                        isActiveLabel.classList.remove('border-slate-200', 'dark:border-slate-700', 'bg-slate-50', 'dark:bg-slate-800/50');
                        isActiveLabel.classList.add('border-indigo-300', 'dark:border-indigo-600', 'bg-indigo-50', 'dark:bg-indigo-500/10');
                        const statusBadge = isActiveLabel.querySelector('.inline-flex');
                        if (statusBadge) {
                            statusBadge.className = 'inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-emerald-100 text-emerald-800 dark:bg-emerald-500/20 dark:text-emerald-300';
                            statusBadge.innerHTML = '<i class="fa-solid fa-check-circle mr-1"></i> Включено';
                        }
                    } else {
                        isActiveLabel.classList.remove('border-indigo-300', 'dark:border-indigo-600', 'bg-indigo-50', 'dark:bg-indigo-500/10');
                        isActiveLabel.classList.add('border-slate-200', 'dark:border-slate-700', 'bg-slate-50', 'dark:bg-slate-800/50');
                        const statusBadge = isActiveLabel.querySelector('.inline-flex');
                        if (statusBadge) {
                            statusBadge.className = 'inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-slate-200 text-slate-600 dark:bg-slate-700 dark:text-slate-400';
                            statusBadge.innerHTML = '<i class="fa-solid fa-pause-circle mr-1"></i> Выключено';
                        }
                    }
                };
                isActiveCheckbox.addEventListener('change', updateCheckboxState);
                updateCheckboxState();
            }

            // Горячие клавиши
            document.addEventListener('keydown', function(e) {
                if ((e.ctrlKey || e.metaKey) && e.key === 'Enter') {
                    e.preventDefault();
                    const submitBtn = document.getElementById('submit-btn');
                    if (submitBtn) {
                        submitBtn.click();
                    }
                }
            });

            // Индикатор загрузки
            const form = document.getElementById('property-form');
            if (form) {
                form.addEventListener('submit', function() {
                    const submitBtn = document.getElementById('submit-btn');
                    if (submitBtn) {
                        submitBtn.disabled = true;
                        submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> <span>Сохранение...</span>';
                    }
                });
            }
        });
    </script>
    @endpush
@endsection
