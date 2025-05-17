<?php

namespace App\Controllers;

use App\Models\Item;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    private $item;
    private $user;

    public function __construct(Item $item, User $user)
    {
        $this->item = $item;
        $this->user = $user;
    }

    /**
     * Show admin dashboard
     */
    public function index()
    {
        if (!$this->isAdmin()) {
            return $this->redirect('home');
        }

        // Get statistics
        $stats = [
            'total_items' => $this->item->getByType(null, null, null, false),
            'total_users' => $this->user->getByRole(null, false),
            'pending_items' => $this->item->getByStatus('pending', false),
            'matched_items' => $this->item->getByStatus('matched', false),
            'recent_items' => $this->item->getByType(null, null, null, true, 5)
        ];

        return $this->view('admin.dashboard', ['stats' => $stats]);
    }

    /**
     * Show users list
     */
    public function users()
    {
        if (!$this->isAdmin()) {
            return $this->redirect('home');
        }

        $users = $this->user->getAll();
        return $this->view('admin.users', ['users' => $users]);
    }

    /**
     * Show items list
     */
    public function items()
    {
        if (!$this->isAdmin()) {
            return $this->redirect('home');
        }

        $items = $this->item->getAll();
        return $this->view('admin.items', ['items' => $items]);
    }

    /**
     * Show matches list
     */
    public function matches()
    {
        if (!$this->isAdmin()) {
            return $this->redirect('home');
        }

        $matches = query("
            SELECT m.*, i1.*, i2.*, u1.name as user1_name, u2.name as user2_name
            FROM matches m
            JOIN items i1 ON m.item_id = i1.id
            JOIN items i2 ON m.matched_item_id = i2.id
            JOIN users u1 ON u1.id = i1.user_id
            JOIN users u2 ON u2.id = i2.user_id
            ORDER BY m.created_at DESC
        ");

        return $this->view('admin.matches', ['matches' => $matches]);
    }

    /**
     * Update user role
     */
    public function updateRole(Request $request, $userId)
    {
        if (!$this->isAdmin()) {
            return $this->redirect('home');
        }

        $validated = $this->validate($request, [
            'role' => 'required|in:user,admin'
        ]);

        $user = $this->user->getById($userId);
        if (!$user) {
            return $this->handleError('Kullanıcı bulunamadı');
        }

        $this->user->updateRole($userId, $validated['role']);
        return $this->handleSuccess('Rol güncellendi', 'admin.users');
    }

    /**
     * Delete user
     */
    public function deleteUser($userId)
    {
        if (!$this->isAdmin()) {
            return $this->redirect('home');
        }

        $user = $this->user->getById($userId);
        if (!$user) {
            return $this->handleError('Kullanıcı bulunamadı');
        }

        // Delete user's items and matches
        query("DELETE FROM items WHERE user_id = ?", [$userId]);
        query("DELETE FROM matches WHERE item_id IN (SELECT id FROM items WHERE user_id = ?) OR matched_item_id IN (SELECT id FROM items WHERE user_id = ?)", [$userId, $userId]);

        $this->user->delete($userId);
        return $this->handleSuccess('Kullanıcı silindi', 'admin.users');
    }

    /**
     * Update item status
     */
    public function updateItemStatus(Request $request, $itemId)
    {
        if (!$this->isAdmin()) {
            return $this->redirect('home');
        }

        $validated = $this->validate($request, [
            'status' => 'required|in:pending,matched,claimed'
        ]);

        $item = $this->item->getById($itemId);
        if (!$item) {
            return $this->handleError('İlan bulunamadı');
        }

        $this->item->updateStatus($itemId, $validated['status']);
        return $this->handleSuccess('İlan durumu güncellendi', 'admin.items');
    }

    /**
     * Delete item
     */
    public function deleteItem($itemId)
    {
        if (!$this->isAdmin()) {
            return $this->redirect('home');
        }

        $item = $this->item->getById($itemId);
        if (!$item) {
            return $this->handleError('İlan bulunamadı');
        }

        // Delete related matches
        query("DELETE FROM matches WHERE item_id = ? OR matched_item_id = ?", [$itemId, $itemId]);

        $this->item->delete($itemId);
        return $this->handleSuccess('İlan silindi', 'admin.items');
    }
}
