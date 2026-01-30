<?php

namespace App\Notifications\Business;

use App\Models\BusinessUserInvitation;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserInvited extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public BusinessUserInvitation $invitation,
        public User $invitedBy
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $business = $this->invitation->business;
        $role = $this->invitation->businessRole;

        return (new MailMessage)
            ->subject('Отправлено приглашение в бизнес '.$business->name)
            ->greeting('Здравствуйте!')
            ->line('Пользователь '.$this->invitedBy->name.' отправил приглашение пользователю '.$this->invitation->email.' с ролью '.$role->name.' в бизнес «'.$business->name.'».')
            ->line('Вы получили это уведомление как администратор бизнеса.')
            ->line('Спасибо за использование нашей системы!');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'invitation_id' => $this->invitation->id,
            'business_id' => $this->invitation->business_id,
            'invited_by_id' => $this->invitedBy->id,
        ];
    }
}
