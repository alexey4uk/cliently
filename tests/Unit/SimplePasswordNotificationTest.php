<?php

namespace Tests\Unit;

use App\Models\Business;
use App\Models\User;
use App\Notifications\BusinessUserCreatedWithPassword;
use PHPUnit\Framework\TestCase;

class SimplePasswordNotificationTest extends TestCase
{
    public function test_password_notification_has_correct_properties()
    {
        // Создаем mock объекты
        $business = new Business();
        $business->name = 'Test Business';
        $business->id = 1;
        
        $temporaryPassword = 'temp_password_123';
        
        // Создаем уведомление
        $notification = new BusinessUserCreatedWithPassword($business, 'master', $temporaryPassword);
        
        // Проверяем, что уведомление правильно создается
        $this->assertInstanceOf(BusinessUserCreatedWithPassword::class, $notification);
        
        // Проверяем свойства уведомления
        $this->assertEquals($business->id, $notification->business->id);
        $this->assertEquals('master', $notification->role);
        $this->assertEquals($temporaryPassword, $notification->temporaryPassword);
        
        // Проверяем метод getRoleLabel
        $this->assertEquals('владельца', $this->invokeMethod($notification, 'getRoleLabel', ['owner']));
        $this->assertEquals('администратора', $this->invokeMethod($notification, 'getRoleLabel', ['admin']));
        $this->assertEquals('мастера', $this->invokeMethod($notification, 'getRoleLabel', ['master']));
        $this->assertEquals('unknown', $this->invokeMethod($notification, 'getRoleLabel', ['unknown']));
    }
    
    /**
     * Call protected/private method of a class.
     */
    private function invokeMethod(&$object, $methodName, array $parameters = array())
    {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);
        
        return $method->invokeArgs($object, $parameters);
    }
}