<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    //
    public function index()
    {
        return view('dashboard', ['clientCount' => $this->clientCount()]);
    }

    public function clientCount()
    {
        $clientCount = 0;

        foreach (auth()->user()->businesses()->get() as $business) {
            $clientCount = $clientCount + $business->clients->count();
        }

        return $clientCount;
    }
}
