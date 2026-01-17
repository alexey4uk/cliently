<?php

namespace App\Repositories;

use App\Models\Appointment;

/**
 * Интерфейс для репозитория Appointment
 */
interface AppointmentRepositoryInterface extends RepositoryInterface
{
    /**
     * Создать новую запись
     *
     * @param array $data
     * @return Appointment
     */
    public function createAppointment(array $data): Appointment;

    /**
     * Найти запись с отношениями
     *
     * @param int $id
     * @param array $relations
     * @return Appointment|null
     */
    public function findWithRelations(int $id, array $relations = ['client', 'service', 'master', 'location']): ?Appointment;

    /**
     * Получить статистику для дашборда бизнеса
     *
     * @param int $businessId
     * @return array
     */
    public function getDashboardStats(int $businessId): array;

    /**
     * Получить записи для дашборда бизнеса
     *
     * @param int $businessId
     * @return array
     */
    public function getDashboardAppointments(int $businessId): array;

    /**
     * Получить записи бизнеса с фильтрами и пагинацией
     *
     * @param int $businessId
     * @param array $filters
     * @param int $perPage
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getFilteredForBusiness(int $businessId, array $filters = [], int $perPage = 20);

    /**
     * Получить записи для календаря
     *
     * @param int $businessId
     * @param string $month
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getForCalendar(int $businessId, string $month);

    /**
     * Проверить, принадлежит ли запись бизнесу
     *
     * @param int $appointmentId
     * @param int $businessId
     * @return bool
     */
    public function belongsToBusiness(int $appointmentId, int $businessId): bool;
}
