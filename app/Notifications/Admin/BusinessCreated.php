<?php

namespace App\Notifications\Admin;

use App\Models\Business;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BusinessCreated extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public Business $business) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ["mail"];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $business = $this->business;
        $ownerRoleId = \App\Models\BusinessRole::where("slug", "owner")->value(
            "id",
        );
        $owner = $business
            ->users()
            ->wherePivotIn("role_id", [$ownerRoleId])
            ->first();

        return new MailMessage()
            ->subject("Новый бизнес зарегистрирован: " . $business->name)
            ->line("В системе зарегистрирован новый бизнес.")
            ->line("Название: " . $business->name)
            ->line("Владелец: " . ($owner ? $owner->name : "Не указан"))
            ->line("Email владельца: " . ($owner ? $owner->email : "Не указан"))
            ->action(
                "Просмотреть бизнес",
                route("panel.businesses.show", $business),
            )
            ->line("Спасибо за использование нашей системы!");
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            "business_id" => $this->business->id,
            "title" => "Новый бизнес зарегистрирован",
            "message" => 'Бизнес "' . $this->business->name . '" создан',
        ];
    }
}
