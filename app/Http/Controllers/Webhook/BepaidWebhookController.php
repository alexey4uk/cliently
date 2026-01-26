<?php

namespace App\Http\Controllers\Webhook;

use App\Http\Controllers\Controller;
use App\Models\BepaidSettings;
use App\Models\Invoice;
use App\Services\BepaidService;
use App\Services\SubscriptionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BepaidWebhookController extends Controller
{
    protected BepaidService $bepaidService;

    protected SubscriptionService $subscriptionService;

    public function __construct(BepaidService $bepaidService, SubscriptionService $subscriptionService)
    {
        $this->bepaidService = $bepaidService;
        $this->subscriptionService = $subscriptionService;
    }

    /**
     * Обработка webhook от bePaid
     *
     * ВАЖНО: Как работает HTTP Basic Auth для webhook bePaid
     *
     * 1. При создании платежа (BepaidService::createPaymentToken):
     *    - Мы указываем webhook URL через setNotificationUrl()
     *    - bePaid сохраняет этот URL для отправки уведомлений
     *
     * 2. Когда bePaid отправляет webhook:
     *    - bePaid АВТОМАТИЧЕСКИ формирует заголовок Authorization с Basic Auth
     *    - Формат: Authorization: Basic base64(shop_id:secret_key)
     *    - shop_id и secret_key берутся из настроек магазина в системе bePaid
     *    - Это те же credentials, которые мы указываем в админ-панели
     *
     * 3. На нашей стороне (validateBasicAuth):
     *    - Извлекаем shop_id и secret_key из заголовка Authorization
     *    - Сравниваем с настройками из нашей БД (BepaidSettings)
     *    - Если совпадают - пропускаем, если нет - возвращаем 401
     *
     * ВАЖНО: Мы НЕ задаем Basic Auth на стороне bePaid!
     * bePaid сам использует shop_id и secret_key из настроек магазина.
     * Мы только проверяем, что пришедшие credentials совпадают с нашими.
     *
     * bePaid отправляет webhook с HTTP Basic Auth
     * Данные приходят в формате JSON
     */
    public function handle(Request $request)
    {
        try {
            // Логируем входящий запрос для отладки
            // В логах будет видно заголовок Authorization с Basic Auth
            if (config('bepaid.logging.enabled')) {
                Log::info('bePaid webhook received', [
                    'headers' => $request->headers->all(),
                    'body' => $request->all(),
                ]);
            }

            // КРИТИЧЕСКИ ВАЖНО: Проверка HTTP Basic Auth от bePaid
            // bePaid автоматически отправляет shop_id и secret_key в заголовке Authorization
            // Мы проверяем, что они совпадают с нашими настройками из БД
            // Если не совпадают - это может быть поддельный webhook
            if (! $this->validateBasicAuth($request)) {
                Log::warning('bePaid webhook: invalid Basic Auth credentials');

                return response()->json(['error' => 'Unauthorized'], 401);
            }

            // Получаем данные из запроса
            $data = $request->all();

            if (empty($data)) {
                // Пытаемся получить из JSON
                $data = json_decode($request->getContent(), true);
            }

            if (empty($data)) {
                Log::warning('bePaid webhook: empty data');

                return response()->json(['error' => 'Empty data'], 400);
            }

            // Валидация данных через сервис
            if (! $this->bepaidService->validateWebhookRequest($data)) {
                Log::warning('bePaid webhook: invalid data', ['data' => $data]);

                return response()->json(['error' => 'Invalid data'], 400);
            }

            // Обрабатываем webhook через сервис
            $invoice = $this->bepaidService->processWebhook($data);

            if (! $invoice) {
                Log::warning('bePaid webhook: invoice not found');

                return response()->json(['error' => 'Invoice not found'], 404);
            }

            // Если платеж успешен, активируем/продлеваем подписку
            if ($invoice->isPaid()) {
                $this->activateSubscription($invoice);
            } elseif ($invoice->status === 'failed') {
                // Уведомляем о неудачной оплате
                \App\Services\SubscriptionNotificationService::notifyPaymentFailed($invoice);
            }

            // Возвращаем успешный ответ
            return response()->json(['status' => 'ok'], 200);

        } catch (\RuntimeException $e) {
            // Ошибки валидации и бизнес-логики - возвращаем 400
            Log::warning('bePaid webhook: validation error', [
                'error' => $e->getMessage(),
                'data' => $request->all(),
            ]);

            return response()->json(['error' => $e->getMessage()], 400);
        } catch (\Exception $e) {
            // Неожиданные ошибки - возвращаем 500, чтобы bePaid мог повторить запрос
            Log::error('bePaid webhook error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'data' => $request->all(),
            ]);

            return response()->json(['error' => 'Processing error'], 500);
        }
    }

    /**
     * Проверка HTTP Basic Auth от bePaid
     *
     * КАК ЭТО РАБОТАЕТ:
     *
     * 1. bePaid отправляет webhook с заголовком:
     *    Authorization: Basic base64(shop_id:secret_key)
     *
     * 2. shop_id и secret_key берутся из настроек магазина в системе bePaid
     *    (это те же данные, которые мы указываем в админ-панели)
     *
     * 3. Мы извлекаем shop_id и secret_key из заголовка
     *
     * 4. Сравниваем с настройками из нашей БД (BepaidSettings)
     *    - Если test_mode = true → используем test_shop_id и test_secret_key
     *    - Если test_mode = false → используем production_shop_id и production_secret_key
     *
     * 5. Если совпадают - возвращаем true, если нет - false
     *
     * ВАЖНО: bePaid автоматически формирует этот заголовок при отправке webhook.
     * Мы НЕ задаем Basic Auth на стороне bePaid - они используют свои настройки.
     * Мы только проверяем, что пришедшие credentials совпадают с нашими.
     *
     * @param  Request  $request  HTTP запрос от bePaid
     * @return bool true если credentials валидны, false если нет
     */
    protected function validateBasicAuth(Request $request): bool
    {
        // Получаем заголовок Authorization
        // Формат: "Basic base64(shop_id:secret_key)"
        $authHeader = $request->header('Authorization');

        // Проверяем, что заголовок есть и начинается с "Basic "
        if (! $authHeader || ! str_starts_with($authHeader, 'Basic ')) {
            return false;
        }

        // Декодируем base64 строку из заголовка
        // Убираем "Basic " (6 символов) и декодируем остальное
        // Результат: "shop_id:secret_key"
        $credentials = base64_decode(substr($authHeader, 6));

        // Проверяем, что декодирование прошло успешно и есть разделитель ":"
        if (! $credentials || ! str_contains($credentials, ':')) {
            return false;
        }

        // Разделяем на shop_id и secret_key
        [$shopId, $secretKey] = explode(':', $credentials, 2);

        // Получаем настройки bePaid из нашей БД
        // Используем where()->first() чтобы получить актуальные данные из базы
        $settings = BepaidSettings::where('id', 1)->first();

        // Если настройки не найдены или bePaid не включен - отклоняем
        if (! $settings || ! $settings->enabled) {
            return false;
        }

        // Получаем текущие настройки (test или production в зависимости от test_mode)
        $currentSettings = $settings->getCurrentSettings();

        // Сравниваем shop_id и secret_key из webhook с настройками из БД
        // Используем trim() для удаления возможных пробелов
        $shopIdMatches = trim($shopId) === trim($currentSettings['shop_id'] ?? '');
        $secretKeyMatches = trim($secretKey) === trim($currentSettings['secret_key'] ?? '');

        // Оба значения должны совпадать
        return $shopIdMatches && $secretKeyMatches;
    }

    /**
     * Активировать или продлить подписку после успешной оплаты
     */
    protected function activateSubscription(Invoice $invoice): void
    {
        $subscription = $invoice->subscription;
        $plan = $invoice->plan;
        $user = $invoice->user;

        // Сохраняем старые значения для проверки продления
        $oldEndsAt = $subscription?->ends_at;

        // Идемпотентность: если подписка уже активна и оплачена этим инвойсом - не обновляем
        if ($subscription && $subscription->invoice_id === $invoice->id && $subscription->status === 'active') {
            if (config('bepaid.logging.enabled')) {
                Log::info('Subscription already activated for this invoice', [
                    'subscription_id' => $subscription->id,
                    'invoice_id' => $invoice->id,
                ]);
            }

            return;
        }

        // Если подписки нет, создаем её
        if (! $subscription) {
            $subscription = $this->subscriptionService->createSubscription($user, $plan, false, $invoice);
            Log::info('Subscription created after payment', [
                'subscription_id' => $subscription->id,
                'invoice_id' => $invoice->id,
                'plan_id' => $plan->id,
            ]);
        } else {
            // Обновляем существующую подписку
            $now = now();
            $endsAt = null;
            $oldPlan = $subscription->plan;
            $isPlanChange = $oldPlan && $oldPlan->id !== $plan->id;

            // Проверяем, является ли это продлением (invoice metadata содержит is_renewal)
            $isRenewal = $invoice->metadata['is_renewal'] ?? false;
            
            // Проверяем, нужно ли сохранять ends_at при смене тарифа
            $preserveEndsAt = $invoice->metadata['preserve_ends_at'] ?? false;
            
            // Получаем текущий metadata
            $metadata = $subscription->metadata ?? [];
            
            // Если это смена тарифа и нужно сохранить ends_at (старая подписка еще активна)
            if ($isPlanChange && $preserveEndsAt && $subscription->ends_at && $subscription->ends_at->isFuture()) {
                // Сохраняем оплаченное время от старой подписки
                $endsAt = $subscription->ends_at;
                
                // Сохраняем информацию о предыдущем тарифе в metadata
                $metadata['previous_plan_id'] = $oldPlan->id;
                $metadata['previous_plan_name'] = $oldPlan->name;
                $metadata['preserved_ends_at'] = $subscription->ends_at->toIso8601String();
            } elseif ($isRenewal && $subscription->ends_at && $subscription->ends_at->isFuture()) {
                // Продление: продлеваем от текущего ends_at
                $baseDate = $subscription->ends_at;
                if ($plan->interval === 'monthly') {
                    $endsAt = $baseDate->copy()->addMonth();
                } elseif ($plan->interval === 'yearly') {
                    $endsAt = $baseDate->copy()->addYear();
                }
                
                // При продлении очищаем информацию о предыдущем тарифе (если была)
                unset($metadata['previous_plan_id']);
                unset($metadata['previous_plan_name']);
                unset($metadata['preserved_ends_at']);
            } else {
                // Новая подписка или истекшая: начинаем новый период от текущего момента
                if ($plan->interval === 'monthly') {
                    $endsAt = $now->copy()->addMonth();
                } elseif ($plan->interval === 'yearly') {
                    $endsAt = $now->copy()->addYear();
                }
                
                // При новой подписке очищаем информацию о предыдущем тарифе (если была)
                unset($metadata['previous_plan_id']);
                unset($metadata['previous_plan_name']);
                unset($metadata['preserved_ends_at']);
            }

            $subscription->update([
                'plan_id' => $plan->id, // Меняем план
                'status' => 'active',
                'starts_at' => $now, // starts_at всегда обновляем на текущий момент
                'ends_at' => $endsAt,
                'payment_status' => 'paid',
                'invoice_id' => $invoice->id,
                'cancelled_at' => null, // Снимаем отмену, если была
                'metadata' => $metadata, // Обновляем metadata с информацией о предыдущем тарифе
            ]);

            // Очищаем кеш подписок пользователя
            $user->clearSubscriptionCache();

            Log::info('Subscription activated after payment', [
                'subscription_id' => $subscription->id,
                'invoice_id' => $invoice->id,
                'plan_id' => $plan->id,
            ]);

            // Уведомляем об успешной оплате
            \App\Services\SubscriptionNotificationService::notifyPaymentSuccess($invoice);

            // Проверяем, является ли это продлением (старый ends_at < новый ends_at)
            if ($oldEndsAt && $subscription->ends_at && $oldEndsAt->lt($subscription->ends_at)) {
                \App\Services\SubscriptionNotificationService::notifyRenewed($subscription);
            }
        }
    }
}
