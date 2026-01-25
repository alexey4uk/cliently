<?php

namespace App\Notifications\Business;

use App\Models\Business;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserRemoved extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Business $business,
        public User $removedBy,
        public ?User $removedUser = null
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        // Если уведомление отправляется удалённому пользователю
        if ($this->removedUser && $notifiable->id === $this->removedUser->id) {
            return (new MailMessage)
                ->subject('Вы были удалены из бизнеса '.$this->business->name)
                ->greeting('Здравствуйте, '.$this->removedUser->name.'!')
                ->line('Вы были удалены из бизнеса «'.$this->business->name.'» пользователем '.$this->removedBy->name.'.')
                ->line('Если вы считаете, что это произошло по ошибке, свяжитесь с администратором бизнеса.')
                ->line('Спасибо за использование нашей системы!');
        }

        // Если уведомление отправляется администратору
        $removedUserName = $this->removedUser ? $this->removedUser->name.' ('.$this->removedUser->email.')' : 'пользователь';
        
        return (new MailMessage)
            ->subject('Пользователь удалён из бизнеса '.$this->business->name)
            ->greeting('Здравствуйте!')
            ->line('Пользователь '.$removedUserName.' был удалён из бизнеса «'.$this->business->name.'» пользователем '.$this->removedBy->name.'.')
            ->action('Управление пользователями', route('settings.users.index'))
            ->line('Спасибо за использование нашей системы!');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'business_id' => $this->business->id,
            'removed_user_id' => $this->removedUser?->id,
            'removed_by_id' => $this->removedBy->id,
        ];
    }
}
