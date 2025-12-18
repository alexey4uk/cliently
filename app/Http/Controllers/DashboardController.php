<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $clientCount = $this->clientCount();

        return view('dashboard', compact('clientCount'));
    }

    public function clientCount(): int
    {
        $clientCount = 0;
        $businesses = Auth::user()->businesses()->with('clients')->get();

        foreach ($businesses as $business) {
            $clientCount += $business->clients->count();
        }

        return $clientCount;
    }
}
