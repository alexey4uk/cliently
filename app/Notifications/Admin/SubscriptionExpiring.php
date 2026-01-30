<?php

namespace App\Notifications\Admin;

use App\Models\Subscription;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SubscriptionExpiring extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public Subscription $subscription
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
        $subscription = $this->subscription;
        $user = $subscription->user;
        $business = $user->businesses()->first();
        $plan = $subscription->plan;
        $daysLeft = $subscription->ends_at
            ? $subscription->ends_at->diffInDays(now())
            : null;

        return (new MailMessage)
            ->subject('Подписка истекает: '.($business ? $business->name : 'Не указан'))
            ->line('Подписка бизнеса скоро истечет.')
            ->line('Бизнес: '.($business ? $business->name : 'Не указан'))
            ->line('Тариф: '.($plan ? $plan->name : 'Не указан'))
            ->line('Дней до истечения: '.($daysLeft > 0 ? $daysLeft : 0))
            ->action('Просмотреть бизнес', $business ? route('panel.businesses.show', $business) : route('panel.businesses'))
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
            'subscription_id' => $this->subscription->id,
            'title' => 'Подписка истекает',
            'message' => 'Подписка скоро истечет',
        ];
    }
}
