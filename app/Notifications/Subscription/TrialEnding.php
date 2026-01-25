<?php

namespace App\Notifications\Subscription;

use App\Models\Subscription;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TrialEnding extends Notification implements ShouldQueue
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
        $trialEndsAt = $this->subscription->trial_ends_at ? $this->subscription->trial_ends_at->format('d.m.Y H:i') : 'не указано';
        $daysLeft = $this->subscription->trial_ends_at ? now()->diffInDays($this->subscription->trial_ends_at, false) : 0;

        return (new MailMessage)
            ->subject('Пробный период заканчивается')
            ->greeting('Здравствуйте, '.$notifiable->name.'!')
            ->line('Пробный период для тарифа «'.$plan->name.'» заканчивается '.$trialEndsAt.'.')
            ->line('Осталось дней: '.$daysLeft)
            ->action('Оформить подписку', route('subscription.index'))
            ->line('Чтобы продолжить использование всех функций после окончания пробного периода, необходимо оформить подписку.')
            ->line('Спасибо за использование нашей системы!');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'subscription_id' => $this->subscription->id,
            'plan_id' => $this->subscription->plan_id,
            'trial_ends_at' => $this->subscription->trial_ends_at?->toIso8601String(),
            'days_left' => $this->subscription->trial_ends_at ? now()->diffInDays($this->subscription->trial_ends_at, false) : 0,
        ];
    }
}
