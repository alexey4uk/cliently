@extends('layouts.user')

@section('title', 'Редактирование записи - Cliently')
@section('page-title', 'Редактирование записи')
@section('page-description', 'Изменение данных записи')

@push('breadcrumbs')
    <x-breadcrumbs :items="[
        ['title' => 'Записи', 'url' => route('appointments.index')],
        ['title' => 'Редактирование', 'url' => null]
    ]" />
@endpush

@section('content')

<form method="POST" action="{{ route('appointments.update', $appointment) }}" class="space-y-6">
    @csrf
    @method('PATCH')

    <div class="space-y-6">
        <!-- Основная информация -->
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-4 md:p-6">
            <div class="space-y-5">
                <div class="pb-4 border-b border-slate-200 dark:border-slate-700">
                    <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-300 flex items-center gap-2">
                        <i class="fa-solid fa-calendar-check text-indigo-600 dark:text-indigo-400"></i>
                        Основная информация
                    </h3>
                </div>
            
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label for="client_id" class="flex items-center gap-1.5 md:gap-2 text-base md:text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                            <span>Клиент*</span>
                        </label>
                        <select id="client_id" name="client_id" required
                                class="w-full px-2.5 md:px-3 py-2 md:py-2.5 text-base md:text-sm rounded-md border {{ $errors->has('client_id') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-indigo-500' }} bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:border-transparent transition-colors">
                            <option value="">Выберите клиента</option>
                            @foreach($clients as $client)
                                <option value="{{ $client->id }}" {{ old('client_id', $appointment->client_id) == $client->id ? 'selected' : '' }}>
                                    {{ $client->full_name }} ({{ $client->phone }})
                                </option>
                            @endforeach
                        </select>
                        @error('client_id')
                        <p class="mt-2 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="service_id" class="flex items-center gap-1.5 md:gap-2 text-base md:text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                            <span>Услуга*</span>
                        </label>
                        <select id="service_id" name="service_id" required
                                class="w-full px-2.5 md:px-3 py-2 md:py-2.5 text-base md:text-sm rounded-md border {{ $errors->has('service_id') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-indigo-500' }} bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:border-transparent transition-colors">
                            <option value="">Выберите услугу</option>
                            @foreach($services as $service)
                                <option value="{{ $service->id }}" {{ old('service_id', $appointment->service_id) == $service->id ? 'selected' : '' }}>
                                    {{ $service->name }} ({{ number_format($service->price, 0, ',', ' ') }} Br, {{ $service->duration }} мин)
                                </option>
                            @endforeach
                        </select>
                        @error('service_id')
                        <p class="mt-2 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label for="master_id" class="flex items-center gap-1.5 md:gap-2 text-base md:text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                            <span>Мастер</span>
                        </label>
                        <select id="master_id" name="master_id"
                                class="w-full px-2.5 md:px-3 py-2 md:py-2.5 text-base md:text-sm rounded-md border {{ $errors->has('master_id') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-indigo-500' }} bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:border-transparent transition-colors">
                            <option value="">Не выбран</option>
                            @foreach($masters as $master)
                                <option value="{{ $master->id }}" {{ old('master_id', $appointment->master_id) == $master->id ? 'selected' : '' }}>
                                    {{ $master->first_name }} {{ $master->last_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('master_id')
                        <p class="mt-2 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="location_id" class="flex items-center gap-1.5 md:gap-2 text-base md:text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                            <span>Локация</span>
                        </label>
                        <select id="location_id" name="location_id"
                                class="w-full px-2.5 md:px-3 py-2 md:py-2.5 text-base md:text-sm rounded-md border {{ $errors->has('location_id') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-indigo-500' }} bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:border-transparent transition-colors">
                            <option value="">Не выбрана</option>
                            @foreach($locations as $location)
                                <option value="{{ $location->id }}" {{ old('location_id', $appointment->location_id) == $location->id ? 'selected' : '' }}>
                                    {{ $location->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('location_id')
                        <p class="mt-2 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Дата и время -->
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-4 md:p-6">
            <div class="space-y-5">
                <div class="pb-4 border-b border-slate-200 dark:border-slate-700">
                    <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-300 flex items-center gap-2">
                        <i class="fa-solid fa-clock text-indigo-600 dark:text-indigo-400"></i>
                        Дата и время
                    </h3>
                </div>
            
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label for="date" class="flex items-center gap-1.5 md:gap-2 text-base md:text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                            <span>Дата*</span>
                        </label>
                        <input type="date" id="date" name="date" required value="{{ old('date', $appointment->date->format('Y-m-d')) }}"
                               min="{{ date('Y-m-d') }}"
                               class="w-full px-2.5 md:px-3 py-2 md:py-2.5 text-base md:text-sm rounded-md border {{ $errors->has('date') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-indigo-500' }} bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:border-transparent transition-colors">
                        @error('date')
                        <p class="mt-2 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="time" class="flex items-center gap-1.5 md:gap-2 text-base md:text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                            <span>Время*</span>
                        </label>
                        <select id="time" name="time" required
                                class="w-full px-2.5 md:px-3 py-2 md:py-2.5 text-base md:text-sm rounded-md border {{ $errors->has('time') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-indigo-500' }} bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:border-transparent transition-colors">
                            <option value="">{{ old('time', $appointment->time) ? old('time', $appointment->time) : 'Загрузка...' }}</option>
                        </select>
                        <div id="time-loading" class="hidden mt-2 text-xs text-slate-500 dark:text-slate-400">
                            <i class="fa-solid fa-spinner fa-spin"></i> Загрузка доступных слотов...
                        </div>
                        <div id="time-error" class="hidden mt-2 text-xs text-rose-600 dark:text-rose-400"></div>
                        @error('time')
                        <p class="mt-2 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="status" class="flex items-center gap-1.5 md:gap-2 text-base md:text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                        <span>Статус</span>
                    </label>
                    <select id="status" name="status"
                            class="w-full px-2.5 md:px-3 py-2 md:py-2.5 text-base md:text-sm rounded-md border {{ $errors->has('status') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-300 dark:border-slate-700 focus:ring-indigo-500' }} bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:border-transparent transition-colors">
                        <option value="pending" {{ old('status', $appointment->status) === 'pending' ? 'selected' : '' }}>Ожидает</option>
                        <option value="confirmed" {{ old('status', $appointment->status) === 'confirmed' ? 'selected' : '' }}>Подтверждена</option>
                        <option value="completed" {{ old('status', $appointment->status) === 'completed' ? 'selected' : '' }}>Завершена</option>
                        <option value="cancelled" {{ old('status', $appointment->status) === 'cancelled' ? 'selected' : '' }}>Отменена</option>
                    </select>
                    @error('status')
                    <p class="mt-2 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Дополнительная информация -->
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-4 md:p-6">
            <div class="space-y-5">
                <div class="pb-4 border-b border-slate-200 dark:border-slate-700">
                    <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-300 flex items-center gap-2">
                        <i class="fa-solid fa-info-circle text-indigo-600 dark:text-indigo-400"></i>
                        Дополнительная информация
                    </h3>
                </div>
            
                <div>
                    <label for="notes" class="flex items-center gap-1.5 md:gap-2 text-base md:text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                        <span>Заметки</span>
                    </label>
                    <textarea id="notes" name="notes" rows="3"
                              class="w-full px-2.5 md:px-3 py-2 md:py-2.5 text-base md:text-sm rounded-md border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-colors resize-none">{{ old('notes', $appointment->notes) }}</textarea>
                    @error('notes')
                    <p class="mt-2 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>
    </div>

    <!-- Кнопки действий -->
    <div class="flex items-center justify-between pt-6 border-t border-slate-200 dark:border-slate-800">
        <a href="{{ route('appointments.index') }}"
           class="px-3 md:px-4 py-1.5 md:py-2 text-base md:text-sm font-medium text-slate-700 dark:text-slate-300 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-md hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
            Отмена
        </a>
        <button type="submit"
                class="px-3 md:px-4 py-1.5 md:py-2 text-base md:text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-md transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            Сохранить изменения
        </button>
    </div>
</form>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const serviceSelect = document.getElementById('service_id');
    const masterSelect = document.getElementById('master_id');
    const dateInput = document.getElementById('date');
    const timeSelect = document.getElementById('time');
    const timeLoading = document.getElementById('time-loading');
    const timeError = document.getElementById('time-error');
    const locationSelect = document.getElementById('location_id');

    let currentOldTime = '{{ old("time", $appointment->time) }}';
    const appointmentId = {{ $appointment->id }};

    function loadAvailableSlots() {
        const serviceId = serviceSelect.value;
        const date = dateInput.value;
        const masterId = masterSelect.value || null;
        const locationId = locationSelect.value || null;

        // Очищаем предыдущие опции
        timeSelect.innerHTML = '<option value="">Загрузка...</option>';
        timeSelect.disabled = true;
        timeLoading.classList.remove('hidden');
        timeError.classList.add('hidden');

        if (!serviceId || !date) {
            timeSelect.innerHTML = '<option value="">Сначала выберите услугу и дату</option>';
            timeSelect.disabled = false;
            timeLoading.classList.add('hidden');
            return;
        }

        // Формируем URL для API (используем публичный роут)
        const url = '{{ route("api.public.appointments.available-slots", $business->slug) }}';
        const params = new URLSearchParams({
            service_id: serviceId,
            date: date,
        });

        if (masterId) {
            params.append('master_id', masterId);
        }

        if (locationId) {
            params.append('location_id', locationId);
        }

        // Выполняем AJAX запрос
        fetch(`${url}?${params.toString()}`, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            },
            credentials: 'same-origin',
        })
        .then(response => response.json())
        .then(data => {
            timeLoading.classList.add('hidden');
            timeSelect.disabled = false;

            if (data.success && data.slots && data.slots.length > 0) {
                timeSelect.innerHTML = '<option value="">Выберите время</option>';
                data.slots.forEach(slot => {
                    const option = document.createElement('option');
                    option.value = slot;
                    option.textContent = slot;
                    // Выбираем текущее время записи или старое значение из формы
                    if (currentOldTime === slot || (!currentOldTime && slot === '{{ $appointment->time }}')) {
                        option.selected = true;
                    }
                    timeSelect.appendChild(option);
                });
                // Если текущее время не в списке доступных, добавляем его
                if (currentOldTime && !data.slots.includes(currentOldTime)) {
                    const currentOption = document.createElement('option');
                    currentOption.value = currentOldTime;
                    currentOption.textContent = currentOldTime + ' (текущее)';
                    currentOption.selected = true;
                    timeSelect.insertBefore(currentOption, timeSelect.firstChild.nextSibling);
                }
                timeError.classList.add('hidden');
            } else {
                // Если нет доступных слотов, но есть текущее время, оставляем его
                if (currentOldTime) {
                    timeSelect.innerHTML = `<option value="${currentOldTime}" selected>${currentOldTime} (текущее)</option>`;
                } else {
                    timeSelect.innerHTML = '<option value="">Нет доступных слотов</option>';
                }
                
                // Более информативное сообщение об ошибке
                let errorMessage = 'На выбранную дату нет доступных временных слотов.';
                
                // Проверяем, сегодня ли это
                const today = new Date().toISOString().split('T')[0];
                const isToday = date === today;
                
                if (isToday) {
                    errorMessage = 'На сегодня нет доступных слотов. Пожалуйста, выберите другую дату.';
                } else {
                    errorMessage = 'На выбранную дату нет доступных временных слотов. Пожалуйста, выберите другую дату или мастера.';
                }
                
                timeError.textContent = errorMessage;
                timeError.classList.remove('hidden');
            }
        })
        .catch(error => {
            console.error('Ошибка при загрузке слотов:', error);
            timeLoading.classList.add('hidden');
            timeSelect.disabled = false;
            // В случае ошибки оставляем текущее время
            if (currentOldTime) {
                timeSelect.innerHTML = `<option value="${currentOldTime}" selected>${currentOldTime}</option>`;
            } else {
                timeSelect.innerHTML = '<option value="">Ошибка загрузки</option>';
            }
            timeError.textContent = 'Произошла ошибка при загрузке доступных слотов. Пожалуйста, обновите страницу.';
            timeError.classList.remove('hidden');
        });
    }

    // Обработчики событий
    serviceSelect.addEventListener('change', loadAvailableSlots);
    masterSelect.addEventListener('change', loadAvailableSlots);
    dateInput.addEventListener('change', loadAvailableSlots);
    locationSelect.addEventListener('change', loadAvailableSlots);

    // Загружаем слоты при загрузке страницы
    loadAvailableSlots();
});
</script>
@endpush

@endsection

