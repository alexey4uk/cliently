@extends('appointments.public.layout')

@section('title', 'Выбор времени')

@section('content')
<div class="space-y-4 w-full min-w-0">
    <!-- Breadcrumb навигация -->
    <div>
        <a href="{{ route('public.appointments.select-service', ['slug' => $business->slug, 'locationId' => $location->id, 'serviceId' => $service->id]) }}" 
           class="inline-flex items-center gap-1.5 text-xs font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 transition-colors">
            <i class="fa-solid fa-arrow-left text-xs"></i>
            <span>Назад к выбору мастера</span>
        </a>
    </div>

    <form method="POST" action="{{ route('public.appointments.store', $business->slug) }}" class="space-y-4" id="appointment-form">
        @csrf
        <input type="hidden" name="location_id" value="{{ $location->id }}">
        <input type="hidden" name="service_id" value="{{ $service->id }}">
        <input type="hidden" name="master_id" value="{{ $master->id }}">
        <input type="hidden" name="date" value="{{ $date }}" id="selected-date-input">

            <!-- Выбор даты: Горизонтальный скролл недели -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-lg overflow-hidden w-full">
                <!-- Компактный заголовок (всегда видимый) -->
                <div class="px-4 py-3 sm:px-4 sm:py-3">
                    <!-- Мобильная версия: вертикальный layout -->
                    <div class="block sm:hidden space-y-3">
                        <div class="flex items-center justify-between">
                            <label class="text-sm font-semibold text-slate-700 dark:text-slate-300 flex items-center gap-2">
                                <div class="w-7 h-7 rounded-lg bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center">
                                    <i class="fa-solid fa-calendar-alt text-indigo-600 dark:text-indigo-400 text-sm"></i>
                                </div>
                                <span>Дата</span>
                            </label>
                            @php
                                $selectedDateCarbon = \Carbon\Carbon::parse($date);
                                $currentYear = \Carbon\Carbon::now()->year;
                                $showYear = $selectedDateCarbon->year !== $currentYear;
                            @endphp
                            @if($showYear)
                                <span class="year-badge text-xs font-semibold text-indigo-600 dark:text-indigo-400 px-2 py-1 bg-indigo-50 dark:bg-indigo-900/20 rounded-lg">
                                    {{ $selectedDateCarbon->year }}
                                </span>
                            @endif
                        </div>
                        <div class="text-sm font-medium text-slate-700 dark:text-slate-300 px-3 py-2 bg-slate-100 dark:bg-slate-800 rounded-xl text-center" id="selected-date-display-mobile">
                            {{ $selectedDateCarbon->locale('ru')->isoFormat('D MMMM') }}
                        </div>
                        <div class="grid grid-cols-2 gap-2">
                            <button type="button" id="toggle-date-selector-btn" class="px-4 py-2.5 text-sm font-medium text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-900/20 active:bg-indigo-100 dark:active:bg-indigo-900/30 rounded-xl transition-colors flex items-center justify-center gap-2 border border-indigo-200 dark:border-indigo-800 min-h-[44px]">
                                <i class="fa-solid fa-calendar-week text-sm"></i>
                                <span>Выбрать</span>
                                <i class="fa-solid fa-chevron-down text-xs transition-transform duration-200" id="date-selector-icon"></i>
                            </button>
                            <button type="button" id="open-calendar-btn" class="px-4 py-2.5 text-sm font-medium text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-900/20 active:bg-indigo-100 dark:active:bg-indigo-900/30 rounded-xl transition-colors flex items-center justify-center gap-2 border border-indigo-200 dark:border-indigo-800 min-h-[44px]">
                                <i class="fa-solid fa-calendar-days text-sm"></i>
                                <span>Календарь</span>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Десктопная версия: горизонтальный layout -->
                    <div class="hidden sm:flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <label class="text-sm font-semibold text-slate-700 dark:text-slate-300 flex items-center gap-1.5">
                                <div class="w-6 h-6 rounded-lg bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center">
                                    <i class="fa-solid fa-calendar-alt text-indigo-600 dark:text-indigo-400 text-xs"></i>
                                </div>
                                <span>Дата</span>
                            </label>
                            @if($showYear)
                                <span class="year-badge text-[10px] font-semibold text-indigo-600 dark:text-indigo-400 px-1.5 py-0.5 bg-indigo-50 dark:bg-indigo-900/20 rounded-lg">
                                    {{ $selectedDateCarbon->year }}
                                </span>
                            @else
                                <span class="year-badge text-[10px] font-semibold text-indigo-600 dark:text-indigo-400 px-1.5 py-0.5 bg-indigo-50 dark:bg-indigo-900/20 rounded hidden"></span>
                            @endif
                            <span class="text-xs sm:text-sm font-medium text-slate-700 dark:text-slate-300 px-2 py-1 bg-slate-100 dark:bg-slate-800 rounded-lg" id="selected-date-display">
                                {{ $selectedDateCarbon->locale('ru')->isoFormat('D MMMM') }}
                            </span>
                        </div>
                        <div class="flex items-center gap-2">
                            <button type="button" id="toggle-date-selector-btn-desktop" class="px-3 py-1.5 text-xs font-medium text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-900/20 hover:bg-indigo-100 dark:hover:bg-indigo-900/30 rounded-xl transition-colors flex items-center gap-1.5 border border-indigo-200 dark:border-indigo-800">
                                <i class="fa-solid fa-calendar-week text-xs"></i>
                                <span>Выбрать</span>
                                <i class="fa-solid fa-chevron-down text-[10px] transition-transform duration-200" id="date-selector-icon-desktop"></i>
                            </button>
                            <button type="button" id="open-calendar-btn-desktop" class="px-3 py-1.5 text-xs font-medium text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-900/20 hover:bg-indigo-100 dark:hover:bg-indigo-900/30 rounded-xl transition-colors flex items-center gap-1.5 border border-indigo-200 dark:border-indigo-800">
                                <i class="fa-solid fa-calendar-days text-xs"></i>
                                <span>Календарь</span>
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Блок выбора даты (скрыт по умолчанию) -->
                <div id="date-selector-panel" class="hidden border-t border-slate-200 dark:border-slate-700">
                    <!-- Горизонтальный скролл недели -->
                    <div class="relative pt-3">
                        <!-- Fade эффект слева -->
                        <div class="absolute left-0 top-0 bottom-0 w-8 bg-gradient-to-r from-white dark:from-slate-900 to-transparent pointer-events-none z-10 opacity-0 transition-opacity duration-300" id="dates-fade-left"></div>
                        <!-- Fade эффект справа -->
                        <div class="absolute right-0 top-0 bottom-0 w-8 bg-gradient-to-l from-white dark:from-slate-900 to-transparent pointer-events-none z-10 opacity-0 transition-opacity duration-300" id="dates-fade-right"></div>
                        <div class="overflow-x-auto scrollbar-hide scroll-smooth select-none px-4 pb-3 snap-x snap-mandatory cursor-grab active:cursor-grabbing" id="week-dates-wrapper" style="scroll-behavior: smooth; -webkit-overflow-scrolling: touch; overscroll-behavior-x: contain;">
                            <div class="flex gap-1.5 pb-1.5 flex-nowrap" id="week-dates" style="min-width: max-content;">
                        @php
                            $today = \Carbon\Carbon::today();
                            $selectedDateCarbon = \Carbon\Carbon::parse($date);
                            
                            // Начинаем с текущей недели по умолчанию
                            $startOfWeek = $today->copy()->startOfWeek();
                            
                            // Если выбранная дата в будущем и не попадает в текущий диапазон (14 дней)
                            $endOfRange = $startOfWeek->copy()->addDays(13);
                            if ($selectedDateCarbon->gte($today) && $selectedDateCarbon->gt($endOfRange)) {
                                // Начинаем с недели выбранной даты
                                $startOfWeek = $selectedDateCarbon->copy()->startOfWeek();
                            }
                            
                            // Если выбранная дата в прошлом, всегда начинаем с текущей недели
                            if ($selectedDateCarbon->lt($today)) {
                                $startOfWeek = $today->copy()->startOfWeek();
                            }
                        @endphp
                        @for($i = 0; $i < 14; $i++)
                            @php
                                $dateItem = $startOfWeek->copy()->addDays($i);
                                $isToday = $dateItem->isToday();
                                $isSelected = $dateItem->format('Y-m-d') === $date;
                                $isPast = $dateItem->isPast() && !$isToday;
                            @endphp
                            <button type="button" 
                                    class="week-date-btn flex-shrink-0 w-16 sm:w-14 md:w-16 p-2.5 sm:p-2 rounded-xl border-2 transition-all duration-200 snap-start {{ $isSelected ? 'border-indigo-400 dark:border-indigo-500 bg-indigo-50 dark:bg-indigo-900/20 ring-1 ring-indigo-200 dark:ring-indigo-800 shadow-sm' : 'border-slate-200 dark:border-slate-700 hover:border-indigo-300 dark:hover:border-indigo-600 bg-white dark:bg-slate-800' }} {{ $isPast ? 'opacity-50 cursor-not-allowed' : 'hover:shadow-md active:scale-95' }} min-h-[60px] sm:min-h-0"
                                    data-date="{{ $dateItem->format('Y-m-d') }}"
                                    {{ $isPast ? 'disabled' : '' }}>
                                <div class="text-[10px] sm:text-[9px] text-slate-500 dark:text-slate-400 mb-1 sm:mb-0.5 leading-tight">
                                    @php
                                        $dayNames = ['Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб', 'Вс'];
                                        $dayIndex = $dateItem->dayOfWeekIso - 1; // ISO: 1=Пн, 7=Вс
                                    @endphp
                                    {{ $dayNames[$dayIndex] }}
                                </div>
                                <div class="text-base sm:text-sm md:text-base font-bold {{ $isToday ? 'text-indigo-600 dark:text-indigo-400' : 'text-slate-900 dark:text-white' }} leading-tight">
                                    {{ $dateItem->day }}
                                </div>
                                <div class="text-[10px] sm:text-[9px] text-slate-500 dark:text-slate-400 mt-1 sm:mt-0.5 leading-tight">
                                    {{ $dateItem->locale('ru')->shortMonthName }}
                                </div>
                            </button>
                        @endfor
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Выбор времени: Горизонтальный скролл -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-lg overflow-hidden w-full">
                <div class="px-4 pt-4 pb-3">
                    <div class="flex items-center justify-between mb-3">
                        <label class="text-sm font-semibold text-slate-700 dark:text-slate-300 flex items-center gap-1.5">
                            <div class="w-6 h-6 rounded-lg bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center">
                                <i class="fa-solid fa-clock text-indigo-600 dark:text-indigo-400 text-xs"></i>
                            </div>
                            <span>Время*</span>
                        </label>
                        <div class="text-[10px] font-medium text-slate-500 dark:text-slate-400 px-1.5 py-0.5 bg-slate-100 dark:bg-slate-800 rounded-lg" id="slots-count-badge" style="{{ count($availableSlots) == 0 ? 'display: none;' : '' }}">
                            {{ count($availableSlots) }} {{ count($availableSlots) == 1 ? 'слот' : 'слотов' }}
                        </div>
                    </div>
                </div>

                <!-- Контейнер для слотов (динамически обновляется) -->
                <div class="relative" id="time-slots-section">
                    <!-- Fade эффект слева -->
                    <div class="absolute left-0 top-0 bottom-0 w-8 bg-gradient-to-r from-white dark:from-slate-900 to-transparent pointer-events-none z-10 opacity-0 transition-opacity duration-300" id="time-fade-left"></div>
                    <!-- Fade эффект справа -->
                    <div class="absolute right-0 top-0 bottom-0 w-8 bg-gradient-to-l from-white dark:from-slate-900 to-transparent pointer-events-none z-10 opacity-0 transition-opacity duration-300" id="time-fade-right"></div>
                    @if(count($availableSlots) > 0)
                        <!-- Горизонтальный скролл временных слотов -->
                        <div class="overflow-x-auto scrollbar-hide scroll-smooth select-none px-4 pb-3 snap-x snap-mandatory cursor-grab active:cursor-grabbing" id="time-slots-wrapper" style="scroll-behavior: smooth; -webkit-overflow-scrolling: touch; overscroll-behavior-x: contain;">
                            <div class="flex gap-2 pb-1.5 flex-nowrap" id="time-slots-container" style="min-width: max-content;">
                            @foreach($availableSlots as $slot)
                                <label class="time-slot-label flex-shrink-0 w-20 sm:w-16 md:w-20 p-3 sm:p-2 rounded-xl border-2 border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-center cursor-pointer transition-all duration-200 hover:border-indigo-300 dark:hover:border-indigo-600 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 hover:shadow-md active:scale-95 snap-start {{ old('time') == $slot ? 'border-indigo-400 dark:border-indigo-500 bg-indigo-50 dark:bg-indigo-900/20 ring-1 ring-indigo-200 dark:ring-indigo-800 shadow-sm' : '' }} min-h-[52px] sm:min-h-0">
                                    <input type="radio" name="time" value="{{ $slot }}" required class="sr-only time-radio" {{ old('time') == $slot ? 'checked' : '' }}>
                                    <span class="text-sm sm:text-xs font-semibold text-slate-900 dark:text-white leading-tight">{{ $slot }}</span>
                                </label>
                            @endforeach
                            </div>
                        </div>
                    @else
                        <!-- Уведомление о отсутствии слотов -->
                        <div class="px-4 pb-4" id="time-slots-container">
                            <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-xl p-4">
                                <div class="flex items-start gap-3">
                                    <div class="flex-shrink-0 w-8 h-8 rounded-lg bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center">
                                        <i class="fa-solid fa-info-circle text-amber-600 dark:text-amber-400 text-sm"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h4 class="text-sm font-semibold text-amber-900 dark:text-amber-200 mb-1">
                                            Нет свободных мест на эту дату
                                        </h4>
                                        <p class="text-xs text-amber-800 dark:text-amber-300">
                                            На выбранную дату все временные слоты заняты. Пожалуйста, выберите другую дату.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Форма контактов -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-lg p-4">
                <h3 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2 mb-4">
                    <div class="w-7 h-7 rounded-lg bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center">
                        <i class="fa-solid fa-user text-indigo-600 dark:text-indigo-400 text-sm"></i>
                    </div>
                    <span>Контактные данные</span>
                </h3>

                <div class="space-y-4">
                    <div>
                        <label for="first_name" class="block text-xs font-medium text-slate-700 dark:text-slate-300 mb-1.5">
                            Имя*
                        </label>
                        <input type="text" id="first_name" name="first_name" required autofocus
                               class="w-full px-3 py-2 text-sm rounded-xl border border-slate-300 dark:border-slate-700 focus:ring-2 focus:ring-indigo-500 focus:border-transparent bg-white dark:bg-slate-900 text-slate-900 dark:text-white transition-all"
                               value="{{ old('first_name') }}" placeholder="Введите ваше имя" aria-label="Имя">
                        @error('first_name')
                        <p class="mt-1.5 text-xs text-rose-600 dark:text-rose-400 flex items-center gap-1">
                            <i class="fa-solid fa-exclamation-circle text-[10px]"></i>
                            <span>{{ $message }}</span>
                        </p>
                        @enderror
                    </div>

                    <div>
                        <label for="phone" class="block text-xs font-medium text-slate-700 dark:text-slate-300 mb-1.5">
                            Телефон*
                        </label>
                        <input type="tel" id="phone" name="phone" required
                               class="w-full px-3 py-2 text-sm rounded-xl border border-slate-300 dark:border-slate-700 focus:ring-2 focus:ring-indigo-500 focus:border-transparent bg-white dark:bg-slate-900 text-slate-900 dark:text-white transition-all"
                               value="{{ old('phone') }}" placeholder="+375XXXXXXXXX">
                        @error('phone')
                        <p class="mt-1.5 text-xs text-rose-600 dark:text-rose-400 flex items-center gap-1">
                            <i class="fa-solid fa-exclamation-circle text-[10px]"></i>
                            <span>{{ $message }}</span>
                        </p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="email" class="block text-xs font-medium text-slate-700 dark:text-slate-300 mb-1.5">
                                Email
                            </label>
                            <input type="email" id="email" name="email"
                                   class="w-full px-3 py-2 text-sm rounded-xl border border-slate-300 dark:border-slate-700 focus:ring-2 focus:ring-indigo-500 focus:border-transparent bg-white dark:bg-slate-900 text-slate-900 dark:text-white transition-all"
                                   value="{{ old('email') }}" placeholder="your@email.com">
                            @error('email')
                            <p class="mt-1.5 text-xs text-rose-600 dark:text-rose-400 flex items-center gap-1">
                                <i class="fa-solid fa-exclamation-circle text-[10px]"></i>
                                <span>{{ $message }}</span>
                            </p>
                            @enderror
                        </div>

                        <div>
                            <label for="notes" class="block text-xs font-medium text-slate-700 dark:text-slate-300 mb-1.5">
                                Заметки
                            </label>
                            <input type="text" id="notes" name="notes"
                                   class="w-full px-3 py-2 text-sm rounded-xl border border-slate-300 dark:border-slate-700 focus:ring-2 focus:ring-indigo-500 focus:border-transparent bg-white dark:bg-slate-900 text-slate-900 dark:text-white transition-all"
                                   value="{{ old('notes') }}" placeholder="Дополнительная информация">
                            @error('notes')
                            <p class="mt-1.5 text-xs text-rose-600 dark:text-rose-400 flex items-center gap-1">
                                <i class="fa-solid fa-exclamation-circle text-[10px]"></i>
                                <span>{{ $message }}</span>
                            </p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <button type="submit" id="submit-btn" class="w-full px-4 py-3 text-sm font-semibold text-white bg-gradient-to-r from-indigo-500 to-purple-600 hover:from-indigo-600 hover:to-purple-700 rounded-xl transition-all shadow-lg hover:shadow-xl flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed" {{ count($availableSlots) == 0 ? 'disabled' : '' }}>
                <i class="fa-solid fa-check text-xs"></i>
                <span class="submit-text">Записаться</span>
                <span class="submit-loading hidden">
                    <i class="fa-solid fa-spinner fa-spin mr-1.5 text-xs"></i>
                    <span>Отправка...</span>
                </span>
            </button>
        </form>
</div>

<!-- Модальное окно календаря -->
<div id="calendar-modal" class="fixed inset-0 z-50 hidden flex items-end sm:items-center justify-center p-0 sm:p-4" style="padding-bottom: env(safe-area-inset-bottom, 0);">
    <div class="fixed inset-0 bg-black/50 backdrop-blur-sm z-40" id="calendar-backdrop"></div>
    <div class="calendar-dialog w-full sm:max-w-md bg-white dark:bg-slate-800 rounded-t-2xl sm:rounded-2xl shadow-xl z-50 relative overflow-hidden max-h-[90vh] flex flex-col" style="max-height: calc(100vh - env(safe-area-inset-bottom, 0px) - env(safe-area-inset-top, 0px));">
        <!-- Индикатор drag для мобильных -->
        <div class="sm:hidden flex justify-center pt-3 pb-2">
            <div class="w-12 h-1.5 bg-slate-300 dark:bg-slate-600 rounded-full"></div>
        </div>
        <div class="flex-1 overflow-y-auto overflow-x-hidden" id="calendar-content" style="max-height: calc(100vh - env(safe-area-inset-bottom, 0px) - env(safe-area-inset-top, 0px) - 20px);">
            <div class="p-4 sm:p-3 md:p-4 pt-3 sm:pt-4 pb-4 sm:pb-4" style="padding-bottom: max(env(safe-area-inset-bottom, 16px), 16px);">
                <!-- Заголовок модального окна -->
                <div class="flex items-center justify-between mb-3 sm:mb-3">
                    <h3 class="text-base sm:text-lg font-bold text-slate-900 dark:text-white">Выберите дату</h3>
                    <button type="button" id="close-calendar-btn" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 p-2 min-w-[44px] min-h-[44px] flex items-center justify-center rounded-full hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors">
                        <i class="fa-solid fa-times text-lg"></i>
                    </button>
                </div>

                <!-- Навигация по месяцам -->
                <div class="flex items-center justify-between gap-2 mb-3">
                    <button type="button" id="prev-month-btn" class="p-2 min-w-[40px] min-h-[40px] flex items-center justify-center text-slate-600 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400 active:bg-slate-100 dark:active:bg-slate-700 transition-colors rounded-md">
                        <i class="fa-solid fa-chevron-left text-sm"></i>
                    </button>
                    <div class="text-sm sm:text-base font-bold text-slate-900 dark:text-white px-2 flex-1 text-center" id="calendar-month-year"></div>
                    <button type="button" id="next-month-btn" class="p-2 min-w-[40px] min-h-[40px] flex items-center justify-center text-slate-600 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400 active:bg-slate-100 dark:active:bg-slate-700 transition-colors rounded-md">
                        <i class="fa-solid fa-chevron-right text-sm"></i>
                    </button>
                </div>

                <!-- Календарь -->
                <div id="calendar-grid" class="grid grid-cols-7 gap-1.5 sm:gap-2 mb-3">
                    <!-- Дни недели -->
                    <div class="text-center text-xs font-semibold text-slate-500 dark:text-slate-400 py-2">Пн</div>
                    <div class="text-center text-xs font-semibold text-slate-500 dark:text-slate-400 py-2">Вт</div>
                    <div class="text-center text-xs font-semibold text-slate-500 dark:text-slate-400 py-2">Ср</div>
                    <div class="text-center text-xs font-semibold text-slate-500 dark:text-slate-400 py-2">Чт</div>
                    <div class="text-center text-xs font-semibold text-slate-500 dark:text-slate-400 py-2">Пт</div>
                    <div class="text-center text-xs font-semibold text-slate-500 dark:text-slate-400 py-2">Сб</div>
                    <div class="text-center text-xs font-semibold text-slate-500 dark:text-slate-400 py-2">Вс</div>
                    <!-- Ячейки календаря будут сгенерированы через JavaScript -->
                </div>

                <!-- Быстрые кнопки -->
                <div class="flex gap-2.5">
                    <button type="button" id="select-today-btn" class="flex-1 px-4 py-3 text-sm font-medium text-indigo-600 dark:text-indigo-400 border-2 border-indigo-600 dark:border-indigo-400 rounded-lg hover:bg-indigo-50 dark:hover:bg-indigo-900/20 active:bg-indigo-100 dark:active:bg-indigo-900/30 transition-colors min-h-[48px]">
                        Сегодня
                    </button>
                    <button type="button" id="select-tomorrow-btn" class="flex-1 px-4 py-3 text-sm font-medium text-indigo-600 dark:text-indigo-400 border-2 border-indigo-600 dark:border-indigo-400 rounded-lg hover:bg-indigo-50 dark:hover:bg-indigo-900/20 active:bg-indigo-100 dark:active:bg-indigo-900/30 transition-colors min-h-[48px]">
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

        // ========== Переключение видимости блока выбора даты ==========
        function initDateSelectorToggle() {
            // Мобильная версия
            const toggleBtn = document.getElementById('toggle-date-selector-btn');
            const panel = document.getElementById('date-selector-panel');
            const icon = document.getElementById('date-selector-icon');
            
            // Десктопная версия
            const toggleBtnDesktop = document.getElementById('toggle-date-selector-btn-desktop');
            const iconDesktop = document.getElementById('date-selector-icon-desktop');
            
            function togglePanel() {
                const isHidden = panel.classList.contains('hidden');
                
                if (isHidden) {
                    panel.classList.remove('hidden');
                    if (icon) icon.classList.add('rotate-180');
                    if (iconDesktop) iconDesktop.classList.add('rotate-180');
                } else {
                    panel.classList.add('hidden');
                    if (icon) icon.classList.remove('rotate-180');
                    if (iconDesktop) iconDesktop.classList.remove('rotate-180');
                }
            }
            
            if (toggleBtn && panel) {
                toggleBtn.addEventListener('click', togglePanel);
            }
            
            if (toggleBtnDesktop && panel) {
                toggleBtnDesktop.addEventListener('click', togglePanel);
            }
        }
        
        // ========== Инициализация ==========
        document.addEventListener('DOMContentLoaded', function() {
            initCalendar();
            initTimeSlots();
            initPhoneInput();
            initWeekDates();
            initWeekDatesDrag();
            initTimeSlotsDrag();
            initDateSelectorToggle();
            renderCalendar();
            
            // Обновляем визуальное отображение выбранной даты при загрузке
            updateSelectedDate(selectedDate);
            
            // Инициализация fade эффектов для скролла
            setTimeout(() => {
                updateFadeEffects('week-dates-wrapper', 'dates-fade-left', 'dates-fade-right');
                const timeWrapper = document.getElementById('time-slots-wrapper');
                if (timeWrapper) {
                    updateFadeEffects('time-slots-wrapper', 'time-fade-left', 'time-fade-right');
                }
            }, 100);
            
            // Проверяем доступность формы при загрузке
            updateFormAvailability();
        });
        
        // ========== Обновление доступности формы ==========
        function updateFormAvailability() {
            const timeSlotsContainer = document.getElementById('time-slots-container');
            const submitBtn = document.getElementById('submit-btn');
            const hasSlots = timeSlotsContainer && timeSlotsContainer.children.length > 0;
            
            if (submitBtn) {
                if (hasSlots) {
                    submitBtn.disabled = false;
                    submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                } else {
                    submitBtn.disabled = true;
                    submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
                }
            }
        }
        
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
        
        // Проверка, есть ли дата в текущем скролле
        function isDateInScroll(dateISO) {
            const weekDateButtons = document.querySelectorAll('.week-date-btn');
            for (let btn of weekDateButtons) {
                if (btn.getAttribute('data-date') === dateISO) {
                    return true;
                }
            }
            return false;
        }
        
        // Обновление визуального отображения выбранной даты
        function updateSelectedDate(dateISO) {
            if (!dateISO) return false;
            
            // Проверяем, есть ли дата в текущем скролле
            if (!isDateInScroll(dateISO)) {
                return false; // Дата не найдена в скролле
            }
            
            // Парсим выбранную дату
            const selectedDate = new Date(dateISO + 'T00:00:00');
            const selectedYear = selectedDate.getFullYear();
            const currentYear = new Date().getFullYear();
            
            // Обновляем отображение года рядом с заголовком "Дата"
            const dateLabelContainer = document.querySelector('.flex.items-center.gap-2');
            if (dateLabelContainer) {
                let yearElement = dateLabelContainer.querySelector('.year-badge');
                
                if (selectedYear !== currentYear) {
                    if (!yearElement) {
                        yearElement = document.createElement('span');
                        yearElement.className = 'year-badge text-xs font-semibold text-indigo-600 dark:text-indigo-400 px-2 py-1 bg-indigo-50 dark:bg-indigo-900/20 rounded-md';
                        dateLabelContainer.appendChild(yearElement);
                    }
                    yearElement.textContent = selectedYear;
                    yearElement.classList.remove('hidden');
                } else {
                    if (yearElement) {
                        yearElement.classList.add('hidden');
                    }
                }
            }
            
            // Обновляем все кнопки дат в горизонтальном скролле
            const weekDateButtons = document.querySelectorAll('.week-date-btn');
            
            weekDateButtons.forEach(btn => {
                const btnDate = btn.getAttribute('data-date');
                if (btnDate === dateISO) {
                    // Добавляем классы для выбранной даты через Tailwind
                    btn.classList.remove('border-slate-200', 'dark:border-slate-700', 'hover:border-indigo-300', 'dark:hover:border-indigo-600', 'bg-white', 'dark:bg-slate-800');
                    btn.classList.add('border-indigo-400', 'dark:border-indigo-500', 'bg-indigo-50', 'dark:bg-indigo-900/20', 'ring-1', 'ring-indigo-200', 'dark:ring-indigo-800', 'shadow-sm');
                } else {
                    // Убираем классы выбранной даты
                    btn.classList.remove('border-indigo-400', 'dark:border-indigo-500', 'bg-indigo-50', 'dark:bg-indigo-900/20', 'ring-1', 'ring-indigo-200', 'dark:ring-indigo-800', 'shadow-sm');
                    btn.classList.add('border-slate-200', 'dark:border-slate-700', 'hover:border-indigo-300', 'dark:hover:border-indigo-600', 'bg-white', 'dark:bg-slate-800');
                }
            });
            
            // Обновляем скрытое поле с датой
            const dateInput = document.getElementById('selected-date-input');
            if (dateInput) {
                dateInput.value = dateISO;
            }
            
            // Обновляем отображение выбранной даты в заголовке (десктоп)
            const dateDisplay = document.getElementById('selected-date-display');
            if (dateDisplay) {
                const dateObj = new Date(dateISO + 'T00:00:00');
                const formatter = new Intl.DateTimeFormat('ru', { day: 'numeric', month: 'long' });
                dateDisplay.textContent = formatter.format(dateObj);
            }
            
            // Обновляем отображение выбранной даты в заголовке (мобильная)
            const dateDisplayMobile = document.getElementById('selected-date-display-mobile');
            if (dateDisplayMobile) {
                const dateObj = new Date(dateISO + 'T00:00:00');
                const formatter = new Intl.DateTimeFormat('ru', { day: 'numeric', month: 'long' });
                dateDisplayMobile.textContent = formatter.format(dateObj);
            }
            
            // Прокручиваем к выбранной дате в горизонтальном скролле
            const selectedBtn = document.querySelector(`.week-date-btn[data-date="${dateISO}"]`);
            if (selectedBtn) {
                const wrapper = document.getElementById('week-dates-wrapper');
                if (wrapper) {
                    setTimeout(() => {
                        const btnRect = selectedBtn.getBoundingClientRect();
                        const wrapperRect = wrapper.getBoundingClientRect();
                        const scrollLeft = wrapper.scrollLeft + (btnRect.left - wrapperRect.left) - (wrapperRect.width / 2) + (btnRect.width / 2);
                        
                        wrapper.scrollTo({
                            left: Math.max(0, scrollLeft),
                            behavior: 'smooth'
                        });
                    }, 100);
                }
            }
            
            return true; // Дата успешно обновлена
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
            let lastScrollTime = 0;
            let velocity = 0;
            let lastScrollLeft = 0;
            let rafId = null;

            function smoothScroll() {
                if (Math.abs(velocity) < 0.5) {
                    velocity = 0;
                    return;
                }
                
                wrapper.scrollLeft += velocity;
                velocity *= 0.95;
                
                rafId = requestAnimationFrame(smoothScroll);
            }

            const start = (pageX) => {
                if (rafId) {
                    cancelAnimationFrame(rafId);
                    rafId = null;
                }
                velocity = 0;
                
                isDown = true;
                const rect = wrapper.getBoundingClientRect();
                startLeft = rect.left;
                startX = pageX - startLeft;
                scrollLeft = wrapper.scrollLeft;
                lastScrollLeft = scrollLeft;
                lastScrollTime = Date.now();
                wrapper.style.scrollBehavior = 'auto';
                wrapper.classList.add('dragging');
                wrapper.classList.remove('cursor-grab');
                wrapper.classList.add('cursor-grabbing');
            };

            const move = (pageX) => {
                if (!isDown) return;
                const x = pageX - startLeft;
                const walk = (x - startX) * 1.2;
                const newScrollLeft = scrollLeft - walk;
                
                const now = Date.now();
                const timeDelta = now - lastScrollTime;
                if (timeDelta > 0) {
                    velocity = (newScrollLeft - lastScrollLeft) / timeDelta * 16;
                }
                lastScrollLeft = newScrollLeft;
                lastScrollTime = now;
                
                wrapper.scrollLeft = newScrollLeft;
            };

            const end = () => {
                if (!isDown) return;
                isDown = false;
                wrapper.classList.remove('dragging', 'cursor-grabbing');
                wrapper.classList.add('cursor-grab');
                wrapper.style.scrollBehavior = 'smooth';
                
                if (Math.abs(velocity) > 1) {
                    smoothScroll();
                }
            };

            // Mouse события
            wrapper.addEventListener('mousedown', (e) => {
                if (e.target.closest('button, label, a, input')) return;
                if (e.button !== 0) return;
                
                e.preventDefault();
                e.stopPropagation();
                start(e.pageX);
            }, { passive: false });
            
            document.addEventListener('mouseup', (e) => {
                if (isDown) {
                    end();
                }
            });
            
            document.addEventListener('mousemove', (e) => {
                if (!isDown) return;
                e.preventDefault();
                e.stopPropagation();
                move(e.pageX);
            }, { passive: false });

            // Touch события
            let touchStartX = 0;
            let touchStartY = 0;
            let touchStartScrollLeft = 0;
            let isScrolling = false;
            
            wrapper.addEventListener('touchstart', (e) => {
                if (!e.touches || !e.touches[0]) return;
                
                const touch = e.touches[0];
                touchStartX = touch.pageX;
                touchStartY = touch.pageY;
                touchStartScrollLeft = wrapper.scrollLeft;
                isScrolling = false;
                
                const target = e.target;
                if (target.closest('button, label, a, input')) {
                    return;
                }
                
                start(touch.pageX);
            }, { passive: true });
            
            wrapper.addEventListener('touchend', (e) => {
                if (!isScrolling && e.target.closest('button, label')) {
                    return;
                }
                end();
            }, { passive: true });
            
            wrapper.addEventListener('touchcancel', (e) => {
                end();
            }, { passive: true });
            
            wrapper.addEventListener('touchmove', (e) => {
                if (!isDown) return;
                if (!e.touches || !e.touches[0]) return;
                
                const touch = e.touches[0];
                const deltaX = Math.abs(touch.pageX - touchStartX);
                const deltaY = Math.abs(touch.pageY - touchStartY);
                
                if (!isScrolling) {
                    if (deltaX > 10 || deltaY > 10) {
                        isScrolling = true;
                        if (deltaY > deltaX) {
                            end();
                            return;
                        }
                    } else {
                        return;
                    }
                }
                
                if (deltaX > deltaY) {
                    e.preventDefault();
                    move(touch.pageX);
                }
            }, { passive: false });

            // Wheel события
            let wheelTimeout = null;
            let wheelAccumulator = 0;
            
            wrapper.addEventListener('wheel', (e) => {
                if (isDown) return;
                if (e.ctrlKey || e.metaKey) return;
                
                let delta = 0;
                
                if (e.shiftKey) {
                    delta = e.deltaY;
                } else {
                    delta = e.deltaX !== 0 ? e.deltaX : e.deltaY;
                }
                
                if (Math.abs(delta) > 0) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    wheelAccumulator += delta;
                    
                    if (wheelTimeout) {
                        clearTimeout(wheelTimeout);
                    }
                    
                    wrapper.scrollLeft += wheelAccumulator;
                    wheelAccumulator *= 0.5;
                    
                    wheelTimeout = setTimeout(() => {
                        wheelAccumulator = 0;
                    }, 50);
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
            const openBtn = document.getElementById('open-calendar-btn');
            const openBtnDesktop = document.getElementById('open-calendar-btn-desktop');
            
            if (openBtn) {
                openBtn.addEventListener('click', openCalendar);
            }
            if (openBtnDesktop) {
                openBtnDesktop.addEventListener('click', openCalendar);
            }
            
            const closeBtn = document.getElementById('close-calendar-btn');
            if (closeBtn) {
                closeBtn.addEventListener('click', closeCalendar);
            }
            
            const backdrop = document.getElementById('calendar-backdrop');
            if (backdrop) {
                backdrop.addEventListener('click', closeCalendar);
            }
            
            const content = document.getElementById('calendar-content');
            if (content) {
                content.addEventListener('click', function(e) {
                    e.stopPropagation();
                });
            }
            
            const prevBtn = document.getElementById('prev-month-btn');
            const nextBtn = document.getElementById('next-month-btn');
            if (prevBtn) {
                prevBtn.addEventListener('click', () => changeMonth(-1));
            }
            if (nextBtn) {
                nextBtn.addEventListener('click', () => changeMonth(1));
            }
            
            const todayBtn = document.getElementById('select-today-btn');
            const tomorrowBtn = document.getElementById('select-tomorrow-btn');
            if (todayBtn) {
                todayBtn.addEventListener('click', selectToday);
            }
            if (tomorrowBtn) {
                tomorrowBtn.addEventListener('click', selectTomorrow);
            }
            
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    const modal = document.getElementById('calendar-modal');
                    if (modal && !modal.classList.contains('hidden')) {
                        closeCalendar();
                    }
                }
            });
        }

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

            const old = grid.querySelectorAll('.calendar-day');
            old.forEach(node => node.remove());

            const year = currentCalendarMonth.getFullYear();
            const month = currentCalendarMonth.getMonth();
            header.textContent = `${monthNames[month]} ${year}`;

            const firstDay = setMonth(year, month, 1);
            const dow = firstDay.getDay();
            const offset = dow === 0 ? 6 : dow - 1;
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
                btn.className = 'calendar-day p-2 sm:p-2.5 min-w-[36px] min-h-[36px] sm:min-w-[40px] sm:min-h-[40px] md:min-w-[44px] md:min-h-[44px] rounded-md text-sm font-semibold transition-all duration-200 flex items-center justify-center ' +
                    (isCurrentMonth ? 'text-slate-900 dark:text-white' : 'text-slate-400 dark:text-slate-500') +
                    (isToday ? ' bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400' : '') +
                    (isSelected ? ' bg-indigo-600 text-white dark:bg-indigo-500 ring-1 ring-indigo-200 dark:ring-indigo-800' : ' hover:bg-slate-100 dark:hover:bg-slate-700') +
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
                document.body.classList.add('modal-open');
                document.body.style.overflow = 'hidden';
                document.body.style.position = 'fixed';
                document.body.style.width = '100%';
                renderCalendar();
            }
        }

        function closeCalendar() {
            const modal = document.getElementById('calendar-modal');
            if (modal) {
                modal.classList.add('hidden');
                document.body.classList.remove('modal-open');
                document.body.style.overflow = '';
                document.body.style.position = '';
                document.body.style.width = '';
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

        function selectDate(dateISO) {
            selectedDateObj = startOfDay(parseISOToLocalDate(dateISO));
            
            closeCalendar();
            
            // Обновляем визуальное отображение даты
            updateSelectedDate(dateISO);
            
            // Загружаем слоты через API
            loadSlotsForDate(dateISO);
            
            // Обновляем URL без перезагрузки страницы
            const url = new URL(window.location.href);
            url.searchParams.set('date', dateISO);
            window.history.pushState({ date: dateISO }, '', url.toString());
        }
        
        // ========== Загрузка слотов через API ==========
        function loadSlotsForDate(dateISO) {
            const serviceId = {{ $service->id }};
            const masterId = {{ $master->id }};
            const locationId = {{ $location->id }};
            const businessSlug = '{{ $business->slug }}';
            
            // Проверяем, что master_id обязателен
            if (!masterId || masterId === 0) {
                console.error('Master ID is required');
                showSlotsError('Ошибка: не указан мастер. Пожалуйста, обновите страницу.');
                return;
            }
            
            const timeSlotsSection = document.getElementById('time-slots-section');
            const slotsCountBadge = document.getElementById('slots-count-badge');
            const submitBtn = document.getElementById('submit-btn');
            
            // Показываем индикатор загрузки
            if (timeSlotsSection) {
                const loadingHtml = `
                    <div class="px-4 pb-4">
                        <div class="flex items-center justify-center py-8">
                            <div class="flex flex-col items-center gap-2">
                                <i class="fa-solid fa-spinner fa-spin text-indigo-600 dark:text-indigo-400 text-xl"></i>
                                <span class="text-sm text-slate-600 dark:text-slate-400">Загрузка слотов...</span>
                            </div>
                        </div>
                    </div>
                `;
                timeSlotsSection.innerHTML = loadingHtml;
            }
            
            // Отключаем форму
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
            }
            
            // Формируем URL для API (master_id обязателен)
            const apiUrl = `/api/book/${businessSlug}/available-slots`;
            const params = new URLSearchParams({
                service_id: serviceId,
                date: dateISO,
                master_id: masterId, // Обязательный параметр для конкретного мастера
                location_id: locationId,
            });
            
            fetch(`${apiUrl}?${params.toString()}`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
                credentials: 'same-origin',
            })
            .then(response => {
                // Проверяем статус ответа
                if (!response.ok) {
                    // Пытаемся получить JSON с ошибкой
                    return response.json().then(errData => {
                        throw new Error(errData.message || `Ошибка ${response.status}: ${response.statusText}`);
                    }).catch(() => {
                        throw new Error(`Ошибка ${response.status}: ${response.statusText}`);
                    });
                }
                return response.json();
            })
            .then(data => {
                console.log('API Response:', data);
                
                // Проверяем структуру ответа
                if (!data || typeof data !== 'object') {
                    throw new Error('Некорректный формат ответа от сервера');
                }
                
                // Обрабатываем успешный ответ
                if (data.success === true) {
                    // slots может быть пустым массивом - это нормально
                    if (Array.isArray(data.slots)) {
                        updateTimeSlots(data.slots, data.preparation_time);
                    } else {
                        throw new Error('Слоты не найдены в ответе сервера');
                    }
                } else {
                    // Обрабатываем ошибки от API
                    let errorMessage = 'Не удалось загрузить слоты';
                    
                    if (data.message) {
                        errorMessage = data.message;
                    } else if (data.errors) {
                        // Ошибки валидации
                        const errorMessages = Object.values(data.errors).flat();
                        errorMessage = errorMessages.join(', ') || errorMessage;
                    }
                    
                    throw new Error(errorMessage);
                }
            })
            .catch(error => {
                console.error('Ошибка при загрузке слотов:', error);
                const errorMessage = error.message || 'Не удалось загрузить слоты. Пожалуйста, обновите страницу.';
                showSlotsError(errorMessage);
            });
        }
        
        // ========== Обновление блока со слотами ==========
        function updateTimeSlots(slots, preparationTime = null) {
            const timeSlotsSection = document.getElementById('time-slots-section');
            const slotsCountBadge = document.getElementById('slots-count-badge');
            const submitBtn = document.getElementById('submit-btn');
            
            if (!timeSlotsSection) return;
            
            if (slots.length === 0) {
                // Показываем уведомление об отсутствии слотов
                const notificationHtml = `
                    <div class="px-4 pb-4" id="time-slots-container">
                        <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-xl p-4">
                            <div class="flex items-start gap-3">
                                <div class="flex-shrink-0 w-8 h-8 rounded-lg bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center">
                                    <i class="fa-solid fa-info-circle text-amber-600 dark:text-amber-400 text-sm"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h4 class="text-sm font-semibold text-amber-900 dark:text-amber-200 mb-1">
                                        Нет свободных мест на эту дату
                                    </h4>
                                    <p class="text-xs text-amber-800 dark:text-amber-300">
                                        На выбранную дату все временные слоты заняты. Пожалуйста, выберите другую дату.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                timeSlotsSection.innerHTML = notificationHtml;
                
                // Скрываем счетчик слотов
                if (slotsCountBadge) {
                    slotsCountBadge.style.display = 'none';
                }
                
                // Отключаем форму
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
                }
                
                return;
            }
            
            // Показываем счетчик слотов
            if (slotsCountBadge) {
                slotsCountBadge.textContent = `${slots.length} ${slots.length === 1 ? 'слот' : 'слотов'}`;
                slotsCountBadge.style.display = '';
            }
            
            // Создаем wrapper для скролла если его нет
            let timeSlotsWrapper = document.getElementById('time-slots-wrapper');
            if (!timeSlotsWrapper) {
                const wrapperHtml = `
                    <div class="overflow-x-auto scrollbar-hide scroll-smooth select-none px-4 pb-3 snap-x snap-mandatory cursor-grab active:cursor-grabbing" id="time-slots-wrapper" style="scroll-behavior: smooth; -webkit-overflow-scrolling: touch; overscroll-behavior-x: contain;">
                        <div class="flex gap-2 pb-1.5 flex-nowrap" id="time-slots-container" style="min-width: max-content;"></div>
                    </div>
                `;
                timeSlotsSection.innerHTML = `
                    <div class="absolute left-0 top-0 bottom-0 w-8 bg-gradient-to-r from-white dark:from-slate-900 to-transparent pointer-events-none z-10 opacity-0 transition-opacity duration-300" id="time-fade-left"></div>
                    <div class="absolute right-0 top-0 bottom-0 w-8 bg-gradient-to-l from-white dark:from-slate-900 to-transparent pointer-events-none z-10 opacity-0 transition-opacity duration-300" id="time-fade-right"></div>
                    ${wrapperHtml}
                `;
                timeSlotsWrapper = document.getElementById('time-slots-wrapper');
            }
            
            const timeSlotsContainer = document.getElementById('time-slots-container');
            if (!timeSlotsContainer) return;
            
            // Очищаем контейнер
            timeSlotsContainer.innerHTML = '';
            
            // Генерируем HTML для слотов
            slots.forEach(slot => {
                const label = document.createElement('label');
                label.className = 'time-slot-label flex-shrink-0 w-20 sm:w-16 md:w-20 p-3 sm:p-2 rounded-xl border-2 border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-center cursor-pointer transition-all duration-200 hover:border-indigo-300 dark:hover:border-indigo-600 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 hover:shadow-md active:scale-95 snap-start min-h-[52px] sm:min-h-0';
                
                const input = document.createElement('input');
                input.type = 'radio';
                input.name = 'time';
                input.value = slot;
                input.required = true;
                input.className = 'sr-only time-radio';
                
                const span = document.createElement('span');
                span.className = 'text-sm sm:text-xs font-semibold text-slate-900 dark:text-white leading-tight';
                span.textContent = slot;
                
                label.appendChild(input);
                label.appendChild(span);
                timeSlotsContainer.appendChild(label);
            });
            
            // Показываем wrapper
            timeSlotsWrapper.style.display = '';
            
            // Восстанавливаем fade эффекты
            setTimeout(() => {
                updateFadeEffects('time-slots-wrapper', 'time-fade-left', 'time-fade-right');
            }, 100);
            
            // Инициализируем обработчики для новых слотов
            initTimeSlots();
            initTimeSlotsDrag();
            
            // Включаем форму
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
            }
        }
        
        // ========== Показ ошибки при загрузке слотов ==========
        function showSlotsError(message) {
            const timeSlotsContainer = document.getElementById('time-slots-container');
            const submitBtn = document.getElementById('submit-btn');
            
            if (timeSlotsContainer) {
                const errorHtml = `
                    <div class="px-4 pb-4">
                        <div class="bg-rose-50 dark:bg-rose-900/20 border border-rose-200 dark:border-rose-800 rounded-xl p-4">
                            <div class="flex items-start gap-3">
                                <div class="flex-shrink-0 w-8 h-8 rounded-lg bg-rose-100 dark:bg-rose-900/30 flex items-center justify-center">
                                    <i class="fa-solid fa-exclamation-circle text-rose-600 dark:text-rose-400 text-sm"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h4 class="text-sm font-semibold text-rose-900 dark:text-rose-200 mb-1">
                                        Ошибка загрузки
                                    </h4>
                                    <p class="text-xs text-rose-800 dark:text-rose-300">
                                        ${message}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                timeSlotsContainer.innerHTML = errorHtml;
            }
            
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
            }
        }
        
        // ========== Подсветка выбранного времени ==========
        function initTimeSlots() {
            const container = document.getElementById('time-slots-container');
            const wrapper = document.getElementById('time-slots-wrapper');
            if (!container) return;
            
            function updateHighlight() {
                const allLabels = container.querySelectorAll('.time-slot-label');
                allLabels.forEach(label => {
                    const radio = label.querySelector('.time-radio');
                    if (radio && radio.checked) {
                        label.classList.remove('border-slate-200', 'dark:border-slate-700', 'bg-white', 'dark:bg-slate-800', 'hover:border-indigo-300', 'dark:hover:border-indigo-600', 'hover:bg-indigo-50', 'dark:hover:bg-indigo-900/20');
                        label.classList.add('border-indigo-400', 'dark:border-indigo-500', 'bg-indigo-50', 'dark:bg-indigo-900/20', 'ring-1', 'ring-indigo-200', 'dark:ring-indigo-800', 'shadow-sm');
                        
                        // Прокручиваем к выбранному слоту
                        if (wrapper) {
                            setTimeout(() => {
                                const labelRect = label.getBoundingClientRect();
                                const wrapperRect = wrapper.getBoundingClientRect();
                                const scrollLeft = wrapper.scrollLeft + (labelRect.left - wrapperRect.left) - (wrapperRect.width / 2) + (labelRect.width / 2);
                                
                                wrapper.scrollTo({
                                    left: Math.max(0, scrollLeft),
                                    behavior: 'smooth'
                                });
                            }, 100);
                        }
                    } else {
                        label.classList.remove('border-indigo-400', 'dark:border-indigo-500', 'bg-indigo-50', 'dark:bg-indigo-900/20', 'ring-1', 'ring-indigo-200', 'dark:ring-indigo-800', 'shadow-sm');
                        label.classList.add('border-slate-200', 'dark:border-slate-700', 'bg-white', 'dark:bg-slate-800', 'hover:border-indigo-300', 'dark:hover:border-indigo-600', 'hover:bg-indigo-50', 'dark:hover:bg-indigo-900/20');
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
        }
        
        // ========== Обработка отправки формы ==========
        const form = document.querySelector('form');
        const submitBtn = document.getElementById('submit-btn');
        let isSubmitting = false; // Флаг для предотвращения повторной отправки
        
        if (form && submitBtn) {
            form.addEventListener('submit', function(e) {
                // Предотвращаем повторную отправку
                if (isSubmitting) {
                    e.preventDefault();
                    e.stopPropagation();
                    return false;
                }
                
                // Проверяем, что дата и время выбраны
                const dateInput = document.getElementById('selected-date-input');
                const timeRadio = document.querySelector('input[name="time"]:checked');
                
                if (!dateInput || !dateInput.value) {
                    e.preventDefault();
                    alert('Пожалуйста, выберите дату');
                    return false;
                }
                
                if (!timeRadio) {
                    e.preventDefault();
                    alert('Пожалуйста, выберите время');
                    return false;
                }
                
                // Устанавливаем флаг отправки
                isSubmitting = true;
                
                // Показываем индикатор загрузки
                submitBtn.disabled = true;
                const submitText = submitBtn.querySelector('.submit-text');
                const submitLoading = submitBtn.querySelector('.submit-loading');
                if (submitText) submitText.classList.add('hidden');
                if (submitLoading) submitLoading.classList.remove('hidden');
                
                // Если форма не отправилась в течение 10 секунд, разрешаем повторную попытку
                setTimeout(() => {
                    if (isSubmitting) {
                        isSubmitting = false;
                        submitBtn.disabled = false;
                        if (submitText) submitText.classList.remove('hidden');
                        if (submitLoading) submitLoading.classList.add('hidden');
                    }
                }, 10000);
            });
        }
        
        // ========== Fade эффекты для скролла ==========
        function updateFadeEffects(wrapperId, fadeLeftId, fadeRightId) {
            const wrapper = document.getElementById(wrapperId);
            const fadeLeft = document.getElementById(fadeLeftId);
            const fadeRight = document.getElementById(fadeRightId);
            
            if (!wrapper || !fadeLeft || !fadeRight) return;
            
            function checkScroll() {
                const { scrollLeft, scrollWidth, clientWidth } = wrapper;
                const isAtStart = scrollLeft <= 5;
                const isAtEnd = scrollLeft >= scrollWidth - clientWidth - 5;
                
                fadeLeft.style.opacity = isAtStart ? '0' : '1';
                fadeRight.style.opacity = isAtEnd ? '0' : '1';
            }
            
            wrapper.addEventListener('scroll', checkScroll);
            checkScroll(); // Проверяем при загрузке
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
                // Убираем все нецифровые символы кроме +
                let value = this.value.replace(/[^\d+]/g, '');
                
                // Если начинается с 375, добавляем +
                if (value.startsWith('375')) {
                    value = '+' + value;
                } else if (!value.startsWith('+375') && value.length > 0) {
                    // Если не начинается с +375, добавляем префикс
                    value = '+375' + value.replace(/\+/g, '');
                }
                
                // Ограничиваем до 13 символов (+375 + 9 цифр)
                if (value.length > 13) {
                    value = value.substring(0, 13);
                }
                
                this.value = value;
            });
            
            // Перед отправкой формы убираем форматирование (оставляем только цифры и +)
            const form = document.getElementById('appointment-form');
            if (form) {
                form.addEventListener('submit', function(e) {
                    if (phoneInput.value) {
                        // Сохраняем только +375 и цифры
                        phoneInput.value = phoneInput.value.replace(/[^\d+]/g, '');
                    }
                });
            }
        }
    })();
</script>
@endpush
@endsection
