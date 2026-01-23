<?php

namespace Tests\Feature;

use App\Models\Business;
use App\Models\User;
use App\Notifications\BusinessUserCreatedWithPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class BusinessUserCreationWithPasswordTest extends TestCase
{
    use RefreshDatabase;

    public function test_business_user_creation_sends_password_email()
    {
        // Создаем тестовые данные
        $admin = User::factory()->create();
        $business = Business::factory()->create();
        
        // Назначаем админа владельцем бизнеса
        $business->users()->attach($admin->id, ['role' => 'owner']);
        
        // Подменяем аутентификацию
        $this->actingAs($admin);
        
        // Подменяем отправку уведомлений
        Notification::fake();
        
        // Данные для создания пользователя
        $userData = [
            'first_name' => 'Иван',
            'last_name' => 'Иванов',
            'email' => 'newuser@example.com',
            'role' => 'master',
        ];
        
        // Вызываем метод контроллера напрямую
        $controller = new \App\Http\Controllers\Settings\BusinessUsersController();
        $request = new \Illuminate\Http\Request($userData);
        
        // Подменяем текущий бизнес
        $controller->setCurrentBusiness($business);
        
        // Вызываем метод создания пользователя
        $response = $controller->storeManual($request);
        
        // Проверяем, что пользователь создан
        $this->assertDatabaseHas('users', ['email' => 'newuser@example.com']);
        
        $newUser = User::where('email', 'newuser@example.com')->first();
        
        // Проверяем, что пользователь добавлен в бизнес
        $this->assertDatabaseHas('business_user', [
            'business_id' => $business->id,
            'user_id' => $newUser->id,
            'role' => 'master'
        ]);
        
        // Проверяем, что уведомление отправлено
        Notification::assertSentTo(
            $newUser,
            BusinessUserCreatedWithPassword::class
        );
        
        // Проверяем, что уведомление содержит временный пароль
        Notification::assertSentTo(
            $newUser,
            BusinessUserCreatedWithPassword::class,
            function ($notification) use ($newUser) {
                return $notification->temporaryPassword !== null && strlen($notification->temporaryPassword) > 0;
            }
        );
    }
}