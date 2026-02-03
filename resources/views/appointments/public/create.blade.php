<!DOCTYPE html>
<html lang="ru" class="h-full overflow-x-hidden">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Онлайн запись - {{ $business->name }}</title>

    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-slate-100 dark:bg-slate-950 text-slate-900 dark:text-slate-50 font-sans overflow-x-hidden">
    <div class="min-h-screen py-8 px-4 sm:px-6 lg:px-8 overflow-x-hidden">
        <div class="max-w-3xl mx-auto w-full">
            <!-- Header -->
            <div class="text-center mb-8">
                <h1 class="text-2xl md:text-3xl font-semibold text-slate-900 dark:text-white mb-2">
                    <i class="fa-solid fa-calendar-check text-indigo-600 dark:text-indigo-400 mr-2"></i>
                    Онлайн запись
                </h1>
                <p class="text-slate-600 dark:text-slate-400 text-base md:text-sm">{{ $business->name }}</p>
            </div>

            <!-- Progress Bar -->
            <div class="mb-6 overflow-x-hidden">
                <div class="flex items-center justify-between mb-2">
                    <div class="flex-1 flex items-center min-w-0">
                        <div class="step-indicator active" data-step="1">
                            <div class="step-number">1</div>
                            <div class="step-label hidden sm:block">Локация</div>
                        </div>
                        <div class="step-line"></div>
                        <div class="step-indicator" data-step="2">
                            <div class="step-number">2</div>
                            <div class="step-label hidden sm:block">Услуга</div>
                        </div>
                        <div class="step-line"></div>
                        <div class="step-indicator" data-step="3">
                            <div class="step-number">3</div>
                            <div class="step-label hidden sm:block">Мастер</div>
                        </div>
                        <div class="step-line"></div>
                        <div class="step-indicator" data-step="4">
                            <div class="step-number">4</div>
                            <div class="step-label hidden sm:block">Дата и время</div>
                        </div>
                        <div class="step-line"></div>
                        <div class="step-indicator" data-step="5">
                            <div class="step-number">5</div>
                            <div class="step-label hidden sm:block">Контакты</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form -->
            <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-4 md:p-6 overflow-x-hidden">
                <form method="POST" action="{{ route('public.appointments.store', $business->slug) }}" id="appointment-form" class="space-y-6 w-full">
                    @csrf

                    <!-- Step 1: Локация -->
                    <div class="step-content" data-step="1">
                        <div class="pb-4 border-b border-slate-200 dark:border-slate-700 mb-5">
                            <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-300">
                                Выберите локацию
                            </h3>
                        </div>

                        <div class="space-y-1">
                            @if($locations->count() > 0)
                                @foreach($locations as $location)
                                    <label class="flex items-center justify-between p-3 border-2 border-slate-200 dark:border-slate-700 rounded-lg cursor-pointer hover:border-indigo-500 dark:hover:border-indigo-500 hover:bg-indigo-50 dark:hover:bg-indigo-900/10 transition-colors location-option">
                                        <div class="flex items-center gap-3 flex-1 min-w-0">
                                            <input type="radio" name="location_id" value="{{ $location->id }}" required class="mt-0 mr-0 location-radio flex-shrink-0" {{ old('location_id') == $location->id ? 'checked' : '' }}>
                                            <div class="flex-1 min-w-0">
                                                <div class="font-semibold text-slate-900 dark:text-white text-sm break-words">{{ $location->name }}</div>
                                                @if($location->full_address)
                                                    <div class="text-xs text-slate-600 dark:text-slate-400 mt-0.5 break-words">{{ $location->full_address }}</div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="flex-shrink-0 ml-3">
                                            <i class="fa-solid fa-chevron-right text-slate-400 location-option:not(.active) && text-indigo-600 text-sm"></i>
                                        </div>
                                    </label>
                                @endforeach
                            @else
                                <div class="text-center py-8 text-slate-500 dark:text-slate-400">
                                    <i class="fa-solid fa-info-circle text-2xl mb-2"></i>
                                    <p>Локации не указаны</p>
                                </div>
                            @endif
                        </div>
                        @error('location_id')
                        <p class="mt-2 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Step 2: Услуга -->
                    <div class="step-content hidden" data-step="2">
                        <div class="pb-4 border-b border-slate-200 dark:border-slate-700 mb-5">
                            <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-300">
                                Выберите услугу
                            </h3>
                        </div>

                        <div class="space-y-1">
                            @foreach($services as $service)
                                <label class="flex items-center justify-between p-3 border-2 border-slate-200 dark:border-slate-700 rounded-lg cursor-pointer hover:border-indigo-500 dark:hover:border-indigo-500 hover:bg-indigo-50 dark:hover:bg-indigo-900/10 transition-colors service-option">
                                    <div class="flex items-start gap-3 flex-1 min-w-0">
                                        <input type="radio" name="service_id" value="{{ $service->id }}" required class="mt-0 mr-0 service-radio flex-shrink-0" {{ old('service_id') == $service->id ? 'checked' : '' }}>
                                        <div class="flex-1 min-w-0">
                                            <div class="font-semibold text-slate-900 dark:text-white text-sm break-words">{{ $service->name }}</div>
                                            <div class="text-xs text-slate-600 dark:text-slate-400 mt-0.5">
                                                <span class="font-medium">{{ number_format($service->price, 0, ',', ' ') }} Br</span>
                                                <span class="mx-1.5">•</span>
                                                <span>{{ $service->duration }} мин</span>
                                            </div>
                                            @if($service->description)
                                                <div class="text-xs text-slate-500 dark:text-slate-500 mt-1 break-words line-clamp-1">{{ $service->description }}</div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="flex-shrink-0 ml-3">
                                        <i class="fa-solid fa-chevron-right text-slate-400 service-option:not(.active) && text-indigo-600 text-sm"></i>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                        @error('service_id')
                        <p class="mt-2 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Step 3: Мастер -->
                    <div class="step-content hidden" data-step="3">
                        <div class="pb-4 border-b border-slate-200 dark:border-slate-700 mb-5">
                            <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-300">
                                Выберите мастера
                            </h3>
                        </div>

                        <div class="space-y-1" id="masters-container">
                            <label class="flex items-center justify-between p-3 border-2 border-slate-200 dark:border-slate-700 rounded-lg cursor-pointer hover:border-indigo-500 dark:hover:border-indigo-500 hover:bg-indigo-50 dark:hover:bg-indigo-900/10 transition-colors master-option master-option-any hidden" data-master-id="any">
                                <div class="flex items-center gap-3 flex-1 min-w-0">
                                    <input type="radio" name="master_id" value="" required class="mt-0 mr-0 master-radio flex-shrink-0" {{ !old('master_id') ? 'checked' : '' }}>
                                    <div class="flex-1 min-w-0">
                                        <div class="font-semibold text-slate-900 dark:text-white text-sm">Любой мастер</div>
                                        <div class="text-xs text-slate-600 dark:text-slate-400 mt-0.5">Вам назначат свободного мастера</div>
                                    </div>
                                </div>
                                <i class="fa-solid fa-chevron-right text-slate-400 text-sm flex-shrink-0 ml-3"></i>
                            </label>
                            @foreach($masters as $master)
                                <label class="flex items-center justify-between p-3 border-2 border-slate-200 dark:border-slate-700 rounded-lg cursor-pointer hover:border-indigo-500 dark:hover:border-indigo-500 hover:bg-indigo-50 dark:hover:bg-indigo-900/10 transition-colors master-option hidden" data-master-id="{{ $master->id }}" data-master-services="{{ $master->services->pluck('id')->implode(',') }}">
                                    <div class="flex items-center gap-3 flex-1 min-w-0">
                                        <input type="radio" name="master_id" value="{{ $master->id }}" class="mt-0 mr-0 master-radio flex-shrink-0" {{ old('master_id') == $master->id ? 'checked' : '' }}>
                                        <div class="flex-1 min-w-0">
                                            <div class="font-semibold text-slate-900 dark:text-white text-sm break-words">{{ $master->first_name }} {{ $master->last_name }}</div>
                                            @if($master->specialization)
                                                <div class="text-xs text-slate-600 dark:text-slate-400 mt-0.5 break-words">{{ $master->specialization }}</div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="flex-shrink-0 ml-3">
                                        <i class="fa-solid fa-chevron-right text-slate-400 master-option:not(.active) && text-indigo-600 text-sm"></i>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                        @error('master_id')
                        <p class="mt-2 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Step 4: Дата и время -->
                    <div class="step-content hidden" data-step="4">
                        <div class="pb-4 border-b border-slate-200 dark:border-slate-700 mb-5">
                            <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-300">
                                Выберите дату и время
                            </h3>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label for="date" class="flex items-center gap-1.5 md:gap-2 text-base md:text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                                    <span>Дата*</span>
                                </label>
                                <input type="date" id="date" name="date" required min="{{ date('Y-m-d') }}"
                                       class="w-full px-2.5 md:px-3 py-2 md:py-2.5 text-base md:text-sm rounded-md border {{ $errors->has('date') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-indigo-500' }} bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:border-transparent transition-colors"
                                       value="{{ old('date') }}">
                                @error('date')
                                <p class="mt-2 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="time" class="flex items-center gap-1.5 md:gap-2 text-base md:text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                                    <span>Время*</span>
                                    <span id="time-loading" class="hidden text-indigo-600 dark:text-indigo-400">
                                        <i class="fa-solid fa-spinner fa-spin text-xs"></i>
                                    </span>
                                </label>
                                <select id="time" name="time" required
                                        class="w-full px-2.5 md:px-3 py-2 md:py-2.5 text-base md:text-sm rounded-md border {{ $errors->has('time') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-indigo-500' }} bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:border-transparent transition-colors"
                                        disabled>
                                    <option value="">Сначала выберите услугу, мастера (или «Любой мастер») и дату</option>
                                </select>
                                <div id="time-error" class="hidden mt-2 text-xs text-rose-600 dark:text-rose-400"></div>
                                @error('time')
                                <p class="mt-2 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Дополнительная информация -->
                    <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 overflow-hidden">
                        <button type="button" id="additional-info-toggle-create" class="w-full px-4 py-3 text-left flex items-center justify-between hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                            <span class="text-sm font-semibold text-slate-700 dark:text-slate-300">Пожелания</span>
                            <i class="fa-solid fa-chevron-down text-slate-400 transition-transform duration-200" id="additional-info-icon-create"></i>
                        </button>
                        <div id="additional-info-content-create" class="hidden px-4 pt-4 pb-4">
                            <div>
                                <textarea id="notes" name="notes" rows="3"
                                          class="w-full px-3 py-2 md:py-2.5 text-base md:text-sm rounded-md border {{ $errors->has('notes') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-indigo-500' }} bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:border-transparent transition-colors resize-none">{{ old('notes') }}</textarea>
                                @error('notes')
                                <p class="mt-2 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Step 5: Контактные данные -->
                    <div class="step-content hidden" data-step="5">
                        <div class="pb-4 border-b border-slate-200 dark:border-slate-700 mb-5">
                            <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-300">
                                Контактные данные
                            </h3>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label for="first_name" class="flex items-center gap-1.5 md:gap-2 text-base md:text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                                    <span>Имя*</span>
                                </label>
                                <input type="text" id="first_name" name="first_name" required
                                       class="w-full px-2.5 md:px-3 py-2 md:py-2.5 text-base md:text-sm rounded-md border {{ $errors->has('first_name') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-indigo-500' }} bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:border-transparent transition-colors"
                                       value="{{ old('first_name') }}" placeholder="Введите ваше имя">
                                @error('first_name')
                                <p class="mt-2 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="last_name" class="flex items-center gap-1.5 md:gap-2 text-base md:text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                                    <span>Фамилия</span>
                                </label>
                                <input type="text" id="last_name" name="last_name"
                                       class="w-full px-2.5 md:px-3 py-2 md:py-2.5 text-base md:text-sm rounded-md border {{ $errors->has('last_name') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-indigo-500' }} bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:border-transparent transition-colors"
                                       value="{{ old('last_name') }}" placeholder="Введите вашу фамилию">
                                @error('last_name')
                                <p class="mt-2 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <label for="phone" class="flex items-center gap-1.5 md:gap-2 text-base md:text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                                <span>Телефон*</span>
                            </label>
                            <input type="tel" id="phone" name="phone" required
                                   class="w-full px-2.5 md:px-3 py-2 md:py-2.5 text-base md:text-sm rounded-md border {{ $errors->has('phone') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-indigo-500' }} bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:border-transparent transition-colors"
                                   value="{{ old('phone') }}">
                            @error('phone')
                            <p class="mt-2 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                            @enderror
                        </div>

                    </div>

                    <!-- Navigation Buttons -->
                    <div class="flex items-center justify-between pt-6 border-t border-slate-200 dark:border-slate-700">
                        <button type="button" id="prev-btn" class="hidden px-4 py-2 text-sm font-medium text-slate-700 dark:text-slate-300 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-md hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
                            <i class="fa-solid fa-arrow-left mr-2"></i>
                            Назад
                        </button>
                        <button type="button" id="next-btn" class="ml-auto px-4 py-2 text-sm font-medium text-white bg-indigo-600 dark:bg-indigo-500 rounded-md hover:bg-indigo-700 dark:hover:bg-indigo-600 transition-colors shadow-sm shadow-indigo-600/40">
                            Далее
                            <i class="fa-solid fa-arrow-right ml-2"></i>
                        </button>
                        <button type="submit" id="submit-btn" class="hidden ml-auto px-4 py-2 text-sm font-medium text-white bg-indigo-600 dark:bg-indigo-500 rounded-md hover:bg-indigo-700 dark:hover:bg-indigo-600 transition-colors shadow-sm shadow-indigo-600/40">
                            Записаться
                            <i class="fa-solid fa-check ml-2"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Данные мастеров для фильтрации
        const masters = @json($masters->map(function($master) {
            return [
                'id' => $master->id,
                'services' => $master->services->map(function($service) {
                    return ['id' => $service->id];
                })->toArray()
            ];
        }));

        document.addEventListener('DOMContentLoaded', function() {
            let currentStep = 1;
            const totalSteps = 5;
            const prevBtn = document.getElementById('prev-btn');
            const nextBtn = document.getElementById('next-btn');
            const submitBtn = document.getElementById('submit-btn');

            // Show step
            function showStep(step) {
                document.querySelectorAll('.step-content').forEach(content => {
                    content.classList.add('hidden');
                });

                const currentStepContent = document.querySelector(`.step-content[data-step="${step}"]`);
                if (currentStepContent) {
                    currentStepContent.classList.remove('hidden');
                }

                // Update progress indicators
                document.querySelectorAll('.step-indicator').forEach((indicator, index) => {
                    const stepNum = index + 1;
                    indicator.classList.remove('active', 'completed');
                    if (stepNum < step) {
                        indicator.classList.add('completed');
                    } else if (stepNum === step) {
                        indicator.classList.add('active');
                    }
                });

                // Update buttons
                prevBtn.classList.toggle('hidden', step === 1);
                nextBtn.classList.toggle('hidden', step === totalSteps);
                submitBtn.classList.toggle('hidden', step !== totalSteps);

                currentStep = step;

                // Load masters for step 3 if service is selected
                if (step === 3) {
                    loadMastersForService();
                }

                // Load slots for step 4
                if (step === 4) {
                    // Небольшая задержка, чтобы убедиться, что все элементы отрендерены
                    setTimeout(() => {
                        // Проверяем, что все обязательные поля выбраны
                        const serviceId = document.querySelector('input[name="service_id"]:checked')?.value;
                        const locationId = document.querySelector('input[name="location_id"]:checked')?.value;
                        const masterId = document.querySelector('input[name="master_id"]:checked')?.value;
                        const date = document.getElementById('date').value;
                        
                        const masterChecked = document.querySelector('input[name="master_id"]:checked');
                        if (serviceId && locationId && masterChecked && date) {
                            loadAvailableSlots();
                        } else {
                            console.log('Не все поля выбраны для загрузки слотов:', {
                                serviceId, locationId, masterId: masterChecked?.value, date
                            });
                        }
                    }, 100);
                }
            }

            // Load masters based on selected service
            function loadMastersForService() {
                const serviceId = document.querySelector('input[name="service_id"]:checked')?.value;
                
                if (!serviceId) {
                    document.querySelectorAll('.master-option[data-master-id]').forEach(option => {
                        option.classList.add('hidden');
                    });
                    document.querySelectorAll('.master-option-any').forEach(option => {
                        option.classList.add('hidden');
                    });
                    return;
                }

                // «Любой мастер» показываем всегда при выбранной услуге
                document.querySelectorAll('.master-option-any').forEach(option => {
                    option.classList.remove('hidden');
                });

                let visibleMastersCount = 0;
                document.querySelectorAll('.master-option[data-master-id]:not(.master-option-any)').forEach(option => {
                    const masterServices = option.getAttribute('data-master-services');
                    const serviceIds = masterServices ? masterServices.split(',').map(id => parseInt(id.trim())) : [];
                    
                    if (serviceIds.includes(parseInt(serviceId))) {
                        option.classList.remove('hidden');
                        visibleMastersCount++;
                    } else {
                        option.classList.add('hidden');
                        const radio = option.querySelector('.master-radio');
                        if (radio && radio.checked) {
                            radio.checked = false;
                        }
                    }
                });

                console.log('Загружено мастеров для услуги:', visibleMastersCount, 'Услуга ID:', serviceId);
                
                if (visibleMastersCount === 0) {
                    console.warn('Нет мастеров для выбранной услуги!');
                }
            }

            // Load available slots
            function loadAvailableSlots() {
                const serviceId = document.querySelector('input[name="service_id"]:checked')?.value;
                const date = document.getElementById('date').value;
                const masterId = document.querySelector('input[name="master_id"]:checked')?.value || null;
                const locationId = document.querySelector('input[name="location_id"]:checked')?.value || null;
                const timeSelect = document.getElementById('time');
                const timeLoading = document.getElementById('time-loading');
                const timeError = document.getElementById('time-error');

                if (!serviceId || !date) {
                    timeSelect.innerHTML = '<option value="">Сначала выберите услугу и дату</option>';
                    timeSelect.disabled = true;
                    return;
                }

                const masterSelected = document.querySelector('input[name="master_id"]:checked');
                if (!masterSelected) {
                    timeSelect.innerHTML = '<option value="">Сначала выберите мастера</option>';
                    timeSelect.disabled = true;
                    timeError.textContent = 'Пожалуйста, выберите мастера или «Любой мастер».';
                    timeError.classList.remove('hidden');
                    return;
                }

                if (!locationId) {
                    timeSelect.innerHTML = '<option value="">Сначала выберите локацию</option>';
                    timeSelect.disabled = true;
                    timeError.textContent = 'Пожалуйста, выберите локацию для продолжения.';
                    timeError.classList.remove('hidden');
                    return;
                }

                timeSelect.innerHTML = '<option value="">Загрузка...</option>';
                timeSelect.disabled = true;
                timeLoading.classList.remove('hidden');
                timeError.classList.add('hidden');

                const url = `{{ route('api.public.appointments.available-slots', $business->slug) }}`;
                const params = new URLSearchParams({
                    service_id: serviceId,
                    date: date,
                });

                // Мастер и локация обязательны, всегда передаем их
                if (masterId && masterId !== '') {
                    params.append('master_id', masterId);
                }

                if (locationId && locationId !== '') {
                    params.append('location_id', locationId);
                }

                console.log('Загрузка слотов:', {
                    serviceId,
                    date,
                    masterId,
                    locationId,
                    url: `${url}?${params.toString()}`
                });

                fetch(`${url}?${params.toString()}`, {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                    credentials: 'same-origin',
                })
                .then(response => {
                    console.log('Ответ сервера:', response.status);
                    return response.json();
                })
                .then(data => {
                    console.log('Данные слотов:', data);
                    timeLoading.classList.add('hidden');
                    timeSelect.disabled = false;

                    if (!data.success) {
                        timeSelect.innerHTML = '<option value="">Ошибка</option>';
                        timeError.textContent = data.message || 'Ошибка при загрузке слотов';
                        timeError.classList.remove('hidden');
                        return;
                    }

                    if (data.success && data.slots && data.slots.length > 0) {
                        timeSelect.innerHTML = '<option value="">Выберите время</option>';
                        data.slots.forEach(slot => {
                            const option = document.createElement('option');
                            option.value = slot;
                            option.textContent = slot;
                            timeSelect.appendChild(option);
                        });
                        timeError.classList.add('hidden');
                        console.log('Загружено слотов:', data.slots.length);
                    } else {
                        timeSelect.innerHTML = '<option value="">Нет доступных слотов</option>';
                        
                        // Более информативное сообщение об ошибке
                        let errorMessage = 'На выбранную дату нет доступных временных слотов.';
                        
                        // Проверяем, сегодня ли это
                        const today = new Date().toISOString().split('T')[0];
                        const isToday = date === today;
                        
                        if (data.debug) {
                            console.log('Debug информация:', data.debug);
                            
                            // Проверяем причину отсутствия слотов
                            if (data.debug.master && !data.debug.master.has_working_hours) {
                                errorMessage = 'У мастера не настроено рабочее время. Пожалуйста, обратитесь к администратору или выберите другого мастера.';
                            } else if (data.debug.master && data.debug.master.is_day_off) {
                                errorMessage = 'Выбранная дата является выходным днем для мастера. Пожалуйста, выберите другую дату.';
                            } else if (data.debug.time_windows_count === 0) {
                                errorMessage = 'Мастер не работает в выбранную дату. Пожалуйста, выберите другую дату.';
                            } else if (data.debug.slots_lost_to_bookings > 0 && data.debug.final_slots_count === 0) {
                                if (isToday) {
                                    errorMessage = 'На сегодня все доступные слоты уже заняты. Пожалуйста, выберите другую дату.';
                                } else {
                                    errorMessage = 'Все доступные слоты на эту дату уже заняты. Пожалуйста, выберите другую дату.';
                                }
                            } else if (data.debug.final_slots_count === 0) {
                                if (isToday) {
                                    errorMessage = 'На сегодня нет доступных слотов. Пожалуйста, выберите другую дату.';
                                } else {
                                    errorMessage = 'На выбранную дату нет доступных слотов. Пожалуйста, выберите другую дату.';
                                }
                            }
                        } else {
                            if (isToday) {
                                errorMessage = 'На сегодня нет доступных слотов. Пожалуйста, выберите другую дату.';
                            }
                        }
                        
                        timeError.textContent = errorMessage;
                        timeError.classList.remove('hidden');
                    }
                })
                .catch(error => {
                    console.error('Ошибка при загрузке слотов:', error);
                    timeLoading.classList.add('hidden');
                    timeSelect.disabled = false;
                    timeSelect.innerHTML = '<option value="">Ошибка загрузки</option>';
                    timeError.textContent = 'Произошла ошибка при загрузке доступных слотов. Пожалуйста, обновите страницу.';
                    timeError.classList.remove('hidden');
                });
            }

            // Validate step
            function validateStep(step) {
                if (step === 1) {
                    const locationSelected = document.querySelector('input[name="location_id"]:checked');
                    if (!locationSelected || !locationSelected.value) {
                        alert('Пожалуйста, выберите локацию');
                        return false;
                    }
                }
                if (step === 2) {
                    const serviceSelected = document.querySelector('input[name="service_id"]:checked');
                    if (!serviceSelected) {
                        alert('Пожалуйста, выберите услугу');
                        return false;
                    }
                }
                if (step === 3) {
                    const masterSelected = document.querySelector('input[name="master_id"]:checked');
                    if (!masterSelected) {
                        alert('Пожалуйста, выберите мастера или «Любой мастер»');
                        return false;
                    }
                }
                if (step === 4) {
                    const date = document.getElementById('date').value;
                    const time = document.getElementById('time').value;
                    if (!date) {
                        alert('Пожалуйста, выберите дату');
                        return false;
                    }
                    if (!time) {
                        alert('Пожалуйста, выберите время');
                        return false;
                    }
                }
                return true;
            }

            // Navigation
            nextBtn.addEventListener('click', () => {
                if (validateStep(currentStep)) {
                    if (currentStep < totalSteps) {
                        showStep(currentStep + 1);
                    }
                }
            });

            prevBtn.addEventListener('click', () => {
                if (currentStep > 1) {
                    showStep(currentStep - 1);
                }
            });

            // Service change - reload masters
            document.querySelectorAll('.service-radio').forEach(radio => {
                radio.addEventListener('change', () => {
                    if (currentStep >= 3) {
                        loadMastersForService();
                        // Сбрасываем выбор мастера при смене услуги, по умолчанию «Любой мастер»
                        const anyMasterRadio = document.querySelector('.master-option-any .master-radio');
                        document.querySelectorAll('.master-radio').forEach(masterRadio => {
                            masterRadio.checked = masterRadio === anyMasterRadio;
                        });
                    }
                });
            });

            // Date/Master/Location change - reload slots
            document.getElementById('date').addEventListener('change', () => {
                if (currentStep === 4) {
                    loadAvailableSlots();
                }
            });

            // Используем делегирование событий для динамически добавляемых элементов
            document.addEventListener('change', (e) => {
                if (e.target.matches('.master-radio')) {
                    if (currentStep === 4) {
                        loadAvailableSlots();
                    }
                }
                if (e.target.matches('.location-radio')) {
                    if (currentStep === 4) {
                        loadAvailableSlots();
                    }
                }
            });

            // Collapsible дополнительная информация
            const toggleBtnCreate = document.getElementById('additional-info-toggle-create');
            const contentCreate = document.getElementById('additional-info-content-create');
            const iconCreate = document.getElementById('additional-info-icon-create');

            if (toggleBtnCreate && contentCreate && iconCreate) {
                toggleBtnCreate.addEventListener('click', function() {
                    const isHidden = contentCreate.classList.contains('hidden');

                    if (isHidden) {
                        contentCreate.classList.remove('hidden');
                        iconCreate.style.transform = 'rotate(180deg)';
                    } else {
                        contentCreate.classList.add('hidden');
                        iconCreate.style.transform = 'rotate(0deg)';
                    }
                });
            }

            // Initialize
            showStep(1);
        });
    </script>

    <style>
        /* Prevent horizontal scroll */
        html, body {
            overflow-x: hidden;
            max-width: 100%;
        }
        
        * {
            box-sizing: border-box;
        }
        
        /* Step Indicator Styles */
        .step-indicator {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.5rem;
            flex: 1;
            min-width: 0;
            flex-shrink: 1;
        }

        .step-number {
            width: 2rem;
            height: 2rem;
            border-radius: 50%;
            background-color: #e2e8f0;
            color: #64748b;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.875rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .dark .step-number {
            background-color: #334155;
            color: #94a3b8;
        }

        .step-indicator.active .step-number {
            background-color: #6366f1;
            color: white;
        }

        .step-indicator.completed .step-number {
            background-color: #10b981;
            color: white;
        }

        .step-label {
            font-size: 0.75rem;
            color: #64748b;
            text-align: center;
            font-weight: 500;
        }

        .dark .step-label {
            color: #94a3b8;
        }

        .step-indicator.active .step-label {
            color: #6366f1;
            font-weight: 600;
        }

        .step-indicator.completed .step-label {
            color: #10b981;
            font-weight: 600;
        }

        .step-line {
            height: 2px;
            flex: 1;
            background-color: #e2e8f0;
            margin: 0 0.5rem;
            margin-top: -1rem;
            min-width: 0;
            flex-shrink: 1;
        }

        .dark .step-line {
            background-color: #334155;
        }

        .step-indicator.completed + .step-line {
            background-color: #10b981;
        }

        @media (max-width: 640px) {
            .step-label {
                display: none;
            }
            .step-line {
                margin: 0 0.25rem;
            }
            .step-indicator {
                flex-shrink: 1;
                min-width: 0;
            }
        }
    </style>
</body>
</html>
