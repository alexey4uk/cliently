<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\Location;
use App\Models\Master;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Throwable;

class OnboardingController extends Controller
{
    public function business()
    {
        if (Auth::user()->businesses->isNotEmpty()) {
            return redirect()->route('onboarding.location');
        }

        return view('onboarding.step1-business');
    }

    /**
     * @throws Throwable
     */
    public function storeBusiness(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:businesses,slug|regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
            'description' => 'nullable|string',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
        ]);

        DB::transaction(function () use ($validated, $request) {
            $business = Business::create([
                'name' => $validated['name'],
                'slug' => $validated['slug'],
                'description' => $validated['description'],
                'phone' => $validated['phone'],
                'email' => $validated['email'],
            ]);

            $business->users()->attach($request->user(), ['role' => 'owner']);

            session(['onboarding.business_id' => $business->id]);
        });

        return redirect()->route('onboarding.location');
    }

    public function location(Request $request)
    {
        $user = auth()->user()->load(['businesses.locations']);
        $business = $user->businesses->first();

        if (! $business) {
            return redirect()->route('onboarding.business');
        }

        if ($business->locations->isNotEmpty()) {
            return redirect()->route('onboarding.service');
        }

        return view('onboarding.step2-location');

    }

    public function storeLocation(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:500',
            'description' => 'nullable|string',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'working_hours' => 'required|array',
            'working_hours.*.from' => 'nullable|date_format:H:i',
            'working_hours.*.to' => 'nullable|date_format:H:i',
            'working_hours.*.day_off' => 'nullable|boolean',
        ]);

        $businessId = session('onboarding.business_id') ?? $request->user()->businesses()->first()->id;

        $location = Location::create([
            'business_id' => $businessId,
            'name' => $validated['name'],
            'address' => $validated['address'],
            'description' => $validated['description'] ?? null,
            'phone' => $validated['phone'],
            'email' => $validated['email'] ?? null,
            'working_hours' => json_encode($validated['working_hours']),
        ]);

        session(['onboarding.location_id' => $location->id]);
        session(['onboarding.location_name' => $location->name]);

        return redirect()->route('onboarding.service');
    }

    public function service()
    {
        $user = auth()->user()->load(['businesses.locations', 'businesses.services']);

        $business = $user->businesses->first();

        if (! $business) {
            return redirect()->route('onboarding.business');
        }

        if ($business->locations->isEmpty()) {
            return redirect()->route('onboarding.location');
        }

        if ($business->services->isNotEmpty()) {
            return redirect()->route('onboarding.master');
        }

        return view('onboarding.step3-service');
    }

    public function storeService(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'duration' => 'required|integer|min:15|max:480',
            'price' => 'required|numeric|min:0|max:999999',
        ]);

        $businessId = $request->user()->businesses()->first()->id;

        $service = Service::create([
            'business_id' => $businessId,
            'name' => $validated['name'],
            'description' => $validated['description'],
            'duration' => $validated['duration'],
            'price' => $validated['price'],
            'is_active' => true,
        ]);

        session(['onboarding.service_id' => $service->id]);
        session(['onboarding.service_name' => $service->name]);

        return redirect()->route('onboarding.master');
    }

    public function master()
    {
        $user = auth()->user()->load([
            'businesses.locations',
            'businesses.services',
            'businesses.masters',
        ]);

        $business = $user->businesses->first();

        if (! $business) {
            return redirect()->route('onboarding.business');
        }

        if ($business->locations->isEmpty()) {
            return redirect()->route('onboarding.location');
        }

        if ($business->services->isEmpty()) {
            return redirect()->route('onboarding.service');
        }

        if ($business->masters->isNotEmpty()) {
            return redirect()->route('onboarding.complete');
        }

        return view('onboarding.step4-master');
    }

    public function storeMaster(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'specialization' => 'required|string|max:255',
            'description' => 'nullable|string',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
        ]);

        $business = $request->user()->businesses()->with(['locations', 'services'])->first();

        if (! $business) {
            return redirect()->route('onboarding.business');
        }

        $master = Master::create([
            'business_id' => $business->id,
            'name' => $validated['name'],
            'description' => $validated['description'],
            'specialization' => $validated['specialization'],
            'phone' => $validated['phone'],
            'email' => $validated['email'],
        ]);

        $locationId = $business->locations->first()?->id;
        $serviceId = $business->services->first()?->id;

        if ($locationId) {
            $master->locations()->attach($locationId);
        }

        if ($serviceId) {
            $master->services()->attach($serviceId);
        }

        return redirect()->route('onboarding.complete');
    }

    public function complete()
    {
        session()->forget([
            'onboarding.business_id',
            'onboarding.location_id',
            'onboarding.location_name',
            'onboarding.service_id',
            'onboarding.service_name',
            'onboarding.master_id',
            'onboarding.master_name',
        ]);

        return view('onboarding.complete');
    }
}
