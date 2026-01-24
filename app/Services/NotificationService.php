<?php

namespace App\Services;

use App\Models\NotificationRecord;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    /**
     * Отправить системное уведомление пользователю.
     * Проверяет права доступа перед созданием уведомления.
     *
     * @param array{
     *     user_id: int,
     *     type: string,
     *     title: string,
     *     message: string,
     *     data?: array,
     *     required_permission?: string|null
     * } $params
     * @return NotificationRecord|null
     */
    public static function send(array $params): ?NotificationRecord
    {
        Log::info('NotificationService: send() called', ['params' => $params]);

        // Валидация обязательных полей
        $required = ['user_id', 'type', 'title', 'message'];
        foreach ($required as $field) {
            if (!isset($params[$field]) || empty($params[$field])) {
                Log::warning('NotificationService: Missing required field', ['field' => $field, 'params' => $params]);
                return null;
            }
        }

        // Получаем пользователя для проверки прав
        $user = User::find($params['user_id']);
        if (!$user) {
            Log::warning('NotificationService: User not found', ['user_id' => $params['user_id']]);
            return null;
        }

        Log::info('NotificationService: User found', ['user_id' => $user->id, 'user_name' => $user->name]);

        // Проверка прав доступа (если указано)
        // ВАЖНО: Если required_permission указан, проверяем права. Если null - создаем уведомление без проверки
        if (isset($params['required_permission']) && !empty($params['required_permission'])) {
            if (!$user->can($params['required_permission'])) {
                Log::info('NotificationService: User lacks permission', [
                    'user_id' => $user->id,
                    'permission' => $params['required_permission']
                ]);
                return null;
            }
            Log::info('NotificationService: User has permission', [
                'user_id' => $user->id,
                'permission' => $params['required_permission']
            ]);
        } else {
            Log::info('NotificationService: No permission required, creating notification', ['user_id' => $user->id]);
        }

        // Создаем уведомление
        try {
            $notification = NotificationRecord::create([
                'user_id' => $params['user_id'],
                'type' => $params['type'],
                'title' => $params['title'],
                'message' => $params['message'],
                'data' => $params['data'] ?? null,
                'required_permission' => $params['required_permission'] ?? null,
                'is_read' => false,
            ]);

            Log::info('NotificationService: Notification created successfully', [
                'notification_id' => $notification->id,
                'user_id' => $user->id,
                'type' => $params['type']
            ]);

            return $notification;
        } catch (\Exception $e) {
            Log::error('NotificationService: Failed to create notification', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'params' => $params
            ]);
            return null;
        }
    }

    /**
     * Получить непрочитанные уведомления пользователя с фильтрацией по правам.
     *
     * @param int $userId
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getUnread(int $userId, int $limit = 10)
    {
        $user = User::find($userId);
        if (!$user) {
            return collect();
        }

        $notifications = NotificationRecord::where('user_id', $userId)
            ->where('is_read', false)
            ->orderBy('created_at', 'desc')
            ->get();

        // Фильтруем по правам доступа
        $filtered = $notifications->filter(function ($notification) use ($user) {
            if (!$notification->required_permission) {
                return true;
            }
            return $user->can($notification->required_permission);
        });

        // Применяем лимит после фильтрации
        return $filtered->take($limit)->values();
    }

    /**
     * Получить все уведомления пользователя с пагинацией и фильтрацией по правам.
     *
     * @param int $userId
     * @param int $page
     * @param int $perPage
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public static function getAll(int $userId, int $page = 1, int $perPage = 15)
    {
        $user = User::find($userId);
        if (!$user) {
            return collect()->paginate();
        }

        // Получаем все уведомления пользователя
        $allNotifications = NotificationRecord::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();

        // Фильтруем по правам доступа ДО пагинации
        $filtered = $allNotifications->filter(function ($notification) use ($user) {
            if (!$notification->required_permission) {
                return true;
            }
            return $user->can($notification->required_permission);
        });

        // Применяем пагинацию к отфильтрованным данным
        $total = $filtered->count();
        $offset = ($page - 1) * $perPage;
        $items = $filtered->slice($offset, $perPage)->values();

        return new \Illuminate\Pagination\LengthAwarePaginator(
            $items,
            $total,
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );
    }

    /**
     * Отметить уведомление как прочитанное.
     *
     * @param int $notificationId
     * @param int $userId
     * @return bool
     */
    public static function markAsRead(int $notificationId, int $userId): bool
    {
        $notification = NotificationRecord::where('id', $notificationId)
            ->where('user_id', $userId)
            ->first();

        if (!$notification) {
            return false;
        }

        return $notification->markAsRead();
    }

    /**
     * Отметить все уведомления пользователя как прочитанные.
     *
     * @param int $userId
     * @return int Количество обновленных записей
     */
    public static function markAllAsRead(int $userId): int
    {
        return NotificationRecord::where('user_id', $userId)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
    }

    /**
     * Удалить конкретное уведомление пользователя.
     *
     * @param int $notificationId
     * @param int $userId
     * @return bool
     */
    public static function delete(int $notificationId, int $userId): bool
    {
        $notification = NotificationRecord::where('id', $notificationId)
            ->where('user_id', $userId)
            ->first();

        if (!$notification) {
            return false;
        }

        $notification->delete();
        return true;
    }

    /**
     * Получить количество непрочитанных уведомлений с учетом прав доступа.
     *
     * @param int $userId
     * @return int
     */
    public static function getUnreadCount(int $userId): int
    {
        $user = User::find($userId);
        if (!$user) {
            return 0;
        }

        $notifications = NotificationRecord::where('user_id', $userId)
            ->where('is_read', false)
            ->get();

        // Фильтруем по правам доступа
        return $notifications->filter(function ($notification) use ($user) {
            if (!$notification->required_permission) {
                return true;
            }
            return $user->can($notification->required_permission);
        })->count();
    }

    /**
     * Очистить старые уведомления (cron job).
     *
     * @param int $days Удалять уведомления старше N дней
     * @return int Количество удаленных записей
     */
    public static function deleteOld(int $days = 90): int
    {
        $date = now()->subDays($days);

        $count = NotificationRecord::where('created_at', '<', $date)->delete();

        Log::info('NotificationService: Old notifications cleaned', [
            'days' => $days,
            'deleted_count' => $count
        ]);

        return $count;
    }

    /**
     * Проверить, может ли пользователь получить уведомление.
     *
     * @param int $userId
     * @param string|null $requiredPermission
     * @return bool
     */
    public static function canReceive(int $userId, ?string $requiredPermission = null): bool
    {
        $user = User::find($userId);
        if (!$user) {
            return false;
        }

        if (!$requiredPermission) {
            return true;
        }

        return $user->can($requiredPermission);
    }
}
