<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class NotificationController extends Controller
{
    /**
     * Получить список уведомлений пользователя.
     * GET /api/notifications
     */
    public function index(Request $request): JsonResponse
    {
        $user = Auth::user();

        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'Не авторизован',
            ], 401);
        }

        $validator = Validator::make($request->all(), [
            'page' => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:50'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $page = $request->input('page', 1);
        $perPage = $request->input('per_page', 15);

        $notifications = NotificationService::getAll($user->id, $page, $perPage);

        return response()->json([
            'success' => true,
            'data' => [
                'notifications' => $notifications->items(),
                'meta' => [
                    'current_page' => $notifications->currentPage(),
                    'last_page' => $notifications->lastPage(),
                    'per_page' => $notifications->perPage(),
                    'total' => $notifications->total(),
                ],
            ],
        ]);
    }

    /**
     * Получить количество непрочитанных уведомлений.
     * GET /api/notifications/unread-count
     */
    public function unreadCount(): JsonResponse
    {
        $user = Auth::user();

        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'Не авторизован',
            ], 401);
        }

        $count = NotificationService::getUnreadCount($user->id);

        return response()->json([
            'success' => true,
            'data' => [
                'count' => $count,
            ],
        ]);
    }

    /**
     * Получить непрочитанные уведомления.
     * GET /api/notifications/unread
     */
    public function unread(): JsonResponse
    {
        $user = Auth::user();

        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'Не авторизован',
            ], 401);
        }

        $notifications = NotificationService::getUnread($user->id, 10);

        return response()->json([
            'success' => true,
            'data' => [
                'notifications' => $notifications,
            ],
        ]);
    }

    /**
     * Отметить уведомление как прочитанное.
     * POST /api/notifications/read
     */
    public function markAsRead(Request $request): JsonResponse
    {
        $user = Auth::user();

        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'Не авторизован',
            ], 401);
        }

        $validator = Validator::make($request->all(), [
            'notification_id' => ['required', 'integer'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $notificationId = $request->input('notification_id');
        $success = NotificationService::markAsRead($notificationId, $user->id);

        if (! $success) {
            return response()->json([
                'success' => false,
                'message' => 'Уведомление не найдено или недоступно',
            ], 404);
        }

        Log::info('Notification marked as read', [
            'user_id' => $user->id,
            'notification_id' => $notificationId,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Уведомление отмечено как прочитанное',
        ]);
    }

    /**
     * Отметить все уведомления как прочитанные.
     * POST /api/notifications/read-all
     */
    public function markAllAsRead(): JsonResponse
    {
        $user = Auth::user();

        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'Не авторизован',
            ], 401);
        }

        $count = NotificationService::markAllAsRead($user->id);

        Log::info('All notifications marked as read', [
            'user_id' => $user->id,
            'count' => $count,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Все уведомления отмечены как прочитанные',
            'data' => [
                'updated_count' => $count,
            ],
        ]);
    }

    /**
     * Удалить уведомление.
     * DELETE /api/notifications/{id}
     */
    public function destroy(int $id): JsonResponse
    {
        $user = Auth::user();

        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'Не авторизован',
            ], 401);
        }

        $success = NotificationService::delete($id, $user->id);

        if (! $success) {
            return response()->json([
                'success' => false,
                'message' => 'Уведомление не найдено или недоступно',
            ], 404);
        }

        Log::info('Notification deleted', [
            'user_id' => $user->id,
            'notification_id' => $id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Уведомление удалено',
        ]);
    }

    /**
     * Пометить уведомление как непрочитанное.
     * POST /api/notifications/unread
     */
    public function markAsUnread(Request $request): JsonResponse
    {
        $user = Auth::user();

        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'Не авторизован',
            ], 401);
        }

        $validator = Validator::make($request->all(), [
            'notification_id' => ['required', 'integer'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $notificationId = $request->input('notification_id');

        $notification = \App\Models\NotificationRecord::where('id', $notificationId)
            ->where('user_id', $user->id)
            ->first();

        if (! $notification) {
            return response()->json([
                'success' => false,
                'message' => 'Уведомление не найдено или недоступно',
            ], 404);
        }

        $notification->markAsUnread();

        Log::info('Notification marked as unread', [
            'user_id' => $user->id,
            'notification_id' => $notificationId,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Уведомление отмечено как непрочитанное',
        ]);
    }
}
