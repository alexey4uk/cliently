<?php

namespace App\Http\Controllers;

use App\Services\SubscriptionAccessService;
use Illuminate\Support\Facades\Auth;

class TelegramSettingsController extends Controller
{
    public function index()
    {
        $business = $this->getCurrentBusiness();

        if (! $business) {
            $bot = \DefStudio\Telegraph\Models\TelegraphBot::first();
            $user = Auth::user();
            if ($user && empty($user->telegram_token)) {
                $user->telegram_token = \Illuminate\Support\Str::random(32);
                $user->save();
            }
            $botUsername = $bot ? $bot->name : null;
            $telegramLink = $botUsername && $user && $user->telegram_token
                ? "https://t.me/{$botUsername}?start=user_auth_{$user->telegram_token}"
                : null;

            return view('settings.telegram.index', [
                'business' => null,
                'bot' => $bot,
                'botState' => $bot ? 'disconnected' : 'no-bot',
                'user' => $user ?? Auth::user(),
                'telegramLink' => $telegramLink,
            ]);
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

        // Данные для блока «Уведомления в Telegram» (привязка личного аккаунта)
        $user = Auth::user();
        if (empty($user->telegram_token)) {
            $user->telegram_token = \Illuminate\Support\Str::random(32);
            $user->save();
        }
        $botUsername = $bot ? $bot->name : null;
        $telegramLink = $botUsername && $user->telegram_token
            ? "https://t.me/{$botUsername}?start=user_auth_{$user->telegram_token}"
            : null;

        // Состояние бота (для обратной совместимости, если понадобится)
        $botState = $bot ? (empty($business->telegram_chat_id) ? 'disconnected' : 'connected') : 'no-bot';

        return view('settings.telegram.index', compact('business', 'bot', 'botState', 'user', 'telegramLink'));
    }

    public function disconnect()
    {
        $business = $this->getCurrentBusiness();

        if (! $business) {
            return redirect()->route('settings.telegram')->with('error', 'Сначала создайте бизнес или примите приглашение.');
        }

        $this->authorizeBusinessPermission('client.telegram.manage');

        $business->update(['telegram_chat_id' => null]);

        return redirect()->route('settings.telegram')->with('success', 'Telegram аккаунт отключен.');
    }
}
