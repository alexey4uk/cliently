<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Requests\PublicAppointmentRequest;
use App\Models\Appointment;
use App\Models\Business;
use App\Models\Client;
use App\Services\AppointmentSlotService;
use App\Services\TelegramNotificationService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AppointmentController extends Controller
{
    protected AppointmentSlotService $slotService;

    public function __construct(AppointmentSlotService $slotService)
    {
        $this->slotService = $slotService;
    }

    /**
     * Шаг 1: Выбор локации
     */
    public function show(string $slug)
    {
        $business = Business::where('slug', $slug)->firstOrFail();
        $locations = $business->locations()->orderBy('name')->get();

        return view('appointments.public.select-location', compact('business', 'locations'))->with('currentStep', 1);
    }

    /**
     * Шаг 2: Выбор услуги (после выбора локации)
     */
    public function selectLocation(string $slug, $locationId)
    {
        $business = Business::where('slug', $slug)->firstOrFail();
        $location = $business->locations()->findOrFail($locationId);

        // Получаем услуги, которые:
        // 1. Привязаны к этой локации через связь location->services
        // 2. ИЛИ все активные услуги бизнеса (если связь не используется)
        $services = $location->services()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        // Если нет услуг, привязанных к локации, показываем все услуги бизнеса
        if ($services->isEmpty()) {
            $services = $business->services()
                ->where('is_active', true)
                ->orderBy('name')
                ->get();
        }

        return view('appointments.public.select-service', compact('business', 'location', 'services'))->with('currentStep', 2);
    }

    /**
     * Шаг 3: Выбор мастера (после выбора услуги)
     */
    public function selectService(string $slug, $locationId, $serviceId)
    {
        $business = Business::where('slug', $slug)->firstOrFail();
        $location = $business->locations()->findOrFail($locationId);
        $service = $business->services()->findOrFail($serviceId);

        // Получаем мастеров, которые:
        // 1. Работают в выбранной локации
        // 2. Предоставляют выбранную услугу
        $masters = $location->masters()
            ->where('is_active', true)
            ->whereHas('services', function ($q) use ($serviceId) {
                $q->where('services.id', $serviceId);
            })
            ->orderBy('first_name')
            ->get();

        // Если нет мастеров с услугой в локации, показываем всех мастеров локации
        if ($masters->isEmpty()) {
            $masters = $location->masters()
                ->where('is_active', true)
                ->orderBy('first_name')
                ->get();
        }

        // Если все еще нет мастеров, показываем всех мастеров бизнеса, которые предоставляют услугу
        if ($masters->isEmpty()) {
            $masters = $business->masters()
                ->where('is_active', true)
                ->whereHas('services', function ($q) use ($serviceId) {
                    $q->where('services.id', $serviceId);
                })
                ->orderBy('first_name')
                ->get();
        }

        return view('appointments.public.select-master', compact('business', 'location', 'service', 'masters'))->with('currentStep', 3);
    }

    /**
     * Шаг 4: Выбор даты и времени (после выбора мастера)
     */
    public function selectTime(string $slug, $locationId, $serviceId, $masterId)
    {
        $business = Business::where('slug', $slug)->firstOrFail();
        $location = $business->locations()->findOrFail($locationId);
        $service = $business->services()->findOrFail($serviceId);
        $master = $business->masters()->findOrFail($masterId);

        // Проверяем, что мастер работает в выбранной локации
        if (! $master->locations()->where('locations.id', $locationId)->exists()) {
            abort(404, 'Мастер не работает в выбранной локации');
        }

        // Проверяем, что мастер предоставляет услугу
        if (! $master->services()->where('services.id', $serviceId)->exists()) {
            abort(404, 'Мастер не предоставляет эту услугу');
        }

        $date = request()->get('date', Carbon::today()->format('Y-m-d'));
        $selectedDate = Carbon::parse($date);

        // ============ ПРОВЕРКА: ДАТА В ПРОШЛОМ ============
        if ($selectedDate->isPast() && ! $selectedDate->isToday()) {
            // Если дата в прошлом (не сегодня), перенаправляем на сегодня
            return redirect()->route('public.appointments.select-time', [
                'slug' => $slug,
                'locationId' => $locationId,
                'serviceId' => $serviceId,
                'masterId' => $masterId,
                'date' => Carbon::today()->format('Y-m-d'),
            ]);
        }
        // ============ КОНЕЦ ПРОВЕРКИ ============

        // Определяем, является ли это явным выбором пользователя
        $isExplicitDateChoice = request()->has('date');

        // Получаем доступные слоты для выбранной даты
        $debugInfo = [];
        $availableSlots = $this->slotService->getAvailableSlots(
            $serviceId,
            $date,
            $masterId,
            $locationId,
            $debugInfo
        );

        // Получаем даты со слотами с сегодня до конца следующего месяца
        $endOfNextMonth = Carbon::today()->endOfMonth()->addMonth()->endOfMonth();
        $datesWithSlots = [];

        // Начинаем с сегодня
        $checkDate = Carbon::today();

        while ($checkDate->lte($endOfNextMonth)) {
            // Для текущей даты используем уже полученные слоты
            if ($checkDate->format('Y-m-d') === $date) {
                $datesWithSlots[$checkDate->format('Y-m-d')] = ! empty($availableSlots);
            } else {
                // Для остальных дат проверяем слоты
                $slots = $this->slotService->getAvailableSlots(
                    $serviceId,
                    $checkDate->format('Y-m-d'),
                    $masterId,
                    $locationId,
                    $debugInfo
                );

                $datesWithSlots[$checkDate->format('Y-m-d')] = ! empty($slots);
            }

            $checkDate->addDay();
        }

        // Если для запрошенной даты нет слотов, ищем ближайшую дату со слотами
        // Но делаем это только если:
        // 1. Это не явный выбор пользователя (первый визит или date не указан в URL)
        // 2. ИЛИ это сегодняшняя дата (чтобы не показывать сегодня, если нет слотов)
        if (empty($availableSlots)) {
            // Определяем, нужно ли искать другую дату
            $shouldFindNextDate = ! $isExplicitDateChoice || $selectedDate->isToday();

            if ($shouldFindNextDate) {
                $nextAvailableDate = $this->findNextAvailableDate(
                    $serviceId,
                    $masterId,
                    $locationId,
                    $selectedDate
                );

                // if ($nextAvailableDate) {
                //     // Перенаправляем на ту же страницу с ближайшей датой
                //     return redirect()->route('public.appointments.select-time', [
                //         'slug' => $slug,
                //         'locationId' => $locationId,
                //         'serviceId' => $serviceId,
                //         'masterId' => $masterId,
                //         'date' => $nextAvailableDate->format('Y-m-d'),
                //     ]);
                // }
            }
            // Если пользователь явно выбрал дату без слотов - показываем пустой список
        }

        return view('appointments.public.select-time', compact(
            'business',
            'location',
            'service',
            'master',
            'selectedDate',
            'availableSlots',
            'date',
            'datesWithSlots'
        ))->with('currentStep', 4);
    }

    /**
     * Найти ближайшую дату со слотами
     *
     * @param  int  $serviceId  ID услуги
     * @param  int|null  $masterId  ID мастера (если указан)
     * @param  int|null  $locationId  ID локации (если указана)
     * @param  Carbon  $startDate  Дата, с которой начинаем поиск
     * @param  int  $maxDays  Максимальное количество дней для поиска вперед
     * @return Carbon|null Ближайшая дата со слотами или null
     */
    private function findNextAvailableDate($serviceId, $masterId, $locationId, Carbon $startDate, $maxDays = 60): ?Carbon
    {
        // Начинаем поиск с завтрашнего дня
        $checkDate = $startDate->copy()->addDay();
        $endDate = $startDate->copy()->addDays($maxDays);

        while ($checkDate->lte($endDate)) {
            $debugInfo = [];

            try {
                $slots = $this->slotService->getAvailableSlots(
                    $serviceId,
                    $checkDate->format('Y-m-d'),
                    $masterId,
                    $locationId,
                    $debugInfo
                );

                if (! empty($slots)) {
                    return $checkDate;
                }
            } catch (\Exception $e) {
                // Логируем ошибку, но продолжаем поиск
                Log::warning('Ошибка при поиске слотов для даты', [
                    'date' => $checkDate->format('Y-m-d'),
                    'error' => $e->getMessage(),
                ]);
            }

            $checkDate->addDay();
        }

        return null;
    }

    /**
     * Сохранить публичную запись
     */
    public function store(PublicAppointmentRequest $request, string $slug)
    {
        $business = Business::where('slug', $slug)->firstOrFail();
        $validated = $request->validated();

        // Находим или создаем клиента
        $client = Client::firstOrCreate(
            [
                'business_id' => $business->id,
                'phone' => $validated['phone'],
            ],
            [
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'] ?? null,
                'email' => $validated['email'] ?? null,
            ]
        );

        // Обновляем данные клиента, если они изменились
        $client->update([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'] ?? null,
            'email' => $validated['email'] ?? null,
        ]);

        // Создаем запись
        $appointment = Appointment::create([
            'business_id' => $business->id,
            'client_id' => $client->id,
            'service_id' => $validated['service_id'],
            'master_id' => $validated['master_id'],
            'location_id' => $validated['location_id'],
            'date' => $validated['date'],
            'time' => $validated['time'],
            'status' => 'pending',
            'notes' => $validated['notes'] ?? null,
        ]);

        TelegramNotificationService::sendAppointmentCreated($appointment);

        return redirect()
            ->route('public.appointments.success', [$business->slug, $appointment->token])
            ->with('success', 'Ваша запись успешно создана! Мы свяжемся с вами для подтверждения.');
    }

    /**
     * Страница успешной записи
     */
    public function success(string $slug, string $token)
    {
        $appointment = null;

        $business = Business::where('slug', $slug)->firstOrFail();

        if ($token) {
            $appointment = Appointment::where('token', $token)
                ->where('business_id', $business->id)
                ->with(['service', 'master', 'location', 'client'])
                ->first();
        }

        if (! $business || ! $appointment) {
            abort(404);
        }

        return view('appointments.public.success', compact('business', 'appointment', 'token'));
    }

    /**
     * Просмотр записи по токену (упрощенная ссылка)
     */
    public function viewByToken(string $token)
    {
        $appointment = Appointment::where('token', $token)
            ->with(['service', 'master', 'location', 'client', 'business'])
            ->firstOrFail();

        $business = $appointment->business;

        return view('appointments.public.view', compact('business', 'appointment'));
    }

    /**
     * Отмена записи по токену (упрощенная ссылка)
     */
    public function cancelByToken(Request $request, string $token)
    {
        $appointment = Appointment::where('token', $token)
            ->with('business')
            ->firstOrFail();

        // Проверяем, можно ли отменить запись
        if (in_array($appointment->status, ['completed', 'cancelled'])) {
            return redirect()
                ->route('public.appointment.view', ['token' => $token])
                ->with('error', 'Эту запись нельзя отменить.');
        }

        $appointment->update(['status' => 'cancelled']);

        return redirect()
            ->route('public.appointment.view', ['token' => $token])
            ->with('success', 'Запись успешно отменена.');
    }

    /**
     * Просмотр записи по токену (старый маршрут для обратной совместимости)
     */
    public function view(string $slug, string $token)
    {
        $business = Business::where('slug', $slug)->firstOrFail();
        $appointment = Appointment::where('token', $token)
            ->where('business_id', $business->id)
            ->with(['service', 'master', 'location', 'client'])
            ->firstOrFail();

        return view('appointments.public.view', compact('business', 'appointment'));
    }

    /**
     * Отмена записи по токену (старый маршрут для обратной совместимости)
     */
    public function cancel(Request $request, string $slug, string $token)
    {
        $business = Business::where('slug', $slug)->firstOrFail();
        $appointment = Appointment::where('token', $token)
            ->where('business_id', $business->id)
            ->firstOrFail();

        // Проверяем, можно ли отменить запись
        if (in_array($appointment->status, ['completed', 'cancelled'])) {
            return redirect()
                ->route('public.appointments.show', ['slug' => $slug, 'token' => $token])
                ->with('error', 'Эту запись нельзя отменить.');
        }

        $appointment->update(['status' => 'cancelled']);

        return redirect()
            ->route('public.appointments.show', ['slug' => $slug, 'token' => $token])
            ->with('success', 'Запись успешно отменена.');
    }
}
