<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Repositories\BusinessRepositoryInterface;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    protected BusinessRepositoryInterface $businessRepository;

    public function __construct(BusinessRepositoryInterface $businessRepository)
    {
        $this->businessRepository = $businessRepository;
    }

    /**
     * Display a listing of services.
     */
    public function index()
    {
        $search = request('search', '');
        $sort = request('sort', 'created_at');
        $direction = request('direction', 'desc');
        $perPage = min((int) request('per_page', 20), 100);
        $businessFilter = request('business_id', '');

        // ОПТИМИЗИРОВАНО: Подзапрос вместо withCount
        $query = Service::query()
            ->with(['business'])
            ->selectRaw('services.*, 
                (SELECT COUNT(*) FROM appointments WHERE appointments.service_id = services.id) as appointments_count'
            );

        if ($search) {
            $searchTerm = $search.'*';
            $query->where(function ($q) use ($searchTerm, $search) {
                $q->whereRaw('MATCH(name) AGAINST(? IN BOOLEAN MODE)', [$searchTerm])
                    // Обычный LIKE для description (короткие тексты)
                    ->orWhere('description', 'like', "%{$search}%");
            })->limit(1000); // Ограничение для поиска
        }

        // Фильтр по бизнесу
        if ($businessFilter) {
            $query->where('business_id', $businessFilter);
        }

        // Сортировка
        $allowedSorts = ['name', 'price', 'duration', 'created_at'];
        if (in_array($sort, $allowedSorts)) {
            $query->orderBy($sort, $direction);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        // ОПТИМИЗАЦИЯ: simplePaginate вместо paginate
        $services = $query->simplePaginate($perPage)->withQueryString();

        // Получаем список бизнесов для фильтра (кешируется)
        $businesses = $this->businessRepository->getAllForFilter();

        return view('panel.services.index', compact(
            'services',
            'search',
            'sort',
            'direction',
            'perPage',
            'businessFilter',
            'businesses'
        ));
    }

    /**
     * Display the specified service.
     */
    public function show(Service $service)
    {
        // КРИТИЧЕСКАЯ ОПТИМИЗАЦИЯ: НЕ загружаем все appointments!
        $service->load(['business']);

        // Только COUNT
        $service->appointments_count = $service->appointments()->count();

        return view('panel.services.show', compact('service'));
    }

    /**
     * Show the form for editing the specified service.
     */
    public function edit(Service $service)
    {
        $businesses = $this->businessRepository->getAllForFilter();

        return view('panel.services.edit', compact('service', 'businesses'));
    }

    /**
     * Update the specified service in storage.
     */
    public function update(Request $request, Service $service)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'duration' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'business_id' => 'required|exists:businesses,id',
            'is_active' => 'boolean',
        ]);

        $service->update($validated);

        return redirect()->route('panel.services.show', $service)->with('success', 'Услуга успешно обновлена');
    }

    /**
     * Remove the specified service from storage.
     */
    public function destroy(Service $service)
    {
        // ОПТИМИЗАЦИЯ: exists() вместо count()
        if ($service->appointments()->exists()) {
            return redirect()->route('panel.services.show', $service)
                ->with('error', 'Невозможно удалить услугу, так как у неё есть связанные записи');
        }

        $service->delete();

        return redirect()->route('panel.services')->with('success', 'Услуга успешно удалена');
    }
}
