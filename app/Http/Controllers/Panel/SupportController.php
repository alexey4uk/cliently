<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;

class SupportController extends Controller
{
    /**
     * Display the support page.
     */
    public function index()
    {
        return view('panel.support.index');
    }
}
