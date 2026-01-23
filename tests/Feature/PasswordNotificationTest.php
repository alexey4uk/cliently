<?php

namespace Tests\Feature;

use App\Models\Business;
use App\Models\User;
use App\Notifications\BusinessUserCreatedWithPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class PasswordNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_password_notification_creation()
    {
        // Создаем тестовые данные
        $business = Business::factory()->create(['name' => 'Test Business']);
        $user = User::factory()->create(['email' => 'test@example.com']);
        
        // Создаем уведомление
        $temporaryPassword = 'temp_password_123';
        $notification = new BusinessUserCreatedWithPassword($business, 'master', $temporaryPassword);
        
        // Проверяем, что уведомление правильно создается
        $this->assertInstanceOf(BusinessUserCreatedWithPassword::class, $notification);
        
        // Проверяем свойства уведомления
        $this->assertEquals($business->id, $notification->business->id);
        $this->assertEquals('master', $notification->role);
        $this->assertEquals($temporaryPassword, $notification->temporaryPassword);
        
        // Проверяем, что уведомление возвращает правильные каналы доставки
        $via = $notification->via($user);
        $this->assertContains('mail', $via);
        
        // Проверяем, что email сообщение создается правильно
        $mailMessage = $notification->toMail($user);
        
        // Проверяем, что email содержит необходимую информацию
        $this->assertStringContainsString('Test Business', $mailMessage->subject);
        $this->assertStringContainsString('мастера', $mailMessage->introLines[0]);
        $this->assertStringContainsString('test@example.com', $mailMessage->introLines[1]);
        $this->assertStringContainsString($temporaryPassword, $mailMessage->introLines[2]);
        
        // Проверяем, что email содержит ссылку на вход
        $this->assertNotEmpty($mailMessage->actionUrl);
        $this->assertStringContainsString('login', $mailMessage->actionUrl);
    }
}