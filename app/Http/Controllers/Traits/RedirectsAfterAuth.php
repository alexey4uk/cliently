<?php

namespace App\Http\Controllers\Traits;

trait RedirectsAfterAuth
{
    /**
     * Определить куда редиректить пользователя после авторизации
     */
    protected function getRedirectAfterAuth($user): string
    {
        $hasPanelAccess = $user->can('panel.access');
        $hasClientAccess = $user->can('client.access');

        // Если есть доступ к панели, но нет к клиентской части → панель
        if ($hasPanelAccess && ! $hasClientAccess) {
            return route('panel.index', absolute: false);
        }

        // Если есть доступ к клиентской части: без бизнесов → приветственная страница
        if ($hasClientAccess) {
            $hasBusinesses = $user->businesses()->exists();
            if (! $hasBusinesses) {
                return route('welcome', absolute: false);
            }

            return route('dashboard', absolute: false);
        }

        // Нет ни доступа к панели, ни к клиентской части — на приветственную страницу
        if (! $hasPanelAccess && ! $hasClientAccess) {
            return route('welcome', absolute: false);
        }

        return route('dashboard', absolute: false);
    }
}
