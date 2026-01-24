<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Location;
use App\Models\Business;
use App\Models\Country;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    /**
     * Display a listing of locations.
     */
    public function index()
    {
        $search = request('search', '');
        $sort = request('sort', 'created_at');
        $direction = request('direction', 'desc');
        $perPage = request('per_page', 20);
        $businessFilter = request('business_id', '');

        $query = Location::with('business')
            ->withCount(['services', 'masters']);

        // Поиск
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%")
                  ->orWhere('street', 'like', "%{$search}%")
                  ->orWhereHas('phones', fn ($p) => $p->where('phone', 'like', "%{$search}%"))
                  ->orWhere('description', 'like', "%{$search}%");
            });
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

        $locations = $query->paginate($perPage)->withQueryString();

        // Получаем список бизнесов для фильтра
        $businesses = Business::orderBy('name')->get();

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
        $location->load(['business', 'services', 'masters']);
        $location->loadCount(['services', 'masters']);

        // Подсчитываем записи для этой локации
        $appointmentsCount = \App\Models\Appointment::where('location_id', $location->id)->count();

        return view('panel.locations.show', compact('location', 'appointmentsCount'));
    }

    /**
     * Show the form for editing the specified location.
     */
    public function edit(Location $location)
    {
        $businesses = Business::orderBy('name')->get();
        $countries = Country::orderBy('name')->get();

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
        // Проверяем, есть ли связанные данные
        $appointmentsCount = \App\Models\Appointment::where('location_id', $location->id)->count();
        
        if ($appointmentsCount > 0 || $location->services()->count() > 0 || $location->masters()->count() > 0) {
            return redirect()->route('panel.locations.show', $location)
                ->with('error', 'Невозможно удалить локацию, так как у неё есть связанные данные (записи, услуги или мастера)');
        }

        $location->delete();

        return redirect()->route('panel.locations')->with('success', 'Локация успешно удалена');
    }
}