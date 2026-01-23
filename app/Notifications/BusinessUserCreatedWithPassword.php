<?php

namespace App\Notifications;

use App\Models\Business;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BusinessUserCreatedWithPassword extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public Business $business,
        public string $role,
        public string $temporaryPassword
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
            ->subject('Аккаунт создан для бизнеса ' . $this->business->name)
            ->greeting('Здравствуйте!')
            ->line('Для вас был создан аккаунт в системе для работы с бизнесом "' . $this->business->name . '" в роли ' . $this->getRoleLabel($this->role) . '.')
            ->line('Для входа в систему используйте следующие данные:')
            ->line('**Email:** ' . $notifiable->email)
            ->line('**Временный пароль:** ' . $this->temporaryPassword)
            ->line('При первом входе вам необходимо будет сменить пароль.')
            ->action('Войти в систему', $loginUrl)
            ->line('Если у вас возникли вопросы, обратитесь к администратору бизнеса.')
            ->line('**Внимание:** Сохраните этот пароль или измените его сразу после входа в систему.');
    }

    /**
     * Get role label in Russian.
     */
    private function getRoleLabel(string $role): string
    {
        return match ($role) {
            'owner' => 'владельца',
            'admin' => 'администратора',
            'master' => 'мастера',
            default => $role,
        };
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
            'temporary_password' => $this->temporaryPassword,
        ];
    }
}