<?php

namespace App\Notifications;

use App\Models\Ticket;
use App\Models\TicketComment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TicketCommentAdded extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public Ticket $ticket,
        public TicketComment $comment
    ) {}

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
        $author = $this->comment->user
            ? $this->comment->user->name
            : ($this->comment->author_name ?? 'Анонимный пользователь');

        return (new MailMessage)
            ->subject('Новый комментарий к тикету #'.$this->ticket->id)
            ->line('Добавлен новый комментарий к тикету: '.$this->ticket->title)
            ->line('Автор: '.$author)
            ->line('Комментарий: '.$this->comment->content)
            ->action('Просмотреть тикет', route('panel.tickets.show', $this->ticket))
            ->line('Спасибо за использование нашей системы!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'ticket_id' => $this->ticket->id,
            'comment_id' => $this->comment->id,
            'title' => $this->ticket->title,
            'message' => 'Добавлен новый комментарий к тикету: '.$this->ticket->title,
        ];
    }
}
