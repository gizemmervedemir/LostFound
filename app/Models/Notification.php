<?php

namespace App\Models;

class Notification
{
    private $table = 'notifications';

    /**
     * Create a new notification
     */
    public function create($userId, $type, $message, $itemId = null)
    {
        $data = [
            'user_id' => $userId,
            'type' => $type,
            'message' => $message,
            'item_id' => $itemId,
            'read' => 0,
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        return insert($this->table, $data);
    }

    /**
     * Get notifications for a user
     */
    public function getByUser($userId, $limit = 10)
    {
        return query("
            SELECT n.*, i.title as item_title
            FROM {$this->table} n
            LEFT JOIN items i ON n.item_id = i.id
            WHERE n.user_id = ?
            ORDER BY n.created_at DESC
            LIMIT ?
        ", [$userId, $limit]);
    }

    /**
     * Mark notification as read
     */
    public function markAsRead($notificationId)
    {
        return update($this->table, ['read' => 1], 'id = ?', [$notificationId]);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead($userId)
    {
        return update($this->table, ['read' => 1], 'user_id = ?', [$userId]);
    }

    /**
     * Get unread notifications count
     */
    public function getUnreadCount($userId)
    {
        $result = query("
            SELECT COUNT(*) as count
            FROM {$this->table}
            WHERE user_id = ? AND read = 0
        ", [$userId])[0];
        return $result['count'];
    }

    /**
     * Delete old notifications
     */
    public function cleanup($days = 30)
    {
        $date = date('Y-m-d H:i:s', strtotime('-' . $days . ' days'));
        return delete($this->table, "created_at < ?", [$date]);
    }
}
