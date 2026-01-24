<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\Client;
use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rule;

class ClientController extends Controller
{
    /**
     * Display a listing of clients.
     */
    public function index()
    {
        $search = request('search', '');
        $sort = request('sort', 'created_at');
        $direction = request('direction', 'desc');
        $perPage = request('per_page', 20);
        $businessFilter = request('business_id', '');

        $query = Client::with(['business', 'appointments'])
            ->withCount(['appointments', 'appointments as upcoming_appointments_count' => function ($query) {
                $query->whereRaw("CONCAT(date, ' ', time) > ?", [now()->toDateTimeString()]);
            }]);

        // Поиск
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhereHas('phones', fn ($p) => $p->where('phone', 'like', "%{$search}%"))
                  ->orWhereRaw("CONCAT(COALESCE(first_name, ''), ' ', COALESCE(last_name, '')) LIKE ?", ["%{$search}%"]);
            });
        }

        // Фильтр по бизнесу
        if ($businessFilter) {
            $query->where('business_id', $businessFilter);
        }

        // Сортировка
        $allowedSorts = ['created_at', 'name', 'email'];
        if (in_array($sort, $allowedSorts)) {
            if ($sort === 'name') {
                $query->orderByRaw("CONCAT(COALESCE(first_name, ''), ' ', COALESCE(last_name, '')) {$direction}");
            } else {
                $query->orderBy($sort, $direction);
            }
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $clients = $query->paginate($perPage)->withQueryString();

        // Получаем список бизнесов для фильтра
        $businesses = \App\Models\Business::orderBy('name')->get();

        return view('panel.clients.index', compact(
            'clients',
            'search',
            'sort',
            'direction',
            'perPage',
            'businessFilter',
            'businesses'
        ));
    }

    /**
     * Show the form for creating a new client.
     */
    public function create()
    {
        $businesses = Business::orderBy('name')->get();
        $countries = Country::orderBy('name')->get();

        return view('panel.clients.create', compact('businesses', 'countries'));
    }

    /**
     * Store a newly created client in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'email' => 'nullable|email|unique:clients,email',
            'phone_country_id' => ['required', 'exists:countries,id'],
            'phone' => ['required', 'string', 'regex:/^\+[0-9]{10,15}$/', Rule::unique('phones', 'phone')->where('phoneable_type', Client::class)],
            'business_id' => 'required|exists:businesses,id',
        ], [
            'phone.regex' => 'Телефон в формате E.164 (например, +375291234567).',
            'phone.unique' => 'Этот телефон уже используется.',
        ]);

        $client = Client::create($request->only(['first_name', 'last_name', 'email', 'business_id']));

        $client->phones()->create([
            'country_id' => (int) $request->phone_country_id,
            'phone' => $request->phone,
            'type' => 'primary',
        ]);

        return redirect()->route('panel.clients')->with('success', 'Клиент создан успешно');
    }

    /**
     * Show the form for editing the specified client.
     */
    public function edit(Client $client)
    {
        $businesses = Business::orderBy('name')->get();
        $countries = Country::orderBy('name')->get();

        return view('panel.clients.edit', compact('client', 'businesses', 'countries'));
    }

    /**
     * Update the specified client in storage.
     */
    public function update(Request $request, Client $client)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'email' => 'nullable|email|unique:clients,email,'.$client->id,
            'phone_country_id' => ['required', 'exists:countries,id'],
            'phone' => [
                'required',
                'string',
                'regex:/^\+[0-9]{10,15}$/',
                Rule::unique('phones', 'phone')->where('phoneable_type', Client::class)->ignore($client->primaryPhone?->id),
            ],
            'business_id' => 'required|exists:businesses,id',
        ], [
            'phone.regex' => 'Телефон в формате E.164 (например, +375291234567).',
            'phone.unique' => 'Этот телефон уже используется.',
        ]);

        $client->update($request->only(['first_name', 'last_name', 'email', 'business_id']));

        $primary = $client->primaryPhone;
        if ($primary) {
            $primary->update([
                'country_id' => (int) $request->phone_country_id,
                'phone' => $request->phone,
            ]);
        } else {
            $client->phones()->create([
                'country_id' => (int) $request->phone_country_id,
                'phone' => $request->phone,
                'type' => 'primary',
            ]);
        }

        return redirect()->route('panel.clients')->with('success', 'Клиент обновлен успешно');
    }

    /**
     * Remove the specified client from storage.
     */
    public function destroy(Client $client)
    {
        $client->delete();

        return redirect()->route('panel.clients')->with('success', 'Клиент удален успешно');
    }
}
