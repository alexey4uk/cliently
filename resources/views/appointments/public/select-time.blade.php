@extends('appointments.public.layout')

@section('title', 'Выбор времени')

@section('content')
<div class="space-y-4">
    <!-- Кнопка "Назад" -->
    <div>
        <a href="{{ route('public.appointments.select-service', ['slug' => $business->slug, 'locationId' => $location->id, 'serviceId' => $service->id]) }}" 
           class="inline-flex items-center text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 transition-colors">
            <i class="fa-solid fa-arrow-left mr-2"></i>Назад
        </a>
    </div>

    <!-- Карточка: Услуга и Мастер -->
    <div class="glass-card rounded-xl p-4 md:p-6">
        <div class="flex items-start gap-4">
            <div class="flex-1">
                <div class="text-xs text-slate-500 dark:text-slate-400 mb-1">Услуга</div>
                <div class="font-semibold text-slate-900 dark:text-white text-base md:text-sm mb-3">{{ $service->name }}</div>
                <div class="text-xs text-slate-500 dark:text-slate-400 mb-1">Мастер</div>
                <div class="font-medium text-slate-700 dark:text-slate-300 text-sm">{{ $master->first_name }} {{ $master->last_name }}</div>
            </div>
            @if($service->duration)
                <div class="text-right">
                    <div class="text-xs text-slate-500 dark:text-slate-400">Длительность</div>
                    <div class="font-semibold text-indigo-600 dark:text-indigo-400 text-sm">{{ $service->duration }} мин</div>
                </div>
            @endif
        </div>
    </div>

    @if(count($availableSlots) > 0)
        <form method="POST" action="{{ route('public.appointments.store', $business->slug) }}">
            @csrf
            <input type="hidden" name="location_id" value="{{ $location->id }}">
            <input type="hidden" name="service_id" value="{{ $service->id }}">
            <input type="hidden" name="master_id" value="{{ $master->id }}">
            <input type="hidden" name="date" value="{{ $date }}" id="selected-date-input">

            <!-- Выбор даты: Горизонтальный скролл недели -->
            <div class="glass-card rounded-xl p-4 md:p-6 mb-4">
                <div class="flex items-center justify-between mb-3">
                    <label class="text-sm font-semibold text-slate-700 dark:text-slate-300 flex items-center gap-2">
                        <i class="fa-solid fa-calendar-alt text-indigo-600 dark:text-indigo-400"></i>
                        Дата
                    </label>
                    <button type="button" id="open-calendar-btn" class="text-xs text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 transition-colors">
                        <i class="fa-solid fa-calendar-days mr-1"></i>Календарь
                    </button>
                </div>
                
                <!-- Горизонтальный скролл недели -->
                <div class="overflow-x-auto scroll-smooth-x select-none" id="week-dates-wrapper">
                    <div class="flex gap-2 pb-2 flex-nowrap cursor-grab active:cursor-grabbing" id="week-dates" style="min-width: max-content;">
                        @php
                            $today = \Carbon\Carbon::today();
                            $selectedDateCarbon = \Carbon\Carbon::parse($date);
                            $startOfWeek = $today->copy()->startOfWeek();
                        @endphp
                        @for($i = 0; $i < 14; $i++)
                            @php
                                $dateItem = $startOfWeek->copy()->addDays($i);
                                $isToday = $dateItem->isToday();
                                $isSelected = $dateItem->format('Y-m-d') === $date;
                                $isPast = $dateItem->isPast() && !$isToday;
                            @endphp
                            <button type="button" 
                                    class="week-date-btn flex-shrink-0 w-16 md:w-20 p-3 rounded-lg border-2 transition-colors {{ $isSelected ? 'border-indigo-500 bg-indigo-50 dark:bg-indigo-900/20' : 'border-slate-200 dark:border-slate-700 hover-border bg-white/50 dark:bg-slate-800/50' }} {{ $isPast ? 'opacity-50 cursor-not-allowed' : '' }}"
                                    data-date="{{ $dateItem->format('Y-m-d') }}"
                                    {{ $isPast ? 'disabled' : '' }}>
                                <div class="text-xs text-slate-500 dark:text-slate-400 mb-1">
                                    {{ $dateItem->locale('ru')->shortDayName }}
                                </div>
                                <div class="text-base md:text-lg font-semibold {{ $isToday ? 'text-indigo-600 dark:text-indigo-400' : 'text-slate-900 dark:text-white' }}">
                                    {{ $dateItem->day }}
                                </div>
                                <div class="text-xs text-slate-500 dark:text-slate-400">
                                    {{ $dateItem->locale('ru')->shortMonthName }}
                                </div>
                            </button>
                        @endfor
                    </div>
                </div>
            </div>

            <!-- Выбор времени: Горизонтальный скролл (как с датами), без grid -->
            <div class="glass-card rounded-xl p-4 md:p-6 mb-4">
                <div class="flex items-center justify-between mb-3">
                    <label class="text-sm font-semibold text-slate-700 dark:text-slate-300 flex items-center gap-2">
                        <i class="fa-solid fa-clock text-indigo-600 dark:text-indigo-400"></i>
                        Время*
                    </label>
                    <div class="text-xs text-slate-500 dark:text-slate-400">
                        {{ count($availableSlots) }} слотов
                    </div>
                </div>

                <div class="overflow-x-auto scroll-smooth-x select-none" id="time-slots-wrapper">
                    <div class="flex gap-2 pb-2 flex-nowrap cursor-grab active:cursor-grabbing" id="time-slots-container" style="min-width: max-content;">
                        @foreach($availableSlots as $slot)
                            <label class="time-slot-label flex-shrink-0 w-24 h-14 flex items-center justify-center border-2 border-slate-200 dark:border-slate-700 rounded-lg cursor-pointer hover-border bg-white/50 dark:bg-slate-800/50 transition-colors">
                                <input type="radio" name="time" value="{{ $slot }}" required class="sr-only time-radio" {{ old('time') == $slot ? 'checked' : '' }}>
                                <span class="text-sm font-medium text-slate-900 dark:text-white">{{ $slot }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Карточка: Локация -->
            <div class="glass-card rounded-xl p-4 md:p-6 mb-4">
                <div class="text-xs text-slate-500 dark:text-slate-400 mb-1">Локация</div>
                <div class="font-semibold text-slate-900 dark:text-white text-base md:text-sm mb-1">{{ $location->name }}</div>
                @if($location->full_address)
                    <div class="text-sm text-slate-600 dark:text-slate-400 mt-2">
                        <i class="fa-solid fa-map-marker-alt text-indigo-600 dark:text-indigo-400 mr-1"></i>
                        {{ $location->full_address }}
                    </div>
                @endif
            </div>

            <!-- Форма контактов -->
            <div class="glass-card rounded-xl p-4 md:p-6 mb-4">
                <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-300 flex items-center gap-2 mb-4">
                    <i class="fa-solid fa-user text-indigo-600 dark:text-indigo-400"></i>
                    Контактные данные
                </h3>

                <div class="space-y-4">
                    <div>
                        <label for="first_name" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                            Имя*
                        </label>
                        <input type="text" id="first_name" name="first_name" required
                               class="w-full px-4 py-3 text-base rounded-md border border-slate-300 dark:border-slate-700 focus:ring-indigo-500 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:border-transparent transition-colors"
                               value="{{ old('first_name') }}" placeholder="Введите ваше имя">
                        @error('first_name')
                        <p class="mt-2 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="phone" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                            Телефон*
                        </label>
                        <input type="tel" id="phone" name="phone" required
                               class="w-full px-4 py-3 text-base rounded-md border border-slate-300 dark:border-slate-700 focus:ring-indigo-500 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:border-transparent transition-colors"
                               value="{{ old('phone') }}" placeholder="+375 (XX) XXX-XX-XX">
                        @error('phone')
                        <p class="mt-2 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="email" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                                Email
                            </label>
                            <input type="email" id="email" name="email"
                                   class="w-full px-4 py-3 text-base rounded-md border border-slate-300 dark:border-slate-700 focus:ring-indigo-500 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:border-transparent transition-colors"
                                   value="{{ old('email') }}" placeholder="your@email.com">
                            @error('email')
                            <p class="mt-2 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="notes" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                                Заметки
                            </label>
                            <input type="text" id="notes" name="notes"
                                   class="w-full px-4 py-3 text-base rounded-md border border-slate-300 dark:border-slate-700 focus:ring-indigo-500 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:border-transparent transition-colors"
                                   value="{{ old('notes') }}" placeholder="Дополнительная информация">
                            @error('notes')
                            <p class="mt-2 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <button type="submit" class="w-full px-4 py-3 text-base font-medium text-white bg-gradient-to-r from-indigo-500 to-purple-600 hover:from-indigo-600 hover:to-purple-700 rounded-md transition-colors shadow-sm">
                <i class="fa-solid fa-check mr-2"></i>
                Записаться
            </button>
        </form>
    @else
        <div class="glass-card rounded-xl p-4 md:p-6 text-center py-8">
            <i class="fa-solid fa-calendar-times text-3xl mb-3 text-slate-400"></i>
            <p class="mb-4 text-slate-500 dark:text-slate-400">На выбранную дату нет доступных слотов</p>
            <a href="{{ route('public.appointments.select-time', ['slug' => $business->slug, 'locationId' => $location->id, 'serviceId' => $service->id, 'masterId' => $master->id, 'date' => \Carbon\Carbon::tomorrow()->format('Y-m-d')]) }}"
               class="text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 transition-colors">
                Посмотреть завтра
            </a>
        </div>
    @endif
</div>

<!-- Модальное окно календаря (новая версия) -->
<div id="calendar-modal" class="fixed inset-0 z-50 hidden flex items-end sm:items-center justify-center p-0 sm:p-4">
    <div class="modal-backdrop fixed inset-0 bg-black/50 z-40" id="calendar-backdrop"></div>
    <!-- mobile: bottom sheet; desktop: centered dialog -->
    <div class="calendar-dialog w-full sm:max-w-md bg-white dark:bg-slate-800 rounded-t-2xl sm:rounded-2xl shadow-xl z-50 relative overflow-hidden max-h-[85vh] sm:max-h-[90vh]">
        <div class="calendar-scroll max-h-[82vh] sm:max-h-[86vh] overflow-y-auto" id="calendar-content">
            <div class="p-2 sm:p-3 md:p-4 pt-3 pb-3 sm:pb-4" style="padding-bottom: max(env(safe-area-inset-bottom, 6px), 10px);">
                <!-- Заголовок модального окна -->
                <div class="flex items-center justify-between mb-2 sm:mb-3">
                    <h3 class="text-sm sm:text-base font-semibold text-slate-900 dark:text-white">Выберите дату</h3>
                    <button type="button" id="close-calendar-btn" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 p-2 min-w-[40px] min-h-[40px] flex items-center justify-center touch-manipulation">
                        <i class="fa-solid fa-times text-base sm:text-lg"></i>
                    </button>
                </div>

                <!-- Навигация по месяцам -->
                <div class="flex items-center justify-between gap-1.5 sm:gap-2 mb-2.5 sm:mb-3">
                    <button type="button" id="prev-month-btn" class="p-2 min-w-[40px] min-h-[40px] flex items-center justify-center text-slate-600 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400 active:bg-slate-100 dark:active:bg-slate-700 transition-colors rounded-md touch-manipulation">
                        <i class="fa-solid fa-chevron-left text-sm"></i>
                    </button>
                    <div class="text-sm sm:text-base font-semibold text-slate-900 dark:text-white px-2 flex-1 text-center" id="calendar-month-year"></div>
                    <button type="button" id="next-month-btn" class="p-2 min-w-[40px] min-h-[40px] flex items-center justify-center text-slate-600 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400 active:bg-slate-100 dark:active:bg-slate-700 transition-colors rounded-md touch-manipulation">
                        <i class="fa-solid fa-chevron-right text-sm"></i>
                    </button>
                </div>

                <!-- Календарь -->
                <div id="calendar-grid" class="grid grid-cols-7 gap-1 sm:gap-1.5 mb-2.5 sm:mb-3">
                    <!-- Дни недели -->
                    <div class="text-center text-[10px] sm:text-xs font-medium text-slate-500 dark:text-slate-400 py-1">Пн</div>
                    <div class="text-center text-[10px] sm:text-xs font-medium text-slate-500 dark:text-slate-400 py-1">Вт</div>
                    <div class="text-center text-[10px] sm:text-xs font-medium text-slate-500 dark:text-slate-400 py-1">Ср</div>
                    <div class="text-center text-[10px] sm:text-xs font-medium text-slate-500 dark:text-slate-400 py-1">Чт</div>
                    <div class="text-center text-[10px] sm:text-xs font-medium text-slate-500 dark:text-slate-400 py-1">Пт</div>
                    <div class="text-center text-[10px] sm:text-xs font-medium text-slate-500 dark:text-slate-400 py-1">Сб</div>
                    <div class="text-center text-[10px] sm:text-xs font-medium text-slate-500 dark:text-slate-400 py-1">Вс</div>
                    <!-- Ячейки календаря будут сгенерированы через JavaScript -->
                </div>

                <!-- Быстрые кнопки -->
                <div class="flex gap-2 sm:gap-2.5">
                    <button type="button" id="select-today-btn" class="flex-1 px-3 sm:px-4 py-2 sm:py-2.5 text-sm font-medium text-indigo-600 dark:text-indigo-400 border-2 border-indigo-600 dark:border-indigo-400 rounded-md hover:bg-indigo-50 dark:hover:bg-indigo-900/20 active:bg-indigo-100 dark:active:bg-indigo-900/30 transition-colors min-h-[40px] sm:min-h-[44px] touch-manipulation">
                        Сегодня
                    </button>
                    <button type="button" id="select-tomorrow-btn" class="flex-1 px-3 sm:px-4 py-2 sm:py-2.5 text-sm font-medium text-indigo-600 dark:text-indigo-400 border-2 border-indigo-600 dark:border-indigo-400 rounded-md hover:bg-indigo-50 dark:hover:bg-indigo-900/20 active:bg-indigo-100 dark:active:bg-indigo-900/30 transition-colors min-h-[40px] sm:min-h-[44px] touch-manipulation">
                        Завтра
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    (function() {
        'use strict';
        
        // ========== Глобальные переменные ==========
        const selectedDate = '{{ $date }}'; // YYYY-MM-DD
        let selectedDateObj = parseISOToLocalDate(selectedDate);
        let today = startOfDay(new Date());
        let currentCalendarMonth = setMonth(selectedDateObj.getFullYear(), selectedDateObj.getMonth(), 1);

        // ========== Инициализация ==========
        document.addEventListener('DOMContentLoaded', function() {
            initCalendar();
            initTimeSlots();
            initPhoneInput();
            initWeekDates();
            initWeekDatesDrag();
            initTimeSlotsDrag();
            renderCalendar();
        });
        
        // ========== Выбор даты из недели ==========
        function initWeekDates() {
            const weekDateButtons = document.querySelectorAll('.week-date-btn');
            weekDateButtons.forEach(btn => {
                btn.addEventListener('click', function() {
                    if (this.disabled) return;
                    const date = this.getAttribute('data-date');
                    selectDate(date);
                });
            });
        }

        // Горизонтальный скролл с drag/wheel
        function initHorizontalDrag({ wrapperId, itemSelector }) {
            const wrapper = document.getElementById(wrapperId);
            if (!wrapper) return;

            const container = itemSelector ? wrapper.querySelector(itemSelector) : wrapper;
            if (!container) return;

            let isDown = false;
            let startX = 0;
            let scrollLeft = 0;
            let startLeft = 0;

            const start = (pageX) => {
                isDown = true;
                const rect = wrapper.getBoundingClientRect();
                startLeft = rect.left;
                startX = pageX - startLeft;
                scrollLeft = wrapper.scrollLeft;
                wrapper.classList.add('dragging');
            };

            const move = (pageX) => {
                if (!isDown) return;
                const x = pageX - startLeft;
                const walk = (x - startX);
                wrapper.scrollLeft = scrollLeft - walk;
            };

            const end = () => {
                isDown = false;
                wrapper.classList.remove('dragging');
            };

            // Mouse
            wrapper.addEventListener('mousedown', (e) => {
                e.preventDefault();
                start(e.pageX);
            });
            wrapper.addEventListener('mouseleave', end);
            wrapper.addEventListener('mouseup', end);
            wrapper.addEventListener('mousemove', (e) => {
                if (!isDown) return;
                e.preventDefault();
                move(e.pageX);
            });

            // Touch
            wrapper.addEventListener('touchstart', (e) => {
                if (!e.touches || !e.touches[0]) return;
                start(e.touches[0].pageX);
            }, { passive: true });
            wrapper.addEventListener('touchend', end);
            wrapper.addEventListener('touchcancel', end);
            wrapper.addEventListener('touchmove', (e) => {
                if (!e.touches || !e.touches[0]) return;
                move(e.touches[0].pageX);
            }, { passive: true });

            // Wheel -> horizontal scroll (desktop)
            wrapper.addEventListener('wheel', (e) => {
                if (e.ctrlKey || e.shiftKey || e.altKey || e.metaKey) return;
                if (Math.abs(e.deltaY) > 0) {
                    e.preventDefault();
                    wrapper.scrollLeft += e.deltaY;
                }
            }, { passive: false });
        }

        function initWeekDatesDrag() {
            initHorizontalDrag({ wrapperId: 'week-dates-wrapper', itemSelector: '#week-dates' });
        }

        function initTimeSlotsDrag() {
            initHorizontalDrag({ wrapperId: 'time-slots-wrapper', itemSelector: '#time-slots-container' });
        }
        
        // ========== Календарь ==========
        function initCalendar() {
            // Кнопка открытия календаря
            const openBtn = document.getElementById('open-calendar-btn');
            if (openBtn) {
                openBtn.addEventListener('click', openCalendar);
            }
            
            // Кнопка закрытия календаря
            const closeBtn = document.getElementById('close-calendar-btn');
            if (closeBtn) {
                closeBtn.addEventListener('click', closeCalendar);
            }
            
            // Backdrop
            const backdrop = document.getElementById('calendar-backdrop');
            if (backdrop) {
                backdrop.addEventListener('click', closeCalendar);
            }
            
            // Предотвращаем закрытие при клике на контент
            const content = document.getElementById('calendar-content');
            if (content) {
                content.addEventListener('click', function(e) {
                    e.stopPropagation();
                });
            }
            
            // Навигация по месяцам
            const prevBtn = document.getElementById('prev-month-btn');
            const nextBtn = document.getElementById('next-month-btn');
            if (prevBtn) {
                prevBtn.addEventListener('click', () => changeMonth(-1));
            }
            if (nextBtn) {
                nextBtn.addEventListener('click', () => changeMonth(1));
            }
            
            // Быстрые кнопки
            const todayBtn = document.getElementById('select-today-btn');
            const tomorrowBtn = document.getElementById('select-tomorrow-btn');
            if (todayBtn) {
                todayBtn.addEventListener('click', selectToday);
            }
            if (tomorrowBtn) {
                tomorrowBtn.addEventListener('click', selectTomorrow);
            }
            
            // Закрытие по Escape
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    const modal = document.getElementById('calendar-modal');
                    if (modal && !modal.classList.contains('hidden')) {
                        closeCalendar();
                    }
                }
            });
        }

        // ---------- Новый календарь ----------
        const monthNames = ['Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'];

        function startOfDay(date) {
            const d = new Date(date);
            d.setHours(0,0,0,0);
            return d;
        }

        function parseISOToLocalDate(iso) {
            const [y, m, d] = iso.split('-').map(Number);
            return new Date(y, m - 1, d);
        }
        
        function setMonth(year, month, day=1) {
            return new Date(year, month, day);
        }

        function formatDateISO(date) {
            const y = date.getFullYear();
            const m = String(date.getMonth() + 1).padStart(2, '0');
            const d = String(date.getDate()).padStart(2, '0');
            return `${y}-${m}-${d}`;
        }

        function renderCalendar() {
            const grid = document.getElementById('calendar-grid');
            const header = document.getElementById('calendar-month-year');
            if (!grid || !header) return;

            // Очищаем старые дни
            const old = grid.querySelectorAll('.calendar-day');
            old.forEach(node => node.remove());

            const year = currentCalendarMonth.getFullYear();
            const month = currentCalendarMonth.getMonth();
            header.textContent = `${monthNames[month]} ${year}`;

            const firstDay = setMonth(year, month, 1);
            const dow = firstDay.getDay(); // 0=вс
            const offset = dow === 0 ? 6 : dow - 1; // сдвиг к понедельнику
            const startDate = new Date(firstDay);
            startDate.setDate(firstDay.getDate() - offset);

            for (let i = 0; i < 42; i++) {
                const date = new Date(startDate);
                date.setDate(startDate.getDate() + i);
                const dateStart = startOfDay(date);

                const isCurrentMonth = date.getMonth() === month;
                const isToday = dateStart.getTime() === today.getTime();
                const isSelected = dateStart.getTime() === selectedDateObj.getTime();
                const isPast = dateStart < today;

                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'calendar-day p-1.5 sm:p-2.5 min-w-[34px] min-h-[34px] sm:min-w-[40px] sm:min-h-[40px] md:min-w-[44px] md:min-h-[44px] rounded-md text-sm font-medium transition-colors flex items-center justify-center ' +
                    (isCurrentMonth ? 'text-slate-900 dark:text-white' : 'text-slate-400 dark:text-slate-500') +
                    (isToday ? ' bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 font-semibold' : '') +
                    (isSelected ? ' bg-indigo-500 text-white dark:bg-indigo-600' : ' hover:bg-slate-100 dark:hover:bg-slate-700') +
                    (isPast ? ' opacity-50 cursor-not-allowed' : ' active:scale-95');
                btn.textContent = date.getDate();
                btn.disabled = isPast;

                if (!isPast) {
                    btn.addEventListener('click', () => {
                        const iso = formatDateISO(dateStart);
                        selectDate(iso);
                    });
                }

                grid.appendChild(btn);
            }
        }

        function changeMonth(direction) {
            currentCalendarMonth = setMonth(
                currentCalendarMonth.getFullYear(),
                currentCalendarMonth.getMonth() + direction,
                1
            );
            renderCalendar();
        }

        function openCalendar() {
            const modal = document.getElementById('calendar-modal');
            if (modal) {
                modal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
                renderCalendar();
            }
        }

        function closeCalendar() {
            const modal = document.getElementById('calendar-modal');
            if (modal) {
                modal.classList.add('hidden');
                document.body.style.overflow = '';
            }
        }

        function selectToday() {
            const iso = formatDateISO(today);
            selectDate(iso);
        }

        function selectTomorrow() {
            const t = new Date(today);
            t.setDate(t.getDate() + 1);
            const iso = formatDateISO(t);
            selectDate(iso);
        }

        // ========== Выбор даты ==========
        function selectDate(dateISO) {
            const url = new URL(window.location.href);
            url.searchParams.set('date', dateISO);
            window.location.href = url.toString();
        }
        
        // ========== Подсветка выбранного времени ==========
        function initTimeSlots() {
            const containers = [
                document.getElementById('time-slots-container'),
                document.getElementById('time-slots-container-desktop')
            ];
            
            containers.forEach(container => {
                if (!container) return;
                
                function updateHighlight() {
                    const allLabels = container.querySelectorAll('.time-slot-label');
                    allLabels.forEach(label => {
                        const radio = label.querySelector('.time-radio');
                        if (radio && radio.checked) {
                            label.classList.add('border-indigo-500', 'bg-indigo-50');
                            label.classList.remove('border-slate-200', 'bg-white/50');
                            if (document.documentElement.classList.contains('dark')) {
                                label.classList.add('dark:bg-indigo-900/20');
                                label.classList.remove('dark:bg-slate-800/50', 'dark:border-slate-700');
                            }
                        } else {
                            label.classList.remove('border-indigo-500', 'bg-indigo-50', 'dark:bg-indigo-900/20');
                            label.classList.add('border-slate-200', 'bg-white/50', 'dark:border-slate-700', 'dark:bg-slate-800/50');
                        }
                    });
                }
                
                container.addEventListener('click', function(e) {
                    const label = e.target.closest('.time-slot-label');
                    if (label) {
                        setTimeout(updateHighlight, 10);
                    }
                });
                
                container.addEventListener('change', function(e) {
                    if (e.target.classList.contains('time-radio')) {
                        updateHighlight();
                    }
                });
                
                setTimeout(updateHighlight, 100);
            });
        }
        
        // ========== Обработка телефона ==========
        function initPhoneInput() {
            const phoneInput = document.getElementById('phone');
            if (!phoneInput) return;
            
            phoneInput.addEventListener('focus', function() {
                if (!this.value) {
                    this.value = '+375';
                }
            });
            
            phoneInput.addEventListener('input', function(e) {
                let value = this.value.replace(/\D/g, '');
                
                if (value.startsWith('375')) {
                    value = '+' + value;
                } else if (!value.startsWith('+375') && value.length > 0) {
                    value = '+375' + value;
                }
                
                if (value.length > 4) {
                    let formatted = value.substring(0, 4);
                    if (value.length > 4) {
                        formatted += ' (' + value.substring(4, 6);
                    }
                    if (value.length > 6) {
                        formatted += ') ' + value.substring(6, 9);
                    }
                    if (value.length > 9) {
                        formatted += '-' + value.substring(9, 11);
                    }
                    if (value.length > 11) {
                        formatted += '-' + value.substring(11, 13);
                    }
                    this.value = formatted;
                } else {
                    this.value = value;
                }
            });
        }
    })();
</script>
@endpush
@endsection
