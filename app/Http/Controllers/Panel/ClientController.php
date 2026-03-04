<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Country;
use App\Repositories\BusinessRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ClientController extends Controller
{
    protected BusinessRepositoryInterface $businessRepository;

    public function __construct(BusinessRepositoryInterface $businessRepository)
    {
        $this->businessRepository = $businessRepository;
    }

    /**
     * Display a listing of clients.
     */
    public function index()
    {
        $search = request('search', '');
        $sort = request('sort', 'created_at');
        $direction = strtolower((string) request('direction', 'desc')) === 'asc' ? 'asc' : 'desc';
        $perPage = min((int) request('per_page', 20), 100);
        $businessFilter = request('business_id', '');

        $query = Client::query()
            ->with(['business'])
            ->withCount('appointments')
            ->withCount(['appointments as upcoming_appointments_count' => function ($q) {
                $q->whereRaw("(date || ' ' || time)::timestamp > ?", [now()]);
            }]);

        if ($search) {
            $query->where(function ($q) use ($search) {
                // В PostgreSQL вместо MATCH AGAINST используем ILIKE (регистронезависимый)
                $q->where('first_name', 'ILIKE', "%{$search}%")
                    ->orWhere('last_name', 'ILIKE', "%{$search}%")
                    ->orWhereIn('id', function ($subquery) use ($search) {
                        $subquery->select('phoneable_id')
                            ->from('phones')
                            ->where('phoneable_type', Client::class)
                            ->where('phone', 'like', "%{$search}%");
                    });
            });
        }

        if ($businessFilter) {
            $query->where('business_id', $businessFilter);
        }

        $allowedSorts = ['created_at', 'name', 'email'];
        if (in_array($sort, $allowedSorts)) {
            if ($sort === 'name') {
                $query->orderByRaw("(COALESCE(first_name, '') || ' ' || COALESCE(last_name, '')) {$direction}");
            } else {
                $query->orderBy($sort, $direction);
            }
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $clients = $query->simplePaginate($perPage)->withQueryString();

        $businesses = $this->businessRepository->getAllForFilter();

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
        $businesses = $this->businessRepository->getAllForFilter();
        $countries = Country::getForPhoneSelect();

        return view('panel.clients.create', compact('businesses', 'countries'));
    }

    /**
     * Store a newly created client in storage.
     */
    public function store(Request $request)
    {
        $request->merge([
            'phone_country_code' => $request->filled('phone_country_code')
                ? strtoupper(substr($request->phone_country_code, 0, 2))
                : null,
        ]);
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'email' => 'nullable|email|unique:clients,email',
            'phone_country_code' => ['required', 'string', 'size:2', Rule::exists('countries', 'code')],
            'phone' => ['required', 'string', 'regex:/^\+[0-9]{10,15}$/', Rule::unique('clients', 'phone')],
            'business_id' => 'required|exists:businesses,id',
        ], [
            'phone.regex' => 'Телефон в формате E.164 (например, +375291234567).',
            'phone.unique' => 'Этот телефон уже используется.',
        ]);

        $phoneCountryCode = $request->phone_country_code;
        $client = Client::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'business_id' => $request->business_id,
            'phone' => $request->phone,
            'phone_country_code' => $phoneCountryCode,
        ]);

        return redirect()->route('panel.clients')->with('success', 'Клиент создан успешно');
    }

    /**
     * Show the form for editing the specified client.
     */
    public function edit(Client $client)
    {
        $businesses = $this->businessRepository->getAllForFilter();
        $countries = Country::getForPhoneSelect();

        return view('panel.clients.edit', compact('client', 'businesses', 'countries'));
    }

    /**
     * Update the specified client in storage.
     */
    public function update(Request $request, Client $client)
    {
        $request->merge([
            'phone_country_code' => $request->filled('phone_country_code')
                ? strtoupper(substr($request->phone_country_code, 0, 2))
                : null,
        ]);
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'email' => 'nullable|email|unique:clients,email,'.$client->id,
            'phone_country_code' => ['required', 'string', 'size:2', Rule::exists('countries', 'code')],
            'phone' => [
                'required',
                'string',
                'regex:/^\+[0-9]{10,15}$/',
                Rule::unique('clients', 'phone')->ignore($client->id),
            ],
            'business_id' => 'required|exists:businesses,id',
        ], [
            'phone.regex' => 'Телефон в формате E.164 (например, +375291234567).',
            'phone.unique' => 'Этот телефон уже используется.',
        ]);

        $phoneCountryCode = $request->phone_country_code;
        $client->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'business_id' => $request->business_id,
            'phone' => $request->phone,
            'phone_country_code' => $phoneCountryCode,
        ]);

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
