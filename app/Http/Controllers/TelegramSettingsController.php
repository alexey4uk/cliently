<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TelegramSettingsController extends Controller
{
    public function index()
    {
        return view('settings.telegram.index');
    }
}
