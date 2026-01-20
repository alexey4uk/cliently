<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Process;

class TelegramManagementController extends Controller
{
    /**
     * Показать список ботов
     */
    public function index()
    {
        $bots = \DefStudio\Telegraph\Models\TelegraphBot::all();

        return view('settings.telegram.management', [
            'bots' => $bots,
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
                'bot_name' => $bot->name
            ]);

            return redirect()->route('panel.telegram.management')->with('success', 'Бот успешно создан');
        } catch (\Exception $e) {
            Log::error('Failed to create bot', [
                'error' => $e->getMessage(),
                'name' => $request->name
            ]);

            return back()->withInput()->with('error', 'Ошибка при создании бота: ' . $e->getMessage());
        }
    }

    /**
     * Показать форму редактирования бота
     */
    public function edit($id)
    {
        $bot = \DefStudio\Telegraph\Models\TelegraphBot::find($id);

        if (!$bot) {
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

        if (!$bot) {
            return back()->with('error', 'Бот не найден');
        }

        $request->validate([
            'name' => 'required|string|max:255|unique:telegraph_bots,name,' . $bot->id,
            'token' => 'required|string|max:255|unique:telegraph_bots,token,' . $bot->id,
        ]);

        try {
            $bot->update([
                'name' => $request->name,
                'token' => $request->token,
            ]);

            Log::info('Bot updated successfully', [
                'bot_id' => $bot->id,
                'bot_name' => $bot->name
            ]);

            return redirect()->route('panel.telegram.management')->with('success', 'Бот успешно обновлен');
        } catch (\Exception $e) {
            Log::error('Failed to update bot', [
                'error' => $e->getMessage(),
                'bot_id' => $bot->id
            ]);

            return back()->withInput()->with('error', 'Ошибка при обновлении бота: ' . $e->getMessage());
        }
    }

    /**
     * Удалить бота
     */
    public function destroy(Request $request, $id)
    {
        $bot = \DefStudio\Telegraph\Models\TelegraphBot::find($id);

        if (!$bot) {
            return back()->with('error', 'Бот не найден');
        }

        $bot->delete();

        return back()->with('success', 'Бот успешно удалён');
    }
}
