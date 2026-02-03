<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Country;
use Illuminate\Http\Request;

class CountryController extends Controller
{
    /**
     * Список стран.
     */
    public function index()
    {
        $search = request('search', '');
        $isForPhoneSelect = request('is_for_phone_select', '');
        $isActive = request('is_active', '');
        $sort = request('sort', 'name');
        $direction = request('direction', 'asc');
        $perPage = request('per_page', 20);

        $query = Country::query();

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('name_en', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhere('code_3', 'like', "%{$search}%")
                    ->orWhere('calling_code', 'like', "%{$search}%");
            });
        }

        if ($isForPhoneSelect !== '') {
            $query->where('is_for_phone_select', $isForPhoneSelect === '1');
        }

        if ($isActive !== '') {
            $query->where('is_active', $isActive === '1');
        }

        $allowedSorts = ['name', 'name_en', 'code', 'calling_code', 'created_at'];
        if (in_array($sort, $allowedSorts)) {
            $query->orderBy($sort, $direction === 'desc' ? 'desc' : 'asc');
        } else {
            $query->orderBy('name', 'asc');
        }

        $countries = $query->withCount('phones')->paginate($perPage)->withQueryString();

        return view('panel.countries.index', compact(
            'countries',
            'search',
            'isForPhoneSelect',
            'isActive',
            'sort',
            'direction',
            'perPage'
        ));
    }

    /**
     * Форма создания страны.
     */
    public function create()
    {
        return view('panel.countries.create');
    }

    /**
     * Сохранить новую страну.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|size:2|unique:countries,code',
            'code_3' => 'nullable|string|size:3|unique:countries,code_3',
            'name' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'calling_code' => 'required|string|max:10',
            'currency' => 'nullable|string|max:10',
            'currency_symbol' => 'nullable|string|max:10',
            'ioc' => 'nullable|string|max:3',
            'is_active' => 'boolean',
            'is_for_phone_select' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active');
        $validated['is_for_phone_select'] = $request->boolean('is_for_phone_select');

        Country::create($validated);

        return redirect()->route('panel.countries.index')
            ->with('success', 'Страна успешно создана.');
    }

    /**
     * Форма редактирования страны.
     */
    public function edit(Country $country)
    {
        $country->loadCount('phones');

        return view('panel.countries.edit', compact('country'));
    }

    /**
     * Обновить страну.
     */
    public function update(Request $request, Country $country)
    {
        $validated = $request->validate([
            'code' => 'required|string|size:2|unique:countries,code,'.$country->id,
            'code_3' => 'nullable|string|size:3|unique:countries,code_3,'.$country->id,
            'name' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'calling_code' => 'required|string|max:10',
            'currency' => 'nullable|string|max:10',
            'currency_symbol' => 'nullable|string|max:10',
            'ioc' => 'nullable|string|max:3',
            'is_active' => 'boolean',
            'is_for_phone_select' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active');
        $validated['is_for_phone_select'] = $request->boolean('is_for_phone_select');

        $country->update($validated);

        return redirect()->route('panel.countries.index')
            ->with('success', 'Страна успешно обновлена.');
    }

    /**
     * Удалить страну.
     */
    public function destroy(Country $country)
    {
        if ($country->phones()->exists()) {
            return redirect()->route('panel.countries.index')
                ->with('error', 'Невозможно удалить страну: к ней привязаны телефоны.');
        }

        $country->delete();

        return redirect()->route('panel.countries.index')
            ->with('success', 'Страна успешно удалена.');
    }
}
