<?php

namespace App\Http\Controllers;

use App\Http\Requests\AppointmentRequest;
use App\Models\Appointment;
use App\Repositories\AppointmentRepositoryInterface;
use App\Repositories\ClientRepositoryInterface;
use App\Repositories\MasterRepositoryInterface;
use App\Repositories\ServiceRepositoryInterface;
use App\Services\AppointmentNotificationService;
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
            return redirect()->route('welcome')
                ->with('info', 'Сначала создайте бизнес или примите приглашение.');
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
            return redirect()->route('welcome')
                ->with('info', 'Сначала создайте бизнес или примите приглашение.');
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
            $allAppointments = $this->appointmentRepository->getForCalendar($business->id, $currentMonth);

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
        ]);
    }

    /**
     * Display the calendar view of appointments.
     */
    public function calendar(Request $request)
    {
        $business = $this->getCurrentBusiness();

        if (! $business) {
            return redirect()->route('welcome')
                ->with('info', 'Сначала создайте бизнес или примите приглашение.');
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
        $allAppointments = $this->appointmentRepository->getForCalendar($business->id, $currentMonth);

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
            return redirect()->route('welcome')
                ->with('info', 'Сначала создайте бизнес или примите приглашение.');
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
            return redirect()->route('welcome')
                ->with('info', 'Сначала создайте бизнес или примите приглашение.');
        }

        $user = Auth::user();

        // Проверка лимита записей в месяц
        $subscriptionService = app(SubscriptionService::class);
        if (! $subscriptionService->canCreateAppointment($user)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Достигнут месячный лимит записей для вашего тарифа. Обновите тариф для увеличения лимита.');
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
            'notes' => $validated['notes'] ?? null,
            'duration' => $validated['duration'] ?? null,
            'price' => $validated['price'] ?? null,
        ]);

        // Увеличиваем usage для месячной метрики
        $subscriptionService->incrementUsage($user, 'max_appointments_per_month');

        // Отправить системное уведомление (включая Telegram для каждого пользователя)
        AppointmentNotificationService::notifyCreated($appointment);

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

        return view('appointments.show', [
            'business' => $business,
            'appointment' => $appointment,
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

        // Отправить уведомление в Telegram, если статус изменился
        if ($appointment->status !== $oldStatus) {
            TelegramNotificationService::sendAppointmentStatusChangedForClient($appointment, $oldStatus);
            // Отправить системное уведомление
            AppointmentNotificationService::notifyStatusChanged($appointment, $oldStatus);
        }

        return redirect()->route('appointments.index')->with('success', 'Запись обновлена');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Appointment $appointment)
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

        $user = Auth::user();

        // Уменьшаем usage для месячной метрики только если запись была создана в текущем месяце
        if ($appointment->created_at->isCurrentMonth()) {
            $subscriptionService = app(SubscriptionService::class);
            $subscriptionService->decrementUsage($user, 'max_appointments_per_month');
        }

        $appointment->delete();

        return redirect()->route('appointments.index')->with('success', 'Запись удалена');
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

        // Отправить уведомление в Telegram
        TelegramNotificationService::sendAppointmentStatusChangedForClient($appointment, $oldStatus);

        // Отправить системное уведомление
        AppointmentNotificationService::notifyStatusChanged($appointment, $oldStatus);

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

        // Отправить уведомление в Telegram
        TelegramNotificationService::sendAppointmentStatusChangedForClient($appointment, $oldStatus);
        TelegramNotificationService::sendAppointmentStatusChanged($appointment, $oldStatus);

        // Отправить системное уведомление
        AppointmentNotificationService::notifyStatusChanged($appointment, $oldStatus);

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

        // Отправить уведомление в Telegram
        TelegramNotificationService::sendAppointmentStatusChanged($appointment);

        // Отправить системное уведомление
        AppointmentNotificationService::notifyStatusChanged($appointment, $oldStatus);

        return redirect()->route('appointments.index')->with('success', 'Запись завершена');
    }

    /**
     * Export appointments to CSV.
     */
    public function export(Request $request)
    {
        $business = $this->getCurrentBusiness();

        if (! $business) {
            return redirect()->route('welcome')
                ->with('info', 'Сначала создайте бизнес или примите приглашение.');
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
                fputcsv($file, [
                    $appointment->date->format('d.m.Y'),
                    \Carbon\Carbon::parse($appointment->time)->format('H:i'),
                    $appointment->client->full_name,
                    $appointment->client->phone,
                    $appointment->service->name,
                    $appointment->master ? $appointment->master->first_name.' '.$appointment->master->last_name : 'Не назначен',
                    $statusLabels[$appointment->status] ?? $appointment->status,
                    $appointment->final_price ? number_format($appointment->final_price, 0, ',', ' ').' Br' : '',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
