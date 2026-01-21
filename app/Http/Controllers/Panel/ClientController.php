<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Business;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

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
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhereRaw("CONCAT(COALESCE(first_name, ''), ' ', COALESCE(last_name, '')) LIKE ?", ["%{$search}%"]);
            });
        }

        // Фильтр по бизнесу
        if ($businessFilter) {
            $query->where('business_id', $businessFilter);
        }

        // Сортировка
        $allowedSorts = ['created_at', 'name', 'phone', 'email'];
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

        return view('panel.clients.create', compact('businesses'));
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
            'phone' => 'required|string|max:20|unique:clients,phone',
            'business_id' => 'required|exists:businesses,id',
        ]);

        Client::create($request->only([
            'first_name',
            'last_name',
            'email',
            'phone',
            'business_id',
        ]));

        return redirect()->route('panel.clients')->with('success', 'Клиент создан успешно');
    }

    /**
     * Show the form for editing the specified client.
     */
    public function edit(Client $client)
    {
        $businesses = Business::orderBy('name')->get();

        return view('panel.clients.edit', compact('client', 'businesses'));
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
            'phone' => 'required|string|max:20|unique:clients,phone,'.$client->id,
            'business_id' => 'required|exists:businesses,id',
        ]);

        $client->update($request->only([
            'first_name',
            'last_name',
            'email',
            'phone',
            'business_id',
        ]));

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
