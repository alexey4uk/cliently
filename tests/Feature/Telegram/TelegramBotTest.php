<?php

namespace Tests\Feature\Telegram;

use App\Models\Business;
use App\Models\Location;
use App\Models\Master;
use App\Models\Service;
use App\Models\TelegramUserState;
use App\Telegram\Handler;
use App\Telegram\TelegramKeyboards;
use App\Telegram\TelegramValidators;
use Carbon\Carbon;
use DefStudio\Telegraph\Keyboard\Keyboard;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class TelegramBotTest extends TestCase
{
    use RefreshDatabase;

    protected $telegramUserId = '123456789';

    protected $chatId = '987654321';

    protected function setUp(): void
    {
        parent::setUp();

        // Мокаем Telegram API
        $this->mockTelegramApi();
    }

    protected function mockTelegramApi()
    {
        // Мокаем chat для предотвращения реальных вызовов Telegram API
        $chatMock = Mockery::mock(\DefStudio\Telegraph\Models\TelegraphChat::class);
        $chatMock->shouldReceive('chat_id')->andReturn($this->chatId);
        $chatMock->shouldReceive('getAttribute')->with('chat_id')->andReturn($this->chatId);

        // Мокаем метод message() который возвращает Telegraph
        $telegraphMock = Mockery::mock(\DefStudio\Telegraph\Telegraph::class);
        $telegraphMock->shouldReceive('message')->andReturnSelf();
        $telegraphMock->shouldReceive('keyboard')->andReturnSelf();

        // Мокаем response для send()
        $responseMock = Mockery::mock(\DefStudio\Telegraph\Client\TelegraphResponse::class);
        $responseMock->shouldReceive('telegraphMessageId')->andReturn(123);

        $telegraphMock->shouldReceive('send')->andReturn($responseMock);
        $telegraphMock->shouldReceive('edit')->andReturnSelf();
        $telegraphMock->shouldReceive('replaceKeyboard')->andReturnSelf();
        $telegraphMock->shouldReceive('deleteMessage')->andReturnSelf();

        $chatMock->shouldReceive('message')->andReturn($telegraphMock);

        // Создаем handler с моками
        $this->handler = new Handler(
            app(\App\Services\AppointmentSlotService::class),
            app(\App\Services\TelegramBotService::class),
            app(\App\Services\Appointment\AppointmentService::class)
        );

        // Устанавливаем моки через reflection
        $reflection = new \ReflectionClass($this->handler);

        $chatProperty = $reflection->getProperty('chat');
        $chatProperty->setAccessible(true);
        $chatProperty->setValue($this->handler, $chatMock);
    }

    protected function setMessage($text = '/start')
    {
        // Создаем реальный DTO объект для message
        $messageData = [
            'message_id' => 123,
            'from' => [
                'id' => $this->telegramUserId,
                'first_name' => 'Test',
                'last_name' => 'User',
                'username' => 'testuser',
            ],
            'chat' => [
                'id' => $this->chatId,
                'type' => 'private',
            ],
            'date' => time(),
            'text' => $text,
        ];

        $message = \DefStudio\Telegraph\DTO\Message::fromArray($messageData);

        $reflection = new \ReflectionClass($this->handler);
        $messageProperty = $reflection->getProperty('message');
        $messageProperty->setAccessible(true);
        $messageProperty->setValue($this->handler, $message);
    }

    protected function setCallbackQuery($action = 'test')
    {
        // Создаем реальный DTO объект для callbackQuery
        $callbackData = [
            'id' => '123',
            'from' => [
                'id' => $this->telegramUserId,
                'first_name' => 'Test',
                'last_name' => 'User',
                'username' => 'testuser',
            ],
            'message' => [
                'message_id' => 123,
                'from' => [
                    'id' => 123456789,
                    'first_name' => 'Bot',
                    'username' => 'testbot',
                ],
                'chat' => [
                    'id' => $this->chatId,
                    'type' => 'private',
                ],
                'date' => time(),
                'text' => 'Test message',
            ],
            'chat_instance' => '123',
            'data' => json_encode(['action' => $action]),
        ];

        $callbackQuery = \DefStudio\Telegraph\DTO\CallbackQuery::fromArray($callbackData);

        $reflection = new \ReflectionClass($this->handler);
        $callbackQueryProperty = $reflection->getProperty('callbackQuery');
        $callbackQueryProperty->setAccessible(true);
        $callbackQueryProperty->setValue($this->handler, $callbackQuery);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_start_command_without_parameters()
    {
        // Arrange
        $this->setMessage('/start');

        // Act
        $this->handler->start();

        // Assert
        $this->assertDatabaseCount('telegram_user_states', 0);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_start_command_with_valid_slug()
    {
        // Arrange (online_booking_enabled нужен, иначе Handler выходит до создания state)
        $business = Business::factory()->create([
            'slug' => 'test-business',
            'online_booking_enabled' => true,
        ]);
        Location::factory()->create(['business_id' => $business->id]);
        $this->setMessage('/start test-business');

        // Act
        $this->handler->start();

        // Assert
        $this->assertDatabaseCount('telegram_user_states', 1);
        $state = TelegramUserState::first();
        $this->assertEquals(TelegramUserState::STEP_SELECT_LOCATION, $state->step);
        $this->assertEquals($business->id, $state->business_id);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_start_command_with_invalid_slug()
    {
        // Arrange
        $this->setMessage('/start invalid-slug');

        // Act
        $this->handler->start();

        // Assert
        $this->assertDatabaseCount('telegram_user_states', 0);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_start_command_with_auth_token()
    {
        // Arrange
        $token = 'test-token-123';
        $business = Business::factory()->create(['telegram_token' => $token]);
        $this->setMessage('/start auth_'.$token);

        // Act
        $this->handler->start();

        // Assert
        $this->assertDatabaseCount('telegram_user_states', 0);
        $business->refresh();
        $this->assertEquals($this->chatId, $business->telegram_chat_id);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_start_command_with_invalid_auth_token()
    {
        // Arrange
        $this->setMessage('/start auth_invalid-token');

        // Act
        $this->handler->start();

        // Assert
        $this->assertDatabaseCount('telegram_user_states', 0);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_search_command()
    {
        // Arrange
        $this->setMessage('/search');

        // Act
        $this->handler->search();

        // Assert
        $this->assertDatabaseCount('telegram_user_states', 1);
        $state = TelegramUserState::first();
        $this->assertEquals(TelegramUserState::STEP_SEARCH, $state->step);
        $this->assertNull($state->business_id);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_list_command()
    {
        // Arrange
        Business::factory()->count(3)->create();
        $this->setMessage('/list');

        // Act
        $this->handler->list();

        // Assert
        $this->assertDatabaseCount('telegram_user_states', 0);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_handle_chat_message_with_search_state()
    {
        // Arrange
        $state = TelegramUserState::factory()
            ->search()
            ->forUser($this->telegramUserId)
            ->create();

        $this->setMessage('test-business');

        // Act
        $this->handler->handleChatMessage(new \Illuminate\Support\Stringable('test-business'));

        // Assert
        $state->refresh();
        $this->assertEquals('test-business', $state->data['search_query']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_handle_chat_message_with_booking_state()
    {
        // Arrange
        $business = Business::factory()->create();
        $state = TelegramUserState::factory()
            ->booking($business, TelegramUserState::STEP_ENTER_CLIENT_INFO, [])
            ->forUser($this->telegramUserId)
            ->create();

        $this->setMessage('John Doe');

        // Act
        $this->handler->handleChatMessage(new \Illuminate\Support\Stringable('John Doe'));

        // Assert
        $state->refresh();
        $this->assertEquals(TelegramUserState::STEP_ENTER_PHONE, $state->step);
        $this->assertEquals('John Doe', $state->data['client_data']['first_name']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_handle_chat_message_with_cancel()
    {
        // Arrange
        $business = Business::factory()->create();
        $state = TelegramUserState::factory()
            ->booking($business, TelegramUserState::STEP_ENTER_CLIENT_INFO, [])
            ->forUser($this->telegramUserId)
            ->create();

        $this->setMessage('отмена');

        // Act
        $this->handler->handleChatMessage(new \Illuminate\Support\Stringable('отмена'));

        // Assert
        $this->assertDatabaseCount('telegram_user_states', 0);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_handle_chat_message_without_state()
    {
        // Arrange
        $this->setMessage('test message');

        // Act
        $this->handler->handleChatMessage(new \Illuminate\Support\Stringable('test message'));

        // Assert
        $this->assertDatabaseCount('telegram_user_states', 0);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_validate_name_valid()
    {
        // Act
        [$isValid, $result] = TelegramValidators::validateName('John Doe');

        // Assert
        $this->assertTrue($isValid);
        $this->assertEquals('John Doe', $result);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_validate_name_empty()
    {
        // Act
        [$isValid, $result] = TelegramValidators::validateName('');

        // Assert
        $this->assertFalse($isValid);
        $this->assertStringContainsString('Имя не может быть пустым', $result);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_validate_name_too_short()
    {
        // Act
        [$isValid, $result] = TelegramValidators::validateName('J');

        // Assert
        $this->assertFalse($isValid);
        $this->assertStringContainsString('Имя слишком короткое', $result);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_validate_name_with_numbers()
    {
        // Act
        [$isValid, $result] = TelegramValidators::validateName('John123');

        // Assert
        $this->assertFalse($isValid);
        $this->assertStringContainsString('не должно содержать цифр', $result);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_validate_phone_valid_plus7()
    {
        // Act
        [$isValid, $result] = TelegramValidators::validatePhone('+79001234567');

        // Assert
        $this->assertTrue($isValid);
        $this->assertEquals('+79001234567', $result);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_validate_phone_valid_8()
    {
        // Act
        [$isValid, $result] = TelegramValidators::validatePhone('89001234567');

        // Assert
        $this->assertTrue($isValid);
        $this->assertEquals('+79001234567', $result);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_validate_phone_empty()
    {
        // Act
        [$isValid, $result] = TelegramValidators::validatePhone('');

        // Assert
        $this->assertFalse($isValid);
        $this->assertStringContainsString('Неверный формат', $result);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_validate_phone_invalid_format()
    {
        // Act
        [$isValid, $result] = TelegramValidators::validatePhone('123');

        // Assert
        $this->assertFalse($isValid);
        $this->assertStringContainsString('Неверный формат', $result);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_validate_notes_valid()
    {
        // Act
        [$isValid, $result] = TelegramValidators::validateNotes('Test notes');

        // Assert
        $this->assertTrue($isValid);
        $this->assertEquals('Test notes', $result);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_validate_notes_too_long()
    {
        // Act
        [$isValid, $result] = TelegramValidators::validateNotes(str_repeat('a', 201));

        // Assert
        $this->assertFalse($isValid);
        $this->assertStringContainsString('Слишком длинно', $result);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_should_skip_notes()
    {
        // Assert
        $this->assertTrue(TelegramValidators::shouldSkipNotes('нет'));
        $this->assertTrue(TelegramValidators::shouldSkipNotes('пропустить'));
        $this->assertTrue(TelegramValidators::shouldSkipNotes('skip'));
        $this->assertTrue(TelegramValidators::shouldSkipNotes(''));
        $this->assertFalse(TelegramValidators::shouldSkipNotes('Test notes'));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_telegram_user_state_get_current_state_with_business()
    {
        // Arrange
        $business = Business::factory()->create();
        $state = TelegramUserState::factory()
            ->booking($business, TelegramUserState::STEP_SELECT_LOCATION, [])
            ->forUser($this->telegramUserId)
            ->create();

        // Act
        $result = TelegramUserState::getCurrentState($this->telegramUserId);

        // Assert
        $this->assertNotNull($result);
        $this->assertEquals($business->id, $result->business_id);
        $this->assertEquals(TelegramUserState::STEP_SELECT_LOCATION, $result->step);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_telegram_user_state_get_current_state_without_business()
    {
        // Arrange
        $state = TelegramUserState::factory()
            ->search()
            ->forUser($this->telegramUserId)
            ->create();

        // Act
        $result = TelegramUserState::getCurrentState($this->telegramUserId);

        // Assert
        $this->assertNotNull($result);
        $this->assertNull($result->business_id);
        $this->assertEquals(TelegramUserState::STEP_SEARCH, $result->step);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_telegram_user_state_get_current_state_with_multiple_states()
    {
        // Arrange
        $business = Business::factory()->create();

        // Создаем состояние поиска
        TelegramUserState::factory()
            ->search()
            ->forUser($this->telegramUserId)
            ->create();

        // Создаем состояние записи
        TelegramUserState::factory()
            ->booking($business, TelegramUserState::STEP_SELECT_LOCATION, [])
            ->forUser($this->telegramUserId)
            ->create();

        // Act
        $result = TelegramUserState::getCurrentState($this->telegramUserId);

        // Assert
        $this->assertNotNull($result);
        $this->assertEquals($business->id, $result->business_id);
        $this->assertEquals(TelegramUserState::STEP_SELECT_LOCATION, $result->step);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_telegram_user_state_get_current_state_no_state()
    {
        // Act
        $result = TelegramUserState::getCurrentState($this->telegramUserId);

        // Assert
        $this->assertNull($result);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_telegram_messages_format()
    {
        // Arrange
        $template = 'Привет, {name}! Ваш код: {code}';

        // Act
        $result = \App\Telegram\TelegramMessages::format($template, [
            'name' => 'John',
            'code' => '12345',
        ]);

        // Assert
        $this->assertEquals('Привет, John! Ваш код: 12345', $result);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_telegram_keyboards_cancel_only()
    {
        // Act
        $keyboard = TelegramKeyboards::cancelOnly();

        // Assert
        $this->assertInstanceOf(Keyboard::class, $keyboard);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_telegram_keyboards_restart_and_cancel()
    {
        // Act
        $keyboard = TelegramKeyboards::restartAndCancel();

        // Assert
        $this->assertInstanceOf(Keyboard::class, $keyboard);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_telegram_keyboards_skip_and_cancel()
    {
        // Act
        $keyboard = TelegramKeyboards::skipAndCancel();

        // Assert
        $this->assertInstanceOf(Keyboard::class, $keyboard);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_telegram_keyboards_locations()
    {
        // Arrange
        $business = Business::factory()->create();
        $locations = Location::factory()->count(3)->create(['business_id' => $business->id]);

        // Act
        $keyboard = TelegramKeyboards::locations($locations);

        // Assert
        $this->assertInstanceOf(Keyboard::class, $keyboard);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_telegram_keyboards_services()
    {
        // Arrange
        $business = Business::factory()->create();
        $services = Service::factory()->count(3)->create(['business_id' => $business->id]);

        // Act
        $keyboard = TelegramKeyboards::services($services);

        // Assert
        $this->assertInstanceOf(Keyboard::class, $keyboard);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_telegram_keyboards_masters()
    {
        // Arrange
        $business = Business::factory()->create();
        $masters = Master::factory()->count(3)->create(['business_id' => $business->id]);

        // Act
        $keyboard = TelegramKeyboards::masters($masters);

        // Assert
        $this->assertInstanceOf(Keyboard::class, $keyboard);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_telegram_keyboards_times()
    {
        // Arrange
        $times = ['10:00', '11:00', '12:00'];

        // Act
        $keyboard = TelegramKeyboards::times($times);

        // Assert
        $this->assertInstanceOf(Keyboard::class, $keyboard);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_telegram_keyboards_confirmation()
    {
        // Act
        $keyboard = TelegramKeyboards::confirmation();

        // Assert
        $this->assertInstanceOf(Keyboard::class, $keyboard);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_telegram_keyboards_business_catalog()
    {
        // Arrange
        $businesses = Business::factory()->count(3)->create();

        // Act
        $keyboard = TelegramKeyboards::businessCatalog($businesses, 1, 2);

        // Assert
        $this->assertInstanceOf(Keyboard::class, $keyboard);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_telegram_keyboards_search_results()
    {
        // Arrange
        $businesses = Business::factory()->count(3)->create();

        // Act
        $keyboard = TelegramKeyboards::searchResults($businesses, 1, 2);

        // Assert
        $this->assertInstanceOf(Keyboard::class, $keyboard);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_telegram_keyboards_calendar()
    {
        // Arrange
        $month = Carbon::today()->format('Y-m');
        $availableDates = [Carbon::today()->addDays(7)->format('Y-m-d')];

        // Act
        $keyboard = TelegramKeyboards::calendar($month, $availableDates);

        // Assert
        $this->assertInstanceOf(Keyboard::class, $keyboard);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_telegram_keyboards_get_available_dates_for_month()
    {
        // Arrange
        $business = Business::factory()->create();
        $location = Location::factory()->create(['business_id' => $business->id]);
        $service = Service::factory()->create(['business_id' => $business->id]);
        $master = Master::factory()->create(['business_id' => $business->id]);

        // Привязываем услугу к мастеру
        $master->services()->attach($service->id);

        $slotService = app(\App\Services\AppointmentSlotService::class);
        $month = Carbon::today()->format('Y-m');

        // Act
        $dates = TelegramKeyboards::getAvailableDatesForMonth(
            $slotService,
            $service->id,
            $master->id,
            $location->id,
            $month
        );

        // Assert
        $this->assertIsArray($dates);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_telegram_keyboards_has_prev_month()
    {
        // Arrange
        $currentMonth = Carbon::today()->format('Y-m');
        $nextMonth = Carbon::today()->addMonth()->format('Y-m');

        // Act
        $hasPrevCurrent = TelegramKeyboards::hasPrevMonth($currentMonth);
        $hasPrevNext = TelegramKeyboards::hasPrevMonth($nextMonth);

        // Assert
        $this->assertFalse($hasPrevCurrent);
        $this->assertTrue($hasPrevNext);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_telegram_user_state_set_message_id()
    {
        // Arrange
        $business = Business::factory()->create();
        $state = TelegramUserState::factory()
            ->booking($business, TelegramUserState::STEP_SELECT_LOCATION, [])
            ->forUser($this->telegramUserId)
            ->create();

        // Act
        TelegramUserState::setMessageId($this->telegramUserId, $business->id, 999);

        // Assert
        $state->refresh();
        $this->assertEquals(999, $state->last_message_id);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_telegram_user_state_get_message_id()
    {
        // Arrange
        $business = Business::factory()->create();
        $state = TelegramUserState::factory()
            ->booking($business, TelegramUserState::STEP_SELECT_LOCATION, [])
            ->withMessageId(999)
            ->forUser($this->telegramUserId)
            ->create();

        // Act
        $messageId = TelegramUserState::getMessageId($this->telegramUserId, $business->id);

        // Assert
        $this->assertEquals(999, $messageId);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_telegram_user_state_clear_state()
    {
        // Arrange
        $business = Business::factory()->create();
        TelegramUserState::factory()
            ->booking($business, TelegramUserState::STEP_SELECT_LOCATION, [])
            ->forUser($this->telegramUserId)
            ->create();

        // Act
        TelegramUserState::clearState($this->telegramUserId, $business->id);

        // Assert
        $this->assertDatabaseCount('telegram_user_states', 0);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_telegram_user_state_clear_all_states()
    {
        // Arrange
        $business = Business::factory()->create();
        TelegramUserState::factory()
            ->booking($business, TelegramUserState::STEP_SELECT_LOCATION, [])
            ->forUser($this->telegramUserId)
            ->create();

        TelegramUserState::factory()
            ->search()
            ->forUser($this->telegramUserId)
            ->create();

        // Act
        TelegramUserState::clearState($this->telegramUserId);

        // Assert
        $this->assertDatabaseCount('telegram_user_states', 0);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_telegram_user_state_update_state_keep_message_id()
    {
        // Arrange
        $business = Business::factory()->create();
        $state = TelegramUserState::factory()
            ->booking($business, TelegramUserState::STEP_SELECT_LOCATION, [])
            ->withMessageId(999)
            ->forUser($this->telegramUserId)
            ->create();

        // Act
        TelegramUserState::updateStateKeepMessageId(
            $this->telegramUserId,
            $business->id,
            TelegramUserState::STEP_SELECT_SERVICE,
            ['location_id' => 1]
        );

        // Assert
        $state->refresh();
        $this->assertEquals(TelegramUserState::STEP_SELECT_SERVICE, $state->step);
        $this->assertEquals(999, $state->last_message_id);
        $this->assertEquals(['location_id' => 1], $state->data);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_telegram_user_state_update_state_with_message_id()
    {
        // Arrange
        $business = Business::factory()->create();

        // Act
        TelegramUserState::updateStateWithMessageId(
            $this->telegramUserId,
            $business->id,
            TelegramUserState::STEP_SELECT_LOCATION,
            [],
            888
        );

        // Assert
        $state = TelegramUserState::first();
        $this->assertEquals(TelegramUserState::STEP_SELECT_LOCATION, $state->step);
        $this->assertEquals(888, $state->last_message_id);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_telegram_user_state_get_state()
    {
        // Arrange
        $business = Business::factory()->create();
        $state = TelegramUserState::factory()
            ->booking($business, TelegramUserState::STEP_SELECT_LOCATION, [])
            ->forUser($this->telegramUserId)
            ->create();

        // Act
        $result = TelegramUserState::getState($this->telegramUserId, $business->id);

        // Assert
        $this->assertNotNull($result);
        $this->assertEquals($state->id, $result->id);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_telegram_user_state_get_state_not_found()
    {
        // Act
        $result = TelegramUserState::getState($this->telegramUserId, 999);

        // Assert
        $this->assertNull($result);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_telegram_user_state_update_state()
    {
        // Arrange
        $business = Business::factory()->create();

        // Act
        TelegramUserState::updateState(
            $this->telegramUserId,
            $business->id,
            TelegramUserState::STEP_SELECT_LOCATION,
            ['test' => 'data']
        );

        // Assert
        $state = TelegramUserState::first();
        $this->assertEquals(TelegramUserState::STEP_SELECT_LOCATION, $state->step);
        $this->assertEquals(['test' => 'data'], $state->data);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_telegram_user_state_update_state_existing()
    {
        // Arrange
        $business = Business::factory()->create();
        $state = TelegramUserState::factory()
            ->booking($business, TelegramUserState::STEP_SELECT_LOCATION, [])
            ->forUser($this->telegramUserId)
            ->create();

        // Act
        TelegramUserState::updateState(
            $this->telegramUserId,
            $business->id,
            TelegramUserState::STEP_SELECT_SERVICE,
            ['location_id' => 1]
        );

        // Assert
        $state->refresh();
        $this->assertEquals(TelegramUserState::STEP_SELECT_SERVICE, $state->step);
        $this->assertEquals(['location_id' => 1], $state->data);
        $this->assertDatabaseCount('telegram_user_states', 1);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_telegram_user_state_constants()
    {
        // Assert
        $this->assertEquals('start', TelegramUserState::STEP_START);
        $this->assertEquals('search', TelegramUserState::STEP_SEARCH);
        $this->assertEquals('select_location', TelegramUserState::STEP_SELECT_LOCATION);
        $this->assertEquals('select_service', TelegramUserState::STEP_SELECT_SERVICE);
        $this->assertEquals('select_master', TelegramUserState::STEP_SELECT_MASTER);
        $this->assertEquals('select_date', TelegramUserState::STEP_SELECT_DATE);
        $this->assertEquals('select_time', TelegramUserState::STEP_SELECT_TIME);
        $this->assertEquals('enter_client_info', TelegramUserState::STEP_ENTER_CLIENT_INFO);
        $this->assertEquals('enter_phone', TelegramUserState::STEP_ENTER_PHONE);
        $this->assertEquals('enter_notes', TelegramUserState::STEP_ENTER_NOTES);
        $this->assertEquals('confirm_appointment', TelegramUserState::STEP_CONFIRM_APPOINTMENT);
    }
}
