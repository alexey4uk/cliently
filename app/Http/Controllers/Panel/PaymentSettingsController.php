<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Services\PaymentSettingsService;
use Illuminate\Http\Request;

class PaymentSettingsController extends Controller
{
    protected PaymentSettingsService $settingsService;

    public function __construct(PaymentSettingsService $settingsService)
    {
        $this->settingsService = $settingsService;
    }

    /**
     * Показать страницу настроек платежей
     */
    public function index()
    {
        $this->authorize('panel.payments.settings');

        $settings = $this->settingsService->getAllSettings();

        return view('panel.settings.payments.index', [
            'gateways' => $settings['gateways'],
            'types' => $settings['types'],
        ]);
    }

    /**
     * Обновить настройки шлюза
     */
    public function updateGateway(Request $request, string $gateway)
    {
        $this->authorize('panel.payments.settings');

        $validated = $request->validate([
            'enabled' => 'required|boolean',
            'test_mode' => 'nullable|boolean',
            'priority' => 'nullable|integer|min:0',
        ]);

        $this->settingsService->updateGatewaySettings($gateway, $validated);

        return response()->json([
            'success' => true,
            'message' => 'Настройки шлюза обновлены',
        ]);
    }

    /**
     * Обновить настройки типа оплаты
     */
    public function updateType(Request $request, string $type)
    {
        $this->authorize('panel.payments.settings');

        $validated = $request->validate([
            'enabled' => 'required|boolean',
            'default_gateway' => 'nullable|string',
            'allowed_gateways' => 'nullable|array',
            'allowed_gateways.*' => 'string',
            'min_amount' => 'nullable|numeric|min:0',
            'max_amount' => 'nullable|numeric|min:0',
        ]);

        // Оставляем в разрешённых только настроенные шлюзы
        if (isset($validated['allowed_gateways'])) {
            $validated['allowed_gateways'] = array_values(array_filter(
                $validated['allowed_gateways'],
                fn (string $gateway) => $this->settingsService->isGatewayConfigured($gateway)
            ));
            if (empty($validated['allowed_gateways'])) {
                $validated['allowed_gateways'] = null;
            }
        }

        // Шлюз по умолчанию должен быть настроен
        if (! empty($validated['default_gateway']) && ! $this->settingsService->isGatewayConfigured($validated['default_gateway'])) {
            $validated['default_gateway'] = null;
        }

        $this->settingsService->updateTypeSettings($type, $validated);

        return response()->json([
            'success' => true,
            'message' => 'Настройки типа оплаты обновлены',
        ]);
    }

    /**
     * Массовое обновление настроек
     */
    public function update(Request $request)
    {
        $this->authorize('panel.payments.settings');

        $validated = $request->validate([
            'gateways' => 'array',
            'gateways.*.enabled' => 'boolean',
            'gateways.*.test_mode' => 'nullable|boolean',
            'gateways.*.priority' => 'nullable|integer|min:0',
            'types' => 'array',
            'types.*.enabled' => 'boolean',
            'types.*.default_gateway' => 'nullable|string',
            'types.*.min_amount' => 'nullable|numeric|min:0',
            'types.*.max_amount' => 'nullable|numeric|min:0',
        ]);

        // Обновляем шлюзы
        foreach ($validated['gateways'] ?? [] as $gateway => $data) {
            $this->settingsService->updateGatewaySettings($gateway, $data);
        }

        // Обновляем типы оплат
        foreach ($validated['types'] ?? [] as $type => $data) {
            $this->settingsService->updateTypeSettings($type, $data);
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Настройки платежей обновлены',
            ]);
        }

        return back()->with('success', 'Настройки платежей обновлены');
    }

    /**
     * Инициализировать настройки по умолчанию
     */
    public function initialize()
    {
        $this->authorize('panel.payments.settings');

        $this->settingsService->initializeDefaults();

        return response()->json([
            'success' => true,
            'message' => 'Настройки инициализированы',
        ]);
    }
}
