<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Repositories\BusinessRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        $perPage = min((int) request('per_page', 20), 100); // ВАЖНО: максимум 100 записей
        $statusFilter = request('status', '');
        $businessFilter = request('business_id', '');
        $dateFilter = request('date', '');

        // ОПТИМИЗИРОВАНО: Используем JOIN вместо whereHas для поиска
        $query = Appointment::query();

        // ОПТИМИЗИРОВАННЫЙ ПОИСК: Подзапросы с FULLTEXT индексами
        if ($search) {
            // Добавляем * для частичного совпадения (как LIKE "%search%")
            $searchTerm = $search . '*';
            
            // Находим ID подходящих записей через подзапросы (БЫСТРО!)
            $clientIds = DB::table('clients')
                ->whereRaw("MATCH(first_name, last_name) AGAINST(? IN BOOLEAN MODE)", [$searchTerm])
                ->pluck('id');
            
            $serviceIds = DB::table('services')
                ->whereRaw("MATCH(name) AGAINST(? IN BOOLEAN MODE)", [$searchTerm])
                ->pluck('id');
            
            $masterIds = DB::table('masters')
                ->whereRaw("MATCH(first_name, last_name) AGAINST(? IN BOOLEAN MODE)", [$searchTerm])
                ->pluck('id');
            
            // Фильтруем appointments по найденным ID
            $query->where(function ($q) use ($clientIds, $serviceIds, $masterIds) {
                if ($clientIds->isNotEmpty()) {
                    $q->orWhereIn('client_id', $clientIds);
                }
                if ($serviceIds->isNotEmpty()) {
                    $q->orWhereIn('service_id', $serviceIds);
                }
                if ($masterIds->isNotEmpty()) {
                    $q->orWhereIn('master_id', $masterIds);
                }
            })->limit(1000); // Ограничение результатов поиска
        }

        // Eager loading для отображения (после фильтрации)
        // ВАЖНО: client.primaryPhone для избежания N+1 при отображении телефонов
        $query->with(['client.primaryPhone', 'master', 'service', 'location', 'business']);

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
        $needsDistinct = false; // Флаг для distinct (только при JOIN)
        $allowedSorts = ['date', 'client', 'service', 'master', 'status'];
        
        if (in_array($sort, $allowedSorts)) {
            if ($sort === 'client') {
                $query->join('clients', 'appointments.client_id', '=', 'clients.id')
                    ->orderByRaw("CONCAT(COALESCE(clients.first_name, ''), ' ', COALESCE(clients.last_name, '')) {$direction}")
                    ->select('appointments.*');
                $needsDistinct = true;
            } elseif ($sort === 'service') {
                $query->join('services', 'appointments.service_id', '=', 'services.id')
                    ->orderBy('services.name', $direction)
                    ->select('appointments.*');
                $needsDistinct = true;
            } elseif ($sort === 'master') {
                $query->leftJoin('masters', 'appointments.master_id', '=', 'masters.id')
                    ->orderByRaw("CONCAT(COALESCE(masters.first_name, ''), ' ', COALESCE(masters.last_name, '')) {$direction}")
                    ->select('appointments.*');
                $needsDistinct = true;
            } else {
                $query->orderByRaw("CONCAT(date, ' ', time) {$direction}");
            }
        } else {
            $query->orderByRaw("CONCAT(date, ' ', time) desc");
        }

        // distinct() только когда есть JOIN (избегаем дубликатов)
        if ($needsDistinct) {
            $query->distinct();
        }

        // ОПТИМИЗАЦИЯ: simplePaginate ВСЕГДА (без медленного COUNT)
        $appointments = $query->simplePaginate($perPage)->withQueryString();

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
