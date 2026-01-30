<?php

namespace App\Notifications\Telegram;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class Connected extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public User $user
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Telegram аккаунт подключен')
            ->greeting('Здравствуйте, '.$notifiable->name.'!')
            ->line('Ваш Telegram аккаунт успешно подключен к системе.')
            ->line('Теперь вы будете получать уведомления в Telegram в дополнение к email и уведомлениям в системе.')
            ->action('Настройки уведомлений', route('settings.notifications.index'))
            ->line('Спасибо за использование нашей системы!');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'user_id' => $this->user->id,
        ];
    }
}
