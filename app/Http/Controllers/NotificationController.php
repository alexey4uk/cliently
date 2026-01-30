<?php

namespace App\Http\Controllers;

use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class NotificationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        if (! $user) {
            return redirect()->route('login');
        }

        $filter = $request->get('filter', 'all'); // all, unread, read
        $page = $request->get('page', 1);
        $perPage = $request->get('per_page', 15);

        // Получаем все уведомления с учетом фильтра
        $query = \App\Models\NotificationRecord::where('user_id', $user->id)
            ->orderBy('created_at', 'desc');

        // Применяем фильтр по статусу прочитанности
        if ($filter === 'unread') {
            $query->where('is_read', false);
        } elseif ($filter === 'read') {
            $query->where('is_read', true);
        }

        $allNotifications = $query->get();

        // Фильтруем по правам доступа ДО пагинации
        $filtered = $allNotifications->filter(function ($notification) use ($user) {
            if (! $notification->required_permission) {
                return true;
            }

            return $user->can($notification->required_permission);
        });

        // Применяем пагинацию к отфильтрованным данным
        $total = $filtered->count();
        $offset = ($page - 1) * $perPage;
        $items = $filtered->slice($offset, $perPage)->values();

        $notifications = new \Illuminate\Pagination\LengthAwarePaginator(
            $items,
            $total,
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        // Получаем количество непрочитанных
        $unreadCount = NotificationService::getUnreadCount($user->id);

        $usePanel = Str::startsWith($request->path(), 'panel');

        return view('notifications.index', [
            'notifications' => $notifications,
            'filter' => $filter,
            'unreadCount' => $unreadCount,
            'layout' => $usePanel ? 'layouts.panel' : 'layouts.user',
        ]);
    }

    /**
     * Get unread count (AJAX).
     */
    public function unreadCount(Request $request)
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
     * Get unread notifications (AJAX).
     */
    public function unread(Request $request)
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
     * Mark notification as read.
     */
    public function markAsRead(Request $request, int $id)
    {
        $user = Auth::user();

        if (! $user) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Не авторизован',
                ], 401);
            }

            return redirect()->route('login');
        }

        $success = NotificationService::markAsRead($id, $user->id);

        if (! $success) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Уведомление не найдено или недоступно',
                ], 404);
            }

            return redirect()->back()
                ->with('error', 'Уведомление не найдено или недоступно.');
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Уведомление отмечено как прочитанное',
            ]);
        }

        return redirect()->back()
            ->with('success', 'Уведомление отмечено как прочитанное.');
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead(Request $request)
    {
        $user = Auth::user();

        if (! $user) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Не авторизован',
                ], 401);
            }

            return redirect()->route('login');
        }

        $count = NotificationService::markAllAsRead($user->id);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Все уведомления ({$count}) отмечены как прочитанные",
                'data' => [
                    'updated_count' => $count,
                ],
            ]);
        }

        return redirect()->back()
            ->with('success', "Все уведомления ({$count}) отмечены как прочитанные.");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        $user = Auth::user();

        if (! $user) {
            return redirect()->route('login');
        }

        $success = NotificationService::delete($id, $user->id);

        if (! $success) {
            return redirect()->back()
                ->with('error', 'Уведомление не найдено или недоступно.');
        }

        return redirect()->back()
            ->with('success', 'Уведомление удалено.');
    }
}
