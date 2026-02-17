<?php

namespace App\Notifications;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ClientNew extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Appointment $appointment
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $appointment = $this->appointment;
        $client = $appointment->client;
        $service = $appointment->service;

        $clientName = $client ? trim(($client->first_name ?? '').' '.($client->last_name ?? '')) : 'Клиент удалён';

        return (new MailMessage)
            ->subject('Новый клиент: '.$clientName)
            ->line('Новый клиент записался на услугу.')
            ->line('Клиент: '.$clientName)
            ->line('Услуга: '.($service->name ?? ''))
            ->line('Дата: '.$appointment->date->format('d.m.Y').', время: '.$appointment->time)
            ->action('Открыть запись', route('appointments.show', $appointment))
            ->line('Спасибо за использование нашей системы!');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'appointment_id' => $this->appointment->id,
            'client_id' => $this->appointment->client_id, // Может быть null, если клиент удалён
            'title' => 'Новый клиент',
            'message' => 'Записался на услугу',
        ];
    }
}
