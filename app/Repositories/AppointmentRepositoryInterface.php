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
}
