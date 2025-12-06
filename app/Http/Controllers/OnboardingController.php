<?php

namespace App\Http\Controllers;

use App\Models\Business;
use Illuminate\Http\Request;

class OnboardingController extends Controller
{
    public function index()
    {
        return view('onboarding.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255',
            'short_description' => 'required|string|max:100',
            'full_description' => 'sometimes|string|max:1000',
        ]);

        Business::query()->create([
            'name' => $request->name,
            'slug' => $request->slug,
            'user_id' => auth()->id(),
            'short_description' => $request->short_description,
            'full_description' => $request->full_description,
        ]);

        return redirect()->route('dashboard')->with('success', 'Данные успешно сохранены');
    }
}
