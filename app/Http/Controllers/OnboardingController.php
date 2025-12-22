<?php

namespace App\Http\Controllers;

use App\Http\Requests\BusinessRequest;
use App\Http\Requests\LocationRequest;
use App\Http\Requests\MasterRequest;
use App\Models\Business;
use App\Models\Location;
use App\Models\Master;
use App\Models\Service;
use App\Services\WorkingHoursService;
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
    public function storeBusiness(BusinessRequest $request)
    {
        $businessData = $request->validated();
        
        // Дополнительная валидация для онбординга (first_name, last_name)
        $ownerData = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
        ], [
            'first_name.required' => 'Поле "Имя" обязательно для заполнения.',
            'first_name.max' => 'Поле "Имя" не может быть длиннее 255 символов.',
            'last_name.required' => 'Поле "Фамилия" обязательно для заполнения.',
            'last_name.max' => 'Поле "Фамилия" не может быть длиннее 255 символов.',
        ]);

        DB::transaction(function () use ($businessData, $ownerData, $request) {
            $business = Business::create($businessData);

            $business->users()->attach($request->user(), [
                'role' => 'owner',
                'first_name' => $ownerData['first_name'],
                'last_name' => $ownerData['last_name'],
            ]);

            session(['onboarding.business_id' => $business->id]);
        });

        return redirect()->route('onboarding.location')->with('info', 'Бизнес добавлен');
    }

    public function location(Request $request)
    {
        $user = $request->user()->load(['businesses.locations']);
        $business = $user->businesses->first();

        if (! $business) {
            return redirect()->route('onboarding.business');
        }

        if ($business->locations->isNotEmpty()) {
            return redirect()->route('onboarding.service');
        }

        return view('onboarding.step2-location');

    }

    public function storeLocation(LocationRequest $request)
    {
        $validated = $request->validated();
        $businessId = session('onboarding.business_id') ?? $request->user()->businesses()->first()->id;

        $location = Location::create([
            'business_id' => $businessId,
            'name' => $validated['name'],
            'city' => $validated['city'],
            'street' => $validated['street'],
            'house' => $validated['house'],
            'building' => $validated['building'] ?? null,
            'apartment' => $validated['apartment'] ?? null,
            'description' => $validated['description'] ?? null,
            'phone' => $validated['phone'],
            'working_hours' => WorkingHoursService::toJson($validated['working_hours']),
        ]);

        return redirect()->route('onboarding.service')->with('info', 'Локация добавлена');
    }

    public function service()
    {
        $user = Auth::user()->load(['businesses.locations', 'businesses.services']);

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
        ], [
            'name.required' => 'Поле "Название услуги" обязательно для заполнения.',
            'duration.required' => 'Поле "Длительность" обязательно для заполнения.',
            'duration.min' => 'Минимальная длительность услуги — 15 минут.',
            'duration.max' => 'Максимальная длительность услуги — 480 минут (8 часов).',
            'price.required' => 'Поле "Цена" обязательно для заполнения.',
            'price.min' => 'Цена не может быть отрицательной.',
            'price.max' => 'Цена не может превышать 999 999.',
        ]);

        $businessId = $request->user()->businesses()->first()->id;

        $service = Service::create([
            'business_id' => $businessId,
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'duration' => $validated['duration'],
            'price' => $validated['price'],
            'is_active' => true,
        ]);

        return redirect()->route('onboarding.master')->with('info', 'Услуга добавлена');
    }

    public function master()
    {
        $user = Auth::user()->load([
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

    public function storeMaster(MasterRequest $request)
    {
        $validated = $request->validated();
        $business = $request->user()->businesses()->with(['locations', 'services'])->first();

        if (! $business) {
            return redirect()->route('onboarding.business');
        }

        $master = Master::create([
            'business_id' => $business->id,
            'user_id' => $request->user()->id,
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'] ?? null,
            'description' => $validated['description'] ?? null,
            'specialization' => $validated['specialization'],
            'phone' => $validated['phone'],
            'email' => $validated['email'] ?? null,
            'working_hours' => WorkingHoursService::toJson($validated['working_hours']),
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
        $user = Auth::user()->load(['businesses.locations', 'businesses.services', 'businesses.masters']);
        $business = $user->businesses->first();

        if (! $business) {
            return redirect()->route('onboarding.business');
        }

        return view('onboarding.complete', [
            'business' => $business,
        ]);
    }
}
