<?php

namespace App\Models;

class Item
{
    private $table = 'items';

    // Create new item
    public function create($userId, $title, $description, $location, $dateFound, $type, $imagePath = null)
    {
        $data = [
            'user_id' => $userId,
            'title' => $title,
            'description' => $description,
            'location' => $location,
            'date_found' => $dateFound,
            'type' => $type,
            'image_path' => $imagePath
        ];
        return insert($this->table, $data);
    }

    // Get item by ID
    public function getById($id)
    {
        $items = select($this->table, "id = ?", [$id]);
        return empty($items) ? null : $items[0];
    }

    // Get items by type
    public function getByType($type, $limit = 10)
    {
        return select($this->table, "type = ?", [$type], '*', 'created_at DESC', $limit);
    }

    // Search items
    public function search($query, $type = null)
    {
        $where = "(
            title LIKE ? OR
            description LIKE ? OR
            location LIKE ?
        )";
        $params = ["%$query%", "%$query%", "%$query%"];

        if ($type) {
            $where .= " AND type = ?";
            $params[] = $type;
        }

        return select($this->table, $where, $params, '*', 'created_at DESC');
    }

    // Update item status
    public function updateStatus($id, $status)
    {
        return update($this->table, ['status' => $status], "id = ?", [$id]);
    }

    // Get matches for an item
    public function getMatches($itemId)
    {
        return query("
            SELECT m.*, i2.* 
            FROM matches m 
            JOIN items i2 ON m.matched_item_id = i2.id 
            WHERE m.item_id = ?
            ORDER BY m.created_at DESC
        ", [$itemId]);
    }

    // Get scan history for a user
    public function getScansByUser($userId)
    {
        return query("
            SELECT i.*, u.name as scanner_name, m.created_at as scan_date
            FROM items i
            JOIN matches m ON m.item_id = i.id
            JOIN users u ON u.id = m.scanner_id
            WHERE i.user_id = ?
            ORDER BY m.created_at DESC
        ", [$userId]);
    }

    // Create match with scanner information
    public function createMatch($itemId, $matchedItemId, $scannerId = null)
    {
        $data = [
            'item_id' => $itemId,
            'matched_item_id' => $matchedItemId,
            'status' => 'pending',
            'scanner_id' => $scannerId
        ];
        return insert('matches', $data);
    }

    // Update match status
    public function updateMatchStatus($matchId, $status)
    {
        return update('matches', ['status' => $status], 'id = ?', [$matchId]);
    }

    // Get match details
    public function getMatchDetails($matchId)
    {
        return query("
            SELECT m.*, i1.*, i2.*, u1.name as user1_name, u2.name as user2_name
            FROM matches m
            JOIN items i1 ON m.item_id = i1.id
            JOIN items i2 ON m.matched_item_id = i2.id
            JOIN users u1 ON u1.id = i1.user_id
            JOIN users u2 ON u2.id = i2.user_id
            WHERE m.id = ?
        ", [$matchId])[0];
    }
}
