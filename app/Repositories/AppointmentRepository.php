<?php

namespace App\Repositories;

use App\Models\Appointment;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Репозиторий для работы с записями
 */
class AppointmentRepository extends BaseRepository implements AppointmentRepositoryInterface
{
    /**
     * Получить модель для репозитория
     *
     * @return Appointment
     */
    public function getModel(): Appointment
    {
        return new Appointment();
    }

    /**
     * Создать новую запись
     *
     * @param array $data
     * @return Appointment
     */
    public function createAppointment(array $data): Appointment
    {
        return $this->create($data);
    }

    /**
     * Найти запись с отношениями
     *
     * @param int $id
     * @param array $relations
     * @return Appointment|null
     */
    public function findWithRelations(int $id, array $relations = ['client', 'service', 'master', 'location']): ?Appointment
    {
        return $this->findWith($id, $relations);
    }

    /**
     * Получить статистику для дашборда бизнеса
     *
     * @param int $businessId
     * @return array
     */
    public function getDashboardStats(int $businessId): array
    {
        $today = Carbon::today();
        $weekAgo = Carbon::now()->subWeek();
        $monthAgo = Carbon::now()->subMonth();

        return [
            'today' => $this->model->where('business_id', $businessId)
                ->where('date', $today->format('Y-m-d'))
                ->where('status', '!=', 'cancelled')
                ->count(),
            'completed_week' => $this->model->where('business_id', $businessId)
                ->where('date', '>=', $weekAgo->format('Y-m-d'))
                ->where('status', 'completed')
                ->count(),
        ];
    }

    /**
     * Получить записи для дашборда бизнеса
     *
     * @param int $businessId
     * @return array
     */
    public function getDashboardAppointments(int $businessId): array
    {
        $today = Carbon::today();
        $currentTime = Carbon::now()->format('H:i');

        // Записи на сегодня
        $todayAppointments = $this->model->where('business_id', $businessId)
            ->where('date', $today->format('Y-m-d'))
            ->whereIn('status', ['confirmed', 'completed'])
            ->with(['client', 'service', 'master', 'location'])
            ->orderBy('time', 'asc')
            ->get();

        // Записи, требующие внимания
        $pendingAppointments = $this->model->where('business_id', $businessId)
            ->where('status', 'pending')
            ->where('date', '>=', $today->format('Y-m-d'))
            ->with(['client', 'service', 'master', 'location'])
            ->orderBy('date', 'asc')
            ->orderBy('time', 'asc')
            ->limit(5)
            ->get();

        // Следующая запись
        $nextAppointment = $todayAppointments
            ->filter(function ($appointment) use ($currentTime) {
                return $appointment->time >= $currentTime && $appointment->status === 'confirmed';
            })
            ->first();

        // Разделяем записи на выполненные и предстоящие
        $completedAppointments = $todayAppointments->where('status', 'completed');
        $upcomingAppointments = $todayAppointments->where('status', 'confirmed');

        // Исключаем следующую запись из основного списка
        $upcomingAppointmentsWithoutNext = $upcomingAppointments->filter(function ($appointment) use ($nextAppointment) {
            return ! $nextAppointment || $appointment->id !== $nextAppointment->id;
        });

        return [
            'today' => $todayAppointments,
            'completed' => $completedAppointments,
            'upcoming' => $upcomingAppointmentsWithoutNext,
            'pending' => $pendingAppointments,
            'next' => $nextAppointment,
            'todayDate' => $today->locale('ru')->isoFormat('D MMMM'),
        ];
    }

    /**
     * Получить записи бизнеса с фильтрами и пагинацией
     *
     * @param int $businessId
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getFilteredForBusiness(int $businessId, array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        $query = $this->model->where('business_id', $businessId)
            ->with(['client', 'service', 'master', 'location']);

        // Фильтр по дате
        if (isset($filters['date']) && $filters['date']) {
            $query->whereDate('date', $filters['date']);
        } elseif (!isset($filters['view']) || $filters['view'] !== 'calendar') {
            // По умолчанию показываем сегодня и будущие записи для таблицы
            $query->whereDate('date', '>=', Carbon::today());
        }

        // Фильтр по статусу
        if (isset($filters['status']) && $filters['status']) {
            $query->where('status', $filters['status']);
        }

        // Фильтр по услуге
        if (isset($filters['service_id']) && $filters['service_id']) {
            $query->where('service_id', $filters['service_id']);
        }

        // Фильтр по мастеру
        if (isset($filters['master_id']) && $filters['master_id']) {
            $query->where('master_id', $filters['master_id']);
        }

        // Поиск
        if (isset($filters['search']) && $filters['search']) {
            $search = $filters['search'];
            $query->whereHas('client', function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            })->orWhereHas('service', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        // Для календаря - сортировка по дате и времени
        if (isset($filters['view']) && $filters['view'] === 'calendar' && isset($filters['month'])) {
            $startOfMonth = Carbon::parse($filters['month'] . '-01')->startOfMonth();
            $endOfMonth = Carbon::parse($filters['month'] . '-01')->endOfMonth();
            $query->whereBetween('date', [$startOfMonth, $endOfMonth])
                ->orderBy('date', 'asc')
                ->orderBy('time', 'asc');
        } else {
            // Сортировка
            $sort = $filters['sort'] ?? 'date';
            $direction = $filters['direction'] ?? 'desc';
            
            if ($sort === 'date') {
                $query->orderBy('date', $direction)
                    ->orderBy('time', $direction);
            } elseif ($sort === 'client') {
                $query->join('clients', 'appointments.client_id', '=', 'clients.id')
                    ->orderBy('clients.first_name', $direction)
                    ->orderBy('clients.last_name', $direction)
                    ->select('appointments.*');
            } elseif ($sort === 'status') {
                $query->orderBy('status', $direction);
            } else {
                $query->orderBy('date', 'desc')
                    ->orderBy('time', 'desc');
            }
        }

        return $query->paginate($perPage)->withQueryString();
    }

    /**
     * Получить записи для календаря
     *
     * @param int $businessId
     * @param string $month
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getForCalendar(int $businessId, string $month)
    {
        $startOfMonth = Carbon::parse($month . '-01')->startOfMonth();
        $endOfMonth = Carbon::parse($month . '-01')->endOfMonth();

        return $this->model->where('business_id', $businessId)
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->with(['client', 'service', 'master', 'location'])
            ->orderBy('date', 'asc')
            ->orderBy('time', 'asc')
            ->get();
    }

    /**
     * Получить все записи бизнеса с фильтрами без пагинации (для экспорта)
     *
     * @param int $businessId
     * @param array $filters
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllFilteredForBusiness(int $businessId, array $filters = [])
    {
        $query = $this->model->where('business_id', $businessId)
            ->with(['client', 'service', 'master', 'location']);

        // Фильтр по дате
        if (isset($filters['date']) && $filters['date']) {
            $query->whereDate('date', $filters['date']);
        } elseif (!isset($filters['view']) || $filters['view'] !== 'calendar') {
            // Для экспорта показываем все записи, не только будущие
        }

        // Фильтр по статусу
        if (isset($filters['status']) && $filters['status']) {
            $query->where('status', $filters['status']);
        }

        // Фильтр по услуге
        if (isset($filters['service_id']) && $filters['service_id']) {
            $query->where('service_id', $filters['service_id']);
        }

        // Фильтр по мастеру
        if (isset($filters['master_id']) && $filters['master_id']) {
            $query->where('master_id', $filters['master_id']);
        }

        // Поиск
        if (isset($filters['search']) && $filters['search']) {
            $search = $filters['search'];
            $query->whereHas('client', function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            })->orWhereHas('service', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        // Для календаря - сортировка по дате и времени
        if (isset($filters['view']) && $filters['view'] === 'calendar' && isset($filters['month'])) {
            $startOfMonth = Carbon::parse($filters['month'] . '-01')->startOfMonth();
            $endOfMonth = Carbon::parse($filters['month'] . '-01')->endOfMonth();
            $query->whereBetween('date', [$startOfMonth, $endOfMonth])
                ->orderBy('date', 'asc')
                ->orderBy('time', 'asc');
        } else {
            $query->orderBy('date', 'desc')
                ->orderBy('time', 'desc');
        }

        return $query->get();
    }

    /**
     * Проверить, принадлежит ли запись бизнесу
     *
     * @param int $appointmentId
     * @param int $businessId
     * @return bool
     */
    public function belongsToBusiness(int $appointmentId, int $businessId): bool
    {
        return $this->model->where('id', $appointmentId)
            ->where('business_id', $businessId)
            ->exists();
    }
}
