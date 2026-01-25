<?php

namespace App\Notifications\Business;

use App\Models\Business;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserJoined extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Business $business,
        public User $joinedUser,
        public string $roleName
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        // Если уведомление отправляется самому присоединившемуся пользователю
        if ($notifiable->id === $this->joinedUser->id) {
            return (new MailMessage)
                ->subject('Добро пожаловать в бизнес '.$this->business->name)
                ->greeting('Здравствуйте, '.$this->joinedUser->name.'!')
                ->line('Вы успешно присоединились к бизнесу «'.$this->business->name.'» с ролью '.$this->roleName.'.')
                ->action('Перейти в панель управления', route('dashboard'))
                ->line('Спасибо за использование нашей системы!');
        }

        // Если уведомление отправляется администратору
        return (new MailMessage)
            ->subject('Пользователь присоединился к бизнесу '.$this->business->name)
            ->greeting('Здравствуйте!')
            ->line('Пользователь '.$this->joinedUser->name.' ('.$this->joinedUser->email.') присоединился к бизнесу «'.$this->business->name.'» с ролью '.$this->roleName.'.')
            ->action('Управление пользователями', route('settings.users.index'))
            ->line('Спасибо за использование нашей системы!');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'business_id' => $this->business->id,
            'joined_user_id' => $this->joinedUser->id,
            'role_name' => $this->roleName,
        ];
    }
}
