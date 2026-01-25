<?php

namespace App\Http\Controllers;

use DefStudio\Telegraph\Models\TelegraphBot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TelegramManagementController extends Controller
{
    /**
     * Показать список ботов
     */
    public function index()
    {
        $bots = \DefStudio\Telegraph\Models\TelegraphBot::all();

        $webhookStatuses = [];
        foreach ($bots as $bot) {
            $webhookStatuses[$bot->id] = $this->checkWebhookStatus($bot);
        }

        return view('settings.telegram.management', [
            'bots' => $bots,
            'webhookStatuses' => $webhookStatuses,
        ]);
    }

    /**
     * Показать форму создания бота
     */
    public function create()
    {
        return view('settings.telegram.create');
    }

    /**
     * Сохранить нового бота
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:telegraph_bots,name',
            'token' => 'required|string|max:255|unique:telegraph_bots,token',
        ]);

        try {
            $bot = \DefStudio\Telegraph\Models\TelegraphBot::create([
                'name' => $request->name,
                'token' => $request->token,
            ]);

            Log::info('Bot created successfully', [
                'bot_id' => $bot->id,
                'bot_name' => $bot->name,
            ]);

            return redirect()->route('panel.telegram.management')->with('success', 'Бот успешно создан');
        } catch (\Exception $e) {
            Log::error('Failed to create bot', [
                'error' => $e->getMessage(),
                'name' => $request->name,
            ]);

            return back()->withInput()->with('error', 'Ошибка при создании бота: '.$e->getMessage());
        }
    }

    /**
     * Показать форму редактирования бота
     */
    public function edit($id)
    {
        $bot = \DefStudio\Telegraph\Models\TelegraphBot::find($id);

        if (! $bot) {
            return redirect()->route('panel.telegram.management')->with('error', 'Бот не найден');
        }

        return view('settings.telegram.edit', compact('bot'));
    }

    /**
     * Обновить бота
     */
    public function update(Request $request, $id)
    {
        $bot = \DefStudio\Telegraph\Models\TelegraphBot::find($id);

        if (! $bot) {
            return back()->with('error', 'Бот не найден');
        }

        $request->validate([
            'name' => 'required|string|max:255|unique:telegraph_bots,name,'.$bot->id,
            'token' => 'required|string|max:255|unique:telegraph_bots,token,'.$bot->id,
        ]);

        try {
            $bot->update([
                'name' => $request->name,
                'token' => $request->token,
            ]);

            Log::info('Bot updated successfully', [
                'bot_id' => $bot->id,
                'bot_name' => $bot->name,
            ]);

            return redirect()->route('panel.telegram.management')->with('success', 'Бот успешно обновлен');
        } catch (\Exception $e) {
            Log::error('Failed to update bot', [
                'error' => $e->getMessage(),
                'bot_id' => $bot->id,
            ]);

            return back()->withInput()->with('error', 'Ошибка при обновлении бота: '.$e->getMessage());
        }
    }

    /**
     * Удалить бота
     */
    public function destroy(Request $request, $id)
    {
        $bot = \DefStudio\Telegraph\Models\TelegraphBot::find($id);

        if (! $bot) {
            return back()->with('error', 'Бот не найден');
        }

        $bot->delete();

        return back()->with('success', 'Бот успешно удалён');
    }

    /**
     * Установить webhook для бота
     */
    public function setWebhook(Request $request, $id)
    {
        /** @var TelegraphBot $bot */
        $bot = \DefStudio\Telegraph\Models\TelegraphBot::find($id);

        if (! $bot) {
            return back()->with('error', 'Бот не найден');
        }

        try {
            // Получаем отладочную информацию о webhook перед установкой
            $debugInfo = $bot->getWebhookDebugInfo()->send();

            Log::info('Webhook debug info before setup', [
                'bot_id' => $bot->id,
                'debug_info' => $debugInfo->json(),
            ]);

            // Используем параметры из документации: dropPendingUpdates = true
            $reply = $bot->registerWebhook(dropPendingUpdates: true)->send();

            if ($reply->telegraphError()) {
                Log::error('Failed to set webhook', [
                    'bot_id' => $bot->id,
                    'error' => $reply->telegraphError(),
                    'response_json' => $reply->json(),
                ]);

                return back()->with('error', 'Ошибка при установке webhook: '.$reply->telegraphError());
            }

            Log::info('Webhook set successfully', [
                'bot_id' => $bot->id,
                'response' => $reply->json(),
            ]);

            return back()->with('success', 'Webhook успешно установлен');
        } catch (\Exception $e) {
            Log::error('Exception setting webhook', [
                'bot_id' => $bot->id,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Ошибка при установке webhook: '.$e->getMessage());
        }
    }

    /**
     * Удалить webhook для бота
     */
    public function deleteWebhook(Request $request, $id)
    {
        /** @var TelegraphBot $bot */
        $bot = \DefStudio\Telegraph\Models\TelegraphBot::find($id);

        if (! $bot) {
            return back()->with('error', 'Бот не найден');
        }

        try {
            // Логируем информацию перед удалением
            Log::info('Attempting to delete webhook', [
                'bot_id' => $bot->id,
                'bot_token' => substr($bot->token, 0, 10).'***',
            ]);

            // Используем именованный параметр для лучшей читаемости
            $reply = $bot->unregisterWebhook(dropPendingUpdates: true)->send();

            if ($reply->telegraphError()) {
                Log::error('Failed to delete webhook', [
                    'bot_id' => $bot->id,
                    'error' => $reply->telegraphError(),
                    'response_json' => $reply->json(),
                ]);

                return back()->with('error', 'Ошибка при удалении webhook: '.$reply->telegraphError());
            }

            Log::info('Webhook deleted successfully', [
                'bot_id' => $bot->id,
                'response' => $reply->json(),
            ]);

            return back()->with('success', 'Webhook успешно удалён');
        } catch (\Exception $e) {
            Log::error('Exception deleting webhook', [
                'bot_id' => $bot->id,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Ошибка при удалении webhook: '.$e->getMessage());
        }
    }

    /**
     * Получить информацию о боте для диагностики
     */
    public function getBotInfo(Request $request, $id)
    {
        /** @var TelegraphBot $bot */
        $bot = \DefStudio\Telegraph\Models\TelegraphBot::find($id);

        if (! $bot) {
            return back()->with('error', 'Бот не найден');
        }

        try {
            $botInfo = $bot->info();

            // Также получаем информацию о webhook
            $webhookDebugInfo = $bot->getWebhookDebugInfo()->send();

            Log::info('Bot and webhook info retrieved', [
                'bot_id' => $bot->id,
                'username' => $botInfo['username'] ?? 'unknown',
                'webhook_info' => $webhookDebugInfo->json(),
            ]);

            $webhookUrl = $webhookDebugInfo->json()['result']['url'] ?? 'не установлен';
            $webhookStatus = ! empty($webhookUrl) ? 'установлен' : 'не установлен';

            return back()->with('success', 'Информация о боте: @'.($botInfo['username'] ?? 'unknown').". Webhook: {$webhookStatus}".(! empty($webhookUrl) ? " ({$webhookUrl})" : ''));
        } catch (\Exception $e) {
            Log::error('Failed to get bot info', [
                'bot_id' => $bot->id,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Ошибка при получении информации о боте: '.$e->getMessage());
        }
    }

    /**
     * Проверить статус webhook для бота
     */
    private function checkWebhookStatus($bot)
    {
        try {
            $reply = $bot->getWebhookDebugInfo()->send();

            if ($reply->telegraphError()) {
                Log::error('Webhook debug info failed', [
                    'bot_id' => $bot->id,
                    'error' => $reply->telegraphError(),
                ]);

                return [
                    'status' => 'error',
                    'message' => 'Ошибка проверки',
                ];
            }

            $webhookInfo = $reply->json()['result'] ?? [];

            // Проверяем, установлен ли webhook
            if (! empty($webhookInfo['url'])) {
                return [
                    'status' => 'connected',
                    'message' => 'Подключен',
                    'url' => $webhookInfo['url'],
                    'last_error_date' => $webhookInfo['last_error_date'] ?? null,
                    'last_error_message' => $webhookInfo['last_error_message'] ?? null,
                ];
            } else {
                return [
                    'status' => 'not_set',
                    'message' => 'Не установлен',
                ];
            }
        } catch (\Exception $e) {
            Log::error('Webhook check failed', [
                'bot_id' => $bot->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'status' => 'error',
                'message' => 'Ошибка проверки',
            ];
        }
    }
}
