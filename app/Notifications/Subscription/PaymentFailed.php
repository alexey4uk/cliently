<?php

namespace App\Notifications\Subscription;

use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentFailed extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Invoice $invoice,
        public ?string $reason = null
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $plan = $this->invoice->plan;

        $message = (new MailMessage)
            ->subject('Оплата подписки не прошла')
            ->greeting('Здравствуйте, '.$notifiable->name.'!')
            ->line('К сожалению, оплата подписки на тариф «'.$plan->name.'» не прошла.')
            ->line('Сумма: '.$this->invoice->amount.' '.$this->invoice->currency);

        if ($this->reason) {
            $message->line('Причина: '.$this->reason);
        }

        return $message
            ->action('Повторить оплату', route('subscription.current'))
            ->line('Если проблема сохраняется, пожалуйста, свяжитесь с поддержкой.')
            ->line('Спасибо за использование нашей системы!');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'invoice_id' => $this->invoice->id,
            'subscription_id' => $this->invoice->subscription_id,
            'plan_id' => $this->invoice->plan_id,
            'amount' => $this->invoice->amount,
            'currency' => $this->invoice->currency,
            'reason' => $this->reason,
        ];
    }
}
