<?php

namespace App\Http\Controllers;

use App\Http\Requests\ClientRequest;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user()->load('businesses');
        $business = $user->businesses->first();

        if (!$business) {
            return redirect()->route('onboarding.business');
        }

        $query = $business->clients();

        // Поиск
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Сортировка
        $sort = $request->get('sort', 'created_at');
        $direction = $request->get('direction', 'desc');

        if ($sort === 'name') {
            $query->orderBy('first_name', $direction)
                  ->orderBy('last_name', $direction);
        } else {
            $query->orderBy($sort, $direction);
        }

        $clients = $query->paginate(15)->withQueryString();

        return view('clients.index', [
            'business' => $business,
            'clients' => $clients,
            'search' => $request->get('search', ''),
            'sort' => $sort,
            'direction' => $direction,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = Auth::user()->load('businesses');
        $business = $user->businesses->first();

        if (!$business) {
            return redirect()->route('onboarding.business');
        }

        return view('clients.create', [
            'business' => $business,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ClientRequest $request)
    {
        $user = Auth::user()->load('businesses');
        $business = $user->businesses->first();

        if (!$business) {
            return redirect()->route('onboarding.business');
        }

        $validated = $request->validated();

        Client::create([
            'business_id' => $business->id,
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'] ?? null,
            'email' => $validated['email'] ?? null,
            'phone' => $validated['phone'],
        ]);

        return redirect()->route('clients.index')->with('success', 'Клиент добавлен');
    }

    /**
     * Display the specified resource.
     */
    public function show(Client $client)
    {
        $user = Auth::user()->load('businesses');
        $business = $user->businesses->first();

        if (!$business || $client->business_id !== $business->id) {
            return redirect()->route('clients.index');
        }

        return view('clients.show', [
            'business' => $business,
            'client' => $client,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Client $client)
    {
        $user = Auth::user()->load('businesses');
        $business = $user->businesses->first();

        if (!$business || $client->business_id !== $business->id) {
            return redirect()->route('clients.index');
        }

        return view('clients.edit', [
            'business' => $business,
            'client' => $client,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ClientRequest $request, Client $client)
    {
        $user = Auth::user()->load('businesses');
        $business = $user->businesses->first();

        if (!$business || $client->business_id !== $business->id) {
            return redirect()->route('clients.index');
        }

        $validated = $request->validated();

        $client->update([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'] ?? null,
            'email' => $validated['email'] ?? null,
            'phone' => $validated['phone'],
        ]);

        return redirect()->route('clients.index')->with('success', 'Клиент обновлен');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Client $client)
    {
        $user = Auth::user()->load('businesses');
        $business = $user->businesses->first();

        if (!$business || $client->business_id !== $business->id) {
            return redirect()->route('clients.index');
        }

        $client->delete();

        return redirect()->route('clients.index')->with('success', 'Клиент удален');
    }
}
