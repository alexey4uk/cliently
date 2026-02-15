<?php

namespace App\Notifications;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AppointmentCreated extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public Appointment $appointment
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $appointment = $this->appointment;
        $client = $appointment->client;
        $service = $appointment->service;

        return (new MailMessage)
            ->subject('Новая запись: '.($service->name ?? 'Услуга'))
            ->line('Создана новая запись на услугу.')
            ->line('Клиент: '.($client->first_name ?? '').' '.($client->last_name ?? ''))
            ->line('Услуга: '.($service->name ?? ''))
            ->line('Дата: '.$appointment->date->format('d.m.Y'))
            ->line('Время: '.$appointment->time)
            ->action('Просмотреть запись', route('appointments.show', $appointment))
            ->line('Спасибо за использование нашей системы!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'appointment_id' => $this->appointment->id,
            'title' => 'Новая запись',
            'message' => 'Создана новая запись',
        ];
    }
}
