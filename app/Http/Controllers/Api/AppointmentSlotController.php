<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AppointmentSlotService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AppointmentSlotController extends Controller
{
    protected AppointmentSlotService $slotService;

    public function __construct(AppointmentSlotService $slotService)
    {
        $this->slotService = $slotService;
    }

    /**
     * Получить доступные временные слоты
     */
    public function getAvailableSlots(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'service_id' => ['required', 'integer', 'exists:services,id'],
            'date' => ['required', 'date', 'date_format:Y-m-d'],
            'master_id' => ['nullable', 'integer', 'exists:masters,id'],
            'location_id' => ['nullable', 'integer', 'exists:locations,id'],
        ]);

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

            $slots = $this->slotService->getAvailableSlots(
                (int) $request->input('service_id'),
                $request->input('date'),
                $masterId,
                $locationId
            );

            return response()->json([
                'success' => true,
                'slots' => $slots,
            ]);
        } catch (\Exception $e) {
            \Log::error('Ошибка при получении слотов в админ-панели', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->all(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Ошибка при получении доступных слотов: '.$e->getMessage(),
            ], 500);
        }
    }
}
