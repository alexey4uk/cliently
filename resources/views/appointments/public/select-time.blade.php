@extends('appointments.public.layout')

@section('title', 'Выбор времени')

@section('content')
    <!-- Контейнер без лишних внешних отступов -->
    <div class="flex-1 overflow-x-hidden">
        <div class="max-w-3xl lg:max-w-3xl mx-auto sm:px-0">

            <x-breadcrumbs-public-book :business="$business" currentStep="time" :location="$location" :service="$service"
                :master="$master" />

            <form method="POST" action="{{ route('public.appointments.store', $business->slug) }}" id="appointment-form"
                class="space-y-6">
                @csrf
                <input type="hidden" name="location_id" value="{{ $location->id }}">
                <input type="hidden" name="service_id" value="{{ $service->id }}">
                <input type="hidden" name="master_id" value="{{ $master->id }}">
                <input type="hidden" name="date" value="{{ $date }}" id="selected-date-input">
                <input type="hidden" name="time" value="" id="selected-time-input">

                <!-- ВЫБОР ДАТЫ -->
                <div
                    class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-5 shadow-sm">
                    <div class="flex items-center gap-3 mb-5">
                        <div
                            class="w-10 h-10 rounded-2xl bg-indigo-50 dark:bg-indigo-500/10 flex items-center justify-center">
                            <i class="fa-solid fa-calendar-day text-indigo-600 dark:text-indigo-400"></i>
                        </div>
                        <h2 class="text-lg font-bold text-slate-900 dark:text-white">Дата визита</h2>
                    </div>

                    @php $selectedDateCarbon = \Carbon\Carbon::parse($date); @endphp
                    <button type="button" id="toggle-date-selector-btn"
                        class="w-full flex items-center justify-between p-4 bg-slate-50 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-800 rounded-2xl transition-all hover:bg-slate-100 dark:hover:bg-slate-800 group outline-none">
                        <div class="flex items-center gap-4 text-left">
                            <div
                                class="flex flex-col items-center justify-center w-12 h-12 bg-white dark:bg-slate-950 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 group-hover:border-indigo-500 transition-colors">
                                <span
                                    class="text-[10px] uppercase font-black text-indigo-600 dark:text-indigo-400 leading-none mb-1">{{ $selectedDateCarbon->locale('ru')->isoFormat('MMM') }}</span>
                                <span
                                    class="text-xl font-black text-slate-900 dark:text-white leading-none">{{ $selectedDateCarbon->day }}</span>
                            </div>
                            <div>
                                <div class="text-base font-bold text-slate-900 dark:text-white leading-tight">
                                    {{ $selectedDateCarbon->isToday() ? 'Сегодня, ' : '' }}{{ $selectedDateCarbon->locale('ru')->isoFormat('dddd') }}
                                </div>
                                <div class="text-xs text-slate-500 dark:text-slate-400 mt-1">Нажмите, чтобы изменить дату
                                </div>
                            </div>
                        </div>
                        <i class="fa-solid fa-chevron-down text-slate-400 group-hover:text-indigo-500 transition-all duration-300"
                            id="date-selector-icon"></i>
                    </button>

                    <!-- Календарь -->
                    <div id="calendar-container" class="hidden mt-6 pt-6 border-t border-slate-100 dark:border-slate-800">
                        <div class="flex items-center justify-between mb-4 px-2">
                            <button type="button" id="prev-month"
                                class="w-10 h-10 flex items-center justify-center rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-600 dark:text-slate-400"><i
                                    class="fa-solid fa-chevron-left"></i></button>
                            <h3 id="current-month-year"
                                class="text-sm font-black uppercase tracking-widest text-slate-900 dark:text-white"></h3>
                            <button type="button" id="next-month"
                                class="w-10 h-10 flex items-center justify-center rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-600 dark:text-slate-400"><i
                                    class="fa-solid fa-chevron-right"></i></button>
                        </div>
                        <div
                            class="grid grid-cols-7 gap-1 text-center text-[11px] font-bold text-slate-400 dark:text-slate-500 uppercase mb-2">
                            <div>Пн</div>
                            <div>Вт</div>
                            <div>Ср</div>
                            <div>Чт</div>
                            <div>Пт</div>
                            <div class="text-indigo-500">Сб</div>
                            <div class="text-indigo-500">Вс</div>
                        </div>
                        <div id="calendar-grid" class="grid grid-cols-7 gap-1"></div>
                    </div>
                </div>

                <!-- ВЫБОР ВРЕМЕНИ -->
                <div
                    class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-5 shadow-sm">
                    <div class="flex items-center gap-3 mb-6 px-1">
                        <div
                            class="w-10 h-10 rounded-2xl bg-amber-50 dark:bg-amber-500/10 flex items-center justify-center">
                            <i class="fa-solid fa-clock text-amber-600 dark:text-amber-400"></i>
                        </div>
                        <h2 class="text-lg font-bold text-slate-900 dark:text-white leading-tight">Доступное время</h2>
                    </div>

                    <div id="time-slots-container">
                        @if (isset($availableSlots) && count($availableSlots) > 0)
                            <div
                                class="grid grid-cols-4 sm:grid-cols-5 md:grid-cols-6 lg:grid-cols-4 gap-2 sm:gap-3 w-full">
                                @foreach ($availableSlots as $slot)
                                    <button type="button" data-time="{{ $slot }}"
                                        class="time-slot-btn w-full flex items-center justify-center py-3.5 bg-white dark:bg-slate-800/40 border border-slate-200 dark:border-slate-700 rounded-2xl text-sm font-bold text-slate-700 dark:text-slate-200 hover:border-indigo-500 dark:hover:border-indigo-400 transition-all active:scale-95 shadow-sm">
                                        {{ $slot }}
                                    </button>
                                @endforeach
                            </div>
                        @else
                            <div
                                class="text-center w-full py-10 bg-slate-50 dark:bg-slate-800/20 rounded-2xl border border-dashed border-slate-200 dark:border-slate-800 text-slate-500">
                                На этот день свободных окон нет
                            </div>
                        @endif
                    </div>
                </div>

                <!-- ДАННЫЕ -->
                <div id="appointment-details"
                    class="hidden bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-5 shadow-2xl shadow-indigo-500/10">
                    <div class="flex items-center gap-3 mb-6">
                        <div
                            class="w-10 h-10 rounded-2xl bg-emerald-50 dark:bg-emerald-500/10 flex items-center justify-center">
                            <i class="fa-solid fa-address-card text-emerald-600 dark:text-emerald-400"></i>
                        </div>
                        <h2 class="text-lg text-slate-900 dark:text-white">Ваши данные</h2>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1.5 ml-1">Имя *</label>
                            <input type="text" name="first_name" required
                                class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-700 rounded-2xl text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500 outline-none transition-all">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1.5 ml-1">Фамилия</label>
                            <input type="text" name="last_name"
                                class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-700 rounded-2xl text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500 outline-none transition-all">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1.5 ml-1">Телефон
                                *</label>
                            <input type="tel" id="phone" name="phone" required
                                class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-700 rounded-2xl text-slate-900 dark:text-white text-lg font-bold outline-none focus:ring-2 focus:ring-indigo-500">
                        </div>
                    </div>

                    <div
                        class="mt-8 p-5 bg-indigo-600 dark:bg-indigo-500 rounded-2xl flex items-center justify-between text-white shadow-lg">
                        <div class="min-w-0 pr-4">
                            <div class="text-2xl font-black leading-none" id="summary-time">--:--</div>
                            <div class="text-[10px] font-bold uppercase opacity-90 mt-1.5" id="summary-date">Время не
                                выбрано</div>
                        </div>
                        <button type="submit" id="submit-btn"
                            class="shrink-0 px-8 py-3.5 bg-white text-indigo-600 rounded-xl font-black uppercase text-[10px] tracking-widest hover:bg-indigo-50 active:scale-95 transition-all">
                            Записаться
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <style>
        .time-slot-btn.selected {
            @apply bg-indigo-600 border-indigo-600 text-white shadow-md !important;
        }

        .dark .time-slot-btn.selected {
            @apply bg-indigo-500 border-indigo-500 !important;
        }

        #calendar-grid button.selected-day {
            @apply bg-indigo-600 text-white font-black rounded-xl !important;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const timeSlots = document.querySelectorAll('.time-slot-btn');
            const detailsBlock = document.getElementById('appointment-details');
            const timeInput = document.getElementById('selected-time-input');
            const timeDisplay = document.getElementById('summary-time');
            const dateText = document.getElementById('summary-date');
            const dateInput = document.getElementById('selected-date-input');
            const form = document.getElementById('appointment-form');

            // Даты со слотами с сегодня до конца месяца
            const datesWithSlots = @json($datesWithSlots);

            // 1. Выбор времени
            timeSlots.forEach(btn => {
                btn.addEventListener('click', function() {
                    timeSlots.forEach(b => b.classList.remove('selected'));
                    this.classList.add('selected');

                    const time = this.dataset.time;
                    timeInput.value = time;
                    timeDisplay.innerText = time;

                    const d = new Date(dateInput.value);
                    dateText.innerText = d.toLocaleDateString('ru-RU', {
                        day: 'numeric',
                        month: 'long'
                    });

                    detailsBlock.classList.remove('hidden');
                    setTimeout(() => detailsBlock.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    }), 100);
                });
            });

            // 2. Универсальный телефон
            const phoneInput = document.getElementById('phone');
            if (phoneInput) {
                phoneInput.addEventListener('input', function() {
                    let v = this.value;
                    if (v.length > 0 && v[0] !== '+') v = '+' + v;
                    this.value = v.replace(/[^\d+]/g, '');
                });
            }

            // 3. Календарь
            const calendarGrid = document.getElementById('calendar-grid');
            const monthLabel = document.getElementById('current-month-year');
            const toggleBtn = document.getElementById('toggle-date-selector-btn');
            const container = document.getElementById('calendar-container');

            // Дата текущей страницы
            const currentPageDate = new Date('{{ $date }}');
            currentPageDate.setHours(0, 0, 0, 0);

            // Есть ли слоты на текущую дату
            const currentDateHasSlots = {{ count($availableSlots) > 0 ? 'true' : 'false' }};

            let calendarDate = new Date(currentPageDate);

            function updateCalendar() {
                const months = ['Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь',
                    'Октябрь', 'Ноябрь', 'Декабрь'
                ];
                monthLabel.textContent = `${months[calendarDate.getMonth()]} ${calendarDate.getFullYear()}`;
                calendarGrid.innerHTML = '';

                const firstDayOfMonth = new Date(calendarDate.getFullYear(), calendarDate.getMonth(), 1);
                const dayOfWeek = firstDayOfMonth.getDay();
                const daysToSubtract = dayOfWeek === 0 ? 6 : dayOfWeek - 1;

                const cellDate = new Date(firstDayOfMonth);
                cellDate.setDate(firstDayOfMonth.getDate() - daysToSubtract);
                cellDate.setHours(0, 0, 0, 0);

                const today = new Date();
                today.setHours(0, 0, 0, 0);

                for (let i = 0; i < 42; i++) {
                    const year = cellDate.getFullYear();
                    const month = String(cellDate.getMonth() + 1).padStart(2, '0');
                    const day = String(cellDate.getDate()).padStart(2, '0');
                    const cellDateString = `${year}-${month}-${day}`;

                    const btn = document.createElement('button');
                    btn.type = 'button';

                    // Обертка для текста, чтобы добавить точку под числом у "сегодня"
                    btn.innerHTML = `<span class="relative z-10">${cellDate.getDate()}</span>`;

                    const isCurrentMonth = cellDate.getMonth() === calendarDate.getMonth();
                    const isPast = cellDate < today;
                    const isToday = cellDate.getTime() === today.getTime();
                    const isSelected = cellDate.getTime() === currentPageDate.getTime();
                    const hasSlots = datesWithSlots[cellDateString] || false;

                    let classList = ['relative', 'py-3', 'text-sm', 'rounded-xl', 'transition-all', 'flex',
                        'flex-col', 'items-center', 'justify-center', 'font-bold', 'w-full', 'h-full',
                        'border-2'
                    ];

                    // 1. Стилизация выбранного дня (Рамка)
                    if (isSelected) {
                        classList.push('border-indigo-500', 'bg-indigo-50/50', 'dark:bg-indigo-900/30');
                    } else {
                        classList.push('border-transparent');
                    }

                    // 2. Стилизация сегодня (Цвет и точка)
                    if (isToday) {
                        classList.push('text-indigo-600', 'dark:text-indigo-400');
                        // Добавляем точку под числом
                        const dot = document.createElement('div');
                        dot.className = 'absolute bottom-1 w-1 h-1 bg-indigo-600 dark:bg-indigo-400 rounded-full';
                        btn.appendChild(dot);
                    }

                    // 3. Основная логика доступности
                    if (!isCurrentMonth) {
                        classList.push('text-slate-300', 'dark:text-slate-700', 'opacity-40', 'cursor-default');
                        btn.disabled = true;
                    } else if (isPast && !isToday) {
                        classList.push('text-slate-400', 'dark:text-slate-600', 'opacity-40', 'cursor-not-allowed');
                        btn.disabled = true;
                    } else if (!hasSlots) {
                        classList.push('text-slate-400', 'dark:text-slate-500', 'opacity-60', 'cursor-not-allowed');
                        btn.disabled = true;
                    } else {
                        // Доступный для клика день
                        if (!isToday) classList.push('text-slate-700', 'dark:text-slate-200');
                        classList.push('hover:bg-indigo-100', 'dark:hover:bg-indigo-800/40', 'cursor-pointer');

                        btn.onclick = () => {
                            const url = new URL(window.location.href);
                            url.searchParams.set('date', cellDateString);
                            window.location.href = url.toString();
                        };
                    }

                    btn.className = classList.join(' ');
                    calendarGrid.appendChild(btn);
                    cellDate.setDate(cellDate.getDate() + 1);
                }
            }



            toggleBtn?.addEventListener('click', () => {
                container.classList.toggle('hidden');
                document.getElementById('date-selector-icon').classList.toggle('rotate-180');
                if (!container.classList.contains('hidden')) {
                    updateCalendar();
                }
            });

            document.getElementById('prev-month')?.addEventListener('click', () => {
                calendarDate.setMonth(calendarDate.getMonth() - 1);
                updateCalendar();
            });

            document.getElementById('next-month')?.addEventListener('click', () => {
                calendarDate.setMonth(calendarDate.getMonth() + 1);
                updateCalendar();
            });

            // 4. Валидация и Отправка
            form.onsubmit = function(e) {
                if (!timeInput.value) {
                    e.preventDefault();
                    alert('Выберите время');
                    return false;
                }
                const btn = document.getElementById('submit-btn');
                btn.disabled = true;
                btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>';
                return true;
            };

            if (!container.classList.contains('hidden')) {
                updateCalendar();
            }
        });
    </script>
@endsection
