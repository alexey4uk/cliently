<?php

namespace App\Notifications;

use App\Models\Business;
use App\Models\BusinessUserInvitation as BusinessUserInvitationModel;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BusinessUserInvitation extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public BusinessUserInvitationModel $invitation,
        public Business $business
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
        $url = route('invite.accept', ['token' => $this->invitation->token]);

        return (new MailMessage)
            ->subject('Приглашение в бизнес '.$this->business->name)
            ->greeting('Здравствуйте!')
            ->line('Вас пригласили присоединиться к бизнесу "'.$this->business->name.'" в роли '.$this->getRoleLabel($this->invitation->businessRole?->slug ?? '').'.')
            ->line('Для активации аккаунта и присоединения к бизнесу перейдите по ссылке ниже:')
            ->action('Принять приглашение', $url)
            ->line('Ссылка действительна до '.$this->invitation->expires_at->format('d.m.Y H:i'))
            ->line('Если вы не ожидали это приглашение, просто проигнорируйте это письмо.');
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
            'invitation_id' => $this->invitation->id,
            'business_id' => $this->business->id,
            'business_name' => $this->business->name,
            'role' => $this->invitation->businessRole?->slug ?? null,
        ];
    }
}
