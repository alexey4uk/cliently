<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\Business;
use DefStudio\Telegraph\Models\TelegraphChat;
use Illuminate\Support\Facades\Log;

class TelegramNotificationService
{
    /**
     * Отправить уведомление о новом назначении
     */
    public static function sendAppointmentCreated(Appointment $appointment)
    {
        $business = $appointment->business;

        if (! $business->telegram_chat_id) {
            return;
        }

        $message = self::formatAppointmentMessage($appointment, 'новая запись');

        self::sendMessage($business, $message);
    }

    /**
     * Отправить уведомление об изменении статуса назначения
     */
    public static function sendAppointmentStatusChanged(Appointment $appointment, ?string $oldStatus = null)
    {
        $business = $appointment->business;

        if (! $business->telegram_chat_id) {
            return;
        }

        $statusText = match ($appointment->status) {
            'confirmed' => 'подтверждена',
            'cancelled' => 'отменена',
            'completed' => 'завершена',
            default => 'обновлена',
        };

        $message = self::formatAppointmentMessage($appointment, "запись {$statusText}");

        self::sendMessage($business, $message);
    }

    /**
     * Форматировать сообщение о назначении
     */
    private static function formatAppointmentMessage(Appointment $appointment, string $action): string
    {
        $client = $appointment->client;
        $service = $appointment->service;
        $master = $appointment->master;
        $location = $appointment->location;

        $message = "📅 {$action}\n\n";
        $message .= "👤 Клиент: {$client->first_name} {$client->last_name}\n";
        $message .= "📱 Телефон: {$client->phone}\n";
        $message .= "💼 Услуга: {$service->name}\n";

        if ($master) {
            $message .= "👨‍💼 Мастер: {$master->first_name} {$master->last_name}\n";
        }

        if ($location) {
            $message .= "📍 Локация: {$location->name}\n";
        }

        $message .= "📆 Дата: {$appointment->date->format('d.m.Y')}\n";
        $message .= "🕐 Время: {$appointment->time}\n";

        if ($appointment->price) {
            $message .= "💰 Цена: {$appointment->price} руб.\n";
        }

        if ($appointment->notes) {
            $message .= "📝 Заметки: {$appointment->notes}\n";
        }

        return $message;
    }

    /**
     * Отправить сообщение в Telegram
     */
    private static function sendMessage(Business $business, string $message)
    {
        try {
            $bot = \DefStudio\Telegraph\Models\TelegraphBot::first();

            if (! $bot) {
                return;
            }

            $chat = TelegraphChat::where('chat_id', $business->telegram_chat_id)->first();

            if (! $chat) {
                $chat = $bot->chats()->create([
                    'chat_id' => $business->telegram_chat_id,
                    'name' => 'Business Notifications',
                ]);
            }

            $chat->message($message)->send();
        } catch (\Exception $e) {
            // Логируем ошибку, но не прерываем выполнение
            Log::error('Telegram notification failed: '.$e->getMessage());
        }
    }
}
