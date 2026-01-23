<?php

namespace Tests\Feature;

use Tests\TestCase;

class InvitationRouteAccessTest extends TestCase
{
    public function test_invitation_route_is_not_protected_by_auth_middleware()
    {
        // Проверяем, что маршрут доступен без аутентификации
        $response = $this->get('/invite/test_token_123');

        // Главное - что это не редирект (статус не 302)
        $response->assertStatus(200);

        // Проверяем, что не перенаправляет на страницу логина
        $response->assertDontSee('Login');
    }
}
