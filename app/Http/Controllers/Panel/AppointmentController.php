<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Appointment;

class AppointmentController extends Controller
{
    /**
     * Display a listing of appointments.
     */
    public function index()
    {
        $appointments = Appointment::with(['client', 'master', 'service', 'location'])
            ->orderBy('start_time', 'desc')
            ->paginate(20);

        return view('panel.appointments.index', compact('appointments'));
    }
}
