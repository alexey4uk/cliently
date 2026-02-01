<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\Master;
use App\Models\Service;
use App\Services\MasterScheduleService;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class AppointmentSlotService
{
    /**
     * Интервал между слотами в минутах
     */
    protected int $slotInterval = 15;

    /**
     * Минимальный интервал между записями в минутах
     */
    protected int $minIntervalBetweenAppointments = 15;

    /**
     * Получить доступные временные слоты
     *
     * @param  int  $serviceId  ID услуги
     * @param  string  $date  Дата в формате Y-m-d
     * @param  int|null  $masterId  ID мастера (опционально)
     * @param  int|null  $locationId  ID локации (опционально, не используется в расчете)
     * @param  int|null  $excludeAppointmentId  ID записи для исключения из расчета (при редактировании)
     * @return array Массив доступных слотов в формате ['HH:MM', ...]
     */
    public function getAvailableSlots(
        int $serviceId,
        string $date,
        ?int $masterId = null,
        ?int $locationId = null,
        &$debugInfo = null,
        ?int $excludeAppointmentId = null,
    ): array {
        $service = Service::findOrFail($serviceId);
        $selectedDate = Carbon::parse($date);
        $duration = $service->duration; // Длительность услуги в минутах

        // Нормализуем masterId
        if ($masterId === 0 || $masterId === "0" || $masterId === "") {
            $masterId = null;
        }

        $debug = [
            "service_id" => $serviceId,
            "service_duration" => $duration,
            "date" => $date,
            "day_of_week" => $selectedDate->dayOfWeek,
            "master_id" => $masterId,
            "location_id" => $locationId,
        ];

        // Получаем мастеров для услуги
        $masters = $this->getMastersForService($serviceId, $masterId);
        $debug["masters_found"] = $masters->count();
        $debug["masters"] = $masters
            ->map(function ($master) {
                return [
                    "id" => $master->id,
                    "name" => $master->first_name . " " . $master->last_name,
                    "has_working_hours" => !empty($master->working_hours),
                    "working_hours" => $master->working_hours,
                ];
            })
            ->toArray();

        if ($masters->isEmpty()) {
            if ($masterId) {
                Log::warning("Мастер не найден или не предоставляет услугу", [
                    "master_id" => $masterId,
                    "service_id" => $serviceId,
                ]);
            }
            if ($debugInfo !== null) {
                $debugInfo = $debug;
            }

            return [];
        }

        // Получаем все доступные временные окна от всех мастеров
        $availableTimeWindows = $this->getAvailableTimeWindows(
            $masters,
            $selectedDate,
        );
        $debug["time_windows"] = $availableTimeWindows;
        $debug["time_windows_count"] = count($availableTimeWindows);

        if (empty($availableTimeWindows)) {
            if ($debugInfo !== null) {
                $debugInfo = $debug;
            }

            return [];
        }

        // Генерируем слоты с интервалом равным длительности услуги
        // Время подготовки не учитывается в генерации слотов, но будет показано пользователю как уведомление
        $allSlots = $this->generateSlots(
            $availableTimeWindows,
            $duration,
            $selectedDate,
        );
        $debug["slot_interval"] = $duration;
        $debug["preparation_time"] = $service->preparation_time;
        $debug["is_today"] = $selectedDate->isToday();
        $debug["current_time"] = Carbon::now()->format("H:i");
        $debug["all_slots_count"] = count($allSlots);
        $debug["all_slots_sample"] = array_slice($allSlots, 0, 10); // Первые 10 слотов для примера

        if (empty($allSlots)) {
            if ($debugInfo !== null) {
                $debugInfo = $debug;
            }

            return [];
        }

        // Фильтруем слоты по длительности услуги
        $slotsFittingDuration = $this->filterSlotsByDuration(
            $allSlots,
            $duration,
            $availableTimeWindows,
        );
        $debug["slots_after_duration_filter"] = count($slotsFittingDuration);
        $debug["slots_after_duration_sample"] = array_slice(
            $slotsFittingDuration,
            0,
            10,
        );

        if (empty($slotsFittingDuration)) {
            if ($debugInfo !== null) {
                $debugInfo = $debug;
            }

            return [];
        }

        // Исключаем занятые слоты
        // Время подготовки уже учтено при генерации слотов, поэтому проверяем только прямое пересечение
        $availableSlots = $this->excludeBookedSlots(
            $slotsFittingDuration,
            $serviceId,
            $selectedDate,
            $duration,
            $masterId,
            null,
            $excludeAppointmentId,
        );
        $debug["final_slots_count"] = count($availableSlots);
        $debug["slots_lost_to_bookings"] =
            count($slotsFittingDuration) - count($availableSlots);
        $debug["excluded_appointment_id"] = $excludeAppointmentId;

        // Сортируем и возвращаем
        sort($availableSlots);

        if ($debugInfo !== null) {
            $debugInfo = $debug;
        }

        return $availableSlots;
    }

    /**
     * Получить мастеров для услуги
     */
    protected function getMastersForService(
        int $serviceId,
        ?int $masterId = null,
    ): Collection {
        // Нормализуем masterId (обрабатываем 0 и пустые значения как null)
        if ($masterId === 0 || $masterId === "0" || $masterId === "") {
            $masterId = null;
        }

        if ($masterId) {
            // Если мастер указан, проверяем, что он предоставляет эту услугу
            $master = Master::where("id", $masterId)
                ->where("is_active", true)
                ->whereHas("services", function ($query) use ($serviceId) {
                    $query->where("services.id", $serviceId);
                })
                ->first();

            if (!$master) {
                // Если мастер не найден или не предоставляет услугу, возвращаем пустую коллекцию
                return collect();
            }

            return collect([$master]);
        }

        // Получаем всех мастеров, которые предоставляют эту услугу
        $masters = Master::where("is_active", true)
            ->whereHas("services", function ($query) use ($serviceId) {
                $query->where("services.id", $serviceId);
            })
            ->get();

        return $masters;
    }

    /**
     * Получить доступные временные окна от мастеров
     *
     * @param  Collection  $masters  Коллекция мастеров
     * @param  Carbon  $date  Дата
     * @return array Массив временных окон [['from' => '09:00', 'to' => '18:00', 'master_id' => 1], ...]
     */
    protected function getAvailableTimeWindows(
        Collection $masters,
        Carbon $date,
    ): array {
        $timeWindows = [];
        $scheduleService = app(MasterScheduleService::class);

        foreach ($masters as $master) {
            // Используем новый сервис для получения времени работы
            $workingTime = $scheduleService->getWorkingTimeForDate(
                $master,
                $date,
            );

            if (!$workingTime) {
                continue; // Мастер не работает в этот день
            }

            $timeWindows[] = [
                "from" => $workingTime["from"],
                "to" => $workingTime["to"],
                "master_id" => $master->id,
            ];
        }

        return $timeWindows;
    }

    /**
     * Парсинг working_hours из JSON
     */
    protected function parseWorkingHours($workingHours): ?array
    {
        // Если null или пусто, возвращаем null
        if (empty($workingHours)) {
            return null;
        }

        // Если это уже массив, возвращаем как есть
        if (is_array($workingHours)) {
            return $workingHours;
        }

        // Если это строка, пытаемся декодировать JSON
        if (is_string($workingHours)) {
            // Убираем возможные пробелы
            $workingHours = trim($workingHours);

            // Если пустая строка после trim
            if ($workingHours === "") {
                return null;
            }

            $decoded = json_decode($workingHours, true);

            // Проверяем ошибки JSON
            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::warning("Ошибка декодирования JSON working_hours", [
                    "json_error" => json_last_error_msg(),
                    "raw_value" => substr($workingHours, 0, 200),
                ]);

                return null;
            }

            return is_array($decoded) ? $decoded : null;
        }

        return null;
    }

    /**
     * Генерация слотов из временных окон
     * Интервал между слотами равен длительности услуги
     *
     * @param  array  $timeWindows  Временные окна
     * @param  int  $interval  Интервал в минутах (длительность услуги)
     * @param  Carbon|null  $selectedDate  Дата для проверки прошедших слотов (если сегодня)
     * @return array Массив слотов ['09:00', '10:00', ...]
     */
    protected function generateSlots(
        array $timeWindows,
        int $interval,
        ?Carbon $selectedDate = null,
    ): array {
        $slots = [];
        $now = Carbon::now();
        $isToday = $selectedDate && $selectedDate->isToday();

        foreach ($timeWindows as $window) {
            $from = Carbon::parse($window["from"]);
            $to = Carbon::parse($window["to"]);

            $current = $from->copy();

            // Генерируем слоты с интервалом равным длительности услуги
            // Слот должен помещаться в рабочее время (слот + длительность <= конец рабочего дня)
            while ($current->lt($to)) {
                $slotEndTime = $current->copy()->addMinutes($interval);

                // Проверяем, что слот помещается в рабочее время
                // Слот + длительность должна быть <= конец рабочего дня
                if ($slotEndTime->lte($to)) {
                    // Если это сегодня, проверяем, что слот не в прошлом
                    if ($isToday) {
                        $slotDateTime = $selectedDate
                            ->copy()
                            ->setTime(
                                (int) $current->format("H"),
                                (int) $current->format("i"),
                                0,
                            );
                        // Проверяем, что слот в будущем (минимум через 15 минут от текущего времени)
                        if ($slotDateTime->gt($now->copy()->addMinutes(15))) {
                            $slots[] = $current->format("H:i");
                        }
                    } else {
                        $slots[] = $current->format("H:i");
                    }
                }

                $current->addMinutes($interval);
            }
        }

        // Убираем дубликаты и сортируем
        $uniqueSlots = array_unique($slots);
        sort($uniqueSlots);

        return $uniqueSlots;
    }

    /**
     * Фильтрация слотов по длительности услуги
     * Слот должен помещаться в рабочее время (слот + длительность <= конец рабочего дня)
     */
    protected function filterSlotsByDuration(
        array $slots,
        int $duration,
        array $timeWindows,
    ): array {
        $filteredSlots = [];

        foreach ($slots as $slot) {
            $slotTime = Carbon::parse($slot);
            $endTime = $slotTime->copy()->addMinutes($duration);

            // Проверяем, помещается ли слот в любое из временных окон
            foreach ($timeWindows as $window) {
                $windowFrom = Carbon::parse($window["from"]);
                $windowTo = Carbon::parse($window["to"]);

                if ($slotTime->gte($windowFrom) && $endTime->lte($windowTo)) {
                    $filteredSlots[] = $slot;
                    break;
                }
            }
        }

        return $filteredSlots;
    }

    /**
     * Исключить занятые слоты
     */
    protected function excludeBookedSlots(
        array $slots,
        int $serviceId,
        Carbon $date,
        int $duration,
        ?int $masterId = null,
        ?int $preparationTime = null,
        ?int $excludeAppointmentId = null,
    ): array {
        // Получаем существующие записи на эту дату
        $query = Appointment::where("date", $date->format("Y-m-d"))
            ->where("status", "!=", "cancelled")
            ->where("service_id", $serviceId);

        // Исключаем текущую запись при редактировании
        if ($excludeAppointmentId) {
            $query->where("id", "!=", $excludeAppointmentId);
        }

        // Если мастер указан, проверяем только его записи
        if ($masterId) {
            $query->where(function ($q) use ($masterId) {
                $q->where("master_id", $masterId)->orWhereNull("master_id"); // Также учитываем записи без мастера
            });
        }

        // КРИТИЧНО: предзагружаем service для избежания N+1 при обращении к final_duration accessor
        $existingAppointments = $query->with("service")->get();

        $availableSlots = [];

        foreach ($slots as $slot) {
            $slotTime = Carbon::parse($slot);
            $slotEndTime = $slotTime->copy()->addMinutes($duration);

            $isAvailable = true;

            foreach ($existingAppointments as $appointment) {
                // Если мастер не указан при поиске слотов, но у записи есть мастер,
                // то эта запись не блокирует слот (так как слот может быть для другого мастера)
                if (!$masterId && $appointment->master_id) {
                    continue;
                }

                $appointmentTime = Carbon::parse($appointment->time);
                $appointmentDuration =
                    $appointment->final_duration ?? $duration; // Используем длительность услуги если final_duration не задан
                $appointmentEndTime = $appointmentTime
                    ->copy()
                    ->addMinutes($appointmentDuration);

                // Проверяем пересечение временных интервалов
                //
                // Время подготовки уже учтено при генерации слотов (интервал между слотами = длительность + подготовка)
                // Поэтому здесь проверяем только прямое пересечение с существующими записями
                //
                // Слот блокируется если он пересекается с записью:
                // slotTime < appointmentEndTime AND slotEndTime > appointmentTime

                $hasOverlap =
                    $slotTime->lt($appointmentEndTime) &&
                    $slotEndTime->gt($appointmentTime);

                if ($hasOverlap) {
                    // Есть пересечение - слот занят
                    $isAvailable = false;
                    break;
                }
            }

            if ($isAvailable) {
                $availableSlots[] = $slot;
            }
        }

        return $availableSlots;
    }

    /**
     * Получить доступные слоты для выбранной даты + календарь с информацией о датах со слотами
     * МАКСИМАЛЬНО ОПТИМИЗИРОВАННАЯ версия - один запрос для всего периода
     *
     * @param  Service  $service  Объект услуги
     * @param  Master  $master  Объект мастера
     * @param  Carbon  $selectedDate  Выбранная дата (для которой нужны слоты)
     * @param  Carbon  $calendarStart  Начало периода для календаря
     * @param  Carbon  $calendarEnd  Конец периода для календаря
     * @return array ['slots' => array, 'calendar' => array]
     */
    public function getAvailableSlotsWithCalendar(
        \App\Models\Service $service,
        \App\Models\Master $master,
        Carbon $selectedDate,
        Carbon $calendarStart,
        Carbon $calendarEnd,
    ): array {
        $duration = $service->duration;
        $masterId = $master->id;

        // Используем уже загруженного мастера вместо запроса к БД
        $masters = collect([$master]);

        // Получаем ВСЕ записи для всего периода календаря ОДНИМ запросом
        $allAppointments = Appointment::whereBetween("date", [
            $calendarStart->format("Y-m-d"),
            $calendarEnd->format("Y-m-d"),
        ])
            ->where("status", "!=", "cancelled")
            ->where("service_id", $service->id)
            ->where(function ($q) use ($masterId) {
                $q->where("master_id", $masterId)->orWhereNull("master_id");
            })
            ->with("service") // Предзагружаем service для избежания N+1 в accessor
            ->get();

        // Группируем записи по датам для быстрого доступа
        $appointmentsByDate = $allAppointments->groupBy(function (
            $appointment,
        ) {
            return $appointment->date->format("Y-m-d");
        });

        // Проверяем каждую дату в периоде
        $calendar = [];
        $selectedDateSlots = [];
        $checkDate = $calendarStart->copy();

        while ($checkDate->lte($calendarEnd)) {
            $dateString = $checkDate->format("Y-m-d");

            // Получаем временные окна для этой даты
            $timeWindows = $this->getAvailableTimeWindows($masters, $checkDate);

            if (empty($timeWindows)) {
                $calendar[$dateString] = false;
                $checkDate->addDay();

                continue;
            }

            // Генерируем слоты
            $slots = $this->generateSlots($timeWindows, $duration, $checkDate);

            if (empty($slots)) {
                $calendar[$dateString] = false;
                $checkDate->addDay();

                continue;
            }

            // Фильтруем по длительности
            $slotsFittingDuration = $this->filterSlotsByDuration(
                $slots,
                $duration,
                $timeWindows,
            );

            if (empty($slotsFittingDuration)) {
                $calendar[$dateString] = false;
                $checkDate->addDay();

                continue;
            }

            // Исключаем занятые слоты (используя уже загруженные appointments)
            $dateAppointments = $appointmentsByDate->get(
                $dateString,
                collect(),
            );
            $availableSlots = $this->excludeBookedSlotsFromCollection(
                $slotsFittingDuration,
                $dateAppointments,
                $duration,
                $masterId,
            );

            $hasSlots = !empty($availableSlots);
            $calendar[$dateString] = $hasSlots;

            // Если это выбранная дата - сохраняем её слоты
            if ($dateString === $selectedDate->format("Y-m-d")) {
                $selectedDateSlots = $availableSlots;
                sort($selectedDateSlots);
            }

            $checkDate->addDay();
        }

        return [
            "slots" => $selectedDateSlots,
            "calendar" => $calendar,
        ];
    }

    /**
     * Получить даты со слотами для периода (оптимизированная версия)
     * Выполняет один запрос к БД для всех дат вместо цикла запросов
     *
     * @param  int  $serviceId  ID услуги
     * @param  Carbon  $startDate  Начальная дата периода
     * @param  Carbon  $endDate  Конечная дата периода
     * @param  int|null  $masterId  ID мастера
     * @param  int|null  $locationId  ID локации
     * @param  string|null  $currentDate  Текущая выбранная дата (для которой слоты уже вычислены)
     * @param  array  $currentDateSlots  Слоты для текущей даты
     * @return array Массив ['Y-m-d' => true/false]
     */
    public function getDatesWithSlotsBatch(
        int $serviceId,
        Carbon $startDate,
        Carbon $endDate,
        ?int $masterId = null,
        ?int $locationId = null,
        ?string $currentDate = null,
        array $currentDateSlots = [],
    ): array {
        $service = Service::findOrFail($serviceId);
        $duration = $service->duration;

        // Получаем мастеров для услуги
        $masters = $this->getMastersForService($serviceId, $masterId);

        if ($masters->isEmpty()) {
            return [];
        }

        // Получаем ВСЕ записи для этого периода ОДНИМ запросом
        $query = Appointment::whereBetween("date", [
            $startDate->format("Y-m-d"),
            $endDate->format("Y-m-d"),
        ])
            ->where("status", "!=", "cancelled")
            ->where("service_id", $serviceId);

        if ($masterId) {
            $query->where(function ($q) use ($masterId) {
                $q->where("master_id", $masterId)->orWhereNull("master_id");
            });
        }

        // КРИТИЧНО: предзагружаем service для избежания N+1 в accessor final_duration
        $allAppointments = $query->with("service")->get();

        // Группируем записи по датам для быстрого доступа
        $appointmentsByDate = $allAppointments->groupBy(function (
            $appointment,
        ) {
            return $appointment->date->format("Y-m-d");
        });

        // Проверяем каждую дату в периоде
        $datesWithSlots = [];
        $checkDate = $startDate->copy();

        while ($checkDate->lte($endDate)) {
            $dateString = $checkDate->format("Y-m-d");

            // Для текущей даты используем уже вычисленные слоты
            if ($currentDate && $dateString === $currentDate) {
                $datesWithSlots[$dateString] = !empty($currentDateSlots);
                $checkDate->addDay();

                continue;
            }

            // Получаем временные окна для этой даты
            $timeWindows = $this->getAvailableTimeWindows($masters, $checkDate);

            if (empty($timeWindows)) {
                $datesWithSlots[$dateString] = false;
                $checkDate->addDay();

                continue;
            }

            // Генерируем слоты
            $slots = $this->generateSlots($timeWindows, $duration, $checkDate);

            if (empty($slots)) {
                $datesWithSlots[$dateString] = false;
                $checkDate->addDay();

                continue;
            }

            // Фильтруем по длительности
            $slotsFittingDuration = $this->filterSlotsByDuration(
                $slots,
                $duration,
                $timeWindows,
            );

            if (empty($slotsFittingDuration)) {
                $datesWithSlots[$dateString] = false;
                $checkDate->addDay();

                continue;
            }

            // Исключаем занятые слоты (используя уже загруженные appointments)
            $dateAppointments = $appointmentsByDate->get(
                $dateString,
                collect(),
            );
            $availableSlots = $this->excludeBookedSlotsFromCollection(
                $slotsFittingDuration,
                $dateAppointments,
                $duration,
                $masterId,
            );

            $datesWithSlots[$dateString] = !empty($availableSlots);
            $checkDate->addDay();
        }

        return $datesWithSlots;
    }

    /**
     * Исключить занятые слоты используя уже загруженную коллекцию appointments
     * (оптимизированная версия без дополнительных запросов к БД)
     *
     * @param  array  $slots  Массив слотов
     * @param  Collection  $appointments  Коллекция appointments с предзагруженными связями
     * @param  int  $duration  Длительность услуги
     * @param  int|null  $masterId  ID мастера
     * @return array Доступные слоты
     */
    protected function excludeBookedSlotsFromCollection(
        array $slots,
        Collection $appointments,
        int $duration,
        ?int $masterId = null,
    ): array {
        $availableSlots = [];

        foreach ($slots as $slot) {
            $slotTime = Carbon::parse($slot);
            $slotEndTime = $slotTime->copy()->addMinutes($duration);

            $isAvailable = true;

            foreach ($appointments as $appointment) {
                // Если мастер не указан при поиске слотов, но у записи есть мастер,
                // то эта запись не блокирует слот (так как слот может быть для другого мастера)
                if (!$masterId && $appointment->master_id) {
                    continue;
                }

                $appointmentTime = Carbon::parse($appointment->time);
                // Используем accessor final_duration, который теперь не вызовет N+1 благодаря with('service')
                $appointmentDuration =
                    $appointment->final_duration ?? $duration;
                $appointmentEndTime = $appointmentTime
                    ->copy()
                    ->addMinutes($appointmentDuration);

                // Проверяем пересечение временных интервалов
                $hasOverlap =
                    $slotTime->lt($appointmentEndTime) &&
                    $slotEndTime->gt($appointmentTime);

                if ($hasOverlap) {
                    $isAvailable = false;
                    break;
                }
            }

            if ($isAvailable) {
                $availableSlots[] = $slot;
            }
        }

        return $availableSlots;
    }

    /**
     * Установить интервал между слотами
     */
    public function setSlotInterval(int $minutes): self
    {
        $this->slotInterval = $minutes;

        return $this;
    }

    /**
     * Установить минимальный интервал между записями
     */
    public function setMinIntervalBetweenAppointments(int $minutes): self
    {
        $this->minIntervalBetweenAppointments = $minutes;

        return $this;
    }
}
