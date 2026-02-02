<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\LocationRequest;
use App\Models\Country;
use App\Models\Location;
use App\Repositories\LocationRepositoryInterface;
use App\Services\BusinessRolePermissionService;
use App\Services\SubscriptionService;
use App\Services\WorkingHoursService;
use Illuminate\Http\Request;
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
    public function index(Request $request)
    {
        $business = $this->getCurrentBusiness();

        if (! $business) {
            return redirect()->route('welcome')
                ->with('info', 'Сначала создайте бизнес или примите приглашение.');
        }

        $query = $business->locations();

        $search = $request->get('search', '');
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%'.$search.'%')
                    ->orWhere('city', 'like', '%'.$search.'%')
                    ->orWhere('street', 'like', '%'.$search.'%')
                    ->orWhere('house', 'like', '%'.$search.'%')
                    ->orWhere('description', 'like', '%'.$search.'%');
            });
        }

        $locations = $query->orderBy('name')->get();

        $role = $this->getCurrentBusinessRole();
        $permissionService = app(BusinessRolePermissionService::class);
        $canCreateLocations = $role && $permissionService->hasPermission($role->id, 'client.locations.create');
        $canUpdateLocations = $role && $permissionService->hasPermission($role->id, 'client.locations.update');
        $canDeleteLocations = $role && $permissionService->hasPermission($role->id, 'client.locations.delete');
        $hasAnyLocationAction = $canUpdateLocations || $canDeleteLocations;
        $canCreateLocation = $canCreateLocations && app(SubscriptionService::class)->canCreateLocation(Auth::user());

        return view('settings.locations.index', [
            'business' => $business,
            'locations' => $locations,
            'search' => $search,
            'canCreateLocations' => $canCreateLocations,
            'canUpdateLocations' => $canUpdateLocations,
            'canDeleteLocations' => $canDeleteLocations,
            'canCreateLocation' => $canCreateLocation,
            'hasAnyLocationAction' => $hasAnyLocationAction,
        ]);
    }

    /**
     * Страница создания новой локации
     */
    public function create()
    {
        $business = $this->getCurrentBusiness();

        if (! $business) {
            return redirect()->route('welcome')
                ->with('info', 'Сначала создайте бизнес или примите приглашение.');
        }

        return view('settings.locations.create', [
            'business' => $business,
            'countries' => Country::getCached(),
        ]);
    }

    /**
     * Сохранение новой локации
     */
    public function store(LocationRequest $request)
    {
        $business = $this->getCurrentBusiness();

        if (! $business) {
            return redirect()->route('welcome')
                ->with('info', 'Сначала создайте бизнес или примите приглашение.');
        }

        // Проверка лимита локаций
        $user = Auth::user();
        $subscriptionService = app(SubscriptionService::class);
        if (! $subscriptionService->canCreateLocation($user)) {
            return redirect()->back()
                ->withInput()
                ->with('error', \App\Services\SubscriptionService::planLimitErrorMessage());
        }

        $validated = $request->validated();
        $phoneCountryId = (int) $validated['phone_country_id'];
        $phoneE164 = $validated['phone'];

        $location = $this->locationRepository->create([
            'business_id' => $business->id,
            'name' => $validated['name'],
            'city' => $validated['city'],
            'street' => $validated['street'],
            'house' => $validated['house'],
            'building' => $validated['building'] ?? null,
            'apartment' => $validated['apartment'] ?? null,
            'description' => $validated['description'] ?? null,
            'working_hours' => WorkingHoursService::toJson($validated['working_hours']),
        ]);

        $location->phones()->create([
            'country_id' => $phoneCountryId,
            'phone' => $phoneE164,
            'type' => 'primary',
        ]);

        // Увеличиваем usage (для месячных метрик, но для локаций это не нужно, т.к. считаем напрямую)
        // Но оставим для консистентности, если в будущем понадобится

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
            'countries' => Country::getCached(),
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
        $phoneCountryId = (int) $validated['phone_country_id'];
        $phoneE164 = $validated['phone'];

        $location->update([
            'name' => $validated['name'],
            'city' => $validated['city'],
            'street' => $validated['street'],
            'house' => $validated['house'],
            'building' => $validated['building'] ?? null,
            'apartment' => $validated['apartment'] ?? null,
            'description' => $validated['description'] ?? null,
            'working_hours' => WorkingHoursService::toJson($validated['working_hours']),
        ]);

        $primary = $location->primaryPhone;
        if ($primary) {
            $primary->update(['country_id' => $phoneCountryId, 'phone' => $phoneE164]);
        } else {
            $location->phones()->create([
                'country_id' => $phoneCountryId,
                'phone' => $phoneE164,
                'type' => 'primary',
            ]);
        }

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

        // Уменьшать usage не нужно, т.к. для локаций считаем напрямую из БД
        // Но оставим для консистентности, если в будущем понадобится

        return redirect()->route('settings.locations')->with('success', 'Локация удалена');
    }
}
