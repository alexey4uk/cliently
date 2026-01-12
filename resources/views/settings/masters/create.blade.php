@extends('layouts.user')

@section('title', 'Добавление мастера - Cliently')
@section('page-title', 'Добавление мастера')
@section('page-description', 'Добавьте нового мастера для вашего бизнеса')

@push('breadcrumbs')
    <x-breadcrumbs :items="[
        ['title' => 'Настройки', 'url' => route('settings.index')],
        ['title' => 'Мастера', 'url' => route('settings.masters')],
        ['title' => 'Добавление', 'url' => null]
    ]" />
@endpush

@section('content')

<form method="POST" action="{{ route('settings.masters.store') }}" class="space-y-4 md:space-y-6">
    @csrf

    <!-- Основная информация -->
    <div class="bg-white dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
        <div class="px-4 md:px-6 py-3 md:py-4 border-b border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/50">
            <div class="flex items-center gap-2">
                <div class="h-8 w-8 rounded-lg bg-indigo-100 dark:bg-indigo-500/20 flex items-center justify-center">
                    <i class="fa-solid fa-user text-indigo-600 dark:text-indigo-400 text-xs"></i>
                </div>
                <h2 class="text-sm font-semibold text-slate-900 dark:text-white">
                    Основная информация
                </h2>
            </div>
        </div>
        <div class="p-4 md:p-6 space-y-4 md:space-y-5">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-5">
                <div>
                    <label for="first_name" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5 sm:mb-2">
                        Имя <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-2.5 sm:pl-3 flex items-center pointer-events-none">
                            <i class="fa-solid fa-user text-slate-400 text-sm"></i>
                        </div>
                        <input type="text" id="first_name" name="first_name" required value="{{ old('first_name') }}"
                               class="w-full pl-9 sm:pl-10 pr-3 py-2 sm:py-2.5 text-sm rounded-lg border {{ $errors->has('first_name') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-indigo-500' }} bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:border-transparent transition-colors"
                               placeholder="Введите имя"
                               autofocus>
                    </div>
                    @error('first_name')
                    <p class="mt-1.5 sm:mt-2 text-xs text-rose-600 dark:text-rose-400 flex items-center gap-1">
                        <i class="fa-solid fa-circle-exclamation text-xs"></i>
                        <span>{{ $message }}</span>
                    </p>
                    @enderror
                </div>

                <div>
                    <label for="last_name" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5 sm:mb-2">
                        Фамилия
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-2.5 sm:pl-3 flex items-center pointer-events-none">
                            <i class="fa-solid fa-user text-slate-400 text-sm"></i>
                        </div>
                        <input type="text" id="last_name" name="last_name" value="{{ old('last_name') }}"
                               class="w-full pl-9 sm:pl-10 pr-3 py-2 sm:py-2.5 text-sm rounded-lg border {{ $errors->has('last_name') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-indigo-500' }} bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:border-transparent transition-colors"
                               placeholder="Введите фамилию">
                    </div>
                    @error('last_name')
                    <p class="mt-1.5 sm:mt-2 text-xs text-rose-600 dark:text-rose-400 flex items-center gap-1">
                        <i class="fa-solid fa-circle-exclamation text-xs"></i>
                        <span>{{ $message }}</span>
                    </p>
                    @enderror
                </div>
            </div>

            <div>
                <label for="specialization" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5 sm:mb-2">
                    Специализация <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-2.5 sm:pl-3 flex items-center pointer-events-none">
                        <i class="fa-solid fa-briefcase text-slate-400 text-sm"></i>
                    </div>
                    <input type="text" id="specialization" name="specialization" required value="{{ old('specialization') }}"
                           class="w-full pl-9 sm:pl-10 pr-3 py-2 sm:py-2.5 text-sm rounded-lg border {{ $errors->has('specialization') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-indigo-500' }} bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:border-transparent transition-colors"
                           placeholder="Например: Парикмахер, Массажист">
                </div>
                @error('specialization')
                <p class="mt-1.5 sm:mt-2 text-xs text-rose-600 dark:text-rose-400 flex items-center gap-1">
                    <i class="fa-solid fa-circle-exclamation text-xs"></i>
                    <span>{{ $message }}</span>
                </p>
                @enderror
            </div>

            <div>
                <label for="description" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5 sm:mb-2">
                    Описание
                </label>
                <textarea id="description" name="description" rows="4"
                          class="w-full px-3 py-2.5 text-sm rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-colors resize-none"
                          placeholder="Расскажите о мастере...">{{ old('description') }}</textarea>
            </div>
        </div>
    </div>

    <!-- Контактная информация -->
    <div class="bg-white dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
        <div class="px-4 md:px-6 py-3 md:py-4 border-b border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/50">
            <div class="flex items-center gap-2">
                <div class="h-8 w-8 rounded-lg bg-emerald-100 dark:bg-emerald-500/20 flex items-center justify-center">
                    <i class="fa-solid fa-phone text-emerald-600 dark:text-emerald-400 text-xs"></i>
                </div>
                <h2 class="text-sm font-semibold text-slate-900 dark:text-white">
                    Контактная информация
                </h2>
            </div>
        </div>
        <div class="p-4 md:p-6 space-y-4 md:space-y-5">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-5">
                <div>
                    <label for="phone" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5 sm:mb-2">
                        Телефон <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-2.5 sm:pl-3 flex items-center pointer-events-none">
                            <i class="fa-solid fa-phone text-slate-400 text-sm"></i>
                        </div>
                        <input type="tel" id="phone" name="phone" required value="{{ old('phone') }}"
                               class="w-full pl-9 sm:pl-10 pr-3 py-2 sm:py-2.5 text-sm rounded-lg border {{ $errors->has('phone') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-indigo-500' }} bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:border-transparent transition-colors"
                               placeholder="+375XXXXXXXXX">
                    </div>
                    <p class="mt-1.5 sm:mt-2 text-xs text-slate-500 dark:text-slate-400">
                        Формат: +375XXXXXXXXX. Коды: 29, 33, 44, 25
                    </p>
                    @error('phone')
                    <p class="mt-1.5 sm:mt-2 text-xs text-rose-600 dark:text-rose-400 flex items-center gap-1">
                        <i class="fa-solid fa-circle-exclamation text-xs"></i>
                        <span>{{ $message }}</span>
                    </p>
                    @enderror
                    <p id="phoneError" class="mt-1.5 sm:mt-2 text-xs text-rose-600 dark:text-rose-400 hidden flex items-center gap-1">
                        <i class="fa-solid fa-circle-exclamation text-xs"></i>
                        <span></span>
                    </p>
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5 sm:mb-2">
                        Почта
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-2.5 sm:pl-3 flex items-center pointer-events-none">
                            <i class="fa-solid fa-envelope text-slate-400 text-sm"></i>
                        </div>
                        <input type="email" id="email" name="email" value="{{ old('email') }}"
                               class="w-full pl-9 sm:pl-10 pr-3 py-2 sm:py-2.5 text-sm rounded-lg border {{ $errors->has('email') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-indigo-500' }} bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:border-transparent transition-colors"
                               placeholder="example@mail.com">
                    </div>
                    @error('email')
                    <p class="mt-1.5 sm:mt-2 text-xs text-rose-600 dark:text-rose-400 flex items-center gap-1">
                        <i class="fa-solid fa-circle-exclamation text-xs"></i>
                        <span>{{ $message }}</span>
                    </p>
                    @enderror
                </div>
            </div>
        </div>
    </div>

    <!-- Локации и услуги -->
    @if($locations->count() > 0 || $services->count() > 0)
    <div class="bg-white dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
        <div class="px-4 md:px-6 py-3 md:py-4 border-b border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/50">
            <div class="flex items-center gap-2">
                <div class="h-8 w-8 rounded-lg bg-purple-100 dark:bg-purple-500/20 flex items-center justify-center">
                    <i class="fa-solid fa-link text-purple-600 dark:text-purple-400 text-xs"></i>
                </div>
                <h2 class="text-sm font-semibold text-slate-900 dark:text-white">
                    Связи
                </h2>
            </div>
        </div>
        <div class="p-4 md:p-6 space-y-4 md:space-y-5">
            @if($locations->count() > 0)
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                    Локации
                </label>
                <div class="space-y-2">
                    @foreach($locations as $location)
                    <label class="flex items-center gap-2 cursor-pointer p-3 rounded-lg border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                        <input type="checkbox" name="location_ids[]" value="{{ $location->id }}"
                               class="rounded border-slate-300 dark:border-slate-700 text-indigo-600 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-0"
                               {{ in_array($location->id, old('location_ids', [])) ? 'checked' : '' }}>
                        <span class="text-sm text-slate-700 dark:text-slate-300">{{ $location->name }}</span>
                    </label>
                    @endforeach
                </div>
            </div>
            @endif

            @if($services->count() > 0)
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                    Услуги
                </label>
                <div class="space-y-2">
                    @foreach($services as $service)
                    <label class="flex items-center gap-2 cursor-pointer p-3 rounded-lg border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                        <input type="checkbox" name="service_ids[]" value="{{ $service->id }}"
                               class="rounded border-slate-300 dark:border-slate-700 text-indigo-600 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-0"
                               {{ in_array($service->id, old('service_ids', [])) ? 'checked' : '' }}>
                        <span class="text-sm text-slate-700 dark:text-slate-300">{{ $service->name }}</span>
                    </label>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
    @endif

    <!-- Время работы -->
    <div class="bg-white dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
        <div class="px-4 md:px-6 py-3 md:py-4 border-b border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/50">
            <div class="flex items-center gap-2">
                <div class="h-8 w-8 rounded-lg bg-amber-100 dark:bg-amber-500/20 flex items-center justify-center">
                    <i class="fa-solid fa-clock text-amber-600 dark:text-amber-400 text-xs"></i>
                </div>
                <h2 class="text-sm font-semibold text-slate-900 dark:text-white">
                    Время работы
                </h2>
            </div>
        </div>
        <div class="p-4 md:p-6 space-y-4 md:space-y-5">
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                    График работы <span class="text-rose-500">*</span>
                </label>
                <div class="space-y-3">
                    <!-- Чекбокс круглосуточно -->
                    <label class="flex items-center gap-2 cursor-pointer p-3 rounded-lg border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                        <input type="checkbox" id="workingHours24" name="working_hours[24_hours]" value="1"
                               class="rounded border-slate-300 dark:border-slate-700 text-indigo-600 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-0"
                               {{ old('working_hours.24_hours') ? 'checked' : '' }}>
                        <span class="text-sm text-slate-700 dark:text-slate-300 font-medium">Круглосуточно</span>
                    </label>

                    <!-- Поля времени работы -->
                    <div id="workingHoursFields" class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-5">
                        <div>
                            <label for="workingHoursFrom" class="block text-sm text-slate-500 dark:text-slate-400 mb-1.5 font-medium">С</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-2.5 sm:pl-3 flex items-center pointer-events-none">
                                    <i class="fa-solid fa-clock text-slate-400 text-sm"></i>
                                </div>
                                <input type="time" name="working_hours[from]" id="workingHoursFrom" required
                                       value="{{ old('working_hours.from', '09:00') }}"
                                       class="w-full pl-9 sm:pl-10 pr-3 py-2 sm:py-2.5 text-sm rounded-lg border {{ $errors->has('working_hours.from') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-indigo-500' }} bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:border-transparent transition-colors">
                            </div>
                            @error('working_hours.from')
                            <p class="mt-1.5 text-xs text-rose-600 dark:text-rose-400 flex items-center gap-1">
                                <i class="fa-solid fa-circle-exclamation text-xs"></i>
                                <span>{{ $message }}</span>
                            </p>
                            @enderror
                        </div>

                        <div>
                            <label for="workingHoursTo" class="block text-sm text-slate-500 dark:text-slate-400 mb-1.5 font-medium">До</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-2.5 sm:pl-3 flex items-center pointer-events-none">
                                    <i class="fa-solid fa-clock text-slate-400 text-sm"></i>
                                </div>
                                <input type="time" name="working_hours[to]" id="workingHoursTo" required
                                       value="{{ old('working_hours.to', '18:00') }}"
                                       class="w-full pl-9 sm:pl-10 pr-3 py-2 sm:py-2.5 text-sm rounded-lg border {{ $errors->has('working_hours.to') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-indigo-500' }} bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:border-transparent transition-colors">
                            </div>
                            @error('working_hours.to')
                            <p class="mt-1.5 text-xs text-rose-600 dark:text-rose-400 flex items-center gap-1">
                                <i class="fa-solid fa-circle-exclamation text-xs"></i>
                                <span>{{ $message }}</span>
                            </p>
                            @enderror
                        </div>
                    </div>

                    <!-- Выходные дни -->
                    <div x-data="{
                        open: false,
                        selectedDays: new Set({{ json_encode(old('working_hours.days_off', []), JSON_HEX_TAG) }}),
                        days: {
                            'monday': 'Понедельник',
                            'tuesday': 'Вторник',
                            'wednesday': 'Среда',
                            'thursday': 'Четверг',
                            'friday': 'Пятница',
                            'saturday': 'Суббота',
                            'sunday': 'Воскресенье'
                        },
                        init() {
                            // Инициализируем выбранные дни из старых значений формы
                            const oldDays = {{ json_encode(old('working_hours.days_off', []), JSON_HEX_TAG) }};
                            oldDays.forEach(day => this.selectedDays.add(day));
                        },
                        toggleDay(day) {
                            if (this.selectedDays.has(day)) {
                                this.selectedDays.delete(day);
                            } else {
                                this.selectedDays.add(day);
                            }
                        },
                        removeDay(day) {
                            this.selectedDays.delete(day);
                        }
                    }">
                        <label class="block text-sm text-slate-500 dark:text-slate-400 mb-2 font-medium">Выходные дни</label>
                        
                        <!-- Кнопка для раскрытия блока -->
                        <button type="button" @click="open = !open" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-3 md:px-4 py-2 md:py-2.5 text-sm font-medium text-slate-700 dark:text-slate-300 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
                            <i class="fa-solid transition-transform duration-200" :class="open ? 'fa-chevron-up' : 'fa-plus'"></i>
                            <span>Добавить выходные дни</span>
                        </button>
                        
                        <!-- Раскрывающийся блок с чекбоксами -->
                        <div x-show="open" x-cloak x-transition class="mt-3 p-4 bg-slate-50 dark:bg-slate-800/50 rounded-lg border border-slate-200 dark:border-slate-700">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                                <template x-for="(dayName, dayKey) in days" :key="dayKey">
                                    <label @click.prevent="toggleDay(dayKey)" class="flex items-center gap-2 cursor-pointer p-2 rounded-lg border border-slate-200 dark:border-slate-700 hover:bg-white dark:hover:bg-slate-800 transition-colors" :class="selectedDays.has(dayKey) ? 'bg-indigo-50 dark:bg-indigo-500/10 border-indigo-200 dark:border-indigo-700' : ''">
                                        <input type="checkbox" class="days-off-checkbox rounded border-slate-300 dark:border-slate-700 text-indigo-600 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-0 pointer-events-none"
                                               :checked="selectedDays.has(dayKey)">
                                        <span class="text-sm text-slate-700 dark:text-slate-300" x-text="dayName"></span>
                                    </label>
                                </template>
                            </div>
                        </div>
                        
                        <!-- Скрытые input'ы для отправки данных -->
                        <template x-for="day in Array.from(selectedDays)" :key="day">
                            <input type="hidden" name="working_hours[days_off][]" :value="day">
                        </template>
                        
                        <!-- Теги выбранных дней -->
                        <div x-show="selectedDays.size > 0" x-cloak class="flex flex-wrap gap-2 mt-3">
                            <template x-for="day in Array.from(selectedDays)" :key="day">
                                <div class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-indigo-100 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300 rounded-lg text-xs font-medium">
                                    <span x-text="days[day]"></span>
                                    <button type="button" @click="removeDay(day)" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-200 transition-colors">
                                        <i class="fa-solid fa-times text-xs"></i>
                                    </button>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
                @error('working_hours')
                <p class="mt-1.5 sm:mt-2 text-xs text-rose-600 dark:text-rose-400 flex items-center gap-1">
                    <i class="fa-solid fa-circle-exclamation text-xs"></i>
                    <span>{{ $message }}</span>
                </p>
                @enderror
            </div>
        </div>
    </div>

    <!-- Кнопки действий -->
    <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 pt-4 border-t border-slate-200 dark:border-slate-800">
        <a href="{{ route('settings.masters') }}" 
           class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-3 md:px-4 py-2 md:py-2.5 text-sm font-medium text-slate-700 dark:text-slate-300 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
            <i class="fa-solid fa-arrow-left text-xs"></i>
            <span>Отмена</span>
        </a>
        <button type="submit"
                class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-3 md:px-4 py-2 md:py-2.5 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 shadow-sm">
            <i class="fa-solid fa-check text-xs"></i>
            <span>Сохранить</span>
        </button>
    </div>
</form>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        setupPhoneInput();
        setupWorkingHours();
    });

    function setupPhoneInput() {
        const phoneInput = document.getElementById('phone');
        if (!phoneInput) return;

        const validOperatorCodes = ['29', '33', '44', '25'];
        const PHONE_OPERATOR_ERROR = 'Неверный код оператора. Допустимые: 29, 33, 44, 25';
        const PHONE_INCOMPLETE_ERROR = 'Введите полный номер телефона (9 цифр после +375)';
        const PHONE_REQUIRED_DIGITS = 9;

        function extractPhoneDigits(value) {
            return value.substring(4).replace(/\D/g, '');
        }

        function resetPhoneToPrefix(input) {
            input.value = '+375';
            setCursorPosition(input, 5);
            showPhoneError(PHONE_OPERATOR_ERROR);
        }

        function setCursorPosition(input, position) {
            requestAnimationFrame(() => {
                const safePosition = Math.max(0, Math.min(position, input.value.length));
                try {
                    input.setSelectionRange(safePosition, safePosition);
                } catch (e) {
                    console.warn('Не удалось установить позицию курсора:', e);
                }
            });
        }

        function canBeValidOperatorCode(firstDigit) {
            return validOperatorCodes.some(code => code.startsWith(firstDigit));
        }

        function validateOperatorCode(digits, input) {
            if (digits.length < 2) {
                if (digits.length === 1) {
                    const firstDigit = digits;
                    if (!canBeValidOperatorCode(firstDigit)) {
                        resetPhoneToPrefix(input);
                        return false;
                    }
                }
                return true;
            }

            const operatorCode = digits.substring(0, 2);
            if (!validOperatorCodes.includes(operatorCode)) {
                const firstDigit = digits.substring(0, 1);
                if (!canBeValidOperatorCode(firstDigit)) {
                    resetPhoneToPrefix(input);
                    return false;
                } else {
                    input.value = '+375' + firstDigit;
                    showPhoneError(PHONE_OPERATOR_ERROR);
                    setCursorPosition(input, 5 + firstDigit.length);
                    return false;
                }
            }

            return true;
        }

        function showPhoneError(message) {
            const errorElement = document.getElementById('phoneError');
            if (errorElement && phoneInput) {
                errorElement.querySelector('span').textContent = message;
                errorElement.classList.remove('hidden');
                phoneInput.classList.add('border-rose-500');
            }
        }

        function hidePhoneError() {
            const errorElement = document.getElementById('phoneError');
            if (errorElement && phoneInput) {
                errorElement.classList.add('hidden');
                phoneInput.classList.remove('border-rose-500');
            }
        }

        phoneInput.addEventListener('focus', function(e) {
            if (!e.target.value || !e.target.value.startsWith('+375')) {
                e.target.value = '+375';
                setCursorPosition(e.target, 4);
            }
        });

        phoneInput.addEventListener('keydown', function(e) {
            const selectionStart = e.target.selectionStart;
            const selectionEnd = e.target.selectionEnd;
            
            if (selectionStart < 5 || selectionEnd < 5) {
                if (e.key === 'Backspace' || e.key === 'Delete') {
                    e.preventDefault();
                    setCursorPosition(e.target, 5);
                    return false;
                }
            }
        });

        phoneInput.addEventListener('input', function(e) {
            let value = e.target.value;
            
            if (!value.startsWith('+375')) {
                value = '+375';
            }
            
            const digits = extractPhoneDigits(value);
            
            if (!validateOperatorCode(digits, e.target)) {
                return;
            }
            
            const limitedDigits = digits.substring(0, PHONE_REQUIRED_DIGITS);
            e.target.value = '+375' + limitedDigits;
            
            if (limitedDigits.length === PHONE_REQUIRED_DIGITS) {
                hidePhoneError();
            }
            
            const cursorPosition = Math.max(5, e.target.value.length);
            setCursorPosition(e.target, cursorPosition);
        });

        phoneInput.addEventListener('blur', function(e) {
            const value = e.target.value;
            const digits = extractPhoneDigits(value);
            
            if (value.startsWith('+375') && digits.length < PHONE_REQUIRED_DIGITS) {
                showPhoneError(PHONE_INCOMPLETE_ERROR);
            } else if (digits.length === PHONE_REQUIRED_DIGITS) {
                hidePhoneError();
            }
        });
    }

    function setupWorkingHours() {
        const checkbox24 = document.getElementById('workingHours24');
        const fieldsContainer = document.getElementById('workingHoursFields');
        const fromInput = document.getElementById('workingHoursFrom');
        const toInput = document.getElementById('workingHoursTo');

        if (!checkbox24 || !fieldsContainer || !fromInput || !toInput) return;

        function toggleFields() {
            if (checkbox24.checked) {
                fieldsContainer.classList.add('opacity-50', 'pointer-events-none');
                fromInput.disabled = true;
                toInput.disabled = true;
                fromInput.removeAttribute('required');
                toInput.removeAttribute('required');
                fromInput.value = '00:00';
                toInput.value = '00:00';
            } else {
                fieldsContainer.classList.remove('opacity-50', 'pointer-events-none');
                fromInput.disabled = false;
                toInput.disabled = false;
                fromInput.setAttribute('required', 'required');
                toInput.setAttribute('required', 'required');
            }
        }

        checkbox24.addEventListener('change', toggleFields);
        toggleFields();
    }
</script>
@endpush