<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\Location;
use App\Models\Master;
use App\Models\Service;
use App\Repositories\BusinessRepositoryInterface;
use Illuminate\Http\Request;

class MasterController extends Controller
{
    protected BusinessRepositoryInterface $businessRepository;

    public function __construct(BusinessRepositoryInterface $businessRepository)
    {
        $this->businessRepository = $businessRepository;
    }

    /**
     * Display a listing of masters.
     */
    public function index()
    {
        $search = request('search', '');
        $sort = request('sort', 'created_at');
        $direction = request('direction', 'desc');
        $perPage = min((int) request('per_page', 20), 100);
        $businessFilter = request('business_id', '');

        // ОПТИМИЗИРОВАНО: Подзапрос вместо withCount
        $query = Master::query()
            ->with(['business'])
            ->selectRaw('masters.*, 
                (SELECT COUNT(*) FROM appointments WHERE appointments.master_id = masters.id) as appointments_count'
            );

        if ($search) {
            $searchTerm = $search.'*';
            $query->where(function ($q) use ($searchTerm, $search) {
                $q->whereRaw('MATCH(first_name, last_name) AGAINST(? IN BOOLEAN MODE)', [$searchTerm])
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('specialization', 'like', "%{$search}%")
                    ->orWhereIn('id', function ($subquery) use ($search) {
                        $subquery->select('phoneable_id')
                            ->from('phones')
                            ->where('phoneable_type', Master::class)
                            ->where('phone', 'like', "%{$search}%");
                    });
            })->limit(1000);
        }

        // Фильтр по бизнесу
        if ($businessFilter) {
            $query->where('business_id', $businessFilter);
        }

        // Сортировка
        $allowedSorts = ['first_name', 'last_name', 'email', 'created_at'];
        if (in_array($sort, $allowedSorts)) {
            $query->orderBy($sort, $direction);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        // ОПТИМИЗАЦИЯ: simplePaginate вместо paginate
        $masters = $query->simplePaginate($perPage)->withQueryString();

        // Получаем список бизнесов для фильтра
        $businesses = $this->businessRepository->getAllForFilter();

        return view('panel.masters.index', compact(
            'masters',
            'search',
            'sort',
            'direction',
            'perPage',
            'businessFilter',
            'businesses'
        ));
    }

    /**
     * Display the specified master.
     */
    public function show(Master $master)
    {
        // КРИТИЧЕСКАЯ ОПТИМИЗАЦИЯ: НЕ загружаем все appointments/locations/services!
        $master->load(['business']);

        // Только COUNT
        $master->appointments_count = $master->appointments()->count();
        $master->locations_count = $master->locations()->count();
        $master->services_count = $master->services()->count();

        return view('panel.masters.show', compact('master'));
    }

    /**
     * Show the form for editing the specified master.
     */
    public function edit(Master $master)
    {
        $master->load(['business', 'locations', 'services']);
        $businesses = $this->businessRepository->getAllForFilter();

        // Загружаем локации и услуги для текущего бизнеса мастера
        $locations = collect();
        $services = collect();

        if ($master->business_id) {
            $locations = Location::where('business_id', $master->business_id)->orderBy('name')->get();
            $services = Service::where('business_id', $master->business_id)->orderBy('name')->get();
        }

        $countries = Country::getCached();

        return view('panel.masters.edit', compact('master', 'businesses', 'locations', 'services', 'countries'));
    }

    /**
     * Update the specified master in storage.
     */
    public function update(Request $request, Master $master)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'specialization' => 'required|string|max:255',
            'description' => 'nullable|string',
            'phone_country_id' => ['required', 'exists:countries,id'],
            'phone' => ['required', 'string', 'regex:/^\+[0-9]{10,15}$/'],
            'email' => 'nullable|email|max:255',
            'business_id' => 'required|exists:businesses,id',
            'is_active' => 'boolean',
            'location_ids' => 'nullable|array',
            'location_ids.*' => 'exists:locations,id',
            'service_ids' => 'nullable|array',
            'service_ids.*' => 'exists:services,id',
            'working_hours' => 'nullable|array',
            'working_hours.from' => 'required_without:working_hours.24_hours|date_format:H:i',
            'working_hours.to' => 'required_without:working_hours.24_hours|date_format:H:i',
            'working_hours.24_hours' => 'nullable|boolean',
            'working_hours.days_off' => 'nullable|array',
            'working_hours.days_off.*' => 'string|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
        ]);

        $updateData = [
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'] ?? null,
            'specialization' => $validated['specialization'],
            'description' => $validated['description'] ?? null,
            'email' => $validated['email'] ?? null,
            'business_id' => $validated['business_id'],
            'is_active' => $validated['is_active'] ?? false,
        ];

        // Добавляем working_hours, если они переданы
        if (isset($validated['working_hours'])) {
            $updateData['working_hours'] = \App\Services\WorkingHoursService::toJson($validated['working_hours']);
        }

        $master->update($updateData);

        $phoneCountryId = (int) $validated['phone_country_id'];
        $phoneE164 = $validated['phone'];
        $primary = $master->primaryPhone;
        if ($primary) {
            $primary->update(['country_id' => $phoneCountryId, 'phone' => $phoneE164]);
        } else {
            $master->phones()->create([
                'country_id' => $phoneCountryId,
                'phone' => $phoneE164,
                'type' => 'primary',
            ]);
        }

        // Синхронизация локаций
        if (isset($validated['location_ids'])) {
            $master->locations()->sync($validated['location_ids']);
        } else {
            $master->locations()->sync([]);
        }

        // Синхронизация услуг
        if (isset($validated['service_ids'])) {
            $master->services()->sync($validated['service_ids']);
        } else {
            $master->services()->sync([]);
        }

        return redirect()->route('panel.masters.show', $master)->with('success', 'Мастер успешно обновлен');
    }

    /**
     * Remove the specified master from storage.
     */
    public function destroy(Master $master)
    {
        // ОПТИМИЗАЦИЯ: exists() вместо count()
        if ($master->appointments()->exists()) {
            return redirect()->back()
                ->with('error', 'Невозможно удалить мастера, так как у него есть связанные записи.');
        }

        $master->delete();

        return redirect()->route('panel.masters')->with('success', 'Мастер успешно удален');
    }
}
