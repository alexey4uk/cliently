<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\LocationRequest;
use App\Models\Location;
use App\Repositories\LocationRepositoryInterface;
use App\Services\WorkingHoursService;
use Illuminate\Support\Facades\Auth;

class LocationSettingsController extends Controller
{
    private LocationRepositoryInterface $locationRepository;

    public function __construct(LocationRepositoryInterface $locationRepository)
    {
        $this->locationRepository = $locationRepository;
    }

    /**
     * Список локаций
     */
    public function index()
    {
        $user = Auth::user()->load('businesses.locations');
        $business = $user->businesses->first();

        if (! $business) {
            return redirect()->route('settings.business.create')
                ->with('info', 'Сначала создайте бизнес.');
        }

        return view('settings.locations.index', [
            'business' => $business,
            'locations' => $business->locations,
        ]);
    }

    /**
     * Страница создания новой локации
     */
    public function create()
    {
        $user = Auth::user()->load('businesses');
        $business = $user->businesses->first();

        if (! $business) {
            return redirect()->route('settings.business.create')
                ->with('info', 'Сначала создайте бизнес.');
        }

        return view('settings.locations.create', [
            'business' => $business,
        ]);
    }

    /**
     * Сохранение новой локации
     */
    public function store(LocationRequest $request)
    {
        $user = Auth::user()->load('businesses');
        $business = $user->businesses->first();

        if (! $business) {
            return redirect()->route('settings.business.create')
                ->with('info', 'Сначала создайте бизнес.');
        }

        $validated = $request->validated();

        $this->locationRepository->create([
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
    public function edit(Location $location)
    {
        $user = Auth::user()->load('businesses');
        $business = $user->businesses->first();

        if (! $business || ! $this->locationRepository->belongsToBusiness($location->id, $business->id)) {
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
    public function update(LocationRequest $request, Location $location)
    {
        $user = Auth::user()->load('businesses');
        $business = $user->businesses->first();

        if (! $business || ! $this->locationRepository->belongsToBusiness($location->id, $business->id)) {
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
    public function destroy(Location $location)
    {
        $user = Auth::user()->load('businesses');
        $business = $user->businesses->first();

        if (! $business || ! $this->locationRepository->belongsToBusiness($location->id, $business->id)) {
            return redirect()->route('settings.locations');
        }

        $location->delete();

        return redirect()->route('settings.locations')->with('success', 'Локация удалена');
    }
}
