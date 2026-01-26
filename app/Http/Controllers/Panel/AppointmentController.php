<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Repositories\BusinessRepositoryInterface;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    protected BusinessRepositoryInterface $businessRepository;

    public function __construct(BusinessRepositoryInterface $businessRepository)
    {
        $this->businessRepository = $businessRepository;
    }

    /**
     * Display a listing of appointments.
     */
    public function index()
    {
        $search = request('search', '');
        $sort = request('sort', 'date');
        $direction = request('direction', 'desc');
        $perPage = request('per_page', 20);
        $statusFilter = request('status', '');
        $businessFilter = request('business_id', '');
        $dateFilter = request('date', '');

        $query = Appointment::with(['client', 'master', 'service', 'location', 'business']);

        // Поиск
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('client', function ($clientQuery) use ($search) {
                    $clientQuery->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhereRaw("CONCAT(COALESCE(first_name, ''), ' ', COALESCE(last_name, '')) LIKE ?", ["%{$search}%"]);
                })
                    ->orWhereHas('service', function ($serviceQuery) use ($search) {
                        $serviceQuery->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('master', function ($masterQuery) use ($search) {
                        $masterQuery->where('name', 'like', "%{$search}%");
                    });
            });
        }

        // Фильтр по статусу
        if ($statusFilter) {
            $query->where('status', $statusFilter);
        }

        // Фильтр по бизнесу
        if ($businessFilter) {
            $query->where('business_id', $businessFilter);
        }

        // Фильтр по дате
        if ($dateFilter) {
            $query->where('date', $dateFilter);
        }

        // Сортировка
        $allowedSorts = ['date', 'client', 'service', 'master', 'status'];
        if (in_array($sort, $allowedSorts)) {
            if ($sort === 'client') {
                $query->join('clients', 'appointments.client_id', '=', 'clients.id')
                    ->orderByRaw("CONCAT(COALESCE(clients.first_name, ''), ' ', COALESCE(clients.last_name, '')) {$direction}")
                    ->select('appointments.*');
            } elseif ($sort === 'service') {
                $query->join('services', 'appointments.service_id', '=', 'services.id')
                    ->orderBy('services.name', $direction)
                    ->select('appointments.*');
            } elseif ($sort === 'master') {
                $query->leftJoin('masters', 'appointments.master_id', '=', 'masters.id')
                    ->orderBy('masters.name', $direction)
                    ->select('appointments.*');
            } else {
                $query->orderByRaw("CONCAT(date, ' ', time) {$direction}");
            }
        } else {
            $query->orderByRaw("CONCAT(date, ' ', time) desc");
        }

        $appointments = $query->paginate($perPage)->withQueryString();

        // Получаем список бизнесов для фильтра
        $businesses = $this->businessRepository->getAllForFilter();

        return view('panel.appointments.index', compact(
            'appointments',
            'search',
            'sort',
            'direction',
            'perPage',
            'statusFilter',
            'businessFilter',
            'dateFilter',
            'businesses'
        ));
    }

    /**
     * Show the form for editing the specified appointment.
     */
    public function edit(Appointment $appointment)
    {
        $businesses = $this->businessRepository->getAllForFilter();

        return view('panel.appointments.edit', compact('appointment', 'businesses'));
    }

    /**
     * Update the specified appointment in storage.
     */
    public function update(Request $request, Appointment $appointment)
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,completed,cancelled',
            'notes' => 'nullable|string|max:1000',
        ]);

        $appointment->update($request->only([
            'status',
            'notes',
        ]));

        return redirect()->route('panel.appointments')->with('success', 'Запись обновлена успешно');
    }

    /**
     * Remove the specified appointment from storage.
     */
    public function destroy(Appointment $appointment)
    {
        // Проверяем, что запись не в прошлом
        if ($appointment->dateTime->isPast() && $appointment->status === 'confirmed') {
            return redirect()->route('panel.appointments')->with('error', 'Нельзя удалить прошедшую подтвержденную запись');
        }

        $appointment->delete();

        return redirect()->route('panel.appointments')->with('success', 'Запись удалена успешно');
    }
}
