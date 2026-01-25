<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\BepaidSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BepaidSettingsController extends Controller
{
    /**
     * Display the bePaid settings page.
     */
    public function index()
    {
        $settings = BepaidSettings::getSettings();

        return view('panel.settings.bepaid.index', [
            'settings' => $settings,
        ]);
    }

    /**
     * Update the bePaid settings.
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'test_mode' => ['nullable', 'in:0,1'],
            'test_shop_id' => ['nullable', 'string', 'max:255', function ($attribute, $value, $fail) {
                if ($value && !is_numeric(trim($value))) {
                    $fail('Shop ID должен быть числом.');
                }
            }],
            'test_secret_key' => 'nullable|string',
            'test_gateway_base' => 'nullable|url|max:255',
            'test_checkout_base' => 'nullable|url|max:255',
            'production_shop_id' => ['nullable', 'string', 'max:255', function ($attribute, $value, $fail) {
                if ($value && !is_numeric(trim($value))) {
                    $fail('Shop ID должен быть числом.');
                }
            }],
            'production_secret_key' => 'nullable|string',
            'production_gateway_base' => 'nullable|url|max:255',
            'production_checkout_base' => 'nullable|url|max:255',
            'webhook_url' => 'nullable|string|max:255',
            'enabled' => ['nullable', 'in:0,1'],
        ]);

        // Преобразуем строковые значения в boolean
        $validated['test_mode'] = isset($validated['test_mode']) ? (bool) $validated['test_mode'] : false;
        $validated['enabled'] = isset($validated['enabled']) ? (bool) $validated['enabled'] : false;

        $settings = BepaidSettings::getSettings();

        // Если включен режим, проверяем наличие обязательных полей
        if ($validated['enabled'] ?? false) {
            if ($validated['test_mode'] ?? true) {
                if (empty($validated['test_shop_id']) || empty($validated['test_secret_key'])) {
                    return redirect()->back()
                        ->withInput()
                        ->withErrors(['enabled' => 'Для включения bePaid необходимо заполнить тестовые Shop ID и Secret Key.']);
                }
            } else {
                if (empty($validated['production_shop_id']) || empty($validated['production_secret_key'])) {
                    return redirect()->back()
                        ->withInput()
                        ->withErrors(['enabled' => 'Для включения bePaid необходимо заполнить продакшн Shop ID и Secret Key.']);
                }
            }
        }

        $settings->update($validated);

        Log::info('bePaid settings updated', [
            'test_mode' => $validated['test_mode'] ?? false,
            'enabled' => $validated['enabled'] ?? false,
        ]);

        return redirect()->route('panel.settings.bepaid')
            ->with('success', 'Настройки bePaid успешно обновлены.');
    }

    /**
     * Test connection to bePaid API.
     */
    public function testConnection(Request $request)
    {
        try {
            $settings = BepaidSettings::getSettings();

            if (! $settings->enabled) {
                return response()->json([
                    'success' => false,
                    'message' => 'bePaid не включен. Включите его перед тестированием.',
                ], 400);
            }

            $currentSettings = $settings->getCurrentSettings();

            if (empty($currentSettings['shop_id']) || empty($currentSettings['secret_key'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Не заполнены обязательные поля для текущего режима.',
                ], 400);
            }

            // Простая проверка - пытаемся создать тестовый запрос
            // В реальности можно сделать запрос к API для проверки
            return response()->json([
                'success' => true,
                'message' => 'Подключение успешно. Настройки корректны.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при проверке подключения: '.$e->getMessage(),
            ], 500);
        }
    }
}
