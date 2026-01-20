<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Business;
use Illuminate\Http\Request;

class BusinessController extends Controller
{
    /**
     * Display a listing of businesses.
     */
    public function index()
    {
        $businesses = Business::with('users')->paginate(20);
        
        return view('panel.businesses.index', compact('businesses'));
    }
}
