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
        Log::info('replyWithMessage called', [
            'has_last_message' => !is_null($this->lastMessageId),
            'last_message_id' => $this->lastMessageId,
            'message_length' => strlen($message),
            'has_keyboard' => !is_null($keyboard)
        ]);

        try {
            if ($this->lastMessageId) {
                // Редактируем существующее сообщение
                $this->chat->edit($this->lastMessageId)
                    ->message($message)
                    ->send();
                
                Log::info('Message edited', ['message_id' => $this->lastMessageId]);
                
                // Если есть новая клавиатура, заменяем ее
                if ($keyboard) {
                    $this->chat->replaceKeyboard($this->lastMessageId, $keyboard)->send();
                    Log::info('Keyboard replaced');
                }
            } else {
                // Отправляем новое сообщение
                $response = $this->chat->message($message);
                
                if ($keyboard) {
                    $response = $response->keyboard($keyboard);
                }
                
                $result = $response->send();
                
                // Сохраняем ID сообщения
                $this->lastMessageId = $result->telegraphMessageId();
                Log::info('New message sent', ['message_id' => $this->lastMessageId]);
            }
        } catch (\Exception $e) {
            Log::error('Error in replyWithMessage: ' . $e->getMessage());
            
            // Если редактирование не удалось, отправляем новое сообщение
            $this->lastMessageId = null;
            
            $response = $this->chat->message($message);
            
            if ($keyboard) {
                $response = $response->keyboard($keyboard);
            }
            
            $result = $response->send();
            $this->lastMessageId = $result->telegraphMessageId();
        }
    }

    /**
     * Обработчик для всех текстовых сообщений
     */
    protected function handleChatMessage(\Illuminate\Support\Stringable $text): void
    {
        $messageText = $text->toString();
        Log::info('Received chat message: ' . $messageText);

        $userId = $this->message->from()->id();
        $states = TelegramUserState::where('telegram_user_id', $userId)->get();

        if ($states->isNotEmpty()) {
            $state = $states->first();
            $business = $state->business;
            if ($state && $state->step !== 'start') {
                $this->handleTextMessage($messageText, $state, $business);
                return;
            }
        }

        // На неизвестные сообщения просто отвечаем
        $this->replyWithMessage('Привет! Для записи используйте ссылку с сайта бизнеса.');
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
                    $this->replyWithMessage('✅ Аккаунт успешно подключен! Теперь вы будете получать уведомления о записях.');
                } else {
                    $this->replyWithMessage('❌ Бизнес не найден.');
                }
                return;
            }

            // Начало записи
            $slug = $parts[1];
            $business = Business::where('slug', $slug)->first();

            if ($business) {
                $this->startBookingProcess($business);
            } else {
                $this->replyWithMessage('❌ Бизнес не найден. Проверьте slug.');
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
        
        switch ($state->step) {
            case 'enter_client_info':
                $this->handleClientInfo($business, $text, $state);
                break;
            case 'enter_phone':
                $this->handlePhone($business, $text, $state);
                break;
            case 'enter_email':
                $this->handleEmail($business, $text, $state);
                break;
            case 'enter_notes':
                $this->handleNotes($business, $text, $state);
                break;
            default:
                Log::warning('Unknown step in handleTextMessage: ' . $state->step);
                $this->replyWithMessage('❌ Неизвестный шаг. Начните заново.');
        }
    }

    /**
     * Обработка имени клиента
     */
    protected function handleClientInfo(Business $business, string $text, $state)
    {
        Log::info('handleClientInfo', ['text' => $text]);
        
        $data = $state->data;
        $data['client_data']['first_name'] = $text;

        TelegramUserState::updateState($state->telegram_user_id, $business->id, 'enter_phone', $data);
        $this->replyWithMessage('📱 Введите ваш номер телефона:');
    }

    /**
     * Обработка телефона клиента
     */
    protected function handlePhone(Business $business, string $text, $state)
    {
        Log::info('handlePhone', ['text' => $text]);
        
        $data = $state->data;
        $data['client_data']['phone'] = $text;

        TelegramUserState::updateState($state->telegram_user_id, $business->id, 'enter_email', $data);

        $keyboard = Keyboard::make();
        $keyboard->button('Пропустить')->action('skip_email');

        $this->replyWithMessage('📧 Введите ваш email (или нажмите "Пропустить"):', $keyboard);
    }

    /**
     * Обработка email клиента
     */
    protected function handleEmail(Business $business, string $text, $state)
    {
        Log::info('handleEmail', ['text' => $text]);
        
        $data = $state->data;
        $data['client_data']['email'] = $text;

        TelegramUserState::updateState($state->telegram_user_id, $business->id, 'enter_notes', $data);

        $keyboard = Keyboard::make();
        $keyboard->button('Пропустить')->action('skip_notes');

        $this->replyWithMessage('📝 Добавьте примечание к записи (или нажмите "Пропустить"):', $keyboard);
    }

    /**
     * Обработка примечаний клиента
     */
    protected function handleNotes(Business $business, string $text, $state)
    {
        Log::info('handleNotes', ['text' => $text]);
        
        $data = $state->data;
        $data['client_data']['notes'] = $text;

        TelegramUserState::updateState($state->telegram_user_id, $business->id, 'confirm_appointment', $data);
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

        $message = "📋 Подтвердите запись:\n\n" .
            "🏢 Локация: {$location->name}\n" .
            "💇‍♀️ Услуга: {$service->name}\n" .
            "👨‍💼 Мастер: {$master->first_name} {$master->last_name}\n" .
            "📅 Дата: {$date}\n" .
            "🕐 Время: {$time}\n" .
            "👤 Имя: {$data['client_data']['first_name']}\n" .
            "📱 Телефон: {$data['client_data']['phone']}\n";

        if (isset($data['client_data']['email']) && !empty($data['client_data']['email'])) {
            $message .= "📧 Email: {$data['client_data']['email']}\n";
        }

        if (isset($data['client_data']['notes']) && !empty($data['client_data']['notes'])) {
            $message .= "📝 Примечание: {$data['client_data']['notes']}\n";
        }

        $keyboard = Keyboard::make();
        $keyboard->button('✅ Подтвердить')->action('confirm_appointment');
        $keyboard->button('❌ Отмена')->action('cancel');

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
            $this->replyWithMessage('❌ У этого бизнеса нет доступных локаций для записи.');
            return;
        }

        $keyboard = Keyboard::make();

        foreach ($locations as $location) {
            $keyboard->button($location->name)->action("location_{$location->id}");
        }

        $keyboard->button('❌ Отмена')->action('cancel');

        $this->replyWithMessage('🏢 Выберите локацию:', $keyboard);

        // Сохраняем состояние
        TelegramUserState::updateState($userId, $business->id, 'select_location');
    }

    /**
     * Показ выбора услуги
     */
    protected function showServiceSelection(Business $business, $locationId)
    {
        Log::info('showServiceSelection called', ['location_id' => $locationId]);
        
        $location = $business->locations()->find($locationId);
        if (!$location) {
            $this->replyWithMessage('❌ Локация не найдена.');
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

        $keyboard = Keyboard::make();

        foreach ($services as $service) {
            $keyboard->button("{$service->name} ({$service->duration} мин)")->action("service_{$service->id}");
        }

        $keyboard->button('⬅️ Назад')->action('back_to_location');
        $keyboard->button('❌ Отмена')->action('cancel');

        $this->replyWithMessage("💇‍♀️ Выберите услугу для локации \"{$location->name}\":", $keyboard);

        $userId = $this->callbackQuery?->from()->id() ?? $this->message->from()->id();
        TelegramUserState::updateState($userId, $business->id, 'select_service', [
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
            $this->replyWithMessage('❌ Локация или услуга не найдены.');
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

        $keyboard = Keyboard::make();

        foreach ($masters as $master) {
            $keyboard->button($master->first_name . ' ' . $master->last_name)->action("master_{$master->id}");
        }

        $keyboard->button('⬅️ Назад')->action('back_to_service');
        $keyboard->button('❌ Отмена')->action('cancel');

        $this->replyWithMessage("👨‍💼 Выберите мастера для услуги \"{$service->name}\":", $keyboard);

        $userId = $this->callbackQuery?->from()->id() ?? $this->message->from()->id();
        TelegramUserState::updateState($userId, $business->id, 'select_master', [
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
            $this->replyWithMessage('❌ Ошибка: не найден action в callback данных.');
            return;
        }

        // Находим состояние пользователя
        $states = TelegramUserState::where('telegram_user_id', $userId)->get();
        Log::info('User states found:', ['count' => $states->count()]);
        
        if ($states->isEmpty()) {
            Log::warning('No states found for user: ' . $userId);
            $this->replyWithMessage('❌ Сессия не найдена. Начните заново.');
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
                $this->replyWithMessage('❌ Ошибка: не найдена информация о локации.');
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
                $this->replyWithMessage('❌ Ошибка: не найдена информация о локации или услуге.');
            }
        } elseif (str_starts_with($action, 'date_')) {
            $date = str_replace('date_', '', $action);
            Log::info('Date selected:', ['date' => $date]);
            $this->showTimeSlots($business, $date, $state);
        } elseif (str_starts_with($action, 'time_')) {
            $time = str_replace('time_', '', $action);
            Log::info('Time selected:', ['time' => $time]);
            $this->handleTimeSelection($business, $time, $state);
        } elseif ($action === 'skip_email') {
            $data = $state->data;
            TelegramUserState::updateState($userId, $business->id, 'enter_notes', $data);

            $keyboard = Keyboard::make();
            $keyboard->button('Пропустить')->action('skip_notes');

            $this->replyWithMessage('📝 Добавьте примечание к записи (или нажмите "Пропустить"):', $keyboard);
        } elseif ($action === 'skip_notes') {
            $data = $state->data;
            TelegramUserState::updateState($userId, $business->id, 'confirm_appointment', $data);
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
        } elseif ($action === 'cancel') {
            TelegramUserState::clearState($userId, $business->id);
            $this->replyWithMessage('✅ Запись отменена.');
            $this->lastMessageId = null;
        } else {
            Log::warning('Unknown action: ' . $action);
            $this->replyWithMessage('❌ Неизвестная команда. Попробуйте снова.');
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

        $keyboard = Keyboard::make();

        for ($i = 0; $i < 7; $i++) {
            $date = Carbon::today()->addDays($i);
            $dayName = $date->locale('ru')->dayName;
            $formattedDate = $date->format('d.m');
            $keyboard->button("{$dayName} {$formattedDate}")->action("date_{$date->format('Y-m-d')}");
        }

        $keyboard->button('⬅️ Назад')->action('back_to_master');
        $keyboard->button('❌ Отмена')->action('cancel');

        $this->replyWithMessage("📅 Выберите дату для записи к {$master->first_name} {$master->last_name}:", $keyboard);

        $userId = $this->callbackQuery?->from()->id() ?? $this->message->from()->id();
        TelegramUserState::updateState($userId, $business->id, 'select_date', [
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
            $this->replyWithMessage('❌ Отсутствуют данные о выборе.');
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
            $keyboard->button('⬅️ Другая дата')->action('back_to_time');
            $keyboard->button('❌ Отмена')->action('cancel');

            $this->replyWithMessage("❌ На выбранную дату нет свободного времени.", $keyboard);
            return;
        }

        $keyboard = Keyboard::make();

        foreach ($availableSlots as $slot) {
            // Убедимся, что слот в правильном формате
            if (str_contains($slot, ':')) {
                $slot = trim($slot);
                $keyboard->button($slot)->action("time_{$slot}");
            } else {
                $displayTime = $slot . ':00';
                $callbackTime = $slot;
                $keyboard->button($displayTime)->action("time_{$callbackTime}");
            }
        }

        $keyboard->button('⬅️ Другая дата')->action('back_to_time');
        $keyboard->button('❌ Отмена')->action('cancel');

        $formattedDate = Carbon::parse($date)->locale('ru')->format('d.m.Y (l)');
        $this->replyWithMessage("🕐 Выберите время на {$formattedDate}:", $keyboard);

        $userId = $this->callbackQuery?->from()->id() ?? $this->message->from()->id();
        TelegramUserState::updateState($userId, $business->id, 'select_time', [
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
        Log::info('handleTimeSelection called', [
            'time' => $time,
            'time_type' => gettype($time),
            'time_class' => is_object($time) ? get_class($time) : 'not object'
        ]);
        
        if (!$state) {
            $this->replyWithMessage('❌ Сессия истекла. Начните заново.');
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

        TelegramUserState::updateState($state->telegram_user_id, $business->id, 'enter_client_info', $data);
        $this->replyWithMessage('👤 Введите ваше имя:');
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

            TelegramUserState::clearState($state->telegram_user_id, $business->id);
            TelegramNotificationService::sendAppointmentCreated($appointment);

            // Форматируем для сообщения
            $formattedDate = $appointment->date->format('d.m.Y');
            $formattedTime = $appointment->time;
            
            $this->replyWithMessage("✅ Запись успешно создана!\n\n" .
                "📅 Дата: {$formattedDate}\n" .
                "🕐 Время: {$formattedTime}\n" .
                "💇‍♀️ Услуга: {$appointment->service->name}\n" .
                "👨‍💼 Мастер: {$appointment->master->first_name} {$appointment->master->last_name}\n" .
                "🏢 Локация: {$appointment->location->name}\n\n" .
                "Мы свяжемся с вами для подтверждения.");
                
            $this->lastMessageId = null;
        } catch (\Exception $e) {
            Log::error('Error creating appointment: ' . $e->getMessage());
            $this->replyWithMessage('❌ Произошла ошибка при создании записи. Попробуйте еще раз.');
        }
    }
}