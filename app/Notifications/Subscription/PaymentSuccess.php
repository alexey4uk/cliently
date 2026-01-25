<?php

namespace App\Notifications\Subscription;

use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentSuccess extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Invoice $invoice
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $plan = $this->invoice->plan;
        $subscription = $this->invoice->subscription;

        $message = (new MailMessage)
            ->subject('Оплата подписки успешна')
            ->greeting('Здравствуйте, '.$notifiable->name.'!')
            ->line('Оплата подписки на тариф «'.$plan->name.'» успешно выполнена.')
            ->line('Сумма: '.$this->invoice->amount.' '.$this->invoice->currency)
            ->line('Дата оплаты: '.$this->invoice->paid_at->format('d.m.Y H:i'));

        if ($subscription && $subscription->ends_at) {
            $message->line('Подписка действует до: '.$subscription->ends_at->format('d.m.Y'));
        }

        return $message
            ->action('Управление подпиской', route('subscription.current'))
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
        ];
    }
}
