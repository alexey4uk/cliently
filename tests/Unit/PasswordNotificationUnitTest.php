<?php

namespace Tests\Unit;

use App\Models\Business;
use App\Models\User;
use App\Notifications\BusinessUserCreatedWithPassword;
use PHPUnit\Framework\TestCase;

class PasswordNotificationUnitTest extends TestCase
{
    public function test_password_notification_structure()
    {
        // Создаем mock объекты
        $business = new Business();
        $business->name = 'Test Business';
        $business->id = 1;
        
        $user = new User();
        $user->email = 'test@example.com';
        
        $temporaryPassword = 'temp_password_123';
        
        // Создаем уведомление
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
        
        // Проверяем, что массив данных содержит пароль
        $arrayData = $notification->toArray($user);
        $this->assertArrayHasKey('temporary_password', $arrayData);
        $this->assertEquals($temporaryPassword, $arrayData['temporary_password']);
    }
    
    public function test_role_labels()
    {
        $business = new Business();
        $business->name = 'Test Business';
        $business->id = 1;
        
        $user = new User();
        $user->email = 'test@example.com';
        
        // Тестируем разные роли
        $roles = [
            'owner' => 'владельца',
            'admin' => 'администратора',
            'master' => 'мастера',
        ];
        
        foreach ($roles as $role => $expectedLabel) {
            $notification = new BusinessUserCreatedWithPassword($business, $role, 'password');
            $mailMessage = $notification->toMail($user);
            $this->assertStringContainsString($expectedLabel, $mailMessage->introLines[0]);
        }
    }
}