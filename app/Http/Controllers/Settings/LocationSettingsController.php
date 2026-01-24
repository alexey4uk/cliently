<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\LocationRequest;
use App\Models\Country;
use App\Models\Location;
use App\Repositories\LocationRepositoryInterface;
use App\Services\SubscriptionService;
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
        $business = $this->getCurrentBusiness();

        if (!$business) {
            return redirect()->route('welcome')
                ->with('info', 'Сначала создайте бизнес или примите приглашение.');
        }

        $business->load('locations');

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
        $business = $this->getCurrentBusiness();

        if (!$business) {
            return redirect()->route('welcome')
                ->with('info', 'Сначала создайте бизнес или примите приглашение.');
        }

        return view('settings.locations.create', [
            'business' => $business,
            'countries' => Country::orderBy('name')->get(),
        ]);
    }

    /**
     * Сохранение новой локации
     */
    public function store(LocationRequest $request)
    {
        $business = $this->getCurrentBusiness();

        if (!$business) {
            return redirect()->route('welcome')
                ->with('info', 'Сначала создайте бизнес или примите приглашение.');
        }

        // Проверка лимита локаций
        $user = Auth::user();
        $subscriptionService = app(SubscriptionService::class);
        if (! $subscriptionService->canCreateLocation($user)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Достигнут лимит локаций для вашего тарифа. Обновите тариф для добавления большего количества локаций.');
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
            'countries' => Country::orderBy('name')->get(),
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
