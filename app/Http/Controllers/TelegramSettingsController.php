<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
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
