<?php

namespace App\Telegram;

use App\Models\Appointment;
use App\Models\Business;
use App\Models\Client;
use App\Models\TelegramUserState;
use App\Services\AppointmentSlotService;
use App\Services\TelegramNotificationService;
use Carbon\Carbon;
use DefStudio\Telegraph\Handlers\WebhookHandler;
use DefStudio\Telegraph\Keyboard\Button;
use DefStudio\Telegraph\Keyboard\Keyboard;
use Illuminate\Support\Facades\Log;

class Handler extends WebhookHandler
{
    protected AppointmentSlotService $slotService;
    protected ?int $lastMessageId = null;

    public function __construct(AppointmentSlotService $slotService)
    {
        $this->slotService = $slotService;
    }

    /**
     * Основной метод для отправки/редактирования сообщений
     */
    protected function replyWithMessage(string $message, ?Keyboard $keyboard = null): void
    {
        Log::info('=== replyWithMessage START ===');
        Log::info('Input', [
            'lastMessageId' => $this->lastMessageId,
            'message_length' => strlen($message),
            'has_keyboard' => !is_null($keyboard),
            'message_preview' => substr($message, 0, 50)
        ]);

        try {
            if ($this->lastMessageId) {
                Log::info('Attempting to EDIT message', ['message_id' => $this->lastMessageId]);
                
                // Редактируем существующее сообщение
                $this->chat->edit($this->lastMessageId)
                    ->message($message)
                    ->send();
                
                Log::info('✓ Message edited successfully');
                
                // Если есть новая клавиатура, заменяем ее
                if ($keyboard) {
                    $this->chat->replaceKeyboard($this->lastMessageId, $keyboard)->send();
                    Log::info('✓ Keyboard replaced');
                }
                
                // ВАЖНО: Сохраняем ID даже при редактировании
                $this->saveMessageIdToState();
            } else {
                Log::info('No lastMessageId, sending NEW message');
                
                // Отправляем новое сообщение
                $response = $this->chat->message($message);
                
                if ($keyboard) {
                    $response = $response->keyboard($keyboard);
                }
                
                $result = $response->send();
                
                // Сохраняем ID сообщения
                $this->lastMessageId = $result->telegraphMessageId();
                Log::info('✓ New message sent', ['message_id' => $this->lastMessageId]);
                
                // Сохраняем в базу для последующего восстановления
                $this->saveMessageIdToState();
            }
        } catch (\Exception $e) {
            Log::error('✗ Error in replyWithMessage: ' . $e->getMessage());
            
            // Если редактирование не удалось, отправляем новое сообщение
            $this->lastMessageId = null;
            
            $response = $this->chat->message($message);
            
            if ($keyboard) {
                $response = $response->keyboard($keyboard);
            }
            
            $result = $response->send();
            $this->lastMessageId = $result->telegraphMessageId();
            
            Log::info('✓ New message sent after error', ['message_id' => $this->lastMessageId]);
            
            // Сохраняем в базу даже при ошибке
            $this->saveMessageIdToState();
        }
        
        Log::info('=== replyWithMessage END ===');
    }

    /**
     * Сохраняет ID последнего сообщения в состояние пользователя
     */
    protected function saveMessageIdToState(): void
    {
        Log::info('=== saveMessageIdToState START ===');
        try {
            // Получаем userId из callback или message
            $userId = $this->callbackQuery?->from()->id() ?? $this->message?->from()->id();
            
            Log::info('Attempting to save', [
                'user_id' => $userId,
                'lastMessageId' => $this->lastMessageId
            ]);
            
            if (!$userId || !$this->lastMessageId) {
                Log::warning('✗ Skipping save - missing data', [
                    'user_id' => $userId,
                    'lastMessageId' => $this->lastMessageId
                ]);
                return;
            }
            
            // Находим текущее состояние пользователя
            $state = TelegramUserState::where('telegram_user_id', $userId)->first();
            
            if ($state) {
                TelegramUserState::setMessageId($userId, $state->business_id, $this->lastMessageId);
                Log::info('✓ Saved to state', [
                    'user_id' => $userId,
                    'business_id' => $state->business_id,
                    'message_id' => $this->lastMessageId,
                    'step' => $state->step
                ]);
            } else {
                Log::warning('✗ No state found for user: ' . $userId);
            }
        } catch (\Exception $e) {
            Log::error('✗ Failed to save message_id: ' . $e->getMessage());
        }
        Log::info('=== saveMessageIdToState END ===');
    }

    /**
     * Обработчик для всех текстовых сообщений
     */
    protected function handleChatMessage(\Illuminate\Support\Stringable $text): void
    {
        $messageText = $text->toString();
        Log::info('=== handleChatMessage START ===');
        Log::info('Received text: ' . $messageText);

        $userId = $this->message->from()->id();
        $states = TelegramUserState::where('telegram_user_id', $userId)->get();

        $messageId = $this->message->id();
        
        if ($states->isNotEmpty()) {
            $state = $states->first();
            $business = $state->business;
            
            Log::info('State found', [
                'step' => $state->step,
                'last_message_id_in_db' => $state->last_message_id,
                'telegram_user_id' => $userId,
                'business_id' => $business->id
            ]);
            
            // Восстанавливаем lastMessageId из состояния
            if ($state->last_message_id) {
                $this->lastMessageId = $state->last_message_id;
                Log::info('✓ Restored lastMessageId', ['message_id' => $this->lastMessageId]);
            } else {
                Log::warning('✗ No last_message_id in state');
            }
            
            if ($state && $state->step !== 'start') {
                $this->handleTextMessage($messageText, $state, $business);
                
                // Удаляем сообщение пользователя после обработки
                Log::info('About to delete user message', ['message_id' => $messageId]);
                $this->deleteUserMessage($messageId);
                
                Log::info('=== handleChatMessage END ===');
                return;
            }
        } else {
            Log::warning('No state found for user: ' . $userId);
        }

        // На неизвестные сообщения просто отвечаем
        $this->replyWithMessage('Привет! Для записи используйте ссылку с сайта бизнеса.');
        
        // Удаляем сообщение пользователя
        $this->deleteUserMessage($messageId);
        
        Log::info('=== handleChatMessage END ===');
    }

    /**
     * Команда /start - основная точка входа
     */
    public function start()
    {
        $text = $this->message->text() ?? '';
        Log::info('Start command received: ' . $text);

        $parts = explode(' ', $text);

        if (isset($parts[1])) {
        if (str_starts_with($parts[1], 'auth_')) {
            // Подключение бизнеса
            $token = str_replace('auth_', '', $parts[1]);
            $business = Business::where('telegram_token', $token)->first();
            
            if ($business) {
                $business->update(['telegram_chat_id' => $this->chat->chat_id]);
                $this->replyWithMessage('✅ Аккаунт подключен!\n\nВы будете получать уведомления.');
            } else {
                $this->replyWithMessage('Бизнес не найден.');
            }
            return;
        }

            // Начало записи
            $slug = $parts[1];
            $business = Business::where('slug', $slug)->first();

            if ($business) {
                $this->startBookingProcess($business);
            } else {
                $this->replyWithMessage('Бизнес не найден.');
            }
            return;
        }

        // Если просто /start без параметров
        $this->replyWithMessage('Привет! Для записи используйте ссылку с сайта бизнеса.');
    }

    /**
     * Команда /list - список бизнесов
     */
    public function list()
    {
        $businesses = Business::select('name', 'slug')->get();

        if ($businesses->isEmpty()) {
            $this->replyWithMessage('Нет доступных бизнесов.');
            return;
        }

        $message = "Доступные бизнесы:\n";
        foreach ($businesses as $business) {
            $message .= "- {$business->name} (slug: {$business->slug})\n";
        }

        $this->replyWithMessage($message);
    }

    /**
     * Обработка текстовых сообщений во время записи
     */
    protected function handleTextMessage(string $text, $state, $business)
    {
        Log::info('handleTextMessage', ['step' => $state->step, 'text' => $text]);
        
        // Проверка на команду отмены
        if (mb_strtolower(trim($text)) === 'отмена' || mb_strtolower(trim($text)) === 'cancel') {
            TelegramUserState::clearState($state->telegram_user_id, $business->id);
            $this->replyWithMessage('❌ Отменено.');
            $this->lastMessageId = null;
            return;
        }
        
        switch ($state->step) {
            case 'enter_client_info':
                $this->handleClientInfo($business, $text, $state);
                break;
            case 'enter_phone':
                $this->handlePhone($business, $text, $state);
                break;
            case 'enter_notes':
                $this->handleNotes($business, $text, $state);
                break;
            default:
                Log::warning('Unknown step in handleTextMessage: ' . $state->step);
                $this->replyWithMessage('Неизвестный шаг. Начните заново.');
        }
    }

    /**
     * Обработка имени клиента
     */
    protected function handleClientInfo(Business $business, string $text, $state)
    {
        Log::info('handleClientInfo', ['text' => $text]);
        
        $name = trim($text);
        
        if (empty($name)) {
            $keyboard = Keyboard::make()->row([
                Button::make('❌ Отмена')->action('cancel'),
            ]);
            $this->replyWithMessage('❌ Имя не может быть пустым:', $keyboard);
            return;
        }
        
        if (mb_strlen($name) < 2) {
            $keyboard = Keyboard::make()->row([
                Button::make('❌ Отмена')->action('cancel'),
            ]);
            $this->replyWithMessage('❌ Имя слишком короткое:', $keyboard);
            return;
        }
        
        $data = $state->data;
        $data['client_data']['first_name'] = $name;

        TelegramUserState::updateStateKeepMessageId($state->telegram_user_id, $business->id, 'enter_phone', $data);
        
        $keyboard = Keyboard::make()->row([
            Button::make('⬅️ Назад')->action('back_to_time'),
            Button::make('❌ Отмена')->action('cancel'),
        ]);
        
        $message = "✅ Имя: {$name}\n\n" .
            "📱 Введите телефон:\n" .
            "Пример: +79001234567";
        
        $this->replyWithMessage($message, $keyboard);
    }

    /**
     * Обработка телефона клиента
     */
    protected function handlePhone(Business $business, string $text, $state)
    {
        Log::info('handlePhone', ['text' => $text]);
        
        $phone = trim($text);
        
        // Очистка и автоформатирование
        $cleaned = preg_replace('/[^0-9+]/', '', $phone);
        
        if (str_starts_with($cleaned, '8')) {
            $cleaned = '+7' . substr($cleaned, 1);
        }
        
        if (!str_starts_with($cleaned, '+')) {
            $cleaned = '+' . $cleaned;
        }
        
        if (!preg_match('/^\+\d{10,15}$/', $cleaned)) {
            $keyboard = Keyboard::make()->row([
                Button::make('❌ Отмена')->action('cancel'),
            ]);
            $this->replyWithMessage(
                "❌ Неверный формат\n\n" .
                "Правильно: +79001234567\n" .
                "Или: 89001234567\n\n" .
                "Введите номер:", 
                $keyboard
            );
            return;
        }
        
        $data = $state->data;
        $data['client_data']['phone'] = $cleaned;

        TelegramUserState::updateStateKeepMessageId($state->telegram_user_id, $business->id, 'enter_notes', $data);
        
        $keyboard = Keyboard::make()->row([
            Button::make('Пропустить')->action('skip_notes'),
            Button::make('❌ Отмена')->action('cancel'),
        ]);
        
        $message = "✅ Имя: {$data['client_data']['first_name']}\n" .
            "✅ Телефон: {$cleaned}\n\n" .
            "📝 Примечание (необязательно):\n" .
            "Аллергия, предпочтения и т.д.\n\n" .
            "Или нажмите Пропустить";
        
        $this->replyWithMessage($message, $keyboard);
    }

    /**
     * Обработка примечаний клиента
     */
    protected function handleNotes(Business $business, string $text, $state)
    {
        Log::info('handleNotes', ['text' => $text]);
        
        $notes = trim($text);
        
        // Авто-пропуск для фраз означающих "нет"
        $skipWords = ['нет', 'нечего', 'нету', 'пропустить', 'skip', '-', 'н'];
        if (empty($notes) || in_array(mb_strtolower($notes), $skipWords)) {
            $data = $state->data;
            TelegramUserState::updateStateKeepMessageId($state->telegram_user_id, $business->id, 'confirm_appointment', $data);
            $this->showAppointmentConfirmation($business, $data);
            return;
        }
        
        if (mb_strlen($notes) > 200) {
            $keyboard = Keyboard::make()->row([
                Button::make('Пропустить')->action('skip_notes'),
                Button::make('❌ Отмена')->action('cancel'),
            ]);
            $this->replyWithMessage(
                "❌ Слишком длинно ({$notes} символов, макс. 200)\n\n" .
                "Сократите:", 
                $keyboard
            );
            return;
        }
        
        $data = $state->data;
        $data['client_data']['notes'] = $notes;

        TelegramUserState::updateStateKeepMessageId($state->telegram_user_id, $business->id, 'confirm_appointment', $data);
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

        // Форматируем дату
        $date = Carbon::parse($data['date'])->format('d.m.Y');
        
        // Форматируем время
        $time = $data['time'];
        if (is_string($time) && !str_contains($time, ':')) {
            $time = $time . ':00';
        }
        $time = Carbon::parse($time)->format('H:i');

        $message = "✅ ПОДТВЕРЖДЕНИЕ\n\n" .
            "📍 Локация: {$location->name}\n" .
            "💇‍♀️ Услуга: {$service->name}\n" .
            "👨‍💼 Мастер: {$master->first_name} {$master->last_name}\n" .
            "📅 Дата: {$date}\n" .
            "⏰ Время: {$time}\n" .
            "👤 Клиент: {$data['client_data']['first_name']}\n" .
            "📱 Телефон: {$data['client_data']['phone']}\n";

        if (isset($data['client_data']['notes']) && !empty($data['client_data']['notes'])) {
            $message .= "📝 Примечание: {$data['client_data']['notes']}\n";
        }

        $keyboard = Keyboard::make()
            ->row([
                Button::make('✅ Подтвердить')->action('confirm_appointment'),
                Button::make('❌ Отмена')->action('cancel'),
            ])
            ->row([
                Button::make('Имя')->action('edit_name'),
                Button::make('Телефон')->action('edit_phone'),
                Button::make('Примечание')->action('edit_notes'),
            ])
            ->row([
                Button::make('⬅️ Назад')->action('back_to_time'),
            ]);

        $this->replyWithMessage($message, $keyboard);
    }

    /**
     * Начало процесса записи
     */
    protected function startBookingProcess(Business $business)
    {
        $userId = $this->message->from()->id();

        // Очищаем предыдущее состояние
        TelegramUserState::clearState($userId, $business->id);
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

        $locations = $business->locations()->orderBy('name')->get();

        if ($locations->isEmpty()) {
            $this->replyWithMessage('❌ Нет доступных локаций.');
            return;
        }

        // Создаем кнопки локаций
        $locationButtons = [];
        foreach ($locations as $location) {
            $locationButtons[] = Button::make($location->name)->action("location_{$location->id}");
        }

        // Создаем клавиатуру: сначала локации сеткой 2 в строку, потом отмена
        $keyboard = Keyboard::make()
            ->row($locationButtons)  // Добавляем все кнопки локаций
            ->chunk(2)               // Разбиваем на сетку 2×N
            ->row([                  // Добавляем отмену отдельной строкой
                Button::make('❌ Отмена')->action('cancel'),
            ]);

        $message = "📍 Выберите локацию:";
        
        $this->replyWithMessage($message, $keyboard);

        // Сохраняем состояние
        TelegramUserState::updateStateKeepMessageId($userId, $business->id, 'select_location');
    }

    /**
     * Показ выбора услуги
     */
    protected function showServiceSelection(Business $business, $locationId)
    {
        Log::info('showServiceSelection called', ['location_id' => $locationId]);
        
        $location = $business->locations()->find($locationId);
        if (!$location) {
            $this->replyWithMessage('Локация не найдена.');
            return;
        }

        $services = $location->services()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        if ($services->isEmpty()) {
            $services = $business->services()
                ->where('is_active', true)
                ->orderBy('name')
                ->get();
        }

        if ($services->isEmpty()) {
            $this->replyWithMessage('❌ Нет доступных услуг.');
            return;
        }

        // Создаем кнопки услуг
        $serviceButtons = [];
        foreach ($services as $service) {
            $serviceButtons[] = Button::make("{$service->name} ({$service->duration} мин)")->action("service_{$service->id}");
        }

        // Создаем клавиатуру: сначала услуги сеткой 2 в строку, потом навигация
        $keyboard = Keyboard::make()
            ->row($serviceButtons)  // Добавляем все кнопки услуг
            ->chunk(2)              // Разбиваем на сетку 2×N
            ->row([                 // Добавляем навигацию отдельной строкой
                Button::make('⬅️ Назад')->action('back_to_location'),
                Button::make('❌ Отмена')->action('cancel'),
            ]);

        $message = "💇‍♀️ Выберите услугу для \"{$location->name}\":";
        
        $this->replyWithMessage($message, $keyboard);

        $userId = $this->callbackQuery?->from()->id() ?? $this->message->from()->id();
        TelegramUserState::updateStateKeepMessageId($userId, $business->id, 'select_service', [
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

        if (!$location || !$service) {
            $this->replyWithMessage('❌ Данные не найдены.');
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
            $this->replyWithMessage('❌ Нет доступных мастеров.');
            return;
        }

        // Создаем кнопки мастеров
        $masterButtons = [];
        foreach ($masters as $master) {
            $masterButtons[] = Button::make($master->first_name . ' ' . $master->last_name)->action("master_{$master->id}");
        }

        // Создаем клавиатуру: сначала мастера сеткой 2 в строку, потом навигация
        $keyboard = Keyboard::make()
            ->row($masterButtons)  // Добавляем все кнопки мастеров
            ->chunk(2)             // Разбиваем на сетку 2×N
            ->row([                // Добавляем навигацию отдельной строкой
                Button::make('⬅️ Назад')->action('back_to_service'),
                Button::make('❌ Отмена')->action('cancel'),
            ]);

        $message = "👨‍💼 Выберите мастера для \"{$service->name}\":";
        
        $this->replyWithMessage($message, $keyboard);

        $userId = $this->callbackQuery?->from()->id() ?? $this->message->from()->id();
        TelegramUserState::updateStateKeepMessageId($userId, $business->id, 'select_master', [
            'location_id' => $locationId,
            'service_id' => $serviceId,
        ]);
    }

    /**
     * Обработка callback запросов
     */
    protected function handleCallbackQuery(): void
    {
        Log::info('=== handleCallbackQuery START ===');
        
        $callbackData = $this->callbackQuery->data();
        $userId = $this->callbackQuery->from()->id();
        
        // Получаем messageId
        $message = $this->callbackQuery->message();
        $messageId = $message ? $message->id() : null;

        Log::info('Callback details:', [
            'callback_data' => $callbackData,
            'callback_data_type' => gettype($callbackData),
            'callback_data_class' => get_class($callbackData),
            'user_id' => $userId,
            'message_id' => $messageId,
            'chat_id' => $this->chat->chat_id
        ]);

        // Сохраняем messageId для последующего редактирования
        if ($messageId) {
            $this->lastMessageId = $messageId;
        }

        // Извлекаем action из Collection
        $action = $callbackData->get('action');
        
        if (!$action) {
            Log::error('No action found in callback data', ['callback_data' => $callbackData->toArray()]);
            $this->replyWithMessage('❌ Ошибка данных. Попробуйте снова.');
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

        $state = $states->first();
        $business = $state->business;
        
        Log::info('Processing action:', [
            'action' => $action,
            'state_step' => $state->step,
            'business_id' => $business->id,
            'business_name' => $business->name
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
                'location_id' => $locationId
            ]);
            if ($locationId) {
                $this->showMasterSelection($business, $locationId, $serviceId);
            } else {
                Log::error('Location ID not found in state data');
                $this->replyWithMessage('❌ Не найдена локация.');
            }
        } elseif (str_starts_with($action, 'master_')) {
            $masterId = str_replace('master_', '', $action);
            $locationId = $state?->data['location_id'] ?? null;
            $serviceId = $state?->data['service_id'] ?? null;
            Log::info('Master selected:', [
                'master_id' => $masterId,
                'location_id' => $locationId,
                'service_id' => $serviceId
            ]);
            if ($locationId && $serviceId) {
                $this->showTimeSelection($business, $locationId, $serviceId, $masterId);
            } else {
                Log::error('Location or Service ID not found in state data');
                $this->replyWithMessage('❌ Не найдены данные.');
            }
        } elseif (str_starts_with($action, 'date_')) {
            $date = str_replace('date_', '', $action);
            Log::info('Date selected:', ['date' => $date]);
            $this->showTimeSlots($business, $date, $state);
        } elseif (str_starts_with($action, 'time_')) {
            $time = str_replace('time_', '', $action);
            Log::info('Time selected:', ['time' => $time]);
            Log::info('Before handleTimeSelection', [
                'lastMessageId' => $this->lastMessageId,
                'messageId_from_callback' => $messageId
            ]);
            $this->handleTimeSelection($business, $time, $state);
        } elseif ($action === 'skip_notes') {
            $data = $state->data;
            TelegramUserState::updateStateKeepMessageId($userId, $business->id, 'confirm_appointment', $data);
            $this->showAppointmentConfirmation($business, $data);
        } elseif ($action === 'confirm_appointment') {
            $this->createAppointment($business, $state);
        } elseif ($action === 'back_to_location') {
            $this->showLocationSelection($business);
        } elseif ($action === 'back_to_service') {
            $locationId = $state?->data['location_id'] ?? null;
            if ($locationId) {
                $this->showServiceSelection($business, $locationId);
            }
        } elseif ($action === 'back_to_master') {
            $locationId = $state?->data['location_id'] ?? null;
            $serviceId = $state?->data['service_id'] ?? null;
            if ($locationId && $serviceId) {
                $this->showMasterSelection($business, $locationId, $serviceId);
            }
        } elseif ($action === 'back_to_time') {
            $locationId = $state?->data['location_id'] ?? null;
            $serviceId = $state?->data['service_id'] ?? null;
            $masterId = $state?->data['master_id'] ?? null;
            if ($locationId && $serviceId && $masterId) {
                $this->showTimeSelection($business, $locationId, $serviceId, $masterId);
            }
        } elseif ($action === 'back_to_date') {
            $locationId = $state?->data['location_id'] ?? null;
            $serviceId = $state?->data['service_id'] ?? null;
            $masterId = $state?->data['master_id'] ?? null;
            $date = $state?->data['date'] ?? null;
            if ($locationId && $serviceId && $masterId && $date) {
                $this->showTimeSlots($business, $date, $state);
            }
        } elseif ($action === 'edit_name') {
            TelegramUserState::updateStateKeepMessageId($userId, $business->id, 'enter_client_info', $state->data);
            $keyboard = Keyboard::make()->row([
                Button::make('⬅️ Назад')->action('back_to_time'),
                Button::make('❌ Отмена')->action('cancel'),
            ]);
            $this->replyWithMessage('Имя:', $keyboard);
            
        } elseif ($action === 'edit_phone') {
            TelegramUserState::updateStateKeepMessageId($userId, $business->id, 'enter_phone', $state->data);
            $keyboard = Keyboard::make()->row([
                Button::make('⬅️ Назад')->action('back_to_name'),
                Button::make('❌ Отмена')->action('cancel'),
            ]);
            $this->replyWithMessage('Телефон:', $keyboard);
            
        } elseif ($action === 'edit_notes') {
            TelegramUserState::updateStateKeepMessageId($userId, $business->id, 'enter_notes', $state->data);
            $keyboard = Keyboard::make()->row([
                Button::make('Пропустить')->action('skip_notes'),
                Button::make('❌ Отмена')->action('cancel'),
            ]);
            $this->replyWithMessage('Примечание:', $keyboard);
            
        } elseif ($action === 'back_to_name') {
            TelegramUserState::updateStateKeepMessageId($userId, $business->id, 'enter_client_info', $state->data);
            $keyboard = Keyboard::make()->row([
                Button::make('⬅️ Назад')->action('back_to_time'),
                Button::make('❌ Отмена')->action('cancel'),
            ]);
            $this->replyWithMessage('Имя:', $keyboard);
            
        } elseif ($action === 'cancel') {
            // Удаляем старое сообщение с кнопками
            $this->deleteBotMessage($messageId);
            
            TelegramUserState::clearState($userId, $business->id);
            $this->replyWithMessage('❌ Отменено.');
            $this->lastMessageId = null;
        } else {
            Log::warning('Unknown action: ' . $action);
            $this->replyWithMessage('❌ Неизвестная команда.');
        }
        
        Log::info('=== handleCallbackQuery END ===');
    }

    /**
     * Показ выбора даты
     */
    protected function showTimeSelection(Business $business, $locationId, $serviceId, $masterId)
    {
        Log::info('showTimeSelection called', [
            'location_id' => $locationId,
            'service_id' => $serviceId,
            'master_id' => $masterId
        ]);
        
        $location = $business->locations()->find($locationId);
        $service = $business->services()->find($serviceId);
        $master = $business->masters()->find($masterId);

        if (!$location || !$service || !$master) {
            $this->replyWithMessage('❌ Данные не найдены.');
            return;
        }

        // Создаем кнопки дат
        $dateButtons = [];
        for ($i = 0; $i < 6; $i++) {
            $date = Carbon::today()->addDays($i);
            $dayName = $date->locale('ru')->dayName;
            $formattedDate = $date->format('d.m');
            $dateButtons[] = Button::make("{$dayName} {$formattedDate}")->action("date_{$date->format('Y-m-d')}");
        }

        // Создаем клавиатуру: сначала даты сеткой 3 в строку, потом навигация
        $keyboard = Keyboard::make()
            ->row($dateButtons)  // Добавляем все кнопки дат
            ->chunk(3)           // Разбиваем на сетку 3×N
            ->row([              // Добавляем навигацию отдельной строкой
                Button::make('⬅️ Назад')->action('back_to_master'),
                Button::make('❌ Отмена')->action('cancel'),
            ]);

        $message = "📅 Выберите дату для {$master->first_name} {$master->last_name}:";
        
        $this->replyWithMessage($message, $keyboard);

        $userId = $this->callbackQuery?->from()->id() ?? $this->message->from()->id();
        TelegramUserState::updateStateKeepMessageId($userId, $business->id, 'select_date', [
            'location_id' => $locationId,
            'service_id' => $serviceId,
            'master_id' => $masterId,
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

        if (!$locationId || !$serviceId || !$masterId) {
            $this->replyWithMessage('❌ Отсутствуют данные.');
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
            $keyboard = Keyboard::make();
            $keyboard = $keyboard->row([
                Button::make('⬅️ Другая дата')->action('back_to_time'),
                Button::make('❌ Отмена')->action('cancel'),
            ]);

            $this->replyWithMessage("❌ Нет свободного времени.", $keyboard);
            return;
        }

        // Создаем кнопки времени
        $timeButtons = [];
        foreach ($availableSlots as $slot) {
            // Убедимся, что слот в правильном формате
            if (str_contains($slot, ':')) {
                $slot = trim($slot);
                $timeButtons[] = Button::make($slot)->action("time_{$slot}");
            } else {
                $displayTime = $slot . ':00';
                $callbackTime = $slot;
                $timeButtons[] = Button::make($displayTime)->action("time_{$callbackTime}");
            }
        }

        // Создаем клавиатуру: сначала время сеткой 3 в строку, потом навигация
        $keyboard = Keyboard::make()
            ->row($timeButtons)  // Добавляем все кнопки времени
            ->chunk(3)           // Разбиваем на сетку 3×N
            ->row([              // Добавляем навигацию отдельной строкой
                Button::make('⬅️ Другая дата')->action('back_to_time'),
                Button::make('❌ Отмена')->action('cancel'),
            ]);

        $formattedDate = Carbon::parse($date)->locale('ru')->format('d.m.Y (l)');
        $message = "⏰ Выберите время на <b>{$formattedDate}</b>:";
        
        $this->replyWithMessage($message, $keyboard);

        $userId = $this->callbackQuery?->from()->id() ?? $this->message->from()->id();
        TelegramUserState::updateStateKeepMessageId($userId, $business->id, 'select_time', [
            'location_id' => $locationId,
            'service_id' => $serviceId,
            'master_id' => $masterId,
            'date' => $date,
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
            'last_message_id_before' => $this->lastMessageId
        ]);
        
        if (!$state) {
            $this->replyWithMessage('⏳ Сессия истекла. Начните запись заново.');
            return;
        }

        $data = $state->data;
        
        // Форматируем время
        if (is_string($time)) {
            $time = trim($time);
            
            // Добавляем :00 если нужно
            if (!str_contains($time, ':')) {
                $time = $time . ':00';
            }
            
            // Приводим к формату HH:MM
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
        TelegramUserState::updateStateKeepMessageId($state->telegram_user_id, $business->id, 'enter_client_info', $data);
        
        $keyboard = Keyboard::make()->row([
            Button::make('⬅️ Назад')->action('back_to_date'),
            Button::make('❌ Отмена')->action('cancel'),
        ]);
        
        $message = "👤 Введите ваше имя:";
        
        Log::info('Calling replyWithMessage', ['lastMessageId' => $this->lastMessageId]);
        $this->replyWithMessage($message, $keyboard);
        
        Log::info('=== handleTimeSelection END ===');
    }

    /**
     * Создание записи
     */
    protected function createAppointment(Business $business, $state)
    {
        if (!$state || !isset($state->data['client_data'])) {
            $this->replyWithMessage('❌ Данные не найдены.');
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
            'client_data' => $data['client_data']
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
                ]
            );

            // Форматируем время
            $time = $data['time'];
            if (is_string($time) && !str_contains($time, ':')) {
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
            
            $this->replyWithMessage("✅ Запись создана!\n\n" .
                "📅 Дата: {$formattedDate}\n" .
                "⏰ Время: {$formattedTime}\n" .
                "💇‍♀️ Услуга: {$appointment->service->name}\n" .
                "👨‍💼 Мастер: {$appointment->master->first_name} {$appointment->master->last_name}\n" .
                "📍 Локация: {$appointment->location->name}\n\n" .
                "Свяжемся для подтверждения.");
                
            // Очищаем ID после отправки финального сообщения
            $this->lastMessageId = null;
        } catch (\Exception $e) {
            Log::error('Error creating appointment: ' . $e->getMessage());
            $this->replyWithMessage('❌ Ошибка при создании записи. Попробуйте снова.');
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
        if (!$messageId) {
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