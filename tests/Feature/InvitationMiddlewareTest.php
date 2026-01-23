<?php

namespace Tests\Feature;

use Tests\TestCase;

class InvitationMiddlewareTest extends TestCase
{
    public function test_invitation_route_is_not_protected_by_auth()
    {
        // Проверяем, что маршрут доступен без аутентификации
        $response = $this->get('/invite/test_token');
        
        // Главное - что это не редирект на страницу логина (статус 302)
        $this->assertNotEquals(302, $response->status());
        
        // И что это не редирект на /login
        $this->assertNotEquals('/login', $response->headers->get('Location'));
    }
}