<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class TelegramSettingsController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $business = $user->businesses()->first(); // Предполагаем, что пользователь имеет один бизнес

        // Получить первого бота (предполагаем, что бот один)
        $bot = \DefStudio\Telegraph\Models\TelegraphBot::first();

        // Проверяем, есть ли настроенный бот для уведомлений
        // Бот настроен, если у бизнеса есть telegram_token
        $hasBotForNotifications = !empty($business->telegram_token);

        return view('settings.telegram.index', compact('business', 'bot', 'hasBotForNotifications'));
    }

    public function disconnect()
    {
        $user = Auth::user();
        $business = $user->businesses()->first();

        if ($business) {
            $business->update(['telegram_chat_id' => null]);
        }

        return redirect()->route('settings.telegram')->with('success', 'Telegram аккаунт отключен.');
    }
}
