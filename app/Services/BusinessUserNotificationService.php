<?php

namespace App\Services;

use App\Models\Business;
use App\Models\BusinessUserInvitation;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class BusinessUserNotificationService
{
    /**
     * Уведомить владельца/админа о том, что отправлено приглашение пользователю
     */
    public static function notifyUserInvited(
        BusinessUserInvitation $invitation,
        User $invitedBy,
    ): void {
        $business = $invitation->business;
        $role = $invitation->businessRole;

        // Получаем владельцев и админов бизнеса
        $recipients = self::getBusinessAdmins($business);

        foreach ($recipients as $recipient) {
            // Пропускаем того, кто отправил приглашение
            if ($recipient->id === $invitedBy->id) {
                continue;
            }

            // Проверяем, включен ли тип уведомления
            if (
                ! NotificationSettingsService::isTypeEnabled(
                    $recipient,
                    'business.user.invited',
                )
            ) {
                continue;
            }

            $title = 'Отправлено приглашение';
            $message = "Пользователь {$invitedBy->name} отправил приглашение пользователю {$invitation->email} с ролью {$role->name} в бизнес «{$business->name}».";

            // In-app уведомление
            NotificationService::send([
                'user_id' => $recipient->id,
                'type' => 'business.user.invited',
                'title' => $title,
                'message' => $message,
                'data' => [
                    'business_id' => $business->id,
                    'invitation_id' => $invitation->id,
                    'invited_by_id' => $invitedBy->id,
                ],
            ]);

            // Email уведомление
            if (
                NotificationSettingsService::shouldSendEmail(
                    $recipient,
                    'business.user.invited',
                ) &&
                $recipient->hasVerifiedEmail()
            ) {
                try {
                    $recipient->notify(
                        new \App\Notifications\Business\UserInvited(
                            $invitation,
                            $invitedBy,
                        ),
                    );
                } catch (\Exception $e) {
                    Log::error(
                        'Failed to send email notification for business.user.invited',
                        [
                            'user_id' => $recipient->id,
                            'error' => $e->getMessage(),
                        ],
                    );
                }
            }

            // Telegram уведомление
            if (
                NotificationSettingsService::shouldSendTelegram(
                    $recipient,
                    'business.user.invited',
                ) &&
                $recipient->isTelegramConnected()
            ) {
                try {
                    TelegramNotificationService::sendBusinessUserInvited(
                        $invitation,
                        $invitedBy,
                        $recipient,
                    );
                } catch (\Exception $e) {
                    Log::error(
                        'Failed to send telegram notification for business.user.invited',
                        [
                            'user_id' => $recipient->id,
                            'error' => $e->getMessage(),
                        ],
                    );
                }
            }
        }
    }

    /**
     * Уведомить владельца/админа о присоединении пользователя
     */
    public static function notifyUserJoined(
        Business $business,
        User $joinedUser,
        ?BusinessUserInvitation $invitation = null,
    ): void {
        $role = null;
        if ($invitation) {
            $role = $invitation->businessRole;
        } else {
            // Получаем роль из pivot таблицы
            $pivot = $business
                ->users()
                ->where('user_id', $joinedUser->id)
                ->first();
            if ($pivot && $pivot->pivot->role_id) {
                $role = \App\Models\BusinessRole::find($pivot->pivot->role_id);
            }
        }

        $roleName = $role ? $role->name : 'неизвестная роль';

        // Получаем владельцев и админов бизнеса
        $recipients = self::getBusinessAdmins($business);

        foreach ($recipients as $recipient) {
            // Пропускаем самого присоединившегося пользователя
            if ($recipient->id === $joinedUser->id) {
                continue;
            }

            // Проверяем, включен ли тип уведомления
            if (
                ! NotificationSettingsService::isTypeEnabled(
                    $recipient,
                    'business.user.joined',
                )
            ) {
                continue;
            }

            $title = 'Пользователь присоединился';
            $message = "Пользователь {$joinedUser->name} ({$joinedUser->email}) присоединился к бизнесу «{$business->name}» с ролью {$roleName}.";

            // In-app уведомление
            NotificationService::send([
                'user_id' => $recipient->id,
                'type' => 'business.user.joined',
                'title' => $title,
                'message' => $message,
                'data' => [
                    'business_id' => $business->id,
                    'joined_user_id' => $joinedUser->id,
                ],
            ]);

            // Email уведомление
            if (
                NotificationSettingsService::shouldSendEmail(
                    $recipient,
                    'business.user.joined',
                ) &&
                $recipient->hasVerifiedEmail()
            ) {
                try {
                    $recipient->notify(
                        new \App\Notifications\Business\UserJoined(
                            $business,
                            $joinedUser,
                            $roleName,
                        ),
                    );
                } catch (\Exception $e) {
                    Log::error(
                        'Failed to send email notification for business.user.joined',
                        [
                            'user_id' => $recipient->id,
                            'error' => $e->getMessage(),
                        ],
                    );
                }
            }

            // Telegram уведомление
            if (
                NotificationSettingsService::shouldSendTelegram(
                    $recipient,
                    'business.user.joined',
                ) &&
                $recipient->isTelegramConnected()
            ) {
                try {
                    TelegramNotificationService::sendBusinessUserJoined(
                        $business,
                        $joinedUser,
                        $recipient,
                    );
                } catch (\Exception $e) {
                    Log::error(
                        'Failed to send telegram notification for business.user.joined',
                        [
                            'user_id' => $recipient->id,
                            'error' => $e->getMessage(),
                        ],
                    );
                }
            }
        }

        // Также уведомляем самого присоединившегося пользователя
        if (
            NotificationSettingsService::isTypeEnabled(
                $joinedUser,
                'business.user.joined',
            )
        ) {
            $title = 'Добро пожаловать!';
            $message = "Вы успешно присоединились к бизнесу «{$business->name}» с ролью {$roleName}.";

            NotificationService::send([
                'user_id' => $joinedUser->id,
                'type' => 'business.user.joined',
                'title' => $title,
                'message' => $message,
                'data' => [
                    'business_id' => $business->id,
                ],
            ]);

            if (
                NotificationSettingsService::shouldSendEmail(
                    $joinedUser,
                    'business.user.joined',
                ) &&
                $joinedUser->hasVerifiedEmail()
            ) {
                try {
                    $joinedUser->notify(
                        new \App\Notifications\Business\UserJoined(
                            $business,
                            $joinedUser,
                            $roleName,
                        ),
                    );
                } catch (\Exception $e) {
                    Log::error(
                        'Failed to send email notification for business.user.joined to joined user',
                        [
                            'user_id' => $joinedUser->id,
                            'error' => $e->getMessage(),
                        ],
                    );
                }
            }
        }
    }

    /**
     * Уведомить об удалении пользователя из бизнеса
     */
    public static function notifyUserRemoved(
        Business $business,
        User $removedUser,
        User $removedBy,
    ): void {
        // Уведомляем удалённого пользователя
        if (
            NotificationSettingsService::isTypeEnabled(
                $removedUser,
                'business.user.removed',
            )
        ) {
            $title = 'Удаление из бизнеса';
            $message = "Вы были удалены из бизнеса «{$business->name}» пользователем {$removedBy->name}.";

            NotificationService::send([
                'user_id' => $removedUser->id,
                'type' => 'business.user.removed',
                'title' => $title,
                'message' => $message,
                'data' => [
                    'business_id' => $business->id,
                    'removed_by_id' => $removedBy->id,
                ],
            ]);

            if (
                NotificationSettingsService::shouldSendEmail(
                    $removedUser,
                    'business.user.removed',
                ) &&
                $removedUser->hasVerifiedEmail()
            ) {
                try {
                    $removedUser->notify(
                        new \App\Notifications\Business\UserRemoved(
                            $business,
                            $removedBy,
                        ),
                    );
                } catch (\Exception $e) {
                    Log::error(
                        'Failed to send email notification for business.user.removed',
                        [
                            'user_id' => $removedUser->id,
                            'error' => $e->getMessage(),
                        ],
                    );
                }
            }

            if (
                NotificationSettingsService::shouldSendTelegram(
                    $removedUser,
                    'business.user.removed',
                ) &&
                $removedUser->isTelegramConnected()
            ) {
                try {
                    TelegramNotificationService::sendBusinessUserRemoved(
                        $business,
                        $removedUser,
                        $removedUser,
                    );
                } catch (\Exception $e) {
                    Log::error(
                        'Failed to send telegram notification for business.user.removed',
                        [
                            'user_id' => $removedUser->id,
                            'error' => $e->getMessage(),
                        ],
                    );
                }
            }
        }

        // Уведомляем владельцев и админов бизнеса
        $recipients = self::getBusinessAdmins($business);

        foreach ($recipients as $recipient) {
            // Пропускаем того, кто удалил
            if ($recipient->id === $removedBy->id) {
                continue;
            }

            if (
                ! NotificationSettingsService::isTypeEnabled(
                    $recipient,
                    'business.user.removed',
                )
            ) {
                continue;
            }

            $title = 'Пользователь удалён';
            $message = "Пользователь {$removedUser->name} ({$removedUser->email}) был удалён из бизнеса «{$business->name}» пользователем {$removedBy->name}.";

            NotificationService::send([
                'user_id' => $recipient->id,
                'type' => 'business.user.removed',
                'title' => $title,
                'message' => $message,
                'data' => [
                    'business_id' => $business->id,
                    'removed_user_id' => $removedUser->id,
                    'removed_by_id' => $removedBy->id,
                ],
            ]);

            if (
                NotificationSettingsService::shouldSendEmail(
                    $recipient,
                    'business.user.removed',
                ) &&
                $recipient->hasVerifiedEmail()
            ) {
                try {
                    $recipient->notify(
                        new \App\Notifications\Business\UserRemoved(
                            $business,
                            $removedBy,
                            $removedUser,
                        ),
                    );
                } catch (\Exception $e) {
                    Log::error(
                        'Failed to send email notification for business.user.removed to admin',
                        [
                            'user_id' => $recipient->id,
                            'error' => $e->getMessage(),
                        ],
                    );
                }
            }

            if (
                NotificationSettingsService::shouldSendTelegram(
                    $recipient,
                    'business.user.removed',
                ) &&
                $recipient->isTelegramConnected()
            ) {
                try {
                    TelegramNotificationService::sendBusinessUserRemoved(
                        $business,
                        $removedUser,
                        $recipient,
                    );
                } catch (\Exception $e) {
                    Log::error(
                        'Failed to send telegram notification for business.user.removed to admin',
                        [
                            'user_id' => $recipient->id,
                            'error' => $e->getMessage(),
                        ],
                    );
                }
            }
        }
    }

    /**
     * Уведомить об изменении роли пользователя
     */
    public static function notifyUserRoleChanged(
        Business $business,
        User $user,
        string $oldRole,
        string $newRole,
        User $changedBy,
    ): void {
        // Уведомляем пользователя, чья роль изменилась
        if (
            NotificationSettingsService::isTypeEnabled(
                $user,
                'business.user.role_changed',
            )
        ) {
            $oldRoleName =
                \App\Models\BusinessRole::where('slug', $oldRole)->first()
                    ?->name ?? $oldRole;
            $newRoleName =
                \App\Models\BusinessRole::where('slug', $newRole)->first()
                    ?->name ?? $newRole;

            $title = 'Изменена роль';
            $message = "Ваша роль в бизнесе «{$business->name}» изменена с «{$oldRoleName}» на «{$newRoleName}» пользователем {$changedBy->name}.";

            NotificationService::send([
                'user_id' => $user->id,
                'type' => 'business.user.role_changed',
                'title' => $title,
                'message' => $message,
                'data' => [
                    'business_id' => $business->id,
                    'old_role' => $oldRole,
                    'new_role' => $newRole,
                    'changed_by_id' => $changedBy->id,
                ],
            ]);

            if (
                NotificationSettingsService::shouldSendEmail(
                    $user,
                    'business.user.role_changed',
                ) &&
                $user->hasVerifiedEmail()
            ) {
                try {
                    $user->notify(
                        new \App\Notifications\Business\UserRoleChanged(
                            $business,
                            $oldRoleName,
                            $newRoleName,
                            $changedBy,
                        ),
                    );
                } catch (\Exception $e) {
                    Log::error(
                        'Failed to send email notification for business.user.role_changed',
                        [
                            'user_id' => $user->id,
                            'error' => $e->getMessage(),
                        ],
                    );
                }
            }

            if (
                NotificationSettingsService::shouldSendTelegram(
                    $user,
                    'business.user.role_changed',
                ) &&
                $user->isTelegramConnected()
            ) {
                try {
                    TelegramNotificationService::sendBusinessUserRoleChanged(
                        $business,
                        $user,
                        $oldRole,
                        $newRole,
                        $user,
                    );
                } catch (\Exception $e) {
                    Log::error(
                        'Failed to send telegram notification for business.user.role_changed',
                        [
                            'user_id' => $user->id,
                            'error' => $e->getMessage(),
                        ],
                    );
                }
            }
        }

        // Уведомляем владельцев и админов бизнеса
        $recipients = self::getBusinessAdmins($business);

        foreach ($recipients as $recipient) {
            // Пропускаем того, кто изменил роль
            if ($recipient->id === $changedBy->id) {
                continue;
            }

            // Пропускаем самого пользователя (уже уведомили выше)
            if ($recipient->id === $user->id) {
                continue;
            }

            if (
                ! NotificationSettingsService::isTypeEnabled(
                    $recipient,
                    'business.user.role_changed',
                )
            ) {
                continue;
            }

            $oldRoleName =
                \App\Models\BusinessRole::where('slug', $oldRole)->first()
                    ?->name ?? $oldRole;
            $newRoleName =
                \App\Models\BusinessRole::where('slug', $newRole)->first()
                    ?->name ?? $newRole;

            $title = 'Изменена роль пользователя';
            $message = "Роль пользователя {$user->name} ({$user->email}) в бизнесе «{$business->name}» изменена с «{$oldRoleName}» на «{$newRoleName}» пользователем {$changedBy->name}.";

            NotificationService::send([
                'user_id' => $recipient->id,
                'type' => 'business.user.role_changed',
                'title' => $title,
                'message' => $message,
                'data' => [
                    'business_id' => $business->id,
                    'user_id' => $user->id,
                    'old_role' => $oldRole,
                    'new_role' => $newRole,
                    'changed_by_id' => $changedBy->id,
                ],
            ]);

            if (
                NotificationSettingsService::shouldSendEmail(
                    $recipient,
                    'business.user.role_changed',
                ) &&
                $recipient->hasVerifiedEmail()
            ) {
                try {
                    $recipient->notify(
                        new \App\Notifications\Business\UserRoleChanged(
                            $business,
                            $oldRoleName,
                            $newRoleName,
                            $changedBy,
                            $user,
                        ),
                    );
                } catch (\Exception $e) {
                    Log::error(
                        'Failed to send email notification for business.user.role_changed to admin',
                        [
                            'user_id' => $recipient->id,
                            'error' => $e->getMessage(),
                        ],
                    );
                }
            }

            if (
                NotificationSettingsService::shouldSendTelegram(
                    $recipient,
                    'business.user.role_changed',
                ) &&
                $recipient->isTelegramConnected()
            ) {
                try {
                    TelegramNotificationService::sendBusinessUserRoleChanged(
                        $business,
                        $user,
                        $oldRole,
                        $newRole,
                        $recipient,
                    );
                } catch (\Exception $e) {
                    Log::error(
                        'Failed to send telegram notification for business.user.role_changed to admin',
                        [
                            'user_id' => $recipient->id,
                            'error' => $e->getMessage(),
                        ],
                    );
                }
            }
        }
    }

    /**
     * Получить владельцев и админов бизнеса для отправки уведомлений
     */
    protected static function getBusinessAdmins(
        Business $business,
    ): \Illuminate\Support\Collection {
        $ownerRoleId = \App\Models\BusinessRole::where('slug', 'owner')->value(
            'id',
        );
        $adminRoleId = \App\Models\BusinessRole::where('slug', 'admin')->value(
            'id',
        );

        return $business
            ->users()
            ->wherePivotIn('role_id', [$ownerRoleId, $adminRoleId])
            ->get();
    }
}
