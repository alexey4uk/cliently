<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user()->load('businesses');
        $business = $user->businesses->first();

        if (!$business) {
            return redirect()->route('onboarding.business');
        }

        $today = Carbon::today();
        $currentTime = Carbon::now()->format('H:i');

        // Записи на сегодня (только подтвержденные и выполненные)
        $todayAppointments = $business->appointments()
            ->where('date', $today->format('Y-m-d'))
            ->whereIn('status', ['confirmed', 'completed'])
            ->with(['client', 'service', 'master', 'location'])
            ->orderBy('time', 'asc')
            ->get();

        // Записи, требующие внимания (pending)
        $pendingAppointments = $business->appointments()
            ->where('status', 'pending')
            ->where('date', '>=', $today->format('Y-m-d'))
            ->with(['client', 'service', 'master', 'location'])
            ->orderBy('date', 'asc')
            ->orderBy('time', 'asc')
            ->limit(5)
            ->get();

        // Следующая запись (только подтвержденные, не выполненные)
        $nextAppointment = $todayAppointments
            ->filter(function ($appointment) use ($currentTime) {
                return $appointment->time >= $currentTime && $appointment->status === 'confirmed';
            })
            ->first();

        // Разделяем записи на выполненные и предстоящие
        $completedAppointments = $todayAppointments->where('status', 'completed');
        $upcomingAppointments = $todayAppointments->where('status', 'confirmed');

        // Исключаем следующую запись из основного списка, чтобы избежать дублирования
        $upcomingAppointmentsWithoutNext = $upcomingAppointments->filter(function ($appointment) use ($nextAppointment) {
            return !$nextAppointment || $appointment->id !== $nextAppointment->id;
        });

        // Прогресс дня
        $totalToday = $todayAppointments->count();
        $completedCount = $completedAppointments->count();
        $progressPercentage = $totalToday > 0 ? round(($completedCount / $totalToday) * 100) : 0;

        return view('dashboard', [
            'business' => $business,
            'todayAppointments' => $todayAppointments,
            'completedAppointments' => $completedAppointments,
            'upcomingAppointments' => $upcomingAppointmentsWithoutNext,
            'pendingAppointments' => $pendingAppointments,
            'nextAppointment' => $nextAppointment,
            'todayDate' => $today->locale('ru')->isoFormat('D MMMM'),
            'currentTime' => $currentTime,
            'totalToday' => $totalToday,
            'completedCount' => $completedCount,
            'progressPercentage' => $progressPercentage,
        ]);
    }
}
