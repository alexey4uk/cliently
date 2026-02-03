<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Requests\PublicAppointmentRequest;
use App\Models\Appointment;
use App\Models\Client;
use App\Repositories\BusinessRepositoryInterface;
use App\Services\AppointmentNotificationService;
use App\Services\AppointmentSlotService;
use App\Services\SubscriptionService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AppointmentController extends Controller
{
    protected AppointmentSlotService $slotService;

    protected BusinessRepositoryInterface $businessRepository;

    public function __construct(
        AppointmentSlotService $slotService,
        BusinessRepositoryInterface $businessRepository
    ) {
        $this->slotService = $slotService;
        $this->businessRepository = $businessRepository;
    }

    /**
     * Шаг 1: Выбор локации
     */
    public function show(string $slug)
    {
        $business = $this->businessRepository->findBySlug($slug);
        if (! $business) {
            abort(404);
        }

        // Проверяем, включена ли онлайн-запись
        if ($business->online_booking_enabled === false) {
            return view('appointments.public.disabled', compact('business'));
        }

        $locations = $business->locations()->orderBy('name')->get();

        return view('appointments.public.select-location', compact('business', 'locations'))->with('currentStep', 1);
    }

    /**
     * Шаг 2: Выбор услуги (после выбора локации)
     */
    public function selectLocation(string $slug, $locationId)
    {
        $business = $this->businessRepository->findBySlug($slug);
        if (! $business) {
            abort(404);
        }

        // Проверяем, включена ли онлайн-запись
        if ($business->online_booking_enabled === false) {
            return view('appointments.public.disabled', compact('business'));
        }

        $location = $business->locations()->findOrFail($locationId);

        // Получаем все активные услуги бизнеса
        $services = $business->services()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('appointments.public.select-service', compact('business', 'location', 'services'))->with('currentStep', 2);
    }

    /**
     * Шаг 3: Выбор мастера (после выбора услуги)
     */
    public function selectService(string $slug, $locationId, $serviceId)
    {
        $business = $this->businessRepository->findBySlug($slug);
        if (! $business) {
            abort(404);
        }

        // Проверяем, включена ли онлайн-запись
        if ($business->online_booking_enabled === false) {
            return view('appointments.public.disabled', compact('business'));
        }

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
        $business = $this->businessRepository->findBySlug($slug);
        if (! $business) {
            abort(404);
        }

        // Проверяем, включена ли онлайн-запись
        if ($business->online_booking_enabled === false) {
            return view('appointments.public.disabled', compact('business'));
        }

        // ОПТИМИЗАЦИЯ: Загружаем всё одним запросом с проверками
        try {
            $location = $business->locations()->findOrFail($locationId);
            $service = $business->services()->findOrFail($serviceId);

            // Загружаем мастера с предзагрузкой связей для проверки
            $master = $business->masters()
                ->with(['locations', 'services'])
                ->findOrFail($masterId);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            abort(404);
        }

        // Проверяем, что мастер работает в выбранной локации
        if (! $master->locations->contains('id', $locationId)) {
            abort(404, 'Мастер не работает в выбранной локации');
        }

        // Проверяем, что мастер предоставляет услугу
        if (! $master->services->contains('id', $serviceId)) {
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

        // ОПТИМИЗАЦИЯ: Получаем даты со слотами с сегодня до конца следующего месяца
        // Используем единый метод, который вернет и слоты для текущей даты, и информацию о других датах
        // Это избегает дублирующихся запросов к БД
        $endOfNextMonth = Carbon::today()->endOfMonth()->addMonth()->endOfMonth();

        $result = $this->slotService->getAvailableSlotsWithCalendar(
            $service,       // Передаем объект service вместо ID
            $master,        // Передаем объект master вместо ID
            $selectedDate,  // Текущая выбранная дата
            Carbon::today(),
            $endOfNextMonth
        );

        $availableSlots = $result['slots'];
        $datesWithSlots = $result['calendar'];

        // ОПТИМИЗАЦИЯ: Календарь уже содержит всю информацию о датах со слотами
        // Пользователь может сам выбрать нужную дату в календаре
        // Больше не нужно искать следующую доступную дату и делать дополнительные запросы

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
        $business = $this->businessRepository->findBySlug($slug);
        if (! $business) {
            abort(404);
        }

        // Проверяем, включена ли онлайн-запись
        if ($business->online_booking_enabled === false) {
            return redirect()->route('public.appointments.show', ['slug' => $slug])
                ->with('error', 'Онлайн-запись временно недоступна. Пожалуйста, свяжитесь с нами напрямую.');
        }

        $validated = $request->validated();

        // Получаем пользователя через бизнес
        $user = $business->users()->first();
        if (! $user) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Ошибка при обработке запроса. Пожалуйста, попробуйте позже.');
        }

        // Проверяем лимит записей в месяц
        $subscriptionService = app(SubscriptionService::class);
        if (! $subscriptionService->canCreateAppointment($user)) {
            \App\Services\AdminNotificationService::notifySubscriptionLimitExceededIfNotThrottled($business, 'max_appointments_per_month');

            return redirect()->back()
                ->withInput()
                ->with('error', 'Достигнут месячный лимит записей. Пожалуйста, свяжитесь с нами напрямую для записи.');
        }

        $client = Client::where('business_id', $business->id)
            ->whereHas('phones', fn ($q) => $q->where('phone', $validated['phone']))
            ->first();

        if (! $client) {
            if (! $subscriptionService->canCreateClient($user)) {
                \App\Services\AdminNotificationService::notifySubscriptionLimitExceededIfNotThrottled($business, 'max_clients');

                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Достигнут лимит клиентов. Пожалуйста, свяжитесь с нами напрямую для записи по телефону: '.$business->phone);
            }

            $client = Client::create([
                'business_id' => $business->id,
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'] ?? null,
                'email' => $validated['email'] ?? null,
            ]);

            $country = isset($validated['phone_country_id'])
                ? \App\Models\Country::find($validated['phone_country_id'])
                : \App\Models\Country::findByPhonePrefix($validated['phone']);
            $countryId = $country?->id ?? \App\Models\Country::where('code', 'BY')->value('id');
            if ($countryId) {
                $client->phones()->create([
                    'country_id' => $countryId,
                    'phone' => $validated['phone'],
                    'type' => 'primary',
                ]);
            }

            $isNewClient = true;
        } else {
            $isNewClient = false;
            $client->update([
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'] ?? $client->last_name,
                'email' => $validated['email'] ?? $client->email,
            ]);
        }

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

        // Увеличиваем usage для месячной метрики
        $subscriptionService->incrementUsage($user, 'max_appointments_per_month');

        // Отправить системное уведомление о создании записи (включая Telegram для каждого пользователя)
        AppointmentNotificationService::notifyCreated($appointment);

        // Отправить уведомление о новом клиенте, если клиент был создан
        if ($isNewClient) {
            AppointmentNotificationService::notifyNewClient($appointment);
        }

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

        $business = $this->businessRepository->findBySlug($slug);
        if (! $business) {
            abort(404);
        }

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
        $business = $this->businessRepository->findBySlug($slug);
        if (! $business) {
            abort(404);
        }
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
        $business = $this->businessRepository->findBySlug($slug);
        if (! $business) {
            abort(404);
        }
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
