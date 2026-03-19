<?php

namespace App\Services\Appointment;

use App\Repositories\AppointmentRepositoryInterface;

class AppointmentService
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        protected AppointmentRepositoryInterface $appointmentRepository
    ) {}

    public function makeLink(int $appointmentId)
    {
        $appointment = $this->appointmentRepository->findOrFail($appointmentId);

        return env('APP_URL')."/a/{$appointment->token}";
    }
}
