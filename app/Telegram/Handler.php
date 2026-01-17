<?php

namespace App\Telegram;

use App\Models\Appointment;
use App\Models\Business;
use App\Models\Client;
use App\Models\TelegramUserState;
use App\Services\AppointmentSlotService;
use App\Services\TelegramBotService;
use App\Services\TelegramNotificationService;
use Carbon\Carbon;
use DefStudio\Telegraph\Handlers\WebhookHandler;
use DefStudio\Telegraph\Keyboard\Keyboard;
use Illuminate\Support\Facades\Log;

// Импортируем наши новые классы

class Handler extends WebhookHandler
{
    protected AppointmentSlotService $slotService;
    protected TelegramBotService $botService;

    protected ?int $lastMessageId = null;

    public function __construct(AppointmentSlotService $slotService, TelegramBotService $botService)
    {
        $this->slotService = $slotService;
        $this->botService = $botService;
    }

    /**
     * Основной метод для отправки/редактирования сообщений
     */
    protected function replyWithMessage(string $message, ?Keyboard $keyboard = null): void
    {
        Log::info('Starting message reply', [
            'last_message_id' => $this->lastMessageId,
            'message_length' => strlen($message),
            'has_keyboard' => ! is_null($keyboard),
            'message_preview' => substr($message, 0, 50),
        ]);

        try {
            if ($this->lastMessageId) {
                Log::info('Attempting to edit existing message', [
                    'message_id' => $this->lastMessageId,
                ]);

                // Редактируем существующее сообщение
                $this->chat->edit($this->lastMessageId)
                    ->message($message)
                    ->send();

                Log::info('Message edited successfully', [
                    'message_id' => $this->lastMessageId,
                ]);

                // Если есть новая клавиатура, заменяем ее
                if ($keyboard) {
                    $this->chat->replaceKeyboard($this->lastMessageId, $keyboard)->send();
                    Log::info('Keyboard replaced', [
                        'message_id' => $this->lastMessageId,
                    ]);
                }

                // ВАЖНО: Сохраняем ID даже при редактировании
                $this->saveMessageIdToState();
            } else {
                Log::info('No lastMessageId, sending new message');

                // Отправляем новое сообщение
                $response = $this->chat->message($message);

                if ($keyboard) {
                    $response = $response->keyboard($keyboard);
                }

                $result = $response->send();

                // Сохраняем ID сообщения
                $this->lastMessageId = $result->telegraphMessageId();
                Log::info('New message sent', ['message_id' => $this->lastMessageId]);

                // Сохраняем в базу для последующего восстановления
                $this->saveMessageIdToState();
            }
        } catch (\Exception $e) {
            Log::error('Error in replyWithMessage', [
                'error' => $e->getMessage(),
                'last_message_id' => $this->lastMessageId,
            ]);

            // Если редактирование не удалось, отправляем новое сообщение
            $this->lastMessageId = null;

            $response = $this->chat->message($message);

            if ($keyboard) {
                $response = $response->keyboard($keyboard);
            }

            $result = $response->send();
            $this->lastMessageId = $result->telegraphMessageId();

            Log::info('New message sent after error', ['message_id' => $this->lastMessageId]);

            // Сохраняем в базу даже при ошибке
            $this->saveMessageIdToState();
        }

        Log::info('Message reply completed');
    }

    /**
     * Сохраняет ID последнего сообщения в состояние пользователя
     */
    protected function saveMessageIdToState(): void
    {
        try {
            // Получаем userId из callback или message
            $userId = $this->callbackQuery?->from()->id() ?? $this->message?->from()->id();

            Log::info('Attempting to save message ID to state', [
                'user_id' => $userId,
                'last_message_id' => $this->lastMessageId,
            ]);

            if (! $userId || ! $this->lastMessageId) {
                Log::warning('Skipping save - missing data', [
                    'user_id' => $userId,
                    'last_message_id' => $this->lastMessageId,
                ]);

                return;
            }

            // Используем упрощенный метод получения состояния
            $state = $this->botService->getCurrentUserState($userId);

            if ($state) {
                $this->botService->setUserMessageId($userId, $state->business_id, $this->lastMessageId);
                Log::info('Message ID saved to state', [
                    'user_id' => $userId,
                    'business_id' => $state->business_id,
                    'message_id' => $this->lastMessageId,
                    'step' => $state->step,
                ]);
            } else {
                Log::warning('No state found for user', ['user_id' => $userId]);
            }
        } catch (\Exception $e) {
            Log::error('Failed to save message_id', [
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Обработчик для всех текстовых сообщений
     */
    public function handleChatMessage(\Illuminate\Support\Stringable $text): void
    {
        $messageText = $text->toString();
        $userId = $this->message->from()->id();
        $messageId = $this->message->id();

        Log::info('Starting chat message handling', [
            'user_id' => $userId,
            'message_text' => $messageText,
            'timestamp' => now()->toISOString(),
        ]);

        $state = $this->botService->getCurrentUserState($userId);

        if ($state) {
            $business = $state->business;

            Log::info('User state found', [
                'user_id' => $userId,
                'step' => $state->step,
                'last_message_id_in_db' => $state->last_message_id,
                'business_id' => $business?->id,
                'business_name' => $business?->name,
            ]);

            // Восстанавливаем lastMessageId из состояния
            if ($state->last_message_id) {
                $this->lastMessageId = $state->last_message_id;
                Log::info('Restored lastMessageId from state', [
                    'user_id' => $userId,
                    'message_id' => $this->lastMessageId,
                ]);
            } else {
                Log::warning('No last_message_id found in state', ['user_id' => $userId]);
            }

            if ($state && $state->step !== TelegramUserState::STEP_START) {
                // Обработка состояния поиска (без бизнеса)
                if ($state->step === TelegramUserState::STEP_SEARCH) {
                    $this->handleSearchQuery($messageText, $state);
                } else {
                    $this->handleTextMessage($messageText, $state, $business);
                }

                // Удаляем сообщение пользователя после обработки
                Log::info('Deleting user message', [
                    'user_id' => $userId,
                    'message_id' => $messageId,
                ]);
                $this->deleteUserMessage($messageId);

                Log::info('Chat message handling completed', ['user_id' => $userId]);

                return;
            }
        } else {
            Log::warning('No state found for user', ['user_id' => $userId]);
        }

        // На неизвестные сообщения просто отвечаем
        $this->replyWithMessage(TelegramMessages::MSG_START);

        // Удаляем сообщение пользователя
        $this->deleteUserMessage($messageId);

        Log::info('Chat message handling completed', ['user_id' => $userId]);
    }

    /**
     * Команда /start - основная точка входа
     */
    public function start()
    {
        $text = $this->message->text() ?? '';
        $userId = $this->message->from()->id();

        Log::info('Start command received', [
            'user_id' => $userId,
            'text' => $text,
            'timestamp' => now()->toISOString(),
        ]);

        $parts = explode(' ', $text);

        if (isset($parts[1])) {
            if (str_starts_with($parts[1], 'auth_')) {
                // Подключение бизнеса
                $token = str_replace('auth_', '', $parts[1]);
                $business = $this->botService->findBusinessByToken($token);

                if ($business) {
                    $this->botService->updateBusinessChatId($business, $this->chat->chat_id);
                    Log::info('Business account connected', [
                        'user_id' => $userId,
                        'business_id' => $business->id,
                        'business_name' => $business->name,
                    ]);
                    $this->replyWithMessage(TelegramMessages::MSG_ACCOUNT_CONNECTED);
                } else {
                    Log::warning('Business not found for auth token', [
                        'user_id' => $userId,
                        'token' => $token,
                    ]);
                    $this->replyWithMessage(TelegramMessages::MSG_BUSINESS_NOT_FOUND);
                }

                return;
            }

            // Начало записи
            $slug = $parts[1];
            $business = $this->botService->findBusinessBySlug($slug);

            if ($business) {
                Log::info('Starting booking process', [
                    'user_id' => $userId,
                    'business_id' => $business->id,
                    'business_name' => $business->name,
                ]);
                $this->startBookingProcess($business);
            } else {
                Log::warning('Business not found for slug', [
                    'user_id' => $userId,
                    'slug' => $slug,
                ]);
                $this->replyWithMessage(TelegramMessages::MSG_BUSINESS_NOT_FOUND);
            }

            return;
        }

        // Если просто /start без параметров
        Log::info('Start command without parameters', ['user_id' => $userId]);
        $this->replyWithMessage(TelegramMessages::MSG_START);
    }

    /**
     * Команда /list - каталог бизнесов
     */
    public function list()
    {
        $userId = $this->message->from()->id();

        // Очищаем предыдущее состояние
        $this->botService->clearUserState($userId, null);
        $this->lastMessageId = null;

        // Получаем первую страницу
        $this->showBusinessListPage(1);
    }

    /**
     * Показывает страницу списка бизнесов
     */
    protected function showBusinessListPage(int $page = 1)
    {
        $perPage = 10;

        $businesses = $this->botService->getBusinessesPaginated($page, $perPage);

        $total = $this->botService->getTotalBusinesses();
        $totalPages = ceil($total / $perPage);

        if ($businesses->isEmpty()) {
            $this->replyWithMessage(TelegramMessages::MSG_NO_BUSINESSES);

            return;
        }

        $message = "🏢 Выберите бизнес для записи:\n\n";
        $message .= TelegramMessages::format(TelegramMessages::MSG_PAGE_INFO, [
            'current' => $page,
            'total' => $totalPages,
        ]);

        $this->replyWithMessage($message, TelegramKeyboards::businessCatalog($businesses, $page, $totalPages));
    }

    /**
     * Команда /search - поиск бизнесов по названию
     */
    public function search()
    {
        $userId = $this->message->from()->id();

        // Очищаем предыдущее состояние
        $this->botService->clearUserState($userId, null);
        $this->lastMessageId = null;

        // Устанавливаем состояние поиска
        $this->botService->updateUserStateKeepMessageId($userId, null, TelegramUserState::STEP_SEARCH, []);

        $this->replyWithMessage(TelegramMessages::MSG_SEARCH_PROMPT);
    }

    /**
     * Показывает страницу результатов поиска
     */
    protected function showSearchResultsPage(string $query, int $page = 1)
    {
        $perPage = 10;

        $businesses = $this->botService->searchBusinesses($query, $page, $perPage);

        $total = $this->botService->getSearchCount($query);
        $totalPages = ceil($total / $perPage);

        if ($businesses->isEmpty()) {
            $this->replyWithMessage(TelegramMessages::MSG_SEARCH_NO_RESULTS);

            return;
        }

        $message = TelegramMessages::format(TelegramMessages::MSG_SEARCH_RESULTS, [
            'query' => $query,
        ]) . "\n\n";
        $message .= TelegramMessages::format(TelegramMessages::MSG_PAGE_INFO, [
            'current' => $page,
            'total' => $totalPages,
        ]);

        $this->replyWithMessage($message, TelegramKeyboards::searchResults($businesses, $page, $totalPages));
    }

    /**
     * Обработка текстовых сообщений во время записи
     */
    protected function handleTextMessage(string $text, $state, $business)
    {
        Log::info('handleTextMessage', ['step' => $state->step, 'text' => $text]);

        // Проверка на команду отмены
        if (mb_strtolower(trim($text)) === 'отмена' || mb_strtolower(trim($text)) === 'cancel') {
            $this->botService->clearUserState($state->telegram_user_id, $business->id);
            $this->replyWithMessage(TelegramMessages::MSG_CANCEL);
            $this->lastMessageId = null;

            return;
        }

        switch ($state->step) {
            case TelegramUserState::STEP_ENTER_CLIENT_INFO:
                $this->handleClientInfo($business, $text, $state);
                break;
            case TelegramUserState::STEP_ENTER_PHONE:
                $this->handlePhone($business, $text, $state);
                break;
            case TelegramUserState::STEP_ENTER_NOTES:
                $this->handleNotes($business, $text, $state);
                break;
            case TelegramUserState::STEP_SELECT_LOCATION:
            case TelegramUserState::STEP_SELECT_SERVICE:
            case TelegramUserState::STEP_SELECT_MASTER:
            case TelegramUserState::STEP_SELECT_DATE:
            case TelegramUserState::STEP_SELECT_TIME:
            case TelegramUserState::STEP_CONFIRM_APPOINTMENT:
                // На этих шагах нужно использовать кнопки
                // Показываем сообщение об ошибке отдельно, не редактируя интерфейс записи
                Log::warning('Text input on step that requires buttons: ' . $state->step);
                $this->chat->message(TelegramMessages::MSG_USE_BUTTONS)->send();
                break;
            default:
                Log::warning('Unknown step in handleTextMessage: ' . $state->step);
                $this->replyWithMessage(TelegramMessages::MSG_UNKNOWN_COMMAND);
        }
    }

    /**
     * Обработка поискового запроса
     */
    protected function handleSearchQuery(string $text, $state)
    {
        Log::info('handleSearchQuery', ['text' => $text]);

        // Проверка на команду отмены
        if (mb_strtolower(trim($text)) === 'отмена' || mb_strtolower(trim($text)) === 'cancel') {
            $this->botService->clearUserState($state->telegram_user_id, null);
            $this->replyWithMessage(TelegramMessages::MSG_CANCEL);
            $this->lastMessageId = null;

            return;
        }

        // Проверка длины запроса
        $query = trim($text);
        if (mb_strlen($query) < 2) {
            $this->replyWithMessage(TelegramMessages::MSG_SEARCH_TOO_SHORT);

            return;
        }

        // Сохраняем запрос в состояние для пагинации
        $data = $state->data;
        $data['search_query'] = $query;
        $this->botService->updateUserStateKeepMessageId($state->telegram_user_id, null, TelegramUserState::STEP_SEARCH, $data);

        // Показываем первую страницу результатов
        $this->showSearchResultsPage($query, 1);
    }

    /**
     * Обработка имени клиента
     */
    protected function handleClientInfo(Business $business, string $text, $state)
    {
        Log::info('handleClientInfo', ['text' => $text]);

        [$isValid, $result] = TelegramValidators::validateName($text);

        if (! $isValid) {
            $this->replyWithMessage($result, TelegramKeyboards::cancelOnly());

            return;
        }

        $name = $result;
        $data = $state->data;
        $data['client_data']['first_name'] = $name;

        $this->botService->updateUserStateKeepMessageId($state->telegram_user_id, $business->id, TelegramUserState::STEP_ENTER_PHONE, $data);

        $message = TelegramMessages::format(TelegramMessages::MSG_STATUS_NAME, ['name' => $name]) . "\n\n" .
            TelegramMessages::MSG_ENTER_PHONE;

        $this->replyWithMessage($message, TelegramKeyboards::restartAndCancel());
    }

    /**
     * Обработка телефона клиента
     */
    protected function handlePhone(Business $business, string $text, $state)
    {
        Log::info('handlePhone', ['text' => $text]);

        [$isValid, $cleaned] = TelegramValidators::validatePhone($text);

        if (! $isValid) {
            $this->replyWithMessage(TelegramMessages::MSG_PHONE_INVALID, TelegramKeyboards::cancelOnly());

            return;
        }

        $data = $state->data;
        $data['client_data']['phone'] = $cleaned;

        $this->botService->updateUserStateKeepMessageId($state->telegram_user_id, $business->id, TelegramUserState::STEP_ENTER_NOTES, $data);

        $message = TelegramMessages::format(TelegramMessages::MSG_STATUS_NAME, ['name' => $data['client_data']['first_name']]) . "\n" .
            TelegramMessages::format(TelegramMessages::MSG_STATUS_PHONE, ['phone' => $cleaned]) . "\n\n" .
            TelegramMessages::MSG_ENTER_NOTES;

        $this->replyWithMessage($message, TelegramKeyboards::skipAndCancel());
    }

    /**
     * Обработка примечаний клиента
     */
    protected function handleNotes(Business $business, string $text, $state)
    {
        Log::info('handleNotes', ['text' => $text]);

        // Проверка на пропуск
        if (TelegramValidators::shouldSkipNotes($text)) {
            $data = $state->data;
            TelegramUserState::updateStateKeepMessageId($state->telegram_user_id, $business->id, TelegramUserState::STEP_CONFIRM_APPOINTMENT, $data);
            $this->showAppointmentConfirmation($business, $data);

            return;
        }

        // Валидация заметки
        [$isValid, $result] = TelegramValidators::validateNotes($text);

        if (! $isValid) {
            $this->replyWithMessage($result, TelegramKeyboards::skipAndCancel());

            return;
        }

        $notes = $result;
        $data = $state->data;
        $data['client_data']['notes'] = $notes;

        TelegramUserState::updateStateKeepMessageId($state->telegram_user_id, $business->id, TelegramUserState::STEP_CONFIRM_APPOINTMENT, $data);
        $this->showAppointmentConfirmation($business, $data);
    }

    /**
     * Показ подтверждения записи
     */
    protected function showAppointmentConfirmation(Business $business, array $data)
    {
        Log::info('showAppointmentConfirmation', ['data' => $data]);

        $location = $business->locations()->find($data['location_id']);
        $service = $business->services()->find($data['service_id']);
        $master = $business->masters()->find($data['master_id']);

        // Форматируем дату и время
        $date = Carbon::parse($data['date'])->format('d.m.Y');
        $time = $data['time'];
        if (is_string($time) && ! str_contains($time, ':')) {
            $time = $time . ':00';
        }
        $time = Carbon::parse($time)->format('H:i');

        // Формируем сообщение
        $message = TelegramMessages::MSG_CONFIRMATION_HEADER;
        $message .= TelegramMessages::format(TelegramMessages::MSG_CONFIRMATION_LINE, [
            'emoji' => '📍',
            'label' => 'Локация',
            'value' => $location->name,
        ]) . "\n";
        $message .= TelegramMessages::format(TelegramMessages::MSG_CONFIRMATION_LINE, [
            'emoji' => '💇‍♀️',
            'label' => 'Услуга',
            'value' => $service->name,
        ]) . "\n";
        $message .= TelegramMessages::format(TelegramMessages::MSG_CONFIRMATION_LINE, [
            'emoji' => '👨‍💼',
            'label' => 'Мастер',
            'value' => $master->first_name . ' ' . $master->last_name,
        ]) . "\n";
        $message .= TelegramMessages::format(TelegramMessages::MSG_CONFIRMATION_LINE, [
            'emoji' => '📅',
            'label' => 'Дата',
            'value' => $date,
        ]) . "\n";
        $message .= TelegramMessages::format(TelegramMessages::MSG_CONFIRMATION_LINE, [
            'emoji' => '⏰',
            'label' => 'Время',
            'value' => $time,
        ]) . "\n";
        $message .= TelegramMessages::format(TelegramMessages::MSG_CONFIRMATION_LINE, [
            'emoji' => '👤',
            'label' => 'Клиент',
            'value' => $data['client_data']['first_name'],
        ]) . "\n";
        $message .= TelegramMessages::format(TelegramMessages::MSG_CONFIRMATION_LINE, [
            'emoji' => '📱',
            'label' => 'Телефон',
            'value' => $data['client_data']['phone'],
        ]) . "\n";

        if (isset($data['client_data']['notes']) && ! empty($data['client_data']['notes'])) {
            $message .= TelegramMessages::format(TelegramMessages::MSG_CONFIRMATION_LINE, [
                'emoji' => '📝',
                'label' => 'Примечание',
                'value' => $data['client_data']['notes'],
            ]) . "\n";
        }

        $this->replyWithMessage($message, TelegramKeyboards::confirmation());
    }

    /**
     * Начало процесса записи
     */
    protected function startBookingProcess(Business $business, $userId = null)
    {
        // Получаем userId из параметра или из сообщения/колбэка
        $userId = $userId ?? $this->callbackQuery?->from()->id() ?? $this->message->from()->id();

        // Очищаем все предыдущие состояния (включая состояние поиска)
        TelegramUserState::clearState($userId);
        $this->lastMessageId = null;

        // Начинаем с выбора локации
        $this->showLocationSelection($business, $userId);
    }

    /**
     * Показ выбора локации
     */
    protected function showLocationSelection(Business $business, $userId = null)
    {
        $userId = $userId ?? $this->callbackQuery?->from()->id() ?? $this->message->from()->id();

        $locations = $this->botService->getLocationsForBusiness($business->id);

        if ($locations->isEmpty()) {
            $this->replyWithMessage(TelegramMessages::MSG_NO_LOCATIONS);

            return;
        }

        $this->replyWithMessage(
            TelegramMessages::MSG_SELECT_LOCATION,
            TelegramKeyboards::locations($locations)
        );

        TelegramUserState::updateStateKeepMessageId($userId, $business->id, TelegramUserState::STEP_SELECT_LOCATION);
    }

    /**
     * Показ выбора услуги
     */
    protected function showServiceSelection(Business $business, $locationId)
    {
        Log::info('showServiceSelection called', ['location_id' => $locationId]);

        $services = $this->botService->getServicesForLocation($locationId);

        if ($services->isEmpty()) {
            $services = $this->botService->getServicesForBusiness($business->id);
        }

        if ($services->isEmpty()) {
            $this->replyWithMessage(TelegramMessages::MSG_NO_SERVICES);

            return;
        }

        $location = $this->botService->findLocation($locationId);
        $message = TelegramMessages::format(TelegramMessages::MSG_SELECT_SERVICE, [
            'location' => $location->name,
        ]);

        $this->replyWithMessage($message, TelegramKeyboards::services($services));

        $userId = $this->callbackQuery?->from()->id() ?? $this->message->from()->id();
        TelegramUserState::updateStateKeepMessageId($userId, $business->id, TelegramUserState::STEP_SELECT_SERVICE, [
            'location_id' => $locationId,
        ]);
    }

    /**
     * Показ выбора мастера
     */
    protected function showMasterSelection(Business $business, $locationId, $serviceId)
    {
        Log::info('showMasterSelection called', ['location_id' => $locationId, 'service_id' => $serviceId]);

        $location = $business->locations()->find($locationId);
        $service = $business->services()->find($serviceId);

        if (! $location || ! $service) {
            $this->replyWithMessage(TelegramMessages::MSG_NOT_FOUND);

            return;
        }

        $masters = $location->masters()
            ->where('is_active', true)
            ->whereHas('services', function ($q) use ($serviceId) {
                $q->where('services.id', $serviceId);
            })
            ->orderBy('first_name')
            ->get();

        if ($masters->isEmpty()) {
            $masters = $location->masters()
                ->where('is_active', true)
                ->orderBy('first_name')
                ->get();
        }

        if ($masters->isEmpty()) {
            $masters = $business->masters()
                ->where('is_active', true)
                ->whereHas('services', function ($q) use ($serviceId) {
                    $q->where('services.id', $serviceId);
                })
                ->orderBy('first_name')
                ->get();
        }

        if ($masters->isEmpty()) {
            $this->replyWithMessage(TelegramMessages::MSG_NO_MASTERS);

            return;
        }

        $message = TelegramMessages::format(TelegramMessages::MSG_SELECT_MASTER, [
            'service' => $service->name,
        ]);

        $this->replyWithMessage($message, TelegramKeyboards::masters($masters));

        $userId = $this->callbackQuery?->from()->id() ?? $this->message->from()->id();
        TelegramUserState::updateStateKeepMessageId($userId, $business->id, TelegramUserState::STEP_SELECT_MASTER, [
            'location_id' => $locationId,
            'service_id' => $serviceId,
        ]);
    }

    /**
     * Обработка callback запросов
     */
    protected function handleCallbackQuery(): void
    {
        $callbackData = $this->callbackQuery->data();
        $userId = $this->callbackQuery->from()->id();

        // Получаем messageId
        $message = $this->callbackQuery->message();
        $messageId = $message ? $message->id() : null;

        Log::info('Processing callback query', [
            'callback_data' => $callbackData,
            'callback_data_type' => gettype($callbackData),
            'callback_data_class' => get_class($callbackData),
            'user_id' => $userId,
            'message_id' => $messageId,
            'chat_id' => $this->chat->chat_id,
            'timestamp' => now()->toISOString(),
        ]);

        // Сохраняем messageId для последующего редактирования
        if ($messageId) {
            $this->lastMessageId = $messageId;
        }

        // Извлекаем action из Collection
        $action = $callbackData->get('action');

        if (! $action) {
            Log::error('No action found in callback data', [
                'user_id' => $userId,
                'callback_data' => $callbackData->toArray(),
            ]);
            $this->replyWithMessage('❌ Ошибка данных. Попробуйте снова.');

            return;
        }

        // Обработка выбора бизнеса из каталога (может быть без состояния)
        if (str_starts_with($action, 'business_')) {
            $businessId = str_replace('business_', '', $action);
            $business = Business::find($businessId);

            if (! $business) {
                $this->replyWithMessage(TelegramMessages::MSG_BUSINESS_NOT_FOUND);

                return;
            }

            Log::info('Business selected from catalog:', ['business_id' => $businessId, 'business_name' => $business->name]);

            // Удаляем старое сообщение с каталогом
            $this->deleteBotMessage($messageId);

            // Начинаем процесс записи
            $this->startBookingProcess($business, $userId);

            return;
        }

        // Обработка пагинации (может быть без состояния)
        if (str_starts_with($action, 'page_')) {
            $parts = explode('_', $action);
            $currentPage = (int) $parts[1];
            $direction = $parts[2] ?? null;

            if ($direction === 'prev') {
                $newPage = $currentPage - 1;
            } elseif ($direction === 'next') {
                $newPage = $currentPage + 1;
            } else {
                Log::warning('Unknown page action: ' . $action);
                $this->replyWithMessage(TelegramMessages::MSG_UNKNOWN_COMMAND);

                return;
            }

            // Находим состояние пользователя для определения типа списка
            $state = TelegramUserState::where('telegram_user_id', $userId)->first();

            // Определяем, что показывать - каталог или поиск
            $searchQuery = $state?->data['search_query'] ?? null;

            if ($searchQuery) {
                // Показываем результаты поиска
                $this->showSearchResultsPage($searchQuery, $newPage);
            } else {
                // Показываем каталог бизнесов
                $this->showBusinessListPage($newPage);
            }

            return;
        }

        // Обработка недоступных кнопок (может быть без состояния)
        if (str_starts_with($action, 'disabled_')) {
            // Недоступная кнопка - игнорируем
            Log::info('Disabled button clicked:', ['action' => $action]);

            return;
        }

        // Находим состояние пользователя
        $states = TelegramUserState::where('telegram_user_id', $userId)->get();
        Log::info('User states found:', ['count' => $states->count()]);

        if ($states->isEmpty()) {
            Log::warning('No states found for user: ' . $userId);
            $this->replyWithMessage('❌ Сессия не найдена.');

            return;
        }

        // Ищем состояние с конкретным бизнесом (не поисковое)
        $state = $states->firstWhere('business_id', '!=', null);

        // Если не нашли состояние с бизнесом, используем первое (может быть поиск)
        if (! $state) {
            $state = $states->first();
        }

        $business = $state->business;

        Log::info('Processing action:', [
            'action' => $action,
            'state_step' => $state->step,
            'business_id' => $business?->id,
            'business_name' => $business?->name,
        ]);

        if (str_starts_with($action, 'location_')) {
            $locationId = str_replace('location_', '', $action);
            Log::info('Location selected:', ['location_id' => $locationId]);
            $this->showServiceSelection($business, $locationId);
        } elseif (str_starts_with($action, 'service_')) {
            $serviceId = str_replace('service_', '', $action);
            $locationId = $state?->data['location_id'] ?? null;
            Log::info('Service selected:', [
                'service_id' => $serviceId,
                'location_id' => $locationId,
            ]);
            if ($locationId) {
                $this->showMasterSelection($business, $locationId, $serviceId);
            } else {
                Log::error('Location ID not found in state data');
                $this->replyWithMessage(TelegramMessages::MSG_NOT_FOUND);
            }
        } elseif (str_starts_with($action, 'master_')) {
            $masterId = str_replace('master_', '', $action);
            $locationId = $state?->data['location_id'] ?? null;
            $serviceId = $state?->data['service_id'] ?? null;
            Log::info('Master selected:', [
                'master_id' => $masterId,
                'location_id' => $locationId,
                'service_id' => $serviceId,
            ]);
            if ($locationId && $serviceId) {
                $this->showTimeSelection($business, $locationId, $serviceId, $masterId);
            } else {
                Log::error('Location or Service ID not found in state data');
                $this->replyWithMessage(TelegramMessages::MSG_NOT_FOUND);
            }
        } elseif (str_starts_with($action, 'date_')) {
            $date = str_replace('date_', '', $action);
            Log::info('Date selected:', ['date' => $date, 'state_step' => $state->step]);

            // Проверяем, что это не недоступная дата
            if (str_starts_with($date, 'disabled_')) {
                Log::info('Disabled date clicked, ignoring');

                return;
            }

            // Обработка специальной кнопки "След. месяц"
            if ($date === 'next_month') {
                $locationId = $state?->data['location_id'] ?? null;
                $serviceId = $state?->data['service_id'] ?? null;
                $masterId = $state?->data['master_id'] ?? null;
                $currentMonth = $state?->data['month'] ?? null;

                if ($locationId && $serviceId && $masterId) {
                    $monthDate = $currentMonth ? Carbon::parse($currentMonth . '-01') : Carbon::today();
                    $nextMonth = $monthDate->addMonth()->format('Y-m');
                    $this->showTimeSelection($business, $locationId, $serviceId, $masterId, $nextMonth);
                }

                return;
            }

            Log::info('Date selected:', ['date' => $date]);
            $this->showTimeSlots($business, $date, $state);
        } elseif (str_starts_with($action, 'calendar_prev_')) {
            $month = str_replace('calendar_prev_', '', $action);
            $monthDate = Carbon::parse($month . '-01');
            $prevMonth = $monthDate->subMonth()->format('Y-m');

            Log::info('Calendar prev month:', ['current' => $month, 'prev' => $prevMonth]);

            $locationId = $state?->data['location_id'] ?? null;
            $serviceId = $state?->data['service_id'] ?? null;
            $masterId = $state?->data['master_id'] ?? null;

            if ($locationId && $serviceId && $masterId) {
                $this->showTimeSelection($business, $locationId, $serviceId, $masterId, $prevMonth);
            }
        } elseif (str_starts_with($action, 'calendar_next_')) {
            $month = str_replace('calendar_next_', '', $action);
            $monthDate = Carbon::parse($month . '-01');
            $nextMonth = $monthDate->addMonth()->format('Y-m');

            Log::info('Calendar next month:', ['current' => $month, 'next' => $nextMonth]);

            $locationId = $state?->data['location_id'] ?? null;
            $serviceId = $state?->data['service_id'] ?? null;
            $masterId = $state?->data['master_id'] ?? null;

            if ($locationId && $serviceId && $masterId) {
                $this->showTimeSelection($business, $locationId, $serviceId, $masterId, $nextMonth);
            }
        } elseif (str_starts_with($action, 'disabled_')) {
            // Недоступная дата или заголовок - игнорируем
            Log::info('Disabled element clicked:', ['action' => $action]);

            return;
        } elseif (str_starts_with($action, 'time_')) {
            $time = str_replace('time_', '', $action);
            Log::info('Time selected:', ['time' => $time]);
            Log::info('Before handleTimeSelection', [
                'lastMessageId' => $this->lastMessageId,
                'messageId_from_callback' => $messageId,
            ]);
            $this->handleTimeSelection($business, $time, $state);
        } elseif ($action === 'skip_notes') {
            $data = $state->data;
            TelegramUserState::updateStateKeepMessageId($userId, $business->id, TelegramUserState::STEP_CONFIRM_APPOINTMENT, $data);
            $this->showAppointmentConfirmation($business, $data);
        } elseif ($action === 'confirm_appointment') {
            $this->createAppointment($business, $state);
        } elseif ($action === 'cancel') {
            Log::info('Cancel action called', ['message_id' => $messageId]);

            // Удаляем старое сообщение с кнопками
            $this->deleteBotMessage($messageId);

            // Очищаем все состояния пользователя
            TelegramUserState::clearState($userId);

            // Сбрасываем lastMessageId чтобы отправить новое сообщение
            $this->lastMessageId = null;

            $this->replyWithMessage(TelegramMessages::MSG_CANCEL);
        } elseif ($action === 'restart') {
            Log::info('Restart action called', ['message_id' => $messageId]);

            // Удаляем старое сообщение с кнопками
            $this->deleteBotMessage($messageId);

            TelegramUserState::clearState($userId, $business?->id);

            // Сбрасываем lastMessageId чтобы отправить новое сообщение
            $this->lastMessageId = null;

            // Начинаем с самого начала
            if ($business) {
                $this->showLocationSelection($business);
            } else {
                $this->replyWithMessage(TelegramMessages::MSG_START);
            }
        } else {
            Log::warning('Unknown action: ' . $action);
            $this->replyWithMessage(TelegramMessages::MSG_UNKNOWN_COMMAND);
        }

        Log::info('=== handleCallbackQuery END ===');
    }

    /**
     * Показ выбора даты (календарь)
     */
    protected function showTimeSelection(Business $business, $locationId, $serviceId, $masterId, ?string $month = null)
    {
        Log::info('showTimeSelection called', [
            'location_id' => $locationId,
            'service_id' => $serviceId,
            'master_id' => $masterId,
            'month' => $month,
        ]);

        $location = $business->locations()->find($locationId);
        $service = $business->services()->find($serviceId);
        $master = $business->masters()->find($masterId);

        if (! $location || ! $service || ! $master) {
            $this->replyWithMessage(TelegramMessages::MSG_NOT_FOUND);

            return;
        }

        // Определяем месяц для отображения
        if (! $month) {
            $month = Carbon::today()->format('Y-m');
        }

        // Получаем доступные даты для месяца
        $availableDates = TelegramKeyboards::getAvailableDatesForMonth(
            $this->slotService,
            $serviceId,
            $masterId,
            $locationId,
            $month
        );

        // Формируем сообщение
        $message = TelegramMessages::format(TelegramMessages::MSG_SELECT_DATE, [
            'master' => $master->first_name . ' ' . $master->last_name,
        ]) . "\n\n📅 " . Carbon::parse($month . '-01')->locale('ru')->isoFormat('MMMM YYYY');

        // Проверяем возможность перехода к предыдущему месяцу
        $hasPrevMonth = TelegramKeyboards::hasPrevMonth($month);

        // Создаем клавиатуру календаря
        $keyboard = TelegramKeyboards::calendar($month, $availableDates, null, $hasPrevMonth);

        $this->replyWithMessage($message, $keyboard);

        $userId = $this->callbackQuery?->from()->id() ?? $this->message->from()->id();

        // Сохраняем доступные даты и месяц в состояние
        TelegramUserState::updateStateKeepMessageId($userId, $business->id, TelegramUserState::STEP_SELECT_DATE, [
            'location_id' => $locationId,
            'service_id' => $serviceId,
            'master_id' => $masterId,
            'month' => $month,
            'available_dates' => $availableDates,
        ]);
    }

    /**
     * Показ выбора времени
     */
    protected function showTimeSlots(Business $business, $date, $state)
    {
        Log::info('showTimeSlots called', ['date' => $date]);

        $locationId = $state?->data['location_id'] ?? null;
        $serviceId = $state?->data['service_id'] ?? null;
        $masterId = $state?->data['master_id'] ?? null;

        if (! $locationId || ! $serviceId || ! $masterId) {
            $this->replyWithMessage(TelegramMessages::MSG_NOT_FOUND);

            return;
        }

        $debugInfo = [];
        $availableSlots = $this->slotService->getAvailableSlots(
            $serviceId,
            $date,
            $masterId,
            $locationId,
            $debugInfo
        );

        Log::info('Available slots:', ['slots' => $availableSlots, 'count' => count($availableSlots)]);

        if (empty($availableSlots)) {
            $this->replyWithMessage(TelegramMessages::MSG_NO_SLOTS, TelegramKeyboards::timesEmpty());

            return;
        }

        $formattedDate = Carbon::parse($date)->locale('ru')->format('d.m.Y (l)');
        $message = TelegramMessages::format(TelegramMessages::MSG_SELECT_TIME, [
            'date' => $formattedDate,
        ]);

        $this->replyWithMessage($message, TelegramKeyboards::times($availableSlots));

        $userId = $this->callbackQuery?->from()->id() ?? $this->message->from()->id();

        // Сохраняем месяц для возврата из выбора времени
        $month = Carbon::parse($date)->format('Y-m');

        TelegramUserState::updateStateKeepMessageId($userId, $business->id, TelegramUserState::STEP_SELECT_TIME, [
            'location_id' => $locationId,
            'service_id' => $serviceId,
            'master_id' => $masterId,
            'date' => $date,
            'month' => $month,
        ]);
    }

    /**
     * Обработка выбора времени
     */
    protected function handleTimeSelection(Business $business, $time, $state)
    {
        Log::info('=== handleTimeSelection START ===');
        Log::info('Input', [
            'time' => $time,
            'state_step' => $state->step,
            'last_message_id_before' => $this->lastMessageId,
        ]);

        if (! $state) {
            $this->replyWithMessage(TelegramMessages::MSG_SESSION_EXPIRED);

            return;
        }

        $data = $state->data;

        // Форматируем время
        if (is_string($time)) {
            $time = trim($time);

            if (! str_contains($time, ':')) {
                $time = $time . ':00';
            }

            try {
                $carbonTime = Carbon::parse($time);
                $time = $carbonTime->format('H:i');
            } catch (\Exception $e) {
                Log::error('Error parsing time: ' . $e->getMessage() . ', time: ' . $time);
                $time = '12:00';
            }
        }

        $data['time'] = $time;

        Log::info('Updating state to enter_client_info');
        TelegramUserState::updateStateKeepMessageId($state->telegram_user_id, $business->id, TelegramUserState::STEP_ENTER_CLIENT_INFO, $data);

        $message = TelegramMessages::MSG_ENTER_NAME;

        Log::info('Calling replyWithMessage', ['lastMessageId' => $this->lastMessageId]);
        $this->replyWithMessage($message, TelegramKeyboards::restartAndCancel());

        Log::info('=== handleTimeSelection END ===');
    }

    /**
     * Создание записи
     */
    protected function createAppointment(Business $business, $state)
    {
        if (! $state || ! isset($state->data['client_data'])) {
            $this->replyWithMessage(TelegramMessages::MSG_NOT_FOUND);

            return;
        }

        $data = $state->data;

        Log::info('Creating appointment with data:', [
            'business_id' => $business->id,
            'service_id' => $data['service_id'],
            'master_id' => $data['master_id'],
            'location_id' => $data['location_id'],
            'date' => $data['date'],
            'time' => $data['time'],
            'time_type' => gettype($data['time']),
            'client_data' => $data['client_data'],
        ]);

        try {
            $client = Client::firstOrCreate(
                [
                    'business_id' => $business->id,
                    'phone' => $data['client_data']['phone'],
                ],
                [
                    'first_name' => $data['client_data']['first_name'],
                    'last_name' => $data['client_data']['last_name'] ?? null,
                    'email' => $data['client_data']['email'] ?? null,
                    'telegram_user_id' => $this->callbackQuery->from()->id(),
                ]
            );

            // Форматируем время
            $time = $data['time'];
            if (is_string($time) && ! str_contains($time, ':')) {
                $time = $time . ':00';
            }
            $time = Carbon::parse($time)->format('H:i');

            $appointment = Appointment::create([
                'business_id' => $business->id,
                'client_id' => $client->id,
                'service_id' => $data['service_id'],
                'master_id' => $data['master_id'],
                'location_id' => $data['location_id'],
                'date' => $data['date'],
                'time' => $time,
                'source' => 'telegram',
                'status' => 'pending',
                'notes' => $data['client_data']['notes'] ?? null,
            ]);

            // Удаляем старое сообщение с кнопками подтверждения
            $this->deleteBotMessage($this->lastMessageId);

            TelegramUserState::clearState($state->telegram_user_id, $business->id);
            TelegramNotificationService::sendAppointmentCreated($appointment);

            // Форматируем для сообщения
            $formattedDate = $appointment->date->format('d.m.Y');
            $formattedTime = $appointment->time;

            // Сбрасываем lastMessageId чтобы отправить новое сообщение
            $this->lastMessageId = null;

            $message = TelegramMessages::format(TelegramMessages::MSG_APPOINTMENT_CREATED, [
                'date' => $formattedDate,
                'time' => $formattedTime,
                'service' => $appointment->service->name,
                'master' => $appointment->master->first_name . ' ' . $appointment->master->last_name,
                'location' => $appointment->location->name,
            ]);

            $this->replyWithMessage($message);

            // Очищаем ID после отправки финального сообщения
            $this->lastMessageId = null;
        } catch (\Exception $e) {
            Log::error('Error creating appointment: ' . $e->getMessage());
            $this->replyWithMessage(TelegramMessages::MSG_ERROR);
        }
    }

    /**
     * Удаляет сообщение пользователя
     */
    protected function deleteUserMessage(int $messageId): void
    {
        Log::info('=== deleteUserMessage START ===', ['message_id' => $messageId]);
        try {
            // Метод delete() в DefStudio\Telegraph возвращает true, но не удаляет
            // Нужно использовать deleteMessage() и вызвать send()
            $result = $this->chat->deleteMessage($messageId)->send();
            Log::info('✓ User message deleted', ['message_id' => $messageId, 'result' => $result]);
        } catch (\Exception $e) {
            Log::error('✗ Failed to delete user message: ' . $e->getMessage());
        }
        Log::info('=== deleteUserMessage END ===');
    }

    /**
     * Удаляет сообщение бота
     */
    protected function deleteBotMessage(?int $messageId): void
    {
        if (! $messageId) {
            Log::warning('✗ Skipping bot message deletion - messageId is null');

            return;
        }
        Log::info('=== deleteBotMessage START ===', ['message_id' => $messageId]);
        try {
            $this->chat->deleteMessage($messageId)->send();
            Log::info('✓ Bot message deleted', ['message_id' => $messageId]);
        } catch (\Exception $e) {
            Log::warning('✗ Failed to delete bot message: ' . $e->getMessage());
        }
        Log::info('=== deleteBotMessage END ===');
    }
}
