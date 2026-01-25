<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\Business;
use App\Models\Ticket;
use App\Models\TicketComment;
use App\Models\User;
use DefStudio\Telegraph\Models\TelegraphChat;
use Illuminate\Support\Facades\Log;

class TelegramNotificationService
{
    /**
     * Отправить уведомление о новом назначении конкретному пользователю
     */
    public static function sendAppointmentCreated(Appointment $appointment, User $user)
    {
        if (! $user->telegram_chat_id) {
            return;
        }

        $message = self::formatAppointmentMessage($appointment, 'новая запись');

        self::sendMessageToUser($user, $message);
    }

    /**
     * Отправить уведомление об изменении статуса назначения конкретному пользователю
     */
    public static function sendAppointmentStatusChanged(Appointment $appointment, User $user, ?string $oldStatus = null)
    {
        if (! $user->telegram_chat_id) {
            return;
        }

        $statusText = match ($appointment->status) {
            'confirmed' => 'подтверждена',
            'cancelled' => 'отменена',
            'completed' => 'завершена',
            default => 'обновлена',
        };

        $message = self::formatAppointmentMessage($appointment, "запись {$statusText}");

        self::sendMessageToUser($user, $message);
    }

    /**
     * Отправить уведомление об изменении статуса назначения для клиента
     */
    public static function sendAppointmentStatusChangedForClient(Appointment $appointment, ?string $oldStatus = null)
    {
        // Проверяем, есть ли у клиента telegram_user_id
        if (! $appointment->client->telegram_user_id) {
            return;
        }

        $statusText = match ($appointment->status) {
            'confirmed' => 'подтверждена',
            'cancelled' => 'отменена',
            'completed' => 'завершена',
            default => 'обновлена',
        };

        $message = self::formatAppointmentMessage($appointment, "запись {$statusText}");

        self::sendMessageForClient($appointment->client->telegram_user_id, $message);
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
     * Отправить сообщение в личный чат пользователя
     */
    private static function sendMessageToUser(User $user, string $message)
    {
        try {
            $bot = \DefStudio\Telegraph\Models\TelegraphBot::first();

            if (! $bot) {
                return;
            }

            if (! $user->telegram_chat_id) {
                return;
            }

            $chat = TelegraphChat::where('chat_id', $user->telegram_chat_id)->first();

            if (! $chat) {
                $chat = $bot->chats()->create([
                    'chat_id' => $user->telegram_chat_id,
                    'name' => $user->name ?? 'User Notifications',
                ]);
            }

            $chat->message($message)->send();
        } catch (\Exception $e) {
            // Логируем ошибку, но не прерываем выполнение
            Log::error('Telegram notification failed for user: '.$e->getMessage(), [
                'user_id' => $user->id,
                'chat_id' => $user->telegram_chat_id,
            ]);
        }
    }

    /**
     * Отправить сообщение в Telegram (для бизнеса - оставлено для обратной совместимости)
     *
     * @deprecated Используйте sendMessageToUser для отправки пользователям
     */
    private static function sendMessage(Business $business, string $message)
    {
        try {
            $bot = \DefStudio\Telegraph\Models\TelegraphBot::first();

            if (! $bot) {
                return;
            }

            if (! $business->telegram_chat_id) {
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

    private static function sendMessageForClient(int $id, string $message)
    {
        try {
            $bot = \DefStudio\Telegraph\Models\TelegraphBot::first();

            if (! $bot) {
                return;
            }

            $chat = TelegraphChat::where('chat_id', $id)->first();

            if (! $chat) {
                $chat = $bot->chats()->create([
                    'chat_id' => $id,
                    'name' => 'Business Notifications',
                ]);
            }

            $chat->message($message)->send();
        } catch (\Exception $e) {
            // Логируем ошибку, но не прерываем выполнение
            Log::error('Telegram notification failed: '.$e->getMessage());
        }
    }

    /**
     * Отправить уведомление о создании тикета конкретному пользователю
     */
    public static function sendTicketCreated(Ticket $ticket, User $user): void
    {
        if (! $user->telegram_chat_id) {
            return;
        }

        $message = self::formatTicketMessage($ticket, 'новый тикет');
        self::sendMessageToUser($user, $message);
    }

    /**
     * Отправить уведомление о новом комментарии к тикету конкретному пользователю
     */
    public static function sendTicketCommentAdded(Ticket $ticket, TicketComment $comment, User $user): void
    {
        if (! $user->telegram_chat_id) {
            return;
        }

        $commentAuthor = $comment->user;
        $message = "💬 Новый комментарий к тикету #{$ticket->id}\n\n";
        $message .= "📋 Тикет: {$ticket->title}\n";
        $message .= '👤 Автор: '.($commentAuthor->name ?? 'Пользователь')."\n";
        $message .= '💬 Комментарий: '.substr($comment->content, 0, 200);
        if (strlen($comment->content) > 200) {
            $message .= '...';
        }

        self::sendMessageToUser($user, $message);
    }

    /**
     * Отправить уведомление об изменении статуса тикета конкретному пользователю
     */
    public static function sendTicketStatusChanged(Ticket $ticket, User $user, string $oldStatus, string $newStatus): void
    {
        if (! $user->telegram_chat_id) {
            return;
        }

        $statusText = match ($newStatus) {
            'pending' => 'ожидает',
            'in_progress' => 'в работе',
            'completed' => 'выполнен',
            'cancelled' => 'отменен',
            default => 'обновлен',
        };

        $message = self::formatTicketMessage($ticket, "тикет {$statusText}");
        self::sendMessageToUser($user, $message);
    }

    /**
     * Отправить уведомление о назначении тикета конкретному пользователю
     */
    public static function sendTicketAssigned(Ticket $ticket, User $user): void
    {
        if (! $user->telegram_chat_id) {
            return;
        }

        $message = "👤 Вам назначен тикет #{$ticket->id}\n\n";
        $message .= "📋 {$ticket->title}\n";
        if ($ticket->description) {
            $message .= '📝 '.substr($ticket->description, 0, 200);
            if (strlen($ticket->description) > 200) {
                $message .= '...';
            }
            $message .= "\n";
        }

        self::sendMessageToUser($user, $message);
    }

    /**
     * Форматировать сообщение о тикете
     */
    private static function formatTicketMessage(Ticket $ticket, string $action): string
    {
        $message = "🎫 {$action}\n\n";
        $message .= "📋 Тикет #{$ticket->id}: {$ticket->title}\n";

        if ($ticket->description) {
            $message .= '📝 '.substr($ticket->description, 0, 200);
            if (strlen($ticket->description) > 200) {
                $message .= '...';
            }
            $message .= "\n";
        }

        $statusText = match ($ticket->status) {
            'pending' => '⏳ Ожидает',
            'in_progress' => '🔄 В работе',
            'completed' => '✅ Выполнен',
            'cancelled' => '❌ Отменен',
            default => '📌 '.ucfirst($ticket->status),
        };
        $message .= "📊 Статус: {$statusText}\n";

        if ($ticket->assignedUser) {
            $message .= "👤 Назначен: {$ticket->assignedUser->name}\n";
        }

        return $message;
    }

    /**
     * Отправить админское уведомление о создании бизнеса конкретному админу
     */
    public static function sendAdminBusinessCreated(Business $business, User $admin): void
    {
        if (! $admin->telegram_chat_id) {
            return;
        }

        $owner = $business->users()->wherePivot('role', 'owner')->first();
        $message = "🏢 Новый бизнес зарегистрирован\n\n";
        $message .= "📋 Название: {$business->name}\n";
        $message .= '👤 Владелец: '.($owner ? $owner->name : 'Не указан')."\n";
        $message .= '📧 Email: '.($owner ? $owner->email : 'Не указан')."\n";

        self::sendMessageToUser($admin, $message);
    }

    /**
     * Отправить админское уведомление о создании тикета конкретному админу
     */
    public static function sendAdminTicketCreated(Ticket $ticket, User $admin): void
    {
        if (! $admin->telegram_chat_id) {
            return;
        }

        $business = $ticket->business;
        $creator = $ticket->creator();

        $message = "🎫 Новый тикет от пользователя\n\n";
        $message .= "📋 Тикет #{$ticket->id}: {$ticket->title}\n";
        $message .= '🏢 Бизнес: '.($business->name ?? 'Не указан')."\n";
        $message .= '👤 Создатель: '.($creator ? $creator->name : 'Не указан')."\n";
        if ($ticket->description) {
            $message .= '📝 '.substr($ticket->description, 0, 200);
            if (strlen($ticket->description) > 200) {
                $message .= '...';
            }
            $message .= "\n";
        }

        self::sendMessageToUser($admin, $message);
    }
}
