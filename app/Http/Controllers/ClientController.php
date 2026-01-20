<?php

namespace App\Http\Controllers;

use App\Http\Requests\ClientRequest;
use App\Models\Client;
use App\Repositories\ClientRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClientController extends Controller
{
    private ClientRepositoryInterface $clientRepository;

    public function __construct(ClientRepositoryInterface $clientRepository)
    {
        $this->clientRepository = $clientRepository;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user()->load('businesses');
        $business = $user->businesses->first();

        if (! $business) {
            return redirect()->route('settings.business.create')
                ->with('info', 'Сначала создайте бизнес.');
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

        // Фильтр по дате
        $period = $request->get('period', '');
        if ($period) {
            switch ($period) {
                case 'today':
                    $query->whereDate('created_at', today());
                    break;
                case 'week':
                    $query->where('created_at', '>=', now()->subWeek());
                    break;
                case 'month':
                    $query->where('created_at', '>=', now()->subMonth());
                    break;
                case 'year':
                    $query->where('created_at', '>=', now()->subYear());
                    break;
            }
        }

        // Фильтр по активности
        $activity = $request->get('activity', '');
        if ($activity) {
            switch ($activity) {
                case 'active':
                    $query->whereHas('appointments');
                    break;
                case 'inactive':
                    $query->whereDoesntHave('appointments');
                    break;
            }
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

        // Количество на страницу
        $perPage = $request->get('per_page', 15);
        $perPage = in_array($perPage, [15, 30, 50]) ? $perPage : 15;

        $clients = $query->withCount(['appointments', 'appointments as upcoming_appointments_count' => function ($q) {
            $q->where('date', '>=', today())
                ->whereIn('status', ['confirmed', 'pending']);
        }])->paginate($perPage)->withQueryString();

        return view('clients.index', [
            'business' => $business,
            'clients' => $clients,
            'search' => $request->get('search', ''),
            'sort' => $sort,
            'direction' => $direction,
            'period' => $period,
            'activity' => $activity,
            'perPage' => $perPage,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = Auth::user()->load('businesses');
        $business = $user->businesses->first();

        if (! $business) {
            return redirect()->route('settings.business.create')
                ->with('info', 'Сначала создайте бизнес.');
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

        if (! $business) {
            return redirect()->route('settings.business.create')
                ->with('info', 'Сначала создайте бизнес.');
        }

        $validated = $request->validated();

        $this->clientRepository->create([
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

        if (! $business || ! $this->clientRepository->belongsToBusiness($client->id, $business->id)) {
            return redirect()->route('clients.index');
        }

        // Статистика записей клиента
        $totalAppointments = $client->appointments()->count();
        $completedAppointments = $client->appointments()->where('status', 'completed')->count();
        $upcomingAppointments = $client->appointments()->where('date', '>=', today())->where('status', 'confirmed')->count();

        return view('clients.show', [
            'business' => $business,
            'client' => $client,
            'totalAppointments' => $totalAppointments,
            'completedAppointments' => $completedAppointments,
            'upcomingAppointments' => $upcomingAppointments,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Client $client)
    {
        $user = Auth::user()->load('businesses');
        $business = $user->businesses->first();

        if (! $business || ! $this->clientRepository->belongsToBusiness($client->id, $business->id)) {
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

        if (! $business || ! $this->clientRepository->belongsToBusiness($client->id, $business->id)) {
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

        if (! $business || ! $this->clientRepository->belongsToBusiness($client->id, $business->id)) {
            return redirect()->route('clients.index');
        }

        $client->delete();

        return redirect()->route('clients.index')->with('success', 'Клиент удален');
    }

    /**
     * Export clients to CSV.
     */
    public function export(Request $request)
    {
        $user = Auth::user()->load('businesses');
        $business = $user->businesses->first();

        if (! $business) {
            return redirect()->route('settings.business.create')
                ->with('info', 'Сначала создайте бизнес.');
        }

        $query = $business->clients();

        // Применяем те же фильтры, что и для index
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $period = $request->get('period', '');
        if ($period) {
            switch ($period) {
                case 'today':
                    $query->whereDate('created_at', today());
                    break;
                case 'week':
                    $query->where('created_at', '>=', now()->subWeek());
                    break;
                case 'month':
                    $query->where('created_at', '>=', now()->subMonth());
                    break;
                case 'year':
                    $query->where('created_at', '>=', now()->subYear());
                    break;
            }
        }

        $activity = $request->get('activity', '');
        if ($activity) {
            switch ($activity) {
                case 'active':
                    $query->whereHas('appointments');
                    break;
                case 'inactive':
                    $query->whereDoesntHave('appointments');
                    break;
            }
        }

        $sort = $request->get('sort', 'created_at');
        $direction = $request->get('direction', 'desc');

        if ($sort === 'name') {
            $query->orderBy('first_name', $direction)
                ->orderBy('last_name', $direction);
        } else {
            $query->orderBy($sort, $direction);
        }

        $clients = $query->get();

        $filename = 'clients_'.now()->format('Y-m-d_H-i-s').'.csv';

        $headers = [
            'Content-type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=$filename",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($clients) {
            $file = fopen('php://output', 'w');

            // Заголовки CSV
            fputcsv($file, ['Имя', 'Фамилия', 'Телефон', 'Email', 'Дата создания']);

            // Данные клиентов
            foreach ($clients as $client) {
                fputcsv($file, [
                    $client->first_name,
                    $client->last_name ?? '',
                    $client->phone,
                    $client->email ?? '',
                    $client->created_at->format('d.m.Y H:i:s'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
