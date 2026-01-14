<?php

namespace App\Telegram;

use App\Models\Business;
use DefStudio\Telegraph\Handlers\WebhookHandler;

class Handler extends WebhookHandler
{
    public function hi()
    {
        $this->chat->message('test')->send();
    }

    public function start()
    {
        $text = $this->message->text() ?? '';
        $parts = explode(' ', $text);

        if (isset($parts[1]) && str_starts_with($parts[1], 'auth_')) {
            $businessId = str_replace('auth_', '', $parts[1]);

            $business = Business::find($businessId);
            if ($business) {
                $business->update(['telegram_chat_id' => $this->chat->chat_id]);

                $this->chat->message('✅ Аккаунт успешно подключен! Теперь вы будете получать уведомления о записях.')->send();
            } else {
                $this->chat->message('❌ Бизнес не найден.')->send();
            }
        } else {
            $this->chat->message('Привет! Я бот для уведомлений о записях. Для подключения используйте ссылку из настроек.')->send();
        }
    }
}
