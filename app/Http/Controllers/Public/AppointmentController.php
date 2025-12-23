<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Requests\PublicAppointmentRequest;
use App\Models\Appointment;
use App\Models\Business;
use App\Models\Client;
use App\Services\AppointmentSlotService;
use Illuminate\Http\Request;
use Carbon\Carbon;

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

        // Если локация только одна, сразу переходим к выбору услуг
        if ($locations->count() === 1) {
            return redirect()->route('public.appointments.select-location', [
                'slug' => $business->slug,
                'locationId' => $locations->first()->id
            ]);
        }

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
            ->whereHas('services', function($q) use ($serviceId) {
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
                ->whereHas('services', function($q) use ($serviceId) {
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
        if (!$master->locations()->where('locations.id', $locationId)->exists()) {
            abort(404, 'Мастер не работает в выбранной локации');
        }

        // Проверяем, что мастер предоставляет услугу
        if (!$master->services()->where('services.id', $serviceId)->exists()) {
            abort(404, 'Мастер не предоставляет эту услугу');
        }

        $date = request()->get('date', Carbon::today()->format('Y-m-d'));
        $selectedDate = Carbon::parse($date);

        // Получаем доступные слоты
        $debugInfo = [];
        $availableSlots = $this->slotService->getAvailableSlots(
            $serviceId,
            $date,
            $masterId,
            $locationId,
            $debugInfo
        );

        return view('appointments.public.select-time', compact(
            'business',
            'location',
            'service',
            'master',
            'selectedDate',
            'availableSlots',
            'date'
        ))->with('currentStep', 4);
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
        Appointment::create([
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

        return redirect()
            ->route('public.appointments.success', $business->slug)
            ->with('success', 'Ваша запись успешно создана! Мы свяжемся с вами для подтверждения.');
    }

    /**
     * Страница успешной записи
     */
    public function success(string $slug)
    {
        $business = Business::where('slug', $slug)->firstOrFail();
        return view('appointments.public.success', compact('business'));
    }
}
