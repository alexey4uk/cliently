<?php

namespace App\Notifications\Business;

use App\Models\Business;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserRoleChanged extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Business $business,
        public string $oldRoleName,
        public string $newRoleName,
        public User $changedBy,
        public ?User $user = null
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        // Если уведомление отправляется пользователю, чья роль изменилась
        if ($this->user && $notifiable->id === $this->user->id) {
            return (new MailMessage)
                ->subject('Изменена ваша роль в бизнесе '.$this->business->name)
                ->greeting('Здравствуйте, '.$this->user->name.'!')
                ->line('Ваша роль в бизнесе «'.$this->business->name.'» изменена с «'.$this->oldRoleName.'» на «'.$this->newRoleName.'» пользователем '.$this->changedBy->name.'.')
                ->action('Перейти в панель управления', route('dashboard'))
                ->line('Спасибо за использование нашей системы!');
        }

        // Если уведомление отправляется администратору
        $userName = $this->user ? $this->user->name.' ('.$this->user->email.')' : 'пользователь';
        
        return (new MailMessage)
            ->subject('Изменена роль пользователя в бизнесе '.$this->business->name)
            ->greeting('Здравствуйте!')
            ->line('Роль пользователя '.$userName.' в бизнесе «'.$this->business->name.'» изменена с «'.$this->oldRoleName.'» на «'.$this->newRoleName.'» пользователем '.$this->changedBy->name.'.')
            ->action('Управление пользователями', route('settings.users.index'))
            ->line('Спасибо за использование нашей системы!');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'business_id' => $this->business->id,
            'user_id' => $this->user?->id,
            'old_role' => $this->oldRoleName,
            'new_role' => $this->newRoleName,
            'changed_by_id' => $this->changedBy->id,
        ];
    }
}
