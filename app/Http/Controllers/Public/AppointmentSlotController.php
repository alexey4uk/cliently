<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Services\AppointmentSlotService;
use App\Models\Business;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class AppointmentSlotController extends Controller
{
    protected AppointmentSlotService $slotService;

    public function __construct(AppointmentSlotService $slotService)
    {
        $this->slotService = $slotService;
    }

    /**
     * Получить доступные временные слоты для публичной записи
     *
     * @param Request $request
     * @param string $slug Slug бизнеса
     * @return JsonResponse
     */
    public function getAvailableSlots(Request $request, string $slug): JsonResponse
    {
        $business = Business::where('slug', $slug)->firstOrFail();

        $validator = Validator::make($request->all(), [
            'service_id' => ['required', 'integer', 'exists:services,id'],
            'date' => ['required', 'date', 'date_format:Y-m-d'],
            'master_id' => ['nullable', 'integer', 'exists:masters,id'],
            'location_id' => ['nullable', 'integer', 'exists:locations,id'],
        ]);

        // Дополнительная проверка, что услуга и мастер принадлежат бизнесу
        $validator->after(function ($validator) use ($business) {
            if ($validator->errors()->any()) {
                return;
            }

            $serviceId = $validator->getData()['service_id'] ?? null;
            $masterId = $validator->getData()['master_id'] ?? null;

            if ($serviceId && !$business->services()->where('id', $serviceId)->exists()) {
                $validator->errors()->add('service_id', 'Услуга не принадлежит этому бизнесу.');
            }

            if ($masterId && !$business->masters()->where('id', $masterId)->exists()) {
                $validator->errors()->add('master_id', 'Мастер не принадлежит этому бизнесу.');
            }
        });

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            // Нормализуем пустые значения в null
            $masterId = $request->input('master_id');
            $locationId = $request->input('location_id');
            
            $masterId = ($masterId === '' || $masterId === null) ? null : (int) $masterId;
            $locationId = ($locationId === '' || $locationId === null) ? null : (int) $locationId;

            $serviceId = (int) $request->input('service_id');
            $date = $request->input('date');

            // Получаем дополнительную информацию для отладки
            $debugInfo = [
                'service_id' => $serviceId,
                'date' => $date,
                'master_id' => $masterId,
                'location_id' => $locationId,
            ];

            if ($masterId) {
                $master = \App\Models\Master::find($masterId);
                if ($master) {
                    $selectedDate = \Carbon\Carbon::parse($date);
                    $dayOfWeek = $selectedDate->dayOfWeek;
                    
                    // Получаем working_hours через getRawOriginal, чтобы обойти cast
                    $rawWorkingHours = $master->getRawOriginal('working_hours');
                    
                    $workingHoursArray = null;
                    if (!empty($rawWorkingHours)) {
                        if (is_array($rawWorkingHours)) {
                            $workingHoursArray = $rawWorkingHours;
                        } elseif (is_string($rawWorkingHours)) {
                            $workingHoursArray = json_decode($rawWorkingHours, true);
                        }
                    }
                    
                    try {
                        $isDayOff = $master->isDayOff($selectedDate);
                        $workingTime = $master->getWorkingTimeForDate($selectedDate);
                    } catch (\Exception $e) {
                        \Log::error('Ошибка при проверке working_hours мастера', [
                            'master_id' => $masterId,
                            'error' => $e->getMessage(),
                        ]);
                        $isDayOff = true;
                        $workingTime = null;
                    }
                    
                    $debugInfo['master'] = [
                        'name' => $master->first_name . ' ' . $master->last_name,
                        'has_working_hours' => !empty($workingHoursArray),
                        'working_hours_raw_type' => gettype($rawWorkingHours),
                        'working_hours' => $workingHoursArray,
                        'day_of_week' => $dayOfWeek,
                        'is_day_off' => $isDayOff,
                        'working_time_for_date' => $workingTime,
                    ];
                    
                    // Если у мастера нет working_hours, возвращаем понятную ошибку
                    if (empty($workingHoursArray)) {
                        return response()->json([
                            'success' => false,
                            'message' => 'У мастера не настроено рабочее время. Обратитесь к администратору.',
                            'error_type' => 'no_working_hours',
                            'debug' => $debugInfo,
                        ], 400);
                    }
                }
            }

            $debugInfoFromService = [];
            $slots = $this->slotService->getAvailableSlots(
                $serviceId,
                $date,
                $masterId,
                $locationId,
                $debugInfoFromService
            );

            $debugInfo = array_merge($debugInfo, $debugInfoFromService);
            $debugInfo['slots_count'] = count($slots);
            $debugInfo['slots'] = $slots;

            // Получаем информацию об услуге для передачи времени подготовки
            $service = \App\Models\Service::find($serviceId);
            $preparationTime = $service ? ($service->preparation_time ?? null) : null;

            return response()->json([
                'success' => true,
                'slots' => $slots,
                'preparation_time' => $preparationTime, // Время подготовки для показа уведомления
                'debug' => $debugInfo,
            ]);
        } catch (\Exception $e) {
            \Log::error('Ошибка при получении слотов', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->all(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Ошибка при получении доступных слотов: ' . $e->getMessage(),
            ], 500);
        }
    }
}

