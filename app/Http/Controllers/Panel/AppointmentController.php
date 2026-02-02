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
        // 1. Собираем параметры
        $search = request('search', '');
        $sort = request('sort', 'date');
        $direction = strtolower(request('direction')) === 'asc' ? 'asc' : 'desc';
        $perPage = min((int) request('per_page', 20), 100);

        $statusFilter = request('status', '');
        $businessFilter = request('business_id', '');
        $dateFilter = request('date', '');

        $query = Appointment::query();

        if ($search) {
            // Разбиваем поисковый запрос на отдельные слова
            $searchWords = explode(' ', trim($search));
            $hasAnyResults = false;

            $query->where(function ($q) use ($searchWords, &$hasAnyResults) {
                foreach ($searchWords as $word) {
                    if (!empty($word)) {
                        $wordLike = '%' . mb_strtolower($word) . '%';

                        // Поиск по клиентам (имя, фамилия, телефон, полное имя)
                        $clientIds = DB::table('clients')
                            ->where(function ($clientQuery) use ($wordLike) {
                                $clientQuery->where('first_name', 'like', $wordLike)
                                    ->orWhere('last_name', 'like', $wordLike)
                                    ->orWhere('phone', 'like', $wordLike)
                                    ->orWhereRaw("LOWER(CONCAT(first_name, ' ', last_name)) LIKE ?", [$wordLike]);
                            })
                            ->pluck('id');

                        if ($clientIds->isNotEmpty()) {
                            $hasAnyResults = true;
                            $q->orWhereIn('client_id', $clientIds);
                        }

                        // Поиск по услугам
                        $serviceIds = DB::table('services')
                            ->where('name', 'like', $wordLike)
                            ->pluck('id');

                        if ($serviceIds->isNotEmpty()) {
                            $hasAnyResults = true;
                            $q->orWhereIn('service_id', $serviceIds);
                        }

                        // Поиск по мастерам
                        $masterIds = DB::table('masters')
                            ->where(function ($masterQuery) use ($wordLike) {
                                $masterQuery->where('first_name', 'like', $wordLike)
                                    ->orWhere('last_name', 'like', $wordLike)
                                    ->orWhereRaw("LOWER(CONCAT(first_name, ' ', last_name)) LIKE ?", [$wordLike]);
                            })
                            ->pluck('id');

                        if ($masterIds->isNotEmpty()) {
                            $hasAnyResults = true;
                            $q->orWhereIn('master_id', $masterIds);
                        }
                    }
                }

                // Если ни одно слово не нашло результатов, возвращаем пустой результат
                if (!$hasAnyResults) {
                    $q->whereRaw('1=0');
                }
            });
        }

        // 3. ЖАДНАЯ ЗАГРУЗКА (УБРАЛИ ЛИШНИЕ СВЯЗИ)
        $query->with(['client', 'master', 'service', 'location', 'business']);

        // 4. ФИЛЬТРЫ (SARGABLE - используют индексы напрямую)
        if ($statusFilter) {
            $query->where('status', $statusFilter);
        }
        if ($businessFilter) {
            $query->where('business_id', $businessFilter);
        }
        if ($dateFilter) {
            // Используем обычный where вместо whereDate для скорости
            $query->where('date', $dateFilter);
        }

        // 5. ОПТИМИЗИРОВАННАЯ СОРТИРОВКА (БЕЗ CONCAT)
        $allowedSorts = ['date', 'client', 'service', 'master', 'status'];
        $sort = in_array($sort, $allowedSorts) ? $sort : 'date';

        if ($sort === 'client') {
            $query->join('clients', 'appointments.client_id', '=', 'clients.id')
                ->orderBy('clients.first_name', $direction)
                ->orderBy('clients.last_name', $direction)
                ->select('appointments.*');
        } elseif ($sort === 'service') {
            $query->join('services', 'appointments.service_id', '=', 'services.id')
                ->orderBy('services.name', $direction)
                ->select('appointments.*');
        } elseif ($sort === 'master') {
            $query->leftJoin('masters', 'appointments.master_id', '=', 'masters.id')
                ->orderBy('masters.first_name', $direction)
                ->orderBy('masters.last_name', $direction)
                ->select('appointments.*');
        } elseif ($sort === 'status') {
            $query->orderBy('status', $direction);
        } else {
            // ГЛАВНЫЙ ФИКС: Сортировка по индексу idx_app_date_time_sort
            $query->orderBy('date', $direction)->orderBy('time', $direction);
        }

        // 6. ПАГИНАЦИЯ (simplePaginate не делает тяжелый COUNT(*))
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
