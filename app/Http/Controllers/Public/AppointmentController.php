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

        // Если в филиале нет ни одного мастера — показываем сообщение и предложение выбрать другой филиал
        if (! $location->masters()->where('is_active', true)->exists()) {
            return view('appointments.public.no-masters-in-location', [
                'business' => $business,
                'location' => $location,
                'backUrl' => route('public.appointments.show', $business->slug),
                'backText' => 'Выбрать другой филиал',
            ]);
        }

        // Показываем только услуги, которые оказывает хотя бы один мастер этого филиала
        $services = $business->services()
            ->where('is_active', true)
            ->whereHas('masters', function ($q) use ($locationId) {
                $q->where('masters.is_active', true)
                    ->whereHas('locations', fn ($q2) => $q2->where('locations.id', $locationId));
            })
            ->orderBy('name')
            ->get();

        // В филиале есть мастера, но ни один не оказывает услуги бизнеса — не путаем клиента, сразу сообщаем
        if ($services->isEmpty()) {
            return view('appointments.public.no-services-in-location', compact('business', 'location'));
        }

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

        // Если в филиале нет ни одного мастера (прямой переход по URL или мастера убрали)
        if (! $location->masters()->where('is_active', true)->exists()) {
            return view('appointments.public.no-masters-in-location', [
                'business' => $business,
                'location' => $location,
                'backUrl' => route('public.appointments.show', $business->slug),
                'backText' => 'Выбрать другой филиал',
            ]);
        }

        // Получаем мастеров, которые работают в локации и предоставляют выбранную услугу
        $masters = $location->masters()
            ->with('services')
            ->where('is_active', true)
            ->whereHas('services', function ($q) use ($serviceId) {
                $q->where('services.id', $serviceId);
            })
            ->orderBy('first_name')
            ->get();

        // В филиале есть мастера, но никто не оказывает эту услугу — показываем отдельный экран
        if ($masters->isEmpty()) {
            return view('appointments.public.no-masters-for-service', compact('business', 'location', 'service'));
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
     * Шаг 4 (альт.): Выбор даты и времени — любой мастер
     */
    public function selectTimeAny(string $slug, $locationId, $serviceId)
    {
        $business = $this->businessRepository->findBySlug($slug);
        if (! $business) {
            abort(404);
        }

        if ($business->online_booking_enabled === false) {
            return view('appointments.public.disabled', compact('business'));
        }

        $location = $business->locations()->findOrFail($locationId);
        $service = $business->services()->findOrFail($serviceId);

        // Если в филиале нет мастеров (выбран «любой мастер») — не показываем календарь, предлагаем вернуться
        if (! $location->masters()->where('is_active', true)->exists()) {
            return view('appointments.public.no-masters-in-location', [
                'business' => $business,
                'location' => $location,
                'backUrl' => route('public.appointments.select-location', [
                    'slug' => $business->slug,
                    'locationId' => $locationId,
                ]),
                'backText' => 'К выбору услуги',
            ]);
        }

        $date = request()->get('date', Carbon::today()->format('Y-m-d'));
        $selectedDate = Carbon::parse($date);

        if ($selectedDate->isPast() && ! $selectedDate->isToday()) {
            return redirect()->route('public.appointments.select-time-any', [
                'slug' => $slug,
                'locationId' => $locationId,
                'serviceId' => $serviceId,
                'date' => Carbon::today()->format('Y-m-d'),
            ]);
        }

        $endOfNextMonth = Carbon::today()->endOfMonth()->addMonth()->endOfMonth();
        $result = $this->slotService->getAvailableSlotsWithCalendarForAnyMaster(
            $service,
            (int) $locationId,
            $selectedDate,
            Carbon::today(),
            $endOfNextMonth,
        );

        $availableSlots = $result['slots'];
        $datesWithSlots = $result['calendar'];
        $master = null;

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

        $phone = $validated['phone'];
        $client = Client::where('business_id', $business->id)
            ->where('phone', $phone)
            ->first();

        if (! $client) {
            $client = Client::where('business_id', $business->id)
                ->whereHas('phones', fn ($q) => $q->where('phone', $phone))
                ->first();
        }

        // ISO код страны — передаёт компонент, в БД пишем как есть (таблица countries не участвует)
        $phoneCountryCode = ! empty($validated['phone_country_code'])
            ? strtoupper(substr($validated['phone_country_code'], 0, 2))
            : null;

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
                'phone' => $phone,
                'phone_country_code' => $phoneCountryCode,
            ]);

            $isNewClient = true;
        } else {
            $isNewClient = false;
            $client->update([
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'] ?? $client->last_name,
                'email' => $validated['email'] ?? $client->email,
                'phone' => $phone,
                'phone_country_code' => $phoneCountryCode,
            ]);
        }

        // Создаем запись (master_id может быть null при записи «к любому мастеру»)
        $appointment = Appointment::create([
            'business_id' => $business->id,
            'client_id' => $client->id,
            'service_id' => $validated['service_id'],
            'master_id' => $validated['master_id'] ?? null,
            'location_id' => $validated['location_id'],
            'date' => $validated['date'],
            'time' => $validated['time'],
            'status' => 'pending',
            'source' => 'online',
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
