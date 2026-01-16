<?php

namespace Database\Factories;

use App\Models\Appointment;
use App\Models\Business;
use App\Models\Client;
use App\Models\Location;
use App\Models\Master;
use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Appointment>
 */
class AppointmentFactory extends Factory
{
    protected $model = Appointment::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'business_id' => Business::factory(),
            'client_id' => Client::factory(),
            'service_id' => Service::factory(),
            'master_id' => Master::factory(),
            'location_id' => Location::factory(),
            'date' => Carbon::today()->addDays(7)->format('Y-m-d'),
            'time' => '10:00',
            'status' => 'pending',
            'notes' => fake()->optional()->text(100),
        ];
    }

    /**
     * Appointment с конкретной датой
     */
    public function withDate(string $date): static
    {
        return $this->state(['date' => $date]);
    }

    /**
     * Appointment с конкретным временем
     */
    public function withTime(string $time): static
    {
        return $this->state(['time' => $time]);
    }

    /**
     * Appointment с конкретным статусом
     */
    public function withStatus(string $status): static
    {
        return $this->state(['status' => $status]);
    }

    /**
     * Appointment с конкретными данными
     */
    public function withData(array $data): static
    {
        return $this->state($data);
    }
}
