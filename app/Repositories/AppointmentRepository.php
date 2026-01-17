<?php

namespace App\Repositories;

use App\Models\Appointment;

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
}
