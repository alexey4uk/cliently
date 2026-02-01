<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\Master;
use App\Services\MasterScheduleService;
use Illuminate\Http\Request;

class MasterScheduleController extends Controller
{
    private MasterScheduleService $scheduleService;

    public function __construct(MasterScheduleService $scheduleService)
    {
        $this->scheduleService = $scheduleService;
    }

    /**
     * Показать страницу расписания мастера
     */
    public function edit(Master $master)
    {
        $user = auth()->user()->load(['businesses']);
        $business = $user->businesses->first();

        if (!$business) {
            return redirect()->route('settings.masters');
        }

        // Проверяем принадлежность мастера бизнесу
        if ($master->business_id !== $business->id) {
            return redirect()->route('settings.masters');
        }

        // Загружаем расписание
        $scheduleData = $this->scheduleService->getScheduleForMaster($master);

        return view('settings.masters.schedule', [
            'master' => $master,
            'schedules' => $scheduleData['schedules'],
            'overrides' => $scheduleData['overrides'],
        ]);
    }

    /**
     * Обновить расписание мастера
     */
    public function update(Request $request, Master $master)
    {
        $user = auth()->user()->load(['businesses']);
        $business = $user->businesses->first();

        if (!$business) {
            return redirect()->route('settings.masters');
        }

        if ($master->business_id !== $business->id) {
            return redirect()->route('settings.masters');
        }

        $validated = $request->validate([
            'schedules' => 'nullable|array',
            'schedules.*.day_of_week' => 'required|integer|min:0|max:6',
            'schedules.*.is_working' => 'nullable|boolean',
            'schedules.*.start_time' => 'nullable|date_format:H:i',
            'schedules.*.end_time' => 'nullable|date_format:H:i',
            'schedules.*.breaks' => 'nullable|array',
            'schedules.*.breaks.*.start_time' => 'nullable|date_format:H:i',
            'schedules.*.breaks.*.end_time' => 'nullable|date_format:H:i',
            'schedules.*.breaks.*.description' => 'nullable|string|max:255',
            'overrides' => 'nullable|array',
            'overrides.*.date' => 'required|date',
            'overrides.*.is_working' => 'nullable|boolean',
            'overrides.*.start_time' => 'nullable|date_format:H:i',
            'overrides.*.end_time' => 'nullable|date_format:H:i',
        ]);

        $this->scheduleService->saveScheduleForMaster($validated, $master);

        return redirect()
            ->route('settings.masters.schedule.edit', $master)
            ->with('success', 'Расписание успешно обновлено');
    }
}
