<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Services\GatewayManager;
use App\Services\PaymentService;
use App\Services\PaymentSettingsService;
use Endroid\QrCode\Builder\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PaymentMethodController extends Controller
{
    public function __construct(
        protected PaymentSettingsService $settingsService,
        protected GatewayManager $gatewayManager,
        protected PaymentService $paymentService,
    ) {}

    /**
     * Показать страницу выбора способа оплаты
     */
    public function show(Invoice $invoice, Request $request)
    {
        $user = Auth::user();

        // Проверяем, что инвойс принадлежит пользователю
        if ($invoice->user_id !== $user->id) {
            abort(403, 'Доступ запрещен');
        }

        // Проверяем, что инвойс ещё не оплачен
        if ($invoice->isPaid()) {
            return redirect()->route('subscription.current')
                ->with('info', 'Этот платеж уже оплачен.');
        }

        // Проверяем, не истёк ли срок
        if ($invoice->isExpired()) {
            return redirect()->route('subscription.index')
                ->with('error', 'Срок действия платежа истек. Создайте новый.');
        }

        // Получаем доступные шлюзы для типа оплаты
        $paymentType = $invoice->payment_type ?? 'subscription';
        $availableGateways = $this->settingsService->getAllowedGatewaysForType($paymentType);

        // Если нет доступных шлюзов
        if (empty($availableGateways)) {
            return redirect()->route('subscription.index')
                ->with('error', 'Нет доступных способов оплаты. Обратитесь в поддержку.');
        }

        // Выбранный шлюз (из запроса или из инвойса или первый доступный)
        $selectedGateway = $request->input('gateway', $invoice->gateway ?? $availableGateways[0] ?? null);

        // Проверяем, что выбранный шлюз доступен
        if (! in_array($selectedGateway, $availableGateways)) {
            $selectedGateway = $availableGateways[0];
        }

        // Получаем информацию о шлюзах для отображения
        $gatewaysInfo = $this->getGatewaysInfo($availableGateways);

        // Получаем платёжные методы для выбранного шлюза
        $paymentMethods = $this->getPaymentMethodsForGateway($selectedGateway);

        return view('payment.select-method', [
            'invoice' => $invoice,
            'plan' => $invoice->plan,
            'availableGateways' => $gatewaysInfo,
            'selectedGateway' => $selectedGateway,
            'paymentMethods' => $paymentMethods,
            'showGatewaySelector' => count($availableGateways) > 1,
        ]);
    }

    /**
     * Показать инструкции для оплаты через ЕРИП
     */
    public function eripInstructions(Invoice $invoice)
    {
        $user = Auth::user();

        if ($invoice->user_id !== $user->id) {
            abort(403, 'Доступ запрещен');
        }

        if ($invoice->isPaid()) {
            return redirect()->route('subscription.current')
                ->with('success', 'Платёж успешно оплачен!');
        }

        $eripInvoiceNo = $invoice->metadata['erip_invoice_no'] ?? null;

        if (! $eripInvoiceNo) {
            return redirect()->route('payment.select', $invoice)
                ->with('error', 'Счёт ЕРИП не найден. Выберите способ оплаты заново.');
        }

        return view('payment.erip-instructions', [
            'invoice' => $invoice,
            'plan' => $invoice->plan,
            'eripInvoiceNo' => $eripInvoiceNo,
            'invoiceUrl' => $invoice->gateway_payment_url,
        ]);
    }

    /**
     * Генерация QR-кода для оплаты
     */
    public function paymentQr(Invoice $invoice, Request $request)
    {
        $user = Auth::user();

        if ($invoice->user_id !== $user->id) {
            abort(403);
        }

        $url = $invoice->gateway_payment_url;

        if (empty($url)) {
            abort(404);
        }

        $size = (int) $request->query('size', 200);
        $size = max(100, min(500, $size));

        $builder = new Builder(data: $url, size: $size, margin: 10);
        $result = $builder->build();

        return response($result->getString(), 200, [
            'Content-Type' => $result->getMimeType(),
            'Cache-Control' => 'private, max-age=3600',
        ]);
    }

    /**
     * Обработать выбор способа оплаты и перенаправить на оплату
     */
    public function process(Request $request, Invoice $invoice)
    {
        $user = Auth::user();

        // Проверяем, что инвойс принадлежит пользователю
        if ($invoice->user_id !== $user->id) {
            abort(403, 'Доступ запрещен');
        }

        // Проверяем, что инвойс ещё не оплачен
        if ($invoice->isPaid()) {
            return redirect()->route('subscription.current')
                ->with('info', 'Этот платеж уже оплачен.');
        }

        // Проверяем, не истёк ли срок
        if ($invoice->isExpired()) {
            return redirect()->route('subscription.index')
                ->with('error', 'Срок действия платежа истек. Создайте новый.');
        }

        $request->validate([
            'gateway' => 'required|string',
            'payment_system_id' => 'required',
        ]);

        $gateway = $request->input('gateway');
        $paymentSystemId = $request->input('payment_system_id');

        // Проверяем, что шлюз доступен
        $paymentType = $invoice->payment_type ?? 'subscription';
        $availableGateways = $this->settingsService->getAllowedGatewaysForType($paymentType);

        if (! in_array($gateway, $availableGateways)) {
            return redirect()->back()
                ->with('error', 'Выбранный способ оплаты недоступен.');
        }

        // ЕРИП: если у инвойса уже есть счёт ЕРИП — не создаём новый, сразу на инструкции
        if ($gateway === 'expresspay' && $paymentSystemId === 'erip') {
            $eripInvoiceNo = $invoice->metadata['erip_invoice_no'] ?? null;
            if ($eripInvoiceNo) {
                return redirect()->route('payment.erip.instructions', ['invoice' => $invoice])
                    ->with('info', 'Используйте ранее созданный счёт ЕРИП для оплаты.');
            }
        }

        // Обновляем шлюз в инвойсе (валюту не меняем — она определяется при создании инвойса)
        $invoice->update(['gateway' => $gateway]);

        try {
            // Создаём платёж с выбранной платёжной системой
            $result = $this->paymentService->createPaymentForInvoice($invoice, $gateway, [
                'payment_system_id' => $paymentSystemId,
            ]);

            if (! $result->isSuccessful()) {
                Log::error('Payment creation failed', [
                    'invoice_id' => $invoice->id,
                    'gateway' => $gateway,
                    'payment_system_id' => $paymentSystemId,
                    'error' => $result->errorMessage,
                ]);

                return redirect()->back()
                    ->with('error', 'Ошибка при создании платежа. Попробуйте другой способ оплаты.');
            }

            // Сохраняем invoice_id в сессию для success/fail callback
            session(['payment_invoice_id' => $invoice->id]);

            // Редирект на страницу оплаты
            return redirect($result->redirectUrl);

        } catch (\Exception $e) {
            Log::error('Payment creation exception', [
                'invoice_id' => $invoice->id,
                'error' => $e->getMessage(),
            ]);

            return redirect()->back()
                ->with('error', 'Ошибка при создании платежа: '.$e->getMessage());
        }
    }

    /**
     * Получить информацию о шлюзах
     */
    protected function getGatewaysInfo(array $gateways): array
    {
        $info = [];

        foreach ($gateways as $gateway) {
            $config = config("payments.gateways.{$gateway}", []);

            $info[$gateway] = [
                'name' => $gateway,
                'display_name' => $config['display_name'] ?? $gateway,
                'currency' => $config['default_currency'] ?? 'BYN',
                'icon' => $this->getGatewayIcon($gateway),
                'description' => $this->getGatewayDescription($gateway),
            ];
        }

        return $info;
    }

    /**
     * Получить иконку шлюза
     */
    protected function getGatewayIcon(string $gateway): string
    {
        return match ($gateway) {
            'bepaid' => 'fa-credit-card',
            'freekassa' => 'fa-wallet',
            'expresspay' => 'fa-building-columns',
            default => 'fa-credit-card',
        };
    }

    /**
     * Получить описание шлюза
     */
    protected function getGatewayDescription(string $gateway): string
    {
        return match ($gateway) {
            'bepaid' => 'Visa, MasterCard, Белкарт',
            'freekassa' => 'Карты, СБП, электронные кошельки',
            'expresspay' => 'Visa, MasterCard, Белкарт, ЕРИП',
            default => '',
        };
    }

    /**
     * Получить платёжные методы для шлюза
     */
    protected function getPaymentMethodsForGateway(string $gateway): array
    {
        return match ($gateway) {
            'freekassa' => $this->getFreekassaPaymentMethods(),
            'bepaid' => $this->getBepaidPaymentMethods(),
            'expresspay' => $this->getExpressPayPaymentMethods(),
            default => [],
        };
    }

    /**
     * Получить платёжные системы FreeKassa
     */
    protected function getFreekassaPaymentMethods(): array
    {
        try {
            $gateway = $this->gatewayManager->get('freekassa');

            // Пробуем получить через API
            $currencies = $gateway->getAvailableCurrencies();

            if (! empty($currencies)) {
                return collect($currencies)
                    ->filter(fn ($c) => ($c['is_enabled'] ?? 1) == 1)
                    ->map(fn ($c) => [
                        'id' => $c['id'],
                        'name' => $c['name'],
                        'currency' => $c['currency'] ?? 'BYN',
                        'icon' => $this->getPaymentMethodIcon($c['id'], $c['name']),
                        'description' => $this->getPaymentMethodDescription($c['id']),
                    ])
                    ->values()
                    ->toArray();
            }
        } catch (\Exception $e) {
            Log::warning('Failed to get FreeKassa currencies via API', ['error' => $e->getMessage()]);
        }

        // Fallback: возвращаем популярные методы
        return [
            ['id' => 4, 'name' => 'Банковская карта (Visa/MC)', 'icon' => 'fa-credit-card', 'description' => 'Visa, MasterCard'],
            ['id' => 12, 'name' => 'Карта МИР', 'icon' => 'fa-credit-card', 'description' => 'Национальная платёжная система'],
            ['id' => 42, 'name' => 'СБП', 'icon' => 'fa-mobile-screen', 'description' => 'Система быстрых платежей'],
            ['id' => 6, 'name' => 'ЮMoney', 'icon' => 'fa-wallet', 'description' => 'Электронный кошелёк'],
            ['id' => 10, 'name' => 'QIWI', 'icon' => 'fa-wallet', 'description' => 'QIWI Кошелёк'],
        ];
    }

    /**
     * Получить платёжные системы bePaid
     */
    protected function getBepaidPaymentMethods(): array
    {
        return [
            ['id' => 'card', 'name' => 'Банковская карта', 'icon' => 'fa-credit-card', 'description' => 'Visa, MasterCard, Белкарт'],
        ];
    }

    /**
     * Получить платёжные системы Express Pay
     */
    protected function getExpressPayPaymentMethods(): array
    {
        return [
            [
                'id' => 'erip',
                'name' => 'ЕРИП (Расчёт)',
                'icon' => 'fa-building-columns',
                'description' => 'Оплата через интернет-банкинг',
            ],
            [
                'id' => 'card',
                'name' => 'Банковская карта',
                'icon' => 'fa-credit-card',
                'description' => 'Visa, MasterCard, Белкарт (временно недоступно)',
                'disabled' => true,
            ],
        ];
    }

    /**
     * Получить иконку для платёжной системы
     */
    protected function getPaymentMethodIcon(int $id, string $name): string
    {
        $icons = [
            4 => 'fa-brands fa-cc-visa',
            8 => 'fa-brands fa-cc-mastercard',
            12 => 'fa-credit-card',
            42 => 'fa-mobile-screen',
            6 => 'fa-wallet',
            10 => 'fa-wallet',
            24 => 'fa-brands fa-bitcoin',
            14 => 'fa-coins',
            15 => 'fa-coins',
        ];

        if (isset($icons[$id])) {
            return $icons[$id];
        }

        // Определяем по названию
        $nameLower = mb_strtolower($name);
        if (str_contains($nameLower, 'visa')) {
            return 'fa-brands fa-cc-visa';
        }
        if (str_contains($nameLower, 'master')) {
            return 'fa-brands fa-cc-mastercard';
        }
        if (str_contains($nameLower, 'мир')) {
            return 'fa-credit-card';
        }
        if (str_contains($nameLower, 'sbp') || str_contains($nameLower, 'сбп')) {
            return 'fa-mobile-screen';
        }
        if (str_contains($nameLower, 'qiwi') || str_contains($nameLower, 'киви')) {
            return 'fa-wallet';
        }
        if (str_contains($nameLower, 'yoomoney') || str_contains($nameLower, 'юmoney')) {
            return 'fa-wallet';
        }
        if (str_contains($nameLower, 'bitcoin') || str_contains($nameLower, 'btc')) {
            return 'fa-brands fa-bitcoin';
        }
        if (str_contains($nameLower, 'usdt') || str_contains($nameLower, 'tether')) {
            return 'fa-coins';
        }
        if (str_contains($nameLower, 'ethereum') || str_contains($nameLower, 'eth')) {
            return 'fa-brands fa-ethereum';
        }

        return 'fa-credit-card';
    }

    /**
     * Получить описание платёжной системы
     */
    protected function getPaymentMethodDescription(int $id): string
    {
        $descriptions = [
            4 => 'Visa, MasterCard (RUB)',
            8 => 'MasterCard (RUB)',
            12 => 'Карта МИР',
            42 => 'Система быстрых платежей',
            6 => 'Электронный кошелёк ЮMoney',
            10 => 'QIWI Кошелёк',
            24 => 'Криптовалюта Bitcoin',
            14 => 'USDT (ERC20)',
            15 => 'USDT (TRC20)',
        ];

        return $descriptions[$id] ?? '';
    }
}
