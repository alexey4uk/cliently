<?php

namespace App\Http\Controllers;

use App\Http\Requests\BusinessRequest;
use App\Http\Requests\LocationRequest;
use App\Http\Requests\MasterRequest;
use App\Models\Business;
use App\Models\Location;
use App\Models\Master;
use App\Services\WorkingHoursService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BusinessSettingsController extends Controller
{
    /**
     * Главная страница настроек бизнеса
     */
    public function index()
    {
        $user = Auth::user()->load(['businesses.locations', 'businesses.services', 'businesses.masters']);
        $business = $user->businesses->first();

        if (!$business) {
            return redirect()->route('onboarding.business');
        }

        return view('settings.index', [
            'business' => $business,
        ]);
    }

    /**
     * Страница редактирования данных бизнеса
     */
    public function edit()
    {
        $user = Auth::user()->load('businesses');
        $business = $user->businesses->first();

        if (!$business) {
            return redirect()->route('onboarding.business');
        }

        return view('settings.business.edit', [
            'business' => $business,
        ]);
    }

    /**
     * Обновление данных бизнеса
     */
    public function update(BusinessRequest $request)
    {
        $user = Auth::user()->load('businesses');
        $business = $user->businesses->first();

        if (!$business) {
            return redirect()->route('onboarding.business');
        }

        $business->update($request->validated());

        return redirect()->route('settings.index')->with('success', 'Данные бизнеса обновлены');
    }

    /**
     * Список локаций
     */
    public function locations()
    {
        $user = Auth::user()->load('businesses.locations');
        $business = $user->businesses->first();

        if (!$business) {
            return redirect()->route('onboarding.business');
        }

        return view('settings.locations.index', [
            'business' => $business,
            'locations' => $business->locations,
        ]);
    }

    /**
     * Страница создания новой локации
     */
    public function createLocation()
    {
        $user = Auth::user()->load('businesses');
        $business = $user->businesses->first();

        if (!$business) {
            return redirect()->route('onboarding.business');
        }

        return view('settings.locations.create', [
            'business' => $business,
        ]);
    }

    /**
     * Сохранение новой локации
     */
    public function storeLocation(LocationRequest $request)
    {
        $user = Auth::user()->load('businesses');
        $business = $user->businesses->first();

        if (!$business) {
            return redirect()->route('onboarding.business');
        }

        $validated = $request->validated();

        Location::create([
            'business_id' => $business->id,
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

        return redirect()->route('settings.locations')->with('success', 'Локация добавлена');
    }

    /**
     * Страница редактирования локации
     */
    public function editLocation(Location $location)
    {
        $user = Auth::user()->load('businesses');
        $business = $user->businesses->first();

        if (!$business || $location->business_id !== $business->id) {
            return redirect()->route('settings.locations');
        }

        return view('settings.locations.edit', [
            'business' => $business,
            'location' => $location,
        ]);
    }

    /**
     * Обновление локации
     */
    public function updateLocation(LocationRequest $request, Location $location)
    {
        $user = Auth::user()->load('businesses');
        $business = $user->businesses->first();

        if (!$business || $location->business_id !== $business->id) {
            return redirect()->route('settings.locations');
        }

        $validated = $request->validated();

        $location->update([
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

        return redirect()->route('settings.locations')->with('success', 'Локация обновлена');
    }

    /**
     * Удаление локации
     */
    public function destroyLocation(Location $location)
    {
        $user = Auth::user()->load('businesses');
        $business = $user->businesses->first();

        if (!$business || $location->business_id !== $business->id) {
            return redirect()->route('settings.locations');
        }

        $location->delete();

        return redirect()->route('settings.locations')->with('success', 'Локация удалена');
    }

    /**
     * Список мастеров
     */
    public function masters()
    {
        $user = Auth::user()->load('businesses.masters');
        $business = $user->businesses->first();

        if (!$business) {
            return redirect()->route('onboarding.business');
        }

        return view('settings.masters.index', [
            'business' => $business,
            'masters' => $business->masters,
        ]);
    }

    /**
     * Страница создания нового мастера
     */
    public function createMaster()
    {
        $user = Auth::user()->load(['businesses.locations', 'businesses.services']);
        $business = $user->businesses->first();

        if (!$business) {
            return redirect()->route('onboarding.business');
        }

        return view('settings.masters.create', [
            'business' => $business,
            'locations' => $business->locations,
            'services' => $business->services,
        ]);
    }

    /**
     * Сохранение нового мастера
     */
    public function storeMaster(MasterRequest $request)
    {
        $user = Auth::user()->load(['businesses.locations', 'businesses.services']);
        $business = $user->businesses->first();

        if (!$business) {
            return redirect()->route('onboarding.business');
        }

        $validated = $request->validated();

        $master = Master::create([
            'business_id' => $business->id,
            'user_id' => $user->id,
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'] ?? null,
            'description' => $validated['description'] ?? null,
            'specialization' => $validated['specialization'],
            'phone' => $validated['phone'],
            'email' => $validated['email'] ?? null,
            'working_hours' => WorkingHoursService::toJson($validated['working_hours']),
        ]);

        if (!empty($validated['location_ids'])) {
            $master->locations()->attach($validated['location_ids']);
        }

        if (!empty($validated['service_ids'])) {
            $master->services()->attach($validated['service_ids']);
        }

        return redirect()->route('settings.masters')->with('success', 'Мастер добавлен');
    }

    /**
     * Страница редактирования мастера
     */
    public function editMaster(Master $master)
    {
        $user = Auth::user()->load(['businesses.locations', 'businesses.services']);
        $business = $user->businesses->first();

        if (!$business || $master->business_id !== $business->id) {
            return redirect()->route('settings.masters');
        }

        $master->load(['locations', 'services']);

        return view('settings.masters.edit', [
            'business' => $business,
            'master' => $master,
            'locations' => $business->locations,
            'services' => $business->services,
        ]);
    }

    /**
     * Обновление мастера
     */
    public function updateMaster(MasterRequest $request, Master $master)
    {
        $user = Auth::user()->load(['businesses.locations', 'businesses.services']);
        $business = $user->businesses->first();

        if (!$business || $master->business_id !== $business->id) {
            return redirect()->route('settings.masters');
        }

        $validated = $request->validated();

        $master->update([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'] ?? null,
            'description' => $validated['description'] ?? null,
            'specialization' => $validated['specialization'],
            'phone' => $validated['phone'],
            'email' => $validated['email'] ?? null,
            'working_hours' => WorkingHoursService::toJson($validated['working_hours']),
            'is_active' => $validated['is_active'] ?? $master->is_active,
        ]);

        if (isset($validated['location_ids'])) {
            $master->locations()->sync($validated['location_ids']);
        }

        if (isset($validated['service_ids'])) {
            $master->services()->sync($validated['service_ids']);
        }

        return redirect()->route('settings.masters')->with('success', 'Мастер обновлен');
    }

    /**
     * Удаление мастера
     */
    public function destroyMaster(Master $master)
    {
        $user = Auth::user()->load('businesses');
        $business = $user->businesses->first();

        if (!$business || $master->business_id !== $business->id) {
            return redirect()->route('settings.masters');
        }

        $master->delete();

        return redirect()->route('settings.masters')->with('success', 'Мастер удален');
    }
}

