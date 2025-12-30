@extends('appointments.public.layout')

@section('title', 'Выбор времени')

@section('content')
<div class="max-w-4xl lg:max-w-4xl mx-auto">
    <!-- Кнопка назад -->
    <div class="mb-4 sm:mb-5 lg:mb-4">
        <a href="{{ route('public.appointments.select-service', ['slug' => $business->slug, 'locationId' => $location->id, 'serviceId' => $service->id]) }}" 
           class="inline-flex items-center gap-2 px-4 py-2 text-sm sm:text-base lg:text-sm text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-200 border border-slate-200 dark:border-slate-700 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors duration-200">
            <i class="fa-solid fa-arrow-left text-xs lg:text-[10px]"></i>
            <span>Вернуться к мастерам</span>
        </a>
    </div>

    <!-- Заголовок -->
    <div class="mb-5 sm:mb-6 lg:mb-5">
        <h1 class="text-2xl sm:text-3xl lg:text-2xl font-bold text-slate-900 dark:text-white mb-2 lg:mb-1.5 leading-tight">
            Выберите время
        </h1>
        <p class="text-sm sm:text-base text-slate-600 dark:text-slate-400">
            {{ $location->name }} • {{ $service->name }} • {{ $master->first_name }} {{ $master->last_name }}
        </p>
    </div>

    <form method="POST" action="{{ route('public.appointments.store', $business->slug) }}" class="space-y-4 sm:space-y-5 lg:space-y-4" id="appointment-form">
        @csrf
        <input type="hidden" name="location_id" value="{{ $location->id }}">
        <input type="hidden" name="service_id" value="{{ $service->id }}">
        <input type="hidden" name="master_id" value="{{ $master->id }}">
        <input type="hidden" name="date" value="{{ $date }}" id="selected-date-input">

        <!-- Выбор даты -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl sm:rounded-3xl lg:rounded-2xl border border-slate-200 dark:border-slate-800 p-4 sm:p-5 lg:p-4">
            <div class="flex items-center gap-3 mb-4 lg:mb-3">
                <div class="w-8 h-8 lg:w-7 lg:h-7 rounded-xl bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center">
                    <i class="fa-solid fa-calendar text-indigo-600 dark:text-indigo-400 text-sm lg:text-xs"></i>
                </div>
                <h2 class="text-lg sm:text-xl lg:text-base font-bold text-slate-900 dark:text-white">
                    Выберите дату
                </h2>
            </div>

            <!-- Упрощенный выбор даты -->
            <div class="space-y-4">
                    @php
                        $selectedDateCarbon = \Carbon\Carbon::parse($date);
                        $currentYear = \Carbon\Carbon::now()->year;
                        $showYear = $selectedDateCarbon->year !== $currentYear;
                    @endphp

                <!-- Текущая выбранная дата -->
                <div class="text-center">
                    <div class="inline-flex items-center gap-2.5 px-4 py-3 bg-slate-50 dark:bg-slate-800 rounded-xl">
                        <i class="fa-solid fa-calendar-check text-indigo-600 dark:text-indigo-400 text-base"></i>
                        <div class="text-center">
                            <div class="text-base sm:text-lg lg:text-base font-bold text-slate-900 dark:text-white">
                                {{ $selectedDateCarbon->locale('ru')->isoFormat('dddd, D MMMM') }}
                            </div>
                            @if($showYear)
                                <div class="text-xs text-slate-600 dark:text-slate-400">
                                    {{ $selectedDateCarbon->year }}
                                </div>
                            @endif
                        </div>
                        </div>
                        </div>

                <!-- Кнопка выбора другой даты -->
                <div class="text-center">
                    <button type="button" id="toggle-date-selector-btn"
                            class="inline-flex items-center gap-2 px-4 py-2 text-sm sm:text-base lg:text-sm font-medium text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-900/20 border border-indigo-200 dark:border-indigo-700/50 rounded-xl hover:bg-indigo-100 dark:hover:bg-indigo-500/30 transition-colors duration-200">
                        <span>Выбрать другую дату</span>
                                <i class="fa-solid fa-chevron-down text-xs transition-transform duration-200" id="date-selector-icon"></i>
                            </button>
                    </div>
                    
                <!-- Скрытый календарь -->
                <div id="calendar-container" class="hidden space-y-4">
                    <!-- Кнопки навигации -->
                    <div class="flex items-center justify-between">
                        <button type="button" id="prev-month" class="p-2 text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-200 transition-colors">
                            <i class="fa-solid fa-chevron-left"></i>
                            </button>
                        <h3 id="current-month-year" class="text-lg font-semibold text-slate-900 dark:text-white"></h3>
                        <button type="button" id="next-month" class="p-2 text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-200 transition-colors">
                            <i class="fa-solid fa-chevron-right"></i>
                            </button>
                        </div>

                    <!-- Дни недели -->
                    <div class="grid grid-cols-7 gap-1 text-center text-sm font-medium text-slate-600 dark:text-slate-400">
                        @foreach(['Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб', 'Вс'] as $day)
                            <div class="py-2">{{ $day }}</div>
                        @endforeach
                    </div>
                    
                    <!-- Календарь -->
                    <div id="calendar-grid" class="grid grid-cols-7 gap-1">
                        <!-- Будет заполнено JavaScript -->
                        </div>
                        </div>
                    </div>
                </div>
                
        <!-- Выбор времени -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl sm:rounded-3xl lg:rounded-2xl border border-slate-200 dark:border-slate-800 p-4 sm:p-5 lg:p-4">
            <div class="flex items-center gap-3 mb-4 lg:mb-3">
                <div class="w-8 h-8 lg:w-7 lg:h-7 rounded-xl bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center">
                    <i class="fa-solid fa-clock text-indigo-600 dark:text-indigo-400 text-sm lg:text-xs"></i>
                        </div>
                <h2 class="text-lg sm:text-xl lg:text-base font-bold text-slate-900 dark:text-white">
                    Выберите время
                </h2>
                </div>

            <div id="time-slots-container" class="space-y-4">
                @if(isset($availableSlots) && count($availableSlots) > 0)
                    @php
                        // Группируем слоты по времени суток
                        $morningSlots = [];
                        $afternoonSlots = [];
                        $eveningSlots = [];

                        foreach($availableSlots as $slot) {
                            $hour = (int)explode(':', $slot)[0];
                            if ($hour >= 6 && $hour < 12) {
                                $morningSlots[] = $slot;
                            } elseif ($hour >= 12 && $hour < 18) {
                                $afternoonSlots[] = $slot;
                            } else {
                                $eveningSlots[] = $slot;
                            }
                        }

                        $slotGroups = [
                            ['title' => 'Утро', 'icon' => 'fa-sun', 'bgClass' => 'bg-amber-100 dark:bg-amber-900/30', 'textClass' => 'text-amber-600 dark:text-amber-400', 'hoverClass' => 'hover:border-amber-500 dark:hover:border-amber-500 hover:bg-amber-50 dark:hover:bg-amber-900/20', 'slots' => $morningSlots],
                            ['title' => 'День', 'icon' => 'fa-sun', 'bgClass' => 'bg-orange-100 dark:bg-orange-900/30', 'textClass' => 'text-orange-600 dark:text-orange-400', 'hoverClass' => 'hover:border-orange-500 dark:hover:border-orange-500 hover:bg-orange-50 dark:hover:bg-orange-900/20', 'slots' => $afternoonSlots],
                            ['title' => 'Вечер', 'icon' => 'fa-moon', 'bgClass' => 'bg-indigo-100 dark:bg-indigo-900/30', 'textClass' => 'text-indigo-600 dark:text-indigo-400', 'hoverClass' => 'hover:border-indigo-500 dark:hover:border-indigo-500 hover:bg-indigo-50 dark:hover:bg-indigo-900/20', 'slots' => $eveningSlots],
                        ];
                        @endphp

                    <!-- Группы слотов -->
                    <div class="space-y-4">
                        @foreach($slotGroups as $group)
                            @if(count($group['slots']) > 0)
                                <div class="space-y-2.5">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-6 h-6 rounded-lg {{ $group['bgClass'] }} flex items-center justify-center">
                                            <i class="fa-solid {{ $group['icon'] }} {{ $group['textClass'] }} text-xs"></i>
                                </div>
                                        <h3 class="text-base font-semibold text-slate-900 dark:text-white">
                                            {{ $group['title'] }}
                                        </h3>
                                        <span class="text-xs text-slate-500 dark:text-slate-400 bg-slate-100 dark:bg-slate-800 px-2 py-0.5 rounded-full">
                                            {{ count($group['slots']) }}
                                        </span>
                                </div>
                                    <div class="flex flex-wrap gap-1.5 sm:gap-2 justify-start">
                                        @foreach($group['slots'] as $slot)
                                            <button type="button"
                                                    class="time-slot-btn flex-shrink-0 px-3 py-1.5 sm:px-3.5 sm:py-2 text-center border border-slate-200 dark:border-slate-700 rounded-lg {{ $group['hoverClass'] }} transition-all duration-200"
                                                    data-time="{{ $slot }}">
                                                <span class="text-xs font-medium text-slate-900 dark:text-white">
                                                    {{ $slot }}
                                                </span>
                            </button>
                                        @endforeach
                            </div>
                        </div>
                            @endif
                            @endforeach
                        </div>
                    @else
                    <div class="text-center py-8">
                        <div class="w-16 h-16 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center mx-auto mb-4">
                            <i class="fa-solid fa-clock text-slate-400 dark:text-slate-500 text-2xl"></i>
                        </div>
                        <p class="text-sm text-slate-600 dark:text-slate-400">
                            На выбранную дату нет доступных слотов времени
                        </p>
                        </div>
                    @endif
                </div>
            </div>

        <!-- Форма записи -->
        <div id="appointment-details" class="hidden bg-white dark:bg-slate-900 rounded-2xl sm:rounded-3xl lg:rounded-2xl border border-slate-200 dark:border-slate-800 p-4 sm:p-5 lg:p-4">
            <div class="flex items-center gap-3 mb-4 lg:mb-3">
                <div class="w-8 h-8 lg:w-7 lg:h-7 rounded-xl bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center">
                    <i class="fa-solid fa-user-check text-emerald-600 dark:text-emerald-400 text-sm lg:text-xs"></i>
                    </div>
                <h2 class="text-lg sm:text-xl lg:text-base font-bold text-slate-900 dark:text-white">
                    Ваши данные
                </h2>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
                <!-- Имя -->
                    <div>
                    <label for="first_name" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">
                        Имя *
                        </label>
                    <input type="text" id="first_name" name="first_name"
                           class="w-full px-3 py-2 border border-slate-300 dark:border-slate-600 rounded-xl bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                           required>
                    </div>

                <!-- Фамилия -->
                    <div>
                    <label for="last_name" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">
                        Фамилия *
                        </label>
                    <input type="text" id="last_name" name="last_name"
                           class="w-full px-3 py-2 border border-slate-300 dark:border-slate-600 rounded-xl bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                           required>
            </div>

                <!-- Телефон -->
                <div class="sm:col-span-2">
                    <label for="phone" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">
                        Телефон *
                    </label>
                    <input type="tel" id="phone" name="phone"
                           class="w-full px-3 py-2 border border-slate-300 dark:border-slate-600 rounded-xl bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                           placeholder="+375 (__) ___-__-__"
                           required>
</div>

                <!-- Комментарий -->
                <div class="sm:col-span-2">
                    <label for="comment" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">
                        Комментарий (необязательно)
                    </label>
                    <textarea id="comment" name="comment" rows="3"
                              class="w-full px-3 py-2 border border-slate-300 dark:border-slate-600 rounded-xl bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors resize-none"
                              placeholder="Дополнительные пожелания..."></textarea>
        </div>
                </div>

            <!-- Выбранное время -->
            <div class="mt-4 pt-4 border-t border-slate-200 dark:border-slate-700">
                <div class="flex items-center justify-between p-3 bg-slate-50 dark:bg-slate-800 rounded-xl">
                    <div class="text-sm text-slate-600 dark:text-slate-400">
                        Выбранное время:
                </div>
                    <div class="text-sm font-medium text-slate-900 dark:text-white" id="selected-time-display">
                        Не выбрано
                    </div>
                </div>
                </div>

            <!-- Кнопка записи -->
            <div class="mt-4 text-center">
                <button type="submit"
                        class="inline-flex items-center gap-2 px-6 py-3 text-sm sm:text-base font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl transition-colors duration-200 shadow-lg hover:shadow-xl">
                    <i class="fa-solid fa-calendar-check"></i>
                    <span>Записаться</span>
                    </button>
                </div>
            </div>
    </form>
</div>

<script>
        document.addEventListener('DOMContentLoaded', function() {
    const timeSlotButtons = document.querySelectorAll('.time-slot-btn');
    const appointmentDetails = document.getElementById('appointment-details');
    const selectedTimeDisplay = document.getElementById('selected-time-display');
    const selectedDateInput = document.getElementById('selected-date-input');

    let selectedTime = null;
    let currentDate = new Date('{{ $date }}');

    // Обработка выбора времени
    timeSlotButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Убираем выделение с других кнопок
            timeSlotButtons.forEach(btn => {
                // Убираем все возможные классы выделения (включая bg-indigo-600)
                btn.classList.remove('bg-indigo-500', 'bg-indigo-600', 'bg-amber-500', 'bg-orange-500', 'text-white', 'border-indigo-500', 'border-indigo-600', 'border-amber-500', 'border-orange-500');

                // Восстанавливаем hover эффекты
                if (!btn.classList.contains('hover:border-amber-500') &&
                    !btn.classList.contains('hover:border-orange-500') &&
                    !btn.classList.contains('hover:border-indigo-500')) {
                    btn.classList.add('hover:border-indigo-500');
                }
            });

            // Выделяем выбранную кнопку
            this.classList.add('bg-indigo-600', 'text-white', 'border-indigo-600');
            this.classList.remove('hover:border-amber-500', 'hover:border-orange-500', 'hover:border-indigo-500');

            // Сохраняем выбранное время
            selectedTime = this.dataset.time;

            // Обновляем отображение
            const dateStr = currentDate.toLocaleDateString('ru-RU', {
                day: 'numeric',
                month: 'long',
                year: currentDate.getFullYear() !== new Date().getFullYear() ? 'numeric' : undefined
            });
            selectedTimeDisplay.textContent = `${selectedTime}, ${dateStr}`;

            // Показываем форму
            appointmentDetails.classList.remove('hidden');
            appointmentDetails.scrollIntoView({ behavior: 'smooth' });
        });
    });

    // Форматирование телефона
    const phoneInput = document.getElementById('phone');
    if (phoneInput) {
        phoneInput.addEventListener('focus', function() {
            if (!this.value) {
                this.value = '+375';
            }
        });

        phoneInput.addEventListener('input', function(e) {
            let value = this.value.replace(/[^\d+]/g, '');
            if (value.startsWith('375')) {
                value = '+' + value;
            } else if (!value.startsWith('+375') && value.length > 0) {
                value = '+375' + value.replace(/\+/g, '');
            }
            if (value.length > 13) {
                value = value.substring(0, 13);
            }
            this.value = value;
        });
    }

    // Функциональность календаря
    const toggleDateBtn = document.getElementById('toggle-date-selector-btn');
    const calendarContainer = document.getElementById('calendar-container');
    const dateSelectorIcon = document.getElementById('date-selector-icon');
    const prevMonthBtn = document.getElementById('prev-month');
    const nextMonthBtn = document.getElementById('next-month');
    const currentMonthYearEl = document.getElementById('current-month-year');
    const calendarGrid = document.getElementById('calendar-grid');

    let calendarDate = new Date(currentDate);

    function updateCalendar() {
        // Обновляем заголовок месяца
        const monthNames = [
            'Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь',
            'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'
        ];
        currentMonthYearEl.textContent = `${monthNames[calendarDate.getMonth()]} ${calendarDate.getFullYear()}`;

        // Очищаем календарь
        calendarGrid.innerHTML = '';

        // Получаем первый день месяца и последний день
        const firstDay = new Date(calendarDate.getFullYear(), calendarDate.getMonth(), 1);
        const lastDay = new Date(calendarDate.getFullYear(), calendarDate.getMonth() + 1, 0);
            const startDate = new Date(firstDay);
        startDate.setDate(startDate.getDate() - firstDay.getDay() + 1); // Начинаем с понедельника

        // Создаем ячейки календаря
        const today = new Date();
        const todayLocal = new Date(today.getFullYear(), today.getMonth(), today.getDate());

        for (let i = 0; i < 42; i++) { // 6 недель * 7 дней
            const cellDate = new Date(startDate);
            cellDate.setDate(startDate.getDate() + i);

            const dayEl = document.createElement('button');
            dayEl.className = 'p-1.5 text-sm hover:bg-indigo-50 dark:hover:bg-indigo-900/20 rounded-lg transition-colors';
            dayEl.textContent = cellDate.getDate();

            // Определяем стиль для разных типов дней
            if (cellDate.getMonth() !== calendarDate.getMonth()) {
                dayEl.classList.add('text-slate-400', 'dark:text-slate-600');
            } else if (cellDate.getTime() === currentDate.getTime()) {
                dayEl.classList.add('bg-indigo-500', 'text-white', 'hover:bg-indigo-600');
            } else if (cellDate.getFullYear() === todayLocal.getFullYear() &&
                       cellDate.getMonth() === todayLocal.getMonth() &&
                       cellDate.getDate() === todayLocal.getDate()) {
                dayEl.classList.add('bg-emerald-500', 'text-white', 'hover:bg-emerald-600');
                    } else {
                dayEl.classList.add('text-slate-900', 'dark:text-slate-100');
            }

            // Блокируем прошедшие дни
            if (cellDate.getFullYear() < todayLocal.getFullYear() ||
                (cellDate.getFullYear() === todayLocal.getFullYear() && cellDate.getMonth() < todayLocal.getMonth()) ||
                (cellDate.getFullYear() === todayLocal.getFullYear() && cellDate.getMonth() === todayLocal.getMonth() && cellDate.getDate() < todayLocal.getDate())) {
                dayEl.disabled = true;
                dayEl.classList.add('opacity-50', 'cursor-not-allowed');
                    } else {
                dayEl.addEventListener('click', function() {
                    currentDate = new Date(cellDate.getFullYear(), cellDate.getMonth(), cellDate.getDate());
                    selectedDateInput.value = currentDate.getFullYear() + '-' +
                        String(currentDate.getMonth() + 1).padStart(2, '0') + '-' +
                        String(currentDate.getDate()).padStart(2, '0');

                    // Обновляем отображение выбранной даты
                    const dateDisplay = document.querySelector('#selected-date-display');
                    if (dateDisplay && selectedTime) {
                        const dateStr = currentDate.toLocaleDateString('ru-RU', {
                            day: 'numeric',
                            month: 'long',
                            year: currentDate.getFullYear() !== new Date().getFullYear() ? 'numeric' : undefined
                        });
                        dateDisplay.textContent = `${selectedTime}, ${dateStr}`;
                    }

                    // Перезагружаем страницу с новой датой
                    window.location.href = `?date=${selectedDateInput.value}`;
                });
            }

            calendarGrid.appendChild(dayEl);
        }
    }

    // Обработчики кнопок календаря
    if (toggleDateBtn && calendarContainer) {
        toggleDateBtn.addEventListener('click', function() {
            calendarContainer.classList.toggle('hidden');
            dateSelectorIcon.classList.toggle('rotate-180');

            if (!calendarContainer.classList.contains('hidden')) {
                updateCalendar();
            }
        });
    }

    if (prevMonthBtn) {
        prevMonthBtn.addEventListener('click', function() {
            calendarDate.setMonth(calendarDate.getMonth() - 1);
            updateCalendar();
        });
    }

    if (nextMonthBtn) {
        nextMonthBtn.addEventListener('click', function() {
            calendarDate.setMonth(calendarDate.getMonth() + 1);
            updateCalendar();
        });
    }
});
</script>
@endsection
