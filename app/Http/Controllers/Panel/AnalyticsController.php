<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Client;
use App\Models\User;

class AnalyticsController extends Controller
{
    /**
     * Display the analytics page.
     */
    public function index()
    {
        $totalAppointments = Appointment::count();
        $totalClients = Client::count();
        $totalUsers = User::count();

        $recentAppointments = Appointment::with(['client', 'master', 'service'])
            ->orderBy('start_time', 'desc')
            ->limit(10)
            ->get();

        return view('panel.analytics.index', compact(
            'totalAppointments',
            'totalClients',
            'totalUsers',
            'recentAppointments'
        ));
    }
}
