<?php

namespace App\Notifications\Subscription;

use App\Models\Plan;
use App\Models\Subscription;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PlanChanged extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Subscription $subscription,
        public Plan $oldPlan,
        public Plan $newPlan
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Тариф подписки изменён')
            ->greeting('Здравствуйте, '.$notifiable->name.'!')
            ->line('Тариф вашей подписки изменён с «'.$this->oldPlan->name.'» на «'.$this->newPlan->name.'».')
            ->action('Управление подпиской', route('subscription.current'))
            ->line('Спасибо за использование нашей системы!');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'subscription_id' => $this->subscription->id,
            'old_plan_id' => $this->oldPlan->id,
            'new_plan_id' => $this->newPlan->id,
        ];
    }
}
