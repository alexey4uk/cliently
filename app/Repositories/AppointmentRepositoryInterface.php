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
     */
    public function createAppointment(array $data): Appointment;

    /**
     * Найти запись с отношениями
     */
    public function findWithRelations(int $id, array $relations = ['client', 'service', 'master', 'location']): ?Appointment;

    /**
     * Получить статистику для дашборда бизнеса
     */
    public function getDashboardStats(int $businessId): array;

    /**
     * Получить записи для дашборда бизнеса
     */
    public function getDashboardAppointments(int $businessId): array;

    /**
     * Получить записи бизнеса с фильтрами и пагинацией
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getFilteredForBusiness(int $businessId, array $filters = [], int $perPage = 20);

    /**
     * Получить все записи бизнеса с фильтрами без пагинации (для экспорта)
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllFilteredForBusiness(int $businessId, array $filters = []);

    /**
     * Получить записи для календаря
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getForCalendar(int $businessId, string $month);

    /**
     * Проверить, принадлежит ли запись бизнесу
     */
    public function belongsToBusiness(int $appointmentId, int $businessId): bool;
}
