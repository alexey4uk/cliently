<?php

namespace App\Http\Controllers;

use App\Http\Requests\AppointmentRequest;
use App\Models\Appointment;
use App\Models\Client;
use App\Models\Service;
use App\Services\TelegramNotificationService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AppointmentsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user()->load('businesses');
        $business = $user->businesses->first();

        if (! $business) {
            return redirect()->route('onboarding.business');
        }

        $view = $request->get('view', 'table'); // table или calendar
        $currentMonth = $request->get('month', Carbon::now()->format('Y-m'));

        try {
            $selectedDate = Carbon::parse($currentMonth.'-01');
        } catch (\Exception $e) {
            $selectedDate = Carbon::now()->startOfMonth();
        }

        $query = $business->appointments()
            ->with(['client', 'service', 'master', 'location']);

        // Для календаря загружаем все записи месяца
        if ($view === 'calendar') {
            $startOfMonth = $selectedDate->copy()->startOfMonth();
            $endOfMonth = $selectedDate->copy()->endOfMonth();
            $query->whereBetween('date', [$startOfMonth, $endOfMonth])
                ->orderBy('date', 'asc')
                ->orderBy('time', 'asc');

            $allAppointments = $query->get();

            // Группируем по датам
            $appointmentsByDate = $allAppointments->groupBy(function ($appointment) {
                return $appointment->date->format('Y-m-d');
            });

            // Для календаря используем коллекцию без пагинации
            $appointments = $allAppointments;

            // Для календаря пагинация не нужна
            $appointments = $appointments;
        } else {
            // Для таблицы используем фильтры
            $query->orderBy('date', 'desc')
                ->orderBy('time', 'desc');

            // Фильтр по дате
            if ($request->has('date') && $request->date) {
                $query->whereDate('date', $request->date);
            } else {
                // По умолчанию показываем сегодня и будущие записи
                $query->whereDate('date', '>=', Carbon::today());
            }

            // Фильтр по статусу
            if ($request->has('status') && $request->status) {
                $query->where('status', $request->status);
            }

            // Поиск
            if ($request->has('search') && $request->search) {
                $search = $request->search;
                $query->whereHas('client', function ($q) use ($search) {
                    $q->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                })->orWhereHas('service', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                });
            }

            $appointments = $query->paginate(20)->withQueryString();
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
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = Auth::user()->load('businesses');
        $business = $user->businesses->first();

        if (! $business) {
            return redirect()->route('onboarding.business');
        }

        return view('appointments.create', [
            'business' => $business,
            'clients' => $business->clients()->orderBy('first_name')->get(),
            'services' => $business->services()->where('is_active', true)->orderBy('name')->get(),
            'masters' => $business->masters()->where('is_active', true)->orderBy('first_name')->get(),
            'locations' => $business->locations()->orderBy('name')->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AppointmentRequest $request)
    {
        $user = Auth::user()->load('businesses');
        $business = $user->businesses->first();

        if (! $business) {
            return redirect()->route('onboarding.business');
        }

        $validated = $request->validated();

        // Проверка, что клиент, услуга принадлежат бизнесу
        $client = Client::where('id', $validated['client_id'])
            ->where('business_id', $business->id)
            ->firstOrFail();

        $service = Service::where('id', $validated['service_id'])
            ->where('business_id', $business->id)
            ->firstOrFail();

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

        // Отправить уведомление в Telegram
        TelegramNotificationService::sendAppointmentCreated($appointment);

        return redirect()->route('appointments.index')->with('success', 'Запись создана');
    }

    /**
     * Display the specified resource.
     */
    public function show(Appointment $appointment)
    {
        //dd($appointment->client);
        $user = Auth::user()->load('businesses');
        $business = $user->businesses->first();

        if (! $business || $appointment->business_id !== $business->id) {
            return redirect()->route('appointments.index');
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
        $user = Auth::user()->load('businesses');
        $business = $user->businesses->first();

        if (! $business || $appointment->business_id !== $business->id) {
            return redirect()->route('appointments.index');
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
        $user = Auth::user()->load('businesses');
        $business = $user->businesses->first();

        if (! $business || $appointment->business_id !== $business->id) {
            return redirect()->route('appointments.index');
        }

        $validated = $request->validated();
        $oldStatus = $appointment->status;

        // Проверка, что клиент, услуга принадлежат бизнесу
        $client = Client::where('id', $validated['client_id'])
            ->where('business_id', $business->id)
            ->firstOrFail();

        $service = Service::where('id', $validated['service_id'])
            ->where('business_id', $business->id)
            ->firstOrFail();

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
        }

        return redirect()->route('appointments.index')->with('success', 'Запись обновлена');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Appointment $appointment)
    {
        $user = Auth::user()->load('businesses');
        $business = $user->businesses->first();

        if (! $business || $appointment->business_id !== $business->id) {
            return redirect()->route('appointments.index');
        }

        $appointment->delete();

        return redirect()->route('appointments.index')->with('success', 'Запись удалена');
    }

    /**
     * Подтвердить запись
     */
    public function confirm(Appointment $appointment)
    {
        $user = Auth::user()->load('businesses');
        $business = $user->businesses->first();

        if (! $business || $appointment->business_id !== $business->id) {
            return redirect()->route('appointments.index');
        }

        $oldStatus = $appointment->status;
        $appointment->update(['status' => 'confirmed']);

        // Отправить уведомление в Telegram
        TelegramNotificationService::sendAppointmentStatusChangedForClient($appointment, $oldStatus);

        return redirect()->route('appointments.index')->with('success', 'Запись подтверждена');
    }

    /**
     * Отменить запись
     */
    public function cancel(Appointment $appointment)
    {
        $user = Auth::user()->load('businesses');
        $business = $user->businesses->first();

        if (! $business || $appointment->business_id !== $business->id) {
            return redirect()->route('appointments.index');
        }

        $oldStatus = $appointment->status;
        $appointment->update(['status' => 'cancelled']);

        // Отправить уведомление в Telegram
        TelegramNotificationService::sendAppointmentStatusChangedForClient($appointment, $oldStatus);
        TelegramNotificationService::sendAppointmentStatusChanged($appointment, $oldStatus);

        return redirect()->route('appointments.index')->with('success', 'Запись отменена');
    }

    /**
     * Выполнить запись
     */
    public function complete(Appointment $appointment)
    {
        $user = Auth::user()->load('businesses');
        $business = $user->businesses->first();

        if (! $business || $appointment->business_id !== $business->id) {
            return redirect()->route('appointments.index');
        }

        $appointment->update(['status' => 'completed']);

        return redirect()->route('appointments.index')->with('success', 'Запись завершена');
    }
}
