<?php

namespace App\Controllers;

use App\Models\Notification;

class NotificationController extends Controller
{
    private $notification;

    public function __construct(Notification $notification)
    {
        $this->notification = $notification;
    }

    /**
     * Show notifications
     */
    public function index()
    {
        if (!$this->isAuthenticated()) {
            return $this->redirect('login');
        }

        $userId = Session::get('user_id');
        $notifications = $this->notification->getByUser($userId);
        $unreadCount = $this->notification->getUnreadCount($userId);

        return $this->view('notifications.index', [
            'notifications' => $notifications,
            'unreadCount' => $unreadCount
        ]);
    }

    /**
     * Mark notification as read
     */
    public function markAsRead($notificationId)
    {
        if (!$this->isAuthenticated()) {
            return $this->redirect('login');
        }

        $userId = Session::get('user_id');
        $notification = query("SELECT * FROM notifications WHERE id = ? AND user_id = ?", [$notificationId, $userId])[0];

        if (!$notification) {
            return $this->handleError('Notification not found');
        }

        $this->notification->markAsRead($notificationId);
        return $this->redirect('notifications');
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead()
    {
        if (!$this->isAuthenticated()) {
            return $this->redirect('login');
        }

        $userId = Session::get('user_id');
        $this->notification->markAllAsRead($userId);
        return $this->redirect('notifications');
    }

    /**
     * API endpoint to get notifications
     */
    public function apiGetNotifications(Request $request)
    {
        $validated = $this->validate($request, [
            'user_id' => 'required|integer|exists:users,id',
            'token' => 'required|string'
        ]);

        // Verify token
        if (!$this->verifyToken($validated['token'])) {
            return response()->json(['error' => 'Invalid token'], 401);
        }

        $notifications = $this->notification->getByUser($validated['user_id']);
        $unreadCount = $this->notification->getUnreadCount($validated['user_id']);

        return response()->json([
            'success' => true,
            'notifications' => $notifications,
            'unread_count' => $unreadCount
        ]);
    }

    /**
     * API endpoint to mark notification as read
     */
    public function apiMarkAsRead(Request $request)
    {
        $validated = $this->validate($request, [
            'notification_id' => 'required|integer|exists:notifications,id',
            'user_id' => 'required|integer|exists:users,id',
            'token' => 'required|string'
        ]);

        // Verify token
        if (!$this->verifyToken($validated['token'])) {
            return response()->json(['error' => 'Invalid token'], 401);
        }

        $notification = query("SELECT * FROM notifications WHERE id = ? AND user_id = ?", [
            $validated['notification_id'],
            $validated['user_id']
        ])[0];

        if (!$notification) {
            return response()->json(['error' => 'Notification not found'], 404);
        }

        $this->notification->markAsRead($validated['notification_id']);
        return response()->json(['success' => true]);
    }

    /**
     * Verify user token
     */
    private function verifyToken($token)
    {
        // Implement your token verification logic here
        // This is just a placeholder
        return true;
    }
}
