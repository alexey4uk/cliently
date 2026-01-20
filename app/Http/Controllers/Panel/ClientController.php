<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Client;

class ClientController extends Controller
{
    /**
     * Display a listing of clients.
     */
    public function index()
    {
        $clients = Client::with('business')->paginate(20);

        return view('panel.clients.index', compact('clients'));
    }
}
