<?php

namespace App\Telegram;

use DefStudio\Telegraph\Handlers\WebhookHandler as BaseWebhookHandler;
use DefStudio\Telegraph\Keyboard\Keyboard;
use Illuminate\Support\Facades\Log;
use DefStudio\Telegraph\Models\TelegraphBot;
use DefStudio\Telegraph\Models\TelegraphChat;
use DefStudio\Telegraph\TelegraphCallbackQuery;
use Illuminate\Support\Stringable;

class WebhookHandler extends BaseWebhookHandler
{
    protected function handleChatMessage(Stringable $text): void
    {
        if ($text->startsWith('/start')) {
            $this->handleStartCommand($text);
        } else {
            // Обработка других сообщений
            $this->chat->message('Неизвестная команда. Используйте /start для начала.')->send();
        }
    }

    protected function handleStartCommand(Stringable $text): void
    {
        Log::info('Telegram start command received: ' . $text);

        $payload = $text->after('/start ')->trim();

        if ($payload->startsWith('auth_')) {
            $this->handleAuth($payload->after('auth_'));
        } elseif ($payload->startsWith('book_')) {
            $this->handleBook($payload->after('book_'));
        } else {
            $this->chat->message('Добро пожаловать! Используйте ссылки для аутентификации или записи.')->send();
        }
    }

    protected function handleAuth(string $businessId): void
    {
        // Найти бизнес
        $business = \App\Models\Business::find($businessId);

        if (!$business) {
            $this->chat->message('Бизнес не найден.')->send();
            return;
        }

        // Проверить, аутентифицирован ли уже
        if ($business->telegram_chat_id) {
            $this->chat->message('Этот бизнес уже аутентифицирован в Telegram.')->send();
            return;
        }

        // Сохранить chat_id в бизнес
        $business->update(['telegram_chat_id' => $this->chat->chat_id]);

        $this->chat->message("✅ Уведомления подключены!\n\nВы будете получать оповещения о новых записях для бизнеса '{$business->name}'.")->send();
    }

    protected function handleBook(string $businessId): void
    {
        // Найти бизнес
        $business = \App\Models\Business::find($businessId);

        if (!$business) {
            $this->chat->message('Бизнес не найден.')->send();
            return;
        }

        // Начать процесс записи
        $this->chat->message("🗓 Запись к {$business->name}\n\nВыберите локацию:")->send();

        // Получить локации
        $locations = $business->locations;

        if ($locations->isEmpty()) {
            $this->chat->message('Нет доступных локаций.')->send();
            return;
        }

        $keyboard = Keyboard::make();

        foreach ($locations as $location) {
            $keyboard->button($location->name)->action('select_location_' . $location->id);
        }

        $this->chat->message("Выберите локацию:")->keyboard($keyboard)->send();
    }

    protected function handleCallbackQuery(): void
    {
        $action = $this->data->get('action');

        if (str_starts_with($action, 'select_location_')) {
            $locationId = str_replace('select_location_', '', $action);
            $this->handleSelectLocation($locationId);
        } elseif (str_starts_with($action, 'select_service_')) {
            $serviceId = str_replace('select_service_', '', $action);
            $this->handleSelectService($serviceId);
        } elseif (str_starts_with($action, 'select_master_')) {
            $masterId = str_replace('select_master_', '', $action);
            $this->handleSelectMaster($masterId);
        } elseif (str_starts_with($action, 'select_time_')) {
            $timeSlot = str_replace('select_time_', '', $action);
            $this->handleSelectTime($timeSlot);
        }
    }

    protected function handleSelectLocation(string $locationId): void
    {
        $location = \App\Models\Location::find($locationId);

        if (!$location) {
            $this->chat->message('Локация не найдена.')->send();
            return;
        }

        // Сохранить в storage выбранную локацию
        $this->chat->storage()->set('booking.location_id', $locationId);

        $this->chat->message("📍 Выбрана локация: {$location->name}\n\nВыберите услугу:")->send();

        $services = $location->services;

        if ($services->isEmpty()) {
            $this->chat->message('Нет доступных услуг для этой локации.')->send();
            return;
        }

        $keyboard = Keyboard::make();

        foreach ($services as $service) {
            $keyboard->button($service->name)->action('select_service_' . $service->id);
        }

        $this->chat->message("Выберите услугу:")->keyboard($keyboard)->send();
    }

    protected function handleSelectService(string $serviceId): void
    {
        $service = \App\Models\Service::find($serviceId);

        if (!$service) {
            $this->chat->message('Услуга не найдена.')->send();
            return;
        }

        $this->chat->storage()->set('booking.service_id', $serviceId);

        $this->chat->message("💇‍♀️ Выбрана услуга: {$service->name}\n\nВыберите мастера:")->send();

        $masters = $service->masters;

        if ($masters->isEmpty()) {
            $this->chat->message('Нет доступных мастеров для этой услуги.')->send();
            return;
        }

        $keyboard = Keyboard::make();

        foreach ($masters as $master) {
            $keyboard->button($master->name)->action('select_master_' . $master->id);
        }

        $this->chat->message("Выберите мастера:")->keyboard($keyboard)->send();
    }

    protected function handleSelectMaster(string $masterId): void
    {
        $master = \App\Models\Master::find($masterId);

        if (!$master) {
            $this->chat->message('Мастер не найден.')->send();
            return;
        }

        $this->chat->storage()->set('booking.master_id', $masterId);

        $this->chat->message("👨‍💼 Выбран мастер: {$master->name}\n\nВыберите время:")->send();

        // Получить доступные слоты
        $locationId = $this->chat->storage()->get('booking.location_id');
        $serviceId = $this->chat->storage()->get('booking.service_id');

        $availableSlots = app(\App\Services\AppointmentSlotService::class)->getAvailableSlots(
            $locationId,
            $serviceId,
            $masterId,
            now()->toDateString()
        );

        if (empty($availableSlots)) {
            $this->chat->message('Нет доступных слотов на сегодня.')->send();
            return;
        }

        $keyboard = Keyboard::make();

        foreach ($availableSlots as $slot) {
            $keyboard->button($slot)->action('select_time_' . $slot);
        }

        $this->chat->message("Выберите время:")->keyboard($keyboard)->send();
    }

    protected function handleSelectTime(string $timeSlot): void
    {
        $this->chat->storage()->set('booking.time', $timeSlot);

        // Получить все данные для создания записи
        $locationId = $this->chat->storage()->get('booking.location_id');
        $serviceId = $this->chat->storage()->get('booking.service_id');
        $masterId = $this->chat->storage()->get('booking.master_id');

        $location = \App\Models\Location::find($locationId);
        $service = \App\Models\Service::find($serviceId);
        $master = \App\Models\Master::find($masterId);

        // Создать запись
        $appointment = \App\Models\Appointment::create([
            'business_id' => $location->business_id,
            'location_id' => $locationId,
            'service_id' => $serviceId,
            'master_id' => $masterId,
            'client_name' => 'Клиент из Telegram', // TODO: запросить имя
            'client_phone' => '', // TODO: запросить телефон
            'appointment_date' => now()->toDateString(),
            'appointment_time' => $timeSlot,
            'status' => 'pending',
            'token' => \Illuminate\Support\Str::random(32),
        ]);

        // Очистить storage
        $this->chat->storage()->forget('booking');

        $this->chat->message("✅ Запись создана!\n\n📅 Дата: " . now()->format('d.m.Y') . "\n🕒 Время: {$timeSlot}\n📍 Локация: {$location->name}\n💇‍♀️ Услуга: {$service->name}\n👨‍💼 Мастер: {$master->name}\n\nТокен подтверждения: {$appointment->token}")->send();

        // Отправить уведомление мастеру, если подключен
        if ($location->business->telegram_chat_id) {
            $bot = TelegraphBot::find(1); // Предполагаем, что бот один
            $chat = TelegraphChat::where('chat_id', $location->business->telegram_chat_id)->first();

            if ($chat) {
                $chat->message("🔔 Новая запись!\n\n📅 Дата: " . now()->format('d.m.Y') . "\n🕒 Время: {$timeSlot}\n👤 Клиент: {$appointment->client_name}\n💇‍♀️ Услуга: {$service->name}")->send();
            }
        }
    }
}
