<?php

namespace Tests\Feature;

use App\Models\BusinessUserInvitation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvitationRouteTest extends TestCase
{
    use RefreshDatabase;

    public function test_invitation_accept_route_is_accessible_without_authentication()
    {
        // Создаем тестовый токен приглашения
        $token = 'test_token_12345';

        // Пытаемся получить доступ к маршруту приглашения без аутентификации
        $response = $this->get('/invite/' . $token);

        // Должно вернуть ответ, а не редирект на логин
        // Если маршрут требует аутентификации, будет редирект на /login
        $response->assertDontSee('Login'); // Не должно показывать страницу логина
        $response->assertStatus(200); // Должно вернуть успешный статус
    }

    public function test_invitation_routes_are_not_redirected_to_login()
    {
        $token = 'test_token_67890';

        // Проверяем, что маршрут не перенаправляет на страницу логина
        $response = $this->get('/invite/' . $token);
        $response->assertDontSee('Login');
        $response->assertDontSee('Register');
        
        // Проверяем, что это не редирект
        $response->assertNotRedirect();
    }
}