<?php

namespace App\Http\Controllers;

use App\Http\Requests\AppointmentRequest;
use App\Models\Appointment;
use App\Models\Master;
use App\Repositories\AppointmentRepositoryInterface;
use App\Repositories\ClientRepositoryInterface;
use App\Repositories\MasterRepositoryInterface;
use App\Repositories\ServiceRepositoryInterface;
use App\Services\AppointmentNotificationService;
use App\Services\BusinessRolePermissionService;
use App\Services\SubscriptionService;
use App\Services\TelegramNotificationService;
use App\Traits\HasOwnDataFiltering;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AppointmentsController extends Controller
{
    use HasOwnDataFiltering;

    private AppointmentRepositoryInterface $appointmentRepository;

    private ClientRepositoryInterface $clientRepository;

    private ServiceRepositoryInterface $serviceRepository;

    private MasterRepositoryInterface $masterRepository;

    public function __construct(
        AppointmentRepositoryInterface $appointmentRepository,
        ClientRepositoryInterface $clientRepository,
        ServiceRepositoryInterface $serviceRepository,
        MasterRepositoryInterface $masterRepository
    ) {
        $this->appointmentRepository = $appointmentRepository;
        $this->clientRepository = $clientRepository;
        $this->serviceRepository = $serviceRepository;
        $this->masterRepository = $masterRepository;
    }

    private function checkAppointmentBelongsToBusiness(Appointment $appointment)
    {
        $business = $this->getCurrentBusiness();

        if (! $business) {
            return redirect()->route('appointments.index')->with('error', 'Сначала создайте бизнес или примите приглашение.');
        }

        if (! $this->appointmentRepository->belongsToBusiness($appointment->id, $business->id)) {
            return redirect()->route('appointments.index');
        }

        return null;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $business = $this->getCurrentBusiness();

        if (! $business) {
            $view = $request->get('view', 'table');
            $currentMonth = $request->get('month', Carbon::now()->format('Y-m'));
            try {
                $selectedDate = Carbon::parse($currentMonth.'-01');
            } catch (\Exception $e) {
                $selectedDate = Carbon::now()->startOfMonth();
            }

            return view('appointments.index', [
                'business' => null,
                'appointments' => collect(),
                'appointmentsByDate' => collect(),
                'view' => $view,
                'currentMonth' => $currentMonth,
                'selectedDate' => $selectedDate,
                'search' => $request->get('search', ''),
                'date' => $request->get('date', ''),
                'status' => $request->get('status', ''),
                'service_id' => $request->get('service_id', ''),
                'master_id' => $request->get('master_id', ''),
                'sort' => $request->get('sort', 'date'),
                'direction' => $request->get('direction', 'desc'),
                'perPage' => 20,
                'services' => collect(),
                'masters' => collect(),
                'canViewAppointments' => false,
                'canExportAppointments' => false,
                'canUpdateAppointments' => false,
                'canCreateAppointments' => false,
                'canCreateAppointment' => false,
                'hasAnyAppointmentAction' => false,
            ]);
        }

        $view = $request->get('view', 'table'); // table или calendar
        $currentMonth = $request->get('month', Carbon::now()->format('Y-m'));

        try {
            $selectedDate = Carbon::parse($currentMonth.'-01');
        } catch (\Exception $e) {
            $selectedDate = Carbon::now()->startOfMonth();
        }

        // Получаем роль пользователя для проверки прав
        $role = $this->getCurrentBusinessRole();
        $permissionService = app(\App\Services\BusinessRolePermissionService::class);

        // Сортировка и пагинация
        $sort = $request->get('sort', 'date');
        $direction = $request->get('direction', 'desc');
        $perPage = $request->get('per_page', 20);
        $perPage = in_array($perPage, [15, 30, 50]) ? $perPage : 20;

        $filters = [
            'view' => $view,
            'month' => $currentMonth,
            'date' => $request->get('date'),
            'status' => $request->get('status'),
            'search' => $request->get('search'),
            'service_id' => $request->get('service_id'),
            'master_id' => $request->get('master_id'),
            'sort' => $sort,
            'direction' => $direction,
        ];

        // Применяем фильтр "только свои данные" если нужно
        if ($role && $permissionService->hasOwnDataPermission($role->id, 'client.appointments.view')) {
            $masterId = $this->getCurrentUserMasterId($business);
            if ($masterId) {
                $filters['master_id'] = $masterId; // Принудительно фильтруем по мастеру
            }
        }

        if ($view === 'calendar') {
            $allAppointments = $this->appointmentRepository->getForCalendar($business->id, $currentMonth, $filters);

            // Применяем фильтр "только свои данные" для календаря
            if ($role && $permissionService->hasOwnDataPermission($role->id, 'client.appointments.view')) {
                $masterId = $this->getCurrentUserMasterId($business);
                if ($masterId) {
                    $allAppointments = $allAppointments->where('master_id', $masterId);
                } else {
                    $allAppointments = collect(); // Пустая коллекция если нет мастера
                }
            }

            // Группируем по датам
            $appointmentsByDate = $allAppointments->groupBy(function ($appointment) {
                return $appointment->date->format('Y-m-d');
            });

            $appointments = $allAppointments;
        } else {
            $appointments = $this->appointmentRepository->getFilteredForBusiness($business->id, $filters, $perPage);
            $appointmentsByDate = collect();
        }

        $services = $this->serviceRepository->getActiveByBusiness($business->id);
        $masters = $this->masterRepository->getActiveByBusiness($business->id);

        // Права через BusinessRolePermissionService (единый источник)
        $canViewAppointments = $role && $permissionService->hasPermission($role->id, 'client.appointments.view');
        $canExportAppointments = $role && $permissionService->hasPermission($role->id, 'client.appointments.export');
        $canUpdateAppointments = $role && $permissionService->hasPermission($role->id, 'client.appointments.update');
        $canCreateAppointments = $role && $permissionService->hasPermission($role->id, 'client.appointments.create');
        $hasAnyAppointmentAction = $canViewAppointments || $canUpdateAppointments;
        $canCreateAppointment = $canCreateAppointments && app(SubscriptionService::class)->canCreateAppointment(Auth::user());

        return view('appointments.index', [
            'business' => $business,
            'appointments' => $appointments,
            'appointmentsByDate' => $appointmentsByDate,
            'view' => $view,
            'currentMonth' => $currentMonth,
            'selectedDate' => $selectedDate,
            'search' => $request->get('search', ''),
            'date' => $request->get('date', ''),
            'status' => $request->get('status', ''),
            'service_id' => $request->get('service_id', ''),
            'master_id' => $request->get('master_id', ''),
            'sort' => $sort,
            'direction' => $direction,
            'perPage' => $perPage,
            'services' => $services,
            'masters' => $masters,
            'canViewAppointments' => $canViewAppointments,
            'canExportAppointments' => $canExportAppointments,
            'canUpdateAppointments' => $canUpdateAppointments,
            'canCreateAppointments' => $canCreateAppointments,
            'canCreateAppointment' => $canCreateAppointment,
            'hasAnyAppointmentAction' => $hasAnyAppointmentAction,
        ]);
    }

    /**
     * Display the calendar view of appointments.
     */
    public function calendar(Request $request)
    {
        $business = $this->getCurrentBusiness();

        if (! $business) {
            $currentMonth = $request->get('month', Carbon::now()->format('Y-m'));
            try {
                $selectedDate = Carbon::parse($currentMonth.'-01');
            } catch (\Exception $e) {
                $selectedDate = Carbon::now()->startOfMonth();
            }

            return view('appointments.index', [
                'business' => null,
                'appointments' => collect(),
                'appointmentsByDate' => collect(),
                'view' => 'calendar',
                'currentMonth' => $currentMonth,
                'selectedDate' => $selectedDate,
                'search' => '',
                'date' => '',
                'status' => '',
                'service_id' => '',
                'master_id' => '',
                'sort' => 'date',
                'direction' => 'desc',
                'perPage' => 20,
                'services' => collect(),
                'masters' => collect(),
                'canViewAppointments' => false,
                'canExportAppointments' => false,
                'canUpdateAppointments' => false,
                'canCreateAppointments' => false,
                'canCreateAppointment' => false,
                'hasAnyAppointmentAction' => false,
            ]);
        }

        $view = 'calendar'; // Всегда используем календарный вид
        $currentMonth = $request->get('month', Carbon::now()->format('Y-m'));

        try {
            $selectedDate = Carbon::parse($currentMonth.'-01');
        } catch (\Exception $e) {
            $selectedDate = Carbon::now()->startOfMonth();
        }

        // Получаем роль пользователя для проверки прав
        $role = $this->getCurrentBusinessRole();
        $permissionService = app(\App\Services\BusinessRolePermissionService::class);

        // Сортировка и пагинация (для совместимости, хотя в календаре не используется)
        $sort = $request->get('sort', 'date');
        $direction = $request->get('direction', 'desc');
        $perPage = $request->get('per_page', 20);
        $perPage = in_array($perPage, [15, 30, 50]) ? $perPage : 20;

        $filters = [
            'view' => $view,
            'month' => $currentMonth,
            'date' => $request->get('date'),
            'status' => $request->get('status'),
            'search' => $request->get('search'),
            'service_id' => $request->get('service_id'),
            'master_id' => $request->get('master_id'),
            'sort' => $sort,
            'direction' => $direction,
        ];

        // Всегда используем календарную логику
        $allAppointments = $this->appointmentRepository->getForCalendar($business->id, $currentMonth, $filters);

        // Применяем фильтр "только свои данные" для календаря
        if ($role && $permissionService->hasOwnDataPermission($role->id, 'client.appointments.view')) {
            $masterId = $this->getCurrentUserMasterId($business);
            if ($masterId) {
                $allAppointments = $allAppointments->where('master_id', $masterId);
            } else {
                $allAppointments = collect(); // Пустая коллекция если нет мастера
            }
        }

        // Группируем по датам
        $appointmentsByDate = $allAppointments->groupBy(function ($appointment) {
            return $appointment->date->format('Y-m-d');
        });

        $appointments = $allAppointments;

        return view('appointments.calendar', [
            'business' => $business,
            'appointments' => $appointments,
            'appointmentsByDate' => $appointmentsByDate,
            'view' => $view,
            'currentMonth' => $currentMonth,
            'selectedDate' => $selectedDate,
            'search' => $request->get('search', ''),
            'date' => $request->get('date', ''),
            'status' => $request->get('status', ''),
            'service_id' => $request->get('service_id', ''),
            'master_id' => $request->get('master_id', ''),
            'sort' => $sort,
            'direction' => $direction,
            'perPage' => $perPage,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $business = $this->getCurrentBusiness();

        if (! $business) {
            return view('appointments.create', [
                'business' => null,
                'clients' => collect(),
                'services' => collect(),
                'masters' => collect(),
                'locations' => collect(),
                'selectedClientId' => $request->get('client_id'),
            ]);
        }

        $selectedClientId = $request->get('client_id');

        return view('appointments.create', [
            'business' => $business,
            'clients' => $business->clients()->orderBy('first_name')->get(),
            'services' => $business->services()->where('is_active', true)->orderBy('name')->get(),
            'masters' => $business->masters()->where('is_active', true)->orderBy('first_name')->get(),
            'locations' => $business->locations()->orderBy('name')->get(),
            'selectedClientId' => $selectedClientId,
        ]);
    }

    private function validateClientAndService($clientId, $serviceId, $businessId)
    {
        $client = $this->clientRepository->findByIdAndBusiness($clientId, $businessId);
        if (! $client) {
            abort(404, 'Клиент не найден');
        }

        $service = $this->serviceRepository->find($serviceId);
        if (! $service || $service->business_id !== $businessId) {
            abort(404, 'Услуга не найдена');
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AppointmentRequest $request)
    {
        $business = $this->getCurrentBusiness();

        if (! $business) {
            return redirect()->back()->with('error', 'Сначала создайте бизнес или примите приглашение.');
        }

        $user = Auth::user();

        // Проверка лимита записей в месяц
        $subscriptionService = app(SubscriptionService::class);
        if (! $subscriptionService->canCreateAppointment($user)) {
            \App\Services\AdminNotificationService::notifySubscriptionLimitExceededIfNotThrottled($business, 'max_appointments_per_month');

            return redirect()->back()
                ->withInput()
                ->with('error', \App\Services\SubscriptionService::planLimitErrorMessage());
        }

        $validated = $request->validated();

        $this->validateClientAndService($validated['client_id'], $validated['service_id'], $business->id);

        $appointment = Appointment::create([
            'business_id' => $business->id,
            'client_id' => $validated['client_id'],
            'service_id' => $validated['service_id'],
            'master_id' => $validated['master_id'] ?? null,
            'location_id' => $validated['location_id'] ?? null,
            'date' => $validated['date'],
            'time' => $validated['time'],
            'status' => $validated['status'] ?? 'pending',
            'source' => 'manual',
            'notes' => $validated['notes'] ?? null,
            'duration' => $validated['duration'] ?? null,
            'price' => $validated['price'] ?? null,
        ]);

        // Увеличиваем usage для месячной метрики
        $subscriptionService->incrementUsage($user, 'max_appointments_per_month');

        // Отправить системное уведомление (включая Telegram); создателю не отправляем
        AppointmentNotificationService::notifyCreated($appointment, $user);

        return redirect()->route('appointments.index')->with('success', 'Запись создана');
    }

    /**
     * Display the specified resource.
     */
    public function show(Appointment $appointment)
    {
        $redirect = $this->checkAppointmentBelongsToBusiness($appointment);
        if ($redirect) {
            return $redirect;
        }

        $business = $this->getCurrentBusiness();
        $role = $this->getCurrentBusinessRole();

        // Проверяем право на просмотр этой конкретной записи
        if ($role && ! $this->canViewAppointment($business, $role->id, 'client.appointments.view', $appointment->id)) {
            return redirect()->route('appointments.index')
                ->with('error', 'У вас нет доступа к этой записи.');
        }

        $appointment->load(['client', 'service', 'master', 'location']);

        $permissionService = app(BusinessRolePermissionService::class);
        $canUpdateAppointments = $role && $permissionService->hasPermission($role->id, 'client.appointments.update');

        // Мастера с свободным слотом на дату и время записи (для быстрого назначения)
        $mastersForAssign = collect();
        if ($canUpdateAppointments && ! $appointment->master_id && ! in_array($appointment->status, ['cancelled', 'completed'], true)) {
            $candidates = $business->masters()
                ->where('is_active', true)
                ->whereHas('services', fn ($q) => $q->where('services.id', $appointment->service_id))
                ->orderBy('first_name')
                ->get();
            $dateStr = $appointment->date->format('Y-m-d');
            $date = Carbon::parse($dateStr);
            $timeStr = Carbon::parse($appointment->time)->format('H:i');
            $duration = (int) $appointment->final_duration;

            $mastersForAssign = $candidates->filter(function (Master $master) use ($appointment, $date, $timeStr, $duration) {
                if ($master->isDayOff($date)) {
                    return false;
                }
                $workingTime = $master->getWorkingTimeForDate($date);
                if (! $workingTime) {
                    return false;
                }
                $startTime = Carbon::parse($timeStr);
                $endTime = $startTime->copy()->addMinutes($duration);
                $workStart = Carbon::parse($workingTime['from']);
                $workEnd = Carbon::parse($workingTime['to']);
                if ($startTime->lt($workStart) || $endTime->gt($workEnd)) {
                    return false;
                }
                if (Appointment::hasConflictForMaster(
                    (int) $master->id,
                    $date,
                    $timeStr,
                    $duration,
                    (int) $appointment->id
                )) {
                    return false;
                }

                return true;
            })->values();
        }

        return view('appointments.show', [
            'business' => $business,
            'appointment' => $appointment,
            'canUpdateAppointments' => $canUpdateAppointments,
            'mastersForAssign' => $mastersForAssign,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Appointment $appointment)
    {
        $redirect = $this->checkAppointmentBelongsToBusiness($appointment);
        if ($redirect) {
            return $redirect;
        }

        $business = $this->getCurrentBusiness();
        $role = $this->getCurrentBusinessRole();

        // Проверяем право на просмотр этой конкретной записи
        if ($role && ! $this->canViewAppointment($business, $role->id, 'client.appointments.view', $appointment->id)) {
            return redirect()->route('appointments.index')
                ->with('error', 'У вас нет доступа к этой записи.');
        }

        $appointment->load(['client', 'service', 'master', 'location']);

        return view('appointments.edit', [
            'business' => $business,
            'appointment' => $appointment,
            'clients' => $business->clients()->orderBy('first_name')->get(),
            'services' => $business->services()->where('is_active', true)->orderBy('name')->get(),
            'masters' => $business->masters()->where('is_active', true)->orderBy('first_name')->get(),
            'locations' => $business->locations()->orderBy('name')->get(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AppointmentRequest $request, Appointment $appointment)
    {
        $redirect = $this->checkAppointmentBelongsToBusiness($appointment);
        if ($redirect) {
            return $redirect;
        }

        $business = $this->getCurrentBusiness();
        $role = $this->getCurrentBusinessRole();

        // Проверяем право на просмотр этой конкретной записи
        if ($role && ! $this->canViewAppointment($business, $role->id, 'client.appointments.view', $appointment->id)) {
            return redirect()->route('appointments.index')
                ->with('error', 'У вас нет доступа к этой записи.');
        }
        $validated = $request->validated();
        $oldStatus = $appointment->status;

        $this->validateClientAndService($validated['client_id'], $validated['service_id'], $business->id);

        $appointment->update([
            'client_id' => $validated['client_id'],
            'service_id' => $validated['service_id'],
            'master_id' => $validated['master_id'] ?? null,
            'location_id' => $validated['location_id'] ?? null,
            'date' => $validated['date'],
            'time' => $validated['time'],
            'status' => $validated['status'] ?? $appointment->status,
            'notes' => $validated['notes'] ?? null,
            'duration' => $validated['duration'] ?? null,
            'price' => $validated['price'] ?? null,
        ]);

        // Уведомления только при смене статуса и только при «важных» переходах (не на любой чих)
        if ($appointment->status !== $oldStatus) {
            if (AppointmentNotificationService::shouldNotifyClientOnStatusChange($oldStatus, $appointment->status)) {
                TelegramNotificationService::sendAppointmentStatusChangedForClient($appointment, $oldStatus);
            }
            if (AppointmentNotificationService::shouldNotifyStaffOnStatusChange($oldStatus, $appointment->status)) {
                AppointmentNotificationService::notifyStatusChanged($appointment, $oldStatus, Auth::user());
            }
        }

        return redirect()->route('appointments.index')->with('success', 'Запись обновлена');
    }

    /**
     * Быстрое назначение мастера для записи (без мастера).
     */
    public function assignMaster(Request $request, Appointment $appointment)
    {
        $redirect = $this->checkAppointmentBelongsToBusiness($appointment);
        if ($redirect) {
            return $redirect;
        }

        $business = $this->getCurrentBusiness();
        $role = $this->getCurrentBusinessRole();

        if (! $role || ! app(BusinessRolePermissionService::class)->hasPermission($role->id, 'client.appointments.update')) {
            return redirect()->route('appointments.index')
                ->with('error', 'У вас нет прав на редактирование записей.');
        }

        if ($role && ! $this->canViewAppointment($business, $role->id, 'client.appointments.view', $appointment->id)) {
            return redirect()->route('appointments.index')
                ->with('error', 'У вас нет доступа к этой записи.');
        }

        $validated = $request->validate([
            'master_id' => ['required', 'exists:masters,id'],
        ]);

        $master = Master::where('id', $validated['master_id'])
            ->where('business_id', $business->id)
            ->first();

        if (! $master) {
            return redirect()->back()->withInput()->withErrors(['master_id' => 'Мастер не найден.']);
        }

        if (! $master->services()->where('services.id', $appointment->service_id)->exists()) {
            return redirect()->back()->withInput()->withErrors(['master_id' => 'Выбранный мастер не предоставляет эту услугу.']);
        }

        // Проверяем расписание мастера: рабочий день и время записи в пределах смены
        $appointmentDate = Carbon::parse($appointment->date);
        if ($master->isDayOff($appointmentDate)) {
            return redirect()->back()->withInput()->withErrors(['master_id' => 'В выбранную дату мастер не работает.']);
        }

        $workingTime = $master->getWorkingTimeForDate($appointmentDate);
        if ($workingTime) {
            $startTime = Carbon::parse($appointment->time);
            $endTime = $startTime->copy()->addMinutes($appointment->final_duration);
            $workStart = Carbon::parse($workingTime['from']);
            $workEnd = Carbon::parse($workingTime['to']);

            if ($startTime->lt($workStart) || $endTime->gt($workEnd)) {
                return redirect()->back()->withInput()->withErrors(['master_id' => 'Время записи выходит за рамки рабочего времени мастера.']);
            }
        }

        if (Appointment::hasConflictForMaster(
            (int) $master->id,
            $appointment->date,
            $appointment->time,
            $appointment->final_duration,
            $appointment->id
        )) {
            return redirect()->back()->withInput()->withErrors(['master_id' => 'У мастера в это время уже есть запись.']);
        }

        $appointment->update(['master_id' => $master->id]);

        $fromShow = $request->get('from') === 'show';

        if ($fromShow) {
            return redirect()->route('appointments.show', $appointment)->with('success', 'Мастер назначен.');
        }

        return redirect()->route('appointments.index')->with('success', 'Мастер назначен.');
    }

    /**
     * Подтвердить запись
     */
    public function confirm(Appointment $appointment)
    {
        $redirect = $this->checkAppointmentBelongsToBusiness($appointment);
        if ($redirect) {
            return $redirect;
        }

        $business = $this->getCurrentBusiness();
        $role = $this->getCurrentBusinessRole();

        // Проверяем право на просмотр этой конкретной записи
        if ($role && ! $this->canViewAppointment($business, $role->id, 'client.appointments.view', $appointment->id)) {
            return redirect()->route('appointments.index')
                ->with('error', 'У вас нет доступа к этой записи.');
        }

        $oldStatus = $appointment->status;
        $appointment->update(['status' => 'confirmed']);

        if (AppointmentNotificationService::shouldNotifyClientOnStatusChange($oldStatus, 'confirmed')) {
            TelegramNotificationService::sendAppointmentStatusChangedForClient($appointment, $oldStatus);
        }
        if (AppointmentNotificationService::shouldNotifyStaffOnStatusChange($oldStatus, 'confirmed')) {
            AppointmentNotificationService::notifyStatusChanged($appointment, $oldStatus, Auth::user());
        }

        return redirect()->route('appointments.index')->with('success', 'Запись подтверждена');
    }

    /**
     * Отменить запись
     */
    public function cancel(Appointment $appointment)
    {
        $redirect = $this->checkAppointmentBelongsToBusiness($appointment);
        if ($redirect) {
            return $redirect;
        }

        $business = $this->getCurrentBusiness();
        $role = $this->getCurrentBusinessRole();

        // Проверяем право на просмотр этой конкретной записи
        if ($role && ! $this->canViewAppointment($business, $role->id, 'client.appointments.view', $appointment->id)) {
            return redirect()->route('appointments.index')
                ->with('error', 'У вас нет доступа к этой записи.');
        }

        $oldStatus = $appointment->status;
        $appointment->update(['status' => 'cancelled']);

        if (AppointmentNotificationService::shouldNotifyClientOnStatusChange($oldStatus, 'cancelled')) {
            TelegramNotificationService::sendAppointmentStatusChangedForClient($appointment, $oldStatus);
        }
        if (AppointmentNotificationService::shouldNotifyStaffOnStatusChange($oldStatus, 'cancelled')) {
            AppointmentNotificationService::notifyStatusChanged($appointment, $oldStatus, Auth::user());
        }

        return redirect()->route('appointments.index')->with('success', 'Запись отменена');
    }

    /**
     * Выполнить запись
     */
    public function complete(Appointment $appointment)
    {
        $redirect = $this->checkAppointmentBelongsToBusiness($appointment);
        if ($redirect) {
            return $redirect;
        }

        $business = $this->getCurrentBusiness();
        $role = $this->getCurrentBusinessRole();

        // Проверяем право на просмотр этой конкретной записи
        if ($role && ! $this->canViewAppointment($business, $role->id, 'client.appointments.view', $appointment->id)) {
            return redirect()->route('appointments.index')
                ->with('error', 'У вас нет доступа к этой записи.');
        }

        $oldStatus = $appointment->status;
        $appointment->update(['status' => 'completed']);

        if (AppointmentNotificationService::shouldNotifyClientOnStatusChange($oldStatus, 'completed')) {
            TelegramNotificationService::sendAppointmentStatusChangedForClient($appointment, $oldStatus);
        }
        if (AppointmentNotificationService::shouldNotifyStaffOnStatusChange($oldStatus, 'completed')) {
            AppointmentNotificationService::notifyStatusChanged($appointment, $oldStatus, Auth::user());
        }

        return redirect()->route('appointments.index')->with('success', 'Запись завершена');
    }

    /**
     * Отправить клиенту в Telegram запрос на подтверждение записи (кнопки «Подтвердить» / «Отменить»).
     * Доступно, если запись создана через ТГ или у клиента известен telegram_user_id.
     */
    public function sendTelegramConfirmation(Appointment $appointment)
    {
        $redirect = $this->checkAppointmentBelongsToBusiness($appointment);
        if ($redirect) {
            return $redirect;
        }

        $business = $this->getCurrentBusiness();
        $role = $this->getCurrentBusinessRole();

        if ($role && ! $this->canViewAppointment($business, $role->id, 'client.appointments.view', $appointment->id)) {
            return redirect()->route('appointments.index')
                ->with('error', 'У вас нет доступа к этой записи.');
        }

        if (! $appointment->client || ! $appointment->client->telegram_user_id) {
            return redirect()->back()
                ->with('error', 'У клиента не привязан Telegram или клиент удалён. Отправить подтверждение нельзя.');
        }

        if ($appointment->status !== 'pending') {
            return redirect()->back()
                ->with('error', 'Запрос на подтверждение можно отправить только для записи в статусе «Ожидает подтверждения».');
        }

        $sent = TelegramNotificationService::sendAppointmentConfirmationRequest($appointment);

        if ($sent) {
            return redirect()->back()
                ->with('success', 'Клиенту отправлено сообщение в Telegram.');
        }

        return redirect()->back()
            ->with('error', 'Не удалось отправить сообщение в Telegram. Попробуйте позже.');
    }

    /**
     * Export appointments to CSV.
     */
    public function export(Request $request)
    {
        $business = $this->getCurrentBusiness();

        if (! $business) {
            return redirect()->back()->with('error', 'Сначала создайте бизнес или примите приглашение.');
        }

        // Получаем роль пользователя для проверки прав
        $role = $this->getCurrentBusinessRole();
        $permissionService = app(\App\Services\BusinessRolePermissionService::class);

        $view = $request->get('view', 'table');
        $currentMonth = $request->get('month', Carbon::now()->format('Y-m'));

        $filters = [
            'view' => $view,
            'month' => $currentMonth,
            'date' => $request->get('date'),
            'status' => $request->get('status'),
            'search' => $request->get('search'),
            'service_id' => $request->get('service_id'),
            'master_id' => $request->get('master_id'),
        ];

        // Применяем фильтр "только свои данные" если нужно
        if ($role && $permissionService->hasOwnDataPermission($role->id, 'client.appointments.view')) {
            $masterId = $this->getCurrentUserMasterId($business);
            if ($masterId) {
                $filters['master_id'] = $masterId; // Принудительно фильтруем по мастеру
            }
        }

        $appointments = $this->appointmentRepository->getAllFilteredForBusiness($business->id, $filters);

        $filename = 'appointments_'.now()->format('Y-m-d_H-i-s').'.csv';

        $headers = [
            'Content-type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=$filename",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $statusLabels = [
            'pending' => 'Ожидает',
            'confirmed' => 'Подтверждена',
            'completed' => 'Завершена',
            'cancelled' => 'Отменена',
        ];

        $callback = function () use ($appointments, $statusLabels) {
            $file = fopen('php://output', 'w');

            // Заголовки CSV
            fputcsv($file, ['Дата', 'Время', 'Клиент', 'Телефон', 'Услуга', 'Мастер', 'Статус', 'Цена']);

            // Данные записей
            foreach ($appointments as $appointment) {
                $masterName = $appointment->master && ! $appointment->master->trashed()
                    ? trim(($appointment->master->first_name ?? '').' '.($appointment->master->last_name ?? ''))
                    : 'Не назначен';

                fputcsv($file, [
                    $appointment->date->format('d.m.Y'),
                    \Carbon\Carbon::parse($appointment->time)->format('H:i'),
                    $appointment->client?->full_name ?? 'Клиент удалён',
                    $appointment->client?->phone ?? '—',
                    $appointment->service?->name ?? 'Услуга удалена',
                    $masterName,
                    $statusLabels[$appointment->status] ?? $appointment->status,
                    $appointment->final_price ? number_format($appointment->final_price, 0, ',', ' ').' BYN' : '',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
