<?php

namespace App\Notifications;

use App\Models\Business;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BusinessUserCreated extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public Business $business,
        public string $role
    ) {
        //
    }

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
        $loginUrl = route('login');

        return (new MailMessage)
            ->subject('Аккаунт создан для бизнеса '.$this->business->name)
            ->greeting('Здравствуйте!')
            ->line('Для вас был создан аккаунт в системе для работы с бизнесом "'.$this->business->name.'" в роли '.$this->role.'.')
            ->line('Для входа в систему используйте ваш email и временный пароль, который был предоставлен администратором.')
            ->line('При первом входе вам необходимо будет сменить пароль.')
            ->action('Войти в систему', $loginUrl)
            ->line('Если у вас возникли вопросы, обратитесь к администратору бизнеса.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'business_id' => $this->business->id,
            'business_name' => $this->business->name,
            'role' => $this->role,
        ];
    }
}
