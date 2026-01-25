<?php

namespace App\Notifications\Telegram;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class Disconnected extends Notification implements ShouldQueue
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
            ->subject('Telegram аккаунт отключен')
            ->greeting('Здравствуйте, '.$notifiable->name.'!')
            ->line('Ваш Telegram аккаунт отключен от системы.')
            ->line('Вы больше не будете получать уведомления в Telegram.')
            ->line('Уведомления по email и в системе продолжают работать.')
            ->action('Настройки уведомлений', route('settings.notifications.index'))
            ->line('Если вы хотите снова подключить Telegram, перейдите в настройки уведомлений.')
            ->line('Спасибо за использование нашей системы!');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'user_id' => $this->user->id,
        ];
    }
}
