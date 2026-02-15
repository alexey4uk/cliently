<?php

namespace App\Notifications\Subscription;

use App\Models\Subscription;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class Renewed extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Subscription $subscription
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $plan = $this->subscription->plan;
        $endsAt = $this->subscription->ends_at ? $this->subscription->ends_at->format('d.m.Y') : 'не указано';

        return (new MailMessage)
            ->subject('Подписка успешно продлена')
            ->greeting('Здравствуйте, '.$notifiable->name.'!')
            ->line('Подписка на тариф «'.$plan->name.'» успешно продлена.')
            ->line('Подписка действует до: '.$endsAt)
            ->action('Управление подпиской', route('subscription.current'))
            ->line('Спасибо за использование нашей системы!');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'subscription_id' => $this->subscription->id,
            'plan_id' => $this->subscription->plan_id,
            'ends_at' => $this->subscription->ends_at?->toIso8601String(),
        ];
    }
}
