<?php

declare(strict_types=1);

namespace App\Services\Gateways;

use App\DTO\Payment\PaymentResult;
use App\DTO\Payment\PaymentStatus;
use App\DTO\Payment\RefundResult;
use App\DTO\Payment\WebhookData;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

/**
 * Шлюз для Express Pay (express-pay.by)
 *
 * Поддерживает:
 * - Оплату банковскими картами (Visa, MasterCard, Белкарт)
 * - Оплату через ЕРИП (EPOS)
 */
class ExpressPayGateway extends AbstractGateway
{
    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'expresspay';
    }

    /**
     * URL API
     */
    protected const API_URL = 'https://api.express-pay.by/v1/';

    protected const SANDBOX_API_URL = 'https://sandbox-api.express-pay.by/v1/';

    /**
     * Методы оплаты
     */
    public const METHOD_CARD = 'card';

    public const METHOD_ERIP = 'erip';

    /**
     * Коды валют
     */
    protected const CURRENCY_CODES = [
        'BYN' => 933,
        'RUB' => 643,
        'USD' => 840,
        'EUR' => 978,
    ];

    /**
     * Статусы счетов
     */
    protected const STATUS_PENDING = 1;      // Ожидает оплату

    protected const STATUS_EXPIRED = 2;      // Просрочен

    protected const STATUS_PAID = 3;         // Оплачен

    protected const STATUS_PAID_PARTIAL = 4; // Оплачен частично

    protected const STATUS_CANCELLED = 5;    // Отменен

    protected const STATUS_PAID_CARD = 6;    // Оплачен картой

    protected const STATUS_REFUNDED = 7;     // Возвращен

    /**
     * {@inheritdoc}
     *
     * @param  array  $options  Опции:
     *                          - payment_method: 'card' (по умолчанию) или 'erip'
     *                          - description: описание платежа
     */
    public function createPayment(Invoice $invoice, array $options = []): PaymentResult
    {
        $paymentMethod = $options['payment_method'] ?? self::METHOD_ERIP;

        return match ($paymentMethod) {
            self::METHOD_ERIP => $this->createEripPayment($invoice, $options),
            self::METHOD_CARD => $this->createCardPayment($invoice, $options),
            default => $this->createEripPayment($invoice, $options),
        };
    }

    /**
     * Создать платёж через ЕРИП (EPOS)
     */
    public function createEripPayment(Invoice $invoice, array $options = []): PaymentResult
    {
        $token = $this->getConfig('token');
        $secretWord = $this->getConfig('secret_word');

        if (empty($token)) {
            return PaymentResult::failure(
                'Express Pay не настроен. Укажите EXPRESSPAY_TOKEN в .env.'
            );
        }

        $amount = number_format((float) $invoice->amount, 2, ',', '');
        $currency = self::CURRENCY_CODES[$invoice->currency] ?? self::CURRENCY_CODES['BYN'];
        $accountNo = (string) $invoice->id;
        $expiration = $this->getExpirationYmd($invoice);

        $description = $options['description'] ?? $this->getPaymentDescription($invoice);

        $params = [
            'Token' => $token,
            'AccountNo' => $accountNo,
            'Amount' => $amount,
            'Currency' => $currency,
            'Expiration' => $expiration,
            'Info' => mb_substr($description, 0, 1024),
            'IsNameEditable' => 1,
            'IsAddressEditable' => 1,
            'IsAmountEditable' => 0,
            'ReturnInvoiceUrl' => 1,
        ];

        if (! empty($secretWord)) {
            $params['signature'] = $this->computeSignature($params, $secretWord, 'add-invoice');
        }

        $this->log('Creating ERIP invoice', [
            'invoice_id' => $invoice->id,
            'amount' => $amount,
            'currency' => $currency,
        ]);

        try {
            $apiUrl = $this->getApiUrl().'invoices?token='.$token;

            $response = Http::asForm()->post($apiUrl, $params);
            $responseData = $response->json();

            $this->log('ERIP API response', [
                'invoice_id' => $invoice->id,
                'status' => $response->status(),
                'response' => $responseData,
            ]);

            if (isset($responseData['Error'])) {
                return PaymentResult::failure(
                    'Ошибка Express Pay: '.($responseData['Error']['Msg'] ?? 'Неизвестная ошибка')
                );
            }

            $eripInvoiceNo = $responseData['InvoiceNo'] ?? null;

            if (! $eripInvoiceNo) {
                return PaymentResult::failure('Не получен номер счета ЕРИП от Express Pay');
            }

            $invoiceUrl = $responseData['InvoiceUrl'] ?? null;

            $invoice->update([
                'gateway_invoice_id' => 'erip_'.$eripInvoiceNo,
                'gateway_payment_url' => $invoiceUrl,
                'metadata' => array_merge($invoice->metadata ?? [], [
                    'erip_invoice_no' => $eripInvoiceNo,
                    'payment_method' => 'erip',
                ]),
            ]);

            return PaymentResult::success(
                transactionId: 'erip_'.$eripInvoiceNo,
                redirectUrl: $invoiceUrl ? route('payment.erip.instructions', ['invoice' => $invoice->id]) : null,
                rawResponse: array_merge($responseData, [
                    'erip_invoice_no' => $eripInvoiceNo,
                    'payment_method' => 'erip',
                ]),
            );

        } catch (\Exception $e) {
            $this->log('ERIP payment creation failed', [
                'invoice_id' => $invoice->id,
                'error' => $e->getMessage(),
            ], 'error');

            return PaymentResult::failure(
                'Ошибка соединения с Express Pay: '.$e->getMessage()
            );
        }
    }

    /**
     * Создать платёж банковской картой
     */
    public function createCardPayment(Invoice $invoice, array $options = []): PaymentResult
    {
        $token = $this->getConfig('token');
        $secretWord = $this->getConfig('secret_word');

        if (empty($token)) {
            return PaymentResult::failure(
                'Express Pay не настроен. Укажите EXPRESSPAY_TOKEN в .env.'
            );
        }

        $amount = number_format((float) $invoice->amount, 2, ',', '');
        $currency = self::CURRENCY_CODES[$invoice->currency] ?? self::CURRENCY_CODES['BYN'];
        $accountNo = (string) $invoice->id;
        $expiration = $this->getExpirationYmd($invoice);

        $description = $options['description'] ?? $this->getPaymentDescription($invoice);

        $params = [
            'Token' => $token,
            'AccountNo' => $accountNo,
            'Amount' => $amount,
            'Currency' => $currency,
            'Expiration' => $expiration,
            'Info' => mb_substr($description, 0, 1024),
            'ReturnUrl' => route('subscription.payment.success', ['invoice' => $invoice->id]),
            'FailUrl' => route('subscription.payment.fail', ['invoice' => $invoice->id]),
            'Language' => 'ru',
            'SessionTimeoutSecs' => 1200,
            'ReturnInvoiceUrl' => 1,
        ];

        if (! empty($secretWord)) {
            $params['signature'] = $this->computeSignature($params, $secretWord, 'add-card-invoice');
        }

        $this->log('Creating card invoice', [
            'invoice_id' => $invoice->id,
            'amount' => $amount,
            'currency' => $currency,
        ]);

        try {
            $apiUrl = $this->getApiUrl().'cardinvoices?token='.$token;

            $response = Http::asForm()->post($apiUrl, $params);
            $responseData = $response->json();

            $this->log('Card API response', [
                'invoice_id' => $invoice->id,
                'status' => $response->status(),
                'response' => $responseData,
            ]);

            if (isset($responseData['Error'])) {
                return PaymentResult::failure(
                    'Ошибка Express Pay: '.($responseData['Error']['Msg'] ?? 'Неизвестная ошибка')
                );
            }

            $cardInvoiceNo = $responseData['CardInvoiceNo'] ?? null;

            if (! $cardInvoiceNo) {
                return PaymentResult::failure('Не получен номер счета от Express Pay');
            }

            $formUrl = $this->getPaymentFormUrl($cardInvoiceNo, $token, $secretWord);

            if (! $formUrl) {
                return PaymentResult::failure('Не удалось получить форму оплаты');
            }

            $invoice->update([
                'gateway_invoice_id' => 'card_'.$cardInvoiceNo,
                'gateway_payment_url' => $formUrl,
                'metadata' => array_merge($invoice->metadata ?? [], [
                    'card_invoice_no' => $cardInvoiceNo,
                    'payment_method' => 'card',
                ]),
            ]);

            return PaymentResult::success(
                transactionId: 'card_'.$cardInvoiceNo,
                redirectUrl: $formUrl,
                rawResponse: array_merge($responseData, [
                    'card_invoice_no' => $cardInvoiceNo,
                    'payment_method' => 'card',
                ]),
            );

        } catch (\Exception $e) {
            $this->log('Card payment creation failed', [
                'invoice_id' => $invoice->id,
                'error' => $e->getMessage(),
            ], 'error');

            return PaymentResult::failure(
                'Ошибка соединения с Express Pay: '.$e->getMessage()
            );
        }
    }

    /**
     * Получить URL формы оплаты
     */
    protected function getPaymentFormUrl(int $cardInvoiceNo, string $token, ?string $secretWord): ?string
    {
        $params = [
            'Token' => $token,
            'CardInvoiceNo' => (string) $cardInvoiceNo,
        ];

        if (! empty($secretWord)) {
            $params['signature'] = $this->computeSignature($params, $secretWord, 'get-form-url');
        }

        try {
            $url = $this->getApiUrl()."cardinvoices/{$cardInvoiceNo}/payment?".http_build_query($params);

            $response = Http::get($url);
            $responseData = $response->json();

            $this->log('Get payment form response', [
                'card_invoice_no' => $cardInvoiceNo,
                'response' => $responseData,
            ]);

            return $responseData['FormUrl'] ?? null;

        } catch (\Exception $e) {
            $this->log('Failed to get payment form URL', [
                'card_invoice_no' => $cardInvoiceNo,
                'error' => $e->getMessage(),
            ], 'error');

            return null;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function checkPaymentStatus(string $transactionId): PaymentStatus
    {
        // Определяем тип счёта по префиксу
        if (str_starts_with($transactionId, 'erip_')) {
            return $this->checkEripPaymentStatus(substr($transactionId, 5));
        }

        if (str_starts_with($transactionId, 'card_')) {
            return $this->checkCardPaymentStatus(substr($transactionId, 5));
        }

        // Для обратной совместимости — пробуем как карточный
        return $this->checkCardPaymentStatus($transactionId);
    }

    /**
     * Проверить статус платежа ЕРИП
     */
    protected function checkEripPaymentStatus(string $invoiceNo): PaymentStatus
    {
        $token = $this->getConfig('token');
        $secretWord = $this->getConfig('secret_word');

        $params = [
            'Token' => $token,
            'InvoiceNo' => $invoiceNo,
        ];

        if (! empty($secretWord)) {
            $params['signature'] = $this->computeSignature($params, $secretWord, 'get-details-invoice');
        }

        try {
            $url = $this->getApiUrl()."invoices/{$invoiceNo}?".http_build_query($params);

            $response = Http::get($url);
            $responseData = $response->json();

            $this->log('Check ERIP payment status response', [
                'invoice_no' => $invoiceNo,
                'response' => $responseData,
            ]);

            if (isset($responseData['Error'])) {
                return new PaymentStatus(
                    status: 'unknown',
                    transactionId: 'erip_'.$invoiceNo,
                    message: $responseData['Error']['Msg'] ?? 'Ошибка проверки статуса',
                    rawResponse: $responseData,
                );
            }

            $status = $responseData['Status'] ?? null;

            return $this->mapStatusToPaymentStatus($status, 'erip_'.$invoiceNo, $responseData);

        } catch (\Exception $e) {
            $this->log('Check ERIP payment status failed', [
                'invoice_no' => $invoiceNo,
                'error' => $e->getMessage(),
            ], 'error');

            return new PaymentStatus(
                status: 'unknown',
                transactionId: 'erip_'.$invoiceNo,
                message: $e->getMessage(),
            );
        }
    }

    /**
     * Проверить статус карточного платежа
     */
    protected function checkCardPaymentStatus(string $cardInvoiceNo): PaymentStatus
    {
        $token = $this->getConfig('token');
        $secretWord = $this->getConfig('secret_word');

        $params = [
            'Token' => $token,
            'CardInvoiceNo' => $cardInvoiceNo,
        ];

        if (! empty($secretWord)) {
            $params['signature'] = $this->computeSignature($params, $secretWord, 'status-card-invoice');
        }

        try {
            $url = $this->getApiUrl()."cardinvoices/{$cardInvoiceNo}/status?".http_build_query($params);

            $response = Http::get($url);
            $responseData = $response->json();

            $this->log('Check card payment status response', [
                'card_invoice_no' => $cardInvoiceNo,
                'response' => $responseData,
            ]);

            if (isset($responseData['Error'])) {
                return new PaymentStatus(
                    status: 'unknown',
                    transactionId: 'card_'.$cardInvoiceNo,
                    message: $responseData['Error']['Msg'] ?? 'Ошибка проверки статуса',
                    rawResponse: $responseData,
                );
            }

            $status = $responseData['Status'] ?? null;

            return $this->mapStatusToPaymentStatus($status, 'card_'.$cardInvoiceNo, $responseData);

        } catch (\Exception $e) {
            $this->log('Check card payment status failed', [
                'card_invoice_no' => $cardInvoiceNo,
                'error' => $e->getMessage(),
            ], 'error');

            return new PaymentStatus(
                status: 'unknown',
                transactionId: 'card_'.$cardInvoiceNo,
                message: $e->getMessage(),
            );
        }
    }

    /**
     * Преобразовать код статуса Express Pay в PaymentStatus
     */
    protected function mapStatusToPaymentStatus(?int $status, string $transactionId, array $responseData): PaymentStatus
    {
        return match ($status) {
            self::STATUS_PAID, self::STATUS_PAID_CARD => new PaymentStatus(
                status: PaymentStatus::STATUS_SUCCESSFUL,
                transactionId: $transactionId,
                amount: isset($responseData['Amount']) ? (float) str_replace(',', '.', $responseData['Amount']) : null,
                rawResponse: $responseData,
            ),
            self::STATUS_PENDING => new PaymentStatus(
                status: PaymentStatus::STATUS_PENDING,
                transactionId: $transactionId,
                rawResponse: $responseData,
            ),
            self::STATUS_CANCELLED, self::STATUS_EXPIRED => new PaymentStatus(
                status: PaymentStatus::STATUS_FAILED,
                transactionId: $transactionId,
                message: 'Платеж отменен или просрочен',
                rawResponse: $responseData,
            ),
            self::STATUS_REFUNDED => new PaymentStatus(
                status: PaymentStatus::STATUS_REFUNDED,
                transactionId: $transactionId,
                rawResponse: $responseData,
            ),
            default => new PaymentStatus(
                status: 'unknown',
                transactionId: $transactionId,
                rawResponse: $responseData,
            ),
        };
    }

    /**
     * {@inheritdoc}
     */
    public function refund(Invoice $invoice, ?float $amount = null): RefundResult
    {
        // Express Pay не поддерживает возвраты через API напрямую
        // Возвраты делаются через личный кабинет
        return RefundResult::notSupported();
    }

    /**
     * {@inheritdoc}
     */
    public function validateWebhook(Request $request): bool
    {
        $secretWord = $this->getConfig('secret_word');
        $useSignature = $this->getConfig('use_signature', false);

        $data = $request->input('Data');
        $signature = $request->input('Signature');

        if (empty($data)) {
            $this->log('Webhook validation failed: no Data', [], 'warning');

            return false;
        }

        // Если подпись не используется — пропускаем проверку
        if (! $useSignature || empty($secretWord)) {
            return true;
        }

        if (empty($signature)) {
            $this->log('Webhook validation failed: no Signature', [], 'warning');

            return false;
        }

        $expectedSignature = $this->computeWebhookSignature($data, $secretWord);
        $isValid = hash_equals($expectedSignature, $signature);

        if (! $isValid) {
            $this->log('Webhook validation failed: invalid signature', [
                'expected' => $expectedSignature,
                'received' => $signature,
            ], 'warning');
        }

        return $isValid;
    }

    /**
     * {@inheritdoc}
     */
    public function parseWebhook(Request $request): WebhookData
    {
        $dataJson = $request->input('Data');
        $data = json_decode($dataJson, true) ?? [];

        $cmdType = isset($data['CmdType']) ? (int) $data['CmdType'] : null;

        // ЕРИП (CmdType 4): InvoiceNumber = номер лицевого счёта (= наш invoice->id)
        // E-POS (CmdType 5): AccountNumber = номер счёта для оплаты (= наш invoice->id)
        // Карты (CmdType 1,2,3,11): AccountNo = наш invoice->id
        $orderId = $data['InvoiceNumber'] ?? $data['AccountNumber'] ?? $data['AccountNo'] ?? null;
        $orderId = $orderId !== null ? (string) $orderId : null;

        $invoiceNo = $data['InvoiceNo'] ?? null;
        $paymentNo = $data['PaymentNo'] ?? null;

        $amount = null;
        if (isset($data['MoneyAmmount'])) {
            $amount = (float) str_replace(',', '.', (string) $data['MoneyAmmount']);
        } elseif (isset($data['TransferredMoneyAmount'])) {
            $amount = (float) str_replace(',', '.', (string) $data['TransferredMoneyAmount']);
        } elseif (isset($data['Amount'])) {
            $amount = (float) str_replace(',', '.', (string) $data['Amount']);
        }

        $statusCode = $data['Status'] ?? null;

        // Определяем статус по типу уведомления
        // 1 = новый платёж (карты), 2 = отмена, 3 = изменение статуса (карты)
        // 4 = зачисление ЕРИП, 5 = зачисление E-POS, 11 = возврат
        $status = match ($cmdType) {
            1 => 'successful',                    // Поступление нового платежа (карты)
            2 => 'failed',                        // Отмена платежа
            3 => $this->mapStatusCodeToStatus($statusCode), // Изменение статуса (карты)
            4 => 'successful',                   // Зачисление средств для ЕРИП
            5 => 'successful',                   // Зачисление средств для E-POS
            11 => 'refunded',                    // Возврат платежа
            default => 'unknown',
        };

        $transactionId = (string) ($paymentNo ?? $invoiceNo ?? $data['TransactionId'] ?? $data['OperationNumberInAgentOffice'] ?? '0');
        $currency = $data['Currency'] ?? 'BYN';

        $this->log('Webhook parsed', [
            'cmd_type' => $cmdType,
            'order_id' => $orderId,
            'transaction_id' => $transactionId,
            'amount' => $amount,
            'status' => $status,
        ]);

        return new WebhookData(
            status: $status,
            transactionId: $transactionId,
            orderId: $orderId,
            amount: $amount,
            currency: $currency,
            rawData: $data,
        );
    }

    /**
     * Маппинг кода статуса в строковый статус
     */
    protected function mapStatusCodeToStatus(?int $statusCode): string
    {
        return match ($statusCode) {
            self::STATUS_PAID, self::STATUS_PAID_CARD => 'successful',
            self::STATUS_PENDING => 'pending',
            self::STATUS_CANCELLED, self::STATUS_EXPIRED => 'failed',
            self::STATUS_REFUNDED => 'refunded',
            default => 'unknown',
        };
    }

    /**
     * Получить URL API (тестовый или боевой)
     */
    protected function getApiUrl(): string
    {
        return $this->isTestMode() ? self::SANDBOX_API_URL : self::API_URL;
    }

    /**
     * Сформировать цифровую подпись для запроса
     */
    protected function computeSignature(array $params, string $secretWord, string $action): string
    {
        // Порядок полей для разных действий (из документации Express Pay)
        $fieldMappings = [
            // ERIP (invoices)
            'add-invoice' => [
                'token', 'accountno', 'amount', 'currency', 'expiration',
                'info', 'surname', 'firstname', 'patronymic', 'city',
                'street', 'house', 'building', 'apartment', 'isnameeditable',
                'isaddresseditable', 'isamounteditable', 'emailnotification',
                'returninvoiceurl',
            ],
            'get-details-invoice' => ['token', 'invoiceno'],
            'cancel-invoice' => ['token', 'invoiceno'],
            'get-list-invoices' => [
                'token', 'from', 'to', 'accountno', 'status',
            ],

            // Card invoices
            'add-card-invoice' => [
                'token', 'accountno', 'expiration', 'amount', 'currency', 'info',
                'returnurl', 'failurl', 'language', 'pageview', 'sessiontimeoutsecs',
                'expirationdate', 'returninvoiceurl',
            ],
            'get-form-url' => ['token', 'cardinvoiceno'],
            'status-card-invoice' => ['token', 'cardinvoiceno'],
            'cancel-card-invoice' => ['token', 'cardinvoiceno'],
        ];

        $mapping = $fieldMappings[$action] ?? array_keys($params);

        $normalizedParams = array_change_key_case($params, CASE_LOWER);

        $signString = '';
        foreach ($mapping as $field) {
            $signString .= $normalizedParams[$field] ?? '';
        }

        return strtoupper(hash_hmac('sha1', $signString, $secretWord));
    }

    /**
     * Сформировать цифровую подпись для webhook
     */
    protected function computeWebhookSignature(string $json, string $secretWord): string
    {
        return strtoupper(hash_hmac('sha1', $json, $secretWord));
    }

    /**
     * Дата истечения счёта в формате Ymd для API (из инвойса или конфига).
     */
    protected function getExpirationYmd(Invoice $invoice): string
    {
        return $invoice->expires_at
            ? $invoice->expires_at->format('Ymd')
            : now()->addDays(config('payments.default_invoice_expiration_days', 7))->format('Ymd');
    }

    /**
     * Получить описание платежа
     */
    protected function getPaymentDescription(Invoice $invoice): string
    {
        $planName = $invoice->plan?->name ?? 'Подписка';

        return "Оплата: {$planName}";
    }
}
