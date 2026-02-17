@extends('appointments.public.layout')

@section('title', 'Выбор времени')

@section('content')
    <div class="w-full">
        <x-breadcrumbs-public-book :business="$business" currentStep="time" :location="$location" :service="$service" />

        <div class="mb-4">
            <h2 class="text-xl font-bold text-slate-900 dark:text-white tracking-tight">Выберите время</h2>
        </div>

        <script type="application/json" id="public-booking-select-time-config">@json(['datesWithSlots' => $datesWithSlots, 'date' => $date, 'currentDateHasSlots' => count($availableSlots) > 0])</script>
        <form method="POST" action="{{ route('public.appointments.store', $business->slug) }}" id="appointment-form"
            class="space-y-6">
                @csrf
                <input type="hidden" name="location_id" value="{{ $location->id }}">
                <input type="hidden" name="service_id" value="{{ $service->id }}">
                @if(isset($master) && $master)
                <input type="hidden" name="master_id" value="{{ $master->id }}">
                @endif
                <input type="hidden" name="date" value="{{ $date }}" id="selected-date-input">
                <input type="hidden" name="time" value="" id="selected-time-input">

                <!-- ВЫБОР ДАТЫ -->
                <div
                    class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-700 p-4 sm:p-5">
                    <div class="flex items-center gap-3 mb-4">
                        <div
                            class="w-10 h-10 sm:w-9 sm:h-9 rounded-lg bg-indigo-100 dark:bg-indigo-500/20 flex items-center justify-center text-indigo-600 dark:text-indigo-400">
                            <i class="fa-solid fa-calendar-day text-base sm:text-sm"></i>
                        </div>
                        <h2 class="text-base font-bold text-slate-900 dark:text-white tracking-tight">Дата визита</h2>
                    </div>

                    @php $selectedDateCarbon = \Carbon\Carbon::parse($date); @endphp
                    <button type="button" id="toggle-date-selector-btn"
                        class="w-full flex items-center justify-between p-3 sm:p-4 min-h-[56px] sm:min-h-0 bg-slate-50 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-800 rounded-xl transition-all hover:bg-slate-100 dark:hover:bg-slate-800 group outline-none touch-manipulation">
                        <div class="flex items-center gap-3 sm:gap-4 text-left">
                            <div
                                class="flex flex-col items-center justify-center w-11 h-11 sm:w-12 sm:h-12 bg-white dark:bg-slate-950 rounded-lg border border-slate-200 dark:border-slate-700 group-hover:border-indigo-500 transition-colors">
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
                    <div id="calendar-container" class="hidden mt-4 pt-4 border-t border-slate-100 dark:border-slate-800">
                        <div class="flex items-center justify-between mb-3 px-1">
                            <button type="button" id="prev-month"
                                class="min-w-[44px] min-h-[44px] w-9 h-9 sm:w-10 sm:h-10 flex items-center justify-center rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-600 dark:text-slate-400 touch-manipulation"><i
                                    class="fa-solid fa-chevron-left text-sm"></i></button>
                            <h3 id="current-month-year"
                                class="text-xs sm:text-sm font-bold uppercase tracking-wider text-slate-900 dark:text-white"></h3>
                            <button type="button" id="next-month"
                                class="min-w-[44px] min-h-[44px] w-9 h-9 sm:w-10 sm:h-10 flex items-center justify-center rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-600 dark:text-slate-400 touch-manipulation"><i
                                    class="fa-solid fa-chevron-right text-sm"></i></button>
                        </div>
                        <div
                            class="grid grid-cols-7 gap-1 text-center text-[11px] font-bold text-slate-400 dark:text-slate-500 uppercase mb-1.5">
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
                    class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-700 p-4 sm:p-5">
                    <div class="flex items-center gap-3 mb-4 px-0">
                        <div
                            class="w-10 h-10 sm:w-9 sm:h-9 rounded-lg bg-amber-100 dark:bg-amber-500/20 flex items-center justify-center text-amber-600 dark:text-amber-400">
                            <i class="fa-solid fa-clock text-base sm:text-sm"></i>
                        </div>
                        <h2 class="text-base font-bold text-slate-900 dark:text-white tracking-tight">Доступное время</h2>
                    </div>

                    <div id="time-slots-container">
                        @if (isset($availableSlots) && count($availableSlots) > 0)
                            <div
                                class="grid grid-cols-4 gap-2 sm:gap-3 w-full">
                                @foreach ($availableSlots as $slot)
                                    <button type="button" data-time="{{ $slot }}"
                                        class="time-slot-btn w-full min-h-[48px] flex items-center justify-center py-3 sm:py-3.5 bg-white dark:bg-slate-800/40 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-bold text-slate-700 dark:text-slate-200 hover:border-indigo-500 dark:hover:border-indigo-400 transition-all active:scale-95 touch-manipulation">
                                        {{ $slot }}
                                    </button>
                                @endforeach
                            </div>
                        @else
                            <div
                                class="text-center w-full py-8 sm:py-10 bg-slate-50 dark:bg-slate-800/20 rounded-xl border border-dashed border-slate-200 dark:border-slate-800 text-slate-500">
                                <p class="text-sm font-medium">На этот день свободных окон нет</p>
                                <p class="mt-1 text-xs text-slate-400 dark:text-slate-500">Откройте календарь выше и выберите другую дату</p>
                                <button type="button" id="scroll-to-calendar-btn"
                                    class="mt-4 inline-flex items-center justify-center gap-2 min-h-[44px] px-4 py-2.5 rounded-xl text-sm font-medium text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-900/20 hover:bg-indigo-100 dark:hover:bg-indigo-900/30 transition-colors touch-manipulation">
                                    <i class="fa-solid fa-calendar-day text-sm"></i>
                                    Выбрать другую дату
                                </button>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- ДАННЫЕ -->
                <div id="appointment-details"
                    class="hidden bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-700 p-4 sm:p-5">
                    <div class="flex items-center gap-3 mb-4">
                        <div
                            class="w-10 h-10 sm:w-9 sm:h-9 rounded-lg bg-emerald-100 dark:bg-emerald-500/20 flex items-center justify-center text-emerald-600 dark:text-emerald-400">
                            <i class="fa-solid fa-address-card text-base sm:text-sm"></i>
                        </div>
                        <h2 class="text-base font-bold text-slate-900 dark:text-white tracking-tight">Ваши данные</h2>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1.5 ml-1">Имя *</label>
                            <input type="text" name="first_name" required
                                value="{{ old('first_name') }}"
                                class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800/50 border {{ $errors->has('first_name') ? 'border-rose-500' : 'border-slate-100 dark:border-slate-700' }} rounded-xl text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500 outline-none transition-all">
                            @error('first_name')
                                <p class="mt-1 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1.5 ml-1">Фамилия</label>
                            <input type="text" name="last_name" value="{{ old('last_name') }}"
                                class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800/50 border {{ $errors->has('last_name') ? 'border-rose-500' : 'border-slate-100 dark:border-slate-700' }} rounded-xl text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500 outline-none transition-all">
                            @error('last_name')
                                <p class="mt-1 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="sm:col-span-2">
                            <x-phone-input
                                block-id="publicBookingPhone"
                                :old-phone="old('phone')"
                                :old-country-id="old('phone_country_id')"
                                :old-national="''"
                                :required="true"
                                helper-text=""
                                :all-countries-from-library="true"
                                :international-format="false"
                            />
                            @error('phone')
                                <p class="mt-1 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div
                        class="mt-6 p-4 sm:p-5 bg-indigo-600 dark:bg-indigo-500 rounded-xl flex items-center justify-between text-white">
                        <div class="min-w-0 pr-4">
                            <div class="text-2xl font-black leading-none" id="summary-time">--:--</div>
                            <div class="text-[10px] font-bold uppercase opacity-90 mt-1.5" id="summary-date">Время не
                                выбрано</div>
                        </div>
                        <button type="submit" id="submit-btn"
                            class="shrink-0 min-h-[44px] px-8 py-3.5 bg-white text-indigo-600 rounded-xl font-black uppercase text-[10px] tracking-widest hover:bg-indigo-50 active:scale-95 transition-all touch-manipulation">
                            Записаться
                        </button>
                    </div>
                </div>
        </form>
    </div>

    @push('scripts')
        @vite('resources/js/public-booking-select-time.js')
    @endpush
@endsection
