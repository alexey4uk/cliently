<?php

namespace App\Http\Controllers;

use App\Services\SubscriptionAccessService;

class TelegramSettingsController extends Controller
{
    public function index()
    {
        $business = $this->getCurrentBusiness();

        if (! $business) {
            return redirect()->route('welcome')
                ->with('info', 'Сначала создайте бизнес или примите приглашение.');
        }

        $this->authorizeBusinessPermission('client.telegram.manage');

        // Проверяем доступ к Telegram боту согласно тарифу
        $accessService = app(SubscriptionAccessService::class);
        $redirect = $accessService->checkAccessWithRedirect(
            $business,
            'telegram_bot_enabled',
            'client.telegram.manage',
            'Telegram бот',
            'subscription.index'
        );

        if ($redirect) {
            return $redirect;
        }

        // Получить первого бота (предполагаем, что бот один)
        $bot = \DefStudio\Telegraph\Models\TelegraphBot::first();

        // Определяем состояние бота
        $botState = 'no-bot'; // бота нет в системе
        if ($bot) {
            // Генерируем токен, если отсутствует
            if (empty($business->telegram_token)) {
                $business->telegram_token = \Illuminate\Support\Str::random(32);
                $business->save();
            }

            if (empty($business->telegram_chat_id)) {
                $botState = 'disconnected'; // пользователь еще не подключил
            } else {
                $botState = 'connected'; // все настроено
            }
        }

        return view('settings.telegram.index', compact('business', 'bot', 'botState'));
    }

    public function disconnect()
    {
        $business = $this->getCurrentBusiness();

        if (! $business) {
            return redirect()->route('welcome')
                ->with('info', 'Сначала создайте бизнес или примите приглашение.');
        }

        $this->authorizeBusinessPermission('client.telegram.manage');

        if (! $business) {
            return redirect()->route('welcome')
                ->with('info', 'Сначала создайте бизнес или примите приглашение.');
        }

        if ($business) {
            $business->update(['telegram_chat_id' => null]);
        }

        return redirect()->route('settings.telegram')->with('success', 'Telegram аккаунт отключен.');
    }
}
