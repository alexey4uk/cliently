<?php

namespace App\Http\Controllers;

use App\Http\Requests\ClientRequest;
use App\Models\Client;
use App\Models\Country;
use App\Repositories\ClientRepositoryInterface;
use App\Services\BusinessRolePermissionService;
use App\Services\SubscriptionService;
use App\Traits\HasOwnDataFiltering;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClientController extends Controller
{
    use HasOwnDataFiltering;

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
        $business = $this->getCurrentBusiness();

        if (! $business) {
            return redirect()->route('welcome')
                ->with('info', 'Сначала создайте бизнес или примите приглашение.');
        }

        $query = $business->clients();

        // Применяем фильтр "только свои данные" если нужно
        $role = $this->getCurrentBusinessRole();
        if ($role) {
            $this->applyOwnDataFilterForClients($query, $business, $role->id, 'client.clients.view');
        }

        // Поиск
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereFullText(['first_name', 'last_name'], $search)
                    ->orWhereHas('phones', fn ($p) => $p->where('phone', 'like', "{$search}%"))
                    ->orWhere('email', 'like', "{$search}%");
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

        $permissionService = app(BusinessRolePermissionService::class);
        $canViewClients = $role && $permissionService->hasPermission($role->id, 'client.clients.view');
        $canExportClients = $role && $permissionService->hasPermission($role->id, 'client.clients.export');
        $canCreateClients = $role && $permissionService->hasPermission($role->id, 'client.clients.create');
        $canUpdateClients = $role && $permissionService->hasPermission($role->id, 'client.clients.update');
        $canDeleteClients = $role && $permissionService->hasPermission($role->id, 'client.clients.delete');
        $hasAnyClientAction = $canViewClients || $canUpdateClients || $canDeleteClients;
        $canCreateClient = $canCreateClients && app(SubscriptionService::class)->canCreateClient(Auth::user());

        return view('clients.index', [
            'business' => $business,
            'clients' => $clients,
            'search' => $request->get('search', ''),
            'sort' => $sort,
            'direction' => $direction,
            'period' => $period,
            'activity' => $activity,
            'perPage' => $perPage,
            'canViewClients' => $canViewClients,
            'canExportClients' => $canExportClients,
            'canCreateClients' => $canCreateClients,
            'canUpdateClients' => $canUpdateClients,
            'canDeleteClients' => $canDeleteClients,
            'canCreateClient' => $canCreateClient,
            'hasAnyClientAction' => $hasAnyClientAction,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $business = $this->getCurrentBusiness();

        if (! $business) {
            return redirect()->route('welcome')
                ->with('info', 'Сначала создайте бизнес или примите приглашение.');
        }

        return view('clients.create', [
            'business' => $business,
            'countries' => Country::getForPhoneSelect(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ClientRequest $request)
    {
        $business = $this->getCurrentBusiness();

        if (! $business) {
            return redirect()->route('welcome')
                ->with('info', 'Сначала создайте бизнес или примите приглашение.');
        }

        $user = Auth::user();

        // Проверка лимита клиентов
        $subscriptionService = app(SubscriptionService::class);
        if (! $subscriptionService->canCreateClient($user)) {
            \App\Services\AdminNotificationService::notifySubscriptionLimitExceededIfNotThrottled($business, 'max_clients');

            return redirect()->back()
                ->withInput()
                ->with('error', \App\Services\SubscriptionService::planLimitErrorMessage());
        }

        $validated = $request->validated();
        $phoneCountryId = (int) $validated['phone_country_id'];
        $phoneE164 = $validated['phone'];

        $client = $this->clientRepository->create([
            'business_id' => $business->id,
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'] ?? null,
            'email' => $validated['email'] ?? null,
        ]);

        $client->phones()->create([
            'country_id' => $phoneCountryId,
            'phone' => $phoneE164,
            'type' => 'primary',
        ]);

        return redirect()->route('clients.index')->with('success', 'Клиент добавлен');
    }

    /**
     * Display the specified resource.
     */
    public function show(Client $client)
    {
        $business = $this->getCurrentBusiness();

        if (! $business || ! $this->clientRepository->belongsToBusiness($client->id, $business->id)) {
            return redirect()->route('clients.index');
        }

        // Проверяем право на просмотр этого конкретного клиента
        $role = $this->getCurrentBusinessRole();
        if ($role && ! $this->canViewClient($business, $role->id, 'client.clients.view', $client->id)) {
            return redirect()->route('clients.index')
                ->with('error', 'У вас нет доступа к этому клиенту.');
        }

        // Статистика записей клиента
        $totalAppointments = $client->appointments()->count();
        $completedAppointments = $client->appointments()->where('status', 'completed')->count();
        $upcomingAppointments = $client->appointments()->where('date', '>=', today())->whereIn('status', ['confirmed', 'pending'])->count();

        // Общая сумма потраченных средств
        $totalSpent = $client->appointments()
            ->where('status', 'completed')
            ->get()
            ->sum(function ($appointment) {
                return $appointment->final_price ?? 0;
            });

        // Средний чек
        $avgCheck = $completedAppointments > 0 ? round($totalSpent / $completedAppointments, 0) : 0;

        // Количество отмененных записей
        $cancelledAppointments = $client->appointments()->where('status', 'cancelled')->count();

        // Предстоящие записи
        $upcomingAppointmentsList = $client->appointments()
            ->where('date', '>=', today())
            ->whereIn('status', ['confirmed', 'pending'])
            ->with(['service', 'master'])
            ->orderBy('date')
            ->orderBy('time')
            ->limit(5)
            ->get();

        // История записей (завершенные)
        $appointmentHistory = $client->appointments()
            ->where('status', 'completed')
            ->with(['service', 'master'])
            ->orderBy('date', 'desc')
            ->orderBy('time', 'desc')
            ->limit(10)
            ->get();

        $permissionService = app(BusinessRolePermissionService::class);
        $canCreateAppointments = $role && $permissionService->hasPermission($role->id, 'client.appointments.create');
        $canUpdateClients = $role && $permissionService->hasPermission($role->id, 'client.clients.update');
        $canDeleteClients = $role && $permissionService->hasPermission($role->id, 'client.clients.delete');

        return view('clients.show', [
            'business' => $business,
            'client' => $client,
            'totalAppointments' => $totalAppointments,
            'completedAppointments' => $completedAppointments,
            'upcomingAppointments' => $upcomingAppointments,
            'totalSpent' => $totalSpent,
            'avgCheck' => $avgCheck,
            'cancelledAppointments' => $cancelledAppointments,
            'upcomingAppointmentsList' => $upcomingAppointmentsList,
            'appointmentHistory' => $appointmentHistory,
            'canCreateAppointments' => $canCreateAppointments,
            'canUpdateClients' => $canUpdateClients,
            'canDeleteClients' => $canDeleteClients,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Client $client)
    {
        $business = $this->getCurrentBusiness();

        if (! $business || ! $this->clientRepository->belongsToBusiness($client->id, $business->id)) {
            return redirect()->route('clients.index');
        }

        // Проверяем право на просмотр этого конкретного клиента
        $role = $this->getCurrentBusinessRole();
        if ($role && ! $this->canViewClient($business, $role->id, 'client.clients.view', $client->id)) {
            return redirect()->route('clients.index')
                ->with('error', 'У вас нет доступа к этому клиенту.');
        }

        return view('clients.edit', [
            'business' => $business,
            'client' => $client,
            'countries' => Country::getForPhoneSelect(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ClientRequest $request, Client $client)
    {
        $business = $this->getCurrentBusiness();

        if (! $business || ! $this->clientRepository->belongsToBusiness($client->id, $business->id)) {
            return redirect()->route('clients.index');
        }

        // Проверяем право на просмотр этого конкретного клиента
        $role = $this->getCurrentBusinessRole();
        if ($role && ! $this->canViewClient($business, $role->id, 'client.clients.view', $client->id)) {
            return redirect()->route('clients.index')
                ->with('error', 'У вас нет доступа к этому клиенту.');
        }

        $validated = $request->validated();
        $phoneCountryId = (int) $validated['phone_country_id'];
        $phoneE164 = $validated['phone'];

        $client->update([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'] ?? null,
            'email' => $validated['email'] ?? null,
        ]);

        $primary = $client->primaryPhone;
        if ($primary) {
            $primary->update(['country_id' => $phoneCountryId, 'phone' => $phoneE164]);
        } else {
            $client->phones()->create([
                'country_id' => $phoneCountryId,
                'phone' => $phoneE164,
                'type' => 'primary',
            ]);
        }

        return redirect()->route('clients.index')->with('success', 'Клиент обновлен');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Client $client)
    {
        $business = $this->getCurrentBusiness();

        if (! $business || ! $this->clientRepository->belongsToBusiness($client->id, $business->id)) {
            return redirect()->route('clients.index');
        }

        // Проверяем право на просмотр этого конкретного клиента
        $role = $this->getCurrentBusinessRole();
        if ($role && ! $this->canViewClient($business, $role->id, 'client.clients.view', $client->id)) {
            return redirect()->route('clients.index')
                ->with('error', 'У вас нет доступа к этому клиенту.');
        }

        $client->delete();

        // Уменьшать usage не нужно, т.к. для клиентов считаем напрямую из БД

        return redirect()->route('clients.index')->with('success', 'Клиент удален');
    }

    /**
     * Export clients to CSV.
     */
    public function export(Request $request)
    {
        $business = $this->getCurrentBusiness();

        if (! $business) {
            return redirect()->route('welcome')
                ->with('info', 'Сначала создайте бизнес или примите приглашение.');
        }

        $query = $business->clients();

        // Применяем фильтр "только свои данные" если нужно
        $role = $this->getCurrentBusinessRole();
        if ($role) {
            $this->applyOwnDataFilterForClients($query, $business, $role->id, 'client.clients.view');
        }

        // Применяем те же фильтры, что и для index
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhereHas('phones', fn ($p) => $p->where('phone', 'like', "%{$search}%"))
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

        $clients = $query->with('primaryPhone')->get();

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
