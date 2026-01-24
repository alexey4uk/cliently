<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Business;
use Illuminate\Http\Request;

class BusinessController extends Controller
{
    /**
     * Display a listing of businesses.
     */
    public function index()
    {
        $search = request('search', '');
        $sort = request('sort', 'created_at');
        $direction = request('direction', 'desc');
        $perPage = request('per_page', 20);

        $query = Business::with(['users' => function ($q) {
            $q->wherePivot('role', 'owner');
        }])->withCount(['clients', 'services', 'masters', 'locations', 'appointments']);

        // Поиск
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhereHas('phones', fn ($p) => $p->where('phone', 'like', "%{$search}%"))
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Сортировка
        $allowedSorts = ['name', 'created_at'];
        if (in_array($sort, $allowedSorts)) {
            $query->orderBy($sort, $direction);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $businesses = $query->paginate($perPage)->withQueryString();

        return view('panel.businesses.index', compact(
            'businesses',
            'search',
            'sort',
            'direction',
            'perPage'
        ));
    }

    /**
     * Display the specified business.
     */
    public function show(Business $business)
    {
        $business->load([
            'users' => function ($q) {
                $q->wherePivot('role', 'owner');
            },
            'clients',
            'services',
            'masters',
            'locations',
            'appointments'
        ]);

        $business->loadCount(['clients', 'services', 'masters', 'locations', 'appointments']);

        return view('panel.businesses.show', compact('business'));
    }

    /**
     * Show the form for editing the specified business.
     */
    public function edit(Business $business)
    {
        return view('panel.businesses.edit', compact('business'));
    }

    /**
     * Update the specified business in storage.
     */
    public function update(Request $request, Business $business)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'description' => 'nullable|string|max:500',
        ]);

        $business->update($validated);

        return redirect()->route('panel.businesses.show', $business)->with('success', 'Бизнес успешно обновлен');
    }

    /**
     * Remove the specified business from storage.
     */
    public function destroy(Business $business)
    {
        // Проверяем, есть ли связанные данные
        if ($business->clients()->count() > 0 || 
            $business->appointments()->count() > 0 ||
            $business->services()->count() > 0 ||
            $business->masters()->count() > 0 ||
            $business->locations()->count() > 0) {
            return redirect()->route('panel.businesses.show', $business)
                ->with('error', 'Невозможно удалить бизнес, так как у него есть связанные данные (клиенты, записи, услуги, мастера или локации)');
        }

        $business->delete();

        return redirect()->route('panel.businesses')->with('success', 'Бизнес успешно удален');
    }
}
