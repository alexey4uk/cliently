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
        $direction = request('direction', 'desc');
        $perPage = min((int) request('per_page', 20), 100);
        $businessFilter = request('business_id', '');

        // ОПТИМИЗИРОВАНО: убрали with(['appointments']) - критично медленно!
        // Добавили подзапросы для COUNT вместо withCount
        $query = Client::query()
            ->with(['business'])
            ->selectRaw('clients.*, 
                (SELECT COUNT(*) FROM appointments WHERE appointments.client_id = clients.id) as appointments_count,
                (SELECT COUNT(*) FROM appointments WHERE appointments.client_id = clients.id AND CONCAT(date, " ", time) > ?) as upcoming_appointments_count',
                [now()->toDateTimeString()]
            );

        // ОПТИМИЗИРОВАННЫЙ ПОИСК с FULLTEXT
        if ($search) {
            // Используем FULLTEXT для имен (быстро!)
            $searchTerm = $search.'*';
            $query->where(function ($q) use ($search, $searchTerm) {
                // FULLTEXT поиск по именам
                $q->whereRaw('MATCH(first_name, last_name) AGAINST(? IN BOOLEAN MODE)', [$searchTerm])
                    // Обычный поиск по email (FULLTEXT не нужен для email)
                    ->orWhere('email', 'like', "%{$search}%")
                    // Поиск по телефону через JOIN (быстрее чем whereHas)
                    ->orWhereIn('id', function ($subquery) use ($search) {
                        $subquery->select('phoneable_id')
                            ->from('phones')
                            ->where('phoneable_type', Client::class)
                            ->where('phone', 'like', "%{$search}%");
                    });
            })->limit(1000); // Ограничение для поиска
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

        // ОПТИМИЗАЦИЯ: simplePaginate вместо paginate (без медленного COUNT)
        $clients = $query->simplePaginate($perPage)->withQueryString();

        // Получаем список бизнесов для фильтра
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
        $countries = Country::getCached();

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
        $businesses = $this->businessRepository->getAllForFilter();
        $countries = Country::getCached();

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
