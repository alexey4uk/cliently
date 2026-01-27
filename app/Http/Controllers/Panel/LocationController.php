<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\Location;
use App\Repositories\BusinessRepositoryInterface;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    protected BusinessRepositoryInterface $businessRepository;

    public function __construct(BusinessRepositoryInterface $businessRepository)
    {
        $this->businessRepository = $businessRepository;
    }

    /**
     * Display a listing of locations.
     */
    public function index()
    {
        $search = request('search', '');
        $sort = request('sort', 'created_at');
        $direction = request('direction', 'desc');
        $perPage = min((int) request('per_page', 20), 100);
        $businessFilter = request('business_id', '');

        // ОПТИМИЗИРОВАНО: Подзапрос через pivot таблицу
        $query = Location::query()
            ->with('business')
            ->selectRaw('locations.*, 
                (SELECT COUNT(*) FROM master_location WHERE master_location.location_id = locations.id) as masters_count'
            );

        // ОПТИМИЗИРОВАННЫЙ ПОИСК
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('city', 'like', "%{$search}%")
                    ->orWhere('street', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    // Поиск по телефону через подзапрос (быстрее whereHas)
                    ->orWhereIn('id', function ($subquery) use ($search) {
                        $subquery->select('phoneable_id')
                            ->from('phones')
                            ->where('phoneable_type', Location::class)
                            ->where('phone', 'like', "%{$search}%");
                    });
            })->limit(1000); // Ограничение для поиска
        }

        // Фильтр по бизнесу
        if ($businessFilter) {
            $query->where('business_id', $businessFilter);
        }

        // Сортировка
        $allowedSorts = ['name', 'city', 'created_at'];
        if (in_array($sort, $allowedSorts)) {
            $query->orderBy($sort, $direction);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        // ОПТИМИЗАЦИЯ: simplePaginate вместо paginate
        $locations = $query->simplePaginate($perPage)->withQueryString();

        // Получаем список бизнесов для фильтра
        $businesses = $this->businessRepository->getAllForFilter();

        return view('panel.locations.index', compact(
            'locations',
            'search',
            'sort',
            'direction',
            'perPage',
            'businessFilter',
            'businesses'
        ));
    }

    /**
     * Display the specified location.
     */
    public function show(Location $location)
    {
        // ОПТИМИЗАЦИЯ: Только business и counts (без загрузки всех мастеров)
        $location->load(['business']);

        // Подсчитываем связанные данные
        $location->masters_count = $location->masters()->count();
        $appointmentsCount = \App\Models\Appointment::where('location_id', $location->id)->count();

        return view('panel.locations.show', compact('location', 'appointmentsCount'));
    }

    /**
     * Show the form for editing the specified location.
     */
    public function edit(Location $location)
    {
        $businesses = $this->businessRepository->getAllForFilter();
        $countries = Country::getCached();

        return view('panel.locations.edit', compact('location', 'businesses', 'countries'));
    }

    /**
     * Update the specified location in storage.
     */
    public function update(Request $request, Location $location)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'street' => 'required|string|max:255',
            'house' => 'required|string|max:50',
            'building' => 'nullable|string|max:50',
            'apartment' => 'nullable|string|max:50',
            'description' => 'nullable|string|max:500',
            'phone_country_id' => ['nullable', 'required_with:phone', 'exists:countries,id'],
            'phone' => ['nullable', 'string', 'regex:/^\+[0-9]{10,15}$/'],
            'business_id' => 'required|exists:businesses,id',
        ]);

        $location->update([
            'name' => $validated['name'],
            'city' => $validated['city'],
            'street' => $validated['street'],
            'house' => $validated['house'],
            'building' => $validated['building'] ?? null,
            'apartment' => $validated['apartment'] ?? null,
            'description' => $validated['description'] ?? null,
            'business_id' => $validated['business_id'],
        ]);

        $phoneCountryId = isset($validated['phone_country_id']) ? (int) $validated['phone_country_id'] : null;
        $phoneE164 = $validated['phone'] ?? null;
        $primary = $location->primaryPhone;
        if ($phoneE164 && $phoneCountryId) {
            if ($primary) {
                $primary->update(['country_id' => $phoneCountryId, 'phone' => $phoneE164]);
            } else {
                $location->phones()->create([
                    'country_id' => $phoneCountryId,
                    'phone' => $phoneE164,
                    'type' => 'primary',
                ]);
            }
        } elseif ($primary) {
            $primary->delete();
        }

        return redirect()->route('panel.locations.show', $location)->with('success', 'Локация успешно обновлена');
    }

    /**
     * Remove the specified location from storage.
     */
    public function destroy(Location $location)
    {
        // ОПТИМИЗАЦИЯ: exists() вместо count()
        $hasAppointments = \App\Models\Appointment::where('location_id', $location->id)->exists();

        if ($hasAppointments || $location->masters()->exists()) {
            return redirect()->route('panel.locations.show', $location)
                ->with('error', 'Невозможно удалить локацию, так как у неё есть связанные данные (записи или мастера)');
        }

        $location->delete();

        return redirect()->route('panel.locations')->with('success', 'Локация успешно удалена');
    }
}
