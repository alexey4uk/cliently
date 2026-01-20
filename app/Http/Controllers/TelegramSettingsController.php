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
        $user = Auth::user();
        $business = $user->businesses()->first();

        if ($business) {
            $business->update(['telegram_chat_id' => null]);
        }

        return redirect()->route('settings.telegram')->with('success', 'Telegram аккаунт отключен.');
    }
}
