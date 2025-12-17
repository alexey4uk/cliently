@extends('layouts.user')
@section('content')
    <!-- Заголовок страницы -->
    <div class="flex items-baseline justify-between gap-2 mb-6">
        <div>
            <h1 class="text-xl md:text-lg font-semibold text-slate-900 dark:text-white">Настройка бизнеса</h1>
            <p class="text-sm md:text-xs text-slate-500 dark:text-slate-400">Заполните информацию о вашем бизнесе</p>
        </div>
    </div>

    <!-- Прогресс-бар -->
    <div class="mb-8">
        <div class="flex items-center justify-between mb-2">
            <span class="text-sm font-medium text-slate-700 dark:text-slate-300">Шаг <span id="currentStep">1</span> из 4</span>
            <span class="text-sm text-slate-500 dark:text-slate-400">25%</span>
        </div>
        <div class="w-full bg-slate-200 dark:bg-slate-800 rounded-full h-2">
            <div id="progressBar" class="bg-indigo-600 h-2 rounded-full transition-all duration-300" style="width: 25%"></div>
        </div>
    </div>

    <!-- Шаги онбординга -->
    <div id="onboardingSteps">
        <!-- Шаг 1: Создание бизнеса -->
        <div id="step1" class="step opacity-0 transform translate-y-2 transition-all duration-300">
            <div class="mb-6">
                <h2 class="text-lg md:text-base font-semibold text-slate-900 dark:text-white mb-2">Создание бизнеса</h2>
                <p class="text-sm text-slate-500 dark:text-slate-400">Основная информация о вашем бизнесе</p>
            </div>

            <form id="businessForm" class="space-y-4">
                <div>
                    <label for="businessName" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Название*</label>
                    <input type="text" id="businessName" required
                        class="w-full px-3 py-2 text-sm rounded-md border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-colors"
                        placeholder="Например: Elite Beauty Salon">
                </div>

                <div>
                    <label for="businessSlug" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Slug*</label>
                    <div class="flex">
                        <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-500 dark:text-slate-400 text-sm">
                            beautybook.ru/
                        </span>
                        <div class="flex-1 relative">
                            <input type="text" id="businessSlug" required
                                class="w-full px-3 py-2 text-sm rounded-r-md border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-colors"
                                placeholder="elite-beauty"
                                pattern="[a-z0-9\-]+"
                                title="Только латинские буквы в нижнем регистре, цифры и дефисы">
                            
                            <!-- Индикатор проверки -->
                            <div id="slugChecking" class="hidden absolute right-2 top-1/2 transform -translate-y-1/2">
                                <div class="animate-spin h-4 w-4 border-2 border-indigo-500 border-t-transparent rounded-full"></div>
                            </div>
                            
                            <div id="slugAvailable" class="hidden absolute right-2 top-1/2 transform -translate-y-1/2 text-emerald-500">
                                <i class="fa-solid fa-check text-sm"></i>
                            </div>
                            
                            <div id="slugUnavailable" class="hidden absolute right-2 top-1/2 transform -translate-y-1/2 text-rose-500">
                                <i class="fa-solid fa-xmark text-sm"></i>
                            </div>
                        </div>
                    </div>
                    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                        Только латинские буквы в нижнем регистре, цифры и дефисы. Например: ip-ivanov, beauty-salon
                    </p>
                    <p id="slugError" class="mt-1 text-xs text-rose-600 dark:text-rose-400 hidden"></p>
                </div>

                <div>
                    <label for="businessDescription" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Описание</label>
                    <textarea id="businessDescription" rows="3"
                        class="w-full px-3 py-2 text-sm rounded-md border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-colors resize-none"
                        placeholder="Краткое описание вашего бизнеса..."></textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="businessPhone" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Телефон*</label>
                        <input type="tel" id="businessPhone" required
                            class="w-full px-3 py-2 text-sm rounded-md border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-colors"
                            placeholder="+7 (999) 123-45-67">
                    </div>

                    <div>
                        <label for="businessEmail" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Почта</label>
                        <input type="email" id="businessEmail"
                            class="w-full px-3 py-2 text-sm rounded-md border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-colors"
                            placeholder="info@example.com">
                    </div>
                </div>
            </form>
        </div>

        <!-- Шаг 2: Добавление локации -->
        <div id="step2" class="step hidden opacity-0 transform translate-y-2 transition-all duration-300">
            <div class="mb-6">
                <h2 class="text-lg md:text-base font-semibold text-slate-900 dark:text-white mb-2">Добавление локации</h2>
                <p class="text-sm text-slate-500 dark:text-slate-400">Информация о вашем салоне или студии</p>
            </div>

            <form id="locationForm" class="space-y-4">
                <div>
                    <label for="locationName" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Название*</label>
                    <input type="text" id="locationName" required
                        class="w-full px-3 py-2 text-sm rounded-md border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-colors"
                        placeholder="Например: Основной салон">
                </div>

                <div>
                    <label for="locationAddress" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Адрес*</label>
                    <input type="text" id="locationAddress" required
                        class="w-full px-3 py-2 text-sm rounded-md border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-colors"
                        placeholder="ул. Пушкинская, д. 10">
                </div>

                <div>
                    <label for="locationDescription" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Описание</label>
                    <textarea id="locationDescription" rows="2"
                        class="w-full px-3 py-2 text-sm rounded-md border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-colors resize-none"
                        placeholder="Описание локации..."></textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="locationPhone" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Телефон*</label>
                        <input type="tel" id="locationPhone" required
                            class="w-full px-3 py-2 text-sm rounded-md border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-colors"
                            placeholder="+7 (999) 123-45-67">
                    </div>

                    <div>
                        <label for="locationEmail" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Почта</label>
                        <input type="email" id="locationEmail"
                            class="w-full px-3 py-2 text-sm rounded-md border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-colors"
                            placeholder="salon@example.com">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Время работы*</label>
                    <div class="space-y-3">
                        @foreach(['Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб', 'Вс'] as $day)
                        <div class="flex items-center gap-3">
                            <div class="w-10 text-sm text-slate-600 dark:text-slate-400">{{ $day }}</div>
                            <div class="flex-1 flex items-center gap-2">
                                <select class="flex-1 px-2 py-1 text-sm rounded border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-colors">
                                    @for($i = 8; $i <= 22; $i++)
                                        <option value="{{ $i }}:00">{{ sprintf('%02d:00', $i) }}</option>
                                    @endfor
                                </select>
                                <span class="text-slate-400">-</span>
                                <select class="flex-1 px-2 py-1 text-sm rounded border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-colors">
                                    @for($i = 8; $i <= 22; $i++)
                                        <option value="{{ $i }}:00" {{ $i == 20 ? 'selected' : '' }}>{{ sprintf('%02d:00', $i) }}</option>
                                    @endfor
                                </select>
                            </div>
                            <label class="flex items-center gap-1 cursor-pointer">
                                <input type="checkbox" class="rounded border-slate-300 dark:border-slate-700 text-indigo-600 focus:ring-1 focus:ring-indigo-500 focus:ring-offset-0">
                                <span class="text-xs text-slate-500 dark:text-slate-400">Выходной</span>
                            </label>
                        </div>
                        @endforeach
                    </div>
                </div>
            </form>
        </div>

        <!-- Шаг 3: Добавление услуги -->
        <div id="step3" class="step hidden opacity-0 transform translate-y-2 transition-all duration-300">
            <div class="mb-6">
                <h2 class="text-lg md:text-base font-semibold text-slate-900 dark:text-white mb-2">Добавление услуги</h2>
                <p class="text-sm text-slate-500 dark:text-slate-400">Создайте вашу первую услугу</p>
            </div>

            <form id="serviceForm" class="space-y-4">
                <div>
                    <label for="serviceName" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Название*</label>
                    <input type="text" id="serviceName" required
                        class="w-full px-3 py-2 text-sm rounded-md border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-colors"
                        placeholder="Например: Стрижка женская">
                </div>

                <div>
                    <label for="serviceDescription" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Описание</label>
                    <textarea id="serviceDescription" rows="3"
                        class="w-full px-3 py-2 text-sm rounded-md border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-colors resize-none"
                        placeholder="Подробное описание услуги..."></textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="serviceDuration" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Длительность*</label>
                        <select id="serviceDuration" required
                            class="w-full px-3 py-2 text-sm rounded-md border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-colors">
                            <option value="">Выберите длительность</option>
                            <option value="30">30 минут</option>
                            <option value="45">45 минут</option>
                            <option value="60" selected>1 час</option>
                            <option value="90">1 час 30 минут</option>
                            <option value="120">2 часа</option>
                            <option value="180">3 часа</option>
                        </select>
                    </div>

                    <div>
                        <label for="servicePrice" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Цена*</label>
                        <div class="relative">
                            <input type="number" id="servicePrice" required min="0" step="50"
                                class="w-full pl-8 pr-3 py-2 text-sm rounded-md border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-colors"
                                placeholder="1000">
                            <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-slate-500 dark:text-slate-400">₽</span>
                        </div>
                    </div>
                </div>

                <div class="rounded-lg border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900/50 p-4">
                    <p class="text-sm text-slate-600 dark:text-slate-400">Вы можете добавить больше услуг позже в разделе "Услуги"</p>
                </div>
            </form>
        </div>

        <!-- Шаг 4: Добавление мастера -->
        <div id="step4" class="step hidden opacity-0 transform translate-y-2 transition-all duration-300">
            <div class="mb-6">
                <h2 class="text-lg md:text-base font-semibold text-slate-900 dark:text-white mb-2">Добавление мастера</h2>
                <p class="text-sm text-slate-500 dark:text-slate-400">Добавьте информацию о мастере</p>
            </div>

            <form id="masterForm" class="space-y-4">
                <div>
                    <label for="masterName" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Имя*</label>
                    <input type="text" id="masterName" required
                        class="w-full px-3 py-2 text-sm rounded-md border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-colors"
                        placeholder="Например: Анна Иванова">
                </div>

                <div>
                    <label for="masterSpecialization" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Специализация*</label>
                    <input type="text" id="masterSpecialization" required
                        class="w-full px-3 py-2 text-sm rounded-md border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-colors"
                        placeholder="Например: Парикмахер, барбер, косметолог">
                </div>

                <div>
                    <label for="masterDescription" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Описание</label>
                    <textarea id="masterDescription" rows="3"
                        class="w-full px-3 py-2 text-sm rounded-md border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-colors resize-none"
                        placeholder="Опыт работы, образование, достижения..."></textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="masterPhone" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Телефон*</label>
                        <input type="tel" id="masterPhone" required
                            class="w-full px-3 py-2 text-sm rounded-md border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-colors"
                            placeholder="+7 (999) 123-45-67">
                    </div>

                    <div>
                        <label for="masterEmail" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Почта</label>
                        <input type="email" id="masterEmail"
                            class="w-full px-3 py-2 text-sm rounded-md border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-colors"
                            placeholder="anna@example.com">
                    </div>
                </div>

                <div class="rounded-lg border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900/50 p-4">
                    <p class="text-sm text-slate-600 dark:text-slate-400">Вы можете добавить больше мастеров позже в разделе "Мастера"</p>
                </div>
            </form>
        </div>
    </div>

    <!-- Кнопки навигации -->
    <div class="flex items-center justify-between pt-6 border-t border-slate-200 dark:border-slate-800 mt-8">
        <button id="prevBtn" onclick="prevStep()" class="hidden px-4 py-2 text-sm font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-md transition-colors">
            <i class="fa-solid fa-arrow-left mr-2"></i> Назад
        </button>
        
        <div class="flex items-center gap-3 ml-auto">
            <button onclick="saveAndExit()" class="px-4 py-2 text-sm font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-md transition-colors">
                Сохранить и выйти
            </button>
            
            <button id="nextBtn" onclick="nextStep()" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-md transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed">
                Далее <i class="fa-solid fa-arrow-right ml-2"></i>
            </button>
        </div>
    </div>

    <!-- Модальное окно завершения -->
    <div id="completionModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4">
        <div class="bg-white dark:bg-slate-900 rounded-lg shadow-xl border border-slate-200 dark:border-slate-800 p-6 max-w-md w-full">
            <div class="text-center mb-6">
                <div class="mx-auto w-16 h-16 flex items-center justify-center rounded-full bg-emerald-100 dark:bg-emerald-500/20 mb-4">
                    <i class="fa-solid fa-check text-2xl text-emerald-600 dark:text-emerald-300"></i>
                </div>
                <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-2">Настройка завершена!</h3>
                <p class="text-sm text-slate-500 dark:text-slate-400">Ваш бизнес успешно настроен. Теперь вы можете принимать записи.</p>
            </div>
            <div class="space-y-3">
                <a href="{{ route('dashboard') }}"
                   class="block w-full text-center px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-md transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Перейти в дашборд
                </a>
                <button onclick="closeCompletionModal()"
                        class="block w-full text-center px-4 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-md transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-slate-500">
                    Продолжить редактирование
                </button>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    let currentStep = 1;
    const totalSteps = 4;
    let isSlugAvailable = false;
    let slugCheckTimeout = null;
    let slugIsChecking = false;
    let slugFormatIsValid = false;

    // Регулярное выражение для проверки формата slug
    // Разрешает только латинские буквы в нижнем регистре, цифры и одиночные дефисы
    // Запрещает дефисы в начале и конце, а также множественные дефисы подряд
    const slugRegex = /^[a-z0-9]+(?:-[a-z0-9]+)*$/;

    function validateSlugFormat(slug) {
        return slugRegex.test(slug);
    }

    function formatSlug(input) {
        // Удаляем все недопустимые символы (оставляем только латинские буквы, цифры и дефисы)
        let formatted = input.toLowerCase().replace(/[^a-z0-9\-]/g, '');
        
        // Заменяем множественные дефисы на один
        formatted = formatted.replace(/-+/g, '-');
        
        // Удаляем дефисы в начале и конце
        formatted = formatted.replace(/^-+/, '').replace(/-+$/, '');
        
        return formatted;
    }

    function sanitizeSlugInput(input, cursorPosition) {
        // Преобразуем в нижний регистр
        let sanitized = input.toLowerCase();
        
        // Если пользователь пытается ввести дефис в начале строки - блокируем
        if (cursorPosition === 1 && sanitized.charAt(0) === '-') {
            return input.slice(0, -1); // Удаляем последний введенный символ (дефис)
        }
        
        // Если пользователь пытается ввести дефис после другого дефиса - блокируем
        if (cursorPosition > 1 && sanitized.charAt(cursorPosition - 1) === '-') {
            const prevChar = sanitized.charAt(cursorPosition - 2);
            if (prevChar === '-') {
                return input.slice(0, -1); // Удаляем последний введенный дефис
            }
        }
        
        // Удаляем недопустимые символы
        sanitized = sanitized.replace(/[^a-z0-9\-]/g, '');
        
        return sanitized;
    }

    async function checkSlugAvailability(slug) {
        if (!slug || slug.length < 3) {
            resetSlugValidation();
            return;
        }

        // Проверяем формат
        if (!validateSlugFormat(slug)) {
            showSlugFormatError('Только латинские буквы в нижнем регистре, цифры и одиночные дефисы. Дефисы не могут быть в начале или конце.');
            slugFormatIsValid = false;
            isSlugAvailable = false;
            updateNextButtonState();
            return;
        }

        slugFormatIsValid = true;

        // Показываем индикатор загрузки
        showSlugChecking();
        slugIsChecking = true;

        try {
            const response = await fetch('https://cliently.local/api/slug/check', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ slug: slug })
            });

            const data = await response.json();
            
            if (data.available === true) {
                showSlugAvailable();
                isSlugAvailable = true;
            } else {
                showSlugUnavailable('Этот slug уже занят. Пожалуйста, выберите другой.');
                isSlugAvailable = false;
            }
        } catch (error) {
            console.error('Ошибка при проверке slug:', error);
            showSlugUnavailable('Не удалось проверить доступность slug. Попробуйте позже.');
            isSlugAvailable = false;
        } finally {
            slugIsChecking = false;
            updateNextButtonState();
        }
    }

    function showSlugChecking() {
        document.getElementById('slugChecking').classList.remove('hidden');
        document.getElementById('slugAvailable').classList.add('hidden');
        document.getElementById('slugUnavailable').classList.add('hidden');
        document.getElementById('slugError').classList.add('hidden');
        
        const slugInput = document.getElementById('businessSlug');
        slugInput.classList.remove('border-emerald-500', 'border-rose-500');
        slugInput.classList.add('border-slate-300', 'dark:border-slate-700');
    }

    function showSlugAvailable() {
        document.getElementById('slugChecking').classList.add('hidden');
        document.getElementById('slugAvailable').classList.remove('hidden');
        document.getElementById('slugUnavailable').classList.add('hidden');
        document.getElementById('slugError').classList.add('hidden');
        
        const slugInput = document.getElementById('businessSlug');
        slugInput.classList.remove('border-rose-500', 'border-slate-300', 'dark:border-slate-700');
        slugInput.classList.add('border-emerald-500');
    }

    function showSlugUnavailable(message) {
        document.getElementById('slugChecking').classList.add('hidden');
        document.getElementById('slugAvailable').classList.add('hidden');
        document.getElementById('slugUnavailable').classList.remove('hidden');
        
        const errorElement = document.getElementById('slugError');
        errorElement.textContent = message;
        errorElement.classList.remove('hidden');
        
        const slugInput = document.getElementById('businessSlug');
        slugInput.classList.remove('border-emerald-500', 'border-slate-300', 'dark:border-slate-700');
        slugInput.classList.add('border-rose-500');
    }

    function showSlugFormatError(message) {
        document.getElementById('slugChecking').classList.add('hidden');
        document.getElementById('slugAvailable').classList.add('hidden');
        document.getElementById('slugUnavailable').classList.add('hidden');
        
        const errorElement = document.getElementById('slugError');
        errorElement.textContent = message;
        errorElement.classList.remove('hidden');
        
        const slugInput = document.getElementById('businessSlug');
        slugInput.classList.remove('border-emerald-500', 'border-slate-300', 'dark:border-slate-700');
        slugInput.classList.add('border-rose-500');
    }

    function resetSlugValidation() {
        document.getElementById('slugChecking').classList.add('hidden');
        document.getElementById('slugAvailable').classList.add('hidden');
        document.getElementById('slugUnavailable').classList.add('hidden');
        document.getElementById('slugError').classList.add('hidden');
        
        const slugInput = document.getElementById('businessSlug');
        slugInput.classList.remove('border-emerald-500', 'border-rose-500');
        slugInput.classList.add('border-slate-300', 'dark:border-slate-700');
        
        slugFormatIsValid = false;
        isSlugAvailable = false;
        updateNextButtonState();
    }

    function updateProgress() {
        // Обновляем прогресс-бар
        const progress = (currentStep / totalSteps) * 100;
        document.getElementById('progressBar').style.width = `${progress}%`;
        
        // Обновляем номер шага
        document.getElementById('currentStep').textContent = currentStep;
        
        // Показываем/скрываем кнопку "Назад"
        const prevBtn = document.getElementById('prevBtn');
        if (currentStep > 1) {
            prevBtn.classList.remove('hidden');
        } else {
            prevBtn.classList.add('hidden');
        }
        
        // Обновляем текст кнопки "Далее"
        const nextBtn = document.getElementById('nextBtn');
        if (currentStep === totalSteps) {
            nextBtn.innerHTML = 'Завершить <i class="fa-solid fa-check ml-2"></i>';
        } else {
            nextBtn.innerHTML = 'Далее <i class="fa-solid fa-arrow-right ml-2"></i>';
        }
        
        // Обновляем состояние кнопки "Далее"
        updateNextButtonState();
        
        // Анимация перехода между шагами
        const currentStepElement = document.querySelector('.step:not(.hidden)');
        if (currentStepElement) {
            currentStepElement.classList.add('opacity-0', 'translate-y-2');
            
            setTimeout(() => {
                currentStepElement.classList.add('hidden');
                
                // Скрываем все шаги и показываем текущий
                document.querySelectorAll('.step').forEach(step => {
                    step.classList.add('hidden');
                });
                
                const nextStepElement = document.getElementById(`step${currentStep}`);
                nextStepElement.classList.remove('hidden');
                
                // Анимация появления
                setTimeout(() => {
                    nextStepElement.classList.remove('opacity-0', 'translate-y-2');
                    nextStepElement.classList.add('opacity-100', 'translate-y-0');
                }, 50);
            }, 200);
        }
    }

    function updateNextButtonState() {
        const nextBtn = document.getElementById('nextBtn');
        
        if (currentStep === 1) {
            // Для первого шага проверяем формат и доступность slug
            nextBtn.disabled = slugIsChecking || !slugFormatIsValid || !isSlugAvailable;
        } else {
            nextBtn.disabled = false;
        }
    }

    function validateStep(step) {
        // Простая валидация полей текущего шага
        let isValid = true;
        let firstInvalidField = null;

        switch(step) {
            case 1:
                const businessName = document.getElementById('businessName');
                const businessSlug = document.getElementById('businessSlug');
                const businessPhone = document.getElementById('businessPhone');
                
                if (!businessName.value.trim()) {
                    markFieldInvalid(businessName, 'Введите название бизнеса');
                    firstInvalidField = businessName;
                    isValid = false;
                } else {
                    markFieldValid(businessName);
                }
                
                // Проверка slug (формат и доступность)
                const slugValue = businessSlug.value.trim();
                if (!slugValue) {
                    markFieldInvalid(businessSlug, 'Введите slug');
                    if (!firstInvalidField) firstInvalidField = businessSlug;
                    isValid = false;
                } else if (!validateSlugFormat(slugValue)) {
                    markFieldInvalid(businessSlug, 'Только латинские буквы в нижнем регистре, цифры и одиночные дефисы. Дефисы не могут быть в начале или конце.');
                    if (!firstInvalidField) firstInvalidField = businessSlug;
                    isValid = false;
                } else if (!isSlugAvailable) {
                    markFieldInvalid(businessSlug, 'Этот slug недоступен');
                    if (!firstInvalidField) firstInvalidField = businessSlug;
                    isValid = false;
                } else {
                    markFieldValid(businessSlug);
                }
                
                if (!businessPhone.value.trim()) {
                    markFieldInvalid(businessPhone, 'Введите телефон');
                    if (!firstInvalidField) firstInvalidField = businessPhone;
                    isValid = false;
                } else {
                    markFieldValid(businessPhone);
                }
                break;
                
            case 2:
                const locationName = document.getElementById('locationName');
                const locationAddress = document.getElementById('locationAddress');
                const locationPhone = document.getElementById('locationPhone');
                
                if (!locationName.value.trim()) {
                    markFieldInvalid(locationName, 'Введите название локации');
                    firstInvalidField = locationName;
                    isValid = false;
                } else {
                    markFieldValid(locationName);
                }
                
                if (!locationAddress.value.trim()) {
                    markFieldInvalid(locationAddress, 'Введите адрес');
                    if (!firstInvalidField) firstInvalidField = locationAddress;
                    isValid = false;
                } else {
                    markFieldValid(locationAddress);
                }
                
                if (!locationPhone.value.trim()) {
                    markFieldInvalid(locationPhone, 'Введите телефон');
                    if (!firstInvalidField) firstInvalidField = locationPhone;
                    isValid = false;
                } else {
                    markFieldValid(locationPhone);
                }
                break;
                
            case 3:
                const serviceName = document.getElementById('serviceName');
                const serviceDuration = document.getElementById('serviceDuration');
                const servicePrice = document.getElementById('servicePrice');
                
                if (!serviceName.value.trim()) {
                    markFieldInvalid(serviceName, 'Введите название услуги');
                    firstInvalidField = serviceName;
                    isValid = false;
                } else {
                    markFieldValid(serviceName);
                }
                
                if (!serviceDuration.value) {
                    markFieldInvalid(serviceDuration, 'Выберите длительность');
                    if (!firstInvalidField) firstInvalidField = serviceDuration;
                    isValid = false;
                } else {
                    markFieldValid(serviceDuration);
                }
                
                if (!servicePrice.value || parseFloat(servicePrice.value) <= 0) {
                    markFieldInvalid(servicePrice, 'Введите корректную цену');
                    if (!firstInvalidField) firstInvalidField = servicePrice;
                    isValid = false;
                } else {
                    markFieldValid(servicePrice);
                }
                break;
                
            case 4:
                const masterName = document.getElementById('masterName');
                const masterSpecialization = document.getElementById('masterSpecialization');
                const masterPhone = document.getElementById('masterPhone');
                
                if (!masterName.value.trim()) {
                    markFieldInvalid(masterName, 'Введите имя мастера');
                    firstInvalidField = masterName;
                    isValid = false;
                } else {
                    markFieldValid(masterName);
                }
                
                if (!masterSpecialization.value.trim()) {
                    markFieldInvalid(masterSpecialization, 'Введите специализацию');
                    if (!firstInvalidField) firstInvalidField = masterSpecialization;
                    isValid = false;
                } else {
                    markFieldValid(masterSpecialization);
                }
                
                if (!masterPhone.value.trim()) {
                    markFieldInvalid(masterPhone, 'Введите телефон');
                    if (!firstInvalidField) firstInvalidField = masterPhone;
                    isValid = false;
                } else {
                    markFieldValid(masterPhone);
                }
                break;
        }
        
        if (!isValid && firstInvalidField) {
            firstInvalidField.focus();
            return false;
        }
        
        return isValid;
    }

    function markFieldInvalid(field, message) {
        field.classList.add('border-rose-500', 'focus:ring-rose-500');
        field.classList.remove('border-slate-300', 'dark:border-slate-700', 'focus:ring-indigo-500');
        
        // Удаляем предыдущие сообщения об ошибке
        const existingError = field.parentElement.querySelector('.error-message');
        if (existingError) {
            existingError.remove();
        }
        
        // Добавляем сообщение об ошибке
        const errorDiv = document.createElement('div');
        errorDiv.className = 'mt-1 text-xs text-rose-600 dark:text-rose-400 error-message';
        errorDiv.textContent = message;
        field.parentElement.appendChild(errorDiv);
    }

    function markFieldValid(field) {
        field.classList.remove('border-rose-500', 'focus:ring-rose-500');
        field.classList.add('border-slate-300', 'dark:border-slate-700', 'focus:ring-indigo-500');
        
        // Удаляем сообщения об ошибке
        const existingError = field.parentElement.querySelector('.error-message');
        if (existingError) {
            existingError.remove();
        }
    }

    function nextStep() {
        if (!validateStep(currentStep)) return;
        
        if (currentStep < totalSteps) {
            currentStep++;
            updateProgress();
        } else {
            // Если последний шаг - завершаем
            completeOnboarding();
        }
    }

    function prevStep() {
        if (currentStep > 1) {
            currentStep--;
            updateProgress();
        }
    }

    function completeOnboarding() {
        // Здесь будет отправка данных на сервер
        // Пока просто показываем модальное окно
        document.getElementById('completionModal').classList.remove('hidden');
    }

    function closeCompletionModal() {
        document.getElementById('completionModal').classList.add('hidden');
    }

    function saveAndExit() {
        // Сохранение данных и переход на дашборд
        if (confirm('Сохранить текущие данные и выйти?')) {
            // Здесь будет отправка данных
            window.location.href = "{{ route('dashboard') }}";
        }
    }

    // Обработка ввода slug с блокировкой недопустимых символов
    document.getElementById('businessSlug').addEventListener('input', function(e) {
        const originalValue = e.target.value;
        const cursorPosition = e.target.selectionStart;
        
        // Санитизируем ввод с учетом позиции курсора
        const sanitizedValue = sanitizeSlugInput(originalValue, cursorPosition);
        
        // Если значение изменилось - обновляем поле
        if (originalValue !== sanitizedValue) {
            e.target.value = sanitizedValue;
            // Восстанавливаем позицию курсора (минус один удаленный символ)
            const newCursorPosition = Math.max(0, cursorPosition - 1);
            e.target.setSelectionRange(newCursorPosition, newCursorPosition);
        }
        
        const slug = sanitizedValue.trim();
        
        // Сбрасываем предыдущий таймаут
        if (slugCheckTimeout) {
            clearTimeout(slugCheckTimeout);
        }
        
        // Сбрасываем валидацию если поле пустое
        if (!slug) {
            resetSlugValidation();
            return;
        }
        
        // Ждем 500мс после последнего ввода перед проверкой
        slugCheckTimeout = setTimeout(() => {
            checkSlugAvailability(slug);
        }, 500);
    });

    // Форматируем slug при уходе с поля (удаляем дефис в конце если есть)
    document.getElementById('businessSlug').addEventListener('blur', function(e) {
        const formattedSlug = formatSlug(e.target.value);
        if (e.target.value !== formattedSlug) {
            e.target.value = formattedSlug;
        }
        
        if (formattedSlug.trim()) {
            checkSlugAvailability(formattedSlug.trim());
        } else {
            resetSlugValidation();
        }
    });

    // Также блокируем вставку недопустимых символов через Ctrl+V
    document.getElementById('businessSlug').addEventListener('paste', function(e) {
        e.preventDefault();
        const pastedText = (e.clipboardData || window.clipboardData).getData('text');
        
        // Форматируем вставленный текст
        const formatted = formatSlug(pastedText);
        
        // Вставляем отформатированный текст
        const start = this.selectionStart;
        const end = this.selectionEnd;
        const value = this.value;
        
        this.value = value.substring(0, start) + formatted + value.substring(end);
        
        // Устанавливаем курсор после вставленного текста
        const newCursorPosition = start + formatted.length;
        this.setSelectionRange(newCursorPosition, newCursorPosition);
        
        // Триггерим событие input для проверки
        this.dispatchEvent(new Event('input'));
    });

    // Очистка ошибок при вводе
    document.querySelectorAll('input, select, textarea').forEach(field => {
        field.addEventListener('input', function() {
            if (this.classList.contains('border-rose-500')) {
                markFieldValid(this);
            }
        });
    });

    // Инициализация
    document.addEventListener('DOMContentLoaded', function() {
        // Анимация появления первого шага
        setTimeout(() => {
            const firstStep = document.getElementById('step1');
            firstStep.classList.remove('opacity-0', 'translate-y-2');
            firstStep.classList.add('opacity-100', 'translate-y-0');
        }, 100);
        
        updateProgress();
    });
</script>
@endpush