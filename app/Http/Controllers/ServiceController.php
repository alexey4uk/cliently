<?php

namespace App\Http\Controllers;

use App\Http\Requests\ServiceRequest;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user()->load('businesses.services');
        $business = $user->businesses->first();

        if (!$business) {
            return redirect()->route('onboarding.business');
        }

        return view('services.index', [
            'business' => $business,
            'services' => $business->services,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = Auth::user()->load('businesses');
        $business = $user->businesses->first();

        if (!$business) {
            return redirect()->route('onboarding.business');
        }

        return view('services.create', [
            'business' => $business,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ServiceRequest $request)
    {
        $user = Auth::user()->load('businesses');
        $business = $user->businesses->first();

        if (!$business) {
            return redirect()->route('onboarding.business');
        }

        $validated = $request->validated();

        Service::create([
            'business_id' => $business->id,
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'duration' => $validated['duration'],
            'price' => $validated['price'],
            'is_active' => $validated['is_active'] ?? true,
        ]);

        return redirect()->route('services.index')->with('success', 'Услуга добавлена');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Service $service)
    {
        $user = Auth::user()->load('businesses');
        $business = $user->businesses->first();

        if (!$business || $service->business_id !== $business->id) {
            return redirect()->route('services.index');
        }

        return view('services.edit', [
            'business' => $business,
            'service' => $service,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ServiceRequest $request, Service $service)
    {
        $user = Auth::user()->load('businesses');
        $business = $user->businesses->first();

        if (!$business || $service->business_id !== $business->id) {
            return redirect()->route('services.index');
        }

        $validated = $request->validated();

        $service->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'duration' => $validated['duration'],
            'price' => $validated['price'],
            'is_active' => $validated['is_active'] ?? $service->is_active,
        ]);

        return redirect()->route('services.index')->with('success', 'Услуга обновлена');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Service $service)
    {
        $user = Auth::user()->load('businesses');
        $business = $user->businesses->first();

        if (!$business || $service->business_id !== $business->id) {
            return redirect()->route('services.index');
        }

        $service->delete();

        return redirect()->route('services.index')->with('success', 'Услуга удалена');
    }
}
