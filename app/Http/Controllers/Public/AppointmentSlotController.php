
<?php namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Repositories\BusinessRepositoryInterface;
use App\Services\AppointmentSlotService;
use App\Services\MasterScheduleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AppointmentSlotController extends Controller
{
    protected AppointmentSlotService $slotService;

    protected BusinessRepositoryInterface $businessRepository;

    public function __construct(
        AppointmentSlotService $slotService,
        BusinessRepositoryInterface $businessRepository,
    ) {
        $this->slotService = $slotService;
        $this->businessRepository = $businessRepository;
    }

    /**
     * Получить доступные временные слоты для публичной записи
     *
     * @param  string  $slug  Slug бизнеса
     */
    public function getAvailableSlots(
        Request $request,
        string $slug,
    ): JsonResponse {
        $business = $this->businessRepository->findBySlug($slug);
        if (!$business) {
            abort(404);
        }

        $validator = Validator::make($request->all(), [
            "service_id" => ["required", "integer", "exists:services,id"],
            "date" => ["required", "date", "date_format:Y-m-d"],
            "master_id" => ["nullable", "integer", "exists:masters,id"],
            "location_id" => ["nullable", "integer", "exists:locations,id"],
            "appointment_id" => [
                "nullable",
                "integer",
                "exists:appointments,id",
            ], // Для исключения текущей записи при редактировании
        ]);

        // Дополнительная проверка, что услуга и мастер принадлежат бизнесу
        $validator->after(function ($validator) use ($business) {
            if ($validator->errors()->any()) {
                return;
            }

            $serviceId = $validator->getData()["service_id"] ?? null;
            $masterId = $validator->getData()["master_id"] ?? null;

            if (
                $serviceId &&
                !$business->services()->where("id", $serviceId)->exists()
            ) {
                $validator
                    ->errors()
                    ->add("service_id", "Услуга не принадлежит этому бизнесу.");
            }

            if (
                $masterId &&
                !$business->masters()->where("id", $masterId)->exists()
            ) {
                $validator
                    ->errors()
                    ->add("master_id", "Мастер не принадлежит этому бизнесу.");
            }
        });

        if ($validator->fails()) {
            return response()->json(
                [
                    "success" => false,
                    "errors" => $validator->errors(),
                ],
                422,
            );
        }

        try {
            // Нормализуем пустые значения в null
            $masterId = $request->input("master_id");
            $locationId = $request->input("location_id");

            $masterId =
                $masterId === "" || $masterId === null ? null : (int) $masterId;
            $locationId =
                $locationId === "" || $locationId === null
                    ? null
                    : (int) $locationId;

            $serviceId = (int) $request->input("service_id");
            $date = $request->input("date");
            $appointmentId = $request->input("appointment_id");
            $appointmentId =
                $appointmentId === "" || $appointmentId === null
                    ? null
                    : (int) $appointmentId;

            // Получаем дополнительную информацию для отладки
            $debugInfo = [
                "service_id" => $serviceId,
                "date" => $date,
                "master_id" => $masterId,
                "location_id" => $locationId,
                "appointment_id" => $appointmentId,
            ];

            if ($masterId) {
                $master = \App\Models\Master::find($masterId);
                if ($master) {
                    $selectedDate = \Carbon\Carbon::parse($date);
                    $dayOfWeek = $selectedDate->dayOfWeek;

                    // Используем новый сервис расписания
                    $scheduleService = app(MasterScheduleService::class);

                    try {
                        $workingTime = $scheduleService->getWorkingTimeForDate(
                            $master,
                            $selectedDate,
                        );
                        $isDayOff = $workingTime === null;
                    } catch (\Exception $e) {
                        \Log::error("Ошибка при проверке расписания мастера", [
                            "master_id" => $masterId,
                            "error" => $e->getMessage(),
                        ]);
                        $isDayOff = true;
                        $workingTime = null;
                    }

                    $debugInfo["master"] = [
                        "name" =>
                            $master->first_name . " " . $master->last_name,
                        "day_of_week" => $dayOfWeek,
                        "is_day_off" => $isDayOff,
                        "working_time_for_date" => $workingTime,
                    ];

                    // Если мастер не работает в этот день, возвращаем понятную ошибку
                    if ($isDayOff) {
                        return response()->json(
                            [
                                "success" => false,
                                "message" =>
                                    "Мастер не работает в выбранный день.",
                                "error_type" => "master_day_off",
                                "debug" => $debugInfo,
                            ],
                            400,
                        );
                    }
                }
            }

            $debugInfoFromService = [];
            $slots = $this->slotService->getAvailableSlots(
                $serviceId,
                $date,
                $masterId,
                $locationId,
                $debugInfoFromService,
                $appointmentId,
            );

            $debugInfo = array_merge($debugInfo, $debugInfoFromService);
            $debugInfo["slots_count"] = count($slots);
            $debugInfo["slots"] = $slots;

            // Получаем информацию об услуге для передачи времени подготовки
            $service = \App\Models\Service::find($serviceId);
            $preparationTime = $service
                ? $service->preparation_time ?? null
                : null;

            return response()->json([
                "success" => true,
                "slots" => $slots,
                "preparation_time" => $preparationTime, // Время подготовки для показа уведомления
                "debug" => $debugInfo,
            ]);
        } catch (\Exception $e) {
            \Log::error("Ошибка при получении слотов", [
                "error" => $e->getMessage(),
                "trace" => $e->getTraceAsString(),
                "request" => $request->all(),
            ]);

            return response()->json(
                [
                    "success" => false,
                    "message" =>
                        "Ошибка при получении доступных слотов: " .
                        $e->getMessage(),
                ],
                500,
            );
        }
    }
}
