<?php

namespace App\Telegram;

use App\Models\Appointment;
use App\Models\Business;
use App\Models\BusinessRole;
use App\Models\Client;
use App\Models\TelegramUserState;
use App\Models\User;
use App\Services\AppointmentSlotService;
use App\Services\SubscriptionService;
use App\Services\TelegramBotService;
use Carbon\Carbon;
use DefStudio\Telegraph\Handlers\WebhookHandler;
use DefStudio\Telegraph\Keyboard\Keyboard;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

// Импортируем наши новые классы

class Handler extends WebhookHandler
{
    protected AppointmentSlotService $slotService;

    protected TelegramBotService $botService;

    protected ?int $lastMessageId = null;

    public function __construct(
        AppointmentSlotService $slotService,
        TelegramBotService $botService,
    ) {
        $this->slotService = $slotService;
        $this->botService = $botService;
    }

    /**
     * Основной метод для отправки/редактирования сообщений
     */
    protected function replyWithMessage(
        string $message,
        ?Keyboard $keyboard = null,
    ): void {
        try {
            if ($this->lastMessageId) {
                // Редактируем существующее сообщение
                $this->chat
                    ->edit($this->lastMessageId)
                    ->message($message)
                    ->send();

                // Если есть новая клавиатура, заменяем ее
                if ($keyboard) {
                    $this->chat
                        ->replaceKeyboard($this->lastMessageId, $keyboard)
                        ->send();
                }

                // ВАЖНО: Сохраняем ID даже при редактировании
                $this->saveMessageIdToState();
            } else {
                // Отправляем новое сообщение
                $response = $this->chat->message($message);

                if ($keyboard) {
                    $response = $response->keyboard($keyboard);
                }

                $result = $response->send();

                // Сохраняем ID сообщения
                $this->lastMessageId = $result->telegraphMessageId();

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

            // Сохраняем в базу даже при ошибке
            $this->saveMessageIdToState();
        }
    }

    /**
     * Сохраняет ID последнего сообщения в состояние пользователя
     */
    protected function saveMessageIdToState(): void
    {
        try {
            // Получаем userId из callback или message
            $userId =
                $this->callbackQuery?->from()->id() ??
                $this->message?->from()->id();

            if (! $userId || ! $this->lastMessageId) {
                return;
            }

            // Используем упрощенный метод получения состояния
            $state = $this->botService->getCurrentUserState($userId);

            if ($state) {
                $this->botService->setUserMessageId(
                    $userId,
                    $state->business_id,
                    $this->lastMessageId,
                );
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
    public function handleChatMessage(
        \Illuminate\Support\Stringable $text,
    ): void {
        $messageText = $text->toString();
        $userId = $this->message->from()->id();
        $messageId = $this->message->id();

        $state = $this->botService->getCurrentUserState($userId);

        if ($state) {
            $business = $state->business;

            // Восстанавливаем lastMessageId из состояния
            if ($state->last_message_id) {
                $this->lastMessageId = $state->last_message_id;
            }

            if ($state && $state->step !== TelegramUserState::STEP_START) {
                // Обработка состояния поиска (без бизнеса)
                if ($state->step === TelegramUserState::STEP_SEARCH) {
                    $this->handleSearchQuery($messageText, $state);
                } else {
                    $this->handleTextMessage($messageText, $state, $business);
                }

                // Удаляем сообщение пользователя после обработки
                $this->deleteUserMessage($messageId);

                return;
            }
        }

        // На неизвестные сообщения просто отвечаем
        $this->replyWithMessage(TelegramMessages::MSG_START);

        // Удаляем сообщение пользователя
        $this->deleteUserMessage($messageId);
    }

    /**
     * Команда /start - основная точка входа
     */
    public function start()
    {
        $text = $this->message->text() ?? '';
        $userId = $this->message->from()->id();

        $parts = explode(' ', $text);

        if (isset($parts[1])) {
            if (str_starts_with($parts[1], 'user_auth_')) {
                // Подключение пользователя
                $token = str_replace('user_auth_', '', $parts[1]);
                $user = $this->botService->findUserByToken($token);

                if ($user) {
                    $this->botService->updateUserChatId(
                        $user,
                        $this->chat->chat_id,
                    );

                    // Уведомляем о подключении Telegram
                    \App\Services\TelegramNotificationService::notifyConnected(
                        $user,
                    );

                    $this->replyWithMessage(
                        TelegramMessages::MSG_ACCOUNT_CONNECTED,
                    );
                } else {
                    Log::warning('User not found for auth token', [
                        'user_id' => $userId,
                        'token' => $token,
                    ]);
                    $this->replyWithMessage(
                        'Пользователь не найден. Проверьте ссылку для привязки.',
                    );
                }

                return;
            }

            if (str_starts_with($parts[1], 'auth_')) {
                // Подключение бизнеса
                $token = str_replace('auth_', '', $parts[1]);
                $business = $this->botService->findBusinessByToken($token);

                if ($business) {
                    $this->botService->updateBusinessChatId(
                        $business,
                        $this->chat->chat_id,
                    );
                    $this->replyWithMessage(
                        TelegramMessages::MSG_ACCOUNT_CONNECTED,
                    );
                } else {
                    Log::warning('Business not found for auth token', [
                        'user_id' => $userId,
                        'token' => $token,
                    ]);
                    $this->replyWithMessage(
                        TelegramMessages::MSG_BUSINESS_NOT_FOUND,
                    );
                }

                return;
            }

            // Начало записи
            $slug = $parts[1];
            $business = $this->botService->findBusinessBySlug($slug);

            if ($business) {
                // Проверяем доступ к Telegram боту для записи
                $subscriptionService = app(SubscriptionService::class);
                $owner = $this->getBusinessOwner($business);

                if ($owner) {
                    $telegramBotEnabled =
                        $subscriptionService->getLimit(
                            $owner,
                            'telegram_bot_enabled',
                        ) === true;

                    if (! $telegramBotEnabled) {
                        $this->replyWithMessage(
                            TelegramMessages::MSG_BOOKING_DISABLED,
                        );

                        return;
                    }
                }

                $this->startBookingProcess($business);
            } else {
                Log::warning('Business not found for slug', [
                    'user_id' => $userId,
                    'slug' => $slug,
                ]);
                $this->replyWithMessage(
                    TelegramMessages::MSG_BUSINESS_NOT_FOUND,
                );
            }

            return;
        }

        // Если просто /start без параметров
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

        $businesses = $this->botService->getBusinessesPaginated(
            $page,
            $perPage,
        );

        $total = $this->botService->getTotalBusinesses();
        $totalPages = ceil($total / $perPage);

        if ($businesses->isEmpty()) {
            $this->replyWithMessage(TelegramMessages::MSG_NO_BUSINESSES);

            return;
        }

        $message = TelegramMessages::MSG_SELECT_BUSINESS_CATALOG."\n\n";
        $message .= TelegramMessages::format(TelegramMessages::MSG_PAGE_INFO, [
            'current' => $page,
            'total' => $totalPages,
        ]);

        $this->replyWithMessage(
            $message,
            TelegramKeyboards::businessCatalog($businesses, $page, $totalPages),
        );
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
        $this->botService->updateUserStateKeepMessageId(
            $userId,
            null,
            TelegramUserState::STEP_SEARCH,
            [],
        );

        $this->replyWithMessage(TelegramMessages::MSG_SEARCH_PROMPT);
    }

    /**
     * Показывает страницу результатов поиска
     */
    protected function showSearchResultsPage(string $query, int $page = 1)
    {
        $perPage = 10;

        $businesses = $this->botService->searchBusinesses(
            $query,
            $page,
            $perPage,
        );

        $total = $this->botService->getSearchCount($query);
        $totalPages = ceil($total / $perPage);

        if ($businesses->isEmpty()) {
            $this->replyWithMessage(TelegramMessages::MSG_SEARCH_NO_RESULTS);

            return;
        }

        $message =
            TelegramMessages::format(TelegramMessages::MSG_SEARCH_RESULTS, [
                'query' => $query,
            ])."\n\n";
        $message .= TelegramMessages::format(TelegramMessages::MSG_PAGE_INFO, [
            'current' => $page,
            'total' => $totalPages,
        ]);

        $this->replyWithMessage(
            $message,
            TelegramKeyboards::searchResults($businesses, $page, $totalPages),
        );
    }

    /**
     * Обработка текстовых сообщений во время записи
     */
    protected function handleTextMessage(string $text, $state, $business)
    {
        // Проверка на команду отмены
        if (
            mb_strtolower(trim($text)) === 'отмена' ||
            mb_strtolower(trim($text)) === 'cancel'
        ) {
            $this->botService->clearUserState(
                $state->telegram_user_id,
                $business->id,
            );
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
                Log::warning(
                    'Text input on step that requires buttons: '.$state->step,
                );
                $this->chat->message(TelegramMessages::MSG_USE_BUTTONS)->send();
                break;
            default:
                Log::warning(
                    'Unknown step in handleTextMessage: '.$state->step,
                );
                $this->replyWithMessage(TelegramMessages::MSG_UNKNOWN_COMMAND);
        }
    }

    /**
     * Обработка поискового запроса
     */
    protected function handleSearchQuery(string $text, $state)
    {
        // Проверка на команду отмены
        if (
            mb_strtolower(trim($text)) === 'отмена' ||
            mb_strtolower(trim($text)) === 'cancel'
        ) {
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
        $this->botService->updateUserStateKeepMessageId(
            $state->telegram_user_id,
            null,
            TelegramUserState::STEP_SEARCH,
            $data,
        );

        // Показываем первую страницу результатов
        $this->showSearchResultsPage($query, 1);
    }

    /**
     * Обработка имени клиента
     */
    protected function handleClientInfo(
        Business $business,
        string $text,
        $state,
    ) {
        [$isValid, $result] = TelegramValidators::validateName($text);

        if (! $isValid) {
            $this->replyWithMessage($result, TelegramKeyboards::cancelOnly());

            return;
        }

        $name = $result;
        $data = $state->data;
        $data['client_data']['first_name'] = $name;

        $this->botService->updateUserStateKeepMessageId(
            $state->telegram_user_id,
            $business->id,
            TelegramUserState::STEP_ENTER_PHONE,
            $data,
        );

        $message =
            TelegramMessages::format(TelegramMessages::MSG_STATUS_NAME, [
                'name' => $name,
            ]).
            "\n\n".
            TelegramMessages::MSG_ENTER_PHONE;

        $this->replyWithMessage(
            $message,
            TelegramKeyboards::restartAndCancel(),
        );
    }

    /**
     * Обработка телефона клиента
     */
    protected function handlePhone(Business $business, string $text, $state)
    {
        [$isValid, $cleaned] = TelegramValidators::validatePhone($text);

        if (! $isValid) {
            $this->replyWithMessage(
                TelegramMessages::MSG_PHONE_INVALID,
                TelegramKeyboards::cancelOnly(),
            );

            return;
        }

        $data = $state->data;
        $data['client_data']['phone'] = $cleaned;

        $this->botService->updateUserStateKeepMessageId(
            $state->telegram_user_id,
            $business->id,
            TelegramUserState::STEP_ENTER_NOTES,
            $data,
        );

        $message =
            TelegramMessages::format(TelegramMessages::MSG_STATUS_NAME, [
                'name' => $data['client_data']['first_name'],
            ]).
            "\n".
            TelegramMessages::format(TelegramMessages::MSG_STATUS_PHONE, [
                'phone' => $cleaned,
            ]).
            "\n\n".
            TelegramMessages::MSG_ENTER_NOTES;

        $this->replyWithMessage($message, TelegramKeyboards::skipAndCancel());
    }

    /**
     * Обработка примечаний клиента
     */
    protected function handleNotes(Business $business, string $text, $state)
    {
        // Проверка на пропуск
        if (TelegramValidators::shouldSkipNotes($text)) {
            $data = $state->data;
            TelegramUserState::updateStateKeepMessageId(
                $state->telegram_user_id,
                $business->id,
                TelegramUserState::STEP_CONFIRM_APPOINTMENT,
                $data,
            );
            $this->showAppointmentConfirmation($business, $data);

            return;
        }

        // Валидация заметки
        [$isValid, $result] = TelegramValidators::validateNotes($text);

        if (! $isValid) {
            $this->replyWithMessage(
                $result,
                TelegramKeyboards::skipAndCancel(),
            );

            return;
        }

        $notes = $result;
        $data = $state->data;
        $data['client_data']['notes'] = $notes;

        TelegramUserState::updateStateKeepMessageId(
            $state->telegram_user_id,
            $business->id,
            TelegramUserState::STEP_CONFIRM_APPOINTMENT,
            $data,
        );
        $this->showAppointmentConfirmation($business, $data);
    }

    /**
     * Показ подтверждения записи
     */
    protected function showAppointmentConfirmation(
        Business $business,
        array $data,
    ) {
        $location = $business->locations()->find($data['location_id'] ?? null);
        $service = $business->services()->find($data['service_id'] ?? null);
        $master = isset($data['master_id']) ? $business->masters()->find($data['master_id']) : null;

        if (! $location || ! $service) {
            $this->replyWithMessage(TelegramMessages::MSG_NOT_FOUND);

            return;
        }

        $masterName = $master
            ? $master->first_name.' '.$master->last_name
            : TelegramMessages::MSG_ANY_MASTER;

        // Форматируем дату и время
        $date = Carbon::parse($data['date'])->format('d.m.Y');
        $time = $data['time'];
        if (is_string($time) && ! str_contains($time, ':')) {
            $time = $time.':00';
        }
        $time = Carbon::parse($time)->format('H:i');

        // Формируем сообщение
        $message = TelegramMessages::MSG_CONFIRMATION_HEADER;
        $message .=
            TelegramMessages::format(TelegramMessages::MSG_CONFIRMATION_LINE, [
                'label' => 'Локация',
                'value' => $location->name,
            ])."\n";
        $message .=
            TelegramMessages::format(TelegramMessages::MSG_CONFIRMATION_LINE, [
                'label' => 'Услуга',
                'value' => $service->name,
            ])."\n";
        $message .=
            TelegramMessages::format(TelegramMessages::MSG_CONFIRMATION_LINE, [
                'label' => 'Мастер',
                'value' => $masterName,
            ])."\n";
        $message .=
            TelegramMessages::format(TelegramMessages::MSG_CONFIRMATION_LINE, [
                'label' => 'Дата',
                'value' => $date,
            ])."\n";
        $message .=
            TelegramMessages::format(TelegramMessages::MSG_CONFIRMATION_LINE, [
                'label' => 'Время',
                'value' => $time,
            ])."\n";
        $message .=
            TelegramMessages::format(TelegramMessages::MSG_CONFIRMATION_LINE, [
                'label' => 'Клиент',
                'value' => $data['client_data']['first_name'],
            ])."\n";
        $message .=
            TelegramMessages::format(TelegramMessages::MSG_CONFIRMATION_LINE, [
                'label' => 'Телефон',
                'value' => $data['client_data']['phone'],
            ])."\n";

        if (
            isset($data['client_data']['notes']) &&
            ! empty($data['client_data']['notes'])
        ) {
            $message .=
                TelegramMessages::format(
                    TelegramMessages::MSG_CONFIRMATION_LINE,
                    [
                        'label' => 'Примечание',
                        'value' => $data['client_data']['notes'],
                    ],
                )."\n";
        }

        $this->replyWithMessage($message, TelegramKeyboards::confirmation());
    }

    /**
     * Начало процесса записи
     */
    protected function startBookingProcess(Business $business, $userId = null)
    {
        // Получаем userId из параметра или из сообщения/колбэка
        $userId =
            $userId ??
            ($this->callbackQuery?->from()->id() ??
                $this->message->from()->id());

        // Проверяем, включена ли онлайн-запись
        if ($business->online_booking_enabled === false) {
            $this->replyWithMessage(
                TelegramMessages::MSG_ONLINE_BOOKING_DISABLED,
            );

            return;
        }

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
        $userId =
            $userId ??
            ($this->callbackQuery?->from()->id() ??
                $this->message->from()->id());

        $locations = $this->botService->getLocationsForBusiness($business->id);

        if ($locations->isEmpty()) {
            $this->replyWithMessage(TelegramMessages::MSG_NO_LOCATIONS);

            return;
        }

        $this->replyWithMessage(
            TelegramMessages::MSG_SELECT_LOCATION,
            TelegramKeyboards::locations($locations),
        );

        TelegramUserState::updateStateKeepMessageId(
            $userId,
            $business->id,
            TelegramUserState::STEP_SELECT_LOCATION,
        );
    }

    /**
     * Показ выбора услуги
     */
    protected function showServiceSelection(Business $business, $locationId)
    {
        $services = $this->botService->getServicesForBusiness($business->id);

        if ($services->isEmpty()) {
            $this->replyWithMessage(TelegramMessages::MSG_NO_SERVICES);

            return;
        }

        $this->replyWithMessage(
            TelegramMessages::MSG_SELECT_SERVICE,
            TelegramKeyboards::services($services),
        );

        $userId =
            $this->callbackQuery?->from()->id() ?? $this->message->from()->id();
        TelegramUserState::updateStateKeepMessageId(
            $userId,
            $business->id,
            TelegramUserState::STEP_SELECT_SERVICE,
            [
                'location_id' => $locationId,
            ],
        );
    }

    /**
     * Показ выбора мастера
     */
    protected function showMasterSelection(
        Business $business,
        $locationId,
        $serviceId,
    ) {
        $location = $business->locations()->find($locationId);
        $service = $business->services()->find($serviceId);

        if (! $location || ! $service) {
            $this->replyWithMessage(TelegramMessages::MSG_NOT_FOUND);

            return;
        }

        $masters = $location
            ->masters()
            ->where('is_active', true)
            ->whereHas('services', function ($q) use ($serviceId) {
                $q->where('services.id', $serviceId);
            })
            ->orderBy('first_name')
            ->get();

        if ($masters->isEmpty()) {
            $masters = $location
                ->masters()
                ->where('is_active', true)
                ->orderBy('first_name')
                ->get();
        }

        if ($masters->isEmpty()) {
            $masters = $business
                ->masters()
                ->where('is_active', true)
                ->whereHas('services', function ($q) use ($serviceId) {
                    $q->where('services.id', $serviceId);
                })
                ->orderBy('first_name')
                ->get();
        }

        // Показываем выбор: «Любой мастер» всегда доступен, плюс список мастеров (если есть)
        $this->replyWithMessage(
            TelegramMessages::MSG_SELECT_MASTER,
            TelegramKeyboards::masters($masters),
        );

        $userId =
            $this->callbackQuery?->from()->id() ?? $this->message->from()->id();
        TelegramUserState::updateStateKeepMessageId(
            $userId,
            $business->id,
            TelegramUserState::STEP_SELECT_MASTER,
            [
                'location_id' => $locationId,
                'service_id' => $serviceId,
            ],
        );
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

        // Сохраняем messageId для последующего редактирования
        if ($messageId) {
            $this->lastMessageId = $messageId;
        }

        // Извлекаем action (Telegraph может вернуть Collection или array)
        $dataArray = $callbackData instanceof \Illuminate\Support\Collection
            ? $callbackData->toArray()
            : (array) $callbackData;
        $action = $dataArray['action'] ?? null;

        if (! $action) {
            Log::error('No action found in callback data', [
                'user_id' => $userId,
                'callback_data' => $dataArray,
            ]);
            $this->replyWithMessage(TelegramMessages::MSG_DATA_ERROR);

            return;
        }

        // Подтверждение/отмена записи после создания (без состояния — запись уже создана)
        if (str_starts_with($action, 'apt_confirm_') || str_starts_with($action, 'apt_cancel_')) {
            $appointmentId = (int) str_replace(['apt_confirm_', 'apt_cancel_'], '', $action);
            $isConfirm = str_starts_with($action, 'apt_confirm_');

            $appointment = Appointment::with('client')->find($appointmentId);
            if (! $appointment || $appointment->source !== 'telegram') {
                return;
            }
            $client = $appointment->client;
            if (! $client || (string) $client->telegram_user_id !== (string) $userId) {
                return;
            }

            $oldStatus = $appointment->status;
            $appointment->update([
                'status' => $isConfirm ? 'confirmed' : 'cancelled',
            ]);

            \App\Services\AppointmentNotificationService::notifyStatusChanged(
                $appointment,
                $oldStatus,
            );

            $resultText = $isConfirm
                ? TelegramMessages::MSG_APPOINTMENT_CONFIRMED_BY_YOU
                : TelegramMessages::MSG_APPOINTMENT_CANCELLED_BY_YOU;

            if ($messageId) {
                try {
                    $this->chat->edit($messageId)->message($resultText)->send();
                    $this->chat->replaceKeyboard($messageId, Keyboard::make())->send();
                } catch (\Throwable $e) {
                    Log::warning('Failed to edit appointment confirm message', [
                        'message_id' => $messageId,
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            return;
        }

        // Обработка выбора бизнеса из каталога (может быть без состояния)
        if (str_starts_with($action, 'business_')) {
            $businessId = str_replace('business_', '', $action);
            $business = Business::with([
                'locations',
                'services',
                'users',
            ])->find($businessId);

            if (! $business) {
                $this->replyWithMessage(
                    TelegramMessages::MSG_BUSINESS_NOT_FOUND,
                );

                return;
            }

            // Проверяем доступ к Telegram боту для записи
            $subscriptionService = app(SubscriptionService::class);
            $owner = $this->getBusinessOwner($business);

            if ($owner) {
                $telegramBotEnabled =
                    $subscriptionService->getLimit(
                        $owner,
                        'telegram_bot_enabled',
                    ) === true;

                if (! $telegramBotEnabled) {
                    Log::warning(
                        'Telegram bot booking not enabled for business from catalog',
                        [
                            'user_id' => $userId,
                            'business_id' => $business->id,
                            'owner_id' => $owner->id,
                        ],
                    );
                    $this->replyWithMessage(
                        TelegramMessages::MSG_BOOKING_DISABLED,
                    );

                    return;
                }
            }

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
                Log::warning('Unknown page action: '.$action);
                $this->replyWithMessage(TelegramMessages::MSG_UNKNOWN_COMMAND);

                return;
            }

            // Находим состояние пользователя для определения типа списка
            $state = TelegramUserState::where(
                'telegram_user_id',
                $userId,
            )->first();

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
            return;
        }

        // Находим состояние пользователя
        $states = TelegramUserState::where('telegram_user_id', $userId)->get();

        if ($states->isEmpty()) {
            Log::warning('No states found for user: '.$userId);
            $this->replyWithMessage(TelegramMessages::MSG_SESSION_NOT_FOUND);

            return;
        }

        // Ищем состояние с конкретным бизнесом (не поисковое)
        $state = $states->firstWhere('business_id', '!=', null);

        // Если не нашли состояние с бизнесом, используем первое (может быть поиск)
        if (! $state) {
            $state = $states->first();
        }

        $business = $state->business;

        // У пользователя может быть только состояние поиска (без бизнеса) — тогда кнопки записи от старого сообщения невалидны
        if (! $business) {
            $this->replyWithMessage(TelegramMessages::MSG_SESSION_EXPIRED);

            return;
        }

        if (str_starts_with($action, 'location_')) {
            $locationId = str_replace('location_', '', $action);
            $this->showServiceSelection($business, $locationId);
        } elseif (str_starts_with($action, 'service_')) {
            $serviceId = str_replace('service_', '', $action);
            $locationId = $state?->data['location_id'] ?? null;
            if ($locationId) {
                $this->showMasterSelection($business, $locationId, $serviceId);
            } else {
                Log::error('Location ID not found in state data');
                $this->replyWithMessage(TelegramMessages::MSG_NOT_FOUND);
            }
        } elseif (str_starts_with($action, 'master_')) {
            $masterIdRaw = str_replace('master_', '', $action);
            $masterId = $masterIdRaw === 'any' ? null : (int) $masterIdRaw;
            $locationId = $state?->data['location_id'] ?? null;
            $serviceId = $state?->data['service_id'] ?? null;
            if ($locationId && $serviceId) {
                $this->showTimeSelection(
                    $business,
                    $locationId,
                    $serviceId,
                    $masterId,
                );
            } else {
                Log::error('Location or Service ID not found in state data');
                $this->replyWithMessage(TelegramMessages::MSG_NOT_FOUND);
            }
        } elseif (str_starts_with($action, 'date_')) {
            $date = str_replace('date_', '', $action);

            // Проверяем, что это не недоступная дата
            if (str_starts_with($date, 'disabled_')) {
                return;
            }

            // Обработка специальной кнопки "След. месяц"
            if ($date === 'next_month') {
                $locationId = $state?->data['location_id'] ?? null;
                $serviceId = $state?->data['service_id'] ?? null;
                $masterId = $state?->data['master_id'] ?? null;
                $currentMonth = $state?->data['month'] ?? null;

                if ($locationId && $serviceId) {
                    $monthDate = $currentMonth
                        ? Carbon::parse($currentMonth.'-01')
                        : Carbon::today();
                    $nextMonth = $monthDate->addMonth()->format('Y-m');
                    $this->showTimeSelection(
                        $business,
                        $locationId,
                        $serviceId,
                        $masterId,
                        $nextMonth,
                    );
                }

                return;
            }

            $this->showTimeSlots($business, $date, $state);
        } elseif (str_starts_with($action, 'calendar_prev_')) {
            $month = str_replace('calendar_prev_', '', $action);
            $monthDate = Carbon::parse($month.'-01');
            $prevMonth = $monthDate->subMonth()->format('Y-m');

            $locationId = $state?->data['location_id'] ?? null;
            $serviceId = $state?->data['service_id'] ?? null;
            $masterId = $state?->data['master_id'] ?? null;

            if ($locationId && $serviceId) {
                $this->showTimeSelection(
                    $business,
                    $locationId,
                    $serviceId,
                    $masterId,
                    $prevMonth,
                );
            }
        } elseif (str_starts_with($action, 'calendar_next_')) {
            $month = str_replace('calendar_next_', '', $action);
            $monthDate = Carbon::parse($month.'-01');
            $nextMonth = $monthDate->addMonth()->format('Y-m');

            $locationId = $state?->data['location_id'] ?? null;
            $serviceId = $state?->data['service_id'] ?? null;
            $masterId = $state?->data['master_id'] ?? null;

            if ($locationId && $serviceId) {
                $this->showTimeSelection(
                    $business,
                    $locationId,
                    $serviceId,
                    $masterId,
                    $nextMonth,
                );
            }
        } elseif (str_starts_with($action, 'disabled_')) {
            // Недоступная дата или заголовок - игнорируем
            return;
        } elseif (str_starts_with($action, 'time_')) {
            $time = str_replace('time_', '', $action);
            $this->handleTimeSelection($business, $time, $state);
        } elseif ($action === 'skip_notes') {
            $data = $state->data;
            TelegramUserState::updateStateKeepMessageId(
                $userId,
                $business->id,
                TelegramUserState::STEP_CONFIRM_APPOINTMENT,
                $data,
            );
            $this->showAppointmentConfirmation($business, $data);
        } elseif ($action === 'confirm_appointment') {
            $this->createAppointment($business, $state);
        } elseif ($action === 'cancel') {
            // Удаляем старое сообщение с кнопками
            $this->deleteBotMessage($messageId);

            // Очищаем все состояния пользователя
            TelegramUserState::clearState($userId);

            // Сбрасываем lastMessageId чтобы отправить новое сообщение
            $this->lastMessageId = null;

            $this->replyWithMessage(TelegramMessages::MSG_CANCEL);
        } elseif ($action === 'restart') {
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
            Log::warning('Unknown action: '.$action);
            $this->replyWithMessage(TelegramMessages::MSG_UNKNOWN_COMMAND);
        }
    }

    /**
     * Показ выбора даты (календарь)
     */
    protected function showTimeSelection(
        Business $business,
        $locationId,
        $serviceId,
        $masterId,
        ?string $month = null,
    ) {
        $location = $business->locations()->find($locationId);
        $service = $business->services()->find($serviceId);
        $master = $masterId !== null ? $business->masters()->find($masterId) : null;

        if (! $location || ! $service) {
            $this->replyWithMessage(TelegramMessages::MSG_NOT_FOUND);

            return;
        }

        // При выборе конкретного мастера проверяем, что он есть
        if ($masterId !== null && ! $master) {
            $this->replyWithMessage(TelegramMessages::MSG_NOT_FOUND);

            return;
        }

        // Определяем месяц для отображения
        if (! $month) {
            $month = Carbon::today()->format('Y-m');
        }

        // Получаем доступные даты для месяца (masterId = null — «любой мастер»)
        $availableDates = TelegramKeyboards::getAvailableDatesForMonth(
            $this->slotService,
            $serviceId,
            $masterId,
            $locationId,
            $month,
        );

        // Формируем сообщение
        $dateHeader = Carbon::parse($month.'-01')
            ->locale('ru')
            ->isoFormat('MMMM YYYY');
        $message = $master
            ? TelegramMessages::format(TelegramMessages::MSG_SELECT_DATE, [
                'master' => $master->first_name.' '.$master->last_name,
            ])."\n\n".$dateHeader
            : TelegramMessages::MSG_SELECT_DATE_ANY_MASTER."\n\n".$dateHeader;

        // Проверяем возможность перехода к предыдущему месяцу
        $hasPrevMonth = TelegramKeyboards::hasPrevMonth($month);

        // Создаем клавиатуру календаря
        $keyboard = TelegramKeyboards::calendar(
            $month,
            $availableDates,
            null,
            $hasPrevMonth,
        );

        $this->replyWithMessage($message, $keyboard);

        $userId =
            $this->callbackQuery?->from()->id() ?? $this->message->from()->id();

        // Сохраняем доступные даты и месяц в состояние
        TelegramUserState::updateStateKeepMessageId(
            $userId,
            $business->id,
            TelegramUserState::STEP_SELECT_DATE,
            [
                'location_id' => $locationId,
                'service_id' => $serviceId,
                'master_id' => $masterId,
                'month' => $month,
                'available_dates' => $availableDates,
            ],
        );
    }

    /**
     * Показ выбора времени
     */
    protected function showTimeSlots(Business $business, $date, $state)
    {
        $locationId = $state?->data['location_id'] ?? null;
        $serviceId = $state?->data['service_id'] ?? null;
        $masterId = $state?->data['master_id'] ?? null;

        if (! $locationId || ! $serviceId) {
            $this->replyWithMessage(TelegramMessages::MSG_NOT_FOUND);

            return;
        }

        $debugInfo = [];
        $availableSlots = $this->slotService->getAvailableSlots(
            $serviceId,
            $date,
            $masterId,
            $locationId,
            $debugInfo,
        );

        if (empty($availableSlots)) {
            $this->replyWithMessage(
                TelegramMessages::MSG_NO_SLOTS,
                TelegramKeyboards::timesEmpty(),
            );

            return;
        }

        $formattedDate = Carbon::parse($date)
            ->locale('ru')
            ->format('d.m.Y (l)');
        $message = TelegramMessages::format(TelegramMessages::MSG_SELECT_TIME, [
            'date' => $formattedDate,
        ]);

        $this->replyWithMessage(
            $message,
            TelegramKeyboards::times($availableSlots),
        );

        $userId =
            $this->callbackQuery?->from()->id() ?? $this->message->from()->id();

        // Сохраняем месяц для возврата из выбора времени
        $month = Carbon::parse($date)->format('Y-m');

        TelegramUserState::updateStateKeepMessageId(
            $userId,
            $business->id,
            TelegramUserState::STEP_SELECT_TIME,
            [
                'location_id' => $locationId,
                'service_id' => $serviceId,
                'master_id' => $masterId,
                'date' => $date,
                'month' => $month,
            ],
        );
    }

    /**
     * Обработка выбора времени
     */
    protected function handleTimeSelection(Business $business, $time, $state)
    {
        if (! $state) {
            $this->replyWithMessage(TelegramMessages::MSG_SESSION_EXPIRED);

            return;
        }

        $data = $state->data;

        // Форматируем время
        if (is_string($time)) {
            $time = trim($time);

            if (! str_contains($time, ':')) {
                $time = $time.':00';
            }

            try {
                $carbonTime = Carbon::parse($time);
                $time = $carbonTime->format('H:i');
            } catch (\Exception $e) {
                Log::error(
                    'Error parsing time: '.
                        $e->getMessage().
                        ', time: '.
                        $time,
                );
                $time = '12:00';
            }
        }

        $data['time'] = $time;

        TelegramUserState::updateStateKeepMessageId(
            $state->telegram_user_id,
            $business->id,
            TelegramUserState::STEP_ENTER_CLIENT_INFO,
            $data,
        );

        $message = TelegramMessages::MSG_ENTER_NAME;

        $this->replyWithMessage(
            $message,
            TelegramKeyboards::restartAndCancel(),
        );
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

        try {
            // Получаем пользователя через бизнес
            $user = $business->users()->first();
            if (! $user) {
                $this->replyWithMessage(
                    TelegramMessages::MSG_REQUEST_ERROR,
                );

                return;
            }

            $subscriptionService = app(
                \App\Services\SubscriptionService::class,
            );

            // Проверяем лимит записей в месяц
            if (! $subscriptionService->canCreateAppointment($user)) {
                \App\Services\AdminNotificationService::notifySubscriptionLimitExceededIfNotThrottled(
                    $business,
                    'max_appointments_per_month',
                );
                $this->replyWithMessage(
                    TelegramMessages::MSG_MONTHLY_LIMIT,
                );

                return;
            }

            $phone = $data['client_data']['phone'];
            $client = Client::where('business_id', $business->id)->where('phone', $phone)->first();
            if (! $client) {
                $client = Client::where('business_id', $business->id)
                    ->whereHas('phones', fn ($q) => $q->where('phone', $phone))
                    ->first();
            }

            // Определяем страну по префиксу номера для phone_country_code (SMS и др.)
            $countryBy = \App\Models\Country::findByPhonePrefix($phone);

            if (! $client) {
                if (! $subscriptionService->canCreateClient($user)) {
                    \App\Services\AdminNotificationService::notifySubscriptionLimitExceededIfNotThrottled(
                        $business,
                        'max_clients',
                    );
                    $this->replyWithMessage(
                        TelegramMessages::MSG_CLIENT_LIMIT,
                    );

                    return;
                }

                $client = Client::create([
                    'business_id' => $business->id,
                    'first_name' => $data['client_data']['first_name'],
                    'last_name' => $data['client_data']['last_name'] ?? null,
                    'email' => $data['client_data']['email'] ?? null,
                    'telegram_user_id' => (string) $this->callbackQuery->from()->id(),
                    'phone' => $phone,
                    'phone_country_code' => $countryBy?->code ?? 'BY',
                ]);
            } else {
                $client->update([
                    'first_name' => $data['client_data']['first_name'],
                    'last_name' => $data['client_data']['last_name'] ?? $client->last_name,
                    'email' => $data['client_data']['email'] ?? $client->email,
                    'telegram_user_id' => (string) $this->callbackQuery->from()->id(),
                    'phone' => $phone,
                    'phone_country_code' => $countryBy?->code ?? $client->phone_country_code,
                ]);
            }

            // Форматируем время
            $time = $data['time'];
            if (is_string($time) && ! str_contains($time, ':')) {
                $time = $time.':00';
            }
            $time = Carbon::parse($time)->format('H:i');

            $appointment = Appointment::create([
                'business_id' => $business->id,
                'client_id' => $client->id,
                'service_id' => $data['service_id'],
                'master_id' => $data['master_id'] ?? null,
                'location_id' => $data['location_id'],
                'date' => $data['date'],
                'time' => $time,
                'source' => 'telegram',
                'status' => 'pending',
                'notes' => $data['client_data']['notes'] ?? null,
            ]);

            // Увеличиваем usage для месячной метрики
            $subscriptionService->incrementUsage(
                $user,
                'max_appointments_per_month',
            );

            // Удаляем старое сообщение с кнопками подтверждения
            $this->deleteBotMessage($this->lastMessageId);

            TelegramUserState::clearState(
                $state->telegram_user_id,
                $business->id,
            );

            // Отправляем уведомления пользователям бизнеса (включая Telegram)
            \App\Services\AppointmentNotificationService::notifyCreated(
                $appointment,
            );

            $appointment->load(['service', 'master', 'location']);

            // Форматируем для сообщения
            $formattedDate = $appointment->date->format('d.m.Y');
            $formattedTime = $appointment->time;

            // Сбрасываем lastMessageId чтобы отправить новое сообщение
            $this->lastMessageId = null;

            $masterName = $appointment->master && ! $appointment->master->trashed()
                ? trim(($appointment->master->first_name ?? '').' '.($appointment->master->last_name ?? ''))
                : TelegramMessages::MSG_ANY_MASTER;

            $message = TelegramMessages::format(
                TelegramMessages::MSG_APPOINTMENT_CREATED,
                [
                    'date' => $formattedDate,
                    'time' => $formattedTime,
                    'service' => $appointment->service?->name ?? 'Услуга удалена',
                    'master' => $masterName,
                    'location' => $appointment->location?->name ?? 'Локация удалена',
                ],
            );

            $this->replyWithMessage($message);

            // Очищаем ID после отправки финального сообщения
            $this->lastMessageId = null;
        } catch (\Exception $e) {
            Log::error('Error creating appointment: '.$e->getMessage());
            $this->replyWithMessage(TelegramMessages::MSG_ERROR);
        }
    }

    /**
     * Удаляет сообщение пользователя
     */
    protected function deleteUserMessage(int $messageId): void
    {
        try {
            // Метод delete() в DefStudio\Telegraph возвращает true, но не удаляет
            // Нужно использовать deleteMessage() и вызвать send()
            $this->chat->deleteMessage($messageId)->send();
        } catch (\Exception $e) {
            Log::error('✗ Failed to delete user message: '.$e->getMessage());
        }
    }

    /**
     * Удаляет сообщение бота
     */
    protected function deleteBotMessage(?int $messageId): void
    {
        if (! $messageId) {
            return;
        }

        try {
            $this->chat->deleteMessage($messageId)->send();
        } catch (\Exception $e) {
            Log::warning('✗ Failed to delete bot message: '.$e->getMessage());
        }
    }

    /**
     * Получить владельца бизнеса
     */
    protected function getBusinessOwner(Business $business): ?User
    {
        $ownerRole = BusinessRole::where('slug', 'owner')->first();

        if (! $ownerRole) {
            return null;
        }

        $ownerPivot = DB::table('business_user')
            ->where('business_id', $business->id)
            ->where('role_id', $ownerRole->id)
            ->first();

        if (! $ownerPivot) {
            // Fallback: пробуем получить первого пользователя бизнеса
            return $business->users()->first();
        }

        return User::find($ownerPivot->user_id);
    }
}
