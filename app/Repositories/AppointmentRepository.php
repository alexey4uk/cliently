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
     */
    public function getModel(): Appointment
    {
        return new Appointment;
    }

    /**
     * Создать новую запись
     */
    public function createAppointment(array $data): Appointment
    {
        return $this->create($data);
    }

    /**
     * Найти запись с отношениями
     */
    public function findWithRelations(int $id, array $relations = ['client', 'service', 'master', 'location']): ?Appointment
    {
        return $this->findWith($id, $relations);
    }

    /**
     * Получить статистику для дашборда бизнеса
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
     */
    public function getFilteredForBusiness(int $businessId, array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        // 1. Создаем базовый клон запроса с общими фильтрами
        $prepareQuery = function () use ($businessId, $filters) {
            $q = $this->model->where('business_id', $businessId);

            // ВАЖНО: Заменяем whereDate на обычный where (SARGABLE)
            if (! empty($filters['date'])) {
                $q->where('date', $filters['date']);
            } elseif (! isset($filters['view']) || $filters['view'] !== 'calendar') {
                $q->where('date', '>=', now()->format('Y-m-d'));
            }

            if (! empty($filters['status'])) {
                $q->where('status', $filters['status']);
            }

            if (! empty($filters['service_id'])) {
                $q->where('service_id', $filters['service_id']);
            }

            // Фильтр по мастеру (значение 'unassigned' — записи без мастера)
            if (isset($filters['master_id'])) {
                if ($filters['master_id'] === 'unassigned') {
                    $q->whereNull('master_id');
                } elseif ($filters['master_id'] !== '' && $filters['master_id'] !== null) {
                    $q->where('master_id', $filters['master_id']);
                }
            }

            return $q;
        };

        $query = $prepareQuery();

        // 2. Оптимизация ПОИСКА через UNION
        if (! empty($filters['search'])) {
            $search = "%{$filters['search']}%";

            // Поиск по ФИО
            $qClient = $prepareQuery()->whereHas('client', function ($q) use ($search) {
                $q->where('first_name', 'like', $search)->orWhere('last_name', 'like', $search);
            });

            // Поиск по телефону (колонка clients.phone)
            $qClientPhone = $prepareQuery()->whereHas('client', function ($q) use ($search) {
                $q->where('phone', 'like', $search);
            });

            // Поиск по телефону (morph phones, обратная совместимость)
            $qPhone = $prepareQuery()->whereHas('client.phones', function ($q) use ($search) {
                $q->where('phone', 'like', $search);
            });

            // Поиск по услуге
            $qService = $prepareQuery()->whereHas('service', function ($q) use ($search) {
                $q->where('name', 'like', $search);
            });

            // Объединяем результаты. UNION в SQL работает быстрее, чем огромный OR
            $query = $qClient->union($qClientPhone)->union($qPhone)->union($qService);
        }

        // 3. Подгружаем связи (with) в самом конце перед пагинацией
        $query->with(['client', 'service', 'master', 'location']);

        // 4. СОРТИРОВКА
        // Внимание: при UNION сортировка должна быть глобальной в конце
        $direction = $filters['direction'] ?? 'desc';

        // Если поиск активен, сортируем по дате/времени (UNION требует общей сортировки)
        if (! empty($filters['search']) || ($filters['sort'] ?? 'date') === 'date') {
            $query->orderBy('date', $direction)->orderBy('time', $direction);
        } else {
            // Остальные виды сортировок...
            $query->orderBy($filters['sort'] ?? 'date', $direction);
        }

        return $query->paginate($perPage)->withQueryString();
    }

    /**
     * Получить записи для календаря
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getForCalendar(int $businessId, string $month, array $filters = [])
    {
        $startOfMonth = Carbon::parse($month.'-01')->startOfMonth();
        $endOfMonth = Carbon::parse($month.'-01')->endOfMonth();

        $query = $this->model->where('business_id', $businessId)
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->with(['client', 'service', 'master', 'location'])
            ->orderBy('date', 'asc')
            ->orderBy('time', 'asc');

        // Фильтр по мастеру ('unassigned' — без мастера)
        if (isset($filters['master_id'])) {
            if ($filters['master_id'] === 'unassigned') {
                $query->whereNull('master_id');
            } elseif ($filters['master_id'] !== '' && $filters['master_id'] !== null) {
                $query->where('master_id', $filters['master_id']);
            }
        }

        return $query->get();
    }

    /**
     * Получить все записи бизнеса с фильтрами без пагинации (для экспорта)
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllFilteredForBusiness(int $businessId, array $filters = [])
    {
        $query = $this->model->where('business_id', $businessId)
            ->with(['client', 'service', 'master', 'location']);

        // Фильтр по дате
        if (isset($filters['date']) && $filters['date']) {
            $query->whereDate('date', $filters['date']);
        } elseif (! isset($filters['view']) || $filters['view'] !== 'calendar') {
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

        // Фильтр по мастеру ('unassigned' — без мастера)
        if (isset($filters['master_id'])) {
            if ($filters['master_id'] === 'unassigned') {
                $query->whereNull('master_id');
            } elseif ($filters['master_id'] !== '' && $filters['master_id'] !== null) {
                $query->where('master_id', $filters['master_id']);
            }
        }

        // Поиск
        if (isset($filters['search']) && $filters['search']) {
            $search = $filters['search'];
            $query->whereHas('client', function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhereHas('phones', fn ($p) => $p->where('phone', 'like', "%{$search}%"));
            })->orWhereHas('service', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        // Для календаря - сортировка по дате и времени
        if (isset($filters['view']) && $filters['view'] === 'calendar' && isset($filters['month'])) {
            $startOfMonth = Carbon::parse($filters['month'].'-01')->startOfMonth();
            $endOfMonth = Carbon::parse($filters['month'].'-01')->endOfMonth();
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
     */
    public function belongsToBusiness(int $appointmentId, int $businessId): bool
    {
        return $this->model->where('id', $appointmentId)
            ->where('business_id', $businessId)
            ->exists();
    }
}
