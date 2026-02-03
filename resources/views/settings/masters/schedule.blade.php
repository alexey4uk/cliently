@extends('layouts.user')

@section('title', 'Расписание мастера - Cliently')
@section('page-title', 'Расписание мастера')
@section('page-description', 'Управление рабочим временем мастера')

@push('breadcrumbs')
    <x-breadcrumbs :items="[
        ['title' => 'Настройки', 'url' => route('settings.index')],
        ['title' => 'Мастера', 'url' => route('settings.masters')],
        ['title' => $master->name, 'url' => route('settings.masters.edit', $master)],
        ['title' => 'Расписание', 'url' => null]
    ]" />
@endpush

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Расписание: {{ $master->name }}</h1>
        <p class="text-slate-600 dark:text-slate-400 mt-1">Настройте рабочее время мастера</p>
    </div>

    <form method="POST" action="{{ route('settings.masters.schedule.update', $master) }}">
        @csrf
        @method('PATCH')

        <!-- Регулярное расписание -->
        <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 p-6 mb-6">
            <h2 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">Регулярное расписание</h2>
            <p class="text-sm text-slate-500 dark:text-slate-400 mb-4">Укажите рабочие дни и время для каждого дня недели</p>

            <div class="space-y-3">
                @php
                $days = [
                    1 => 'Понедельник',
                    2 => 'Вторник',
                    3 => 'Среда',
                    4 => 'Четверг',
                    5 => 'Пятница',
                    6 => 'Суббота',
                    0 => 'Воскресенье',
                ];
                @endphp

                @foreach($days as $dayNum => $dayName)
                @php
                $schedule = $schedules[$dayNum] ?? null;
                $isWorking = isset($schedule['is_working']) ? (bool) $schedule['is_working'] : false;
                $startTime = isset($schedule['start_time']) ? \Carbon\Carbon::parse($schedule['start_time'])->format('H:i') : '09:00';
                $endTime = isset($schedule['end_time']) ? \Carbon\Carbon::parse($schedule['end_time'])->format('H:i') : '18:00';
                @endphp
                <div class="schedule-row flex flex-col sm:flex-row sm:items-center gap-3 p-4 rounded-lg border border-slate-200 dark:border-slate-700 w-full transition-colors"
                     x-data="{ isWorking: {!! json_encode($isWorking) !!} }"
                     :class="isWorking ? 'border-l-4 border-l-green-500 dark:border-l-green-600 bg-green-50/50 dark:bg-green-900/10' : 'border-l-4 border-l-slate-300 dark:border-l-slate-600 bg-slate-50/50 dark:bg-slate-800/50'">

                    <div class="flex items-center gap-2 shrink-0 w-full sm:w-40">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox"
                                   name="schedules[{{ $dayNum }}][is_working]"
                                   value="1"
                                   x-model="isWorking"
                                   class="w-4 h-4 rounded border-slate-300 dark:border-slate-700 text-indigo-600 focus:ring-2 focus:ring-indigo-500">
                            <span class="text-sm font-medium text-slate-900 dark:text-white">{{ $dayName }}</span>
                        </label>
                    </div>

                    <div class="flex items-center gap-2 w-full sm:flex-1 min-w-0" x-show="isWorking" x-transition>
                        <input type="time"
                               name="schedules[{{ $dayNum }}][start_time]"
                               id="start_{{ $dayNum }}"
                               value="{{ old("schedules.{$dayNum}.start_time", $startTime) }}"
                               class="w-24 sm:w-28 px-2 py-1.5 border border-slate-300 dark:border-slate-700 rounded-lg bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-xs">
                        <span class="text-slate-400 text-xs">—</span>
                        <input type="time"
                               name="schedules[{{ $dayNum }}][end_time]"
                               id="end_{{ $dayNum }}"
                               value="{{ old("schedules.{$dayNum}.end_time", $endTime) }}"
                               class="w-24 sm:w-28 px-2 py-1.5 border border-slate-300 dark:border-slate-700 rounded-lg bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-xs">
                    </div>

                    <div class="text-sm text-slate-500 dark:text-slate-400 shrink-0" x-show="!isWorking" x-transition>
                        Выходной
                    </div>

                    <input type="hidden" name="schedules[{{ $dayNum }}][day_of_week]" value="{{ $dayNum }}">
                </div>
                @endforeach
            </div>
        </div>

        <!-- Переопределения на даты -->
        <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 p-6 mb-6">
            <h2 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">Переопределения на даты</h2>
            <p class="text-sm text-slate-500 dark:text-slate-400 mb-4">Укажите исключения из регулярного расписания (праздники, отпуска, изменённые смены)</p>

            <div id="overrides-container" class="space-y-3">
                @if(count($overrides) > 0)
                    @foreach($overrides as $date => $override)
                    @php
                        $isWorkingOverride = ($override['is_working'] ?? true);
                        $dateYmd = \Carbon\Carbon::parse($date)->format('Y-m-d');
                        $overrideStartTime = isset($override['start_time']) ? \Carbon\Carbon::parse($override['start_time'])->format('H:i') : '';
                        $overrideEndTime = isset($override['end_time']) ? \Carbon\Carbon::parse($override['end_time'])->format('H:i') : '';
                    @endphp
                    <div class="override-row flex flex-col sm:flex-row sm:items-center gap-3 p-4 rounded-lg border border-slate-200 dark:border-slate-700 border-l-4 border-l-green-500 dark:border-l-green-600 bg-green-50/50 dark:bg-green-900/10 w-full"
                         x-data="{ isDayOff: {{ !$isWorkingOverride ? 'true' : 'false' }} }">
                        <input type="date"
                               name="overrides[{{ $dateYmd }}][date]"
                               value="{{ $dateYmd }}"
                               class="w-full sm:flex-1 min-w-0 px-2 py-1.5 border border-slate-300 dark:border-slate-700 rounded-lg bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-xs">

                        <label class="flex items-center gap-2 shrink-0">
                            <input type="checkbox"
                                   name="overrides[{{ $dateYmd }}][is_working]"
                                   value="0"
                                   {{ !$isWorkingOverride ? 'checked' : '' }}
                                   x-model="isDayOff"
                                   class="w-4 h-4 rounded border-slate-300 dark:border-slate-700 text-indigo-600 focus:ring-2 focus:ring-indigo-500">
                            <span class="text-sm text-slate-600 dark:text-slate-400">Выходной</span>
                        </label>

                        <div class="flex items-center gap-2 w-full sm:flex-1 min-w-0 overflow-x-auto"
                             x-show="!isDayOff"
                             x-transition>
                            <input type="time"
                                   name="overrides[{{ $dateYmd }}][start_time]"
                                   value="{{ $overrideStartTime }}"
                                   class="w-24 sm:w-28 px-2 py-1.5 border border-slate-300 dark:border-slate-700 rounded-lg bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-xs"
                                   placeholder="С">
                            <span class="text-slate-400 text-xs">—</span>
                            <input type="time"
                                   name="overrides[{{ $dateYmd }}][end_time]"
                                   value="{{ $overrideEndTime }}"
                                   class="w-24 sm:w-28 px-2 py-1.5 border border-slate-300 dark:border-slate-700 rounded-lg bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-xs"
                                   placeholder="До">
                        </div>

                        <button type="button"
                                class="shrink-0 text-rose-500 hover:text-rose-600 transition-colors"
                                onclick="this.closest('.override-row').remove()"
                                title="Удалить переопределение">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </div>
                    @endforeach
                @endif
            </div>

            <button type="button"
                    id="add-override"
                    class="mt-3 w-full sm:w-auto inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-medium text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-900/20 rounded-lg hover:bg-indigo-100 dark:hover:bg-indigo-900/30 transition-colors">
                <i class="fa-solid fa-plus"></i>
                <span>Добавить переопределение</span>
            </button>
        </div>

        <!-- Кнопки -->
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-end gap-3">
            <a href="{{ route('settings.masters.edit', $master) }}"
               class="px-6 py-2.5 text-sm font-medium text-center text-slate-700 dark:text-slate-300 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
                Отмена
            </a>
            <button type="submit"
                    class="px-6 py-2.5 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-slate-900 transition-colors">
                Сохранить расписание
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const addOverrideBtn = document.getElementById('add-override');
    const overridesContainer = document.getElementById('overrides-container');
    let overrideCounter = 0;

    addOverrideBtn.addEventListener('click', function() {
        const today = new Date().toISOString().split('T')[0];
        overrideCounter++;

        const html = `
            <div class="override-row flex flex-col sm:flex-row sm:items-center gap-3 p-4 rounded-lg border border-slate-200 dark:border-slate-700 border-l-4 border-l-amber-400 dark:border-l-amber-600 bg-amber-50/50 dark:bg-amber-900/10 w-full" title="Черновик — сохраните расписание" x-data="{ isDayOff: false }">
                <input type="date"
                       name="overrides[new_${overrideCounter}][date]"
                       value="${today}"
                       min="${today}"
                       class="w-full sm:flex-1 min-w-0 px-2 py-1.5 border border-slate-300 dark:border-slate-700 rounded-lg bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-xs">

                <label class="flex items-center gap-2 shrink-0">
                    <input type="checkbox"
                           name="overrides[new_${overrideCounter}][is_working]"
                           value="0"
                           x-model="isDayOff"
                           class="w-4 h-4 rounded border-slate-300 dark:border-slate-700 text-indigo-600 focus:ring-2 focus:ring-indigo-500">
                    <span class="text-sm text-slate-600 dark:text-slate-400">Выходной</span>
                </label>

                <div class="flex items-center gap-2 w-full sm:flex-1 min-w-0 overflow-x-auto" x-show="!isDayOff" x-transition>
                    <input type="time"
                           name="overrides[new_${overrideCounter}][start_time]"
                           value="09:00"
                           class="w-24 sm:w-28 px-2 py-1.5 border border-slate-300 dark:border-slate-700 rounded-lg bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-xs"
                           placeholder="С">
                    <span class="text-slate-400 text-xs">—</span>
                    <input type="time"
                           name="overrides[new_${overrideCounter}][end_time]"
                           value="18:00"
                           class="w-24 sm:w-28 px-2 py-1.5 border border-slate-300 dark:border-slate-700 rounded-lg bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-xs"
                           placeholder="До">
                </div>

                <button type="button"
                        class="shrink-0 text-rose-500 hover:text-rose-600 transition-colors"
                        onclick="this.closest('.override-row').remove()"
                        title="Удалить переопределение">
                    <i class="fa-solid fa-trash"></i>
                </button>
            </div>
        `;

        overridesContainer.insertAdjacentHTML('beforeend', html);
    });
});
</script>
@endpush
@endsection
