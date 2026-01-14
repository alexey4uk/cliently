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

    public function __construct(AppointmentSlotService $slotService)
    {
        $this->slotService = $slotService;
    }

    public function hi()
    {
        $this->chat->message('test')->send();
    }

    // Обработчик для всех текстовых сообщений
    protected function handleChatMessage(\Illuminate\Support\Stringable $text): void
    {
        $messageText = $text->toString();
        Log::info('Received chat message: ' . $messageText);

        // Проверяем, есть ли активная сессия записи
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

        // Если просто прислали текст, попробуем найти бизнес по этому тексту
        if ($messageText && !str_starts_with($messageText, '/')) {
            $business = Business::where('slug', $messageText)->first();
            if ($business) {
                Log::info('Found business by text input: ' . $business->name);
                $this->startBookingProcess($business);
                return;
            }
        }

        // По умолчанию отвечаем на неизвестные сообщения
        $this->chat->message('Привет! Я бот для уведомлений о записях. Для подключения используйте ссылку из настроек или /book <slug> для записи.')->send();
    }

    public function start()
    {
        $text = $this->message->text() ?? '';
        Log::info('Start command received: ' . $text);

        $parts = explode(' ', $text);
        Log::info('Parts: ' . json_encode($parts));

        if (isset($parts[1])) {
            if (str_starts_with($parts[1], 'auth_')) {
                $token = str_replace('auth_', '', $parts[1]);

                $business = Business::where('telegram_token', $token)->first();
                if ($business) {
                    $business->update(['telegram_chat_id' => $this->chat->chat_id]);

                    $this->chat->message('✅ Аккаунт успешно подключен! Теперь вы будете получать уведомления о записях.')->send();
                } else {
                    $this->chat->message('❌ Бизнес не найден.')->send();
                }
                return;
            }

            // Попытка найти бизнес по slug для записи
            $identifier = $parts[1];
            Log::info('Searching for business with identifier: ' . $identifier);

            $business = Business::where('slug', $identifier)
                ->orWhere('telegram_token', $identifier)
                ->first();

            Log::info('Business found: ' . ($business ? $business->name : 'null'));

            if ($business) {
                $this->startBookingProcess($business);
            } else {
                $this->chat->message('❌ Бизнес не найден. Проверьте slug: ' . $identifier . '. Используйте /list для просмотра доступных бизнесов.')->send();
            }
            return;
        } else {
            $this->chat->message('Привет! Я бот для уведомлений о записях. Для подключения используйте ссылку из настроек или /book <slug> для записи.')->send();
        }
    }

    protected function handleTextMessage(string $text, $state, $business)
    {
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
        }
    }

    public function book()
    {
        $text = $this->message->text() ?? '';
        $parts = explode(' ', $text);

        if (!isset($parts[1])) {
            $this->chat->message('Укажите slug бизнеса: /book <slug>')->send();
            return;
        }

        $slug = $parts[1];
        Log::info('Book command with slug: ' . $slug);

        $business = Business::where('slug', $slug)->first();

        Log::info('Business found for book: ' . ($business ? $business->name : 'null'));

        if (!$business) {
            $this->chat->message('❌ Бизнес не найден. Проверьте slug: ' . $slug)->send();
            return;
        }

        $this->startBookingProcess($business);
    }

    public function list()
    {
        $businesses = Business::select('name', 'slug')->get();

        if ($businesses->isEmpty()) {
            $this->chat->message('Нет доступных бизнесов.')->send();
            return;
        }

        $message = "Доступные бизнесы:\n";
        foreach ($businesses as $business) {
            $message .= "- {$business->name} (slug: {$business->slug})\n";
        }

        $this->chat->message($message)->send();
    }

    // Простая команда для тестирования
    public function test()
    {
        $this->chat->message('Бот работает! Используйте /list для просмотра бизнесов или /book <slug> для записи.')->send();
    }



    protected function handleClientInfo(Business $business, string $text, $state)
    {
        $data = $state->data;
        $data['client_data']['first_name'] = $text;

        TelegramUserState::updateState($state->telegram_user_id, $business->id, 'enter_phone', $data);

        $this->chat->message('📱 Введите ваш номер телефона:')->send();
    }

    protected function handlePhone(Business $business, string $text, $state)
    {
        $data = $state->data;
        $data['client_data']['phone'] = $text;

        TelegramUserState::updateState($state->telegram_user_id, $business->id, 'enter_email', $data);

        $keyboard = Keyboard::make();
        $keyboard->button('Пропустить')->param('action', 'skip_email');

        $this->chat->message('📧 Введите ваш email (или нажмите "Пропустить"):')
            ->keyboard($keyboard)
            ->send();
    }

    protected function handleEmail(Business $business, string $text, $state)
    {
        $data = $state->data;
        $data['client_data']['email'] = $text;

        TelegramUserState::updateState($state->telegram_user_id, $business->id, 'enter_notes', $data);

        $keyboard = Keyboard::make();
        $keyboard->button('Пропустить')->param('action', 'skip_notes');

        $this->chat->message('📝 Добавьте примечание к записи (или нажмите "Пропустить"):')
            ->keyboard($keyboard)
            ->send();
    }

    protected function handleNotes(Business $business, string $text, $state)
    {
        $data = $state->data;
        $data['client_data']['notes'] = $text;

        TelegramUserState::updateState($state->telegram_user_id, $business->id, 'confirm_appointment', $data);

        $this->showAppointmentConfirmation($business, $data);
    }

    protected function showAppointmentConfirmation(Business $business, array $data)
    {
        $location = $business->locations()->find($data['location_id']);
        $service = $business->services()->find($data['service_id']);
        $master = $business->masters()->find($data['master_id']);

        $message = "📋 Подтвердите запись:\n\n" .
            "🏢 Локация: {$location->name}\n" .
            "💇‍♀️ Услуга: {$service->name}\n" .
            "👨‍💼 Мастер: {$master->first_name} {$master->last_name}\n" .
            "📅 Дата: " . Carbon::parse($data['date'])->format('d.m.Y') . "\n" .
            "🕐 Время: {$data['time']}\n" .
            "👤 Имя: {$data['client_data']['first_name']}\n" .
            "📱 Телефон: {$data['client_data']['phone']}\n";

        if (isset($data['client_data']['email'])) {
            $message .= "📧 Email: {$data['client_data']['email']}\n";
        }

        if (isset($data['client_data']['notes'])) {
            $message .= "📝 Примечание: {$data['client_data']['notes']}\n";
        }

        $keyboard = Keyboard::make();
        $keyboard->button('✅ Подтвердить')->param('action', 'confirm_appointment');
        $keyboard->button('❌ Отмена')->param('action', 'cancel');

        $this->chat->message($message)->keyboard($keyboard)->send();
    }

    protected function startBookingProcess(Business $business)
    {
        $userId = $this->message->from()->id();

        // Очищаем предыдущее состояние
        TelegramUserState::clearState($userId, $business->id);

        // Начинаем с выбора локации
        $this->showLocationSelection($business, $userId);
    }

    protected function showLocationSelection(Business $business, $userId = null)
    {
        $userId = $userId ?? $this->callbackQuery?->from()->id() ?? $this->message->from()->id();

        $locations = $business->locations()->orderBy('name')->get();

        if ($locations->isEmpty()) {
            $this->chat->message('❌ У этого бизнеса нет доступных локаций для записи.')->send();
            return;
        }

        $keyboard = Keyboard::make();

        foreach ($locations as $location) {
            $keyboard->button($location->name)->param('action', "location_{$location->id}");
        }

        $keyboard->button('❌ Отмена')->param('action', 'cancel');

        $this->chat->message('🏢 Выберите локацию:')
            ->keyboard($keyboard)
            ->send();

        // Сохраняем состояние
        TelegramUserState::updateState($userId, $business->id, 'select_location');
    }

    protected function showServiceSelection(Business $business, $locationId)
    {
        $location = $business->locations()->find($locationId);
        if (!$location) {
            $this->chat->message('❌ Локация не найдена.')->send();
            return;
        }

        // Получаем услуги, привязанные к локации
        $services = $location->services()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        // Если нет услуг, привязанных к локации, показываем все услуги бизнеса
        if ($services->isEmpty()) {
            $services = $business->services()
                ->where('is_active', true)
                ->orderBy('name')
                ->get();
        }

        if ($services->isEmpty()) {
            $this->chat->message('❌ Нет доступных услуг.')->send();
            return;
        }

        $keyboard = Keyboard::make();

        foreach ($services as $service) {
            $keyboard->button("{$service->name} ({$service->duration} мин)")->param('action', "service_{$service->id}");
        }

        $keyboard->button('⬅️ Назад')->param('action', 'back_to_location');
        $keyboard->button('❌ Отмена')->param('action', 'cancel');

        $this->chat->message("💇‍♀️ Выберите услугу для локации \"{$location->name}\":")
            ->keyboard($keyboard)
            ->send();

        // Обновляем состояние
        $userId = $this->callbackQuery?->from()->id() ?? $this->message->from()->id();
        TelegramUserState::updateState($userId, $business->id, 'select_service', [
            'location_id' => $locationId,
        ]);
    }

    protected function showMasterSelection(Business $business, $locationId, $serviceId)
    {
        $location = $business->locations()->find($locationId);
        $service = $business->services()->find($serviceId);

        if (!$location || !$service) {
            $this->chat->message('❌ Локация или услуга не найдены.')->send();
            return;
        }

        // Получаем мастеров для выбранной локации и услуги
        $masters = $location->masters()
            ->where('is_active', true)
            ->whereHas('services', function ($q) use ($serviceId) {
                $q->where('services.id', $serviceId);
            })
            ->orderBy('first_name')
            ->get();

        // Если нет мастеров с услугой в локации, показываем всех мастеров локации
        if ($masters->isEmpty()) {
            $masters = $location->masters()
                ->where('is_active', true)
                ->orderBy('first_name')
                ->get();
        }

        // Если все еще нет мастеров, показываем всех мастеров бизнеса, которые предоставляют услугу
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
            $this->chat->message('❌ Нет доступных мастеров.')->send();
            return;
        }

        $keyboard = Keyboard::make();

        foreach ($masters as $master) {
            $keyboard->button($master->first_name . ' ' . $master->last_name)->param('action', "master_{$master->id}");
        }

        $keyboard->button('⬅️ Назад')->param('action', 'back_to_service');
        $keyboard->button('❌ Отмена')->param('action', 'cancel');

        $this->chat->message("👨‍💼 Выберите мастера для услуги \"{$service->name}\":")
            ->keyboard($keyboard)
            ->send();

        // Обновляем состояние
        $userId = $this->callbackQuery?->from()->id() ?? $this->message->from()->id();
        TelegramUserState::updateState($userId, $business->id, 'select_master', [
            'location_id' => $locationId,
            'service_id' => $serviceId,
        ]);
    }

    // Обработка callback запросов
    protected function handleCallbackQuery(): void
    {
        $callbackData = $this->callbackQuery->data();
        $userId = $this->callbackQuery->from()->id();

        Log::info('Callback received: ' . $callbackData . ' from user: ' . $userId);

        // Парсим callbackData - может быть JSON или action:value
        $actions = [];
        if (str_starts_with($callbackData, '{') && str_ends_with($callbackData, '}')) {
            // JSON формат
            $parsed = json_decode($callbackData, true);
            Log::info('JSON parsed: ' . json_encode($parsed));
            if ($parsed && isset($parsed['action'])) {
                $action = $parsed['action'];
                $actions = $parsed;
            } else {
                $action = $callbackData;
            }
        } else {
            // Формат action:value;param:value
            $parts = explode(';', $callbackData);
            foreach ($parts as $part) {
                if (str_contains($part, ':')) {
                    [$key, $value] = explode(':', $part, 2);
                    $actions[$key] = $value;
                }
            }
            $action = $actions['action'] ?? $callbackData;
        }

        Log::info('Parsed action: ' . $action);

        // Находим состояние пользователя (любой бизнес)
        $states = TelegramUserState::where('telegram_user_id', $userId)->get();
        if ($states->isEmpty()) {
            $this->chat->message('❌ Сессия не найдена. Начните заново.')->send();
            return;
        }

        // Берем первое состояние (предполагаем, что пользователь работает только с одним бизнесом одновременно)
        $state = $states->first();
        $business = $state->business;

        if (str_starts_with($action, 'location_')) {
            $locationId = str_replace('location_', '', $action);
            $this->showServiceSelection($business, $locationId);
        } elseif (str_starts_with($action, 'service_')) {
            $serviceId = str_replace('service_', '', $action);
            $locationId = $state?->data['location_id'] ?? null;
            if ($locationId) {
                $this->showMasterSelection($business, $locationId, $serviceId);
            }
        } elseif (str_starts_with($action, 'master_')) {
            $masterId = str_replace('master_', '', $action);
            $locationId = $state?->data['location_id'] ?? null;
            $serviceId = $state?->data['service_id'] ?? null;
            if ($locationId && $serviceId) {
                $this->showTimeSelection($business, $locationId, $serviceId, $masterId);
            }
        } elseif (str_starts_with($action, 'date_')) {
            $date = str_replace('date_', '', $action);
            $this->showTimeSlots($business, $date, $state);
        } elseif (str_starts_with($action, 'time_')) {
            $time = str_replace('time_', '', $action);
            $this->handleTimeSelection($business, $time, $state);
        } elseif ($action === 'skip_email') {
            $data = $state->data;
            TelegramUserState::updateState($userId, $business->id, 'enter_notes', $data);

            $keyboard = Keyboard::make();
            $keyboard->button('Пропустить')->param('action', 'skip_notes');

            $this->chat->message('📝 Добавьте примечание к записи (или нажмите "Пропустить"):')
                ->keyboard($keyboard)
                ->send();
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
            $this->chat->message('✅ Запись отменена.')->send();
        } else {
            Log::warning('Unknown action: ' . $action);
        }
    }

    protected function showTimeSelection(Business $business, $locationId, $serviceId, $masterId)
    {
        $location = $business->locations()->find($locationId);
        $service = $business->services()->find($serviceId);
        $master = $business->masters()->find($masterId);

        if (!$location || !$service || !$master) {
            $this->chat->message('❌ Данные не найдены.')->send();
            return;
        }

        // Проверяем, что мастер работает в выбранной локации
        if (!$master->locations()->where('locations.id', $locationId)->exists()) {
            $this->chat->message('❌ Мастер не работает в выбранной локации.')->send();
            return;
        }

        // Проверяем, что мастер предоставляет услугу
        if (!$master->services()->where('services.id', $serviceId)->exists()) {
            $this->chat->message('❌ Мастер не предоставляет эту услугу.')->send();
            return;
        }

        // Показываем календарь для выбора даты
        $keyboard = Keyboard::make();

        // Показываем следующие 7 дней
        for ($i = 0; $i < 7; $i++) {
            $date = Carbon::today()->addDays($i);
            $dayName = $date->locale('ru')->dayName;
            $formattedDate = $date->format('d.m');
            $keyboard->button("{$dayName} {$formattedDate}")->param('action', "date_{$date->format('Y-m-d')}");
        }

        $keyboard->button('⬅️ Назад')->param('action', 'back_to_master');
        $keyboard->button('❌ Отмена')->param('action', 'cancel');

        $this->chat->message("📅 Выберите дату для записи к {$master->first_name} {$master->last_name}:")
            ->keyboard($keyboard)
            ->send();

        // Обновляем состояние
        $userId = $this->callbackQuery?->from()->id() ?? $this->message->from()->id();
        TelegramUserState::updateState($userId, $business->id, 'select_date', [
            'location_id' => $locationId,
            'service_id' => $serviceId,
            'master_id' => $masterId,
        ]);
    }

    protected function showTimeSlots(Business $business, $date, $state)
    {
        $locationId = $state?->data['location_id'] ?? null;
        $serviceId = $state?->data['service_id'] ?? null;
        $masterId = $state?->data['master_id'] ?? null;

        if (!$locationId || !$serviceId || !$masterId) {
            $this->chat->message('❌ Отсутствуют данные о выборе.')->send();
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

        if (empty($availableSlots)) {
            $keyboard = Keyboard::make();
            $keyboard->button('⬅️ Другая дата')->param('action', 'back_to_time');
            $keyboard->button('❌ Отмена')->param('action', 'cancel');

            $this->chat->message("❌ На выбранную дату нет свободного времени.")
                ->keyboard($keyboard)
                ->send();
            return;
        }

        $keyboard = Keyboard::make();

        foreach ($availableSlots as $slot) {
            $keyboard->button($slot)->param('action', "time_{$slot}");
        }

        $keyboard->button('⬅️ Другая дата')->param('action', 'back_to_time');
        $keyboard->button('❌ Отмена')->param('action', 'cancel');

        $formattedDate = Carbon::parse($date)->locale('ru')->format('d.m.Y (l)');
        $this->chat->message("🕐 Выберите время на {$formattedDate}:")
            ->keyboard($keyboard)
            ->send();

        // Обновляем состояние
        $userId = $this->callbackQuery?->from()->id() ?? $this->message->from()->id();
        TelegramUserState::updateState($userId, $business->id, 'select_time', [
            'location_id' => $locationId,
            'service_id' => $serviceId,
            'master_id' => $masterId,
            'date' => $date,
        ]);
    }

    protected function handleTimeSelection(Business $business, $time, $state)
    {
        if (!$state) {
            $this->chat->message('❌ Сессия истекла. Начните заново.')->send();
            return;
        }

        // Сохраняем выбранное время
        $data = $state->data;
        $data['time'] = $time;

        TelegramUserState::updateState($state->telegram_user_id, $business->id, 'enter_client_info', $data);

        // Запрашиваем данные клиента
        $this->chat->message('👤 Введите ваше имя:')->send();
    }

    protected function createAppointment(Business $business, $state)
    {
        if (!$state || !isset($state->data['client_data'])) {
            $this->chat->message('❌ Данные не найдены.')->send();
            return;
        }

        $data = $state->data;

        // Находим или создаем клиента
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

        // Создаем запись
        $appointment = Appointment::create([
            'business_id' => $business->id,
            'client_id' => $client->id,
            'service_id' => $data['service_id'],
            'master_id' => $data['master_id'],
            'location_id' => $data['location_id'],
            'date' => $data['date'],
            'time' => $data['time'],
            'status' => 'pending',
            'notes' => $data['client_data']['notes'] ?? null,
        ]);

        // Очищаем состояние
        TelegramUserState::clearState($state->telegram_user_id, $business->id);

        // Отправляем уведомление
        TelegramNotificationService::sendAppointmentCreated($appointment);

        $this->chat->message("✅ Запись успешно создана!\n\n" .
            "📅 Дата: {$appointment->date->format('d.m.Y')}\n" .
            "🕐 Время: {$appointment->time}\n" .
            "💇‍♀️ Услуга: {$appointment->service->name}\n" .
            "👨‍💼 Мастер: {$appointment->master->first_name} {$appointment->master->last_name}\n" .
            "🏢 Локация: {$appointment->location->name}\n\n" .
            "Мы свяжемся с вами для подтверждения.")->send();
    }
}
